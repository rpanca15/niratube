<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Videos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VideoControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_dapat_mengunggah_video_dengan_semua_field_valid() // Kasus Uji 7
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('videos.store'), [
            'title' => 'Valid Video',
            'description' => 'This is a valid video description.',
            'category' => 'Education',
            'privacy' => 'public',
            'video' => UploadedFile::fake()->create('video.mp4', 20000, 'video/mp4'),
        ]);

        $this->assertDatabaseHas('videos', [
            'title' => 'Valid Video',
        ]);

        $response->assertRedirect(route('videos.index'));
    }

    /** @test */
    public function user_dapat_melihat_video_yang_diunggah() // Kasus Uji 8
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $video = Videos::factory()->create(['uploader_id' => $user->id]);

        $response = $this->get(route('videos.index'));

        $response->assertStatus(200);
        $response->assertSee($video->title);
    }

    /** @test */
    public function user_tidak_bisa_mengunggah_video_dengan_file_tidak_valid() // Kasus Uji 9
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('videos.store'), [
            'title' => 'Invalid Video',
            'description' => 'Invalid video upload.',
            'category' => 'Education',
            'privacy' => 'public',
            'video' => UploadedFile::fake()->create('invalid.txt', 2000, 'text/plain'), // File tidak valid
        ]);

        $response->assertRedirect(route('videos.store'));
        $response->assertSessionHasErrors('video');
    }

    /** @test */
    public function user_tidak_bisa_mengunggah_video_dengan_ukuran_file_melebihi_batas_maksimum() // Kasus Uji 10
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('videos.store'), [
            'title' => 'Large Video',
            'description' => 'This video exceeds the size limit.',
            'category' => 'Education',
            'privacy' => 'public',
            'video' => UploadedFile::fake()->create('large_video.mp4', 10240000, 'video/mp4'), // Ukuran melebihi batas
        ]);

        $response->assertRedirect(route('videos.store'));
        $response->assertSessionHasErrors('video');
    }

    /** @test */
    public function user_dapat_mengubah_informasi_video_dengan_data_valid() // Kasus Uji 11
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $video = Videos::factory()->create(['uploader_id' => $user->id]);

        $response = $this->put(route('videos.update', $video), [
            'title' => 'Updated Video Title',
            'description' => 'Updated video description.',
            'category' => 'Education',
            'privacy' => 'public',
        ]);

        $video->refresh();
        $this->assertEquals('Updated Video Title', $video->title);
        $response->assertRedirect(route('videos.index'));
    }

    /** @test */
    public function user_tidak_bisa_mengubah_video_yang_tidak_ada()
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put(route('videos.update', ['video' => 999]), [ // ID yang tidak ada
            'title' => 'Updated Video',
            'description' => 'Updated description.',
            'category' => 'Science',
            'privacy' => 'private',
        ]);

        $response->assertNotFound();
    }

    /** @test */
    public function user_tidak_bisa_mengubah_informasi_video_dengan_data_invalid() // Kasus Uji 12
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $video = Videos::factory()->create(['uploader_id' => $user->id]);

        $response = $this->put(route('videos.update', $video), [
            'title' => '', // Invalid
            'description' => '',
            'category' => '',
            'privacy' => '',
        ]);

        $response->assertRedirect(route('videos.edit', $video));
        $response->assertSessionHasErrors(['title', 'description', 'category']);
    }

    /** @test */
    public function user_dapat_menghapus_video_dari_akun_sendiri() // Kasus Uji 13
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $video = Videos::factory()->create(['uploader_id' => $user->id]);

        $response = $this->delete(route('videos.destroy', $video));

        $this->assertDatabaseMissing('videos', [
            'id' => $video->id,
        ]);
        $response->assertRedirect(route('videos.index'));
    }

    /** @test */
    public function user_tidak_bisa_menghapus_video_yang_tidak_ada()
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete(route('videos.destroy', ['video' => 999])); // ID 999 tidak ada

        $response->assertRedirect(route('videos.index'));
    }

    /** @test */
    public function user_tidak_bisa_menghapus_video_yang_bukan_miliknya()
    {
        $owner = User::factory()->create(); // Membuat pemilik video
        $video = Videos::factory()->create(['uploader_id' => $owner->id]); // Video milik pengguna

        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $nonOwner */
        $nonOwner = User::factory()->create(); // Membuat pengguna non-pemilik
        $this->actingAs($nonOwner); // Masuk sebagai pengguna non-pemilik

        $response = $this->delete(route('videos.destroy', $video->id));

        $response->assertRedirect(route('videos.index'));
    }
}
