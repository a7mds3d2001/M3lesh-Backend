<?php

namespace Tests\Unit\SupportTicket;

use App\Models\SupportTicket\SupportTicket;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_open_normal_priority_ticket_by_default(): void
    {
        $ticket = SupportTicket::factory()->create();

        $this->assertInstanceOf(SupportTicket::class, $ticket);
        $this->assertEquals(SupportTicket::STATUS_OPEN, $ticket->status);
        $this->assertEquals(SupportTicket::PRIORITY_NORMAL, $ticket->priority);
    }

    public function test_is_closed_helper_works_based_on_status(): void
    {
        $open = SupportTicket::factory()->create(['status' => SupportTicket::STATUS_OPEN]);
        $closed = SupportTicket::factory()->create(['status' => SupportTicket::STATUS_CLOSED]);

        $this->assertFalse($open->isClosed());
        $this->assertTrue($closed->isClosed());
    }

    public function test_owner_label_prefers_user_name_over_visitor(): void
    {
        $user = User::factory()->create(['name' => 'Named User']);
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'visitor_name' => 'Visitor Name',
        ]);

        $this->assertEquals('Named User', $ticket->ownerLabel());
    }
}
