<?php

namespace App\Policies;

use App\Models\Post\PostCommentPreset;
use Illuminate\Contracts\Auth\Authenticatable;

class PostCommentPresetPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('view_post_comment_presets');
    }

    public function view(Authenticatable $user, PostCommentPreset $postCommentPreset): bool
    {
        return $user->can('view_post_comment_presets');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('create_post_comment_presets');
    }

    public function update(Authenticatable $user, PostCommentPreset $postCommentPreset): bool
    {
        return $user->can('edit_post_comment_presets');
    }

    public function delete(Authenticatable $user, PostCommentPreset $postCommentPreset): bool
    {
        return $user->can('delete_post_comment_presets');
    }

    public function restore(Authenticatable $user, PostCommentPreset $postCommentPreset): bool
    {
        return $user->can('restore_post_comment_presets');
    }

    public function forceDelete(Authenticatable $user, PostCommentPreset $postCommentPreset): bool
    {
        return $user->can('force_delete_post_comment_presets');
    }
}
