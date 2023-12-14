<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Notify
{
    public static function send($notifiables, $notification)
    {
        foreach($notifiables as $notifiable) {
            DB::table('notifications')->insert([
                'user_id' => $notifiable->id,
                'type' => get_class($notification),
                'icon' => $notification->getIconClass(),
                'title' => $notification->getTitle(),
                'description' => $notification->getDescription(),
                'read_at' => null,
                'created_at' => Carbon::now()
            ]);
        }
    }
}
