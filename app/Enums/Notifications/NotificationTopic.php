<?php

namespace App\Enums\Notifications;

enum NotificationTopic: string
{
    case AllUsers = 'AllUsers';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::AllUsers->value => __('filament.notifications.all_users'),
        ];
    }
}
