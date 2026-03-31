<?php

namespace App\Filament\Resources\Admin\Pages;

use App\Filament\Resources\Admin\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected static bool $canCreateAnother = false;
}
