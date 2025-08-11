<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Repositories\SubGroupRepositoryInterface;
use Exception;

class SubGroupService implements SubGroupServiceInterface
{
    protected $subGroupRepository;

    public function __construct(SubGroupRepositoryInterface $subGroupRepository)
    {
        $this->subGroupRepository = $subGroupRepository;
    }

    public function listSubGroups(array $filters = [])
    {
        return $this->subGroupRepository->all($filters);
    }

    public function getSubGroup(int $id)
    {
        return $this->subGroupRepository->find($id);
    }

    public function createSubGroup(array $data)
    {
        // Validate that the group exists
        $group = Group::find($data['group_id']);
        if (! $group) {
            throw new Exception('Group not found');
        }

        // Validate date range
        if ($data['start_date'] >= $data['end_date']) {
            throw new Exception('End date must be after start date');
        }

        // Validate members exist if provided
        if (! empty($data['members'])) {
            $existingUsers = User::whereIn('user_id', $data['members'])->count();
            if ($existingUsers !== count($data['members'])) {
                throw new Exception('Some users do not exist');
            }
        }

        return $this->subGroupRepository->create($data);
    }

    public function updateSubGroup(int $id, array $data)
    {
        $subGroup = $this->subGroupRepository->find($id);
        if (! $subGroup) {
            throw new Exception('Sub group not found');
        }

        // Validate that the group exists if being updated
        if (isset($data['group_id'])) {
            $group = Group::find($data['group_id']);
            if (! $group) {
                throw new Exception('Group not found');
            }
        }

        // Validate date range if dates are being updated
        if (isset($data['start_date']) && isset($data['end_date'])) {
            if ($data['start_date'] >= $data['end_date']) {
                throw new Exception('End date must be after start date');
            }
        }

        // Validate members exist if provided
        if (isset($data['members']) && ! empty($data['members'])) {
            $existingUsers = User::whereIn('user_id', $data['members'])->count();
            if ($existingUsers !== count($data['members'])) {
                throw new Exception('Some users do not exist');
            }
        }

        return $this->subGroupRepository->update($id, $data);
    }

    public function deleteSubGroup(int $id)
    {
        $subGroup = $this->subGroupRepository->find($id);
        if (! $subGroup) {
            throw new Exception('Sub group not found');
        }

        return $this->subGroupRepository->delete($id);
    }

    public function addMembers(int $subGroupId, array $userIds)
    {
        $subGroup = $this->subGroupRepository->find($subGroupId);
        if (! $subGroup) {
            throw new Exception('Sub group not found');
        }

        // Validate that users exist
        $existingUsers = User::whereIn('user_id', $userIds)->count();
        if ($existingUsers !== count($userIds)) {
            throw new Exception('Some users do not exist');
        }

        return $this->subGroupRepository->addMembers($subGroupId, $userIds);
    }

    public function removeMembers(int $subGroupId, array $userIds)
    {
        $subGroup = $this->subGroupRepository->find($subGroupId);
        if (! $subGroup) {
            throw new Exception('Sub group not found');
        }

        return $this->subGroupRepository->removeMembers($subGroupId, $userIds);
    }

    public function listMembers(int $subGroupId)
    {
        $subGroup = $this->subGroupRepository->find($subGroupId);
        if (! $subGroup) {
            throw new Exception('Sub group not found');
        }

        return $this->subGroupRepository->listMembers($subGroupId);
    }

    public function getByGroup(int $groupId)
    {
        $group = Group::find($groupId);
        if (! $group) {
            throw new Exception('Group not found');
        }

        return $this->subGroupRepository->getByGroup($groupId);
    }
}
