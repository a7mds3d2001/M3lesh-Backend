<?php

namespace App\Filament\Resources\SupportTicket\Schemas;

use App\Models\Post\Post;
use App\Models\SupportTicket\SupportTicket;
use App\Models\User\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class SupportTicketForm
{
    public static function configure(Schema $schema, array $context = []): Schema
    {
        $components = [
            Radio::make('owner_type')
                ->label(__('filament.support_ticket.owner_type'))
                ->options([
                    'user' => __('filament.support_ticket.link_to_user'),
                    'visitor' => __('filament.support_ticket.visitor'),
                ])
                ->default('user')
                ->live()
                ->visible(fn ($record) => $record === null),

            Select::make('user_id')
                ->label(__('filament.support_ticket.user'))
                ->options(
                    User::whereNull('deleted_at')->orderBy('name')->get()->mapWithKeys(fn ($u) => [$u->id => $u->name.' ('.$u->email.')']),
                )
                ->searchable()
                ->preload()
                ->visible(fn ($get, $record) => $record === null && $get('owner_type') === 'user'),

            TextInput::make('visitor_name')
                ->label(__('filament.support_ticket.visitor_name'))
                ->maxLength(255)
                ->visible(fn ($get, $record) => $record === null && $get('owner_type') === 'visitor'),

            PhoneInput::make('visitor_phone')
                ->label(__('filament.support_ticket.visitor_phone'))
                ->defaultCountry('EG')
                ->visible(fn ($get, $record) => $record === null && $get('owner_type') === 'visitor'),

            TextInput::make('visitor_email')
                ->label(__('filament.support_ticket.visitor_email'))
                ->email()
                ->maxLength(255)
                ->visible(fn ($get, $record) => $record === null && $get('owner_type') === 'visitor'),

            Select::make('priority')
                ->label(__('filament.support_ticket.priority'))
                ->options(SupportTicket::priorities())
                ->default(SupportTicket::PRIORITY_NORMAL)
                ->required()
                ->columnSpanFull(),

            Textarea::make('message')
                ->label(__('filament.support_ticket.message'))
                ->required()
                ->rows(4)
                ->columnSpanFull(),

            FileUpload::make('attachments')
                ->label(__('filament.support_ticket.attachments'))
                ->disk('public')
                ->directory('support_tickets')
                ->visibility('public')
                ->multiple()
                ->maxFiles(10)
                ->columnSpanFull(),

            Select::make('post_id')
                ->label(__('filament.post.linked_post'))
                ->relationship(
                    name: 'post',
                    titleAttribute: 'body',
                    modifyQueryUsing: fn ($query) => $query->orderByDesc('id'),
                )
                ->getOptionLabelFromRecordUsing(fn (Post $record): string => '#'.$record->id.' '.Str::limit($record->body, 50))
                ->searchable()
                ->preload()
                ->nullable()
                ->visible(fn ($record) => $record !== null)
                ->columnSpanFull(),

        ];

        return $schema->components($components)->columns(2);
    }
}
