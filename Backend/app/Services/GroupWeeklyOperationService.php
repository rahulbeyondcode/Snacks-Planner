<?php

namespace App\Services;

use App\Repositories\GroupWeeklyOperationRepositoryInterface;
use App\Repositories\GroupWeeklyOperationDetailRepositoryInterface;

class GroupWeeklyOperationService extends BaseService
{
    protected $detailRepo;

    public function __construct(
        GroupWeeklyOperationRepositoryInterface $operationRepo,
        GroupWeeklyOperationDetailRepositoryInterface $detailRepo
    ) {
        $this->repository = $operationRepo;
        $this->detailRepo = $detailRepo;
    }

    public function assign(array $data)
    {
        $details = $data['details'] ?? [];
        unset($data['details']);
        $operation = $this->create($data);
        foreach ($details as $detail) {
            $detail['group_weekly_operation_id'] = $operation->group_weekly_operation_id;
            $this->detailRepo->create($detail);
        }
        return $this->find($operation->group_weekly_operation_id);
    }

    public function updateDetailStatus($detailId, $status)
    {
        return $this->detailRepo->update($detailId, ['status' => $status]);
    }

    public function listAssignments(array $filters = [])
    {
        // Implement filtering as needed
        return $this->repository->allWithRelations($filters);
    }

    public function getAssignment($id)
    {
        return $this->find($id);
    }
}
