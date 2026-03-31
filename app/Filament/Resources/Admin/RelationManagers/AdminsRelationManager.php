<?php

namespace App\Filament\Resources\Admin\RelationManagers;

use App\Filament\Resources\Admin\AdminResource;
use App\Models\User\Admin;
use App\Models\User\Role;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class AdminsRelationManager extends RelationManager
{
    protected static string $relationship = 'admins';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.tabs.admins');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.admin');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.admin');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => AdminResource::getUrl('view', ['record' => $record]))
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
                    ->iconButton()
                    ->url(fn ($record) => AdminResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultSort('name');
    }
}
