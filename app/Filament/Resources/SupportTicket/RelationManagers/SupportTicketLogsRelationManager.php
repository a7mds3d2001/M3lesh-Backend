<?php

namespace App\Filament\Resources\SupportTicket\RelationManagers;

use App\Models\SupportTicket\SupportTicket;
use App\Models\SupportTicket\SupportTicketLog;
use App\Models\User\Admin;
use App\Services\Notifications\NotificationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SupportTicketLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.support_ticket.logs_tab');
    }

    public static function getModelLabel(): string
    {
        return __('filament.support_ticket.log_singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.support_ticket.logs_tab');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('log_type')
                    ->label(__('filament.support_ticket.log_type'))
                    ->options([
                        SupportTicketLog::LOG_TYPE_COMMENT => __('filament.support_ticket.log_type_comment'),
                        SupportTicketLog::LOG_TYPE_INTERNAL_NOTE => __('filament.support_ticket.log_type_internal_note'),
                    ])
                    ->default(SupportTicketLog::LOG_TYPE_COMMENT)
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('message')
                    ->label(__('filament.support_ticket.message'))
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('attachments')
                    ->label(__('filament.support_ticket.attachments'))
                    ->disk('public')
                    ->directory('support_ticket_logs')
                    ->visibility('public')
                    ->multiple()
                    ->maxFiles(5)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('actorLabel')
                    ->label(__('filament.support_ticket.who'))
                    ->weight('bold')
                    ->getStateUsing(function ($record) {
                        return $record instanceof SupportTicketLog
                            ? $record->actorLabel()
                            : null;
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('log_type')
                    ->label(__('filament.support_ticket.log_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => SupportTicketLog::logTypes()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        SupportTicketLog::LOG_TYPE_STATUS_CHANGE => 'warning',
                        SupportTicketLog::LOG_TYPE_PRIORITY_CHANGE => 'info',
                        SupportTicketLog::LOG_TYPE_INTERNAL_NOTE => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('message')
                    ->label(__('filament.support_ticket.message'))
                    ->limit(80)
                    ->wrap()
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament.support_ticket.add_log'))
                    ->modalHeading(__('filament.support_ticket.add_log'))
                    ->createAnother(false)
                    ->hidden(function (): bool {
                        /** @var SupportTicket $ticket */
                        $ticket = $this->getOwnerRecord();

                        return $ticket->status === SupportTicket::STATUS_CLOSED
                            || ! auth()->guard('admin')->user()?->can('create_support_ticket_logs');
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        /** @var SupportTicket $ticket */
                        $ticket = $this->getOwnerRecord();
                        $data['ticket_id'] = $ticket->id;
                        $data['actor_type'] = Admin::class;
                        $data['actor_id'] = current_audit_admin_id();
                        $data['attachments'] = $data['attachments'] ?? null;

                        return $data;
                    })
                    ->using(function (array $data): SupportTicketLog {
                        /** @var SupportTicket $ticket */
                        $ticket = $this->getOwnerRecord();

                        $log = SupportTicketLog::create($data);

                        // Only notify the ticket owner when the admin adds a public comment.
                        $ticket->loadMissing('user');
                        if (($data['log_type'] ?? null) === SupportTicketLog::LOG_TYPE_COMMENT && $ticket->user) {
                            app(NotificationService::class)->notify($ticket->user, [
                                'title' => 'New reply on support ticket',
                                'body' => "You received a new message on ticket {$ticket->ticket_number}.",
                                'target_type' => 'tickets',
                                'target_id' => $ticket->id,
                            ]);
                        }

                        return $log;
                    }),
            ])
            ->recordActions([
                Action::make('view_message')
                    ->label(__('filament.support_ticket.view_message'))
                    ->icon('heroicon-o-document-text')
                    ->modalHeading(__('filament.support_ticket.view_message'))
                    ->modalContent(fn (SupportTicketLog $record) => new HtmlString(nl2br(e($record->message ?: '—'))))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament.support_ticket.close')),
                Action::make('view_attachments')
                    ->label(__('filament.support_ticket.view_attachments'))
                    ->icon('heroicon-o-paper-clip')
                    ->modalHeading(__('filament.support_ticket.view_attachments'))
                    ->visible(fn (SupportTicketLog $record) => is_array($record->attachments) && count($record->attachments) > 0)
                    ->infolist([
                        RepeatableEntry::make('attachments_list')
                            ->label(__('filament.support_ticket.attachments'))
                            ->getStateUsing(function (SupportTicketLog $record): array {
                                if (! is_array($record->attachments) || count($record->attachments) === 0) {
                                    return [];
                                }

                                return collect($record->attachments)
                                    ->map(function (string $path) {
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
                                    ->url(fn (?string $state) => $state)
                                    ->openUrlInNewTab(),
                            ])
                            ->columnSpanFull(),
                    ]),
                DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->visible(fn () => auth()->guard('admin')->user()?->can('delete_support_ticket_logs')),
                RestoreAction::make()
                    ->iconButton()
                    ->color('success')
                    ->hidden(fn (SupportTicketLog $record) => ! $record->trashed())
                    ->visible(fn () => auth()->guard('admin')->user()?->can('restore_support_ticket_logs')),
                ForceDeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->hidden(fn (SupportTicketLog $record) => ! $record->trashed())
                    ->visible(fn () => auth()->guard('admin')->user()?->can('force_delete_support_ticket_logs')),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->deferFilters(false)
            ->defaultSort('created_at', 'asc');
    }
}
