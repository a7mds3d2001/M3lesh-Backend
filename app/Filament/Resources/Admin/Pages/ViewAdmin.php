<?php

namespace App\Filament\Resources\Admin\Pages;

use App\Filament\Resources\Admin\AdminResource;
use App\Models\User\Admin;
use Filament\Resources\Pages\ViewRecord;

class ViewAdmin extends ViewRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Admin $record */
        $record = $this->getRecord();

        return [
            \Filament\Actions\EditAction::make()
                ->hidden(fn () => $record->isSuperAdmin() || $record->trashed()),
            \Filament\Actions\DeleteAction::make()
                ->successRedirectUrl(static::getResource()::getUrl('index'))
                ->hidden(fn () => $record->isSuperAdmin() || $record->trashed()),
            \Filament\Actions\RestoreAction::make()
                ->successRedirectUrl(static::getResource()::getUrl('index'))
                ->hidden(fn () => $record->isSuperAdmin() || ! $record->trashed()),
            \Filament\Actions\ForceDeleteAction::make()
                ->successRedirectUrl(static::getResource()::getUrl('index'))
                ->hidden(fn () => $record->isSuperAdmin() || ! $record->trashed()),
        ];
    }
}
