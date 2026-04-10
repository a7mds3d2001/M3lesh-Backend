<?php

namespace App\Filament\Resources\SupportTicket\Pages;

use App\Filament\Resources\SupportTicket\SupportTicketResource;
use App\Models\SupportTicket\SupportTicket;
use App\Models\SupportTicket\SupportTicketLog;
use App\Models\User\Admin;
use App\Services\Notifications\NotificationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;

class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->getRecord()->loadMissing('post');
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('filament.support_ticket.ticket_information');
    }

    public function getRelationManagersContentComponent(): Component
    {
        $component = parent::getRelationManagersContentComponent();

        if ($component instanceof Tabs) {
            $component->contained();
        }

        return $component;
    }

    protected function getHeaderActions(): array
    {
        /** @var SupportTicket $record */
        $record = $this->getRecord();
        $resource = static::getResource();

        return [
            Action::make('changeStatus')
                ->label(__('filament.support_ticket.change_status'))
                ->icon('heroicon-o-arrow-path')
                ->iconButton()
                ->color('primary')
                ->tooltip(__('filament.support_ticket.change_status'))
                ->form([
                    Select::make('status')
                        ->label(__('filament.support_ticket.status'))
                        ->options(SupportTicket::statuses())
                        ->required(),
                ])
                ->action(function (SupportTicket $record, array $data): void {
                    $oldStatus = $record->status;
                    $newStatus = $data['status'];
                    $record->update(['status' => $newStatus]);

                    SupportTicketLog::create([
                        'ticket_id' => $record->id,
                        'actor_type' => Admin::class,
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
                ->visible(fn (SupportTicket $record) => ! $record->trashed() && auth()->guard('admin')->user()->can('manage_support_ticket_status')),
            Action::make('changePriority')
                ->label(__('filament.support_ticket.change_priority'))
                ->icon('heroicon-o-flag')
                ->iconButton()
                ->color('primary')
                ->tooltip(__('filament.support_ticket.change_priority'))
                ->form([
                    Select::make('priority')
                        ->label(__('filament.support_ticket.priority'))
                        ->options(SupportTicket::priorities())
                        ->required(),
                ])
                ->action(function (SupportTicket $record, array $data): void {
                    $oldPriority = $record->priority;
                    $newPriority = $data['priority'];
                    $record->update(['priority' => $newPriority]);

                    SupportTicketLog::create([
                        'ticket_id' => $record->id,
                        'actor_type' => Admin::class,
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
                ->visible(fn (SupportTicket $record) => ! $record->trashed() && auth()->guard('admin')->user()->can('manage_support_ticket_priority')),
            Action::make('close')
                ->label(__('filament.support_ticket.close'))
                ->icon('heroicon-o-lock-closed')
                ->iconButton()
                ->color('gray')
                ->tooltip(__('filament.support_ticket.close'))
                ->requiresConfirmation()
                ->action(function (SupportTicket $record): void {
                    $oldStatus = $record->status;
                    $record->update(['status' => SupportTicket::STATUS_CLOSED]);

                    SupportTicketLog::create([
                        'ticket_id' => $record->id,
                        'actor_type' => Admin::class,
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
                ->visible(fn (SupportTicket $record) => ! $record->trashed() && $record->status !== SupportTicket::STATUS_CLOSED && auth()->guard('admin')->user()->can('manage_support_ticket_status')),
            EditAction::make()
                ->slideOver()
                ->authorize(fn (): bool => true)
                ->hidden(fn () => $record->trashed()),
            DeleteAction::make()
                ->authorize(fn (): bool => true)
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn (SupportTicket $record) => $record->trashed()),
            RestoreAction::make()
                ->iconButton()
                ->hidden(fn () => ! $record->trashed() || ! $resource::canRestore($record)),
            ForceDeleteAction::make()
                ->iconButton()
                ->successRedirectUrl($resource::getUrl('index'))
                ->hidden(fn () => ! $record->trashed() || ! $resource::canForceDelete($record)),
        ];
    }
}
