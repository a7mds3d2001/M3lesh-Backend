<?php

namespace App\Filament\Resources\Notifications;

use App\Filament\Resources\Admin\AdminResource;
use App\Filament\Resources\ContentPage\ContentPageResource;
use App\Filament\Resources\Notifications\Pages\ListNotifications;
use App\Filament\Resources\Notifications\Pages\ViewNotification;
use App\Filament\Resources\Notifications\Schemas\NotificationForm;
use App\Filament\Resources\Notifications\Tables\NotificationsTable;
use App\Filament\Resources\SupportTicket\SupportTicketResource;
use App\Filament\Resources\User\UserResource;
use App\Models\ContentPage\ContentPage;
use App\Models\Notifications\Notification;
use App\Models\SupportTicket\SupportTicket;
use App\Models\User\Admin;
use App\Models\User\User;
use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $slug = 'notifications';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.notifications');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.notification_singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.notifications');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin instanceof Admin) {
            return false;
        }

        return $admin->can('view_notifications') || $admin->can('send_notifications');
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
                        && ($admin->can('view_notifications') || $admin->can('send_notifications'));
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

        return $admin->can('view_notifications') || $admin->can('send_notifications');
    }

    public static function getViewAuthorizationResponse(Model $record): Response
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin instanceof Admin) {
            return Response::deny();
        }

        if ($admin->can('view_notifications') || $admin->can('send_notifications')) {
            return Response::allow();
        }

        if (
            $record instanceof Notification
            && $record->notifiable_type === Admin::class
            && (int) $record->notifiable_id === (int) $admin->getKey()
        ) {
            return Response::allow();
        }

        return Response::deny();
    }

    public static function canView($record): bool
    {
        return static::getViewAuthorizationResponse($record)->allowed();
    }

    public static function canCreate(): bool
    {
        $admin = auth()->guard('admin')->user();

        if (! $admin instanceof Admin) {
            return false;
        }

        return $admin->can('send_notifications');
    }

    public static function form(Schema $schema): Schema
    {
        return NotificationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament.resources.notification_singular'))
                ->schema([
                    TextEntry::make('title')
                        ->label(__('filament.fields.title'))
                        ->columnSpanFull(),
                    TextEntry::make('body')
                        ->label(__('filament.notifications.body'))
                        ->placeholder(__('filament.placeholder.empty'))
                        ->columnSpanFull(),
                    ImageEntry::make('image')
                        ->label(__('filament.fields.image'))
                        ->getStateUsing(fn (Notification $record) => $record->image ? storage_public_url($record->image) : null)
                        ->height(200)
                        ->visible(fn (Notification $record) => filled($record->image))
                        ->columnSpanFull(),
                    TextEntry::make('notifiable_type')
                        ->label(__('filament.notifications.recipient'))
                        ->formatStateUsing(function ($state, Notification $record) {
                            return str_ends_with((string) $state, '\\Admin')
                                ? __('filament.resources.admin')
                                : __('filament.resources.user_singular');
                        })
                        ->badge(),
                    TextEntry::make('notifiable_id')
                        ->label(__('filament.notifications.recipient_id'))
                        ->formatStateUsing(function (Notification $record): string {
                            $n = $record->notifiable;

                            return ($n instanceof User || $n instanceof Admin) ? $n->name : '—';
                        })
                        ->badge()
                        ->url(function (Notification $record): ?string {
                            if (! $record->notifiable) {
                                return null;
                            }

                            return $record->notifiable instanceof Admin
                                ? AdminResource::getUrl('view', ['record' => $record->notifiable->getKey()])
                                : UserResource::getUrl('view', ['record' => $record->notifiable->getKey()]);
                        }),
                    TextEntry::make('target_type')
                        ->label(__('filament.notifications.target_type'))
                        ->badge()
                        ->placeholder(__('filament.placeholder.empty')),
                    TextEntry::make('target_id')
                        ->label(__('filament.notifications.target_id'))
                        ->formatStateUsing(fn (Notification $record): string => static::getTargetLabel($record))
                        ->badge()
                        ->url(fn (Notification $record): ?string => static::getTargetUrl($record))
                        ->placeholder(__('filament.placeholder.empty')),
                    TextEntry::make('data')
                        ->label(__('filament.notifications.data_json'))
                        ->formatStateUsing(function ($state) {
                            if ($state === null) {
                                return '—';
                            }

                            $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            return $json ?: '—';
                        })
                        ->copyable()
                        ->copyMessage(__('filament.activity.copied'))
                        ->copyMessageDuration(1500),
                    TextEntry::make('sent_at')
                        ->label(__('filament.notifications.sent_at'))
                        ->dateTime()
                        ->placeholder(__('filament.placeholder.empty')),
                    TextEntry::make('read_at')
                        ->label(__('filament.notifications.read_at'))
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
        return NotificationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),
            'view' => ViewNotification::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    protected static function getTargetUrl(Notification $record): ?string
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

    protected static function getTargetLabel(Notification $record): string
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
