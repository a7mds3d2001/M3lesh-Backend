<?php

namespace App\Http\Controllers\Api\Admin\SupportTicket;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportTicket\StoreSupportTicketLogRequest;
use App\Http\Requests\SupportTicket\StoreSupportTicketRequest;
use App\Http\Requests\SupportTicket\UpdateSupportTicketPriorityRequest;
use App\Http\Requests\SupportTicket\UpdateSupportTicketRequest;
use App\Http\Requests\SupportTicket\UpdateSupportTicketStatusRequest;
use App\Http\Resources\SupportTicket\SupportTicketResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\SupportTicket\SupportTicket;
use App\Models\SupportTicket\SupportTicketLog;
use App\Models\User\Admin;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['id', 'ticket_number', 'user_id', 'status', 'priority', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', SupportTicket::class);

        $query = SupportTicket::query()
            ->with(['user'])
            ->withAudit()
            ->byStatus($request->input('status'))
            ->byPriority($request->input('priority'))
            ->search($request->input('search') ?? $request->input('q'));

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderBy('updated_at', 'desc');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (SupportTicket $ticket) => SupportTicketResource::make($ticket)->resolve($request));

        return response()->json($paginator);
    }

    public function store(StoreSupportTicketRequest $request): JsonResponse
    {
        $this->authorize('create', SupportTicket::class);

        $validated = $request->validated();
        $validated['status'] = SupportTicket::STATUS_OPEN;
        $validated['priority'] = $validated['priority'] ?? SupportTicket::PRIORITY_NORMAL;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $ticket = SupportTicket::create($validated);
        $this->createLog($ticket, SupportTicketLog::LOG_TYPE_COMMENT, $validated['message'], $validated['attachments'] ?? null, true);

        $ticket->load(['user', 'logs.actor'])->loadAudit();

        return SupportTicketResource::make($ticket)->response($request)->setStatusCode(201);
    }

    public function show(Request $request, SupportTicket $support_ticket): JsonResponse
    {
        $this->authorize('view', $support_ticket);
        $support_ticket->load(['user', 'logs.actor', 'creator', 'updater']);

        return SupportTicketResource::make($support_ticket)->response($request);
    }

    public function update(UpdateSupportTicketRequest $request, SupportTicket $support_ticket): JsonResponse
    {
        $this->authorize('update', $support_ticket);

        $validated = $request->validated();
        unset($validated['status']);
        $support_ticket->update($validated);

        $support_ticket->load(['user', 'logs.actor'])->loadAudit();

        return SupportTicketResource::make($support_ticket)->response($request);
    }

    public function destroy(SupportTicket $support_ticket): JsonResponse
    {
        $this->authorize('delete', $support_ticket);
        $support_ticket->delete();

        return response()->json(null, 204);
    }

    public function updateStatus(UpdateSupportTicketStatusRequest $request, SupportTicket $support_ticket): JsonResponse
    {
        $this->authorize('manageStatus', $support_ticket);

        $newStatus = $request->input('status');
        $oldStatus = $support_ticket->status;
        $support_ticket->update(['status' => $newStatus]);

        $this->createLog(
            $support_ticket,
            SupportTicketLog::LOG_TYPE_STATUS_CHANGE,
            "Status changed from {$oldStatus} to {$newStatus}.",
            null,
            true,
        );

        $support_ticket->load(['user', 'logs.actor'])->loadAudit();

        if ($support_ticket->user) {
            app(NotificationService::class)->notify($support_ticket->user, [
                'title' => 'Support ticket status changed',
                'body' => "Ticket {$support_ticket->ticket_number} status changed from {$oldStatus} to {$newStatus}.",
                'target_type' => 'tickets',
                'target_id' => $support_ticket->id,
            ]);
        }

        return SupportTicketResource::make($support_ticket)->response($request);
    }

    public function updatePriority(UpdateSupportTicketPriorityRequest $request, SupportTicket $support_ticket): JsonResponse
    {
        $this->authorize('managePriority', $support_ticket);

        $newPriority = $request->input('priority');
        $oldPriority = $support_ticket->priority;
        $support_ticket->update(['priority' => $newPriority]);

        $this->createLog(
            $support_ticket,
            SupportTicketLog::LOG_TYPE_PRIORITY_CHANGE,
            "Priority changed from {$oldPriority} to {$newPriority}.",
            null,
            true,
        );

        $support_ticket->load(['user', 'logs.actor'])->loadAudit();

        if ($support_ticket->user) {
            app(NotificationService::class)->notify($support_ticket->user, [
                'title' => 'Support ticket priority changed',
                'body' => "Ticket {$support_ticket->ticket_number} priority changed from {$oldPriority} to {$newPriority}.",
                'target_type' => 'tickets',
                'target_id' => $support_ticket->id,
            ]);
        }

        return SupportTicketResource::make($support_ticket)->response($request);
    }

    public function storeLog(StoreSupportTicketLogRequest $request, SupportTicket $support_ticket): JsonResponse
    {
        $this->authorize('addLog', $support_ticket);

        $validated = $request->validated();
        $logType = $validated['log_type'] ?? SupportTicketLog::LOG_TYPE_COMMENT;
        $message = $validated['message'] ?? '';
        $attachments = $validated['attachments'] ?? null;

        if (empty($message) && empty($attachments)) {
            return response()->json(['message' => 'Either message or attachments are required.'], 422);
        }

        $this->createLog($support_ticket, $logType, $message, $attachments, true);

        $support_ticket->load(['user', 'logs.actor'])->loadAudit();

        // Only notify the ticket owner for public comments.
        if (
            $logType === SupportTicketLog::LOG_TYPE_COMMENT
            && $support_ticket->user
        ) {
            app(NotificationService::class)->notify($support_ticket->user, [
                'title' => 'New reply on support ticket',
                'body' => "You received a new message on ticket {$support_ticket->ticket_number}.",
                'target_type' => 'tickets',
                'target_id' => $support_ticket->id,
            ]);
        }

        return SupportTicketResource::make($support_ticket)->response($request);
    }

    private function createLog(SupportTicket $ticket, string $logType, ?string $message, ?array $attachments, bool $asAdmin): void
    {
        $admin = $asAdmin ? auth('sanctum')->user() : null;
        $actorType = $admin instanceof Admin ? Admin::class : null;
        $actorId = $actorType ? $admin->id : null;

        SupportTicketLog::create([
            'ticket_id' => $ticket->id,
            'actor_type' => $actorType ?? '',
            'actor_id' => $actorId,
            'message' => $message,
            'log_type' => $logType,
            'attachments' => $attachments,
        ]);
    }
}
