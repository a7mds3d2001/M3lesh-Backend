<?php

namespace Tests\Feature\Api\Admin;

use App\Models\User\Admin;
use App\Models\User\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature tests for Admin Auth endpoints.
 *
 * Covers: POST /api/admin/login, GET /api/admin/me, POST /api/admin/logout
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function createAdmin(array $overrides = []): Admin
    {
        return Admin::create(array_merge([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret123'),
            'phone' => '0500000000',
            'is_active' => true,
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // POST /api/admin/login
    // -------------------------------------------------------------------------

    public function test_login_returns_token_and_admin_shape(): void
    {
        $admin = $this->createAdmin();
        $admin->assignRole(Role::where('name_en', 'Super Admin')->first());

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'admin' => ['id', 'name', 'email', 'phone', 'is_active', 'roles', 'permissions'],
            ]);

        $this->assertNotEmpty($response->json('token'));
        $this->assertEquals($admin->id, $response->json('admin.id'));
    }

    public function test_login_returns_roles_and_permissions(): void
    {
        $admin = $this->createAdmin();
        $admin->assignRole(Role::where('name_en', 'Super Admin')->first());

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('admin.roles'));
        $this->assertNotEmpty($response->json('admin.permissions'));
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->createAdmin();

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'The provided credentials are incorrect.')
            ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_login_fails_with_unknown_email(): void
    {
        $response = $this->postJson('/api/admin/login', [
            'email' => 'nobody@test.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_fails_for_inactive_admin(): void
    {
        $this->createAdmin(['is_active' => false]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Account is disabled or deleted.');
    }

    public function test_login_validates_required_fields(): void
    {
        $this->postJson('/api/admin/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_validates_email_format(): void
    {
        $this->postJson('/api/admin/login', ['email' => 'not-an-email', 'password' => 'x'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_allows_multiple_active_tokens(): void
    {
        $admin = $this->createAdmin();

        // First login
        $first = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);
        $firstToken = $first->json('token');

        // Second login should create an additional token without revoking the first
        $second = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);
        $secondToken = $second->json('token');

        $this->assertNotSame($firstToken, $secondToken);

        // Both tokens should remain valid (multi-device login)
        $this->withToken($firstToken)->getJson('/api/admin/me')->assertOk();
        $this->withToken($secondToken)->getJson('/api/admin/me')->assertOk();

        // There should be two tokens stored for this admin
        $this->assertCount(2, $admin->fresh()->tokens);
    }

    // -------------------------------------------------------------------------
    // GET /api/admin/me
    // -------------------------------------------------------------------------

    public function test_me_returns_admin_data_with_valid_token(): void
    {
        $admin = $this->createAdmin();
        $token = $admin->createToken('api')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/admin/me');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'phone', 'is_active', 'roles', 'permissions'],
            ]);

        $this->assertEquals($admin->id, $response->json('data.id'));
    }

    public function test_me_returns_401_without_token(): void
    {
        $this->getJson('/api/admin/me')->assertUnauthorized();
    }

    // -------------------------------------------------------------------------
    // POST /api/admin/logout
    // -------------------------------------------------------------------------

    public function test_logout_revokes_token_and_returns_message(): void
    {
        $admin = $this->createAdmin();
        $token = $admin->createToken('api')->plainTextToken;

        $this->withToken($token)->postJson('/api/admin/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logged out.');

        // The token row must be removed from the DB (the real contract).
        // Sanctum caches the resolved user within the test kernel's request
        // lifecycle, so a second in-process HTTP call cannot verify invalidity;
        // a direct database assertion is the authoritative check.
        $this->assertCount(0, $admin->fresh()->tokens);
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/admin/logout')->assertUnauthorized();
    }
}
