<?php

namespace App\Notifications;

use App\Notifications\BaseNotification;

class ServiceCreatedNotification extends BaseNotification
{
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(String $service)
    {
        $this->data = ['service' => $service];
    }

    public function getIconClass(): ?string
    {
        return 'info';
    }

    public function getTitle(): string
    {
        return 'Enroll to new Service now!';
    }

    public function getDescription(): string
    {
        return 'Check out our new service, "'.$this->data['service'].'".';
    }
}
