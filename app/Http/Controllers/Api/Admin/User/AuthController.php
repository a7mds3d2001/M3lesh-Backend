<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Device\StoreDeviceFromRequest;
use App\Http\Resources\User\AdminResource;
use App\Models\User\Admin;
use App\Models\User\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Admin login: validates credentials and returns a Sanctum token.
     * Returns 401 (not a validation error) when credentials are wrong or
     * the account is disabled, to avoid leaking user existence information.
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $admin = Admin::where('email', $request->validated('email'))->first();

        if (! $admin || ! Hash::check($request->validated('password'), $admin->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['email' => [__('auth.failed')]],
            ], 401);
        }

        if (! $admin->is_active || $admin->deleted_at !== null) {
            return response()->json([
                'message' => 'Account is disabled or deleted.',
                'errors' => ['email' => ['Account is disabled or deleted.']],
            ], 401);
        }

        $tokenName = sprintf(
            'admin-%s',
            $request->header('x-platform', 'api'),
        );

        $token = $admin->createToken($tokenName)->plainTextToken;

        // Optionally store/update the admin device for multi-device tracking and notifications.
        StoreDeviceFromRequest::forAdmin($request, $admin);

        // Load roles for AdminResource (constrained select to minimise payload).
        $admin->load(['roles' => fn ($q) => $q->select('id', 'name_en', 'name_ar', 'guard_name')]);

        return response()->json([
            'token' => $token,
            'admin' => AdminResource::make($admin)->resolve($request),
        ]);
    }

    /**
     * Return the currently authenticated admin with roles and permissions.
     */
    public function me(Request $request): JsonResponse
    {
        $admin = $request->user();

        if (! $admin instanceof Admin) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $admin->load(['roles' => fn ($q) => $q->select('id', 'name_en', 'name_ar', 'guard_name')]);

        return AdminResource::make($admin)->response($request);
    }

    /**
     * Revoke the current API token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $admin = $request->user();

        if ($admin instanceof Admin) {
            $admin->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out.']);
    }
}
