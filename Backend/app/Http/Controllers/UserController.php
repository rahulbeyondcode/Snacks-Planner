<?php

namespace App\Http\Controllers;

use App\Services\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AssignUserRoleRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\UnauthorizedActionException;
use App\Exceptions\UserNotFoundException;

class UserController extends Controller
{
    // Update own profile
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return apiResponse(
                    false,
                    'Authentication required to update profile',
                    [],
                    401
                );
            }

            $updatedUser = $this->userService->updateUser($user->user_id, $request->validated());

            return apiResponse(
                true,
                'Profile updated successfully',
                $updatedUser,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to update profile: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    // List users (admin only)
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role->name !== 'account_manager') {
                return apiResponse(
                    false,
                    'Access denied. Only account managers can list users.',
                    [],
                    403
                );
            }

            $filters = $request->only(['role_id', 'search']);
            $users = $this->userService->listUsers($filters);

            return apiResponse(
                true,
                'Users retrieved successfully',
                $users,
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve users: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Show user details (admin only)
    public function show($id)
    {
        try {
            $userData = $this->userService->getUser($id);

            return apiResponse(
                true,
                'User details retrieved successfully',
                $userData,
                200
            );
        } catch (UserNotFoundException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                404
            );
        } catch (UnauthorizedActionException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                403
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve user details: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Create user (admin only)
    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = bcrypt($validated['password']);
            $validated['role_id'] = 4; // Always assign Employee role
            $validated['preference'] = $validated['preference'] ?? 'all_snacks'; // Default to 'all_snacks' if not provided

            $created = $this->userService->createUser($validated);
            $users = $this->userService->listUsers([]);

            return apiResponse(
                true,
                'User created successfully',
                [
                    'user' => $created,
                    'users' => $users
                ],
                201
            );
        } catch (UnauthorizedActionException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                403
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to create user: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Update user (admin only)
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            $updatedUser = $this->userService->updateUser($id, $validated);
            $users = $this->userService->listUsers([]);

            return apiResponse(
                true,
                'User updated successfully',
                [
                    'user' => $updatedUser,
                    'users' => $users
                ],
                200
            );
        } catch (UserNotFoundException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                404
            );
        } catch (UnauthorizedActionException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                403
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to update user: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Delete user (admin only)
    public function destroy($id)
    {
        try {
            $deleted = $this->userService->deleteUser($id);
            $users = $this->userService->listUsers([]);

            return apiResponse(
                true,
                'User deleted successfully',
                $users,
                200
            );
        } catch (UserNotFoundException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                404
            );
        } catch (UnauthorizedActionException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                403
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to delete user: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Assign role (admin only)
    public function assignRole(AssignUserRoleRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $updatedUser = $this->userService->assignRole($id, $validated['role_id']);

            return apiResponse(
                true,
                'Role assigned successfully',
                $updatedUser,
                200
            );
        } catch (UserNotFoundException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                404
            );
        } catch (UnauthorizedActionException $e) {
            return apiResponse(
                false,
                $e->getMessage(),
                [],
                403
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to assign role: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
}
