<?php

namespace App\Http\Controllers\Api\Admin\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\AdminStorePostRequest;
use App\Http\Requests\Post\AdminUpdatePostRequest;
use App\Http\Resources\Post\AdminPostLikeResource;
use App\Http\Resources\Post\AdminPostResource;
use App\Http\Resources\Post\PostCommentResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\Post\Post;
use App\Models\Post\PostComment;
use App\Models\Post\PostLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['id', 'created_at', 'updated_at', 'likes_count', 'comments_count', 'is_active'];

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Post::class);

        $query = Post::query()->with(['user'])->withAudit();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

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
            $query->where('body', 'like', '%'.$search.'%');
        }

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderByDesc('updated_at');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (Post $post) => AdminPostResource::make($post)->resolve($request));

        return response()->json($paginator);
    }

    public function store(AdminStorePostRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $validated = $request->validated();
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['likes_count'] = 0;
        $validated['comments_count'] = 0;

        $post = Post::create($validated);
        $post->load(['user'])->loadAudit();

        return AdminPostResource::make($post)->response($request)->setStatusCode(201);
    }

    public function show(Request $request, Post $admin_post): JsonResponse
    {
        $this->authorize('view', $admin_post);
        $admin_post->load(['user'])->loadAudit();

        return AdminPostResource::make($admin_post)->response($request);
    }

    public function update(AdminUpdatePostRequest $request, Post $admin_post): JsonResponse
    {
        $this->authorize('update', $admin_post);

        $admin_post->update($request->validated());

        return AdminPostResource::make($admin_post->fresh()->load(['user'])->loadAudit())->response($request);
    }

    public function destroy(Post $admin_post): JsonResponse
    {
        $this->authorize('delete', $admin_post);
        $admin_post->delete();

        return response()->json(null, 204);
    }

    public function restore(Post $admin_post): JsonResponse
    {
        $this->authorize('restore', $admin_post);
        $admin_post->restore();

        return AdminPostResource::make($admin_post->fresh()->load(['user'])->loadAudit())->response(request());
    }

    public function forceDestroy(Post $admin_post): JsonResponse
    {
        $this->authorize('forceDelete', $admin_post);
        $admin_post->forceDelete();

        return response()->json(null, 204);
    }

    public function commentsIndex(Request $request, Post $admin_post): JsonResponse
    {
        $this->authorize('view', $admin_post);

        $query = PostComment::query()
            ->where('post_id', $admin_post->id)
            ->with('user')
            ->orderByDesc('created_at');

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (PostComment $c) => PostCommentResource::make($c)->resolve($request));

        return response()->json($paginator);
    }

    public function destroyComment(Post $admin_post, PostComment $comment): JsonResponse
    {
        $this->authorize('moderateComments', $admin_post);

        if ($comment->post_id !== $admin_post->id) {
            abort(404);
        }

        $admin_post->decrement('comments_count');
        $comment->delete();

        return response()->json(null, 204);
    }

    public function likesIndex(Request $request, Post $admin_post): JsonResponse
    {
        $this->authorize('view', $admin_post);

        $query = PostLike::query()
            ->where('post_id', $admin_post->id)
            ->with('user')
            ->orderByDesc('created_at');

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (PostLike $like) => AdminPostLikeResource::make($like)->resolve($request));

        return response()->json($paginator);
    }

    public function destroyLike(Post $admin_post, PostLike $like): JsonResponse
    {
        $this->authorize('moderateLikes', $admin_post);

        if ($like->post_id !== $admin_post->id) {
            abort(404);
        }

        $admin_post->decrement('likes_count');
        $like->delete();

        return response()->json(null, 204);
    }
}
