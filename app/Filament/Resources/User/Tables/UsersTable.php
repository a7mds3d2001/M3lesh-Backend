<?php

namespace App\Filament\Resources\User\Tables;

use App\Models\User\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('filament.fields.image'))
                    ->getStateUsing(function (User $record) {
                        if (! $record->image) {
                            return null;
                        }

                        return str_starts_with($record->image, 'defaults/')
                            ? url('/images/'.$record->image)
                            : storage_public_url($record->image);
                    })
                    ->circular()
                    ->defaultImageUrl(url('/images/defaults/user.png')),
                TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('filament.fields.phone'))
                    ->icon('heroicon-o-phone')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('email')
                    ->label(__('filament.fields.email'))
                    ->icon('heroicon-o-envelope')
                    ->searchable()
                    ->copyable(),
                IconColumn::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make()->iconButton()->color('gray'),
                EditAction::make()
                    ->iconButton()->color('primary')
                    ->hidden(fn (User $record) => $record->trashed()),
                DeleteAction::make()
                    ->iconButton()->color('danger')
                    ->hidden(fn (User $record) => $record->trashed()),
                RestoreAction::make()
                    ->iconButton()->color('success')
                    ->hidden(fn (User $record) => ! $record->trashed()),
                ForceDeleteAction::make()
                    ->iconButton()->color('danger')
                    ->hidden(fn (User $record) => ! $record->trashed()),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->placeholder(__('filament.filters.all'))
                    ->trueLabel(__('filament.filters.active'))
                    ->falseLabel(__('filament.filters.inactive')),
                TrashedFilter::make(),
            ])
            ->toolbarActions([])
            ->deferFilters(false)
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['creator', 'updater']));
    }
}
