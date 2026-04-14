<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\User\PublicUserProfileResource;
use App\Http\Traits\ApiPaginationFilters;
use App\Http\Traits\InteractsWithPostRecentComments;
use App\Models\Post\Post;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    use ApiPaginationFilters;
    use InteractsWithPostRecentComments;

    private const SORT_ALLOWED = ['id', 'created_at', 'updated_at'];

    public function show(Request $request, User $user): JsonResponse
    {
        if (! $user->is_active) {
            abort(404);
        }

        $authUser = $request->user('sanctum');

        $query = Post::query()
            ->feed()
            ->where('user_id', $user->id)
            ->with(['user']);

        if ($authUser instanceof User) {
            $query->withCount(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $authUser->id)]);
        }

        $query = $this->applySort($query, $request, self::SORT_ALLOWED);
        if (! $request->has('sort_by')) {
            $query->orderByDesc('created_at');
        }

        $paginator = $query->paginate($this->getPerPage($request));
        $this->attachRecentCommentsToPosts($this->postsFromPaginatorItems($paginator->getCollection()), 2);
        $paginator->through(fn (Post $post) => PostResource::make($post)->resolve($request));

        return response()->json([
            'user' => PublicUserProfileResource::make($user)->resolve($request),
            'posts' => $paginator,
        ]);
    }
}
