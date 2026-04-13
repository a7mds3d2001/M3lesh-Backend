<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Concerns\ReturnsAuditAdminObject;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Post\Post
 */
class AdminPostResource extends JsonResource
{
    use ReturnsAuditAdminObject;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'body' => $this->body,
            'is_active' => $this->is_active,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'user' => $this->whenLoaded('user', fn () => UserResource::make($this->user)->resolve($request)),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
            'created_by' => $this->auditAdminObject('creator'),
            'updated_by' => $this->auditAdminObject('updater'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
