<?php

namespace Database\Factories\User;

use App\Models\User\Avatar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Avatar>
 */
class AvatarFactory extends Factory
{
    protected $model = Avatar::class;

    public function definition(): array
    {
        return [
            'image' => 'avatars/'.fake()->uuid().'.png',
            'sort_order' => (int) (Avatar::query()->max('sort_order') ?? 0) + 1,
        ];
    }
}
