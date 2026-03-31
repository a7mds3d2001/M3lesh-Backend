<?php

namespace App\Policies\User;

use App\Models\User\Role;
use Illuminate\Contracts\Auth\Authenticatable;

class RolePolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('view_roles');
    }

    public function view(Authenticatable $user, Role $role): bool
    {
        return $user->can('view_roles');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('create_roles');
    }

    public function update(Authenticatable $user, Role $role): bool
    {
        return $user->can('edit_roles');
    }

    public function delete(Authenticatable $user, Role $role): bool
    {
        return $user->can('delete_roles');
    }
}
