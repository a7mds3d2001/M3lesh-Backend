<?php

namespace App\Policies\Post;

use App\Models\Post\PostComment;
use App\Models\User\User;
use Illuminate\Contracts\Auth\Authenticatable;

class PostCommentPolicy
{
    public function delete(?Authenticatable $user, PostComment $comment): bool
    {
        return $user instanceof User && $user->id === $comment->user_id;
    }
}
