<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function index()
    {
        if (Auth::check()) return redirect(url('/dashboard'));
        $data['css'] = ['global'];
        return view('wheels.login', $data);
    }

    public function register()
    {
        $data['css'] = ['global'];
        return view('wheels.register', $data);
    }

    public function account()
    {
        $data['css'] = ['global'];
        return view('wheels.account', $data);
    }

    public function dashboard()
    {
        $query = UserDetail::where('user_id', Auth::id())->count();
        // $user = Auth::user();
        $data['css'] = ['global'];
        if ($query == 0) return redirect('/account');
        // if ($user->user_type == User::TYPE_SECRETARY)
        //     return view('wheels.pending_enrollments', $data);
        // else if ($user->user_type == User::TYPE_INSTRUCTOR)
        //     return view('wheels.enrolled_students', $data);
        // else return view('wheels.dashboard', $data);
        return view('wheels.dashboard', $data);
    }

    public function enrollment_form($service_id, $batch)
    {
        $data['service_id'] = $service_id;
        $data['batch'] = $batch;
        $data['css'] = ['global'];
        return view('wheels.enrollment_form', $data);
    }

    public function newsevent()
    {
        $data['css'] = ['global'];
        return view('wheels.newsevent', $data);
    }

    public function student_enrollment($mode = null)
    {
        $data['mode'] = $mode;
        $data['css'] = ['global'];
        return view('wheels.student_enrollment', $data);
    }

    public function faq()
    {
        $data['css'] = ['global'];
        return view('wheels.faq', $data);
    }
    public function aboutus()
    {
        $data['css'] = ['global'];
        return view('wheels.aboutus', $data);
    }
    public function contactus()
    {
        $data['css'] = ['global'];
        return view('wheels.contactus', $data);
    }
    public function terms()
    {
        $data['css'] = ['global'];
        return view('wheels.terms', $data);
    }

}
