<?php

namespace App\Filament\Resources\User\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class UserForm
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
                    ->defaultCountry('EG')
                    ->required(),
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
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(6)
                    ->maxLength(255),
                FileUpload::make('image')
                    ->label(__('filament.fields.image'))
                    ->image()
                    ->disk('public')
                    ->directory('users')
                    ->visibility('public')
                    ->imagePreviewHeight('160')
                    ->maxSize(2048)
                    ->nullable()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->default(true),
            ])
            ->columns(2);
    }
}
