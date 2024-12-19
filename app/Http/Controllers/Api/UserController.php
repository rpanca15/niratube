<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        $users = User::all();
        return $this->sendResponse($users, 'Users retrieved successfully.');
    }

    /**
     * Menampilkan data pengguna berdasarkan ID (untuk profil).
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found.', 404);
        }

        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    /**
     * Menyimpan data pengguna baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:user,admin',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);

        return $this->sendResponse($user, 'User created successfully.');
    }

    /**
     * Mengupdate data pengguna berdasarkan ID.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found.', 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|min:3|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|in:user,admin',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        // Update data pengguna
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return $this->sendResponse($user, 'User updated successfully.');
    }

    /**
     * Menghapus data pengguna berdasarkan ID.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found.', 404);
        }

        $user->delete();

        return $this->sendResponse([], 'User deleted successfully.');
    }

    /**
     * Menampilkan profil pengguna berdasarkan token yang sedang aktif.
     */
    public function profile(Request $request)
    {
        try {
            // Ambil pengguna yang sedang login
            $user = Auth::user();

            // Jika pengguna tidak ditemukan
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Return data pengguna
            return response()->json([
                'success' => true,
                'message' => 'User profile retrieved successfully.',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
