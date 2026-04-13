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
                FileUpload::make('images')
                    ->label(__('filament.fields.image'))
                    ->helperText(__('filament.avatar.multi_upload_hint'))
                    ->image()
                    ->multiple()
                    ->minFiles(1)
                    ->reorderable()
                    ->appendFiles()
                    ->disk('public')
                    ->directory('avatars')
                    ->visibility('public')
                    ->imagePreviewHeight('160')
                    ->maxSize(5120)
                    ->columnSpanFull(),
            ]);
    }
}
