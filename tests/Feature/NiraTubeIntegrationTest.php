<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Videos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NiraTubeIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $testUser;
    protected User $anotherUser;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Create test users
        $this->testUser = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $this->anotherUser = User::factory()->create([
            'email' => 'another@example.com',
            'password' => bcrypt('password123')
        ]);
    }

    /** @test */
    public function guest_to_authenticated_user_journey()
    {
        // 1. Guest mencoba mengakses halaman videos (should redirect to login)
        $response = $this->get('/videos');
        $response->assertRedirect('/login');

        // 2. Guest melakukan registrasi
        $registerResponse = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123'
        ]);
        $registerResponse->assertRedirect('/login');

        // 3. Guest melakukan login
        $loginResponse = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'Password123'
        ]);
        $loginResponse->assertRedirect('/videos');

        // 4. Sekarang sebagai user yang terautentikasi
        $this->actingAs(User::where('email', 'john@example.com')->firstOrFail());

        // 5. User dapat mengakses halaman videos
        $response = $this->get('/videos');
        $response->assertOk();
    }

    /** @test */
    public function video_management_workflow()
    {
        // 1. Setup user terautentikasi
        $this->actingAs($this->testUser);

        // 2. Upload video
        $video = UploadedFile::fake()->create('test-video.mp4', 1024);
        $uploadResponse = $this->post('/videos', [
            'video' => $video,
            'title' => 'Test Video',
            'description' => 'Test Description',
            'category' => 'Education',
            'privacy' => 'public'
        ]);
        $uploadResponse->assertRedirect('/videos');

        // 3. Verifikasi video di database
        $createdVideo = Videos::where('title', 'Test Video')->first();
        $this->assertNotNull($createdVideo);

        // 4. Edit video
        $editResponse = $this->put("/videos/{$createdVideo->id}", [
            'title' => 'Updated Video',
            'description' => 'Updated Description',
            'category' => 'Entertainment',
            'privacy' => 'private'
        ]);
        $editResponse->assertRedirect('/videos');

        // 5. Verifikasi perubahan
        $this->assertDatabaseHas('videos', [
            'id' => $createdVideo->id,
            'title' => 'Updated Video',
            'status' => 'private'
        ]);

        // 6. Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
    }

    /** @test */
    public function video_viewing_and_access_control()
    {
        // 1. Buat video untuk test user
        $video = Videos::create([
            'video' => 'test-video.mp4',
            'title' => 'Public Video',
            'description' => 'Test Description',
            'category' => 'Education',
            'status' => 'public',
            'uploader_id' => $this->testUser->id,
            'views' => 0,
            'likes' => 0
        ]);

        // 2. Guest dapat melihat video public
        $viewResponse = $this->get("/videos/{$video->id}");
        $viewResponse->assertOk();

        // 3. Guest dapat increment views
        $incrementResponse = $this->post("/videos/{$video->id}/increment-views");
        $incrementResponse->assertOk();

        // 4. Verifikasi views bertambah
        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'views' => 1
        ]);

        // 5. Guest tidak bisa mengedit video
        $editResponse = $this->put("/videos/{$video->id}", [
            'title' => 'Hacked Title'
        ]);
        $editResponse->assertRedirect('/login');
    }

    /** @test */
    public function multiple_user_interaction_workflow()
    {
        // 1. User1 upload video
        $this->actingAs($this->testUser);
        // $video = UploadedFile::fake()->create('user1-video.mp4', 1024, 'video/mp4');
        $response =  $this->post('/videos', [
            'title' => 'User1 Video',
            'description' => 'This is a valid video description.',
            'category' => 'Education',
            'privacy' => 'public',
            'video' => UploadedFile::fake()->create('video.mp4', 20000, 'video/mp4')
        ]);

        $response->assertStatus(302); 

        $this->assertDatabaseHas('videos', [
            'title' => 'User1 Video',
            'category' => 'Education',
        ]);

        $uploadedVideo = Videos::where('title', 'User1 Video')->first();

        // 2. User2 mencoba melihat dan berinteraksi
        $this->actingAs($this->anotherUser);

        // User2 dapat melihat video
        $viewResponse = $this->get("/videos/{$uploadedVideo->id}");
        $viewResponse->assertOk();

        // User2 dapat increment views
        $this->post("/videos/{$uploadedVideo->id}/increment-views");

        // User2 tidak dapat mengedit video User1
        $editResponse = $this->put("/videos/{$uploadedVideo->id}", [
            'title' => 'Edited by User2',
            'description' => 'This is a valid video description.',
            'category' => 'Education',
            'privacy' => 'public'
        ]);
        $editResponse->assertStatus(302);

        // User2 tidak dapat menghapus video User1
        $deleteResponse = $this->delete("/videos/{$uploadedVideo->id}");
        $deleteResponse->assertStatus(302);

        // 3. Verifikasi video tetap milik User1
        $this->assertDatabaseHas('videos', [
            'id' => $uploadedVideo->id,
            'title' => 'User1 Video',
            'uploader_id' => $this->testUser->id
        ]);
    }
}
