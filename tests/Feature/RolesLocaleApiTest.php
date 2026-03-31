<?php

namespace Tests\Feature;

use App\Models\User\Admin;
use App\Models\User\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolesLocaleApiTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private string $token;

    private Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.local',
            'password' => Hash::make('password'),
            'phone' => null,
            'is_active' => true,
        ]);

        $this->admin->assignRole(Role::where('name_en', 'Super Admin')->first());

        $this->token = $this->admin->createToken('test')->plainTextToken;

        $this->role = Role::create([
            'name_en' => 'Editor',
            'name_ar' => 'محرر',
            'guard_name' => 'admin',
        ]);
    }

    public function test_accept_language_ar_returns_name_as_name_ar(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/admin/roles/'.$this->role->id, ['Accept-Language' => 'ar']);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'محرر');
    }

    public function test_accept_language_en_returns_name_as_name_en(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/admin/roles/'.$this->role->id, ['Accept-Language' => 'en']);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Editor');
    }

    public function test_missing_accept_language_defaults_to_en(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/admin/roles/'.$this->role->id);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Editor');
    }

    public function test_invalid_accept_language_defaults_to_en(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/admin/roles/'.$this->role->id, ['Accept-Language' => 'fr']);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Editor');
    }

    public function test_response_does_not_contain_name_ar_or_name_en(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/admin/roles/'.$this->role->id, ['Accept-Language' => 'ar']);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertArrayNotHasKey('name_ar', $data);
        $this->assertArrayNotHasKey('name_en', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function test_roles_index_does_not_contain_name_ar_or_name_en(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/admin/roles', ['Accept-Language' => 'en']);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertNotEmpty($data);

        foreach ($data as $role) {
            $this->assertArrayNotHasKey('name_ar', $role);
            $this->assertArrayNotHasKey('name_en', $role);
            $this->assertArrayHasKey('name', $role);
        }
    }

    public function test_roles_index_pagination_unchanged(): void
    {
        $response = $this->withToken($this->token)
            ->getJson('/api/admin/roles?per_page=5', ['Accept-Language' => 'en']);

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'current_page',
            'per_page',
            'total',
            'path',
        ]);
    }
}
