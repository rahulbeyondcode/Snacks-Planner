<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use App\Exceptions\UnauthorizedActionException;
use App\Exceptions\UserNotFoundException;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function listUsers(array $filters = [])
    {
        // Apply business rule: exclude account managers from general user listings
        $filters['exclude_roles'] = ['account_manager'];
        return $this->userRepository->all($filters);
    }

    public function getUser(int $id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        // Business rule: account managers can only be accessed by other account managers
        if ($user->role && $user->role->name === 'account_manager') {
            $currentUser = Auth::user();
            if (!$currentUser || !$currentUser->role || $currentUser->role->name !== 'account_manager') {
                throw new UnauthorizedActionException('Access denied to account manager users');
            }
        }

        return $user;
    }

    public function createUser(array $data)
    {
        // Business rule: cannot create account manager users through normal flow
        if (isset($data['role_id']) && $data['role_id'] == Role::ACCOUNT_MANAGER) {
            $currentUser = Auth::user();
            if (!$currentUser || !$currentUser->role || $currentUser->role->name !== 'account_manager') {
                throw new UnauthorizedActionException('Only account managers can create account manager users');
            }
        }

        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        // Business rule: account managers can only be updated by other account managers
        if ($user->role && $user->role->name === 'account_manager') {
            $currentUser = Auth::user();
            if (!$currentUser || !$currentUser->role || $currentUser->role->name !== 'account_manager') {
                throw new UnauthorizedActionException('Only account managers can update account manager users');
            }
        }

        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        // Business rule: account managers cannot be deleted through normal flow
        if ($user->role && $user->role->name === 'account_manager') {
            throw new UnauthorizedActionException('Account manager users cannot be deleted');
        }

        return $this->userRepository->delete($id);
    }

    public function assignRole(int $userId, int $roleId)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        // Business rule: account manager role changes require special permissions
        $isAccountManagerRole = $roleId == Role::ACCOUNT_MANAGER;
        $userIsAccountManager = $user->role && $user->role->name === 'account_manager';

        if ($isAccountManagerRole || $userIsAccountManager) {
            $currentUser = Auth::user();
            if (!$currentUser || !$currentUser->role || $currentUser->role->name !== 'account_manager') {
                throw new UnauthorizedActionException('Only account managers can assign or change account manager roles');
            }
        }

        return $this->userRepository->assignRole($userId, $roleId);
    }
}
