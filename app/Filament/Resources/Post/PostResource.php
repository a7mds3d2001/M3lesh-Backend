<?php

namespace App\Filament\Resources\Post;

use App\Filament\Resources\Post\Pages\ListPosts;
use App\Filament\Resources\Post\Pages\ViewPost;
use App\Filament\Resources\Post\RelationManagers\PostCommentsRelationManager;
use App\Filament\Resources\Post\RelationManagers\PostLikesRelationManager;
use App\Filament\Resources\Post\Schemas\PostForm;
use App\Filament\Resources\Post\Tables\PostsTable;
use App\Filament\Support\AuditInfolistSection;
use App\Models\Post\Post;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $slug = 'posts';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.posts_management');
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.post.nav');
    }

    public static function getModelLabel(): string
    {
        return __('filament.post.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.post.nav');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_posts');
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.post.section'))
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(__('filament.post.author')),
                        TextEntry::make('body')
                            ->label(__('filament.post.body'))
                            ->columnSpanFull(),
                        TextEntry::make('likes_count')
                            ->label(__('filament.post.likes_count')),
                        TextEntry::make('comments_count')
                            ->label(__('filament.post.comments_count')),
                        IconEntry::make('is_active')
                            ->label(__('filament.fields.is_active'))
                            ->boolean(),
                    ])
                    ->columns(2),
                AuditInfolistSection::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PostLikesRelationManager::class,
            PostCommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'view' => ViewPost::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_posts');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_posts');
    }

    public static function canCreate(): bool
    {
        return auth()->guard('admin')->user()->can('create_posts');
    }

    public static function canEdit($record): bool
    {
        return auth()->guard('admin')->user()->can('edit_posts');
    }

    public static function canDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('delete_posts');
    }

    public static function canForceDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('force_delete_posts');
    }

    public static function canRestore($record): bool
    {
        return auth()->guard('admin')->user()->can('restore_posts');
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
