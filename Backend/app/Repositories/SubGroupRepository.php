<?php

namespace App\Repositories;

use App\Models\SubGroup;
use App\Models\SubGroupMember;
use Exception;
use Illuminate\Support\Facades\DB;

class SubGroupRepository implements SubGroupRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = SubGroup::select('sub_group_id', 'group_id', 'name', 'start_date', 'end_date', 'status')
            ->with(['group:id,name', 'subGroupMembers.user:id,name,email']);

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        if (! empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function find(int $id)
    {
        return SubGroup::select('sub_group_id', 'group_id', 'name', 'start_date', 'end_date', 'status')
            ->with(['group:id,name', 'subGroupMembers.user:id,name,email'])
            ->find($id);
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $subGroup = new SubGroup;
            $subGroup->group_id = $data['group_id'];
            $subGroup->name = $data['name'];
            $subGroup->start_date = $data['start_date'];
            $subGroup->end_date = $data['end_date'];
            $subGroup->status = $data['status'] ?? 'inactive';
            $subGroup->save();

            // Add members if provided
            if (! empty($data['members']) && is_array($data['members'])) {
                $this->addMembers($subGroup->sub_group_id, $data['members']);
            }

            DB::commit();

            return $subGroup;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            $subGroup = SubGroup::find($id);
            if (! $subGroup) {
                return null;
            }

            $subGroup->group_id = $data['group_id'] ?? $subGroup->group_id;
            $subGroup->name = $data['name'] ?? $subGroup->name;
            $subGroup->start_date = $data['start_date'] ?? $subGroup->start_date;
            $subGroup->end_date = $data['end_date'] ?? $subGroup->end_date;
            $subGroup->status = $data['status'] ?? $subGroup->status;
            $subGroup->save();

            // Update members if provided
            if (isset($data['members']) && is_array($data['members'])) {
                // Remove existing members
                SubGroupMember::where('sub_group_id', $id)->delete();
                // Add new members
                if (! empty($data['members'])) {
                    $this->addMembers($id, $data['members']);
                }
            }

            DB::commit();

            return $subGroup;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        try {
            DB::beginTransaction();

            $subGroup = SubGroup::find($id);
            if (! $subGroup) {
                return false;
            }

            // Remove all members first
            SubGroupMember::where('sub_group_id', $id)->delete();

            // Delete the sub group
            $subGroup->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function addMembers(int $subGroupId, array $userIds)
    {
        $subGroupMembers = [];
        foreach ($userIds as $userId) {
            $subGroupMembers[] = [
                'sub_group_id' => $subGroupId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (! empty($subGroupMembers)) {
            SubGroupMember::insert($subGroupMembers);
        }

        return true;
    }

    public function removeMembers(int $subGroupId, array $userIds)
    {
        return SubGroupMember::where('sub_group_id', $subGroupId)
            ->whereIn('user_id', $userIds)
            ->delete();
    }

    public function listMembers(int $subGroupId)
    {
        return SubGroupMember::where('sub_group_id', $subGroupId)
            ->with('user:id,name,email')
            ->get();
    }

    public function getByGroup(int $groupId)
    {
        return SubGroup::where('group_id', $groupId)
            ->with(['subGroupMembers.user:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
