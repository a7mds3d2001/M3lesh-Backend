<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\User\Role;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->createAnother(false)
                ->using(function (array $data) {
                    $permissionNames = RoleResource::extractPermissionNames($data);
                    $prepared = RoleResource::prepareRoleDataForSave($data);

                    $model = RoleResource::getModel();
                    $record = new $model;
                    $record->fill($prepared);
                    $record->save();

                    if (! $record instanceof Role) {
                        throw new \RuntimeException('Unexpected role model instance.');
                    }

                    RoleResource::syncRolePermissionNames(
                        $record,
                        $permissionNames,
                        $prepared['guard_name'] ?? 'admin',
                    );

                    return $record;
                }),
        ];
    }
}
