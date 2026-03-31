<?php

namespace App\Http\Resources\ContentPage;

use App\Http\Resources\Concerns\ReturnsAuditAdminObject;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Content page API resource.
 *
 * Locale: same as shop categories — set Accept-Language header (ar|en); SetLocaleFromHeader sets app locale; title/content follow it.
 * content_ar and content_en are HTML; render in WebView or safe HTML renderer.
 * For GET /api/user/*, StripUserApiLocaleNameFields strips title_ar, title_en, content_ar, content_en so only locale-based title and content are returned.
 *
 * @mixin \App\Models\ContentPage\ContentPage
 */
class ContentPageResource extends JsonResource
{
    use ReturnsAuditAdminObject;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'content_ar' => $this->content_ar,
            'title_en' => $this->title_en,
            'content_en' => $this->content_en,
            'title' => $this->title,
            'content' => $this->content,
            'is_active' => $this->is_active,
            'created_by' => $this->auditAdminObject('creator'),
            'updated_by' => $this->auditAdminObject('updater'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
