<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\SubGroup;
use App\Models\SubGroupMember;
use Exception;
use Illuminate\Support\Facades\DB;

class SubGroupRepository implements SubGroupRepositoryInterface
{
    public function all(array $filters = [])
    {
        // Validate that the group exists
        $group = Group::where('group_status', 'active')->first();
        if (! $group) {
            return null;
        }

        $currentMonth = now()->format('Y-m');

        $query = SubGroup::select('sub_group_id', 'group_id', 'name', 'start_date', 'end_date', 'status')
            ->with(['subGroupMembers'])
            ->whereRaw('DATE_FORMAT(start_date, "%Y-%m") = ?', [$currentMonth])
            ->orWhereRaw('DATE_FORMAT(end_date, "%Y-%m") = ?', [$currentMonth])
            ->orWhere(function ($query) {
                $query->where('start_date', '<=', now()->startOfMonth())
                    ->where('end_date', '>=', now()->endOfMonth());
            })
            ->orderBy('start_date', 'asc')
            ->where('group_id', $group->group_id)
            ->get();

        return $query;
    }

    public function find(int $id)
    {
        return SubGroup::select('sub_group_id', 'group_id', 'name', 'start_date', 'end_date', 'status')
            ->with(['subGroupMembers'])
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

            return $subGroup->load('subGroupMembers');
        } catch (Exception $e) {
            DB::rollback();

            return null;
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

            return $subGroup->load('subGroupMembers');
        } catch (Exception $e) {
            DB::rollback();

            return null;
        }
    }

    public function delete(int $id)
    {
        try {
            DB::beginTransaction();

            // Remove all members first
            SubGroupMember::where('sub_group_id', $id)->delete();

            // Delete the sub group
            SubGroup::where('sub_group_id', $id)->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();

            return null;
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
}
