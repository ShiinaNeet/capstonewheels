<?php

namespace App\Notifications;

use App\Notifications\BaseNotification;

class NewsCreatedNotification extends BaseNotification
{
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(String $news)
    {
        $this->data = ['news' => $news];
    }

    public function getIconClass(): ?string
    {
        return 'info';
    }

    public function getTitle(): string
    {
        return 'News';
    }

    public function getDescription(): string
    {
        return 'A news landed, "'.$this->data['news'].'", has been added to our News Page.';
    }
}
