<?php

namespace App\Filament\Resources\Admin\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_en')
                    ->label(__('filament.role.name_en'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('name_ar')
                    ->label(__('filament.role.name_ar'))
                    ->required()
                    ->maxLength(255),

                Hidden::make('guard_name')
                    ->default('admin'),

                Select::make('permissions')
                    ->label(__('filament.fields.permissions'))
                    ->relationship(
                        'permissions',
                        'key',
                        fn ($query, ?string $search) => $query->where('guard_name', 'admin')
                            ->when($search, fn ($q) => $q->where('key', 'like', "%{$search}%")
                                ->orWhere('name_ar', 'like', "%{$search}%")
                                ->orWhere('name_en', 'like', "%{$search}%"))
                            ->orderBy('id'),
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name ?? $record->name)
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->placeholder(__('filament.placeholder.select')),
            ]);
    }
}
