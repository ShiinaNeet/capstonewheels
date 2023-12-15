<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Mail\RescheduleStudent;
use App\Models\AuditTrail;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Requirement;
use App\Models\RescheduleEnrollment;
use App\Models\Room;
use App\Models\Schedule\ServiceSchedule;
use App\Models\Service;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Vehicle;
use App\Notifications\Notify;
use App\Notifications\RescheduleStudentNotification;
use App\Notifications\ScheduleCancelledNotification;
use App\Notifications\ScheduleUpdateNotification;
use App\Notifications\ServiceCreatedNotification;
use DateTime;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Intervention\Image\ImageManagerStatic as Image;

class ServiceController extends Controller
{
    public function cancel_schedule(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'schedules' => 'required',
            'reason' => 'required|max:100'
        ], [
            'schedules.required' => "The services field is required"
        ]);

        DB::beginTransaction();
        try {
            foreach($request->schedules as $id) {
                $isReschedule = false;
                $queryToCancel = ServiceSchedule::where('id', $id)
                    ->where('status', ServiceSchedule::STATUS_ACTIVE)
                    ->first();
                $isThereStudentEnrolledtoSchedule = Enrollment::leftJoin('service_schedules', 'enrollments.service_id', '=', 'service_schedules.service_id')
                    ->where('enrollments.service_id', '=', $queryToCancel->service_id)
                    ->where('enrollments.batch', '=', $queryToCancel->batch)
                    ->where('service_schedules.day_of_week', '=', $queryToCancel->day_of_week)
                    ->select('enrollments.id');

                $query1 = Service::find($queryToCancel->service_id);
                $query2 = UserDetail::where('user_id', $queryToCancel->instructor_id)
                    ->select(DB::raw("CONCAT(lastname, ', ', firstname) AS name"))
                    ->first();

                if ($isThereStudentEnrolledtoSchedule->count() > 0) {
                    $queryToCancel->status = ServiceSchedule::STATUS_CANCELLED;
                    foreach($isThereStudentEnrolledtoSchedule->get() as $enrollment) {
                        $query3 = new RescheduleEnrollment();
                        $query3->enrollment_id = $enrollment->id;
                        $query3->day_of_week = $queryToCancel->day_of_week;
                        $query3->save();
                    }
                    $isReschedule = true;
                    $studentNotification = User::leftJoin('enrollments', 'users.id', '=', 'enrollments.student_id')
                        ->leftJoin('service_schedules', function ($join) {
                            $join->on('service_schedules.service_id', '=', 'enrollments.service_id')
                                ->on('service_schedules.batch', '=', 'enrollments.batch');
                        })->where('enrollments.service_id', '=', $queryToCancel->service_id)
                        ->where('enrollments.batch', '=', $queryToCancel->batch)
                        ->where('service_schedules.status', '=', ServiceSchedule::STATUS_ACTIVE)
                        ->get(['users.*', 'enrollments.service_id as enrolled_service_id', 'enrollments.batch as enrolled_batch']);
                }
                $queryToCancel->status = ServiceSchedule::STATUS_CANCELLED;
                $queryToCancel->cancel_reason = $request->reason;
                $queryToCancel->save();
                if ($isReschedule) {
                    Notify::send($studentNotification, new ScheduleCancelledNotification($query1, $queryToCancel));
                }
                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_SCHEDULE, AuditTrail::ACTION_UPDATE,
                    "Schedule " . $query1->name . " with Instructor " . $query2->name .  " on " . date("M. jS Y", strtotime($query1->day_of_week)) .
                    ", " . date("h A", strtotime($query1->time_start)) . "-" . date("h A", strtotime($query1->time_end)) .
                    ", status " . ServiceSchedule::STATUS[$queryToCancel->status] . " for reason " . $request->reason . " cancelled"
                );
            }
            DB::commit();
            $rs = SharedFunctions::success_msg("Schedule(s) cancelled");
            return response()->json($rs);
        } catch (\Exception $e) {
            DB::rollBack();
            $rs = SharedFunctions::prompt_msg("Failed to cancel schedule(s)");
            return response()->json($rs);
        }
    }

    public function check_enrollment_form(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'batch' => 'required',
            'services' => 'required',
            'mop' => 'required'
        ]);

        $reference_no = date('Y-', strtotime(now())) . SharedFunctions::generate_random_string(10, true);
        if ($request->mop == Payment::MOP_PAYMONGO) {
            $paymongo = $this->pay_via_paymongo($reference_no, $request->services);

            if (isset($paymongo->errors) && $paymongo->errors) {
                // "errors": "code": "too_many_requests"
                // "errors": "code": "parameter_format_invalid"
                $payment_rs = $paymongo->errors;
                $rs = SharedFunctions::prompt_msg($payment_rs[0]->detail);
                goto end;
            } else {
                $payment_rs = $paymongo->data;
                // $rs['payment_url'] = $payment_rs->attributes->checkout_url;
                $payment_url = $payment_rs->attributes->checkout_url;
            }
        } else {
            $payment_rs = json_decode(
                json_encode([
                    'attributes' => [
                        'reference_number' => $reference_no,
                    ],
                ]),
                false
            );
            // $rs['payment_url'] = 'https://sample.com';
        }
        DB::transaction(function() use($request, $payment_rs) {
            $payment = new Payment();
            $payment->student_id = Auth::id();
            $payment->reference_no = $payment_rs->attributes->reference_number;
            $payment->mode_of_payment = $request->mop;
            // $payment->checkout_url = $payment_rs->attributes->checkout_url;
            $payment->status = Payment::STATUS_PENDING;
            $payment->save();

            foreach($request->services as $service_id) {
                $batch = ServiceSchedule::where('service_id', $service_id)
                    ->where('batch', $request->batch)
                    ->pluck('batch')
                    ->first();
                $enrollment = new Enrollment();
                $enrollment->service_id = $service_id;
                $enrollment->student_id = Auth::id();
                $enrollment->batch = $batch;
                $enrollment->status = Enrollment::STATUS_PENDING;
                $enrollment->save();

                $payment_item = new PaymentItem();
                $payment_item->enrollment_id = $enrollment->id;
                $payment_item->payment_id = $payment->id;
                $payment_item->save();
            }

            Session::put('payment_id', $payment->id);
            if (isset($payment_rs->id) && $payment_rs->id) Session::put('paymongo_id', $payment_rs->id);
        });

        $rs = SharedFunctions::success_msg("Payment saved");
        SharedFunctions::create_audit_log(
            AuditTrail::CATEGORY_PAYMENT, AuditTrail::ACTION_CREATE, "Payment " . $reference_no . " saved"
        );
        if (isset($payment_url) && $payment_url) $rs['payment_url'] = $payment_url;
        end: return response()->json($rs);
    }

    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = Service::find($request->id);
        if ($query->delete()) {
            $rs = SharedFunctions::success_msg("Service deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_SERVICE, AuditTrail::ACTION_DELETE, "Service " . $query->name . " deleted"
            );
        }
        return response()->json($rs);
    }

    public function get($sort_by)
    {
        $query = Service::orderBy('created_at', $sort_by == "created_at" ? 'DESC' : 'ASC')
            ->get()->map(function($q) {
                $arr = [];
                $requirements = explode("|", $q->requirements);
                foreach($requirements as $req) {
                    $arr[] = Requirement::where('id', $req)
                        ->pluck('id')->first();
                }
                $q->requirements = $arr;
                $q->price = (string) $q->price;
                $q->duration = (string) $q->duration;
                $q->image = $q->image !== null ? [$q->image] : [];
                return $q;
            });
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_available_services()
    {
        // $query = Service::whereNotIn('id', function ($q) {
        //     $q->select('service_id')
        //         ->from('service_schedules')
        //             ->where('status', 0);
        //     })->orderBy('name');
        $query = Service::orderBy('name')
            ->get()->map(function($q) {
                $arr = [];
                $requirements = explode("|", $q->requirements);
                foreach($requirements as $req) {
                    $arr[] = Requirement::where('id', $req)
                        ->pluck('id')->first();
                } $q->requirements = $arr; return $q;
            });
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_available_services_for_student()
    {
        $query = Service::leftJoin('service_schedules', 'services.id', '=', 'service_schedules.service_id')
            ->where('service_schedules.status', ServiceSchedule::STATUS_ACTIVE)
            ->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_enrollments()
    {
        $payment_item = function($q) {
            $q->price = (string) $q->price;
            $q->services = PaymentItem::leftJoin('enrollments', 'payment_items.enrollment_id', '=', 'enrollments.id')
                ->leftJoin('services', 'enrollments.service_id', '=', 'services.id')
                ->leftJoin('rooms', 'services.room_id', '=', 'rooms.id')
                ->leftJoin('vehicles', 'services.vehicle_id', '=', 'vehicles.id')
                ->select('services.id as service_id', 'services.type', 'services.name as service_name', 'services.price', 'services.image')
                ->addSelect('rooms.name as room', 'vehicles.name as vehicle')
                ->addSelect('enrollments.batch')
                ->where('payment_items.payment_id', $q->id)
                ->get()->map(function($qq) {
                    $qq->schedules = ServiceSchedule::where('service_id', $qq->service_id)
                        ->where('status', '!=', ServiceSchedule::STATUS_CANCELLED)
                        ->where('batch', $qq->batch)
                        ->select('day_of_week', 'time_start', 'time_end')
                        ->orderBy('day_of_week')->orderBy('time_start')
                        ->get();
                    return $qq;
                });
            return $q;
        };
        $archived_students = Enrollment::leftJoin('services', 'enrollments.service_id', '=', 'services.id')
            ->leftJoin('users', 'enrollments.student_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('services.name as service', 'enrollments.updated_at as date_archived')
            ->addSelect(DB::raw("CONCAT(user_details.lastname, ', ', user_details.firstname) as name"))
            ->where('enrollments.status', Enrollment::STATUS_CANCELLED)
            ->orderBy('enrollments.updated_at', 'DESC')
            ->get();
        $cancelled_scheds = ServiceSchedule::leftJoin('services', 'service_schedules.service_id', '=', 'services.id')
            ->leftJoin('users', 'service_schedules.instructor_id', '=', 'users.id')
            ->leftJoin('user_details', 'service_schedules.instructor_id', '=', 'user_details.user_id')
            ->select('services.name as service_name', 'service_schedules.day_of_week', 'service_schedules.time_start', 'service_schedules.time_end')
            ->addSelect('user_details.firstname', 'user_details.middlename', 'user_details.lastname')
            ->where('service_schedules.status', ServiceSchedule::STATUS_CANCELLED)
            ->orderBy('service_schedules.day_of_week', 'DESC')
            ->orderBy('service_schedules.time_start', 'DESC')
            ->get();
        $pending = Payment::leftJoin('users', 'payments.student_id', '=', 'users.id')
            ->leftJoin('user_details', 'payments.student_id', '=', 'user_details.user_id')
            ->select('payments.id', 'payments.reference_no', 'payments.mode_of_payment', 'payments.created_at')
            ->addSelect('user_details.firstname', 'user_details.middlename', 'user_details.lastname', 'user_details.gender')
            ->addSelect('user_details.address', 'user_details.barangay', 'user_details.city', 'user_details.province', 'user_details.birthdate')
            ->addSelect('user_details.mobile', 'users.email', 'users.id as user_id')
            ->orderBy('payments.created_at', 'DESC')
            ->where('payments.status', Payment::STATUS_PENDING)
            ->get()->map($payment_item);
        $verified = Payment::leftJoin('users', 'payments.student_id', '=', 'users.id')
            ->leftJoin('user_details', 'payments.student_id', '=', 'user_details.user_id')
            ->select('payments.id', 'payments.reference_no', 'payments.mode_of_payment', 'payments.created_at')
            ->addSelect('user_details.firstname', 'user_details.middlename', 'user_details.lastname', 'user_details.gender')
            ->addSelect('user_details.address', 'user_details.barangay', 'user_details.city', 'user_details.province', 'user_details.birthdate')
            ->addSelect('user_details.mobile', 'users.email', 'users.id as user_id')
            ->orderBy('payments.created_at', 'DESC')
            ->where('payments.status', Payment::STATUS_VERIFIED)
            ->get()->map($payment_item);
        $cancelledEnrollments = Enrollment::join('users', 'enrollments.student_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->leftJoin('payments', 'enrollments.student_id', '=', 'payments.student_id')
            ->select(
                'payments.id',
                'payments.reference_no',
                'payments.mode_of_payment',
                'payments.created_at',
                'user_details.mobile',
                'users.email',
                'users.id as user_id',
                'user_details.firstname',
                'user_details.middlename',
                'user_details.lastname'
            )
            ->where('enrollments.status', Enrollment::STATUS_CANCELLED)
            ->where('payments.status', 2)
            ->get()
            ->map($payment_item);
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = [
            'pending' => $pending,
            'verified' => $verified,
            'archived_students' => $archived_students,
            'cancelled_scheds' => $cancelled_scheds,
            'cancelled_enrollments' => $cancelledEnrollments,
        ];
        return response()->json($rs);
    }

    public function get_student_enrollment_history()
    {
        $active = Enrollment::leftJoin('services', 'enrollments.service_id', '=', 'services.id')
            ->leftJoin('service_schedules', function ($join) {
                $join->on('services.id', '=', 'service_schedules.service_id')
                    ->on('enrollments.batch', '=', 'service_schedules.batch');
            })->leftJoin('users', 'service_schedules.instructor_id', '=', 'users.id')
            ->leftJoin('user_details as instruc', 'service_schedules.instructor_id', '=', 'instruc.user_id')
            ->select('services.name')
            ->addSelect(DB::raw("COALESCE(CONCAT(instruc.lastname, ', ', instruc.firstname), users.email) AS instructor"))
            ->addSelect(DB::raw("MIN(service_schedules.day_of_week) AS date_start"))
            ->addSelect(DB::raw("MIN(service_schedules.time_start) AS time_start"))
            ->addSelect(DB::raw("MAX(service_schedules.day_of_week) AS date_end"))
            ->addSelect(DB::raw("MAX(service_schedules.time_end) AS time_end"))
            ->where('enrollments.student_id', Auth::id())
            ->where('enrollments.status', Enrollment::STATUS_ACTIVE)
            ->groupBy('enrollments.service_id')
            ->groupBy('enrollments.batch')
            ->get();
        $completed = Enrollment::leftJoin('services', 'enrollments.service_id', '=', 'services.id')
            ->leftJoin('service_schedules', function ($join) {
                $join->on('services.id', '=', 'service_schedules.service_id')
                    ->on('enrollments.batch', '=', 'service_schedules.batch');
            })->leftJoin('users', 'service_schedules.instructor_id', '=', 'users.id')
            ->leftJoin('user_details as instruc', 'service_schedules.instructor_id', '=', 'instruc.user_id')
            ->select('services.name')
            ->addSelect(DB::raw("COALESCE(CONCAT(instruc.lastname, ', ', instruc.firstname), users.email) AS instructor"))
            ->addSelect(DB::raw("CONCAT(instruc.lastname, ', ', instruc.firstname) AS instructor"))
            ->addSelect(DB::raw("MIN(service_schedules.day_of_week) AS date_start"))
            ->addSelect(DB::raw("MIN(service_schedules.time_start) AS time_start"))
            ->addSelect(DB::raw("MAX(service_schedules.day_of_week) AS date_end"))
            ->addSelect(DB::raw("MAX(service_schedules.time_end) AS time_end"))
            ->where('enrollments.student_id', Auth::id())
            ->whereIn('enrollments.status', [Enrollment::STATUS_COMPLETED, Enrollment::STATUS_CANCELLED])
            ->groupBy('enrollments.service_id')
            ->groupBy('enrollments.batch')
            ->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = ['active' => $active, 'completed' => $completed];
        return response()->json($rs);
    }

    public function get_options()
    {
        $query = User::find(Auth::id());
        $requirements = Requirement::orderBy('name')->get();
        $rooms = Room::orderBy('name')->get();
        $vehicles = Vehicle::orderBy('name')->get();
        $rs = SharedFunctions::success_msg('Success');
        if ($query->user_type !== 0) {
            $rs['result'] = [
                'requirements' => $requirements,
                'rooms' => $rooms,
                'vehicles' => $vehicles
            ];
        } else {
            $rs['result'] = [
                'requirements' => $requirements
            ];
        }
        return response()->json($rs);
    }

    // TODO: reschedule batches
    public function get_service_batches()
    {
        $query = ServiceSchedule::leftJoin('services', 'service_schedules.service_id', '=', 'services.id')
            ->leftJoin('users', 'service_schedules.instructor_id', '=', 'users.id')
            ->leftJoin('user_details as instruc', 'service_schedules.instructor_id', '=', 'instruc.user_id')
            ->select(
                DB::raw("MIN(service_schedules.day_of_week) as date_start"),
                DB::raw("MAX(service_schedules.day_of_week) as date_end"),
                'service_schedules.time_start',
                'service_schedules.time_end',
                'service_schedules.batch'
            )
            ->addSelect(DB::raw("COALESCE(CONCAT(instruc.lastname, ', ', instruc.firstname), users.email) AS instructor_name"))
            ->addSelect('services.id as service_id', 'services.name as service_name')
            ->where('service_schedules.status', ServiceSchedule::STATUS_ACTIVE)
            ->groupBy('service_schedules.batch')
            ->groupBy('service_schedules.service_id')
            ->get();
        $rs = SharedFunctions::success_msg("Success");
        $rs['result'] = $query->toArray();
        return response()->json($rs);
    }

    public function get_batches(Request $request)
    {
        $query = ServiceSchedule::where('service_id', $request->service_id)
            ->select(DB::raw("MIN(day_of_week) as date_start"), DB::raw("MAX(day_of_week) as date_end"), 'batch')
            ->where('status', ServiceSchedule::STATUS_ACTIVE)
            ->groupBy('batch')
            ->get();
        $rs = SharedFunctions::success_msg("Success");
        $rs['result'] = $query->toArray();
        return response()->json($rs);
    }

    public function get_schedules(Request $request)
    {
        $query = ServiceSchedule::leftJoin('enrollments', function ($join) {
            $join->on('service_schedules.service_id', '=', 'enrollments.service_id')
                ->on('service_schedules.batch', '=', 'enrollments.batch')
                ->where('enrollments.status', Enrollment::STATUS_ACTIVE);
        })->select(DB::raw('COUNT(enrollments.student_id) as stud_count'))
        ->addSelect('service_schedules.*')
        ->where('service_schedules.status', ServiceSchedule::STATUS_ACTIVE)
        ->groupBy('service_schedules.id')
        ->orderBy('service_schedules.day_of_week');
        if ($request->service_id > 0) $query = $query
            ->where('service_schedules.service_id', $request->service_id);

        $query = $query->get()->map(function($q) use($request) {
            $service = Service::where('id', $q->service_id)
                ->select('name')->first();
            $q->title = $service->name . " [#" . $q->batch . "] (" . $q->stud_count . "/" . $q->max_capacity . ")";
            if (isset($request->enrollment) && $request->enrollment === true) {
                $slots_left = $q->max_capacity - $q->stud_count;
                $q->title = date('h:ia', strtotime($q->time_start)) . "-" . date('h:ia', strtotime($q->time_end))
                    . " [#" . $q->batch . "] (" . $q->stud_count . "/" . $q->max_capacity . ")";
            }

            $today = new DateTime();
            $q->date = $q->day_of_week;
            $eservDate = new DateTime($q->day_of_week);
            if ($eservDate->setTime(0, 0, 0) < $today->setTime(0, 0, 0))
                $q->color = "rgba(118,124,136,0.85)"; // INV: SECONDARY COLOR
            else {
                if ($q->max_capacity !== $q->stud_count)
                    $q->color = "rgba(61,146,9,0.85)"; // ELSE SUCCESS COLOR
                else
                    $q->color = "rgba(228,34,34,0.85)"; // FULL: DANGER COLOR
            }
            return $q;
        })->toArray();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_service_schedules(Request $request)
    {
        $rs = SharedFunctions::prompt_msg("No service found");
        $query = ServiceSchedule::leftJoin('enrollments', function ($join) {
                $join->on('service_schedules.service_id', '=', 'enrollments.service_id')
                    ->where('enrollments.status', Enrollment::STATUS_ACTIVE);
            })->select(DB::raw('COUNT(enrollments.student_id) as stud_count'))
            ->addSelect('service_schedules.*')
            ->where('service_schedules.service_id', $request->service_id)
            ->where('service_schedules.batch', $request->batch)
            ->groupBy('service_schedules.service_id')
            ->get()->map(function($q) {
                $arr = [];
                $q->service = Service::where('id', $q->service_id)
                    ->select('name', 'price', 'requirements')->first();
                $requirements = explode("|", $q->service->requirements);
                foreach($requirements as $req) {
                    $arr[] = Requirement::where('id', $req)
                        ->pluck('id')->first();
                }
                $q->service->requirements = $arr;
                $q->instructor = User::where('id', $q->instructor_id)
                    ->select('email as name')->first();

                $schedules = ServiceSchedule::where('service_id', $q->service_id)
                    ->where('batch', $q->batch)
                    ->where('status', ServiceSchedule::STATUS_ACTIVE)
                    ->get();
                $q->schedules = $schedules;

                if ($q->max_capacity == $q->stud_count) $q->is_full = true;
                else $q->is_full = false;
                return $q;
            })->toArray();
        if (count($query) > 0) $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_services_from_date(Request $request)
    {
        $rs = SharedFunctions::prompt_msg("No service found");
        $query = ServiceSchedule::leftJoin('enrollments', function ($join) {
                $join->on('service_schedules.service_id', '=', 'enrollments.service_id')
                    ->where('enrollments.status', Enrollment::STATUS_ACTIVE);
            })->select(DB::raw('COUNT(enrollments.student_id) as stud_count'))
            ->addSelect('service_schedules.*')
            ->where('service_schedules.day_of_week', $request->day_of_week)
            ->groupBy('service_schedules.id')
            ->get()->map(function($q) {
                $arr = [];
                $q->service = Service::where('id', $q->service_id)
                    ->select('name', 'price', 'requirements')->first();
                $requirements = explode("|", $q->service->requirements);
                foreach($requirements as $req) {
                    $arr[] = Requirement::where('id', $req)
                        ->pluck('id')->first();
                }
                $q->service->requirements = $arr;
                $q->instructor = User::where('id', $q->instructor_id)
                    ->select('email as name')->first();

                if ($q->max_capacity == $q->stud_count) $q->is_full = true;
                else $q->is_full = false;
                return $q;
            })->toArray();
        if (count($query) > 0) $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function reschedule_student(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'service_id' => 'required',
            'old_batch' => 'required',
            'new_batch' => 'required',
            'student_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $enrollment = Enrollment::where('student_id', $request->student_id)
                ->where('service_id', $request->service_id)
                ->where('batch', $request->old_batch)
                ->first();

            if ($enrollment) {
                $enrollment->batch = $request->new_batch;
                $enrollment->save();

                $resched = RescheduleEnrollment::where('enrollment_id', $enrollment->id)->first();
                if ($resched) $resched->delete();
            }
            $this->student_resched_notify($enrollment);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback(); return response()->json($rs);
        }
        $rs = SharedFunctions::success_msg("Student rescheduled");
        return response()->json($rs);
    }

    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $form = new Request(json_decode($request->form, true));
        if (isset($request->file) && $request->hasFile('file')) $form->merge(['image' => $request->file]);

        $validations = [
            'name' => 'required|max:255',
            'description' => 'max:500',
            'price' => 'numeric|required|max:500000.00',
            'duration' => 'numeric|required|max:9999',
            'requirements' => 'required',
            'type' => 'required'
        ];

        if ($form->type == Service::TYPE_ROOM) $validations['room_id'] = 'required';
        else $validations['vehicle_id'] = 'required';
        if (isset($form->image) && !empty($form->image)) $validations['image'] = 'image|max:' . strval($form->filesize_limit / 1024);

        $this->validate($form, $validations, [
            'price.numeric' => "Please enter a number in price field",
            'duration.numeric' => "Please enter a number in duration field",
            'room_id.required' => "The room field is required",
            'vehicle_id.required' => "The vehicle field is required",
        ]);

        $new_service = false;
        if (isset($form->id)) $service = Service::find($form->id);
        else { $service = new Service(); $new_service = true; }

        if ($new_service) $service->type = $form->type;
        if ($form->type == Service::TYPE_ROOM) $service->room_id = $form->room_id;
        else $service->vehicle_id = $form->vehicle_id;
        $service->name = $form->name;
        $service->description = !empty($form->description) ? $form->description : null;
        $service->price = $form->price;
        $service->duration = $form->duration;
        $service->requirements = implode("|", $form->requirements);
        if (isset($form->image) && !empty($form->image)) {
            $fext = 'jpg';
            $path = public_path('images/uploads/services/');
            if (File::exists($path . $service->image)) File::delete($path . $service->image);
            $filename = date('YmdwHis') . "_" . SharedFunctions::generate_random_string(7) . '_' . SharedFunctions::generate_random_string() . '.' . $fext;
            if (!File::isDirectory($path)) File::makeDirectory($path, 0755, true, true);
            $image = Image::make($form->image);
            $image->save($path . '/' . $filename, 60, $fext);
            $service->image = $filename;
        }
        if ($service->save()) {
            $rs = SharedFunctions::success_msg("Service saved");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_SERVICE, $new_service ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE, "Service " . $form->name . " saved"
            );
        }
        return response()->json($rs);
    }

    public function save_schedule(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $validations = [
            'instructor_id' => 'required',
            'is_new_batch' => 'required',
            'service_id' => 'required',
            'dates' => 'required',
            'dates.*.day_of_week' => 'required',
            'dates.*.time_start' => 'required',
            'dates.*.time_end' => 'required'
        ];
        $this->validate($request, $validations, [
            'dates.*.time_start' => "The time start field is required.",
            'dates.*.time_end' => "The time end field is required.",
            'instructor_id.required' => "The instructor field is required."
        ]);
        $service = Service::find($request->service_id);
        if ($service->type == Service::TYPE_ROOM) $validations['max_capacity'] = 'required';
        if (!$request->is_new_batch) $validations['batch'] = 'required';

        $has_overlapping_sched = false;
        DB::beginTransaction();
        try {
            $batch = 0;
            if ($request->is_new_batch) {
                $check = ServiceSchedule::where('service_id', $request->service_id)
                    ->select(DB::raw("MAX(batch) as max_batch"))
                    ->first();
                if ($check) $batch = (intval($check->max_batch) + 1);
            } else $batch = $request->batch;
            foreach($request->dates as $date) {
                $check_sched = ServiceSchedule::where('day_of_week', $date['day_of_week'])
                    ->where('instructor_id','=', $request->instructor_id)
                    ->where('time_end', '>', $date['time_start'])
                    ->count();
                if ($check_sched > 0) {
                    $has_overlapping_sched = true; break;
                }

                $new_schedule = false;
                if (isset($request->id)) $schedule = ServiceSchedule::find($request->id);
                else { $schedule = new ServiceSchedule(); $new_schedule = true; }

                $schedule->service_id = $request->service_id;
                $query1 = Service::find($request->service_id);
                $schedule->instructor_id = $request->instructor_id;
                $query2 = UserDetail::where('user_id', $request->instructor_id)
                    ->select(DB::raw("CONCAT(lastname, ', ', firstname) AS name"))
                    ->first();
                if (!$query2) {
                    $query2 = User::where('id', $request->instructor_id)
                    ->select(DB::raw("email AS name"))
                    ->first();
                }
                $schedule->batch = $batch;
                $schedule->day_of_week = $date['day_of_week'];
                $schedule->time_start = $date['time_start'];
                $schedule->time_end = $date['time_end'];

                if ($service->type == Service::TYPE_VEHICLE) $max_capacity = 1;
                else $max_capacity = $request->max_capacity;
                $schedule->max_capacity = $max_capacity;
                $schedule->save();

                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_SCHEDULE,  $new_schedule ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE,
                    "Schedule " . $query1->name . " with Instructor " . $query2->name .
                    " on " . date("M. jS Y", strtotime($date['day_of_week'])) .
                    " " . date("h A", strtotime($date['time_start'])) . "-" . date("h A", strtotime($date['time_end'])) . " saved"
                );
            }
            // $service->max_capacity = $max_capacity;
            // $service->save();

            if ($has_overlapping_sched) {
                $rs = SharedFunctions::prompt_msg("Schedule must not overlap to an existing schedule");
                return response()->json($rs);
            }
            $this->service_created_notify($service->name);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback(); return response()->json($rs);
        }
        $rs = SharedFunctions::success_msg("Schedule saved");
        return response()->json($rs);
    }

    public function update_schedule(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'id' => 'required',
            'reason' => 'required|max:100',
            'time_start' => 'required',
            'time_end' => 'required'
        ], [
            'id.required' => "The service field is required"
        ]);

        // Changed it here to access schedule of instructor_id
        $query = ServiceSchedule::find($request->id);
        // check if overlapping an schedule
        $check_sched = ServiceSchedule::where('id', '!=', $request->id)
            ->where('day_of_week', $request->new_date)
            ->where('instructor_id', '=', $query->instructor_id)
            ->where('time_end', '>', $request->time_start)
            ->count();
        if ($check_sched > 0) {
            $rs = SharedFunctions::prompt_msg("Schedule must not overlap to an existing schedule");
            return response()->json($rs);
        }


        $query1 = Service::find($query->service_id);
        $query2 = UserDetail::where('user_id', $query->instructor_id)
            ->select(DB::raw("CONCAT(lastname, ', ', firstname) AS name"))
            ->first();
        if (!$query2) {
            $query2 = User::where('id', $request->instructor_id)
            ->select(DB::raw("email AS name"))
            ->first();
        }
        if ($request->new_date) $query->day_of_week = $request->new_date;
        $query->time_start = $request->time_start;
        $query->time_end = $request->time_end;
        $query->update_reason = $request->reason;
        $query->save();

        $this->service_sched_update_notify($query);

        $rs = SharedFunctions::success_msg("Schedule updated");
        SharedFunctions::create_audit_log(
            AuditTrail::CATEGORY_SCHEDULE, AuditTrail::ACTION_UPDATE,
            "Schedule " . $query1->name . " with Instructor " . $query2->name .
            " on " . date("M. jS Y", strtotime($request->new_date)) .
            ", " . date("h A", strtotime($request->time_start)) . "-" . date("h A", strtotime($request->time_end)) .
            ", status " . ServiceSchedule::STATUS[$query->status] . " for reason " . $request->reason . " saved"
        );
        return response()->json($rs);
    }

    private function pay_via_paymongo($reference_no, $items)
    {
        $query = Service::whereIn('id', $items)->get();

        $line_items = [];
        foreach($query as $item) {
            $line_items[] = [
                'currency' => 'PHP',
                'amount' => ($item->price * 100),
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => 1
            ];
        }

        $host = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $fallback_url = "$protocol://$host";

        $user = User::where('id', Auth::id())->first();
        $user_details = UserDetail::where('user_id', $user->id)->first();
        $data = [
            'data' => [
                'attributes' => [
                    'billing' => [
                        'name' => $user_details ? ($user_details->firstname . ' ' . $user_details->lastname) : '',
                        'email' => $user->email,
                        'phone' => $user_details ? $user_details->mobile : ''
                    ],
                    // 'billing_information_fields_editable' => "disabled",
                    'description' => 'Reference: #' . $reference_no,
                    'line_items' => $line_items,
                    'payment_method_types' => [
                        'card',
                        'gcash',
                        'paymaya'
                    ],
                    'reference_number' => $reference_no,
                    'send_email_receipt' => true,
                    'show_description' => true,
                    'show_line_items' => true,
                    'success_url' => env('APP_URL', $fallback_url) . (env('APP_DEBUG', false) ? ':8000' : '') . '/payment/success'
                ],
            ],
        ];

        $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions')
            ->withHeader('Content-Type: application/json')
            ->withHeader('accept: application/json')
            ->withHeader('Authorization: Basic ' . env('AUTH_PAY', ''))
            ->withData($data)
            ->asJson()
            ->post();
        // Session::put('paymongo_id', $response->data->id);
        // return $response->data->attributes->checkout_url;
        return $response;
    }

    private function service_created_notify($service)
    {
        $users = User::where('user_type', User::TYPE_STUDENT)->get();
        Notify::send($users, new ServiceCreatedNotification($service));
    }

    private function service_sched_update_notify($service_schedule)
    {
        $service = Service::where('id', $service_schedule->service_id)
            ->select('name')->first();
        $users = Enrollment::leftJoin('users', 'enrollments.student_id', '=', 'users.id')
            ->where('enrollments.service_id', $service_schedule->service_id)
            ->select('users.id')
            ->get();
        $date = date('Y-m-d h:ia', strtotime($service_schedule->day_of_week." ".$service_schedule->time_start))." - ".
            date('h:ia', strtotime($service_schedule->day_of_week." ".$service_schedule->time_end));
        if (count($users) > 0) Notify::send($users, new ScheduleUpdateNotification($service->name, $date));
    }

    private function student_resched_notify($enrollment)
    {
        $users = User::where('user_type', User::TYPE_STUDENT)
            ->where('id', $enrollment->student_id)
            ->get();
        $service = Service::where('id', $enrollment->service_id)
            ->pluck('name')
            ->first();
        $schedule = ServiceSchedule::where('service_id', $enrollment->service_id)
            ->where('batch', $enrollment->batch)
            ->select(DB::raw("MIN(day_of_week) AS date_start"),
                DB::raw("MAX(day_of_week) AS date_end"),
                'time_start', 'time_end')
            ->first();
        $date = date('Y-m-d h:ia', strtotime($schedule->date_start." ".$schedule->time_start))." - ".
            date('h:ia', strtotime($schedule->day_of_week." ".$schedule->time_end));
        Notify::send($users, new RescheduleStudentNotification($service, $date, $enrollment->batch));

        if (filter_var($users[0]->email, FILTER_VALIDATE_EMAIL))
            Mail::to($users[0]->email)->send(new RescheduleStudent($service, $date, $enrollment->batch));
    }

    // TODO: reschedule
    public function resched_student(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'batch'   => 'required',
            'service_id' => 'required',
            'student_id' => 'required',
        ], [
            'service_id.required' => "The service field is required",
            'student_id.required' => "The student is required"
        ]);
        $enrollments = Enrollment::where('student_id', $request->student_id)
            ->where('service_id', $request->service_id)
            ->where('batch', $request->batch)
            ->pluck('id');

        DB::beginTransaction();
        try {
            foreach($enrollments as $enrollment) {
                $query = new RescheduleEnrollment();
                $query->enrollment_id = $enrollment;
                $query->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($rs);
        }
        $rs = SharedFunctions::success_msg("Student rescheduled");
        return response()->json($rs);
    }

    public function get_reschedule_enrollments()
    {
        $rs = SharedFunctions::default_msg();
        // $query = ServiceSchedule::leftJoin('services', 'service_schedules.service_id', '=', 'services.id')
        //     ->leftJoin('enrollments', 'service_schedules.service_id', '=', 'enrollments.service_id')
        //     ->leftJoin('users', 'service_schedules.instructor_id', '=', 'users.id')
        //     ->leftJoin('user_details as instructor_details', 'service_schedules.instructor_id', '=', 'instructor_details.user_id')
        //     ->leftJoin('users as students', 'enrollments.student_id', '=', 'students.id')
        //     ->leftJoin('user_details as student_details', 'students.id', '=', 'student_details.user_id')
        //     ->select(
        //         'services.id as service_id',
        //         'services.name as service_name',
        //         'service_schedules.day_of_week',
        //         'service_schedules.time_start',
        //         'service_schedules.time_end',
        //         'service_schedules.batch',
        //         DB::raw('CONCAT(student_details.lastname, ", ", student_details.firstname) as student_name'),
        //         DB::raw('CONCAT(instructor_details.firstname, " ", instructor_details.lastname) as instructor_name'),
        //         'students.id as student_id',
        //         'enrollments.student_id'
        //     )
        //     ->where('service_schedules.status', ServiceSchedule::STATUS_RESCHEDULE)
        //     ->whereIn('enrollments.status', [Enrollment::STATUS_COMPLETED, Enrollment::STATUS_ACTIVE])
        //     ->orderBy('service_schedules.day_of_week', 'DESC')
        //     ->orderBy('service_schedules.time_start', 'DESC')
        //     ->get();

        // TODO: reschedule
        // $query2 = ServiceSchedule::leftJoin('services', 'service_schedules.service_id', '=', 'services.id')
        //     ->leftJoin('enrollments', 'service_schedules.service_id', '=', 'enrollments.service_id')
        //     ->leftJoin('reschedule_enrollments', 'enrollments.id', '=', 'reschedule_enrollments.enrollment_id')
        //     ->leftJoin('users', 'service_schedules.instructor_id', '=', 'users.id')
        //     ->leftJoin('user_details as instructor_details', 'service_schedules.instructor_id', '=', 'instructor_details.user_id')
        //     ->leftJoin('users as students', 'enrollments.student_id', '=', 'students.id')
        //     ->leftJoin('user_details as student_details', 'students.id', '=', 'student_details.user_id')
        //     ->select(
        //         'services.id as service_id',
        //         'services.name as service_name',
        //         'service_schedules.day_of_week',
        //         'service_schedules.time_start',
        //         'service_schedules.time_end',
        //         'service_schedules.batch',
        //         DB::raw('CONCAT(student_details.lastname, ", ", student_details.firstname) as student_name'),
        //         DB::raw('CONCAT(instructor_details.firstname, " ", instructor_details.lastname) as instructor_name'),
        //         'students.id as student_id',
        //         'enrollments.student_id'
        //     )
        //     ->whereNotNull('reschedule_enrollments.enrollment_id')
        //     ->orderBy('service_schedules.day_of_week', 'DESC')
        //     ->orderBy('service_schedules.time_start', 'DESC')
        //     ->get();

        // $result = array_merge($query->toArray(), $query2->toArray());

        $query = RescheduleEnrollment::leftJoin('enrollments', 'reschedule_enrollments.enrollment_id', '=', 'enrollments.id')
            ->leftJoin('services', 'enrollments.service_id', '=', 'services.id')
            ->leftJoin('service_schedules', function ($join) {
                $join->on('enrollments.service_id', '=', 'service_schedules.service_id')
                    ->on('enrollments.batch', '=', 'service_schedules.batch');
            })
            ->leftJoin('users', 'service_schedules.instructor_id', '=', 'users.id')
            ->leftJoin('user_details as instructor_details', 'service_schedules.instructor_id', '=', 'instructor_details.user_id')
            ->leftJoin('users as students', 'enrollments.student_id', '=', 'students.id')
            ->leftJoin('user_details as student_details', 'students.id', '=', 'student_details.user_id')
            ->select(
                'services.id as service_id',
                'services.name as service_name',
                'service_schedules.day_of_week',
                'service_schedules.time_start',
                'service_schedules.time_end',
                'service_schedules.batch',
                DB::raw('CONCAT(student_details.lastname, ", ", student_details.firstname) as student_name'),
                DB::raw('CONCAT(instructor_details.firstname, " ", instructor_details.lastname) as instructor_name'),
                'students.id as student_id',
                'enrollments.student_id'
            )
            ->whereNotNull('reschedule_enrollments.enrollment_id')
            ->where('enrollments.status', Enrollment::STATUS_ACTIVE)
            ->where('service_schedules.status', '!=', ServiceSchedule::STATUS_COMPLETE)
            ->groupBy('reschedule_enrollments.day_of_week')
            ->orderBy('service_schedules.day_of_week', 'DESC')
            ->orderBy('service_schedules.time_start', 'DESC')
            ->get();

        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }
}
