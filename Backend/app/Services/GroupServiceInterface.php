<?php

namespace App\Services;

interface GroupServiceInterface
{
    public function assignLeader(int $groupId, int $userId);

    public function listGroups(array $filters = []);
    public function getGroup(int $id);
    public function createGroup(array $data);
    public function updateGroup(int $id, array $data);
    public function deleteGroup(int $id);
    public function addMembers(int $groupId, array $userIds);
    public function removeMembers(int $groupId, array $userIds);
    public function listMembers(int $groupId);
}
