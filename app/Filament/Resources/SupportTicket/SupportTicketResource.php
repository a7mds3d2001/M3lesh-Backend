<?php

namespace App\Filament\Resources\SupportTicket;

use App\Filament\Resources\SupportTicket\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTicket\Pages\ViewSupportTicket;
use App\Filament\Resources\SupportTicket\RelationManagers\SupportTicketLogsRelationManager;
use App\Filament\Resources\SupportTicket\Schemas\SupportTicketForm;
use App\Filament\Resources\SupportTicket\Tables\SupportTicketsTable;
use App\Filament\Support\AuditInfolistSection;
use App\Models\SupportTicket\SupportTicket;
use App\Models\SupportTicket\SupportTicketLog;
use App\Services\Notifications\NotificationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->headerActions([
                        Action::make('changeStatus')
                            ->label(__('filament.support_ticket.change_status'))
                            ->icon('heroicon-o-arrow-path')
                            ->iconButton()
                            ->color('primary')
                            ->tooltip(__('filament.support_ticket.change_status'))
                            ->form([
                                \Filament\Forms\Components\Select::make('status')
                                    ->label(__('filament.support_ticket.status'))
                                    ->options(SupportTicket::statuses())
                                    ->required(),
                            ])
                            ->action(function ($record, array $data): void {
                                $oldStatus = $record->status;
                                $newStatus = $data['status'];
                                $record->update(['status' => $newStatus]);
                                SupportTicketLog::create([
                                    'ticket_id' => $record->id,
                                    'actor_type' => \App\Models\User\Admin::class,
                                    'actor_id' => current_audit_admin_id(),
                                    'message' => "Status changed from {$oldStatus} to {$newStatus}.",
                                    'log_type' => SupportTicketLog::LOG_TYPE_STATUS_CHANGE,
                                    'attachments' => null,
                                ]);

                                $record->loadMissing('user');
                                if ($record->user) {
                                    app(NotificationService::class)->notify($record->user, [
                                        'title' => 'Support ticket status changed',
                                        'body' => "Ticket {$record->ticket_number} status changed from {$oldStatus} to {$newStatus}.",
                                        'target_type' => 'tickets',
                                        'target_id' => $record->id,
                                    ]);
                                }
                            })
                            ->visible(fn ($record) => ! $record->trashed() && auth()->guard('admin')->user()->can('manage_support_ticket_status')),

                        Action::make('changePriority')
                            ->label(__('filament.support_ticket.change_priority'))
                            ->icon('heroicon-o-flag')
                            ->iconButton()
                            ->color('primary')
                            ->tooltip(__('filament.support_ticket.change_priority'))
                            ->form([
                                \Filament\Forms\Components\Select::make('priority')
                                    ->label(__('filament.support_ticket.priority'))
                                    ->options(SupportTicket::priorities())
                                    ->required(),
                            ])
                            ->action(function ($record, array $data): void {
                                $oldPriority = $record->priority;
                                $newPriority = $data['priority'];
                                $record->update(['priority' => $newPriority]);
                                SupportTicketLog::create([
                                    'ticket_id' => $record->id,
                                    'actor_type' => \App\Models\User\Admin::class,
                                    'actor_id' => current_audit_admin_id(),
                                    'message' => "Priority changed from {$oldPriority} to {$newPriority}.",
                                    'log_type' => SupportTicketLog::LOG_TYPE_PRIORITY_CHANGE,
                                    'attachments' => null,
                                ]);

                                $record->loadMissing('user');
                                if ($record->user) {
                                    app(NotificationService::class)->notify($record->user, [
                                        'title' => 'Support ticket priority changed',
                                        'body' => "Ticket {$record->ticket_number} priority changed from {$oldPriority} to {$newPriority}.",
                                        'target_type' => 'tickets',
                                        'target_id' => $record->id,
                                    ]);
                                }
                            })
                            ->visible(fn ($record) => ! $record->trashed() && auth()->guard('admin')->user()->can('manage_support_ticket_priority')),

                        Action::make('close')
                            ->label(__('filament.support_ticket.close'))
                            ->icon('heroicon-o-lock-closed')
                            ->iconButton()
                            ->color('gray')
                            ->tooltip(__('filament.support_ticket.close'))
                            ->requiresConfirmation()
                            ->action(function ($record): void {
                                $oldStatus = $record->status;
                                $record->update(['status' => SupportTicket::STATUS_CLOSED]);
                                SupportTicketLog::create([
                                    'ticket_id' => $record->id,
                                    'actor_type' => \App\Models\User\Admin::class,
                                    'actor_id' => current_audit_admin_id(),
                                    'message' => "Status changed from {$oldStatus} to closed.",
                                    'log_type' => SupportTicketLog::LOG_TYPE_STATUS_CHANGE,
                                    'attachments' => null,
                                ]);

                                $record->loadMissing('user');
                                if ($record->user) {
                                    app(NotificationService::class)->notify($record->user, [
                                        'title' => 'Support ticket closed',
                                        'body' => "Ticket {$record->ticket_number} was closed.",
                                        'target_type' => 'tickets',
                                        'target_id' => $record->id,
                                    ]);
                                }
                            })
                            ->visible(fn ($record) => ! $record->trashed() && $record->status !== SupportTicket::STATUS_CLOSED && auth()->guard('admin')->user()->can('manage_support_ticket_status')),

                        Action::make('delete')
                            ->label(__('filament.actions.delete'))
                            ->icon('heroicon-o-trash')
                            ->iconButton()
                            ->color('danger')
                            ->tooltip(__('filament.actions.delete'))
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->delete();

                                return redirect(static::getUrl('index'));
                            })
                            ->visible(fn ($record) => ! $record->trashed() && static::canDelete($record)),
                    ])
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
