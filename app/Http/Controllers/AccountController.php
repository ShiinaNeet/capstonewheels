<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Mail\ForgotPasswordMail;
use App\Mail\SendPassword;
use App\Models\AuditTrail;
use App\Models\Enrollment;
use App\Models\Schedule\ServiceSchedule;
use App\Models\RescheduleEnrollment;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AccountController extends Controller
{
    public function register(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'email' => 'required|max:120|regex:/(.+)@(.+)\.(.+)/i'
        ], [
            'email.required' => "Please check your credential and try again."
        ]);
        $password = SharedFunctions::generate_random_string(10, true, true, true);

        $check = User::where('email', $request->email)
            ->whereNull('deleted_at')
            ->count();
        if ($check > 0) {
            $rs = SharedFunctions::prompt_msg("This credential is already linked to an account");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_NOTICE, "Account credential " . $request->email . " exist"
            );
            goto end;
        }

        $user = new User();
        $user->email = $request->email;
        $user->user_type = 0;
        $user->password = bcrypt($password);
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL))
            Mail::to($request->email)->send(new SendPassword($password, 'student'));

        if ($user->save()) {
            $rs = SharedFunctions::success_msg("Your account is created, please check your email");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_CREATE, "Account " . $request->email . " created"
            );
        }
        end: return response()->json($rs);
    }

    public function delete_account(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = User::find($request->id);
        $check = ServiceSchedule::where('instructor_id', $request->id)
            ->where('status', ServiceSchedule::STATUS_ACTIVE)
            ->count();
        if ($check > 0) {
            $rs = SharedFunctions::prompt_msg("The account is in active service and can't be deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_NOTICE, "Account " . $query->email . " delete attempted"
            );
            goto end;
        }

        if ($query->delete()) {
            $rs = SharedFunctions::success_msg("Account deactivated");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_UPDATE, "Account " . $query->email . " deleted"
            );
        }
        end: return response()->json($rs);
    }

    public function save_account(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'email' => 'required|max:120|regex:/(.+)@(.+)\.(.+)/i',
            'user_type' => 'required'
        ]);
        $new_account = false;
        if (isset($request->id)) $user = User::find($request->id);
        else { $user = new User(); $new_account = true; }
        $password = SharedFunctions::generate_random_string(10, true, true, true);
        $user->email = $request->email;
        $user->user_type = $request->user_type;
        if ($new_account) $user->password = bcrypt($password);

        $user_types = ['student', 'instructor', 'secretary', 'administrator'];
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL))
            Mail::to($request->email)->send(new SendPassword($password, $user_types[$request->user_type]));

        if ($user->save()) {
            if ($new_account) {
                $rs = SharedFunctions::success_msg("Account created");
                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_CREATE, "Account " . $request->email . " created"
                );
            } else {
                $rs = SharedFunctions::success_msg("Account saved");
                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_UPDATE, "Account " . $request->email . " updated"
                );
            }
        }
        return response()->json($rs);
    }

    public function update_account_details(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $validations = [
            'firstname' => 'required',
            'lastname' => 'required',
            'mobile' => 'required'
        ];
        $addtlValidations = $licenseValidations = [];

        $query = User::find(Auth::id());
        if (isset($query->user_type) && ($query->user_type === 0 || $query->user_type === 1)) {
            $addtlValidations = [
                'gender' => 'numeric|required',
                'birthdate' => 'required',
                'address' => 'required',
                'barangay' => 'required',
                'city' => 'required',
                'province' => 'required',
                'region' => 'required'
            ];
        }

        if (isset($request->license_no) && !empty($request->license_no)) {
            // $validations['restriction_codes'] = 'required';
            // $validations['expiration_date'] = 'required';

            $licenseValidations = [
                'restriction_codes' => 'required',
                'expiration_date' => 'required'
            ];
        }
        $validations += $addtlValidations + $licenseValidations;

        $this->validate($request, $validations, [
            'gender.numeric' => "The gender field is required",
        ]);

        $restriction_codes = null;
        $user_details = UserDetail::where('user_id', Auth::id())->first();
        if (!$user_details) $user_details = new UserDetail();

        $user_details->user_id = Auth::id();
        $user_details->firstname = $request->firstname;
        $user_details->middlename = $request->middlename;
        $user_details->lastname = $request->lastname;
        $user_details->gender = $request->gender;
        $user_details->birthdate = $request->birthdate;
        $user_details->address = $request->address;
        $user_details->barangay = $request->barangay;
        $user_details->city = $request->city;
        $user_details->province = $request->province;
        $user_details->region = $request->region;
        $user_details->mobile = $request->mobile;
        $user_details->license_no = $request->license_no;
        if (isset($request->restriction_codes) && count($request->restriction_codes) > 0) {
            $restriction_codes  = $request->restriction_codes_old ? 'old' : 'new';
            $restriction_codes .= ":";
            $restriction_codes .= implode((count($request->restriction_codes) > 1 ? "|" : ""), $request->restriction_codes);
        }
        $user_details->restriction_codes = $restriction_codes;
        $user_details->expiration_date = $request->expiration_date;

        if ($user_details->save()) {
            $rs = SharedFunctions::success_msg("Account details updated");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_UPDATE, "Account details updated"
            );
        }
        return response()->json($rs);
    }

    public function update_account_password(Request $request)
    {
        $rs = SharedFunctions::prompt_msg("Password incorrect");
        $this->validate($request, [
            'password.crt' => 'required',
            'password.new' => 'required|min:8|max:120'
        ]);

        $query = User::find(Auth::id());
        if (Hash::check($request->password['crt'], $query->password)) {
            if (Hash::check($request->password['new'], $query->password)) {
                $rs['status'] = 2;
                $rs['message'] = "Password conflict";
                return response()->json($rs);
            }
            $query->password = bcrypt($request->password['new']);
            if ($query->save()) {
                $rs = SharedFunctions::success_msg("Password changed");
                SharedFunctions::create_audit_log(
                    AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_UPDATE, "Account password " . $request->email . " updated"
                );
            }
        } return response()->json($rs);
    }

    public function delete_enrolled(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'id' => 'required',
            'services' => 'required',
        ], [
            'services.required' => "The service(s) field is required"
        ]);

        foreach($request->services as $id) {
            $query = Enrollment::where('student_id', $request->id)
                ->where('service_id', $id)
                ->get();
            foreach($query as $q) {
                $q->status = Enrollment::STATUS_CANCELLED;
                $q->save();

                $schedules = ServiceSchedule::where('service_id', $id)
                    ->where('batch', $q->batch)
                    ->get();
                foreach($schedules as $schedule) {
                    $qq = new RescheduleEnrollment();
                    $qq->enrollment_id = $q->id;
                    $qq->day_of_week = $schedule->day_of_week;
                    $qq->save();
                }
            }
        }
        $rs = SharedFunctions::success_msg("Student archived");
        return response()->json($rs);
    }

    public function rescheduled_enrolled(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'id' => 'required',
            'services' => 'required',
        ], [
            'services.required' => "The service(s) field is required"
        ]);

        foreach($request->services as $id) {
            $query = Enrollment::where('student_id', $request->id)
                ->where('service_id', $id)
                ->get();
            foreach($query as $q) {
                $q->status = Enrollment::STATUS_ACTIVE;
                $q->save();

                $schedules = ServiceSchedule::where('service_id', $id)
                    ->where('batch', $q->batch)
                    ->get();
                foreach($schedules as $schedule) {
                    $qq = new RescheduleEnrollment();
                    $qq->enrollment_id = $q->id;
                    $qq->day_of_week = $schedule->day_of_week;
                    $qq->save();
                }
            }
        }
        $rs = SharedFunctions::success_msg("Student moved to rescheduled");
        return response()->json($rs);
    }


    public function save_enrolled(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'services' => 'required',
            'student_id' => 'required',
            'modif' => 'required',
        ]);

        if (!$request->modif) {
            // create new student enrollment
            $exists = false;
            foreach($request->services as $id) {
                $query = Enrollment::where('student_id', $request->student_id)
                    ->where('service_id', $id)
                    ->where('status', [Enrollment::STATUS_ACTIVE])
                    ->first();
                if ($query) $exists = true;
            }
            if ($exists) {
                $rs = SharedFunctions::prompt_msg("Student is already at a service");
                goto end;
            }
            foreach($request->services as $id) {
                $batch = ServiceSchedule::where('service_id', $id)
                    ->where('instructor_id', Auth::id())
                    ->select(DB::raw("MAX(batch) as max_batch"))
                    ->pluck('max_batch')
                    ->first();
                if ($id) {
                    $query = new Enrollment();
                    $query->student_id = $request->student_id;
                    $query->service_id = $id;
                    $query->batch = $batch;
                    $query->status = Enrollment::STATUS_ACTIVE;
                    $query->save();
                }
            }
        } else {
            // edit existing student enrollment
            $query = Enrollment::where('student_id', $request->student_id)
                ->where('service_id', $request->services)
                ->where('status', [Enrollment::STATUS_ACTIVE])
                ->first();
            if ($query) {
                $rs = SharedFunctions::prompt_msg("Student is already at this service");
                goto end;
            }
            $cancels = Enrollment::where('student_id', $request->student_id)
                ->where('status', [Enrollment::STATUS_ACTIVE])
                ->get();
            foreach($cancels as $cancel) {
                $cancel->status = Enrollment::STATUS_CANCELLED;
                $cancel->save();
            }
            $batch = ServiceSchedule::where('service_id', $request->services)
                ->where('instructor_id', Auth::id())
                ->select(DB::raw("MAX(batch) as max_batch"))
                ->pluck('max_batch')
                ->first();
            $query = new Enrollment();
            $query->student_id = $request->student_id;
            $query->service_id = $request->services;
            $query->batch = $batch;
            $query->status = Enrollment::STATUS_ACTIVE;
            $query->save();
        }
        $rs = SharedFunctions::success_msg("Student saved");
        end: return response()->json($rs);
    }

    public function login(Request $request)
    {
        $rs = SharedFunctions::prompt_msg('Login failed');
        $this->validate($request, [
            'email' => 'required|max:120|regex:/(.+)@(.+)\.(.+)/i',
            'password' => 'required|max:255'
        ]);
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            Auth::attempt([
                'email'     => $request->email,
                'password'  => $request->password
            ]);
        }
        if (Auth::check()) {
            $rs = SharedFunctions::success_msg('Login success');
            $rs['redirect'] = '/dashboard';
            $query = UserDetail::where('user_id', Auth::id())->count();
            if ($query == 0) $rs['redirect'] = '/account';
        }
        return response()->json($rs);
    }

    public function get_accounts()
    {
        $accounts = User::withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $accounts;
        return response()->json($rs);
    }

    public function get_account_details()
    {
        $query = UserDetail::where('user_id', Auth::id())->first();
        if ($query) $query->email = Auth::user()->email;
        else $query = ['email' => Auth::user()->email];
        $rs = SharedFunctions::success_msg('Success');
        if ($query && $query->restriction_codes) {
            $query->restriction_codes_old = explode(":", $query->restriction_codes)[0] == 'old' ? true : false;
            $query->restriction_codes = array_map(function($item) {
                return (int) $item;
            }, explode("|", explode(":", $query->restriction_codes)[1]));
        }
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_enrolled_students()
    {
        $query = Enrollment::leftJoin('services', 'enrollments.service_id', '=', 'services.id')
            ->leftJoin('service_schedules', function ($join) {
                $join->on('services.id', '=', 'service_schedules.service_id')
                    ->on('enrollments.batch', '=', 'service_schedules.batch');
            })->leftJoin('users', 'enrollments.student_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('enrollments.created_at as date_enrolled', 'enrollments.student_id')
            ->addSelect(DB::raw("CONCAT(user_details.lastname, ', ', user_details.firstname) as name"))
            ->where('service_schedules.instructor_id', Auth::id())
            ->whereIn('enrollments.status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->groupBy('enrollments.student_id')
            ->orderBy('name')
            ->get()->map(function($q) {
                $enrolled_services = Enrollment::leftJoin('services', 'enrollments.service_id', '=', 'services.id')
                    ->whereIn('enrollments.status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
                    ->where('enrollments.student_id', $q->student_id)
                    ->select('services.id', 'services.name')->get();
                $service_id = [];
                $q->services_name = "";
                foreach($enrolled_services as $key => $serv) {
                    $service_id[] = $serv->id;
                    $q->services_name .= $serv->name;
                    if (($key + 1) != count($enrolled_services)) $q->services_name .= ", ";
                } $q->service_id = $service_id;
                return $q;
            });
        $services = ServiceSchedule::leftJoin('services', 'service_schedules.service_id', '=', 'services.id')
            ->where('service_schedules.instructor_id', Auth::id())
            ->whereIn('service_schedules.status', [ServiceSchedule::STATUS_ACTIVE, ServiceSchedule::STATUS_COMPLETE])
            ->select('services.id', 'services.name')
            ->groupBy('service_schedules.service_id')
            ->get();

        $rs = SharedFunctions::success_msg('Success.');
        $rs['result'] = ['services' => $services, 'students' => $query];
        return response()->json($rs);
    }

    public function get_enrolled_students_for_admin()
    {
        $query = Enrollment::leftJoin('services', 'enrollments.service_id', '=', 'services.id')
            ->leftJoin('reschedule_enrollments', 'enrollments.id', '=', 'reschedule_enrollments.enrollment_id')
            ->leftJoin('service_schedules', function ($join) {
                $join->on('services.id', '=', 'service_schedules.service_id')
                    ->on('enrollments.batch', '=', 'service_schedules.batch');
            })->leftJoin('users', 'enrollments.student_id', '=', 'users.id')
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('enrollments.created_at as date_enrolled', 'enrollments.student_id')
            ->addSelect(DB::raw("CONCAT(user_details.lastname, ', ', user_details.firstname) as name"))
            ->whereIn('enrollments.status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->whereNull('reschedule_enrollments.enrollment_id')
            ->groupBy('enrollments.student_id')
            ->orderBy('name')
            ->get()->map(function($q) {
                $enrolled_services = Enrollment::leftJoin('services', 'enrollments.service_id', '=', 'services.id')
                    ->whereIn('enrollments.status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
                    ->where('enrollments.student_id', $q->student_id)
                    ->select('services.id', 'enrollments.batch', 'services.name')->get();
                $batches = [];
                $service_id = [];
                $q->services_name = "";
                foreach($enrolled_services as $key => $serv) {
                    $batches[] = $serv->batch;
                    $service_id[] = $serv->id;
                    $q->services_name .= $serv->name;
                    if (($key + 1) != count($enrolled_services)) $q->services_name .= ", ";
                } $q->service_id = $service_id;
                $q->batches = $batches;
                return $q;
            });
        $services = ServiceSchedule::leftJoin('services', 'service_schedules.service_id', '=', 'services.id');

        if (Auth::user()->user_type === 1) $services = $services->where('service_schedules.instructor_id', Auth::id());

        $services = $services->whereIn('service_schedules.status', [ServiceSchedule::STATUS_ACTIVE, ServiceSchedule::STATUS_COMPLETE])
            ->select('services.id', 'services.name')
            ->groupBy('service_schedules.service_id')
            ->get();

        $rs = SharedFunctions::success_msg('Success.');
        $rs['result'] = ['services' => $services, 'students' => $query];
        return response()->json($rs);
    }

    public function get_instructors()
    {
        $accounts = User::where('user_type', 1)
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('users.*')
            ->addSelect('user_details.firstname', 'user_details.lastname')
            ->orderBy('users.created_at', 'DESC')
            ->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $accounts;
        return response()->json($rs);
    }

    public function get_staffs()
    {
        $accounts = User::where('user_type', '!=', 0)
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('users.*')
            ->addSelect('user_details.firstname', 'user_details.lastname')
            ->orderBy('users.created_at', 'DESC')
            ->get();
        $rs = SharedFunctions::success_msg('Success.');
        $rs['result'] = $accounts;
        return response()->json($rs);
    }

    public function get_password(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, ['email' => 'required|email'], ['email.required' => 'Please check your credentials and try again', 'email.email' => 'Invalid email format']);
        $password = SharedFunctions::generate_random_string(10, true, true, true);

        $check = User::where('email', $request->email)
            ->whereNull('deleted_at')
            ->count();
        if ($check == 0) {
            $rs = SharedFunctions::prompt_msg("This email is not associated to any account");
            goto end;
        }
        $user = User::where('email', $request->email)
            ->whereNull('deleted_at')
            ->first();

        $user->password = bcrypt($password);

        if (filter_var($request->email, FILTER_VALIDATE_EMAIL))

        if ($user->save()) {
            $rs = SharedFunctions::success_msg("Reset success! Please check your email for you new password!");
            //send mail here
            Mail::to($request->email)->send(new ForgotPasswordMail($password, 'student'));

            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ACCOUNT, AuditTrail::ACTION_CREATE, "Account " . $request->email . " has requested new password"
            );
        }
        end: return response()->json($rs);
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }
}
