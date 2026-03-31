<?php

namespace App\Filament\Resources\SupportTicket\Pages;

use App\Filament\Resources\SupportTicket\SupportTicketResource;
use App\Models\SupportTicket\SupportTicket;
use App\Models\SupportTicket\SupportTicketLog;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupportTickets extends ListRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false)
                ->using(function (array $data): SupportTicket {
                    $ownerType = $data['owner_type'] ?? 'visitor';
                    unset($data['owner_type']);

                    if ($ownerType === 'user') {
                        $data['visitor_name'] = null;
                        $data['visitor_phone'] = null;
                        $data['visitor_email'] = null;
                    } else {
                        $data['user_id'] = null;
                    }

                    $data['status'] = SupportTicket::STATUS_OPEN;
                    $data['priority'] = $data['priority'] ?? SupportTicket::PRIORITY_NORMAL;
                    $data['is_active'] = $data['is_active'] ?? true;

                    $ticket = SupportTicket::create($data);

                    SupportTicketLog::create([
                        'ticket_id' => $ticket->id,
                        'actor_type' => \App\Models\User\Admin::class,
                        'actor_id' => current_audit_admin_id(),
                        'message' => $ticket->message,
                        'log_type' => SupportTicketLog::LOG_TYPE_COMMENT,
                        'attachments' => $ticket->attachments,
                    ]);

                    return $ticket;
                }),
        ];
    }
}
