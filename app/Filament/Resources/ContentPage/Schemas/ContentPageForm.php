<?php

namespace App\Filament\Resources\ContentPage\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContentPageForm
{
    public static function configure(Schema $schema, array $context = []): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_ar')
                    ->label(__('filament.content_pages.title_ar'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                RichEditor::make('content_ar')
                    ->label(__('filament.content_pages.content_ar'))
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('title_en')
                    ->label(__('filament.content_pages.title_en'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                RichEditor::make('content_en')
                    ->label(__('filament.content_pages.content_en'))
                    ->required()
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->default(true)
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
