<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['name', 'phone', 'email', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->withAudit()
            ->isActive($this->parseBool($request, 'is_active'))
            ->search($request->input('search') ?? $request->input('q'));

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderBy('updated_at', 'desc');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (User $user) => UserResource::make($user)->resolve(request()));

        return response()->json($paginator);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->loadAudit();

        return UserResource::make($user)->response($request)->setStatusCode(201);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        $this->authorize('view', $user);
        $user->loadAudit();

        return UserResource::make($user)->response($request);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return UserResource::make($user->fresh()->loadAudit())->response($request);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);
        $user->delete();

        return response()->json(null, 204);
    }
}
