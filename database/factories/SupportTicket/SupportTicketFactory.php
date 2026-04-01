<?php

namespace Database\Factories\SupportTicket;

use App\Models\SupportTicket\SupportTicket;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'visitor_name' => fake()->name(),
            'visitor_phone' => fake()->numerify('050#######'),
            'visitor_email' => fake()->safeEmail(),
            'message' => fake()->sentence(),
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => SupportTicket::PRIORITY_NORMAL,
            'is_active' => true,
            'attachments' => [],
        ];
    }

    public function forUser(?User $user = null): self
    {
        return $this->state(function () use ($user): array {
            $user ??= User::factory()->create();

            return [
                'user_id' => $user->id,
                'visitor_name' => null,
                'visitor_phone' => null,
                'visitor_email' => null,
            ];
        });
    }
}
