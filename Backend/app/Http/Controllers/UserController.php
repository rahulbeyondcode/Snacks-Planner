<?php

namespace App\Http\Controllers;

use App\Services\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AssignUserRoleRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UnauthorizedActionException;
use App\Exceptions\UserNotFoundException;

class UserController extends Controller
{
    // Update own profile
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required to update profile',
                'data' => []
            ], 401);
        }

        $updatedUser = $this->userService->updateUser($user->user_id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserResource($updatedUser)
        ]);
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
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only account managers can list users.',
                'data' => []
            ], 403);
        }

        $filters = $request->only(['role_id', 'search']);
        $users = $this->userService->listUsers($filters);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users)
        ]);
    }

    // Show user details (admin only)
    public function show($id)
    {
        try {
            $userData = $this->userService->getUser($id);

            return response()->json([
                'success' => true,
                'data' => new UserResource($userData)
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 404);
        } catch (UnauthorizedActionException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 403);
        }
    }

    // Create user (admin only)
    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();
            // Use env USER_PASSWORD or default to 'password'
            $defaultPassword = env('USER_PASSWORD', 'password');
            $validated['password'] = bcrypt($defaultPassword);
            $validated['role_id'] = 4; // Always assign Employee role
            $validated['preference'] = $validated['preference'] ?? 'all_snacks'; // Default to 'all_snacks' if not provided

            $created = $this->userService->createUser($validated);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($created)
            ], 201);
        } catch (UnauthorizedActionException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 403);
        }
    }

    // Update user (admin only)
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            // Always set password using env USER_PASSWORD or default to 'password'
            $defaultPassword = env('USER_PASSWORD', 'password');
            $validated['password'] = bcrypt($defaultPassword);

            $updatedUser = $this->userService->updateUser($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($updatedUser)
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 404);
        } catch (UnauthorizedActionException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 403);
        }
    }

    // Delete user (admin only)
    public function destroy($id)
    {
        try {
            $deleted = $this->userService->deleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => []
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 404);
        } catch (UnauthorizedActionException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 403);
        }
    }

    // Assign role (admin only)
    public function assignRole(AssignUserRoleRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $updatedUser = $this->userService->assignRole($id, $validated['role_id']);

            return response()->json([
                'success' => true,
                'message' => 'Role assigned successfully',
                'data' => new UserResource($updatedUser)
            ]);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 404);
        } catch (UnauthorizedActionException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 403);
        }
    }
}
