<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEnrollee extends Notification
{
    use Queueable;
    public $data;
    public $serviceName;

    public function __construct(String $studentname,$serviceName)
    {
        $this->data = ['reference' => $studentname];
        $this->serviceName = $serviceName;
    }

    public function getIconClass(): ?string
    {
        return 'info';
    }

    public function getTitle(): string
    {
        return 'New Enrollee';
    }

    public function getDescription(): string
    {
        //return 'Your payment reference "'.$this->data['reference'].'" is successfully paid and are now enrolled to corresponding services.';
        return 'Student "'.$this->data['reference'].'" successfully enrolled to '.$this->serviceName.'.';
    }
}
