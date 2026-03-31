<?php

namespace Database\Seeders\Concerns;

use Illuminate\Database\Eloquent\Model;

trait SeedsWithSuperAdmin
{
    /**
     * Force created_by for seeded data to 1 (Super Admin) and keep updated_by null.
     * - Runs when the record is newly created or when created_by is still null.
     */
    protected function setAuditIfNew(Model $record): void
    {
        $superAdminId = 1;

        $needsAudit = $record->wasRecentlyCreated
            || $record->getAttribute('created_by') === null;

        if ($needsAudit) {
            $record->created_by = $superAdminId;
            $record->updated_by = null;
            $record->save();
        }
    }
}
