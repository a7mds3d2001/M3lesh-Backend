<?php

namespace App\Filament\Resources\ContentPage;

use App\Filament\Resources\ContentPage\Pages\ListContentPages;
use App\Filament\Resources\ContentPage\Pages\ViewContentPage;
use App\Filament\Resources\ContentPage\Schemas\ContentPageForm;
use App\Filament\Resources\ContentPage\Tables\ContentPagesTable;
use App\Filament\Support\AuditInfolistSection;
use App\Models\ContentPage\ContentPage;
use BackedEnum;
use Filament\Actions\Action as InfolistAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContentPageResource extends Resource
{
    protected static ?string $model = ContentPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'title_ar';

    protected static ?string $slug = 'content-pages';

    public static function getNavigationLabel(): string
    {
        return __('filament.content_pages.nav.content_pages');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.content_page_singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.content_page');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_content_pages');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentPagesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.content_pages.nav.section_info'))
                    ->columnSpanFull()
                    ->headerActions([
                        InfolistAction::make('openEditModal')
                            ->label(__('filament.actions.edit'))
                            ->icon('heroicon-o-pencil')
                            ->color('primary')
                            ->iconButton()
                            ->action(null)
                            ->livewireClickHandlerEnabled(false)
                            ->extraAttributes([
                                'data-trigger-edit' => 'content-page',
                                'type' => 'button',
                            ])
                            ->hidden(fn ($record) => ! static::canEdit($record)),

                        InfolistAction::make('delete')
                            ->label(__('filament.actions.delete'))
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->iconButton()
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->delete();

                                return redirect(static::getUrl('index'));
                            })
                            ->hidden(fn ($record) => ! static::canDelete($record)),
                    ])
                    ->schema([
                        IconEntry::make('is_active')
                            ->label(__('filament.fields.is_active'))
                            ->boolean(),

                        TextEntry::make('title_ar')
                            ->label(__('filament.content_pages.title_ar'))
                            ->placeholder('—'),
                        TextEntry::make('content_ar')
                            ->label(__('filament.content_pages.content_ar'))
                            ->placeholder('—')
                            ->html()
                            ->columnSpanFull(),

                        TextEntry::make('title_en')
                            ->label(__('filament.content_pages.title_en'))
                            ->placeholder('—'),
                        TextEntry::make('content_en')
                            ->label(__('filament.content_pages.content_en'))
                            ->placeholder('—')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                AuditInfolistSection::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentPages::route('/'),
            'view' => ViewContentPage::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_content_pages');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_content_pages');
    }

    public static function canCreate(): bool
    {
        return auth()->guard('admin')->user()->can('create_content_pages');
    }

    public static function canEdit($record): bool
    {
        return auth()->guard('admin')->user()->can('edit_content_pages');
    }

    public static function canDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('delete_content_pages');
    }
}
