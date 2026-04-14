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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
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
            ->reorderable('sort_order')
            ->authorizeReorder(fn (): bool => auth()->guard('admin')->user()?->can('create_avatars') ?? false)
            ->defaultSort('sort_order')
            ->recordActions([
                DeleteAction::make()
                    ->iconButton()
                    ->color('danger'),
            ])
            ->toolbarActions([])
            ->paginated(false);
    }
}
