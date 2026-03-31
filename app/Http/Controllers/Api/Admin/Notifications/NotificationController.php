<?php

namespace App\Http\Controllers\Api\Admin\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\SendNotificationRequest;
use App\Http\Resources\Notifications\NotificationResource;
use App\Models\Notifications\Notification;
use App\Models\User\Admin;
use App\Models\User\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unreadCount(Request $request): JsonResponse
    {
        $admin = $request->user();
        $this->authorize('viewAny', Notification::class);

        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $count = Notification::query()
            ->where('notifiable_type', Admin::class)
            ->where('notifiable_id', $admin->id)
            ->unread()
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();
        $this->authorize('viewAny', Notification::class);

        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $query = Notification::query()
            ->where('notifiable_type', Admin::class)
            ->where('notifiable_id', $admin->id)
            ->latest();

        if ($request->boolean('unread')) {
            $query->unread();
        }

        $notifications = $query->paginate(
            perPage: min((int) $request->query('per_page', 15), 100),
        );

        $notifications->through(fn (Notification $n) => NotificationResource::make($n)->resolve($request));

        return response()->json($notifications);
    }

    public function show(Request $request, Notification $notification): JsonResponse
    {
        $admin = $request->user();

        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->authorize('view', $notification);

        return NotificationResource::make($notification)->response($request);
    }

    public function markRead(Request $request, Notification $notification): JsonResponse
    {
        $admin = $request->user();

        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->authorize('update', $notification);

        if ($notification->read_at === null) {
            $notification->forceFill(['read_at' => now()])->save();
        }

        return response()->json(['message' => 'Marked as read.']);
    }

    public function readAll(Request $request): JsonResponse
    {
        $admin = $request->user();
        $this->authorize('viewAny', Notification::class);

        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        Notification::query()
            ->where('notifiable_type', Admin::class)
            ->where('notifiable_id', $admin->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function send(SendNotificationRequest $request, NotificationService $service): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('notifications', 'public');
        }

        $data = null;
        if (! empty($validated['data_json'])) {
            $decoded = json_decode((string) $validated['data_json'], true);
            $data = is_array($decoded) ? $decoded : null;
        }

        $payload = [
            'title' => $validated['title'],
            'body' => $validated['body'],
            'image' => $imagePath,
            'target_type' => $validated['target_type'] ?? null,
            'target_id' => $validated['target_id'] ?? null,
            'data' => $data,
        ];

        $recipientType = $validated['recipient_type'];
        $recipientIds = $validated['recipient_ids'] ?? [];

        if (in_array($recipientType, ['user', 'admin'], true) && count($recipientIds) === 0) {
            return response()->json([
                'message' => 'recipient_ids is required when recipient_type is user or admin.',
            ], 422);
        }

        $notifiables = match ($recipientType) {
            'admin' => Admin::query()->whereIn('id', $recipientIds)->get(),
            default => User::query()->whereIn('id', $recipientIds)->get(),
        };

        $ids = $service->notifyMany($notifiables, $payload);

        return response()->json([
            'message' => 'Sent.',
            'count' => count($ids),
            'notification_ids' => $ids,
        ], 201);
    }
}
