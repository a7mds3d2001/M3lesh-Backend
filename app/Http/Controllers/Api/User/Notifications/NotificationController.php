<?php

namespace App\Http\Controllers\Api\User\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\NotificationResource;
use App\Models\Notifications\Notification;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorize('viewAny', Notification::class);

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $count = Notification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->unread()
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorize('viewAny', Notification::class);

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $query = Notification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
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
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->authorize('view', $notification);

        return NotificationResource::make($notification)->response($request);
    }

    public function markRead(Request $request, Notification $notification): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
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
        $user = $request->user();
        $this->authorize('viewAny', Notification::class);

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        Notification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }
}
