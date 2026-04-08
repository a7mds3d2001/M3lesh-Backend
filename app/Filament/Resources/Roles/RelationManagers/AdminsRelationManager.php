<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\RelationManagers;

use App\Filament\Resources\Admin\AdminResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AdminsRelationManager extends RelationManager
{
    protected static string $relationship = 'admins';

    public static function getModelLabel(): string
    {
        return __('filament.resources.admin');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.admin');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament.tabs.admins');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->emptyStateHeading(__('filament.role.no_admins'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament.fields.email'))
                    ->icon('heroicon-o-envelope')
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->url(fn ($record) => AdminResource::getUrl('view', ['record' => $record]))
                    ->hidden(fn ($record) => $record->trashed()),
            ])
            ->headerActions([])
            ->bulkActions([]);
    }
}
