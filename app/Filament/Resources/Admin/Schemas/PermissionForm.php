<?php

namespace App\Filament\Resources\Admin\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament.fields.name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Hidden::make('guard_name')
                    ->default('admin'),
            ]);
    }
}
