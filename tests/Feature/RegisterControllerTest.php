<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Akun berhasil dibuat! Silakan login.');
        $this->assertTrue(User::where('email', 'john@example.com')->exists());
    }

    /** @test */
    public function it_fails_to_register_a_user_with_existing_email()
    {
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'jane@example.com', // Existing email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_fails_to_register_a_user_with_empty_fields()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function it_fails_to_register_a_user_with_mismatched_passwords()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_fails_to_register_a_user_with_short_password()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_fails_to_register_a_user_with_invalid_email_format()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_fails_to_register_a_user_with_short_name()
    {
        $response = $this->post('/register', [
            'name' => 'Jo', // Nama terlalu pendek
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function it_fails_to_register_a_user_with_invalid_name_characters()
    {
        $response = $this->post('/register', [
            'name' => 'John123!', // Karakter tidak valid dalam nama
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function it_fails_to_register_a_user_with_weak_password()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_fails_to_register_a_user_with_case_insensitive_existing_email()
    {
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'JANE@EXAMPLE.COM', // Email yang sama dengan perbedaan huruf besar/kecil
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        
        $response->assertSessionHasErrors('email');
    }
}
