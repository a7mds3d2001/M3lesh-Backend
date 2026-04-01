<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Pages\ViewRole;
use App\Filament\Resources\Roles\RelationManagers\AdminsRelationManager;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use App\Filament\Support\AuditInfolistSection;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\PluginEssentials\Concerns\Resource as Essentials;
use Filament\Infolists\Components\TextEntry;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Override;

class RoleResource extends Resource
{
    use Essentials\BelongsToParent;
    use Essentials\BelongsToTenant;
    use Essentials\HasGlobalSearch;
    use Essentials\HasLabels;
    use Essentials\HasNavigation;
    protected static ?string $recordTitleAttribute = 'name_en';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.role.role_information'))
                ->schema([
                    TextEntry::make('name_en')->label(__('filament.role.name_en')),
                    TextEntry::make('name_ar')->label(__('filament.role.name_ar')),
                ])
                ->columns(2)->columnSpanFull(),
            Section::make(__('filament.fields.permissions'))
                ->schema([
                    TextEntry::make('permissions_list')
                        ->hiddenLabel()
                        ->badge()
                        ->separator(',')
                        ->getStateUsing(function ($record): array {
                            $isArabic = app()->getLocale() === 'ar';

                            return $record->permissions
                                ->map(function ($permission) use ($isArabic) {
                                    $primary = $isArabic ? $permission->name_ar : $permission->name_en;
                                    $secondary = $isArabic ? $permission->name_en : $permission->name_ar;

                                    return $primary ?: ($secondary ?: $permission->name);
                                })
                                ->filter()
                                ->unique()
                                ->values()
                                ->all();
                        })
                        ->placeholder(__('filament.role.no_permissions'))
                        ->columnSpanFull(),
                ])->columnSpanFull(),
            AuditInfolistSection::make(),
        ]);
    }

    #[Override]
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

    #[Override]
    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return Utils::getResourceSlug();
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster();
    }

    public static function getEssentialsPlugin(): ?FilamentShieldPlugin
    {
        return FilamentShieldPlugin::get();
    }

    public static function shield(): FilamentShieldPlugin
    {
        return FilamentShieldPlugin::get();
    }

    public static function prepareRoleDataForSave(array $data): array
    {
        $prepared = [
            'name' => $data['name_en'] ?? null,
            'name_en' => $data['name_en'] ?? null,
            'name_ar' => $data['name_ar'] ?? null,
            'guard_name' => $data['guard_name'] ?? Utils::getFilamentAuthGuard(),
        ];

        if (Utils::isTenancyEnabled()) {
            $tenantKey = Utils::getTenantModelForeignKey();
            if (! empty($tenantKey) && array_key_exists($tenantKey, $data) && filled($data[$tenantKey])) {
                $prepared[$tenantKey] = $data[$tenantKey];
            }
        }

        return $prepared;
    }

    public static function syncRolePermissions(Model $record, array $data): void
    {
        static::syncRolePermissionNames(
            $record,
            static::extractPermissionNames($data),
            $data['guard_name'] ?? Utils::getFilamentAuthGuard(),
        );
    }

    public static function extractPermissionNames(array $data): array
    {
        $excludedKeys = ['name', 'name_en', 'name_ar', 'guard_name', 'select_all'];

        if (Utils::isTenancyEnabled() && filled(Utils::getTenantModelForeignKey())) {
            $excludedKeys[] = Utils::getTenantModelForeignKey();
        }

        return collect($data)
            ->filter(fn (mixed $permission, string $key): bool => ! in_array($key, $excludedKeys, true))
            ->values()
            ->flatten()
            ->filter(fn (mixed $permission): bool => is_string($permission) && $permission !== '')
            ->unique()
            ->values()
            ->all();
    }

    public static function syncRolePermissionNames(Model $record, array $permissionNames, string $guardName): void
    {
        $permissionModels = collect($permissionNames)->map(function (string $permissionName) use ($guardName) {
            return Utils::getPermissionModel()::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guardName,
            ]);
        });

        $record->syncPermissions($permissionModels);
    }

    public static function touchRoleUpdatedBy(Model $record): void
    {
        $adminId = current_audit_admin_id();

        if ($adminId === null) {
            return;
        }

        $record->forceFill(['updated_by' => $adminId])->save();
    }

    public static function canRestore(Model $record): bool
    {
        return static::canDelete($record);
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canDelete($record);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
