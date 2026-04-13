<?php

namespace App\Filament\Resources\PostCommentPreset;

use App\Filament\Resources\PostCommentPreset\Pages\ListPostCommentPresets;
use App\Filament\Resources\PostCommentPreset\Pages\ViewPostCommentPreset;
use App\Filament\Resources\PostCommentPreset\Schemas\PostCommentPresetForm;
use App\Filament\Resources\PostCommentPreset\Tables\PostCommentPresetsTable;
use App\Filament\Support\AuditInfolistSection;
use App\Models\Post\PostCommentPreset;
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

class PostCommentPresetResource extends Resource
{
    protected static ?string $model = PostCommentPreset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $recordTitleAttribute = 'text';

    protected static ?string $slug = 'post-comment-presets';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.posts_management');
    }

    public static function getNavigationSort(): ?int
    {
        return 11;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.post_comment_preset.nav');
    }

    public static function getModelLabel(): string
    {
        return __('filament.post_comment_preset.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.post_comment_preset.nav');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_post_comment_presets');
    }

    public static function form(Schema $schema): Schema
    {
        return PostCommentPresetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostCommentPresetsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.post_comment_preset.section'))
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('text')
                            ->label(__('filament.post_comment_preset.text'))
                            ->columnSpanFull(),
                        IconEntry::make('is_active')
                            ->label(__('filament.fields.is_active'))
                            ->boolean(),
                    ])
                    ->columns(2),
                AuditInfolistSection::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPostCommentPresets::route('/'),
            'view' => ViewPostCommentPreset::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_post_comment_presets');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_post_comment_presets');
    }

    public static function canCreate(): bool
    {
        return auth()->guard('admin')->user()->can('create_post_comment_presets');
    }

    public static function canEdit($record): bool
    {
        return auth()->guard('admin')->user()->can('edit_post_comment_presets');
    }

    public static function canDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('delete_post_comment_presets');
    }

    public static function canForceDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('force_delete_post_comment_presets');
    }

    public static function canRestore($record): bool
    {
        return auth()->guard('admin')->user()->can('restore_post_comment_presets');
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
