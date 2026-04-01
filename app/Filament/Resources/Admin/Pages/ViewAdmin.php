<?php

namespace App\Filament\Resources\Admin\Pages;

use App\Filament\Resources\Admin\AdminResource;
use App\Models\User\Admin;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAdmin extends ViewRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Admin $record */
        $record = $this->getRecord();

        return [
            EditAction::make()
                ->hidden(fn () => $record->isSuperAdmin() || $record->trashed()),
            DeleteAction::make()
                ->successRedirectUrl(static::getResource()::getUrl('index'))
                ->hidden(fn () => $record->isSuperAdmin() || $record->trashed()),
            RestoreAction::make()
                ->successRedirectUrl(static::getResource()::getUrl('index'))
                ->hidden(fn () => $record->isSuperAdmin() || ! $record->trashed()),
            ForceDeleteAction::make()
                ->successRedirectUrl(static::getResource()::getUrl('index'))
                ->hidden(fn () => $record->isSuperAdmin() || ! $record->trashed()),
        ];
    }
}
