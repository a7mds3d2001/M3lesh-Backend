<?php

namespace App\Models\Post;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'post_comment_preset_id',
        'body',
        'preset_text_snapshot',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function preset(): BelongsTo
    {
        return $this->belongsTo(PostCommentPreset::class, 'post_comment_preset_id');
    }

    public function displayBody(): string
    {
        $parts = array_filter([
            $this->preset_text_snapshot,
            $this->body,
        ], fn (?string $s) => $s !== null && $s !== '');

        return implode("\n\n", $parts);
    }
}
