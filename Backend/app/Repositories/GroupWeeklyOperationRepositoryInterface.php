<?php

namespace App\Repositories;

interface GroupWeeklyOperationRepositoryInterface
{
    public function create(array $data);
    public function find(int $id);
    public function findByGroupAndWeek(int $groupId, string $weekStartDate);
    public function update(int $id, array $data);
    public function delete(int $id);
}
