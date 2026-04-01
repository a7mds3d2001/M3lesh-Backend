<?php

namespace App\Filament\Resources\User\RelationManagers;

use App\Filament\Resources\SupportTicket\SupportTicketResource;
use App\Models\SupportTicket\SupportTicket;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SupportTicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'supportTickets';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament.support_ticket.nav.support_tickets');
    }

    public static function getModelLabel(): string
    {
        return __('filament.support_ticket.ticket_singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.support_ticket.nav.support_tickets');
    }

    public function isReadOnly(): bool
    {
        return ! auth()->guard('admin')->user()->can('view_support_tickets');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => SupportTicketResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('ticket_number')
                    ->label(__('filament.support_ticket.ticket_number'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label(__('filament.support_ticket.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => SupportTicket::statuses()[$state] ?? __('filament.support_ticket.status_closed'))
                    ->color(fn (string $state): string => match ($state) {
                        SupportTicket::STATUS_CLOSED => 'gray',
                        'resolved' => 'gray',
                        SupportTicket::STATUS_IN_PROGRESS => 'warning',
                        default => 'primary',
                    })
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('filament.support_ticket.priority'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => SupportTicket::priorities()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        SupportTicket::PRIORITY_HIGH => 'danger',
                        SupportTicket::PRIORITY_LOW => 'gray',
                        default => 'primary',
                    })
                    ->sortable(),
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->color('gray')
                    ->url(fn ($record) => SupportTicketResource::getUrl('view', ['record' => $record]))
                    ->hidden(fn ($record) => $record->trashed()),
                DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->visible(fn () => auth()->guard('admin')->user()->can('delete_support_tickets'))
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->iconButton()
                    ->color('success')
                    ->visible(fn () => auth()->guard('admin')->user()->can('delete_support_tickets'))
                    ->hidden(fn ($record) => ! $record->trashed()),
                ForceDeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->visible(fn () => auth()->guard('admin')->user()->can('delete_support_tickets'))
                    ->hidden(fn ($record) => ! $record->trashed()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.support_ticket.status'))
                    ->options(SupportTicket::statuses()),

                SelectFilter::make('priority')
                    ->label(__('filament.support_ticket.priority'))
                    ->options(SupportTicket::priorities()),

                TrashedFilter::make(),
            ])
            ->deferFilters(false)
            ->defaultSort('created_at', 'desc');
    }
}
