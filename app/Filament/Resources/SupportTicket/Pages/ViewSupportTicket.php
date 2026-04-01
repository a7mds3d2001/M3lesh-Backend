<?php

namespace App\Filament\Resources\SupportTicket\Pages;

use App\Filament\Resources\SupportTicket\SupportTicketResource;
use App\Models\SupportTicket\SupportTicket;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('filament.support_ticket.ticket_information');
    }

    protected function getHeaderActions(): array
    {
        /** @var SupportTicket $record */
        $record = $this->getRecord();
        $resource = static::getResource();

        return [
            EditAction::make()
                ->slideOver()
                ->hidden(fn () => $record->trashed() || ! $resource::canEdit($record)),
            RestoreAction::make()
                ->hidden(fn () => ! $record->trashed() || ! $resource::canRestore($record)),
            ForceDeleteAction::make()
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn () => ! $record->trashed() || ! $resource::canForceDelete($record)),
        ];
    }
}
