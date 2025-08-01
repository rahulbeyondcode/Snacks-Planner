<?php

namespace App\Http\Controllers;

use App\Services\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AssignUserRoleRequest;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    // Update own profile
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user->fill($request->validated());
        $user->save();
        return response()->json($user);
    }

    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    // List users (admin only)
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $filters = $request->only(['role_id', 'search']);
        $users = $this->userService->listUsers($filters);
        return response()->json($users);
    }

    // Show user details (admin only)
    public function show($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $userData = $this->userService->getUser($id);
        if (!$userData) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($userData);
    }

    // Create user (admin only)
    public function store(StoreUserRequest $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $validated['role_id'] = 4; // Always assign Employee role
        $created = $this->userService->createUser($validated);
        $users = $this->userService->listUsers([]);
        return response()->json([
            'message' => 'User created successfully',
            'users' => $users
        ], 201);
    }

    // Update user (admin only)
    public function update(UpdateUserRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validated();
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        $updatedUser = $this->userService->updateUser($id, $validated);
        if (!$updatedUser) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $users = $this->userService->listUsers([]);
        return response()->json([
            'message' => 'User updated successfully',
            'users' => $users
        ]);
    }

    // Delete user (admin only)
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $deleted = $this->userService->deleteUser($id);
        if (!$deleted) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $users = $this->userService->listUsers([]);
        return response()->json([
            'message' => 'User deleted successfully',
            'users' => $users
        ]);
    }

    // Assign role (admin only)
    public function assignRole(AssignUserRoleRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validated();
        $updatedUser = $this->userService->assignRole($id, $validated['role_id']);
        if (!$updatedUser) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($updatedUser);
    }
}
