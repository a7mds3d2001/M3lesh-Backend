<?php

namespace App\Filament\Resources\Admin\Schemas;

use App\Models\User\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament.fields.name'))
                    ->required()
                    ->maxLength(255),
                PhoneInput::make('phone')
                    ->label(__('filament.fields.phone'))
                    ->defaultCountry('EG'),
                TextInput::make('email')
                    ->label(__('filament.fields.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label(__('filament.user.password'))
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),
                Select::make('roles')
                    ->label(__('filament.fields.roles'))
                    ->relationship('roles', 'name', fn ($query) => $query->where('guard_name', 'admin')->orderBy('id'))
                    ->getOptionLabelFromRecordUsing(fn (Role $record) => $record->display_name)
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->placeholder(__('filament.placeholder.select'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->default(true),
            ]);
    }
}
