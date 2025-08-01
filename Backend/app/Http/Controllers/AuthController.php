<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            $token = $user->createToken('auth-token')->plainTextToken;

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
                    'token' => $token,
                ],
                'status' => 200
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            'data' => null,
            'status' => 401
        ], 401);
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
            'data' => null,
            'status' => 200
        ], 200);
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
            'status' => 200
        ], 200);
    }
} 