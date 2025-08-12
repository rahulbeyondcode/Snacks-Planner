<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupMember;
use App\Repositories\SubGroupRepositoryInterface;

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
        $group = Group::where('group_status', 'active')->first();
        if (! $group) {
            return null;
        }

        // Validate date range
        if ($data['start_date'] >= $data['end_date']) {
            return response()->unprocessableEntity(__('sub_group.end_date_must_be_after_start_date'));
        }

        // Validate members exist if provided
        if (! empty($data['members'])) {
            $existingMembers = GroupMember::whereIn('user_id', $data['members'])->count();
            if ($existingMembers !== count($data['members'])) {
                return response()->unprocessableEntity(__('sub_group.some_users_do_not_exist'));
            }
        }

        return $this->subGroupRepository->create($data);
    }

    public function updateSubGroup(int $id, array $data)
    {
        $subGroup = $this->subGroupRepository->find($id);
        if (! $subGroup) {
            return null;
        }

        // Validate that the group exists if being updated
        if (isset($data['group_id'])) {
            $group = Group::find($data['group_id']);
            if (! $group) {
                return null;
            }
        }

        // Validate date range if dates are being updated
        if (isset($data['start_date']) && isset($data['end_date'])) {
            if ($data['start_date'] >= $data['end_date']) {
                return response()->unprocessableEntity(__('sub_group.end_date_must_be_after_start_date'));
            }
        }

        // Validate members exist if provided
        if (! empty($data['members'])) {
            $existingMembers = GroupMember::whereIn('user_id', $data['members'])->count();
            if ($existingMembers !== count($data['members'])) {
                return response()->unprocessableEntity(__('sub_group.some_users_do_not_exist'));
            }
        }

        return $this->subGroupRepository->update($id, $data);
    }

    public function deleteSubGroup(int $id)
    {
        $subGroup = $this->subGroupRepository->find($id);
        if (! $subGroup) {
            return null;
        }

        return $this->subGroupRepository->delete($id);
    }
}
