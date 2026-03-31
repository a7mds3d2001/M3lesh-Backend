<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\PermissionResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\User\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['key', 'guard_name', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);

        $query = Permission::query()
            ->where('guard_name', 'admin')
            ->withCount('roles');

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where('key', 'like', "%{$term}%");
        }

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderBy('key');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(
            fn (Permission $perm) => PermissionResource::make($perm)->resolve(request()),
        );

        return response()->json($paginator);
    }

    public function show(Request $request, Permission $permission): JsonResponse
    {
        $this->authorize('view', $permission);

        if ($permission->guard_name !== 'admin') {
            abort(404);
        }

        $permission->loadCount('roles');

        return PermissionResource::make($permission)->response($request);
    }
}
