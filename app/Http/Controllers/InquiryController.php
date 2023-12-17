<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{
    public function get()
    {
        $query = Inquiry::leftJoin('users', 'inquiries.student_id', '=', 'users.id')
            ->selectRaw('COALESCE(users.email, inquiries.email) as email')
            ->addSelect('inquiries.id as id')
            ->addSelect('inquiries.subject', 'inquiries.inquiry', 'inquiries.deleted_at', 'inquiries.created_at')
            ->orderBy('inquiries.created_at', 'DESC')
            ->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        

        $new_inquiry = false;
        $inquiry = null;
        $inquiry = new Inquiry();

        if (Auth::check()) {
            // Authenticated user
            $this->validate($request, [
                'subject' => 'required|max:120',
                'inquiry' => 'required|max:255'
            ]);
            $user = User::find(Auth::id());
            $inquiry->student_id = $user->id;
            $inquiry->email = $user->email;
        } else {
            // Non-authenticated user
            $validationMessages = [
                'senderEmail.email' => 'The email address must be a valid email address.',
            ];
            $this->validate($request, [
                'senderEmail' => 'required|email',
                'subject' => 'required|max:120',
                'inquiry' => 'required|max:255'
            ],$validationMessages);
    
            $new_inquiry = true;
            $inquiry->email = $request->senderEmail;
        }

        $inquiry->subject = $request->subject;
        $inquiry->inquiry = $request->inquiry;
        
        if ($inquiry->save()) {
            $rs = SharedFunctions::success_msg("Inquiry recorded");
            $userEmail = Auth::check() ? Auth::user()->email : $request->senderEmail;
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_INQUIRY,
                $new_inquiry ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE,
                "Inquiry " . $request->subject . " submitted by " . $userEmail
            );
        }
        return response()->json($rs);
    }

    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = Inquiry::find($request->id);
        if ($query->forceDelete()) {
            $rs = SharedFunctions::success_msg("Inquiry deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_HELP, AuditTrail::ACTION_DELETE, "Inquiry " . $query->question . " deleted"
            );
        }
        return response()->json($rs);
    }
}
