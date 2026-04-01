<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Tables;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\User\Role;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight('medium')
                    ->label(__('filament.fields.name'))
                    ->formatStateUsing(function ($state, $record): string {
                        $localized = app()->getLocale() === 'ar'
                            ? ($record->name_ar ?? null)
                            : ($record->name_en ?? null);

                        return filled($localized)
                            ? (string) $localized
                            : Str::headline((string) $state);
                    })
                    ->searchable(),
                TextColumn::make('team.name')
                    ->default(__('filament.fields.global'))
                    ->badge()
                    ->color(fn (mixed $state): string => filled($state) ? 'primary' : 'gray')
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->searchable()
                    ->visible(fn (): bool => RoleResource::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->color('primary'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),
                EditAction::make()
                    ->iconButton()
                    ->slideOver()
                    ->hidden(fn (Role $record): bool => $record->trashed())
                    ->using(function (Role $record, array $data): Role {
                        $permissionNames = RoleResource::extractPermissionNames($data);
                        $prepared = RoleResource::prepareRoleDataForSave($data);

                        $record->update($prepared);

                        RoleResource::syncRolePermissionNames(
                            $record,
                            $permissionNames,
                            $prepared['guard_name'] ?? 'admin',
                        );

                        RoleResource::touchRoleUpdatedBy($record);

                        return $record;
                    }),
                DeleteAction::make()
                    ->iconButton()
                    ->hidden(fn (Role $record): bool => $record->trashed()),
                RestoreAction::make()
                    ->iconButton()
                    ->hidden(fn (Role $record): bool => ! $record->trashed()),
                ForceDeleteAction::make()
                    ->iconButton()
                    ->hidden(fn (Role $record): bool => ! $record->trashed()),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->deferFilters(false);
    }
}
