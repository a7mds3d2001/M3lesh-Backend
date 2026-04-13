<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Post\PostComment
 */
class PostCommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'display_text' => $this->displayBody(),
            'body' => $this->body,
            'preset_text_snapshot' => $this->preset_text_snapshot,
            'post_comment_preset_id' => $this->post_comment_preset_id,
            'user' => $this->whenLoaded('user', fn () => UserResource::make($this->user)->resolve($request)),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
