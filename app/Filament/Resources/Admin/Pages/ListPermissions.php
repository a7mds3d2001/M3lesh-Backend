<?php

namespace App\Filament\Resources\Admin\Pages;

use App\Filament\Resources\Admin\PermissionResource;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
