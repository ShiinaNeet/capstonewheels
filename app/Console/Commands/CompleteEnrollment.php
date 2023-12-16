<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Schedule\ServiceSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

use function Laravel\Prompts\error;

class CompleteEnrollment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:complete-enrollment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $query = Enrollment::where('status', Enrollment::STATUS_ACTIVE)
                ->whereDate('created_at', Carbon::yesterday())
                ->get();
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
            $now = Carbon::now();
            $enrollmentToComplete = Enrollment::join('users', 'enrollments.student_id', '=', 'users.id')
                ->leftJoin('payments', 'enrollments.student_id', '=', 'payments.student_id')
                ->leftJoin('services', 'enrollments.service_id', '=', 'services.id')
                ->leftJoin('payment_items', 'enrollments.id', '=', 'payment_items.enrollment_id')
                ->leftJoin('service_schedules', function ($join) {
                    $join->on('services.id', '=', 'service_schedules.service_id')
                        ->on('enrollments.batch', '=', 'service_schedules.batch');
                })
                ->where('enrollments.status', Enrollment::STATUS_ACTIVE)
                ->where('payments.status', Payment::STATUS_VERIFIED)
                ->where('service_schedules.day_of_week', '<', $now->toDateString())
                ->where('service_schedules.status', '=', ServiceSchedule::STATUS_COMPLETE)
                ->get();

            foreach ($enrollmentToComplete as $enrollment) 
            {
                $this->info('Enrollment ID: ' . $enrollment->id);
                
                $enrollment->status = Enrollment::STATUS_COMPLETED;
                $enrollment->save();
                $this->info('Enrollment status updated to 0 for ID: ' . $enrollment->id);
                
            }
        }
        catch (\Exception $e) {
            $this->error('Error updating complete status:'. $e->getmessage());
        }
    }
}
