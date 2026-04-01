<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $perPage = min(max((int) $request->integer('per_page', 15), 1), 100);

        $devices = $user->devices()
            ->latest('last_used_at')
            ->latest('id')
            ->paginate($perPage);

        return response()->json($devices);
    }
}
