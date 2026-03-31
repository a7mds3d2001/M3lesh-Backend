<?php

namespace App\Filament\Resources\Notifications\Tables;

use App\Models\Notifications\Notification;
use App\Models\User\Admin;
use App\Models\User\User;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('notifiable_name')
                    ->label(__('filament.fields.name'))
                    ->getStateUsing(function (Notification $record): ?string {
                        $n = $record->notifiable;

                        return ($n instanceof User || $n instanceof Admin) ? $n->name : null;
                    })
                    ->placeholder(__('filament.placeholder.empty'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHasMorph('notifiable', [User::class, Admin::class], function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->weight('bold'),
                TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable()
                    ->limit(60),
                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
                TextColumn::make('read_at')
                    ->label(__('filament.notifications.read_at'))
                    ->dateTime()
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActionsPosition(RecordActionsPosition::AfterColumns)
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->icon(Heroicon::OutlinedEye)
                    ->color('gray'),
            ])
            ->toolbarActions([]);
    }
}
