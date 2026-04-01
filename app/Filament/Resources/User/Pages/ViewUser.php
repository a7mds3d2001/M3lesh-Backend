<?php

namespace App\Filament\Resources\User\Pages;

use App\Filament\Resources\User\UserResource;
use App\Models\User\User;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

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

    protected function resolveRecord(int|string $key): User
    {
        return User::query()
            ->withTrashed()
            ->findOrFail($key);
    }

    public function openEditModal(): void
    {
        $this->mountAction('edit');
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
            RestoreAction::make()
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn () => ! $record->trashed() || ! $resource::canRestore($record)),
            ForceDeleteAction::make()
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn () => ! $record->trashed() || ! $resource::canForceDelete($record)),
        ];
    }
}
