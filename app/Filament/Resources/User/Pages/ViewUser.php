<?php

namespace App\Filament\Resources\User\Pages;

use App\Filament\Resources\User\UserResource;
use App\Models\User\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

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

    protected function resolveRecord(int|string $key): User
    {
        return User::query()
            ->withTrashed()
            ->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        /** @var User $record */
        $record = $this->getRecord();
        $resource = static::getResource();

        return [
            EditAction::make()
                ->slideOver()
                ->hidden(fn () => $record->trashed() || ! $resource::canEdit($record)),
            DeleteAction::make()
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn () => $record->trashed() || ! $resource::canDelete($record)),
            RestoreAction::make()
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn () => ! $record->trashed() || ! $resource::canRestore($record)),
            ForceDeleteAction::make()
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn () => ! $record->trashed() || ! $resource::canForceDelete($record)),
        ];
    }
}
