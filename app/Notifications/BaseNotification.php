<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['database'];

        if ($this->getMailable() != null) $channels[] = 'mail';

        return $channels;
    }

    /**
     * Get icon class for notification display
     * @return string|null
     */
    public function getIconClass(): ?string
    {
        return null;
    }

    /**
     * Get notification title
     *
     * @return string|null
     */
    abstract public function getTitle(): ?string;

    /**
     * Get notification description
     *
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * Get mailable for email notification
     *
     * @return Mailable|null
     */
    public function getMailable(): ?Mailable
    {
        return null;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return Mailable|null
     */
    public function toMail($notifiable): ?Mailable
    {
        $mailable = $this->getMailable();

        if ($mailable == null) return null;

        $mailable->to($notifiable);

        return $mailable;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray()
    {
        return [
            'icon_class' => $this->getIconClass(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription()
        ];
    }
}
