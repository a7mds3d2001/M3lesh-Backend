<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property string|null $name_ar
 * @property string|null $name_en
 * @property-read string $display_name
 */
class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'key',
        'guard_name',
        'name_ar',
        'name_en',
    ];

    /**
     * DB column is "key"; we expose it as "name" for app code (can(), syncPermissions, etc.).
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->attributes['key'] ?? null,
            // Keep both columns synced for compatibility with packages that query `name`.
            set: fn (?string $value): array => ['key' => $value, 'name' => $value],
        );
    }

    protected static function booted(): void
    {
        static::saving(function (self $permission): void {
            $key = $permission->attributes['key'] ?? null;

            if (is_string($key) && $key !== '') {
                $permission->attributes['name'] = $key;
            }
        });
    }

    /**
     * Display name for current locale (name_ar / name_en), fallback to key.
     */
    protected function displayName(): Attribute
    {
        return Attribute::get(function (): string {
            $locale = app()->getLocale();
            $name = $locale === 'ar' ? $this->name_ar : $this->name_en;

            return $name ?? $this->name;
        });
    }

    /**
     * Spatie looks up by "name"; our DB column is "key", so we pass key to the registrar.
     */
    protected static function getPermission(array $params = []): ?PermissionContract
    {
        $queryParams = $params;
        if (array_key_exists('name', $queryParams)) {
            $queryParams['key'] = $queryParams['name'];
            unset($queryParams['name']);
        }

        return parent::getPermission($queryParams);
    }

    /**
     * Get the roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            'permission_id',
            'role_id',
        );
    }
}
