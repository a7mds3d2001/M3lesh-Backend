<?php

namespace App\Policies\Notifications;

use App\Models\Notifications\Notification;
use App\Models\User\Admin;
use App\Models\User\User;
use Illuminate\Contracts\Auth\Authenticatable;

class NotificationPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        if ($user instanceof User) {
            return true;
        }

        if ($user instanceof Admin) {
            return $user->can('view_notifications') || $user->can('send_notifications');
        }

        return false;
    }

    public function view(Authenticatable $user, Notification $notification): bool
    {
        if ($user instanceof Admin) {
            if ($user->can('view_notifications') || $user->can('send_notifications')) {
                return true;
            }

            return $this->ownsNotification($user, $notification);
        }

        if (! $this->ownsNotification($user, $notification)) {
            return false;
        }

        if ($user instanceof User) {
            return true;
        }

        return false;
    }

    public function update(Authenticatable $user, Notification $notification): bool
    {
        if (! $this->ownsNotification($user, $notification)) {
            return false;
        }

        if ($user instanceof User) {
            return true;
        }

        if ($user instanceof Admin) {
            return $user->can('view_notifications');
        }

        return false;
    }

    public function send(Authenticatable $user): bool
    {
        return $user instanceof Admin && $user->can('send_notifications');
    }

    protected function ownsNotification(Authenticatable $user, Notification $notification): bool
    {
        if ($user instanceof Admin) {
            return $notification->notifiable_type === Admin::class
                && (int) $notification->notifiable_id === (int) $user->id;
        }

        if ($user instanceof User) {
            return $notification->notifiable_type === User::class
                && (int) $notification->notifiable_id === (int) $user->id;
        }

        return false;
    }
}
