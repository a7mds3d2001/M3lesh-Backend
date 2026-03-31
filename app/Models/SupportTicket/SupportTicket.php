<?php

namespace App\Models\SupportTicket;

use App\Models\Concerns\HasAuditFields;
use App\Models\User\Admin;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasAuditFields;
    use HasFactory;
    use SoftDeletes;

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_CLOSED = 'closed';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';

    protected $fillable = [
        'ticket_number',
        'user_id',
        'visitor_name',
        'visitor_phone',
        'visitor_email',
        'message',
        'status',
        'priority',
        'is_active',
        'attachments',
    ];

    /** Audit fields are set by the application only; not mass assignable from request. */
    protected $guarded = ['created_by', 'updated_by'];

    protected $casts = [
        'is_active' => 'boolean',
        'attachments' => 'array',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function ($q) use ($term) {
            $wildcard = "%{$term}%";
            if (is_numeric($term)) {
                $q->where('id', (int) $term);
            }
            $q->orWhere('ticket_number', 'like', $wildcard)
                ->orWhere('visitor_name', 'like', $wildcard)
                ->orWhere('visitor_phone', 'like', $wildcard)
                ->orWhere('visitor_email', 'like', $wildcard)
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $wildcard)->orWhere('email', 'like', $wildcard)->orWhere('phone', 'like', $wildcard));
        });
    }

    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn ($q) => $q->where('status', $status));
    }

    public function scopeByPriority(Builder $query, ?string $priority): Builder
    {
        return $query->when($priority, fn ($q) => $q->where('priority', $priority));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SupportTicketLog::class, 'ticket_id')->orderBy('created_at');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function ownerLabel(): string
    {
        if ($this->user_id && $this->user) {
            return $this->user->name ?? (string) $this->user_id;
        }

        return $this->visitor_name ?? '—';
    }

    public function contactLabel(): string
    {
        if ($this->user_id && $this->user) {
            return $this->user->email ?? $this->user->phone ?? '—';
        }

        if ($this->visitor_email) {
            return $this->visitor_email;
        }

        return $this->visitor_phone ?? '—';
    }

    protected static function booted(): void
    {
        static::created(function (SupportTicket $ticket): void {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-'.str_pad((string) $ticket->id, 6, '0', STR_PAD_LEFT);
                $ticket->saveQuietly();
            }
        });
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN => __('filament.support_ticket.status_open'),
            self::STATUS_IN_PROGRESS => __('filament.support_ticket.status_in_progress'),
            self::STATUS_CLOSED => __('filament.support_ticket.status_closed'),
        ];
    }

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => __('filament.support_ticket.priority_low'),
            self::PRIORITY_NORMAL => __('filament.support_ticket.priority_normal'),
            self::PRIORITY_HIGH => __('filament.support_ticket.priority_high'),
        ];
    }
}
