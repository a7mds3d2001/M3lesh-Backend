<?php

namespace App\Models\Post;

use App\Enums\Post\PostReportReason;
use App\Models\SupportTicket\SupportTicket;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property PostReportReason $reason
 * @property string|null $details
 */
class PostReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'reporter_id',
        'support_ticket_id',
        'reason',
        'details',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    protected function casts(): array
    {
        return [
            'reason' => PostReportReason::class,
        ];
    }
}
