<?php

namespace App\Policies\User;

use App\Models\User\User;
use Illuminate\Contracts\Auth\Authenticatable;

class UserPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('view_users');
    }

    public function view(Authenticatable $user, User $model): bool
    {
        return $user->can('view_users');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('create_users');
    }

    public function update(Authenticatable $user, User $model): bool
    {
        return $user->can('edit_users');
    }

    public function delete(Authenticatable $user, User $model): bool
    {
        return $user->can('delete_users');
    }
}
