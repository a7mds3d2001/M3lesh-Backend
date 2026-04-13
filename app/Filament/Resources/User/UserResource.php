<?php

namespace App\Filament\Resources\User;

use App\Enums\User\Gender;
use App\Filament\Resources\User\Pages\ListUsers;
use App\Filament\Resources\User\Pages\ViewUser;
use App\Filament\Resources\User\RelationManagers\DevicesRelationManager;
use App\Filament\Resources\User\RelationManagers\PostsRelationManager;
use App\Filament\Resources\User\RelationManagers\SupportTicketsRelationManager;
use App\Filament\Resources\User\Schemas\UserForm;
use App\Filament\Resources\User\Tables\UsersTable;
use App\Filament\Support\AuditInfolistSection;
use App\Models\User\User;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.user');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.user_singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.user');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_users');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.user.user_information'))
                    ->schema([
                        ImageEntry::make('image')
                            ->label(__('filament.fields.image'))
                            ->getStateUsing(function (User $record) {
                                if ($record->image) {
                                    return str_starts_with($record->image, 'defaults/')
                                        ? url('/images/'.$record->image)
                                        : storage_public_url($record->image);
                                }

                                return url('/images/defaults/user.png');
                            })
                            ->height(180)
                            ->columnSpanFull(),
                        TextEntry::make('name')
                            ->label(__('filament.fields.name')),
                        TextEntry::make('phone')
                            ->label(__('filament.fields.phone'))
                            ->icon('heroicon-o-phone')
                            ->placeholder(__('filament.placeholder.empty')),
                        TextEntry::make('email')
                            ->label(__('filament.fields.email'))
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage(__('filament.activity.copied'))
                            ->copyMessageDuration(1500)
                            ->placeholder(__('filament.placeholder.empty')),
                        TextEntry::make('birth_date')
                            ->label(__('filament.fields.birth_date'))
                            ->date()
                            ->placeholder(__('filament.placeholder.empty')),
                        TextEntry::make('gender')
                            ->label(__('filament.fields.gender'))
                            ->formatStateUsing(fn (?Gender $state): ?string => $state?->label())
                            ->placeholder(__('filament.placeholder.empty')),
                        IconEntry::make('is_active')
                            ->label(__('filament.fields.is_active'))
                            ->boolean(),
                    ])
                    ->columnSpanFull()
                    ->columns(2),
                AuditInfolistSection::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DevicesRelationManager::class,
            PostsRelationManager::class,
            SupportTicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_users');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_users');
    }

    public static function canCreate(): bool
    {
        return auth()->guard('admin')->user()->can('create_users');
    }

    public static function canEdit($record): bool
    {
        return auth()->guard('admin')->user()->can('edit_users');
    }

    public static function canDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('delete_users');
    }

    public static function canForceDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('force_delete_users');
    }

    public static function canRestore($record): bool
    {
        return auth()->guard('admin')->user()->can('restore_users');
    }
}
