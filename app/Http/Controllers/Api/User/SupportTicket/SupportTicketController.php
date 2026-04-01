<?php

namespace App\Http\Controllers\Api\User\SupportTicket;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportTicket\CreateSupportTicketRequest;
use App\Http\Requests\SupportTicket\StoreSupportTicketLogRequest;
use App\Http\Resources\SupportTicket\SupportTicketResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\SupportTicket\SupportTicket;
use App\Models\SupportTicket\SupportTicketLog;
use App\Models\User\Admin;
use App\Models\User\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class SupportTicketController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['id', 'status', 'priority', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $query = SupportTicket::query()
            ->where('user_id', $request->user()->id)
            ->with(['user', 'creator', 'updater']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('search')) {
            $term = '%'.$request->input('search').'%';
            $query->where('message', 'like', $term);
        }

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderBy('updated_at', 'desc');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (SupportTicket $ticket) => SupportTicketResource::make($ticket)->resolve($request));

        return response()->json($paginator);
    }

    public function store(CreateSupportTicketRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user('sanctum');

        if ($user) {
            $validated['user_id'] = $user->id;
            $validated['visitor_name'] = null;
            $validated['visitor_phone'] = null;
            $validated['visitor_email'] = null;
        } else {
            $validated['user_id'] = null;
        }

        $validated['status'] = SupportTicket::STATUS_OPEN;
        $validated['priority'] = SupportTicket::PRIORITY_NORMAL;
        $validated['is_active'] = true;
        $validated['attachments'] = $this->normalizeAttachments($validated['attachments'] ?? []);

        $ticket = SupportTicket::create($validated);
        $this->createLog($ticket, SupportTicketLog::LOG_TYPE_COMMENT, $validated['message'], $validated['attachments'], false, $user);

        // Notify admins that a new ticket was created by a user/visitor.
        $ticket->refresh();
        $admins = Admin::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (Admin $admin) => $admin->can('view_support_tickets'))
            ->values();

        app(NotificationService::class)->notifyMany($admins, [
            'title' => 'New support ticket',
            'body' => "Ticket {$ticket->ticket_number} was created.",
            'target_type' => 'tickets',
            'target_id' => $ticket->id,
        ]);

        $ticket->load($user ? ['user', 'logs.actor', 'creator', 'updater'] : ['logs']);

        return SupportTicketResource::make($ticket)->response($request)->setStatusCode(201);
    }

    public function show(Request $request, SupportTicket $support_ticket): JsonResponse
    {
        $this->ensureOwn($support_ticket, $request);
        $support_ticket->load(['user', 'logs.actor', 'creator', 'updater']);

        return SupportTicketResource::make($support_ticket)->response($request);
    }

    public function storeLog(StoreSupportTicketLogRequest $request, SupportTicket $support_ticket): JsonResponse
    {
        $this->ensureOwn($support_ticket, $request);

        if ($support_ticket->isClosed()) {
            return response()->json(['message' => 'Cannot add logs to a closed ticket.'], 422);
        }

        $validated = $request->validated();
        $message = $validated['message'] ?? '';
        $attachments = $this->normalizeAttachments($validated['attachments'] ?? []);

        if (empty($message) && empty($attachments)) {
            return response()->json(['message' => 'Either message or attachments are required.'], 422);
        }

        $this->createLog($support_ticket, SupportTicketLog::LOG_TYPE_COMMENT, $message, $attachments ?: null, false, $request->user());

        // Notify admins that a user added a new comment/log to this ticket.
        $support_ticket->refresh();
        $admins = Admin::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (Admin $admin) => $admin->can('view_support_tickets'))
            ->values();

        app(NotificationService::class)->notifyMany($admins, [
            'title' => 'Support ticket updated',
            'body' => "Ticket {$support_ticket->ticket_number} received a new message.",
            'target_type' => 'tickets',
            'target_id' => $support_ticket->id,
        ]);

        $support_ticket->load(['user', 'logs.actor', 'creator', 'updater']);

        return SupportTicketResource::make($support_ticket)->response($request);
    }

    private function normalizeAttachments(array $attachments): array
    {
        $paths = [];
        foreach ($attachments as $item) {
            if ($item instanceof UploadedFile) {
                $paths[] = $item->store('support-tickets', 'public');
            } else {
                $paths[] = $item;
            }
        }

        return $paths;
    }

    private function ensureOwn(SupportTicket $ticket, Request $request): void
    {
        if ($ticket->user_id !== $request->user()->id) {
            abort(403, 'You do not have access to this ticket.');
        }
    }

    private function createLog(SupportTicket $ticket, string $logType, ?string $message, ?array $attachments, bool $asAdmin, ?User $user = null): void
    {
        if ($asAdmin) {
            $admin = auth('sanctum')->user();
            $actorType = $admin instanceof Admin ? Admin::class : '';
            $actorId = $actorType ? $admin->id : null;
        } else {
            $actorType = $user ? User::class : '';
            $actorId = $user?->id;
        }

        SupportTicketLog::create([
            'ticket_id' => $ticket->id,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'message' => $message,
            'log_type' => $logType,
            'attachments' => $attachments,
        ]);
    }
}
