<?php

namespace App\Repositories;

interface GroupWeeklyOperationDetailRepositoryInterface
{
    public function create(array $data);
    public function find(int $id);
    public function findByOperation(int $groupWeeklyOperationId);
    public function update(int $id, array $data);
    public function delete(int $id);
}
