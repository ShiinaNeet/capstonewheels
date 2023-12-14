<?php

namespace App\Console\Commands;

use App\Mail\RescheduledSessionMail;
use App\Models\Service;
use App\Models\Schedule\ServiceSchedule;
use App\Models\User;
use App\Notifications\Notify;
use App\Notifications\RescheduledSessionNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRescheduledSessionNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-rescheduled-session-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification for rescheduled session';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
        //Query the the user for the email to be sent to. 
        // $student->email to get the email
        $student = User::where('id', 4)->first();

        //Query student. Reason why this uses get() because Notify:send() can send to multple people
        $studentnotify =  User::where('id', 4)->get();
        //Sample service of updated schedule
        //Get the service of the rescheduled session the student is enrolled to
        $service = Service::where('id', 1)->first();

        //Sample Schedule
        //Query the update schedule here
        $updatedSchedule = ServiceSchedule::where('batch', 2)->get();
        
        //1st param = Service
        //2nd param = Updated schedules
        Mail::to($student->email)->send(new RescheduledSessionMail($service, $updatedSchedule));
        if($studentnotify){

            $this->rescheduled_session_notify($studentnotify,$updatedSchedule);
        }else{
            info('no student to send');
        }
        }catch (\Exception $e) {
            $this->error('Error sending email to: ' . $student->email . ' - ' . $e->getMessage());
        }
    }
    // Add this to controller where you can call this
    public function rescheduled_session_notify($studentnotify,$updatedSchedule){

        Notify::send($studentnotify, new RescheduledSessionNotification($updatedSchedule));
    }
}
