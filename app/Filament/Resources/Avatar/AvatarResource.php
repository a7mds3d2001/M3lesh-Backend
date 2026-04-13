<?php

namespace App\Filament\Resources\Avatar;

use App\Filament\Resources\Avatar\Pages\ListAvatars;
use App\Filament\Resources\Avatar\Schemas\AvatarForm;
use App\Filament\Resources\Avatar\Tables\AvatarsTable;
use App\Models\User\Avatar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AvatarResource extends Resource
{
    protected static ?string $model = Avatar::class;

    protected static ?string $slug = 'avatars';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.accounts_and_permissions');
    }

    public static function getNavigationSort(): ?int
    {
        return 15;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.avatar.nav');
    }

    public static function getModelLabel(): string
    {
        return __('filament.avatar.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.avatar.nav');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_avatars');
    }

    public static function form(Schema $schema): Schema
    {
        return AvatarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvatarsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAvatars::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_avatars');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_avatars');
    }

    public static function canCreate(): bool
    {
        return auth()->guard('admin')->user()->can('create_avatars');
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('delete_avatars');
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
