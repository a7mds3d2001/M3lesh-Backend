<?php

namespace App\Filament\Resources\Notifications\Schemas;

use App\Models\ContentPage\ContentPage;
use App\Models\SupportTicket\SupportTicket;
use App\Models\User\Admin;
use App\Models\User\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('recipient_type')
                ->label(__('filament.notifications.recipient_type'))
                ->options([
                    'user' => __('filament.resources.user_singular'),
                    'admin' => __('filament.resources.admin'),
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function (callable $set) {
                    $set('recipient_ids', []);
                }),
            TextInput::make('recipient_ids_placeholder')
                ->label(__('filament.notifications.recipients'))
                ->placeholder(__('filament.notifications.select_recipient_type_first'))
                ->disabled()
                ->dehydrated(false)
                ->visible(fn (callable $get) => blank($get('recipient_type'))),
            Select::make('recipient_ids')
                ->label(__('filament.notifications.recipients'))
                ->multiple()
                ->searchable()
                ->options(function (callable $get) {
                    return match ($get('recipient_type')) {
                        'admin' => Admin::query()->orderBy('name')->limit(200)->pluck('name', 'id')->all(),
                        default => User::query()->orderBy('name')->limit(200)->pluck('name', 'id')->all(),
                    };
                })
                ->required(fn (callable $get) => in_array($get('recipient_type'), ['user', 'admin'], true))
                ->visible(fn (callable $get) => in_array($get('recipient_type'), ['user', 'admin'], true)),
            TextInput::make('title')
                ->label(__('filament.fields.title'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Textarea::make('body')
                ->label(__('filament.notifications.body'))
                ->rows(4)
                ->required()
                ->columnSpanFull(),
            FileUpload::make('image')
                ->label(__('filament.fields.image'))
                ->disk('public')
                ->directory('notifications')
                ->image()
                ->imageEditor()
                ->openable()
                ->downloadable()
                ->columnSpanFull(),
            Select::make('target_type')
                ->label(__('filament.notifications.target_type'))
                ->options([
                    'content_pages' => __('filament.resources.content_page_singular'),
                    'tickets' => __('filament.support_ticket.ticket_singular'),
                ])
                ->searchable()
                ->live()
                ->afterStateUpdated(function (callable $set) {
                    $set('target_id', null);
                }),
            TextInput::make('target_id_placeholder')
                ->label(__('filament.notifications.target_id'))
                ->placeholder(__('filament.notifications.select_target_type_first'))
                ->disabled()
                ->dehydrated(false)
                ->visible(fn (callable $get) => blank($get('target_type'))),
            Select::make('target_id')
                ->label(__('filament.notifications.target_id'))
                ->searchable()
                ->required(fn (callable $get) => filled($get('target_type')))
                ->options(fn (callable $get) => static::initialTargetOptions($get('target_type')))
                ->getSearchResultsUsing(fn (string $search, callable $get) => static::searchTargetOptions($get('target_type'), $search))
                ->getOptionLabelUsing(fn ($value, callable $get) => static::targetLabel($get('target_type'), $value))
                ->visible(fn (callable $get) => filled($get('target_type'))),
        ]);
    }

    /**
     * @return array<int|string, string>
     */
    protected static function initialTargetOptions(?string $targetType): array
    {
        if (! $targetType) {
            return [];
        }

        return match ($targetType) {
            'content_pages' => ContentPage::query()->orderByDesc('id')->limit(50)->pluck('title', 'id')->all(),
            'tickets' => SupportTicket::query()->orderByDesc('id')->limit(50)->pluck('ticket_number', 'id')->all(),
            default => [],
        };
    }

    /**
     * @return array<int|string, string>
     */
    protected static function searchTargetOptions(?string $targetType, string $search): array
    {
        $search = trim($search);
        if (! $targetType) {
            return [];
        }

        return match ($targetType) {
            'content_pages' => ContentPage::query()
                ->where('title', 'like', "%{$search}%")
                ->orderByDesc('id')
                ->limit(50)
                ->pluck('title', 'id')
                ->all(),
            'tickets' => SupportTicket::query()
                ->where('ticket_number', 'like', "%{$search}%")
                ->orderByDesc('id')
                ->limit(50)
                ->pluck('ticket_number', 'id')
                ->all(),
            default => [],
        };
    }

    protected static function targetLabel(?string $targetType, mixed $value): ?string
    {
        if (! $targetType || ! $value) {
            return null;
        }

        return match ($targetType) {
            'content_pages' => ContentPage::query()->find($value)?->title,
            'tickets' => SupportTicket::query()->find($value)?->ticket_number,
            default => null,
        };
    }
}
