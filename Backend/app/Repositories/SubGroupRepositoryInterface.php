<?php

namespace App\Repositories;

interface SubGroupRepositoryInterface
{
    public function all(array $filters = []);

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function addMembers(int $subGroupId, array $userIds);

    public function removeMembers(int $subGroupId, array $userIds);

    public function listMembers(int $subGroupId);

    public function getByGroup(int $groupId);
}
