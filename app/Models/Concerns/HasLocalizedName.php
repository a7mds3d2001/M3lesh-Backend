<?php

namespace App\Models\Concerns;

trait HasLocalizedName
{
    /**
     * Localized name column prefix (e.g. "name" → name_ar, name_en).
     */
    protected function localizedNameKey(): string
    {
        return 'name';
    }

    /**
     * API/display: locale-based name (ar → name_ar, en → name_en).
     * Returns name_{locale} when present, otherwise name_en.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $key = $this->localizedNameKey().'_'.$locale;
        $value = $this->attributes[$key] ?? null;

        if ($value !== null && $value !== '') {
            return (string) $value;
        }

        $fallbackKey = $this->localizedNameKey().'_en';

        return (string) ($this->attributes[$fallbackKey] ?? '');
    }

    /**
     * Column names to hide from array/JSON (raw locale columns).
     */
    protected function localizedNameHiddenColumns(): array
    {
        $key = $this->localizedNameKey();

        return [$key.'_ar', $key.'_en'];
    }
}
