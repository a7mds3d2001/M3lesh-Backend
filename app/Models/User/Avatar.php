<?php

namespace App\Models\User;

use Database\Factories\User\AvatarFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Avatar extends Model
{
    use HasFactory;

    protected static function newFactory(): AvatarFactory
    {
        return AvatarFactory::new();
    }

    protected $fillable = [
        'image',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'avatar_id');
    }
}
