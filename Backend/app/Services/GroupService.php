<?php

namespace App\Services;

use App\Repositories\GroupRepositoryInterface;

class GroupService implements GroupServiceInterface
{
    public function assignLeader(int $groupId, int $userId)
    {
        return $this->groupRepository->assignLeader($groupId, $userId);
    }

    protected $groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function listGroups(array $filters = [])
    {
        return $this->groupRepository->all($filters);
    }

    public function getGroup(int $id)
    {
        return $this->groupRepository->find($id);
    }

    public function createGroup(array $data)
    {
        return $this->groupRepository->create($data);
    }

    public function updateGroup(int $id, array $data)
    {
        return $this->groupRepository->update($id, $data);
    }

    public function deleteGroup(int $id)
    {
        return $this->groupRepository->delete($id);
    }

    public function addMembers(int $groupId, array $userIds)
    {
        return $this->groupRepository->addMembers($groupId, $userIds);
    }

    public function removeMembers(int $groupId, array $userIds)
    {
        return $this->groupRepository->removeMembers($groupId, $userIds);
    }

    public function listMembers(int $groupId)
    {
        return $this->groupRepository->listMembers($groupId);
    }
}
