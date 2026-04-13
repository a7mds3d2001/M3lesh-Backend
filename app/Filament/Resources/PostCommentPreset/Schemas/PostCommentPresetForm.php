<?php

namespace App\Filament\Resources\PostCommentPreset\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostCommentPresetForm
{
    public static function configure(Schema $schema, array $context = []): Schema
    {
        return $schema
            ->components([
                TextInput::make('text')
                    ->label(__('filament.post_comment_preset.text'))
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->default(true)
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
