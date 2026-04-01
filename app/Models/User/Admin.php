<?php

namespace App\Models\User;

use App\Models\Concerns\HasAuditFields;
use App\Models\Notifications\Notification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements FilamentUser, HasMedia
{
    use HasApiTokens;
    use HasAuditFields;
    use HasFactory;
    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;
    use SoftDeletes;

    protected $guard = 'admin';

    protected $guard_name = 'admin';

    public const TYPE_ADMIN = 'admin';

    public const TYPE_SUPER_ADMIN = 'super_admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'admin_type',
    ];

    /** Audit fields are set by the application only; not mass assignable from request. */
    protected $guarded = ['created_by', 'updated_by'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * App inbox + Filament database notifications share `notifications`.
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderByDesc('created_at');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            // Check if admin is active and not soft-deleted
            // Note: is_active is cast to boolean, so we check it directly
            // deleted_at will be null if not soft-deleted
            return $this->is_active === true && $this->deleted_at === null;
        }

        return false;
    }

    /**
     * Whether this admin is the system owner (admin_type = super_admin).
     * Used for: bypass permissions, and protect from edit/delete in UI.
     */
    public function isSuperAdmin(): bool
    {
        return $this->admin_type === self::TYPE_SUPER_ADMIN;
    }

    public static function types(): array
    {
        return [
            self::TYPE_ADMIN => __('filament.admin.type_admin'),
            self::TYPE_SUPER_ADMIN => __('filament.admin.type_super_admin'),
        ];
    }
}
