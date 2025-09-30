<?php

namespace App\Repositories;

use App\Models\GroupWeeklyOperationDetail;

class GroupWeeklyOperationDetailRepository implements GroupWeeklyOperationDetailRepositoryInterface
{
    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        return GroupWeeklyOperationDetail::create($data);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        $query = GroupWeeklyOperationDetail::query();
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->find($id, $columns);
    }

    public function findByOperation(int $groupWeeklyOperationId)
    {
        return GroupWeeklyOperationDetail::where('group_weekly_operation_id', $groupWeeklyOperationId)->get();
    }

    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model
    {
        $detail = GroupWeeklyOperationDetail::find($id);
        if ($detail) {
            $detail->update($data);
            return $detail->fresh();
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $detail = GroupWeeklyOperationDetail::find($id);
        if ($detail) {
            $detail->delete();
            return true;
        }
        return false;
    }
}
