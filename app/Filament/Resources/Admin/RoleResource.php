<?php

namespace App\Filament\Resources\Admin;

use App\Filament\Resources\Admin\Pages\ListRoles;
use App\Filament\Resources\Admin\Pages\ViewRole;
use App\Filament\Resources\Admin\RelationManagers\AdminsRelationManager;
use App\Filament\Resources\Admin\Schemas\RoleForm;
use App\Filament\Resources\Admin\Tables\RolesTable;
use App\Filament\Support\AuditInfolistSection;
use App\Models\User\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $slug = 'roles';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $recordTitleAttribute = 'name_en';

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): \Illuminate\Contracts\Support\Htmlable|string|null
    {
        return $record instanceof Role ? $record->display_name : null;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.role');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.role');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_roles');
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make(__('filament.role.role_information'))
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('name_en')
                            ->label(__('filament.role.name_en')),
                        \Filament\Infolists\Components\TextEntry::make('name_ar')
                            ->label(__('filament.role.name_ar'))
                            ->placeholder(__('filament.placeholder.empty')),
                    ])
                    ->columnSpanFull()
                    ->columns(2),

                \Filament\Schemas\Components\Section::make(__('filament.fields.permissions'))
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('permissions_list')
                            ->hiddenLabel()
                            ->badge()
                            ->color('success')
                            ->url(function ($state, $record) {
                                // When clicking, open the first permission in this role in PermissionResource
                                $permission = $record->permissions->first();

                                return $permission ? PermissionResource::getUrl('view', ['record' => $permission]) : null;
                            })
                            ->getStateUsing(function ($record): array {
                                $isArabic = app()->getLocale() === 'ar';

                                return $record->permissions
                                    ->map(function ($permission) use ($isArabic) {
                                        $primary = $isArabic ? $permission->name_ar : $permission->name_en;
                                        $secondary = $isArabic ? $permission->name_en : $permission->name_ar;

                                        return $primary ?: ($secondary ?: $permission->key);
                                    })
                                    ->filter()
                                    ->unique()
                                    ->values()
                                    ->all();
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                AuditInfolistSection::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AdminsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'view' => ViewRole::route('/{record}'),
        ];
    }

    // Authorization Methods
    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_roles');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_roles');
    }

    public static function canCreate(): bool
    {
        return auth()->guard('admin')->user()->can('create_roles');
    }

    public static function canEdit($record): bool
    {
        return auth()->guard('admin')->user()->can('edit_roles');
    }

    public static function canDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('delete_roles');
    }
}
