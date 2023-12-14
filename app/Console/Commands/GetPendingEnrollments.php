<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Mail\PendingEnrollmentsNotification;
use App\Models\User;
use App\Models\Service;
use App\Models\Payment;
use App\Models\PaymentItem;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GetPendingEnrollments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enrollments:get-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get enrollments with status pending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Enrollment::where('status', 3)
            ->whereDate('created_at', Carbon::yesterday())
            ->get();

        // Loop to send Email and update status
        foreach ($query as $enrollment) {
            $this->info('Enrollment ID: ' . $enrollment->id);

            $student = User::where('id', $enrollment->student_id)->first();
            $service = Service::where('id', $enrollment->service_id)->first();

            if ($student) {
                $studentEmail = $student->email;
                $serviceName = $service->name;

                try {
                    // Send email
                    Mail::to($studentEmail)->send(new PendingEnrollmentsNotification($enrollment, $serviceName));
                    // Console confirmation
                    $this->info('Email sent successfully to: ' . $studentEmail);

                    // Update enrollment status to 0
                    $enrollment->update(['status' => 0]);
                    $pending = Payment::leftJoin('payment_items', 'payments.id', '=', 'payment_items.payment_id')
                        ->leftJoin('enrollments', 'payment_items.enrollment_id', '=', 'enrollments.id')
                        ->where('payments.status', 0)
                        ->first();
                    $pending->update(['status'=> 2]);
                    $this->info('Enrollment status updated to 0 for ID: ' . $enrollment->id);
                } catch (\Exception $e) {
                    $this->error('Error sending email to: ' . $studentEmail . ' - ' . $e->getMessage());
                }
            } else {
                // Handle the case where no user is found with the given student_id
                $this->error('No user found for student_id: ' . $enrollment->student_id);
            }
        }
    }
}
