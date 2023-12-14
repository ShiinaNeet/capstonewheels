<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Schedule\ServiceSchedule;
use App\Models\User;
use App\Notifications\Notify;
use App\Notifications\PaymongoSuccessNotification;
// use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    public function get_student_verified_payments()
    {
        $payment_item = function($q) {
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
        $query = Payment::select('payments.id', 'payments.reference_no', 'payments.mode_of_payment', 'payments.created_at')
            ->where('student_id', Auth::id())
            ->where('status', Payment::STATUS_VERIFIED)
            ->orderBy('created_at', 'DESC')
            ->get()->map($payment_item);
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function payment_success()
    {
        $paymongo_id = Session::get('paymongo_id');

        if ($paymongo_id) {
            $payment_id = Session::get('payment_id');
            $payment = Payment::find($payment_id);
            $payment->status = Payment::STATUS_VERIFIED;
            if ($payment->save()) Session::forget(['paymongo_id', 'payment_id']);

            PaymentItem::leftJoin('enrollments', 'payment_items.enrollment_id', '=', 'enrollments.id')
                ->where('payment_items.payment_id', $payment_id)
                ->update(['enrollments.status' => Enrollment::STATUS_ACTIVE]);
            $this->paymongo_success_notify($payment->reference_no);
        }
        // $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions/'.$payment_id)
        //     ->withHeader('accept: application/json')
        //     ->withHeader('Authorization: Basic ' . env('AUTH_PAY', ''))
        //     ->asJson()
        //     ->get();
        // if ($paymongo_id) {
        //     Session::forget(['paymongo_id', 'payment_id']);
        //     return redirect()->to('/enrollment');
        // }
        return redirect()->to('/dashboard');
    }

    public function confirm_payment(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'payment_id' => 'required',
            'amount_to_pay' => 'numeric|required',
            'amount_paid' => 'numeric|required',
        ], [
            'payment_id.required' => 'The payment information is invalid.',
        ]);

        DB::transaction(function() use($request) {
            $change = 0;
            $payment = Payment::find($request->payment_id);
            $payment->payment_amount = $request->amount_paid;
            if ($request->amount_paid > $request->amount_paid_to_pay)
                $change = ($request->amount_paid - $request->amount_paid_to_pay);
            $payment->change_amount = $change;
            $payment->status = Payment::STATUS_VERIFIED;
            $payment->save();

            PaymentItem::leftJoin('enrollments', 'payment_items.enrollment_id', '=', 'enrollments.id')
                ->where('payment_items.payment_id', $request->payment_id)
                ->update(['enrollments.status' => Enrollment::STATUS_ACTIVE]);

            //Notification
            $this->cash_success_notify($payment->reference_no,$payment->student_id);

            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_PAYMENT, AuditTrail::ACTION_UPDATE, "Payment for " . $payment->reference_no . ""
            );
        });
        $rs = SharedFunctions::success_msg('Payment saved');
        return response()->json($rs);
    }

    private function paymongo_success_notify($ref)
    {
        $users = User::where('id', Auth::id())->get();
        Notify::send($users, new PaymongoSuccessNotification($ref));
    }
    private function cash_success_notify($ref,$student_id)
    {
        $users = User::where('id', $student_id)->get();
        Notify::send($users, new PaymongoSuccessNotification($ref));
    }
}
