<?php

namespace App\Filament\Resources\Notifications;

use App\Filament\Resources\ContentPage\ContentPageResource;
use App\Filament\Resources\Notifications\Pages\ListNotificationBroadcasts;
use App\Filament\Resources\Notifications\Pages\ViewNotificationBroadcast;
use App\Filament\Resources\Notifications\Schemas\NotificationBroadcastForm;
use App\Filament\Resources\Notifications\Tables\NotificationBroadcastsTable;
use App\Filament\Resources\SupportTicket\SupportTicketResource;
use App\Models\ContentPage\ContentPage;
use App\Models\Notifications\NotificationBroadcast;
use App\Models\SupportTicket\SupportTicket;
use App\Models\User\Admin;
use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationBroadcastResource extends Resource
{
    protected static ?string $model = NotificationBroadcast::class;

    protected static ?string $slug = 'notification-broadcasts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.notification_broadcasts');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.notification_broadcast_singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.notification_broadcasts');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin instanceof Admin) {
            return false;
        }

        return $admin->can('view_notification_broadcasts')
            || $admin->can('send_notification_broadcasts');
    }

    /**
     * @return array<int, NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return array_map(
            fn (NavigationItem $item) => $item->visible(
                function (): bool {
                    $admin = auth()->guard('admin')->user();

                    return $admin instanceof Admin
                        && ($admin->can('view_notification_broadcasts')
                            || $admin->can('send_notification_broadcasts'));
                },
            ),
            parent::getNavigationItems(),
        );
    }

    public static function canViewAny(): bool
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin instanceof Admin) {
            return false;
        }

        return $admin->can('view_notification_broadcasts')
            || $admin->can('send_notification_broadcasts');
    }

    public static function canView($record): bool
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin instanceof Admin) {
            return false;
        }

        return $admin->can('view_notification_broadcasts');
    }

    public static function canCreate(): bool
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin instanceof Admin) {
            return false;
        }

        return $admin->can('send_notification_broadcasts');
    }

    public static function form(Schema $schema): Schema
    {
        return NotificationBroadcastForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.resources.notification_broadcast_singular'))
                ->schema([
                    TextEntry::make('topic')
                        ->label(__('filament.notifications.topic'))
                        ->badge(),
                    TextEntry::make('title')
                        ->label(__('filament.fields.title'))
                        ->columnSpanFull(),
                    TextEntry::make('body')
                        ->label(__('filament.notifications.body'))
                        ->placeholder(__('filament.placeholder.empty'))
                        ->columnSpanFull(),
                    ImageEntry::make('image')
                        ->label(__('filament.fields.image'))
                        ->getStateUsing(fn (NotificationBroadcast $record) => $record->image ? storage_public_url($record->image) : null)
                        ->height(200)
                        ->visible(fn (NotificationBroadcast $record) => filled($record->image))
                        ->columnSpanFull(),
                    TextEntry::make('target_type')
                        ->label(__('filament.notifications.target_type'))
                        ->badge()
                        ->placeholder(__('filament.placeholder.empty')),
                    TextEntry::make('target_id')
                        ->label(__('filament.notifications.target_id'))
                        ->formatStateUsing(fn (NotificationBroadcast $record): string => static::getTargetLabel($record))
                        ->badge()
                        ->url(fn (NotificationBroadcast $record): ?string => static::getTargetUrl($record))
                        ->placeholder(__('filament.placeholder.empty')),
                    TextEntry::make('sent_at')
                        ->label(__('filament.notifications.sent_at'))
                        ->dateTime()
                        ->placeholder(__('filament.placeholder.empty')),
                    TextEntry::make('created_at')
                        ->label(__('filament.fields.created_at'))
                        ->dateTime(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return NotificationBroadcastsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationBroadcasts::route('/'),
            'view' => ViewNotificationBroadcast::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    protected static function getTargetUrl(NotificationBroadcast $record): ?string
    {
        if (! $record->target_type || ! $record->target_id) {
            return null;
        }

        return match ($record->target_type) {
            'content_pages' => ContentPageResource::getUrl('view', ['record' => $record->target_id]),
            'tickets' => SupportTicketResource::getUrl('view', ['record' => $record->target_id]),
            default => null,
        };
    }

    protected static function getTargetLabel(NotificationBroadcast $record): string
    {
        if (! $record->target_type || ! $record->target_id) {
            return '—';
        }

        $label = match ($record->target_type) {
            'content_pages' => ContentPage::query()->find($record->target_id)?->title,
            'tickets' => SupportTicket::query()->find($record->target_id)?->ticket_number,
            default => null,
        };

        return filled($label) ? (string) $label : "#{$record->target_id}";
    }
}
