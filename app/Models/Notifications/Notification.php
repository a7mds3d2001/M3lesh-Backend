<?php

namespace App\Models\Notifications;

use App\Notifications\M3leshInboxNotification;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'title',
        'body',
        'image',
        'target_type',
        'target_id',
        'data',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public static function m3leshInboxType(): string
    {
        return M3leshInboxNotification::class;
    }
}
