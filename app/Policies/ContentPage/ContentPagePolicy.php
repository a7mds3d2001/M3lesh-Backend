<?php

namespace App\Policies\ContentPage;

use App\Models\ContentPage\ContentPage;
use Illuminate\Contracts\Auth\Authenticatable;

class ContentPagePolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('view_content_pages');
    }

    public function view(Authenticatable $user, ContentPage $contentPage): bool
    {
        return $user->can('view_content_pages');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('create_content_pages');
    }

    public function update(Authenticatable $user, ContentPage $contentPage): bool
    {
        return $user->can('edit_content_pages');
    }

    public function delete(Authenticatable $user, ContentPage $contentPage): bool
    {
        return $user->can('delete_content_pages');
    }
}
