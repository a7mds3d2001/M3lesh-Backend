<?php

namespace App\Models\User;

use App\Models\Concerns\HasAuditFields;
use App\Models\Concerns\HasLocalizedName;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

/**
 * @property string|null $name_ar
 * @property string $name_en
 * @property-read string $display_name
 */
class Role extends SpatieRole
{
    use HasAuditFields;
    use HasLocalizedName;

    protected $fillable = ['name', 'name_en', 'name_ar', 'guard_name'];

    /** Audit fields are set by the application only; not mass assignable from request. */
    protected $guarded = ['created_by', 'updated_by'];

    protected $casts = [
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    protected $appends = ['name'];

    protected static function booted(): void
    {
        static::saving(function (self $role): void {
            if (($role->attributes['name_en'] ?? null) !== null) {
                $role->attributes['name'] = $role->attributes['name_en'];
            }
        });
    }

    /** Filament/display: same as locale-based name. */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Allow creating a Role by passing name_en without a separate name key.
     * We skip Spatie's duplicate-name check so multiple roles can share the same name_en.
     */
    public static function create(array $attributes = []): static
    {
        if (! isset($attributes['name']) && isset($attributes['name_en'])) {
            $attributes['name'] = $attributes['name_en'];
        }
        $attributes['guard_name'] ??= Guard::getDefaultName(static::class);
        $registrar = app(PermissionRegistrar::class);
        if ($registrar->teams) {
            $teamsKey = $registrar->teamsKey;
            if (! array_key_exists($teamsKey, $attributes)) {
                $attributes[$teamsKey] = getPermissionsTeamId();
            }
        }

        return static::query()->create($attributes);
    }

    /** Map Spatie setter to our name_en column. */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name_en'] = $value;
        $this->attributes['name'] = $value;
    }

    /** Internal: English name (for Spatie and boot checks). */
    public function getNameEnAttribute(): string
    {
        return $this->attributes['name_en'] ?? '';
    }

    public function getOriginal($key = null, $default = null)
    {
        if ($key === 'name') {
            $key = 'name_en';
        }

        return parent::getOriginal($key, $default);
    }

    /**
     * Spatie queries by column "name"; our table uses "name_en".
     */
    protected static function findByParam(array $params = []): ?RoleContract
    {
        if (array_key_exists('name', $params)) {
            $params['name_en'] = $params['name'];
            unset($params['name']);
        }

        return parent::findByParam($params);
    }

    /*
        * Get the admins that have this role
    */
    public function admins(): MorphToMany
    {
        return $this->morphedByMany(
            Admin::class,
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key'),
        );
    }
}
