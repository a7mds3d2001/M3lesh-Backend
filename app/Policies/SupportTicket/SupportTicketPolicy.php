<?php

namespace App\Policies\SupportTicket;

use App\Models\SupportTicket\SupportTicket;
use Illuminate\Contracts\Auth\Authenticatable;

class SupportTicketPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('view_support_tickets');
    }

    public function view(Authenticatable $user, SupportTicket $supportTicket): bool
    {
        return $user->can('view_support_tickets');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('create_support_tickets');
    }

    public function update(Authenticatable $user, SupportTicket $supportTicket): bool
    {
        return $user->can('edit_support_tickets');
    }

    public function delete(Authenticatable $user, SupportTicket $supportTicket): bool
    {
        return $user->can('delete_support_tickets');
    }

    public function restore(Authenticatable $user, SupportTicket $supportTicket): bool
    {
        return $user->can('restore_support_tickets');
    }

    public function forceDelete(Authenticatable $user, SupportTicket $supportTicket): bool
    {
        return $user->can('force_delete_support_tickets');
    }

    public function manageStatus(Authenticatable $user, SupportTicket $supportTicket): bool
    {
        return $user->can('manage_support_ticket_status');
    }

    public function managePriority(Authenticatable $user, SupportTicket $supportTicket): bool
    {
        return $user->can('manage_support_ticket_priority');
    }

    public function addLog(Authenticatable $user, SupportTicket $supportTicket): bool
    {
        return $user->can('create_support_ticket_logs');
    }
}
