<?php

namespace Tests\Feature\Api\User;

use App\Models\User\Avatar;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_avatars(): void
    {
        $this->getJson('/api/user/avatars')->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_avatars(): void
    {
        Storage::fake('public');
        $path = 'avatars/test.png';
        Storage::disk('public')->put($path, 'fake');
        Avatar::query()->create(['image' => $path]);

        $user = User::create([
            'name' => 'Tester',
            'email' => 't@avatars.local',
            'phone' => '0503333333',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/user/avatars')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 1);
    }

    public function test_user_can_apply_catalog_avatar_copies_to_user_storage(): void
    {
        Storage::fake('public');
        $catalogPath = 'avatars/source.png';
        Storage::disk('public')->put($catalogPath, 'fake-image-bytes');
        $avatar = Avatar::query()->create(['image' => $catalogPath]);

        $user = User::create([
            'name' => 'Picker',
            'email' => 'picker@avatars.local',
            'phone' => '0504444444',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->putJson('/api/user/me', [
            'avatar_id' => $avatar->id,
        ])->assertOk()
            ->assertJsonPath('data.avatar_id', $avatar->id);

        $user->refresh();
        $this->assertSame($avatar->id, $user->avatar_id);
        $this->assertNotNull($user->image);
        $this->assertStringStartsWith('users/'.$user->id.'/', (string) $user->image);
        Storage::disk('public')->assertExists((string) $user->image);
    }

    public function test_upload_profile_image_clears_avatar_id(): void
    {
        Storage::fake('public');
        $catalogPath = 'avatars/source.png';
        Storage::disk('public')->put($catalogPath, 'x');
        $avatar = Avatar::query()->create(['image' => $catalogPath]);

        $user = User::create([
            'name' => 'Uploader',
            'email' => 'up@avatars.local',
            'phone' => '0505555555',
            'password' => Hash::make('password'),
            'is_active' => true,
            'avatar_id' => $avatar->id,
            'image' => 'users/old.png',
        ]);
        Storage::disk('public')->put('users/old.png', 'old');
        $token = $user->createToken('test')->plainTextToken;

        $file = UploadedFile::fake()->image('face.jpg', 100, 100);

        $this->withToken($token)->put('/api/user/me', [
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'image' => $file,
        ])->assertOk();

        $user->refresh();
        $this->assertNull($user->avatar_id);
        $this->assertNotSame('users/old.png', $user->image);
    }

    public function test_apply_avatar_fails_when_catalog_file_missing(): void
    {
        Storage::fake('public');
        $avatar = Avatar::query()->create(['image' => 'avatars/missing.png']);

        $user = User::create([
            'name' => 'Bad',
            'email' => 'bad@avatars.local',
            'phone' => '0506666666',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->putJson('/api/user/me', [
            'avatar_id' => $avatar->id,
        ])->assertStatus(422)
            ->assertJsonPath('errors.avatar_id.0', 'The selected avatar file is unavailable.');
    }
}
