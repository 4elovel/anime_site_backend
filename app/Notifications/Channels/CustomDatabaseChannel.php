<?php

namespace Liamtseva\Cinema\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Liamtseva\Cinema\Models\User;

class CustomDatabaseChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toCustomDatabase')) {
            $notification->toCustomDatabase($notifiable);
        }
    }
}