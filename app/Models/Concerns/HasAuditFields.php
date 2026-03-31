<?php

namespace App\Models\Concerns;

use App\Models\User\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sets created_by on create and updated_by on update from the current authenticated admin
 * (Sanctum for API, admin guard for Filament). Works for both API and dashboard.
 *
 * @property int|string|null $created_by
 * @property int|null $updated_by
 */
trait HasAuditFields
{
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /**
     * Scope a query to eager load audit relations.
     */
    public function scopeWithAudit(Builder $query): Builder
    {
        return $query->with(['creator', 'updater']);
    }

    /**
     * Load audit relations on a model instance.
     */
    public function loadAudit(): self
    {
        return $this->load(['creator', 'updater']);
    }

    public static function bootHasAuditFields(): void
    {
        static::creating(function (Model $model): void {
            if (! $model->isDirty('created_by')) {
                /** @var self $model */
                $model->setAuditCreatedBy();
            }
        });

        static::updating(function (Model $model): void {
            // Always set updated_by when an admin is authenticated (dashboard or API)
            /** @var self $model */
            $model->setAuditUpdatedBy();
        });
    }

    protected function setAuditCreatedBy(): void
    {
        $id = current_audit_admin_id();
        if ($id !== null) {
            $this->created_by = $id;
        }
    }

    protected function setAuditUpdatedBy(): void
    {
        $id = current_audit_admin_id();
        if ($id !== null) {
            $this->updated_by = $id;
        }
    }
}
