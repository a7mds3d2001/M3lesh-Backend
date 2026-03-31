<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Standardizes 'is_active' and 'publish_at' logic for models.
 * Assumes model has 'is_active' (boolean) and optionally 'publish_at' (datetime) and 'expires_at' (datetime).
 */
trait HasPublishedScope
{
    /**
     * Scope a query to only include "published" items.
     */
    public function scopePublished(Builder $query): Builder
    {
        $query->where($this->getTable().'.is_active', true);

        if (in_array('publish_at', $this->getFillable())) {
            $query->where(function ($q) {
                $q->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            });
        }

        if (in_array('expires_at', $this->getFillable())) {
            $query->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
        }

        return $query;
    }
}
