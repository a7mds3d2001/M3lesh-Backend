<?php

namespace Tests\Feature\Api\Admin;

use App\Models\User\Admin;
use App\Models\User\Permission;
use App\Models\User\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature tests for Admin Roles and Permissions API endpoints.
 *
 * Covers:
 *   GET|POST|PUT|DELETE /api/admin/roles
 *   GET /api/admin/permissions
 */
class RolesPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'phone' => '0500000000',
            'is_active' => true,
        ]);

        $this->admin->assignRole(Role::where('name_en', 'Super Admin')->first());
        $this->token = $this->admin->createToken('test')->plainTextToken;
    }

    // -------------------------------------------------------------------------
    // Roles — index
    // -------------------------------------------------------------------------

    public function test_roles_index_requires_authentication(): void
    {
        $this->getJson('/api/admin/roles')->assertUnauthorized();
    }

    public function test_roles_index_returns_paginated_admin_roles_only(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/admin/roles');

        $response->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'total']);

        // All returned roles must belong to the admin guard.
        foreach ($response->json('data') as $role) {
            $this->assertEquals('admin', $role['guard_name']);
        }
    }

    public function test_roles_index_returns_permissions_count(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/admin/roles');

        $response->assertOk();
        $this->assertArrayHasKey('permissions_count', $response->json('data.0'));
    }

    public function test_roles_index_does_not_expose_name_ar_or_name_en(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/admin/roles');

        $response->assertOk();
        $role = $response->json('data.0');
        $this->assertArrayNotHasKey('name_ar', $role);
        $this->assertArrayNotHasKey('name_en', $role);
    }

    // -------------------------------------------------------------------------
    // Roles — show
    // -------------------------------------------------------------------------

    public function test_roles_show_returns_role_with_permissions(): void
    {
        $role = Role::where('name_en', 'Super Admin')->first();

        $response = $this->withToken($this->token)->getJson("/api/admin/roles/{$role->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $role->id)
            ->assertJsonStructure(['data' => ['id', 'name', 'guard_name', 'permissions']]);
    }

    // -------------------------------------------------------------------------
    // Roles — store
    // -------------------------------------------------------------------------

    public function test_store_role_creates_and_returns_201(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/admin/roles', [
            'name_en' => 'Editor',
            'name_ar' => 'محرر',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Editor')
            ->assertJsonPath('data.guard_name', 'admin');

        $this->assertDatabaseHas('roles', ['name_en' => 'Editor', 'guard_name' => 'admin']);
    }

    public function test_store_role_assigns_permissions(): void
    {
        $permission = Permission::where('guard_name', 'admin')->first();

        $response = $this->withToken($this->token)->postJson('/api/admin/roles', [
            'name_ar' => 'محرر',
            'name_en' => 'Editor',
            'permissions' => [$permission->id],
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['permissions']]);

        $permNames = collect($response->json('data.permissions'))->pluck('name');
        $this->assertContains($permission->name, $permNames);
    }

    public function test_store_role_requires_authentication(): void
    {
        $this->postJson('/api/admin/roles', ['name_en' => 'Editor'])->assertUnauthorized();
    }

    public function test_store_role_validates_required_fields(): void
    {
        $this->withToken($this->token)->postJson('/api/admin/roles', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name_en', 'name_ar']);
    }

    public function test_store_role_validates_permission_ids_exist(): void
    {
        $this->withToken($this->token)->postJson('/api/admin/roles', [
            'name_ar' => 'محرر',
            'name_en' => 'Editor',
            'permissions' => [9999],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['permissions.0']);
    }

    // -------------------------------------------------------------------------
    // Roles — update
    // -------------------------------------------------------------------------

    public function test_update_role_modifies_and_returns_200(): void
    {
        $role = Role::create([
            'name_en' => 'Old Name',
            'guard_name' => 'admin',
        ]);

        $response = $this->withToken($this->token)->putJson("/api/admin/roles/{$role->id}", [
            'name_en' => 'New Name',
        ]);

        $response->assertOk()->assertJsonPath('data.name', 'New Name');
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name_en' => 'New Name']);
    }

    public function test_update_role_syncs_permissions(): void
    {
        $role = Role::create(['name_en' => 'Writer', 'guard_name' => 'admin']);
        $permission = Permission::where('guard_name', 'admin')->first();

        $this->withToken($this->token)->putJson("/api/admin/roles/{$role->id}", [
            'permissions' => [$permission->id],
        ])->assertOk();

        $this->assertTrue($role->fresh()->hasPermissionTo($permission));
    }

    public function test_update_returns_404_for_non_admin_guard_role(): void
    {
        // Create a role that belongs to a non-admin guard to confirm the guard
        // check in the controller (guard_name !== 'admin' → 404) works correctly.
        $role = Role::create(['name_en' => 'web-role', 'guard_name' => 'web']);

        $this->withToken($this->token)->putJson("/api/admin/roles/{$role->id}", ['name_en' => 'X'])
            ->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // Roles — destroy
    // -------------------------------------------------------------------------

    public function test_destroy_role_returns_204(): void
    {
        $role = Role::create(['name_en' => 'Temp Role', 'guard_name' => 'admin']);

        $this->withToken($this->token)->deleteJson("/api/admin/roles/{$role->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_destroy_super_admin_role_is_allowed(): void
    {
        $superAdmin = Role::where('name_en', 'Super Admin')->first();

        // Super Admin role is a normal role; system owner is identified by admin_type, not role.
        $this->withToken($this->token)->deleteJson("/api/admin/roles/{$superAdmin->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('roles', ['id' => $superAdmin->id]);
    }

    // -------------------------------------------------------------------------
    // Permissions — index
    // -------------------------------------------------------------------------

    public function test_permissions_index_requires_authentication(): void
    {
        $this->getJson('/api/admin/permissions')->assertUnauthorized();
    }

    public function test_permissions_index_returns_paginated_permissions(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/admin/permissions');

        $response->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_permissions_index_returns_roles_count(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/admin/permissions');

        $response->assertOk();
        $this->assertArrayHasKey('roles_count', $response->json('data.0'));
    }

    public function test_permissions_index_returns_admin_guard_only(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/admin/permissions');

        $response->assertOk();
        foreach ($response->json('data') as $perm) {
            $this->assertEquals('admin', $perm['guard_name']);
        }
    }

    public function test_permissions_show_returns_permission(): void
    {
        $perm = Permission::where('guard_name', 'admin')->first();

        $this->withToken($this->token)->getJson("/api/admin/permissions/{$perm->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $perm->id)
            ->assertJsonStructure(['data' => ['id', 'name', 'guard_name', 'roles_count']]);
    }
}
