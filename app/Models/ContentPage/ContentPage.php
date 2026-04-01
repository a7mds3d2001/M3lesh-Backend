<?php

namespace App\Models\ContentPage;

use App\Models\Concerns\HasAuditFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentPage extends Model
{
    use HasAuditFields;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'content_pages';

    protected $fillable = [
        'title_ar',
        'content_ar',
        'title_en',
        'content_en',
        'is_active',
    ];

    /** Audit fields are set by the application only; not mass assignable from request. */
    protected $guarded = ['created_by', 'updated_by'];

    protected $casts = [
        'is_active' => 'boolean',
        'updated_by' => 'integer',
    ];

    /**
     * Scope: only active pages (for user-facing API).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Locale-based title (ar → title_ar, en → title_en). Fallback to English when empty.
     */
    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale();
        $key = 'title_'.($locale === 'ar' ? 'ar' : 'en');
        $value = $this->attributes[$key] ?? null;
        if ($value !== null && $value !== '') {
            return (string) $value;
        }

        return (string) ($this->attributes['title_en'] ?? $this->attributes['title_ar'] ?? '');
    }

    /**
     * Locale-based content (ar → content_ar, en → content_en). Fallback to English when empty.
     */
    public function getContentAttribute(): string
    {
        $locale = app()->getLocale();
        $key = 'content_'.($locale === 'ar' ? 'ar' : 'en');
        $value = $this->attributes[$key] ?? null;
        if ($value !== null && $value !== '') {
            return (string) $value;
        }

        return (string) ($this->attributes['content_en'] ?? $this->attributes['content_ar'] ?? '');
    }
}
