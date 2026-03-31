<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\StoreDeviceFromRequest;
use App\Http\Requests\User\ChangeUserPasswordRequest;
use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $tokenName = sprintf(
            'user-%s',
            $request->header('x-platform', 'api'),
        );

        $token = $user->createToken($tokenName)->plainTextToken;

        StoreDeviceFromRequest::forUser($request, $user);

        return response()->json([
            'token' => $token,
            'user' => UserResource::make($user)->resolve($request),
        ], 201);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['email' => [__('auth.failed')]],
            ], 401);
        }

        if (! $user->is_active || $user->deleted_at !== null) {
            return response()->json([
                'message' => 'Account is disabled or deleted.',
                'errors' => ['email' => ['Account is disabled or deleted.']],
            ], 401);
        }

        $tokenName = sprintf(
            'user-%s',
            $request->header('x-platform', 'api'),
        );

        $token = $user->createToken($tokenName)->plainTextToken;

        StoreDeviceFromRequest::forUser($request, $user);

        return response()->json([
            'token' => $token,
            'user' => UserResource::make($user)->resolve($request),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return UserResource::make($user)->response($request);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validated();

        // Handle image upload separately (multipart/form-data)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('users', 'public');
            $user->image = $path;
            unset($validated['image']);
        }

        $user->fill($validated);
        $user->save();

        return UserResource::make($user->fresh())->response($request);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Soft delete the user and revoke all tokens.
        $user->tokens()->delete();
        $user->delete();

        return response()->json(null, 204);
    }

    public function changePassword(ChangeUserPasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validated();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['current_password' => [__('auth.password')]],
            ], 422);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        /** @phpstan-ignore-next-line */
        if ($user && $user->currentAccessToken()) {
            /** @phpstan-ignore-next-line */
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out.']);
    }
}
