<?php

namespace Tests\Feature\Api\Post;

use App\Models\Notifications\Notification;
use App\Models\Post\Post;
use App\Models\Post\PostComment;
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

    public function test_guest_can_browse_feed_show_and_comments_without_token(): void
    {
        $post = Post::factory()->create([
            'user_id' => $this->author->id,
            'body' => 'For visitors',
            'is_active' => true,
            'comments_count' => 3,
        ]);
        Post::factory()->create(['user_id' => $this->author->id, 'is_active' => false]);

        PostComment::create(['post_id' => $post->id, 'user_id' => $this->other->id, 'body' => 'c1', 'preset_text_snapshot' => null]);
        PostComment::create(['post_id' => $post->id, 'user_id' => $this->author->id, 'body' => 'c2', 'preset_text_snapshot' => null]);
        PostComment::create(['post_id' => $post->id, 'user_id' => $this->other->id, 'body' => 'c3', 'preset_text_snapshot' => null]);

        $this->getJson('/api/user/posts')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.body', 'For visitors')
            ->assertJsonPath('data.0.liked_by_me', false)
            ->assertJsonCount(2, 'data.0.recent_comments')
            ->assertJsonPath('data.0.recent_comments.0.body', 'c3')
            ->assertJsonPath('data.0.recent_comments.1.body', 'c2');

        $this->getJson("/api/user/posts/{$post->id}")
            ->assertOk()
            ->assertJsonPath('data.body', 'For visitors')
            ->assertJsonPath('data.liked_by_me', false)
            ->assertJsonCount(3, 'data.recent_comments');

        $this->getJson("/api/user/posts/{$post->id}/comments")
            ->assertOk();
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
            ->assertJsonPath('data.post_id', $post->id)
            ->assertJsonPath('data.post_report.reason', 'spam')
            ->assertJsonPath('data.post_report.reason_label_en', 'Spam or advertising');

        $ticketId = $response->json('data.id');
        $this->assertNotNull($ticketId);
        $this->assertDatabaseHas('post_reports', [
            'post_id' => $post->id,
            'reporter_id' => $this->other->id,
            'support_ticket_id' => $ticketId,
            'reason' => 'spam',
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
            'reason' => 'spam',
        ])->assertForbidden();
    }

    public function test_report_rejects_invalid_reason_enum(): void
    {
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true]);

        $this->withToken($this->otherToken)->postJson("/api/user/posts/{$post->id}/report", [
            'reason' => 'not_a_valid_reason',
        ])->assertUnprocessable();
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

    public function test_user_can_delete_own_comment(): void
    {
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true, 'comments_count' => 1]);
        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => $this->other->id,
            'body' => 'Mine to remove',
            'preset_text_snapshot' => null,
        ]);

        $this->withToken($this->otherToken)->deleteJson("/api/user/posts/{$post->id}/comments/{$comment->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('post_comments', ['id' => $comment->id]);
        $this->assertSame(0, $post->fresh()->comments_count);
    }

    public function test_user_cannot_delete_someone_elses_comment(): void
    {
        $post = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true, 'comments_count' => 1]);
        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => $this->author->id,
            'body' => 'Author comment',
            'preset_text_snapshot' => null,
        ]);

        $this->withToken($this->otherToken)->deleteJson("/api/user/posts/{$post->id}/comments/{$comment->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('post_comments', ['id' => $comment->id]);
        $this->assertSame(1, $post->fresh()->comments_count);
    }

    public function test_delete_comment_returns_404_when_comment_belongs_to_another_post(): void
    {
        $postA = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true, 'comments_count' => 0]);
        $postB = Post::factory()->create(['user_id' => $this->author->id, 'is_active' => true, 'comments_count' => 1]);
        $commentOnB = PostComment::create([
            'post_id' => $postB->id,
            'user_id' => $this->other->id,
            'body' => 'On B',
            'preset_text_snapshot' => null,
        ]);

        $this->withToken($this->otherToken)->deleteJson("/api/user/posts/{$postA->id}/comments/{$commentOnB->id}")
            ->assertNotFound();
    }
}
