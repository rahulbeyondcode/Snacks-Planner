<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function all(array $columns = ['*'], array $relations = [], array $filters = []): \Illuminate\Database\Eloquent\Collection;
    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model;
    public function create(array $data): \Illuminate\Database\Eloquent\Model;
    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model;
    public function delete(int $id): bool;
    public function assignRole(int $userId, int $roleId);
}
