<?php

namespace App\Http\Resources\Concerns;

/**
 * Shared logic for API resources that expose created_by / updated_by as { id, name, email } when loaded.
 */
trait ReturnsAuditAdminObject
{
    /**
     * Return created_by/updated_by as { id, name, email } when relation loaded, else raw id for backward compat.
     *
     * @return array{id: int, name: string, email: string}|string|int|null
     */
    protected function auditAdminObject(string $relation): array|string|int|null
    {
        $admin = $this->resource->relationLoaded($relation) ? $this->resource->{$relation} : null;
        if ($admin) {
            return [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ];
        }

        return $this->resource->{$relation === 'creator' ? 'created_by' : 'updated_by'};
    }
}
