<?php

namespace App\Filament\Resources\Post\Schemas;

use App\Models\User\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema, array $context = []): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('filament.post.author'))
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->whereNull('deleted_at')->orderBy('name'),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (User $user): string => $user->name.' ('.($user->email ?? $user->phone ?? '#'.$user->id).')',
                    )
                    ->searchable(['name', 'email', 'phone'])
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('body')
                    ->label(__('filament.post.body'))
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label(__('filament.fields.is_active'))
                    ->default(true)
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
