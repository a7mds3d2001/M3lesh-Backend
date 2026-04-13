<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\User\UserResource;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Post\Post
 */
class PostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum');

        $likedByMe = isset($this->liked_by_me)
            ? $this->liked_by_me > 0
            : ($user instanceof User && $this->likes()->where('user_id', $user->id)->exists());

        return [
            'id' => $this->id,
            'body' => $this->body,
            'is_active' => $this->is_active,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'liked_by_me' => $likedByMe,
            'recent_comments' => $this->when(
                $this->relationLoaded('recentComments'),
                fn () => PostCommentResource::collection($this->recentComments)->resolve($request),
            ),
            'user' => $this->whenLoaded('user', fn () => UserResource::make($this->user)->resolve($request)),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
