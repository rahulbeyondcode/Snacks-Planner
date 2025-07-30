<?php
namespace App\Services;

use App\Repositories\WorkingDayRepositoryInterface;

class WorkingDayService
{
    protected $workingDayRepo;

    public function __construct(WorkingDayRepositoryInterface $workingDayRepo)
    {
        $this->workingDayRepo = $workingDayRepo;
    }

    public function getCurrent()
    {
        return $this->workingDayRepo->getCurrent();
    }

    public function update(array $days, $userId)
    {
        return $this->workingDayRepo->update($days, $userId);
    }
}
