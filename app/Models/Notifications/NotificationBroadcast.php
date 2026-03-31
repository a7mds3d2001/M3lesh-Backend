<?php

namespace App\Models\Notifications;

use App\Models\User\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationBroadcast extends Model
{
    protected $fillable = [
        'topic',
        'title',
        'body',
        'image',
        'target_type',
        'target_id',
        'data',
        'sent_at',
        'created_by_admin_id',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
    ];

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
