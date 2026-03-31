<?php

namespace App\Filament\Resources\Admin\Tables;

use App\Models\User\Admin;
use App\Models\User\Role;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class AdminsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament.fields.email'))
                    ->icon('heroicon-o-envelope')
                    ->searchable()
                    ->copyable(),
                TagsColumn::make('roles_display')
                    ->label(__('filament.fields.roles'))
                    ->getStateUsing(fn (Admin $record) => $record->roles->map(function (EloquentModel $role): string {
                        assert($role instanceof Role);

                        return $role->display_name;
                    })->all())
                    ->limit(2),
                IconColumn::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),
                EditAction::make()
                    ->iconButton()
                    ->hidden(fn (Admin $record) => $record->isSuperAdmin() || $record->trashed()),
                DeleteAction::make()
                    ->iconButton()
                    ->hidden(fn (Admin $record) => $record->isSuperAdmin() || $record->trashed()),
                RestoreAction::make()
                    ->iconButton()
                    ->hidden(fn (Admin $record) => $record->isSuperAdmin() || ! $record->trashed()),
                ForceDeleteAction::make()
                    ->iconButton()
                    ->hidden(fn (Admin $record) => $record->isSuperAdmin() || ! $record->trashed()),
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
            ->modifyQueryUsing(fn ($query) => $query->with('roles'));
    }
}
