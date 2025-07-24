<?php

namespace App\Repositories;

use App\Models\GroupWeeklyOperationDetail;

class GroupWeeklyOperationDetailRepository implements GroupWeeklyOperationDetailRepositoryInterface
{
    public function create(array $data)
    {
        return GroupWeeklyOperationDetail::create($data);
    }

    public function find(int $id)
    {
        return GroupWeeklyOperationDetail::find($id);
    }

    public function findByOperation(int $groupWeeklyOperationId)
    {
        return GroupWeeklyOperationDetail::where('group_weekly_operation_id', $groupWeeklyOperationId)->get();
    }

    public function update(int $id, array $data)
    {
        $detail = GroupWeeklyOperationDetail::find($id);
        if ($detail) {
            $detail->update($data);
        }
        return $detail;
    }

    public function delete(int $id)
    {
        $detail = GroupWeeklyOperationDetail::find($id);
        if ($detail) {
            $detail->delete();
            return true;
        }
        return false;
    }
}
