<?php

use App\Models\User\Admin;

/**
 * Returns the currently authenticated admin ID for audit fields (created_by, updated_by).
 * Works for: Dashboard (Filament admin guard), API (Bearer token via Sanctum).
 */
function current_audit_admin_id(): ?int
{
    // Dashboard / Filament: admin guard (session) — try first so Livewire requests get the right user
    $admin = auth('admin')->user();
    if ($admin instanceof Admin) {
        return (int) $admin->getAuthIdentifier();
    }

    // API: Bearer token (Sanctum)
    $user = request()->user();
    if ($user instanceof Admin) {
        return (int) $user->getAuthIdentifier();
    }

    $id = auth('sanctum')->id();
    if ($id !== null) {
        return (int) $id;
    }

    return null;
}
