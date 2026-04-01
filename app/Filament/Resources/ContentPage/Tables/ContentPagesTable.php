<?php

namespace App\Filament\Resources\ContentPage\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContentPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_ar')
                    ->label(__('filament.content_pages.title'))
                    ->placeholder('—')
                    ->formatStateUsing(fn ($record) => app()->getLocale() === 'ar'
                        ? ($record->title_ar ?: $record->title_en)
                        : ($record->title_en ?: $record->title_ar))
                    ->searchable(['title_ar', 'title_en'])
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make()->iconButton()->color('gray'),
                EditAction::make()->iconButton()->color('primary')->slideOver(),
                DeleteAction::make()->iconButton()->color('danger'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->placeholder(__('filament.filters.all'))
                    ->trueLabel(__('filament.filters.active'))
                    ->falseLabel(__('filament.filters.inactive')),
            ])
            ->toolbarActions([])
            ->deferFilters(false)
            ->defaultSort('created_at', 'desc');
    }
}
