<?php

namespace Tests\Unit\User;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_active_user_with_hashed_password(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($user->is_active);
        $this->assertNotEmpty($user->password);
        $this->assertNotEquals('password', $user->password);
    }

    public function test_is_active_scope_filters_by_flag(): void
    {
        $active = User::factory()->create(['is_active' => true]);
        User::factory()->inactive()->create();

        $results = User::isActive(true)->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($active));
    }
}
