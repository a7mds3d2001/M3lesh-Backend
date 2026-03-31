<?php

namespace App\Http\Controllers\Api\Admin\Notifications;

use App\Enums\Notifications\NotificationTopic;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\SendBroadcastNotificationRequest;
use App\Http\Resources\Notifications\NotificationBroadcastResource;
use App\Models\Notifications\NotificationBroadcast;
use App\Models\User\Admin;
use App\Services\Notifications\BroadcastNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationBroadcastController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();

        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->authorize('viewAny', NotificationBroadcast::class);

        $query = NotificationBroadcast::query()->latest();

        $broadcasts = $query->paginate(
            perPage: min((int) $request->query('per_page', 15), 100),
        );

        $broadcasts->through(fn (NotificationBroadcast $b) => NotificationBroadcastResource::make($b)->resolve($request));

        return response()->json($broadcasts);
    }

    public function show(Request $request, NotificationBroadcast $broadcast): JsonResponse
    {
        $admin = $request->user();

        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->authorize('view', $broadcast);

        return NotificationBroadcastResource::make($broadcast)->response($request);
    }

    public function send(SendBroadcastNotificationRequest $request, BroadcastNotificationService $service): JsonResponse
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

        $topic = $validated['topic'] instanceof NotificationTopic
            ? $validated['topic']
            : NotificationTopic::from((string) $validated['topic']);

        $admin = $request->user();
        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $broadcast = $service->broadcast($topic, $payload, $admin);

        return response()->json([
            'message' => 'Sent.',
            'broadcast_id' => $broadcast->id,
        ], 201);
    }
}
