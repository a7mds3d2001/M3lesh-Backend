<?php

namespace Tests\Unit\Admin;

use App\Models\User\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_active_admin_by_default(): void
    {
        $admin = Admin::factory()->create();

        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertTrue($admin->is_active);
    }

    public function test_is_super_admin_helper_checks_admin_type(): void
    {
        $normal = Admin::factory()->create(['admin_type' => Admin::TYPE_ADMIN]);
        $super = Admin::factory()->create(['admin_type' => Admin::TYPE_SUPER_ADMIN]);

        $this->assertFalse($normal->isSuperAdmin());
        $this->assertTrue($super->isSuperAdmin());
    }
}
