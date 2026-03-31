<?php

namespace App\Filament\Resources\Notifications\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationBroadcastsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('topic')
                    ->label(__('filament.notifications.topic'))
                    ->badge(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label(__('filament.notifications.sent_at'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('filament.empty.no_notification_broadcasts'))
            ->recordActions([
                ViewAction::make()->iconButton()->color('gray'),
            ])
            ->toolbarActions([]);
    }
}
