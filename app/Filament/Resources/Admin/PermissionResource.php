<?php

namespace App\Filament\Resources\Admin;

use App\Filament\Resources\Admin\Pages\ListPermissions;
use App\Filament\Resources\Admin\Pages\ViewPermission;
use App\Filament\Resources\Admin\Tables\PermissionsTable;
use App\Models\User\Permission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $slug = 'permissions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record instanceof Permission) {
            return parent::getRecordTitle($record);
        }

        return $record->display_name ?? $record->name;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.permission');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.permission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.permission');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_permissions');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make(__('filament.permission.permission_information'))
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('name')
                            ->label(__('filament.permission.key')),
                        \Filament\Infolists\Components\TextEntry::make('name_ar')
                            ->label(__('filament.permission.name_ar')),
                        \Filament\Infolists\Components\TextEntry::make('name_en')
                            ->label(__('filament.permission.name_en')),
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->label(__('filament.fields.created_at'))
                            ->dateTime()
                            ->icon('heroicon-o-calendar'),
                        \Filament\Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('filament.fields.updated_at'))
                            ->dateTime()
                            ->icon('heroicon-o-clock')
                            ->since(),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make(__('filament.resources.role'))
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('roles.name')
                            ->hiddenLabel()
                            ->badge()
                            ->color('success')
                            ->separator(',')
                            ->formatStateUsing(function (string $state, $record): string {
                                $role = $record->roles->firstWhere('name', $state);

                                return $role === null ? $state : ($role->display_name ?? $state);
                            })
                            ->url(function ($state, $record) {
                                $role = $record->roles->firstWhere('name', $state);

                                return $role ? RoleResource::getUrl('view', ['record' => $role]) : null;
                            })
                            ->columnSpanFull()
                            ->placeholder(__('filament.permission.no_roles')),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
            'view' => ViewPermission::route('/{record}'),
        ];
    }

    // Authorization Methods
    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_permissions');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_permissions');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canForceDelete($record): bool
    {
        return false;
    }

    public static function canRestore($record): bool
    {
        return false;
    }
}
