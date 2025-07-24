<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function listUsers(array $filters = [])
    {
        return $this->userRepository->all($filters);
    }

    public function getUser(int $id)
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data)
    {
        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data)
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id)
    {
        return $this->userRepository->delete($id);
    }

    public function assignRole(int $userId, int $roleId)
    {
        return $this->userRepository->assignRole($userId, $roleId);
    }
}
