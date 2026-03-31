<?php

namespace App\Http\Resources\Notifications;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Notifications\NotificationBroadcast
 */
class NotificationBroadcastResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'topic' => $this->topic,
            'title' => $this->title,
            'body' => $this->body,
            'image' => $this->image
                ? (str_starts_with((string) $this->image, 'http')
                    ? $this->image
                    : storage_public_url($this->image))
                : null,
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'data' => $this->data,
            'sent_at' => $this->sent_at?->toIso8601String(),
            'created_by_admin_id' => $this->created_by_admin_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
