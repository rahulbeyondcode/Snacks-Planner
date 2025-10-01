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

class UserController extends BaseController
{
    // Update own profile
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $updatedUser = $this->userService->updateUser($user->user_id, $request->validated());

            return $this->updatedResponse(
                new UserResource($updatedUser),
                'Profile updated successfully'
            );
        }, null, 'Failed to update profile');
    }

    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    // List users (admin only)
    public function index(Request $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $filters = $request->only(['role_id', 'search']);
            $users = $this->userService->listUsers($filters);

            return $this->resourceCollectionResponse(UserResource::collection($users));
        }, 'account_manager', 'Failed to retrieve users');
    }

    // Show user details (admin only)
    public function show($id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            try {
                $userData = $this->userService->getUser($id);
                return $this->resourceResponse(new UserResource($userData));
            } catch (UserNotFoundException $e) {
                return $this->notFoundResponse($e->getMessage());
            } catch (UnauthorizedActionException $e) {
                return $this->forbiddenResponse($e->getMessage());
            }
        }, 'account_manager', 'Failed to retrieve user');
    }

    // Create user (admin only)
    public function store(StoreUserRequest $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            try {
                $validated = $request->validated();
                // Use env USER_PASSWORD or default to 'password'
                $defaultPassword = env('USER_PASSWORD', 'password');
                $validated['password'] = bcrypt($defaultPassword);
                $validated['role_id'] = 4; // Always assign Employee role
                $validated['preference'] = $validated['preference'] ?? 'all_snacks'; // Default to 'all_snacks' if not provided

                $created = $this->userService->createUser($validated);

                // Get all active users for the response
                $allUsers = $this->userService->listUsers();

                return $this->createdResponse(UserResource::collection($allUsers), 'User created successfully');
            } catch (UnauthorizedActionException $e) {
                return $this->forbiddenResponse($e->getMessage());
            }
        }, 'account_manager', 'Failed to create user');
    }

    // Update user (admin only)
    public function update(UpdateUserRequest $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            try {
                $validated = $request->validated();
                // Always set password using env USER_PASSWORD or default to 'password'
                $defaultPassword = env('USER_PASSWORD', 'password');
                $validated['password'] = bcrypt($defaultPassword);

                $updatedUser = $this->userService->updateUser($id, $validated);

                // Get all active users for the response
                $allUsers = $this->userService->listUsers();

                return $this->updatedResponse(UserResource::collection($allUsers), 'User updated successfully');
            } catch (UserNotFoundException $e) {
                return $this->notFoundResponse($e->getMessage());
            } catch (UnauthorizedActionException $e) {
                return $this->forbiddenResponse($e->getMessage());
            }
        }, 'account_manager', 'Failed to update user');
    }

    // Delete user (admin only)
    public function destroy($id)
    {
        return $this->executeWithAuth(function ($user) use ($id) {
            try {
                $deleted = $this->userService->deleteUser($id);

                // Get all active users for the response
                $allUsers = $this->userService->listUsers();

                return $this->successResponse('User deleted successfully', UserResource::collection($allUsers));
            } catch (UserNotFoundException $e) {
                return $this->notFoundResponse($e->getMessage());
            } catch (UnauthorizedActionException $e) {
                return $this->forbiddenResponse($e->getMessage());
            }
        }, 'account_manager', 'Failed to delete user');
    }

    // Assign role (admin only)
    public function assignRole(AssignUserRoleRequest $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            try {
                $validated = $request->validated();
                $updatedUser = $this->userService->assignRole($id, $validated['role_id']);

                return $this->updatedResponse(new UserResource($updatedUser), 'Role assigned successfully');
            } catch (UserNotFoundException $e) {
                return $this->notFoundResponse($e->getMessage());
            } catch (UnauthorizedActionException $e) {
                return $this->forbiddenResponse($e->getMessage());
            }
        }, 'account_manager', 'Failed to assign role');
    }
}
