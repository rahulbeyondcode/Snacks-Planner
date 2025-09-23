<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
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
            /** @var \App\Models\User $user */
            $user = Auth::user();
            session()->regenerate();

            // Load user's role and permissions
            $user->load('role.permissions');

            return $this->successResponse('Login successful', [
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
            ]);
        }

        return $this->errorResponse('Invalid credentials', null, 401);
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->noContentResponse();
    }

    /**
     * Get current user profile with permissions
     */
    public function profile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->load('role.permissions');

        return $this->successResponse('Profile retrieved successfully', [
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
        ]);
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

        return $this->successResponse(__('messages.update_msg'));
    }
}
