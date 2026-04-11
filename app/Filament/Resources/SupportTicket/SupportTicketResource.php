<?php

namespace App\Filament\Resources\SupportTicket;

use App\Enums\Post\PostReportReason;
use App\Filament\Resources\Post\PostResource as FilamentPostResource;
use App\Filament\Resources\SupportTicket\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTicket\Pages\ViewSupportTicket;
use App\Filament\Resources\SupportTicket\RelationManagers\SupportTicketLogsRelationManager;
use App\Filament\Resources\SupportTicket\Schemas\SupportTicketForm;
use App\Filament\Resources\SupportTicket\Tables\SupportTicketsTable;
use App\Filament\Support\AuditInfolistSection;
use App\Models\SupportTicket\SupportTicket;
use BackedEnum;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $recordTitleAttribute = 'ticket_number';

    protected static ?string $slug = 'support-tickets';

    public static function getNavigationLabel(): string
    {
        return __('filament.support_ticket.nav.support_tickets');
    }

    public static function getModelLabel(): string
    {
        return __('filament.support_ticket.ticket_singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.support_ticket.nav.support_tickets');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->guard('admin')->user()->can('view_support_tickets');
    }

    public static function form(Schema $schema): Schema
    {
        return SupportTicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportTicketsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.support_ticket.ticket_information'))
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('filament.support_ticket.status'))
                            ->badge()
                            ->formatStateUsing(fn (string $state) => SupportTicket::statuses()[$state] ?? __('filament.support_ticket.status_closed'))
                            ->color(fn (string $state): string => match ($state) {
                                SupportTicket::STATUS_CLOSED => 'gray',
                                'resolved' => 'gray',
                                SupportTicket::STATUS_IN_PROGRESS => 'warning',
                                default => 'primary',
                            }),

                        TextEntry::make('priority')
                            ->label(__('filament.support_ticket.priority'))
                            ->badge()
                            ->formatStateUsing(fn (string $state) => SupportTicket::priorities()[$state] ?? $state)
                            ->color(fn (string $state): string => match ($state) {
                                SupportTicket::PRIORITY_HIGH => 'danger',
                                SupportTicket::PRIORITY_LOW => 'gray',
                                default => 'primary',
                            }),

                        TextEntry::make('ticket_number')
                            ->label(__('filament.support_ticket.ticket_number'))
                            ->weight('bold'),

                        TextEntry::make('owner_label')
                            ->label(__('filament.support_ticket.owner'))
                            ->getStateUsing(fn ($record) => $record->ownerLabel()),

                        TextEntry::make('phone')
                            ->label(__('filament.support_ticket.phone'))
                            ->getStateUsing(function ($record) {
                                if ($record->user_id && $record->user) {
                                    return $record->user->phone ?? '—';
                                }

                                return $record->visitor_phone ?? '—';
                            }),

                        TextEntry::make('email')
                            ->label(__('filament.support_ticket.email'))
                            ->getStateUsing(function ($record) {
                                if ($record->user_id && $record->user) {
                                    return $record->user->email ?? '—';
                                }

                                return $record->visitor_email ?? '—';
                            }),

                        TextEntry::make('message')
                            ->label(__('filament.support_ticket.message'))
                            ->columnSpanFull(),

                        TextEntry::make('post_id')
                            ->label(__('filament.post.linked_post'))
                            ->formatStateUsing(function (?int $state, SupportTicket $record): string {
                                if (! $state) {
                                    return '—';
                                }

                                return '#'.$state.' '.Str::limit($record->post?->body ?? '', 60);
                            })
                            ->url(fn (SupportTicket $record): ?string => $record->post_id
                                ? FilamentPostResource::getUrl('view', ['record' => $record->post_id])
                                : null)
                            ->visible(fn (SupportTicket $record): bool => (bool) $record->post_id),

                        TextEntry::make('post_report_reason_display')
                            ->label(__('filament.support_ticket.post_report_reason'))
                            ->getStateUsing(function (SupportTicket $record): string {
                                $record->loadMissing('postReport');
                                if (! $record->postReport) {
                                    return '—';
                                }
                                $reason = $record->postReport->reason;
                                if (! $reason instanceof PostReportReason) {
                                    return '—';
                                }

                                return $reason->labelsBilingual();
                            })
                            ->visible(fn (SupportTicket $record): bool => (bool) $record->post_id),

                        TextEntry::make('post_report_details_display')
                            ->label(__('filament.support_ticket.post_report_details'))
                            ->getStateUsing(function (SupportTicket $record): ?string {
                                $record->loadMissing('postReport');

                                return $record->postReport?->details;
                            })
                            ->placeholder('—')
                            ->visible(fn (SupportTicket $record): bool => (bool) $record->post_id && (bool) $record->postReport?->details),

                        RepeatableEntry::make('attachments')
                            ->label(__('filament.support_ticket.attachments'))
                            ->getStateUsing(function ($record) {
                                if (! is_array($record->attachments) || count($record->attachments) === 0) {
                                    return [];
                                }

                                return collect($record->attachments)
                                    ->map(function ($path) {
                                        $url = storage_public_url($path);

                                        return [
                                            'file_url' => $url,
                                        ];
                                    })
                                    ->values()
                                    ->all();
                            })
                            ->schema([
                                TextEntry::make('file_url')
                                    ->hiddenLabel()
                                    ->formatStateUsing(function (?string $state): string {
                                        if (blank($state)) {
                                            return '—';
                                        }

                                        $path = parse_url($state, PHP_URL_PATH) ?: $state;
                                        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                        $name = basename($path);

                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                        $typeKey = in_array($extension, $imageExtensions, true)
                                            ? 'filament.support_ticket.attachment_type_image'
                                            : 'filament.support_ticket.attachment_type_document';

                                        $typeLabel = __($typeKey);
                                        $clickText = __('filament.support_ticket.attachment_click_to_view');
                                        $extLabel = $extension !== '' ? strtoupper($extension) : 'FILE';

                                        return "{$typeLabel} ({$extLabel}) - {$name} - {$clickText}";
                                    })
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab(),
                            ])
                            ->contained(true)
                            ->columnSpanFull()
                            ->grid([
                                'default' => 1,
                            ])
                            ->visible(fn ($record) => is_array($record->attachments) && count($record->attachments) > 0),
                    ])
                    ->columns(2),
                AuditInfolistSection::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SupportTicketLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupportTickets::route('/'),
            'view' => ViewSupportTicket::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->guard('admin')->user()->can('view_support_tickets');
    }

    public static function canView($record): bool
    {
        return auth()->guard('admin')->user()->can('view_support_tickets');
    }

    public static function canCreate(): bool
    {
        return auth()->guard('admin')->user()->can('create_support_tickets');
    }

    public static function canEdit($record): bool
    {
        return auth()->guard('admin')->user()->can('edit_support_tickets');
    }

    public static function canDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('delete_support_tickets');
    }

    public static function canForceDelete($record): bool
    {
        return auth()->guard('admin')->user()->can('force_delete_support_tickets');
    }

    public static function canRestore($record): bool
    {
        return auth()->guard('admin')->user()->can('restore_support_tickets');
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
