<?php

namespace App\Filament\Resources\User\RelationManagers;

use App\Filament\Resources\Post\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
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

    public function isReadOnly(): bool
    {
        return ! auth()->guard('admin')->user()->can('view_posts');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => PostResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('body')
                    ->label(__('filament.post.body'))
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('likes_count')
                    ->label(__('filament.post.likes_count'))
                    ->sortable(),

                TextColumn::make('comments_count')
                    ->label(__('filament.post.comments_count'))
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->boolean(),
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->color('gray')
                    ->url(fn ($record) => PostResource::getUrl('view', ['record' => $record]))
                    ->hidden(fn ($record) => $record->trashed()),
                DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->visible(fn () => auth()->guard('admin')->user()->can('delete_posts'))
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->iconButton()
                    ->color('success')
                    ->visible(fn () => auth()->guard('admin')->user()->can('delete_posts'))
                    ->hidden(fn ($record) => ! $record->trashed()),
                ForceDeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->visible(fn () => auth()->guard('admin')->user()->can('force_delete_posts'))
                    ->hidden(fn ($record) => ! $record->trashed()),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->placeholder(__('filament.filters.all'))
                    ->trueLabel(__('filament.filters.active'))
                    ->falseLabel(__('filament.filters.inactive')),
                TrashedFilter::make(),
            ])
            ->deferFilters(false)
            ->defaultSort('created_at', 'desc');
    }
}
