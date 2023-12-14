<?php

namespace App\Http\Controllers;

use App\Exports\EnrolledStudents;
use App\Exports\IncomeReport;
use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\Enrollment;
use App\Models\Service;
use App\Models\User;
use App\Models\UserDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    public function get_enrolled_students(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'export' => 'required',
            'month' => 'required',
            'service' => 'required',
        ]);

        $date = Carbon::parse($request->month);
        $start = $date->startOfMonth()->format('Y-m-d');
        $end = $date->endOfMonth()->format('Y-m-d');

        $query = Enrollment::leftJoin('users', 'enrollments.student_id', '=', 'users.id')
            ->leftJoin('services', 'enrollments.service_id', '=', 'services.id')
            ->leftJoin('service_schedules', 'services.id', '=', 'service_schedules.service_id')
            ->leftJoin('user_details as student', 'users.id', '=', 'student.user_id')
            ->leftJoin('vehicles', 'services.vehicle_id', '=', 'vehicles.id')
            ->rightJoin('user_details as instruc', 'service_schedules.instructor_id', '=', 'instruc.user_id')
            ->select(DB::raw("CONCAT(student.lastname, ', ', student.firstname) AS student_name"))
            ->addSelect('student.lastname', 'student.firstname', 'student.middlename')
            ->addSelect('student.gender', 'student.birthdate')
            ->addSelect(DB::raw("CONCAT(instruc.lastname, ', ', instruc.firstname) AS instructor"))
            ->addSelect(DB::raw("services.duration AS hours"))
            ->addSelect(DB::raw("vehicles.transmission"))
            ->addSelect(DB::raw("MIN(service_schedules.day_of_week) AS date_start"))
            ->addSelect(DB::raw("MAX(service_schedules.day_of_week) AS date_end"))
            ->where('service_schedules.day_of_week', '>=', $start)
            ->where('service_schedules.day_of_week', '<=', $end)
            ->where('enrollments.service_id', $request->service)
            ->whereIn('enrollments.status', $request->failed_enrollments ? [Enrollment::STATUS_CANCELLED] : [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->groupBy('enrollments.student_id')
            ->get()->map(function($q) {
                if ($q->transmission == "")
                    $q->transmission = "-";
                return $q;
            })->toArray();

        if ($request->export) {
            $name = "";
            $now = strtotime("now");
            $month = $date->format("F, Y");
            $formatted_date = date("F d, Y - h:i A", $now);
            $user_details = UserDetail::where('user_id', Auth::id())
                ->select(DB::raw("CONCAT(lastname, ', ', firstname) AS name"))
                ->first();
            if ($user_details) $name = $user_details->name;
            else {
                $user = User::find(Auth::id());
                $name = $user->email;
            }
            $service = Service::where('id', $request->service)
                ->pluck('name')
                ->first();
            $rep_name =  str_replace(' ', '-', $service) . "_enrolled_students_" . date("Y-m-d_h-i-s", $now);
            $filename = $rep_name . "_report" . ($request->pdf ? ".pdf" : ".xlsx");
            $rep_data = [
                'date_printed'    => $formatted_date,
                'printed_by'      => $name,
                'exclude_columns' => $request->exclude_columns,
                'pdf'             => $request->pdf,
                'month'           => $month,
                'service'         => $service,
                'list'            => $query,
            ];
            $rs = SharedFunctions::success_msg('Report export ready');

            if ($request->pdf) {
                $pdf_content = Pdf::loadView('exports.enrolled_students', $rep_data)
                    ->setOption(['defaultFont' => 'sans-serif'])
                    ->setPaper('letter', 'landscape')
                    ->stream();

                $rs['result'] = [
                    'file' => [
                        'name' => $filename,
                        'blob' => base64_encode($pdf_content),
                    ],
                ];
            } else {
                $file = 'temp/' . $filename;
                Excel::store(new EnrolledStudents($rep_data), $file);
                $xlsx_content = file_get_contents(storage_path("app/{$file}"));

                $rs['result'] = [
                    'file' => [
                        'name' => $filename,
                        'blob' => base64_encode($xlsx_content),
                    ],
                ];
            }
            Storage::delete($filename);
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_REPORT, AuditTrail::ACTION_CREATE, "Report " . $rep_name . " exported"
            );
            return response()->json($rs);
        }
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_income_report(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'export' => 'required',
            'date_range.start' => 'required',
            'date_range.end' => 'required',
        ]);

        $total = 0;
        $date_start = Carbon::parse($request->date_range['start']);
        $date_end = Carbon::parse($request->date_range['end']);
        $start = $date_start->format('Y-m-d H:i:s');
        $end = $date_end->format('Y-m-d H:i:s');

        $query = Enrollment::leftJoin('services', 'enrollments.service_id', '=', 'services.id')
            ->leftJoin('users', 'enrollments.student_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select(DB::raw("CONCAT(user_details.lastname, ', ', user_details.firstname) AS student_name"))
            ->addSelect('services.name AS service', 'services.price as amount')
            ->addSelect('enrollments.created_at AS date_enrolled')
            ->where('enrollments.created_at', '>=', $start)
            ->where('enrollments.created_at', '<=', $end)
            ->whereIn('enrollments.status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->get()->toArray();

        if ($query) {
            foreach($query as $row) {
                $total += $row['amount'];
            }
        }

        if ($request->export) {
            $name = "";
            $now = strtotime("now");
            $formatted_date = date("F d, Y - h:i A", $now);
            $user_details = UserDetail::where('user_id', Auth::id())
                ->select(DB::raw("CONCAT(lastname, ', ', firstname) AS name"))
                ->first();
            if ($user_details) $name = $user_details->name;
            else {
                $user = User::find(Auth::id());
                $name = $user->email;
            }
            $rep_name = $date_start->format("Y_F") . "_income_" . date("Y-m-d_h-i-s", $now);
            $filename = $rep_name . "_report" . ($request->pdf ? ".pdf" : ".xlsx");
            $rep_data = [
                'date_printed' => $formatted_date,
                'printed_by'   => $name,
                'pdf'          => $request->pdf,
                'date_start'   => $date_start->format("F j, Y"),
                'date_end'     => $date_end->format("F j, Y"),
                'list'         => $query,
                'total_income' => $total,
            ];
            $rs = SharedFunctions::success_msg('Report export ready');

            if ($request->pdf) {
                $pdf_content = Pdf::loadView('exports.income_report', $rep_data)
                    ->setOption(['defaultFont' => 'sans-serif'])
                    ->setPaper('letter', 'portrait')
                    ->stream();

                $rs['result'] = [
                    'file' => [
                        'name' => $filename,
                        'blob' => base64_encode($pdf_content),
                    ],
                ];
            } else {
                $file = 'temp/' . $filename;
                Excel::store(new IncomeReport($rep_data), $file);
                $xlsx_content = file_get_contents(storage_path("app/{$file}"));

                $rs['result'] = [
                    'file' => [
                        'name' => $filename,
                        'blob' => base64_encode($xlsx_content),
                    ],
                ];
            }
            Storage::delete($filename);
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_REPORT, AuditTrail::ACTION_CREATE, "Report " . $rep_name . " exported"
            );
            return response()->json($rs);
        }

        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = ['list' => $query, 'total_income' => $total];
        return response()->json($rs);
    }

    public function get_services_for_report()
    {
        $query = Service::orderBy('name')
            ->select('id', 'name')
            ->get()->toArray();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }
}
