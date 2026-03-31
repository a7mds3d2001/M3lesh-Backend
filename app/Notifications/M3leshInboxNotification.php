<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Stored in `notifications.type` for inbox rows created by the app (not dispatched).
 */
class M3leshInboxNotification extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [];
    }
}
