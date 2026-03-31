<?php

namespace App\Filament\Resources\SupportTicket\Tables;

use App\Models\SupportTicket\SupportTicket;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->label(__('filament.support_ticket.ticket_number'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('owner_label')
                    ->label(__('filament.support_ticket.owner'))
                    ->getStateUsing(fn ($record) => $record->ownerLabel())
                    ->searchable(query: function ($query, $search) {
                        return $query->where(function ($q) use ($search) {
                            $term = '%'.$search.'%';
                            $q->where('visitor_name', 'like', $term)
                                ->orWhere('visitor_phone', 'like', $term)
                                ->orWhere('visitor_email', 'like', $term)
                                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('phone', 'like', $term));
                        });
                    }),

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
            ->recordActions([
                ViewAction::make()->iconButton()->color('gray'),
                DeleteAction::make()
                    ->iconButton()->color('danger')
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->iconButton()->color('success')
                    ->hidden(fn ($record) => ! $record->trashed()),
                ForceDeleteAction::make()
                    ->iconButton()->color('danger')
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
            ->toolbarActions([])
            ->deferFilters(false)
            ->defaultSort('created_at', 'desc');
    }
}
