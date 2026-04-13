<?php

namespace App\Enums\Post;

enum PostReportReason: string
{
    case Spam = 'spam';
    case Harassment = 'harassment';
    case HateSpeech = 'hate_speech';
    case Violence = 'violence';
    case Misinformation = 'misinformation';
    case NudityOrSexual = 'nudity_or_sexual';
    case IllegalContent = 'illegal_content';
    case Copyright = 'copyright';
    case Impersonation = 'impersonation';
    case SelfHarm = 'self_harm';

    public function label(?string $locale = null): string
    {
        return __('filament.post_reports.reasons.'.$this->value, [], $locale);
    }

    /** Bilingual line for ticket body / Filament when both locales needed. */
    public function labelsBilingual(): string
    {
        return $this->label('en').' / '.$this->label('ar');
    }
}
