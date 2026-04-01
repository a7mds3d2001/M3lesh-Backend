<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var Admin|null $admin */
        $admin = $request->user('admin');

        if (! $admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $perPage = min(max((int) $request->integer('per_page', 15), 1), 100);

        $devices = $admin->devices()
            ->latest('last_used_at')
            ->latest('id')
            ->paginate($perPage);

        return response()->json($devices);
    }
}
