<?php

namespace App\Policies\User;

use App\Models\User\Permission;
use Illuminate\Contracts\Auth\Authenticatable;

class PermissionPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('view_permissions');
    }

    public function view(Authenticatable $user, Permission $permission): bool
    {
        return $user->can('view_permissions');
    }
}
