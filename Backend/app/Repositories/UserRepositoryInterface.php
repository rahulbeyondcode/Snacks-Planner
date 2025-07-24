<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function assignRole(int $userId, int $roleId);
}
