<?php

namespace App\Policies\Notifications;

use App\Models\Notifications\NotificationBroadcast;
use App\Models\User\Admin;
use Illuminate\Contracts\Auth\Authenticatable;

class NotificationBroadcastPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof Admin
            && ($user->can('view_notification_broadcasts')
                || $user->can('send_notification_broadcasts'));
    }

    public function view(Authenticatable $user, NotificationBroadcast $notificationBroadcast): bool
    {
        return $user instanceof Admin && $user->can('view_notification_broadcasts');
    }

    public function create(Authenticatable $user): bool
    {
        return $user instanceof Admin && $user->can('send_notification_broadcasts');
    }
}
