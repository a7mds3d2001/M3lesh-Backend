<?php

namespace App\Http\Resources\SupportTicket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\SupportTicket\SupportTicketLog
 */
class SupportTicketLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'actor_type' => $this->actor_type === \App\Models\User\Admin::class ? 'admin' : 'user',
            'actor_id' => $this->actor_id,
            'actor' => $this->whenLoaded('actor', function () {
                /** @var \App\Models\User\Admin|\App\Models\User\User|null $actor */
                $actor = $this->actor;

                return $actor ? [
                    'id' => $actor->id,
                    'name' => $actor->name ?? $actor->email ?? null,
                ] : null;
            }),
            'message' => $this->message,
            'log_type' => $this->log_type,
            'attachments' => $this->when(
                ! empty($this->attachments),
                fn () => array_map(fn ($path) => str_starts_with((string) $path, 'http') ? $path : storage_public_url($path), $this->attachments ?? []),
                [],
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
