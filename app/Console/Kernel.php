<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CheckServiceSchedules;
use App\Console\Commands\CompleteEnrollment;
use App\Console\Commands\DeleteOldInquiry;
use App\Console\Commands\GetPendingEnrollments;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(GetPendingEnrollments::class)->everyMinute();
        $schedule->command(CheckServiceSchedules::class)->everyMinute();
        $schedule->command(DeleteOldInquiry::class)->everyMinute();
        $schedule->command(CompleteEnrollment::class)->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
