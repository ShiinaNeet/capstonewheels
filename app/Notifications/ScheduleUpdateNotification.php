<?php

namespace App\Notifications;

use App\Notifications\BaseNotification;

class ScheduleUpdateNotification extends BaseNotification
{
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(String $service, String $date)
    {
        $this->data = ['service' => $service, 'date' => $date];
    }

    public function getIconClass(): ?string
    {
        return 'info';
    }

    public function getTitle(): string
    {
        return 'Service Schedule Updated';
    }

    public function getDescription(): string
    {
        return 'The service schedule for '.$this->data['service'].' has been updated to '.$this->data['date'].'.';
    }
}
