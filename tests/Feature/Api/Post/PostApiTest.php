<?php

namespace Tests\Feature\Api\Post;

use App\Models\Notifications\Notification;
use App\Models\Post\Post;
use App\Models\Post\PostCommentPreset;
use App\Models\SupportTicket\SupportTicket;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    private User $author;

    private User $other;

    private string $authorToken;

    private string $otherToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::create([
            'name' => 'Post Author',
            'email' => 'author@posts.local',
            'phone' => '0501111111',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->other = User::create([
            'name' => 'Other User',
            'email' => 'other@posts.local',
            'phone' => '0502222222',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->authorToken = $this->author->createToken('test')->plainTextToken;
        $this->otherToken = $this->other->createToken('test')->plainTextToken;
    }

    public function test_user_can_create_and_list_own_posts_in_mine(): void
    {
        $this->withToken($this->authorToken)->postJson('/api/user/posts', [
            'body' => 'Hello feed',
        ])->assertCreated()
            ->assertJsonPath('data.body', 'Hello feed');

        $this->withToken($this->authorToken)->getJson('/api/user/posts/mine')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_feed_lists_only_active_posts(): void
    {
        Post::factory()->create(['user_id' => $this->author->id, 'body' => 'Public', 'is_active' => true]);
        Post::factory()->create(['user_id' => $this->author->id, 'body' => 'Hidden', 'is_active' => false]);

        $this->withToken($this->otherToken)->getJson('/api/user/posts')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.body', 'Public');
    }

    public function test_reporter_creates_ticket_and_post_report(): void
    {
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true]);

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $admin = \App\Models\User\Admin::create([
            'name' => 'Admin',
            'email' => 'admin@posts.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole(\App\Models\User\Role::where('name_en', 'Super Admin')->first());

        $response = $this->withToken($this->otherToken)->postJson("/api/user/posts/{$post->id}/report", [
            'reason' => 'spam',
            'details' => 'Looks like spam',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.post_id', $post->id);

        $ticketId = $response->json('data.id');
        $this->assertNotNull($ticketId);
        $this->assertDatabaseHas('post_reports', [
            'post_id' => $post->id,
            'reporter_id' => $this->other->id,
            'support_ticket_id' => $ticketId,
        ]);
        $this->assertDatabaseHas('support_tickets', [
            'id' => $ticketId,
            'post_id' => $post->id,
            'priority' => SupportTicket::PRIORITY_HIGH,
        ]);
    }

    public function test_author_cannot_report_own_post(): void
    {
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true]);

        $this->withToken($this->authorToken)->postJson("/api/user/posts/{$post->id}/report", [
            'reason' => 'test',
        ])->assertForbidden();
    }

    public function test_comment_with_preset_and_free_text(): void
    {
        $preset = PostCommentPreset::create([
            'text' => 'Nice',
            'is_active' => true,
        ]);
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true]);

        $this->withToken($this->otherToken)->postJson("/api/user/posts/{$post->id}/comments", [
            'comment_preset_id' => $preset->id,
            'body' => 'Extra note',
        ])->assertCreated()
            ->assertJsonPath('data.preset_text_snapshot', $preset->text)
            ->assertJsonPath('data.body', 'Extra note');

        $this->assertEquals(1, $post->fresh()->comments_count);
    }

    public function test_like_sends_notification_to_post_author(): void
    {
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true]);

        $this->withToken($this->otherToken)->postJson("/api/user/posts/{$post->id}/like")
            ->assertOk()
            ->assertJsonPath('liked', true);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->author->id,
            'target_type' => 'posts',
            'target_id' => $post->id,
        ]);
    }

    public function test_self_like_does_not_notify_author(): void
    {
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true]);

        $countBefore = Notification::query()->where('notifiable_id', $this->author->id)->count();

        $this->withToken($this->authorToken)->postJson("/api/user/posts/{$post->id}/like")->assertOk();

        $this->assertSame($countBefore, Notification::query()->where('notifiable_id', $this->author->id)->count());
    }

    public function test_comment_sends_notification_to_post_author(): void
    {
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true]);

        $this->withToken($this->otherToken)->postJson("/api/user/posts/{$post->id}/comments", [
            'body' => 'Hello author',
        ])->assertCreated();

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->author->id,
            'target_type' => 'posts',
            'target_id' => $post->id,
        ]);
    }
}
