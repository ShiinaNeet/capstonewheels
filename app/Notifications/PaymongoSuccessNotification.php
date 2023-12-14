<?php

namespace App\Notifications;

use App\Notifications\BaseNotification;

class PaymongoSuccessNotification extends BaseNotification
{
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
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
        return 'Payment Success';
    }

    public function getDescription(): string
    {
        return 'Your payment reference "'.$this->data['reference'].'" is successfully paid and are now enrolled to corresponding services.';
    }
}
