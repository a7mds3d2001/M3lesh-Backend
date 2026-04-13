<?php

namespace App\Filament\Resources\Avatar\Tables;

use App\Models\User\Avatar;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AvatarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('filament.fields.image'))
                    ->getStateUsing(fn (Avatar $record): ?string => $record->image
                        ? storage_public_url($record->image)
                        : null)
                    ->height(64),
                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->iconButton()
                    ->color('danger'),
            ])
            ->toolbarActions([])
            ->defaultSort('id', 'desc');
    }
}
