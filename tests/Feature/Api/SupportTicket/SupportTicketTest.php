<?php

namespace Tests\Feature\Api\SupportTicket;

use App\Models\SupportTicket\SupportTicket;
use App\Models\User\Admin;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature tests for Support Ticket API endpoints.
 */
class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private string $adminToken;
    private User $user;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Setup Admin
        $this->admin = Admin::create([
            'name' => 'Support Admin',
            'email' => 'admin@support.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->admin->assignRole(Role::where('name_en', 'Super Admin')->first());
        $this->adminToken = $this->admin->createToken('admin')->plainTextToken;

        // Setup User
        $this->user = User::create([
            'name' => 'Support User',
            'email' => 'user@support.local',
            'phone' => '0503334445',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->userToken = $this->user->createToken('user')->plainTextToken;
    }

    // -------------------------------------------------------------------------
    // Support Tickets — Visitor / Public
    // -------------------------------------------------------------------------

    public function test_visitor_can_submit_ticket(): void
    {
        $response = $this->postJson('/api/user/support-tickets', [
            'visitor_name' => 'Guest User',
            'visitor_email' => 'guest@test.com',
            'visitor_phone' => '0500000000',
            'message' => 'I cannot login.',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('support_tickets', ['visitor_name' => 'Guest User', 'user_id' => null]);
    }

    // -------------------------------------------------------------------------
    // Support Tickets — Authenticated User
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_submit_ticket(): void
    {
        $response = $this->withToken($this->userToken)->postJson('/api/user/support-tickets', [
            'message' => 'My Account Problem',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.user_id', $this->user->id);

        $this->assertDatabaseHas('support_tickets', ['message' => 'My Account Problem', 'user_id' => $this->user->id]);
    }

    public function test_user_can_view_only_own_tickets(): void
    {
        SupportTicket::factory()->create(['user_id' => $this->user->id, 'message' => 'My Message']);
        SupportTicket::factory()->create(['user_id' => null, 'message' => 'Guest Message']);

        $response = $this->withToken($this->userToken)->getJson('/api/user/support-tickets');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.message', 'My Message');
    }

    // -------------------------------------------------------------------------
    // Support Tickets — Admin Actions
    // -------------------------------------------------------------------------

    public function test_admin_can_update_ticket_status_and_priority(): void
    {
        $ticket = SupportTicket::factory()->create(['status' => 'pending', 'priority' => 'normal']);

        $this->withToken($this->adminToken)->putJson("/api/admin/support-tickets/{$ticket->id}/status", [
            'status' => 'in_progress',
        ])->assertOk();

        $this->withToken($this->adminToken)->putJson("/api/admin/support-tickets/{$ticket->id}/priority", [
            'priority' => 'high',
        ])->assertOk();

        $this->assertEquals('in_progress', $ticket->fresh()->status);
        $this->assertEquals('high', $ticket->fresh()->priority);
    }

    public function test_admin_can_add_log_to_ticket(): void
    {
        $ticket = SupportTicket::factory()->create();

        $this->withToken($this->adminToken)->postJson("/api/admin/support-tickets/{$ticket->id}/logs", [
            'message' => 'Internal note',
            'log_type' => 'internal_note',
        ])->assertOk();

        $this->assertDatabaseHas('support_ticket_logs', [
            'ticket_id' => $ticket->id,
            'message' => 'Internal note',
            'log_type' => 'internal_note',
        ]);
    }

    public function test_visitor_support_ticket_requires_message(): void
    {
        $this->postJson('/api/user/support-tickets', [
            'visitor_name' => 'Guest User',
            'visitor_email' => 'guest@test.com',
            'visitor_phone' => '0500000000',
            // 'message' missing on purpose
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }

    public function test_guest_cannot_list_user_tickets(): void
    {
        $this->getJson('/api/user/support-tickets')
            ->assertUnauthorized();
    }

    public function test_non_admin_cannot_update_ticket_status(): void
    {
        $ticket = SupportTicket::factory()->create(['status' => 'pending']);

        $this->withToken($this->userToken)->putJson("/api/admin/support-tickets/{$ticket->id}/status", [
            'status' => 'in_progress',
        ])->assertForbidden();
    }
}
