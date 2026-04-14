<?php

namespace App\Http\Traits;

use App\Models\Post\Post;
use App\Models\Post\PostComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait InteractsWithPostRecentComments
{
    /**
     * @param  Collection<int, Model>  $items
     * @return Collection<int, Post>
     */
    protected function postsFromPaginatorItems(Collection $items): Collection
    {
        return $items->map(function (Model $model): Post {
            if (! $model instanceof Post) {
                throw new \UnexpectedValueException('Paginator items must be post models.');
            }

            return $model;
        });
    }

    /**
     * @param  Collection<int, Post>  $posts
     */
    protected function attachRecentCommentsToPosts(Collection $posts, int $limit): void
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

    protected function loadRecentCommentsForPost(Post $post, int $limit): void
    {
        $recent = $post->comments()
            ->with('user')
            ->limit($limit)
            ->get();
        $post->setRelation('recentComments', $recent);
    }
}
