<?php

namespace App\Console\Commands;

use App\Models\Schedule\ServiceSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckServiceSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'serviceschedule:check-service-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks Service Schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Get current date and time
            $now = Carbon::now();

            // Get service schedules where time_end is before the current time
            $expiredSchedules = ServiceSchedule::whereDate('day_of_week', '<', $now->toDateString())
                ->where('status', '!=', ServiceSchedule::STATUS_COMPLETE)
                ->get();

            // Update the status of expired schedules, e.g., set status to 2 (finished)
            foreach ($expiredSchedules as $schedule) {
                $schedule->update(['status' => ServiceSchedule::STATUS_COMPLETE]);
                $this->info('Service Schedule ID ' . $schedule->id . ' marked as finished.');
            }
            $this->info('Service schedules checked successfully.');

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
