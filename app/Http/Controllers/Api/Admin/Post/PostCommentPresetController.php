<?php

namespace App\Http\Controllers\Api\Admin\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\AdminStorePostCommentPresetRequest;
use App\Http\Requests\Post\AdminUpdatePostCommentPresetRequest;
use App\Http\Resources\Post\AdminPostCommentPresetResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\Post\PostCommentPreset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostCommentPresetController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['id', 'text', 'is_active', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PostCommentPreset::class);

        $query = PostCommentPreset::query()->withAudit();

        if ($request->has('is_active')) {
            $active = $this->parseBool($request, 'is_active');
            if ($active !== null) {
                $query->where('is_active', $active);
            }
        }

        if ($this->parseBool($request, 'with_trashed') === true) {
            $query->withTrashed();
        } elseif ($this->parseBool($request, 'only_trashed') === true) {
            $query->onlyTrashed();
        }

        $search = $request->input('search') ?? $request->input('q');
        if (is_string($search) && $search !== '') {
            $query->where('text', 'like', '%'.$search.'%');
        }

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->ordered();
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (PostCommentPreset $p) => AdminPostCommentPresetResource::make($p)->resolve($request));

        return response()->json($paginator);
    }

    public function store(AdminStorePostCommentPresetRequest $request): JsonResponse
    {
        $this->authorize('create', PostCommentPreset::class);

        $validated = $request->validated();
        $validated['is_active'] = $validated['is_active'] ?? true;

        $preset = PostCommentPreset::create($validated);
        $preset->loadAudit();

        return AdminPostCommentPresetResource::make($preset)->response($request)->setStatusCode(201);
    }

    public function show(Request $request, PostCommentPreset $comment_preset): JsonResponse
    {
        $this->authorize('view', $comment_preset);
        $comment_preset->loadAudit();

        return AdminPostCommentPresetResource::make($comment_preset)->response($request);
    }

    public function update(AdminUpdatePostCommentPresetRequest $request, PostCommentPreset $comment_preset): JsonResponse
    {
        $this->authorize('update', $comment_preset);

        $comment_preset->update($request->validated());

        return AdminPostCommentPresetResource::make($comment_preset->fresh()->loadAudit())->response($request);
    }

    public function destroy(PostCommentPreset $comment_preset): JsonResponse
    {
        $this->authorize('delete', $comment_preset);
        $comment_preset->delete();

        return response()->json(null, 204);
    }

    public function restore(PostCommentPreset $comment_preset): JsonResponse
    {
        $this->authorize('restore', $comment_preset);
        $comment_preset->restore();

        return AdminPostCommentPresetResource::make($comment_preset->fresh()->loadAudit())->response(request());
    }

    public function forceDestroy(PostCommentPreset $comment_preset): JsonResponse
    {
        $this->authorize('forceDelete', $comment_preset);
        $comment_preset->forceDelete();

        return response()->json(null, 204);
    }
}
