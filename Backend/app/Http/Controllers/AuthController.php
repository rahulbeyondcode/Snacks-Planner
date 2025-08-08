<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * User login with permissions
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($validated)) {
            $user = Auth::user();
            session()->regenerate();

            // Load user's role and permissions
            $user->load('role.permissions');

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'user_id' => $user->user_id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => [
                            'role_id' => $user->role->role_id,
                            'name' => $user->role->name,
                            'description' => $user->role->description,
                        ],
                        'permissions' => $user->getPermissionsByModule(),
                    ],
                ],
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            'data' => null,
            'status' => 401,
        ], 401);
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }

    /**
     * Get current user profile with permissions
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load('role.permissions');

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => [
                        'role_id' => $user->role->role_id,
                        'name' => $user->role->name,
                        'description' => $user->role->description,
                    ],
                    'permissions' => $user->getPermissionsByModule(),
                ],
            ],
            'status' => 200,
        ], 200);
    }

    /**
     * User reset password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->message(__('messages.update_msg'));
    }
}
