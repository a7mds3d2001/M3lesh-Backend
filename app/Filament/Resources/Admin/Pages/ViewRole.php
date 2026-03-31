<?php

namespace App\Filament\Resources\Admin\Pages;

use App\Filament\Resources\Admin\RoleResource;
use App\Models\User\Role;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('filament.tabs.info');
    }

    protected function getHeaderActions(): array
    {
        /** @var Role $record */
        $record = $this->getRecord();
        $resource = static::getResource();

        return [
            \Filament\Actions\EditAction::make()
                ->hidden(fn () => ! $resource::canEdit($record)),
            \Filament\Actions\DeleteAction::make()
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn () => ! $resource::canDelete($record)),
        ];
    }
}
