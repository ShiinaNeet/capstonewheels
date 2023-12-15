<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuccessPaymentToAdminOnly extends Notification
{
    use Queueable;

    public $data;
    public function __construct(String $reference)
    {
        $this->data = ['reference' => $reference];

    }

    public function getIconClass(): ?string
    {
        return 'info';
    }

    public function getTitle(): string
    {
        return 'Student Successful Payment';
    }

    public function getDescription(): string
    {
        return 'Student has paid successfully with "'.$this->data['reference'].'".';
        
    }
}
