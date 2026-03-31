<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Concerns\ReturnsAuditAdminObject;
use App\Models\User\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;

/**
 * Transforms a Role model for API responses.
 * Returns the locale-based name only; name_ar / name_en are intentionally excluded.
 *
 * @mixin \App\Models\User\Role
 */
class RoleResource extends JsonResource
{
    use ReturnsAuditAdminObject;

    /**
     * Transform the resource into an array.
     * Returns a locale-based name only (not raw name_ar / name_en columns).
     * Callers that need both raw locale values (e.g. edit forms) should read
     * directly from the model layer, not from this API resource.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Role $role */
        $role = $this->resource;

        return [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'created_by' => $this->auditAdminObject('creator'),
            'updated_by' => $this->auditAdminObject('updater'),
            'created_at' => $role->created_at?->toIso8601String(),
            'updated_at' => $role->updated_at?->toIso8601String(),
            'permissions_count' => $this->whenCounted('permissions'),
            'permissions' => $this->whenLoaded('permissions', function () use ($role) {
                // Spatie's permissions relation returns Collection<int, Model> generically;
                // the actual items are always Permission instances at runtime.
                /** @var \Illuminate\Database\Eloquent\Collection<int, Permission> $perms */
                $perms = $role->permissions;

                return $perms->map(fn (Permission $p) => ['id' => $p->id, 'name' => $p->name]);
            }),
        ];
    }
}
