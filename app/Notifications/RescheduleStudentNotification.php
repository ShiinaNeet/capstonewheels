<?php

namespace App\Notifications;

use App\Notifications\BaseNotification;

class RescheduleStudentNotification extends BaseNotification
{
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($service, $date, $batch)
    {
        $this->data = [
            'service' => $service,
            'date' => $date,
            'batch' => $batch,
        ];
    }

    public function getIconClass(): ?string
    {
        return 'info';
    }

    public function getTitle(): string
    {
        return 'Enrollment Rescheduled';
    }

    public function getDescription(): string
    {
        return 'Your enrollment "'.$this->data['service'].'" is now rescheduled to batch no. '.$this->data['batch'].' ('.$this->data['date'].').';
    }
}
