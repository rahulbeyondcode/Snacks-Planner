<?php

namespace App\Repositories;

use App\Models\GroupWeeklyOperation;

class GroupWeeklyOperationRepository implements GroupWeeklyOperationRepositoryInterface
{
    public function allWithRelations(array $filters = [])
    {
        $query = GroupWeeklyOperation::with(['group', 'employee', 'assignedBy', 'details']);
        if (isset($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (isset($filters['week_start_date'])) {
            $query->where('week_start_date', $filters['week_start_date']);
        }
        return $query->orderByDesc('created_at')->get();
    }

    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        return GroupWeeklyOperation::create($data);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        $defaultRelations = ['details'];
        $relations = !empty($relations) ? $relations : $defaultRelations;
        
        return GroupWeeklyOperation::with($relations)->find($id, $columns);
    }

    public function findByGroupAndWeek(int $groupId, string $weekStartDate)
    {
        return GroupWeeklyOperation::where('group_id', $groupId)
            ->where('week_start_date', $weekStartDate)
            ->with('details')
            ->first();
    }

    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model
    {
        $operation = GroupWeeklyOperation::find($id);
        if ($operation) {
            $operation->update($data);
            return $operation->fresh();
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $operation = GroupWeeklyOperation::find($id);
        if ($operation) {
            $operation->delete();
            return true;
        }
        return false;
    }
}
