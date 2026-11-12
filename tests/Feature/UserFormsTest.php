<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_show_the_create_link_and_placeholder_avatar(): void
    {
        User::factory()->create();
        $this->get('/users')->assertOk()->assertSee('Create user')->assertSee('images/avatar-placeholder.svg');
        $this->get('/users/create')->assertOk()->assertSee('name="username"', false);
    }

    public function test_user_can_be_created(): void
    {
        $this->post('/users', ['username' => 'lanz', 'full_name' => 'Lanz Wayne Villaruel'])
            ->assertRedirect('/users')->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['username' => 'lanz', 'full_name' => 'Lanz Wayne Villaruel', 'avatar' => null]);
    }

    public function test_username_and_full_name_are_required(): void
    {
        $this->post('/users', [])->assertSessionHasErrors(['username', 'full_name']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_duplicate_username_is_rejected_with_old_input(): void
    {
        User::factory()->create(['username' => 'taken']);
        $this->from('/users/create')->post('/users', ['username' => 'taken', 'full_name' => 'Keep My Name'])
            ->assertRedirect('/users/create')->assertSessionHasErrors('username')
            ->assertSessionHasInput('full_name', 'Keep My Name');
        $this->get('/users/create')->assertOk()->assertSee('value="Keep My Name"', false)
            ->assertSee('That username is already taken.');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_edit_is_prefilled_and_accepts_the_current_username(): void
    {
        $user = User::factory()->create(['username' => 'lanz', 'full_name' => 'Original Name']);
        $this->get('/users/'.$user->id.'/edit')->assertOk()
            ->assertSee('value="lanz"', false)->assertSee('value="Original Name"', false)
            ->assertSee('enctype="multipart/form-data"', false);
        $this->put('/users/'.$user->id, ['username' => 'lanz', 'full_name' => 'Updated Name'])
            ->assertRedirect('/users');
        $this->assertSame('Updated Name', $user->fresh()->full_name);
    }

    public function test_update_rejects_another_users_username(): void
    {
        User::factory()->create(['username' => 'taken']);
        $user = User::factory()->create(['username' => 'original']);
        $this->put('/users/'.$user->id, ['username' => 'taken', 'full_name' => 'Changed'])
            ->assertSessionHasErrors('username');
        $this->assertSame('original', $user->fresh()->username);
    }

    public function test_upload_stores_a_256_pixel_png_and_only_its_relative_path(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        // Match the browser's multipart POST and method override.
        $this->post('/users/'.$user->id, [
            '_method' => 'PUT', 'username' => $user->username, 'full_name' => $user->full_name,
            'avatar' => UploadedFile::fake()->image('portrait.jpg', 800, 500),
        ])->assertRedirect('/users')->assertSessionHasNoErrors();
        $path = $user->fresh()->avatar;
        $this->assertMatchesRegularExpression('/^avatars\/[0-9a-f-]+\.png$/', $path);
        Storage::disk('public')->assertExists($path);
        $dimensions = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame(256, $dimensions[0]);
        $this->assertSame(256, $dimensions[1]);
        $this->assertSame('image/png', $dimensions['mime']);
        $this->get('/users')->assertOk()->assertSee($path);
    }

    public function test_replacing_an_avatar_deletes_the_old_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/old.png', 'old image');
        $user = User::factory()->create(['avatar' => 'avatars/old.png']);
        $this->put('/users/'.$user->id, [
            'username' => $user->username, 'full_name' => $user->full_name,
            'avatar' => UploadedFile::fake()->image('new.png', 400, 300),
        ])->assertRedirect('/users');
        Storage::disk('public')->assertMissing('avatars/old.png');
        Storage::disk('public')->assertExists($user->fresh()->avatar);
    }

    public function test_edit_without_a_file_keeps_the_current_avatar(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/current.png', 'current');
        $user = User::factory()->create(['avatar' => 'avatars/current.png']);
        $this->put('/users/'.$user->id, ['username' => $user->username, 'full_name' => 'Changed'])
            ->assertRedirect('/users');
        $this->assertSame('avatars/current.png', $user->fresh()->avatar);
        Storage::disk('public')->assertExists('avatars/current.png');
    }

    public function test_non_images_and_disguised_images_are_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        foreach (['notes.txt', 'fake.jpg', 'avatar.svg'] as $name) {
            $this->put('/users/'.$user->id, [
                'username' => $user->username, 'full_name' => $user->full_name,
                'avatar' => UploadedFile::fake()->createWithContent($name, 'This is not an image.'),
            ])->assertSessionHasErrors('avatar');
        }
        $this->assertNull($user->fresh()->avatar);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_images_larger_than_2_mb_are_rejected_without_losing_data(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/current.png', 'current');
        $user = User::factory()->create(['full_name' => 'Original Name', 'avatar' => 'avatars/current.png']);
        $this->from('/users/'.$user->id.'/edit')->put('/users/'.$user->id, [
            'username' => $user->username, 'full_name' => 'Keep This Input',
            'avatar' => UploadedFile::fake()->image('large.jpg')->size(2049),
        ])->assertSessionHasErrors('avatar')->assertSessionHasInput('full_name', 'Keep This Input');
        $this->assertSame('Original Name', $user->fresh()->full_name);
        $this->assertSame('avatars/current.png', $user->fresh()->avatar);
        Storage::disk('public')->assertExists('avatars/current.png');
    }

    public function test_image_at_2_mb_limit_is_accepted(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->put('/users/'.$user->id, [
            'username' => $user->username, 'full_name' => $user->full_name,
            'avatar' => UploadedFile::fake()->image('limit.jpg')->size(2048),
        ])->assertRedirect('/users')->assertSessionHasNoErrors();
        Storage::disk('public')->assertExists($user->fresh()->avatar);
    }

    public function test_missing_user_returns_404(): void
    {
        $this->get('/users/999/edit')->assertNotFound();
        $this->put('/users/999', ['username' => 'new', 'full_name' => 'New Name'])->assertNotFound();
    }
}
