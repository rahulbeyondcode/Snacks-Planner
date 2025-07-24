<?php

namespace App\Services;

interface UserServiceInterface
{
    public function listUsers(array $filters = []);
    public function getUser(int $id);
    public function createUser(array $data);
    public function updateUser(int $id, array $data);
    public function deleteUser(int $id);
    public function assignRole(int $userId, int $roleId);
}
