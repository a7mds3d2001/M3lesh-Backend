<?php

namespace Database\Factories\Post;

use App\Models\Post\Post;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'body' => fake()->paragraphs(2, true),
            'is_active' => true,
            'likes_count' => 0,
            'comments_count' => 0,
        ];
    }
}
