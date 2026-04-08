<?php

namespace App\Filament\Resources\Admin;

use App\Filament\Resources\Admin\Pages\ListAdmins;
use App\Filament\Resources\Admin\Pages\ViewAdmin;
use App\Filament\Resources\Admin\RelationManagers\DevicesRelationManager;
use App\Filament\Resources\Admin\Schemas\AdminForm;
use App\Filament\Resources\Admin\Tables\AdminsTable;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Support\AuditInfolistSection;
use App\Models\User\Admin;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $slug = 'admins';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.admin');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.admin');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_admins');
    }

    public static function form(Schema $schema): Schema
    {
        return AdminForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.admin.admin_information'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('filament.fields.name')),
                        TextEntry::make('email')
                            ->label(__('filament.fields.email'))
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage(__('filament.activity.copied'))
                            ->copyMessageDuration(1500),
                        TextEntry::make('phone')
                            ->label(__('filament.fields.phone'))
                            ->icon('heroicon-o-phone')
                            ->placeholder(__('filament.placeholder.empty')),
                        IconEntry::make('is_active')
                            ->label(__('filament.fields.is_active'))
                            ->boolean(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),

                Section::make(__('filament.fields.roles'))
                    ->schema([
                        RepeatableEntry::make('roles')
                            ->hiddenLabel()
                            ->placeholder(__('filament.placeholder.no_roles'))
                            ->schema([
                                TextEntry::make('display_name')
                                    ->hiddenLabel()
                                    ->badge()
                                    ->color('success')
                                    ->url(fn ($record) => RoleResource::getUrl('view', ['record' => $record])),
                            ])
                            ->contained(false)
                            ->extraAttributes([
                                'style' => 'display: flex !important; flex-wrap: wrap; align-items: center; column-gap: 0.5rem; row-gap: 0.25rem;',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                AuditInfolistSection::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DevicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmins::route('/'),
            'view' => ViewAdmin::route('/{record}'),
        ];
    }

    // Authorization Methods
    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_admins');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_admins');
    }

    public static function canCreate(): bool
    {
        return auth()->guard('admin')->user()->can('create_admins');
    }

    public static function canEdit($record): bool
    {
        // Prevent editing Super Admin
        if ($record->isSuperAdmin()) {
            return false;
        }

        return auth()->guard('admin')->user()->can('edit_admins');
    }

    public static function canDelete($record): bool
    {
        // Prevent deleting Super Admin
        if ($record->isSuperAdmin()) {
            return false;
        }

        return auth()->guard('admin')->user()->can('delete_admins');
    }

    public static function canForceDelete($record): bool
    {
        // Prevent force deleting Super Admin
        if ($record->isSuperAdmin()) {
            return false;
        }

        return auth()->guard('admin')->user()->can('force_delete_admins');
    }

    public static function canRestore($record): bool
    {
        // Prevent restoring Super Admin
        if ($record->isSuperAdmin()) {
            return false;
        }

        return auth()->guard('admin')->user()->can('restore_admins');
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
