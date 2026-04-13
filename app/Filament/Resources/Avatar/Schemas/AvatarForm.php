<?php

namespace App\Filament\Resources\Avatar\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class AvatarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->label(__('filament.fields.image'))
                    ->image()
                    ->required()
                    ->disk('public')
                    ->directory('avatars')
                    ->visibility('public')
                    ->imagePreviewHeight('160')
                    ->maxSize(5120)
                    ->columnSpanFull(),
            ]);
    }
}
