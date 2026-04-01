<?php

namespace App\Filament\Resources\Admin\Pages;

use App\Filament\Resources\Admin\AdminResource;
use App\Models\User\Admin;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;

class ViewAdmin extends ViewRecord
{
    protected static string $resource = AdminResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('filament.tabs.info');
    }

    public function getRelationManagersContentComponent(): Component
    {
        $component = parent::getRelationManagersContentComponent();

        if ($component instanceof Tabs) {
            $component->contained();
        }

        return $component;
    }

    protected function getHeaderActions(): array
    {
        /** @var Admin $record */
        $record = $this->getRecord();

        return [
            EditAction::make()
                ->slideOver()
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
