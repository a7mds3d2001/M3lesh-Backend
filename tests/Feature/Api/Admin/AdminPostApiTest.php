<?php

namespace Tests\Feature\Api\Admin;

use App\Models\Post\Post;
use App\Models\Post\PostComment;
use App\Models\Post\PostLike;
use App\Models\User\Admin;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminPostApiTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->admin = Admin::create([
            'name' => 'API Admin',
            'email' => 'api-admin@test.com',
            'password' => Hash::make('secret123'),
            'phone' => '0500000001',
            'is_active' => true,
        ]);
        $this->admin->assignRole(Role::where('name_en', 'Super Admin')->first());

        Sanctum::actingAs($this->admin);
    }

    public function test_admin_can_list_and_create_post(): void
    {
        $user = User::factory()->create();

        $this->getJson('/api/admin/posts')->assertOk();

        $response = $this->postJson('/api/admin/posts', [
            'user_id' => $user->id,
            'body' => 'Admin-created post',
            'is_active' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.body', 'Admin-created post')
            ->assertJsonPath('data.user_id', $user->id);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'body' => 'Admin-created post',
        ]);
    }

    public function test_admin_can_soft_delete_and_restore_post(): void
    {
        $post = Post::factory()->create();

        $this->deleteJson("/api/admin/posts/{$post->id}")->assertNoContent();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);

        $this->postJson("/api/admin/posts/{$post->id}/restore")->assertOk();
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'deleted_at' => null]);
    }

    public function test_admin_can_delete_comment_and_decrement_count(): void
    {
        $post = Post::factory()->create(['comments_count' => 1]);
        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => User::factory()->create()->id,
            'body' => 'To remove',
            'preset_text_snapshot' => null,
        ]);

        $this->deleteJson("/api/admin/posts/{$post->id}/comments/{$comment->id}")->assertNoContent();

        $this->assertDatabaseMissing('post_comments', ['id' => $comment->id]);
        $this->assertSame(0, $post->fresh()->comments_count);
    }

    public function test_admin_can_delete_like_and_decrement_count(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['likes_count' => 1]);
        $like = PostLike::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $this->deleteJson("/api/admin/posts/{$post->id}/likes/{$like->id}")->assertNoContent();

        $this->assertDatabaseMissing('post_likes', ['id' => $like->id]);
        $this->assertSame(0, $post->fresh()->likes_count);
    }

    public function test_admin_comment_preset_crud(): void
    {
        $r = $this->postJson('/api/admin/post-comment-presets', [
            'text' => 'Hello preset',
            'is_active' => true,
        ]);
        $r->assertCreated()->assertJsonPath('data.text', 'Hello preset');
        $id = $r->json('data.id');

        $this->putJson("/api/admin/post-comment-presets/{$id}", [
            'text' => 'Updated',
        ])->assertOk()->assertJsonPath('data.text', 'Updated');

        $this->deleteJson("/api/admin/post-comment-presets/{$id}")->assertNoContent();
        $this->assertSoftDeleted('post_comment_presets', ['id' => $id]);

        $this->postJson("/api/admin/post-comment-presets/{$id}/restore")->assertOk();
        $this->assertDatabaseHas('post_comment_presets', ['id' => $id, 'deleted_at' => null]);

        $this->deleteJson("/api/admin/post-comment-presets/{$id}/force")->assertNoContent();
        $this->assertDatabaseMissing('post_comment_presets', ['id' => $id]);
    }

    public function test_wrong_post_returns_404_for_comment_delete(): void
    {
        $postA = Post::factory()->create();
        $postB = Post::factory()->create();
        $comment = PostComment::create([
            'post_id' => $postA->id,
            'user_id' => User::factory()->create()->id,
            'body' => 'x',
            'preset_text_snapshot' => null,
        ]);

        $this->deleteJson("/api/admin/posts/{$postB->id}/comments/{$comment->id}")->assertNotFound();
    }
}
