<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\Role;
use App\Repositories\Traits\TransactionHelperTrait;
use App\Repositories\Traits\BatchOperationTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class GroupRepository extends BaseRepository implements GroupRepositoryInterface
{
    use TransactionHelperTrait, BatchOperationTrait;

    public function __construct(Group $model)
    {
        parent::__construct($model);
    }

    /**
     * Apply filters to group query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
    }

    public function assignLeader(int $groupId, int $userId)
    {
        $group = $this->find($groupId);
        if ($group) {
            $group->operations_manager_id = $userId;
            $group->save();
            return $group;
        }
        return null;
    }

    public function all(array $columns = ['*'], array $relations = [], array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->model->select(['group_id', 'name', 'description', 'sort_order'])
            ->with(['groupMembers' => function ($query) {
                $query->select('group_member_id', 'user_id', 'role_id', 'group_id');
            }]);

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->orderBy('name')->get();
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->model->select(['group_id', 'name', 'description', 'sort_order'])
            ->with(['groupMembers' => function ($query) {
                $query->select('group_member_id', 'user_id', 'role_id', 'group_id');
            }])
            ->find($id);
    }

    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {        
        return $this->executeInTransaction(function () use ($data) {
            $group = new Group;
            $group->name = $data['name'];
            $group->description = $data['description'] ?? '';
            $group->sort_order = $data['sort_order'] ?? 0;
            $group->save();

            $this->addGroupMembers($group, $data);

            return $group->load('groupMembers');
        });
    }

    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model
    {
        $group = $this->find($id);
        if (!$group) {
            return null;
        }

        return $this->executeInTransaction(function () use ($group, $data) {
            // Update the group basic info
            $group->update([
                'name' => $data['name'] ?? $group->name,
                'description' => $data['description'] ?? $group->description,
                'sort_order' => $data['sort_order'] ?? $group->sort_order,
            ]);

            $this->updateGroupMembers($group, $data);

            return $group;
        });
    }

    public function delete(int $id): bool
    {
        return $this->executeInTransaction(function () use ($id) {
            $group = $this->find($id);

            if (!$group) {
                return false;
            }

            // Delete related group members
            GroupMember::where('group_id', $group->group_id)->delete();

            // Delete the group
            $group->delete();

            return true;
        });
    }

    public function addMembers(int $groupId, array $userIds)
    {
        $group = $this->find($groupId);
        if ($group) {
            $group->members()->syncWithoutDetaching($userIds);
            return $group->members()->get();
        }
        return null;
    }

    public function removeMembers(int $groupId, array $userIds)
    {
        $group = $this->find($groupId);
        if ($group) {
            $group->members()->detach($userIds);
            return $group->members()->get();
        }
        return null;
    }

    public function listMembers(int $groupId)
    {
        $group = $this->find($groupId);
        return $group ? $group->members()->get() : null;
    }

    /**
     * Add group members during creation
     */
    private function addGroupMembers(Group $group, array $data): void
    {
        $groupMembers = [];

        // Add employees as group members
        $employeeIds = $data['employees'] ?? [];
        if (!empty($employeeIds) && is_array($employeeIds)) {
            foreach ($employeeIds as $employee_id) {
                $groupMembers[] = [
                    'user_id' => $employee_id,
                    'role_id' => Role::EMPLOYEE,
                    'group_id' => $group->group_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Add operation managers as group members
        $operationManagerIds = $data['snack_managers'] ?? [];
        if (!empty($operationManagerIds) && is_array($operationManagerIds)) {
            foreach ($operationManagerIds as $snack_manager_id) {
                $groupMembers[] = [
                    'user_id' => $snack_manager_id,
                    'role_id' => Role::SNACK_MANAGER,
                    'group_id' => $group->group_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Batch insert group members
        if (!empty($groupMembers)) {
            $this->batchInsert($groupMembers, 'group_members');
        }

        // Batch update roles for users
        if (!empty($employeeIds)) {
            User::whereIn('user_id', $employeeIds)->update(['role_id' => Role::EMPLOYEE]);
        }
        if (!empty($operationManagerIds)) {
            User::whereIn('user_id', $operationManagerIds)->update(['role_id' => Role::SNACK_MANAGER]);
        }
    }

    /**
     * Update group members during update
     */
    private function updateGroupMembers(Group $group, array $data): void
    {
        $newEmployeeIds = $data['employees'] ?? [];
        $newManagerIds = $data['snack_managers'] ?? [];

        // Get current group members
        $currentMembers = GroupMember::where('group_id', $group->group_id)->get();

        $currentMap = $currentMembers->mapWithKeys(function ($member) {
            return [$member->user_id => $member->role_id];
        });

        // Prepare new map
        $newMap = collect();

        foreach ($newEmployeeIds as $uid) {
            $newMap[$uid] = Role::EMPLOYEE;
        }

        foreach ($newManagerIds as $uid) {
            $newMap[$uid] = Role::SNACK_MANAGER;
        }

        // Compare current and new map
        if ($currentMap != $newMap) {
            // Soft delete old members
            GroupMember::where('group_id', $group->group_id)->delete();

            // Reinsert new members
            $this->addGroupMembers($group, $data);
        }
    }
}
