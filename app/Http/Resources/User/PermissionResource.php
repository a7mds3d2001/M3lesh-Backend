<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms a Permission model for API responses.
 * roles_count is included only when loaded via withCount / loadCount.
 *
 * @mixin \App\Models\User\Permission
 */
class PermissionResource extends JsonResource
{
    /**
     * key: programmatic identifier (e.g. create_admins).
     * name: display name according to request language (Accept-Language) — name_ar or name_en.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $displayName = $locale === 'ar' ? $this->name_ar : $this->name_en;
        $displayName = $displayName ?? $this->name;

        return [
            'id' => $this->id,
            'key' => $this->name,
            'name' => $displayName,
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'roles_count' => $this->whenCounted('roles'),
        ];
    }
}
