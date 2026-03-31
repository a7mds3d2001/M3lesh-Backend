<?php

namespace App\Models\SupportTicket;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicketLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const LOG_TYPE_COMMENT = 'comment';
    public const LOG_TYPE_STATUS_CHANGE = 'status_change';
    public const LOG_TYPE_PRIORITY_CHANGE = 'priority_change';
    public const LOG_TYPE_INTERNAL_NOTE = 'internal_note';

    public const ACTOR_ADMIN = 'admin';
    public const ACTOR_USER = 'user';

    protected $fillable = [
        'ticket_id',
        'actor_type',
        'actor_id',
        'message',
        'log_type',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Actor who created this log (Admin or User model).
     */
    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function actorLabel(): string
    {
        $actor = $this->actor;
        if ($actor) {
            return $actor->name ?? $actor->email ?? (string) $this->actor_id;
        }

        return $this->actor_type === 'App\Models\User\Admin' ? __('filament.support_ticket.actor_admin') : __('filament.support_ticket.actor_user');
    }

    public static function logTypes(): array
    {
        return [
            self::LOG_TYPE_COMMENT => __('filament.support_ticket.log_type_comment'),
            self::LOG_TYPE_STATUS_CHANGE => __('filament.support_ticket.log_type_status_change'),
            self::LOG_TYPE_PRIORITY_CHANGE => __('filament.support_ticket.log_type_priority_change'),
            self::LOG_TYPE_INTERNAL_NOTE => __('filament.support_ticket.log_type_internal_note'),
        ];
    }
}
