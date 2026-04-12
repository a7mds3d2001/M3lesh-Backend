<?php

namespace App\Policies\Post;

use App\Models\Post\Post;
use App\Models\User\Admin;
use App\Models\User\User;
use Illuminate\Contracts\Auth\Authenticatable;

class PostPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof Admin && $user->can('view_posts');
    }

    public function view(?Authenticatable $user, Post $post): bool
    {
        if ($user instanceof Admin) {
            return $user->can('view_posts');
        }

        if ($post->is_active) {
            return true;
        }

        return $user instanceof User && $user->id === $post->user_id;
    }

    public function create(Authenticatable $user): bool
    {
        return $user instanceof Admin && $user->can('create_posts');
    }

    public function update(Authenticatable $user, Post $post): bool
    {
        if ($user instanceof Admin) {
            return $user->can('edit_posts');
        }

        if ($user instanceof User) {
            return $user->id === $post->user_id;
        }

        return false;
    }

    public function delete(Authenticatable $user, Post $post): bool
    {
        if ($user instanceof Admin) {
            return $user->can('delete_posts');
        }

        if ($user instanceof User) {
            return $user->id === $post->user_id;
        }

        return false;
    }

    public function restore(Authenticatable $user, Post $post): bool
    {
        return $user instanceof Admin && $user->can('restore_posts');
    }

    public function forceDelete(Authenticatable $user, Post $post): bool
    {
        return $user instanceof Admin && $user->can('force_delete_posts');
    }

    public function moderateComments(Authenticatable $user, Post $post): bool
    {
        return $user instanceof Admin && $user->can('edit_posts');
    }

    public function moderateLikes(Authenticatable $user, Post $post): bool
    {
        return $user instanceof Admin && $user->can('edit_posts');
    }

    public function comment(User $user, Post $post): bool
    {
        return $post->is_active && ! $post->trashed();
    }

    public function like(User $user, Post $post): bool
    {
        return $post->is_active && ! $post->trashed();
    }

    public function report(User $user, Post $post): bool
    {
        if (! $post->is_active || $post->trashed()) {
            return false;
        }

        return $user->id !== $post->user_id;
    }
}
