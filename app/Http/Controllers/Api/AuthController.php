<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    /**
     * Login dan beri akses token.
     */
    public function login(Request $request)
    {
        // Validasi input menggunakan Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->sendError('Wrong credentials', ['error' => 'The provided credentials are incorrect'], 401);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        Log::info('Generated token: ', ['token' => $token]);

        return $this->sendResponse($token, 'User logged in successfully.');
    }

    /**
     * Register a newly created user.
     */
    public function register(Request $request)
    {
        // Validasi input menggunakan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'role' => 'nullable|in:user,admin',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        // Buat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
            'remember_token' => Str::random(60),  // Menambahkan token untuk remember me
        ]);

        // Buat token untuk pengguna yang baru didaftarkan
        $token = $user->createToken('my-app-token')->plainTextToken;

        Log::info('User registered and token generated: ', ['token' => $token]);

        // Kirimkan respons
        return $this->sendResponse($token, 'User registered successfully.');
    }

    /**
     * Logout dan hapus semua token pengguna.
     */
    public function logout(Request $request)
    {
        // Log untuk memastikan token ditemukan
        Log::info('User tokens: ', ['tokens' => $request->user()->tokens]);

        // Menghapus semua token yang terkait dengan pengguna yang sedang login
        $request->user()->tokens->each(function ($token) {
            Log::info('Deleting token: ', ['token' => $token->id]);
            $token->delete();
        });

        // Atau revoke hanya token yang sedang aktif
        Log::info('Deleting current active token: ', ['token' => $request->user()->currentAccessToken()->id]);
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'You have been successfully logged out.', 200);
    }

    /**
     * Menampilkan daftar pengguna (opsional, tergantung kebutuhan).
     */
    public function index()
    {
        // Bisa diisi dengan logika untuk menampilkan pengguna
    }

    /**
     * Menyimpan data baru (opsional, tergantung kebutuhan).
     */
    public function store(Request $request)
    {
        // Bisa diisi dengan logika untuk menyimpan data pengguna baru
    }

    /**
     * Menampilkan informasi pengguna berdasarkan ID (opsional, tergantung kebutuhan).
     */
    public function show(string $id)
    {
        // Bisa diisi dengan logika untuk menampilkan pengguna berdasarkan ID
    }

    /**
     * Mengupdate data pengguna berdasarkan ID (opsional, tergantung kebutuhan).
     */
    public function update(Request $request, string $id)
    {
        // Bisa diisi dengan logika untuk mengupdate data pengguna berdasarkan ID
    }

    /**
     * Menghapus data pengguna berdasarkan ID (opsional, tergantung kebutuhan).
     */
    public function destroy(string $id)
    {
        // Bisa diisi dengan logika untuk menghapus data pengguna berdasarkan ID
    }
}
