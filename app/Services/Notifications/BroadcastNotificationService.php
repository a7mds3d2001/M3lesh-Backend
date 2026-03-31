<?php

namespace App\Services\Notifications;

use App\Enums\Notifications\NotificationTopic;
use App\Models\Notifications\NotificationBroadcast;
use App\Models\User\Admin;
use App\Services\Fcm\FcmService;

class BroadcastNotificationService
{
    /**
     * @param  array{title:string,body?:string|null,image?:string|null,target_type?:string|null,target_id?:int|null,data?:array<string,mixed>|null}  $payload
     */
    public function broadcast(NotificationTopic $topic, array $payload, ?Admin $admin = null): NotificationBroadcast
    {
        /** @var NotificationBroadcast $broadcast */
        $broadcast = NotificationBroadcast::create([
            'topic' => $topic->value,
            'title' => $payload['title'],
            'body' => $payload['body'] ?? null,
            'image' => $payload['image'] ?? null,
            'target_type' => $payload['target_type'] ?? null,
            'target_id' => $payload['target_id'] ?? null,
            'data' => $payload['data'] ?? null,
            'created_by_admin_id' => $admin?->id,
        ]);

        /** @var FcmService $fcm */
        $fcm = app(FcmService::class);

        $data = array_merge(
            [
                'target_type' => $broadcast->target_type,
                'target_id' => $broadcast->target_id,
            ],
            $broadcast->data ?? [],
        );

        $fcm->sendToTopic($topic->value, [
            'title' => $broadcast->title,
            'body' => $broadcast->body ?? '',
            'image' => $broadcast->image ? url('/storage/'.$broadcast->image) : null,
        ], $data);

        $broadcast->forceFill(['sent_at' => now()])->save();

        return $broadcast;
    }
}
