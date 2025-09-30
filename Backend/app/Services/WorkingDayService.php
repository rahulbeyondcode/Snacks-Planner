<?php
namespace App\Services;

use App\Repositories\WorkingDayRepositoryInterface;

class WorkingDayService extends BaseService
{
    public function __construct(WorkingDayRepositoryInterface $workingDayRepo)
    {
        $this->repository = $workingDayRepo;
    }

    public function getCurrent()
    {
        return $this->repository->getCurrent();
    }

    public function updateWorkingDays(array $days, $userId)
    {
        return $this->repository->update($days, $userId);
    }
}
