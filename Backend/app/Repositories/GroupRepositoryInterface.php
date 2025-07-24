<?php

namespace App\Repositories;

interface GroupRepositoryInterface
{
    public function assignLeader(int $groupId, int $userId);

    public function all(array $filters = []);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function addMembers(int $groupId, array $userIds);
    public function removeMembers(int $groupId, array $userIds);
    public function listMembers(int $groupId);
}
