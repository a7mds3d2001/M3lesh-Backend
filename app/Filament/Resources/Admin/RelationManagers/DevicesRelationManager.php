<?php

namespace App\Filament\Resources\Admin\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DevicesRelationManager extends RelationManager
{
    protected static string $relationship = 'devices';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament.resources.devices');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('device_id')
            ->emptyStateHeading(__('filament.empty.no_devices'))
            ->columns([
                TextColumn::make('device_id')
                    ->label(__('filament.fields.device_id'))
                    ->wrap(),
                TextColumn::make('platform')
                    ->label(__('filament.fields.platform'))
                    ->badge(),
                TextColumn::make('manufacturer')
                    ->label(__('filament.fields.manufacturer'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('model')
                    ->label(__('filament.fields.model'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('os_version')
                    ->label(__('filament.fields.os_version'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('app_version')
                    ->label(__('filament.fields.app_version'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_used_at')
                    ->label(__('filament.fields.last_used_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->since(),
            ])
            ->actions([])
            ->headerActions([])
            ->bulkActions([]);
    }
}
