<?php

namespace App\Http\Resources\User;

use App\Models\User\Admin;
use App\Models\User\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms an Admin model for API responses (login, me).
 *
 * Roles are included only when the relation has been eager-loaded.
 * Permissions always reflect getAllPermissions() so that role-based
 * permissions are included without a separate eager-load step.
 *
 * Response shape matches the current adminWithRolesPermissions() output
 * exactly to avoid breaking clients.
 *
 * @mixin \App\Models\User\Admin
 */
class AdminResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Admin $admin */
        $admin = $this->resource;

        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'phone' => $admin->phone,
            'is_active' => $admin->is_active,
            // Roles are always loaded before this resource is used in auth endpoints.
            // The map preserves all three name variants (locale + bilingual) to match
            // the existing client contract.
            'roles' => $this->whenLoaded('roles', function () use ($admin) {
                // Spatie's HasRoles returns Collection<int, Model> generically;
                // the actual items are always Role instances at runtime.
                /** @var \Illuminate\Database\Eloquent\Collection<int, Role> $roles */
                $roles = $admin->roles;

                return $roles->map(fn (Role $r) => [
                    'id' => $r->id,
                    'name' => $r->name,
                    'name_ar' => $r->name_ar,
                    'name_en' => $r->name_en,
                ])->all();
            }),
            // getAllPermissions() includes both direct and role-inherited permissions.
            'permissions' => $admin->getAllPermissions()->pluck('name'),
        ];
    }
}
