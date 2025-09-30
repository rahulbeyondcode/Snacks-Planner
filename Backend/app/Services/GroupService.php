<?php

namespace App\Services;

use App\Repositories\GroupRepositoryInterface;

class GroupService extends BaseService implements GroupServiceInterface
{
    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->repository = $groupRepository;
    }

    public function listGroups(array $filters = [])
    {
        return $this->all($filters);
    }

    public function getGroup(int $id)
    {
        return $this->find($id);
    }

    public function createGroup(array $data)
    {
        return $this->create($data);
    }

    public function updateGroup(int $id, array $data)
    {
        return $this->update($id, $data);
    }

    public function deleteGroup(int $id)
    {
        return $this->delete($id);
    }

    public function assignLeader(int $groupId, int $userId)
    {
        return $this->repository->assignLeader($groupId, $userId);
    }

    public function addMembers(int $groupId, array $userIds)
    {
        return $this->repository->addMembers($groupId, $userIds);
    }

    public function removeMembers(int $groupId, array $userIds)
    {
        return $this->repository->removeMembers($groupId, $userIds);
    }

    public function listMembers(int $groupId)
    {
        return $this->repository->listMembers($groupId);
    }
}
