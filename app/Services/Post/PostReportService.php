<?php

namespace App\Services\Post;

use App\Models\Post\Post;
use App\Models\Post\PostReport;
use App\Models\SupportTicket\SupportTicket;
use App\Models\SupportTicket\SupportTicketLog;
use App\Models\User\Admin;
use App\Models\User\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\DB;

class PostReportService
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    /**
     * @return array{ticket: SupportTicket, report: PostReport}
     */
    public function report(Post $post, User $reporter, string $reason, ?string $details): array
    {
        if (PostReport::query()->where('post_id', $post->id)->where('reporter_id', $reporter->id)->exists()) {
            abort(422, 'You have already reported this post.');
        }

        $details = $details !== null ? trim($details) : null;
        $message = "[Post report] Post #{$post->id}\nReason: {$reason}";
        if ($details !== null && $details !== '') {
            $message .= "\nDetails: {$details}";
        }

        return DB::transaction(function () use ($post, $reporter, $reason, $details, $message) {
            $ticket = SupportTicket::create([
                'user_id' => $reporter->id,
                'post_id' => $post->id,
                'visitor_name' => null,
                'visitor_phone' => null,
                'visitor_email' => null,
                'message' => $message,
                'status' => SupportTicket::STATUS_OPEN,
                'priority' => SupportTicket::PRIORITY_HIGH,
                'is_active' => true,
                'attachments' => [],
            ]);

            SupportTicketLog::create([
                'ticket_id' => $ticket->id,
                'actor_type' => User::class,
                'actor_id' => $reporter->id,
                'message' => $message,
                'log_type' => SupportTicketLog::LOG_TYPE_COMMENT,
                'attachments' => null,
            ]);

            $report = PostReport::create([
                'post_id' => $post->id,
                'reporter_id' => $reporter->id,
                'support_ticket_id' => $ticket->id,
                'reason' => $reason,
                'details' => $details,
            ]);

            $ticket->refresh();

            $admins = Admin::query()
                ->where('is_active', true)
                ->get()
                ->filter(fn (Admin $admin) => $admin->can('view_support_tickets'))
                ->values();

            $this->notificationService->notifyMany($admins, [
                'title' => 'New post report',
                'body' => "Ticket {$ticket->ticket_number} — reported post #{$post->id}.",
                'target_type' => 'tickets',
                'target_id' => $ticket->id,
            ]);

            return ['ticket' => $ticket, 'report' => $report];
        });
    }
}
