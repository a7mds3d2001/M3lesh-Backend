<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Resources\User\RoleResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\User\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['name_en', 'name_ar', 'guard_name', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $query = Role::query()
            ->where('guard_name', 'admin')
            ->with(['creator', 'updater'])
            ->withCount('permissions');

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('name_en', 'like', "%{$term}%")
                    ->orWhere('name_ar', 'like', "%{$term}%");
            });
        }

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderBy('name_en');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (Role $role) => RoleResource::make($role)->resolve(request()));

        return response()->json($paginator);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $this->authorize('create', Role::class);

        $validated = $request->validated();

        $role = Role::create([
            'name_en' => $validated['name_en'],
            'name_ar' => $validated['name_ar'] ?? null,
            'guard_name' => 'admin',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return RoleResource::make($role->load(['permissions', 'creator', 'updater']))->response($request)->setStatusCode(201);
    }

    public function show(Request $request, Role $role): JsonResponse
    {
        $this->authorize('view', $role);

        if ($role->guard_name !== 'admin') {
            abort(404);
        }

        return RoleResource::make($role->load(['permissions', 'creator', 'updater']))->response($request);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $this->authorize('update', $role);

        if ($role->guard_name !== 'admin') {
            abort(404);
        }

        $validated = $request->validated();

        if (isset($validated['name_en'])) {
            $role->name_en = $validated['name_en'];
        }
        if (array_key_exists('name_ar', $validated)) {
            $role->name_ar = $validated['name_ar'];
        }
        $role->save();

        if (array_key_exists('permissions', $validated)) {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        return RoleResource::make($role->fresh(['permissions', 'creator', 'updater']))->response($request);
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);

        if ($role->guard_name !== 'admin') {
            abort(404);
        }

        $role->forceDelete();

        return response()->json(null, 204);
    }
}
