<?php

namespace App\Http\Controllers\Api\User\Post;

use App\Enums\Post\PostReportReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\ReportPostRequest;
use App\Http\Requests\Post\StorePostCommentRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostCommentResource;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\SupportTicket\SupportTicketResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Models\Post\Post;
use App\Models\Post\PostComment;
use App\Models\Post\PostCommentPreset;
use App\Models\Post\PostLike;
use App\Models\User\User;
use App\Services\Notifications\NotificationService;
use App\Services\Post\PostReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use ApiPaginationFilters;

    private const SORT_ALLOWED = ['id', 'created_at', 'updated_at'];

    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        $query = Post::query()
            ->feed()
            ->with(['user']);

        if ($user instanceof User) {
            $query->withCount(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $user->id)]);
        }

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderByDesc('created_at');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $this->attachRecentCommentsToPosts($paginator->getCollection(), 2);
        $paginator->through(fn (Post $post) => PostResource::make($post)->resolve($request));

        return response()->json($paginator);
    }

    public function mine(Request $request): JsonResponse
    {
        $query = Post::query()
            ->where('user_id', $request->user()->id)
            ->with(['user'])
            ->withCount(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $request->user()->id)]);

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderByDesc('created_at');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $this->attachRecentCommentsToPosts($paginator->getCollection(), 2);
        $paginator->through(fn (Post $post) => PostResource::make($post)->resolve($request));

        return response()->json($paginator);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = Post::create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
            'is_active' => true,
        ]);
        $post->load('user');
        $post->loadCount(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $request->user()->id)]);
        $this->loadRecentCommentsForPost($post, 10);

        return PostResource::make($post)->response($request)->setStatusCode(201);
    }

    public function show(Request $request, Post $post): JsonResponse
    {
        Gate::forUser($request->user('sanctum'))->authorize('view', $post);

        $user = $request->user('sanctum');
        $post->load('user');
        if ($user instanceof User) {
            $post->loadCount(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $user->id)]);
        }
        $this->loadRecentCommentsForPost($post, 10);

        return PostResource::make($post)->response($request);
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $post->update($request->validated());
        $post->load('user');
        $post->loadCount(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $request->user()->id)]);
        $this->loadRecentCommentsForPost($post, 10);

        return PostResource::make($post)->response($request);
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Post deleted.']);
    }

    public function toggleLike(Request $request, Post $post): JsonResponse
    {
        $this->authorize('like', $post);

        $like = PostLike::query()
            ->where('post_id', $post->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');
        } else {
            PostLike::create([
                'post_id' => $post->id,
                'user_id' => $request->user()->id,
            ]);
            $post->increment('likes_count');

            $actor = $request->user();
            if ($post->user_id !== $actor->id) {
                $owner = User::query()->find($post->user_id);
                if ($owner !== null) {
                    $this->notificationService->notify($owner, [
                        'title' => __('notifications.post_liked_title'),
                        'body' => __('notifications.post_liked_body', ['name' => $actor->name]),
                        'target_type' => 'posts',
                        'target_id' => $post->id,
                    ]);
                }
            }
        }

        $post->refresh();

        return response()->json([
            'liked' => $like === null,
            'likes_count' => $post->likes_count,
        ]);
    }

    public function commentsIndex(Request $request, Post $post): JsonResponse
    {
        Gate::forUser($request->user('sanctum'))->authorize('view', $post);

        $query = PostComment::query()
            ->where('post_id', $post->id)
            ->with('user')
            ->orderByDesc('created_at');

        $paginator = $query->paginate($this->getPerPage($request));
        $paginator->through(fn (PostComment $c) => PostCommentResource::make($c)->resolve($request));

        return response()->json($paginator);
    }

    public function commentsStore(StorePostCommentRequest $request, Post $post): JsonResponse
    {
        $this->authorize('comment', $post);

        $validated = $request->validated();
        $presetId = $validated['comment_preset_id'] ?? null;
        $body = isset($validated['body']) ? trim((string) $validated['body']) : '';
        $body = $body === '' ? null : $body;

        $snapshot = null;
        if ($presetId !== null) {
            /** @var PostCommentPreset $preset */
            $preset = PostCommentPreset::query()->findOrFail($presetId);
            $snapshot = $preset->displayText();
        }

        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'post_comment_preset_id' => $presetId,
            'body' => $body,
            'preset_text_snapshot' => $snapshot,
        ]);
        $post->increment('comments_count');
        $comment->load('user');

        $actor = $request->user();
        if ($post->user_id !== $actor->id) {
            $owner = User::query()->find($post->user_id);
            if ($owner !== null) {
                $preview = Str::limit($comment->displayBody(), 120);
                $this->notificationService->notify($owner, [
                    'title' => __('notifications.post_commented_title'),
                    'body' => __('notifications.post_commented_body', [
                        'name' => $actor->name,
                        'preview' => $preview,
                    ]),
                    'target_type' => 'posts',
                    'target_id' => $post->id,
                ]);
            }
        }

        return PostCommentResource::make($comment)->response($request)->setStatusCode(201);
    }

    public function report(ReportPostRequest $request, Post $post, PostReportService $postReportService): JsonResponse
    {
        $this->authorize('report', $post);

        $validated = $request->validated();
        $result = $postReportService->report(
            $post,
            $request->user(),
            PostReportReason::from($validated['reason']),
            $validated['details'] ?? null,
        );

        $ticket = $result['ticket']->load(['user', 'post', 'postReport', 'logs.actor', 'creator', 'updater']);

        return SupportTicketResource::make($ticket)->response($request)->setStatusCode(201);
    }

    /**
     * @param  Collection<int, Post>  $posts
     */
    private function attachRecentCommentsToPosts(Collection $posts, int $limit): void
    {
        if ($posts->isEmpty()) {
            return;
        }

        $postIds = $posts->pluck('id')->all();
        $grouped = PostComment::query()
            ->with('user')
            ->whereIn('post_id', $postIds)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('post_id');

        foreach ($posts as $post) {
            $recent = $grouped->get($post->id, collect())->take($limit)->values();
            $post->setRelation('recentComments', $recent);
        }
    }

    private function loadRecentCommentsForPost(Post $post, int $limit): void
    {
        $recent = $post->comments()
            ->with('user')
            ->limit($limit)
            ->get();
        $post->setRelation('recentComments', $recent);
    }
}
