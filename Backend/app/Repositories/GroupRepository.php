<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;

class GroupRepository implements GroupRepositoryInterface
{
    public function assignLeader(int $groupId, int $userId)
    {
        $group = Group::find($groupId);
        if ($group) {
            $group->operations_manager_id = $userId;
            $group->save();
            return $group;
        }
        return null;
    }

    public function all(array $filters = [])
    {
        $query = Group::select('group_id', 'name', 'description', 'sort_order')
            ->with(['groupMembers' => function ($query) {
                $query->select('group_member_id', 'user_id', 'role_id', 'group_id');
            }]);
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        return $query->orderBy('name')->get();
    }

    public function find(int $id)
    {
        return Group::select('group_id', 'name', 'description', 'sort_order')
            ->with(['groupMembers' => function ($query) {
                $query->select('group_member_id', 'user_id', 'role_id', 'group_id');
            }])
            ->find($id);
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $group = new Group;
            $group->name = $data['name'];
            $group->description = $data['description'] ?? '';
            $group->sort_order = $data['sort_order'] ?? 0;
            $group->save();

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
            $operationManagerIds = $data['operation_managers'] ?? [];
            if (!empty($operationManagerIds) && is_array($operationManagerIds)) {
                foreach ($operationManagerIds as $operation_manager_id) {
                    $groupMembers[] = [
                        'user_id' => $operation_manager_id,
                        'role_id' => Role::OPERATION_MANAGER,
                        'group_id' => $group->group_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Batch insert group members
            if (!empty($groupMembers)) {
                GroupMember::insert($groupMembers);
            }

            // Batch update roles for users
            if (!empty($employeeIds)) {
                User::whereIn('user_id', $employeeIds)->update(['role_id' => Role::EMPLOYEE]);
            }
            if (!empty($operationManagerIds)) {
                User::whereIn('user_id', $operationManagerIds)->update(['role_id' => Role::OPERATION_MANAGER]);
            }
            DB::commit();
            return $group;
        } catch (Exception $e) {
            dd("haii");
            DB::rollBack();
            throw $e;
        }
    }


    public function update(int $id, array $data)
    {
        $group = Group::find($id);
        if (!$group) {
            return null;
        }

        // Update the group basic info
        $group->update([
            'name' => $data['name'] ?? $group->name,
            'description' => $data['description'] ?? $group->description,
            'sort_order' => $data['sort_order'] ?? $group->sort_order,
        ]);

        $newEmployeeIds = $data['employees'] ?? [];
        $newManagerIds = $data['operation_managers'] ?? [];

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
            $newMap[$uid] = Role::OPERATION_MANAGER;
        }

        // Compare current and new map
        if ($currentMap != $newMap) {
            // Soft delete old members
            GroupMember::where('group_id', $group->group_id)->delete();

            // Reinsert new members
            $newMembers = [];

            foreach ($newEmployeeIds as $employee_id) {
                $newMembers[] = [
                    'user_id' => $employee_id,
                    'role_id' => Role::EMPLOYEE,
                    'group_id' => $group->group_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            foreach ($newManagerIds as $manager_id) {
                $newMembers[] = [
                    'user_id' => $manager_id,
                    'role_id' => Role::OPERATION_MANAGER,
                    'group_id' => $group->group_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            GroupMember::insert($newMembers);

            // Update user roles in batch
            if (!empty($newEmployeeIds)) {
                User::whereIn('user_id', $newEmployeeIds)->update(['role_id' => Role::EMPLOYEE]);
            }
            if (!empty($newManagerIds)) {
                User::whereIn('user_id', $newManagerIds)->update(['role_id' => Role::OPERATION_MANAGER]);
            }
        }

        return $group;
    }


    public function delete(int $id)
    {
        $group = Group::find($id);

        if (!$group) {
            return false; // or throw an exception if you prefer
        }

        // Delete related group members
        GroupMember::where('group_id', $group->group_id)->delete();

        // Delete the group
        $group->delete();

        return true;
    }

    public function addMembers(int $groupId, array $userIds)
    {
        $group = Group::find($groupId);
        if ($group) {
            $group->members()->syncWithoutDetaching($userIds);
            return $group->members()->get();
        }
        return null;
    }

    public function removeMembers(int $groupId, array $userIds)
    {
        $group = Group::find($groupId);
        if ($group) {
            $group->members()->detach($userIds);
            return $group->members()->get();
        }
        return null;
    }

    public function listMembers(int $groupId)
    {
        $group = Group::find($groupId);
        return $group ? $group->members()->get() : null;
    }
}
