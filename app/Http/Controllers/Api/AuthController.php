<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

        // Kembalikan respons tanpa token
        return $this->sendResponse(
            ['user' => $user],
            'User registered successfully.'
        );
    }

    /**
     * Logout dan hapus semua token pengguna.
     */
    public function logout(Request $request)
    {
        // Periksa apakah user tersedia
        if ($user = $request->user()) {
            // Hapus semua token yang terkait dengan user
            $user->tokens->each(function ($token) {
                $token->delete();
            });
        }

        return response()->json([
            'message' => 'You have been successfully logged out.',
        ], 200);
    }
}
