<?php

namespace Database\Factories\User;

use App\Models\User\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('050#######'),
            'password' => Hash::make('password'),
            'is_active' => true,
            'admin_type' => Admin::TYPE_ADMIN,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
