<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\User;

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
        $query = Group::query();
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        return $query->orderBy('name')->get();
    }

    public function find(int $id)
    {
        return Group::find($id);
    }

    public function create(array $data)
    {
        return Group::create($data);
    }

    public function update(int $id, array $data)
    {
        $group = Group::find($id);
        if ($group) {
            $group->update($data);
        }
        return $group;
    }

    public function delete(int $id)
    {
        $group = Group::find($id);
        if ($group) {
            $group->delete();
            return true;
        }
        return false;
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
