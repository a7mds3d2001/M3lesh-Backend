<?php

namespace App\Http\Resources\SupportTicket;

use App\Http\Resources\Concerns\ReturnsAuditAdminObject;
use App\Http\Resources\Post\PostResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\SupportTicket\SupportTicket
 */
class SupportTicketResource extends JsonResource
{
    use ReturnsAuditAdminObject;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'user_id' => $this->user_id,
            'post_id' => $this->post_id,
            'post' => $this->whenLoaded('post', fn () => PostResource::make($this->post)->resolve($request)),
            'visitor_name' => $this->visitor_name,
            'visitor_phone' => $this->visitor_phone,
            'visitor_email' => $this->visitor_email,
            'message' => $this->message,
            'status' => $this->status,
            'priority' => $this->priority,
            'is_active' => $this->is_active,
            'attachments' => $this->when(
                ! empty($this->attachments),
                fn () => array_map(fn ($path) => str_starts_with((string) $path, 'http') ? $path : storage_public_url($path), $this->attachments ?? []),
                [],
            ),
            'user' => $this->whenLoaded('user', function () {
                /** @var \App\Models\User\User $user */
                $user = $this->user;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ];
            }),
            'created_by' => $this->auditAdminObject('creator'),
            'updated_by' => $this->auditAdminObject('updater'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'logs' => $this->whenLoaded('logs', fn () => SupportTicketLogResource::collection($this->logs)->resolve($request)),
        ];
    }
}
