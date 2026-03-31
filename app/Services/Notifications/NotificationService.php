<?php

namespace App\Services\Notifications;

use App\Jobs\Notifications\SendNotificationJob;
use App\Models\Notifications\Notification;
use App\Models\User\Admin;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Create notification row then dispatch push send.
     *
     * @param  Model  $notifiable  User/Admin (future: Shop auth)
     * @param  array{title:string,body?:string|null,image?:string|null,target_type?:string|null,target_id?:int|null}  $payload
     */
    public function notify(Model $notifiable, array $payload): Notification
    {
        /** @var Notification $notification */
        $notification = Notification::create([
            'notifiable_type' => $notifiable::class,
            'notifiable_id' => $notifiable->getKey(),
            'title' => $payload['title'],
            'body' => $payload['body'] ?? null,
            'image' => $payload['image'] ?? null,
            'target_type' => $payload['target_type'] ?? null,
            'target_id' => $payload['target_id'] ?? null,
            'data' => null,
            'type' => $notifiable instanceof Admin ? Notification::m3leshInboxType() : null,
        ]);

        SendNotificationJob::dispatch($notification->id);

        return $notification->refresh();
    }

    /**
     * @param  iterable<int, Model>  $notifiables
     * @param  array{title:string,body?:string|null,image?:string|null,target_type?:string|null,target_id?:int|null}  $payload
     * @return array<int, int> notification ids
     */
    public function notifyMany(iterable $notifiables, array $payload): array
    {
        $ids = [];
        foreach ($notifiables as $notifiable) {
            $ids[] = $this->notify($notifiable, $payload)->id;
        }

        return $ids;
    }
}
