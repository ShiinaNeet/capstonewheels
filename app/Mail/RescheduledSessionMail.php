<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RescheduledSessionMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $service;
    protected $service_schedule;

    public function __construct($service,$service_schedule)
    {
        $this->service = $service;
        $this->service_schedule = $service_schedule;
    }

    public function build()
    {
        return $this->markdown('wheels.email.rescheduledsessions', [
            'service' => $this->service,
            'service_schedule' => $this->service_schedule,
        ])->subject('Session Rescheduled Notification');
    }
}
