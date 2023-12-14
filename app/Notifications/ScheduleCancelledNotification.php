<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ScheduleCancelledNotification extends Notification
{
    use Queueable;

    public $service;
    public $schedule;
    public function __construct($service,$schedule)
    {
        $this->schedule =  $schedule;
        $this->service =  $service;
    }

    public function getIconClass(): ?string
    {
        return 'info';
    }

    public function getTitle(): string
    {
        return 'Scheduled Cancelled';
    }

    public function getDescription(): string
    {
        return 'Instructor has canceled your '. $this->service->name .' session due to ' . $this->schedule->cancel_reason . '. Please wait for the rescheduled session.';
    }

}
