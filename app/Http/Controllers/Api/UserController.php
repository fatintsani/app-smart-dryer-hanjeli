<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List all users
     */
    public function index(): JsonResponse
    {
        $users = User::latest()->get()->map(fn (User $u) => $u->toAuthPayload());

        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Create a new user (Admin action)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|min:3|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string|in:ADMIN,OPERATOR',
            'phone' => 'nullable|string|max:30',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
        ]);

        $username = !empty($validated['username'])
            ? strtolower(trim($validated['username']))
            : strtolower(explode('@', $validated['email'])[0]);

        if (empty($validated['username'])) {
            $baseUsername = preg_replace('/[^a-z0-9_-]/', '', $username) ?: 'user';
            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => strtoupper($validated['role'] ?? 'OPERATOR'),
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Pengguna baru berhasil ditambahkan.',
            'user' => $user->toAuthPayload(),
        ], 201);
    }

    /**
     * Update user details (Admin action)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|min:3|max:50|alpha_dash|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:ADMIN,OPERATOR',
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => strtoupper($validated['role']),
        ];

        if (isset($validated['username'])) {
            $updateData['username'] = strtolower(trim($validated['username']));
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return response()->json([
            'message' => "Data pengguna '{$user->name}' berhasil diperbarui.",
            'user' => $user->toAuthPayload(),
        ]);
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|string|in:ADMIN,OPERATOR',
        ]);

        $user = User::findOrFail($id);
        $user->update(['role' => strtoupper($validated['role'])]);

        return response()->json([
            'message' => 'Role pengguna berhasil diperbarui.',
            'user' => $user->toAuthPayload(),
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($request->user() && $request->user()->id == $user->id) {
            return response()->json([
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }
}
