<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RescheduledSessionNotification extends Notification
{
    use Queueable;
    public $data;

    public function __construct(string $service_schedule)
    {
        $this->data = ['service_schedule' => $service_schedule];
    }

    public function getIconClass(): ?string
    {
        return 'info';
    }

    public function getTitle(): string
    {
        return 'Rescheduled Session';
    }

    public function getDescription(): string
{
    return 'Your schedule has been rescheduled.';
}
}
