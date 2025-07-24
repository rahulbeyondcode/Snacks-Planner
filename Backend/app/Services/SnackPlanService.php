<?php

namespace App\Services;

use App\Repositories\SnackPlanRepositoryInterface;
use App\Repositories\SnackPlanDetailRepositoryInterface;

interface SnackPlanServiceInterface
{
    public function planSnack(array $data);
    public function getSnackPlan(int $id);
    public function planFullSnackDay(array $planData, array $snackItems);
    public function listSnackPlans(array $filters = []);
    public function updateSnackPlan(int $id, array $data);
    public function deleteSnackPlan(int $id);
}

class SnackPlanService implements SnackPlanServiceInterface
{
    protected $snackPlanRepository;
    protected $snackPlanDetailRepository;

    public function __construct(
        SnackPlanRepositoryInterface $snackPlanRepository,
        SnackPlanDetailRepositoryInterface $snackPlanDetailRepository
    ) {
        $this->snackPlanRepository = $snackPlanRepository;
        $this->snackPlanDetailRepository = $snackPlanDetailRepository;
    }

    public function planSnack(array $data)
    {
        // Business logic for planning a snack
        return $this->snackPlanRepository->create($data);
    }

    public function getSnackPlan(int $id)
    {
        // Business logic for retrieving a snack plan with details
        $plan = $this->snackPlanRepository->find($id);
        if ($plan) {
            $plan->details = $this->snackPlanDetailRepository->findByPlanId($id);
        }
        return $plan;
    }

    public function planFullSnackDay(array $planData, array $snackItems)
    {
        // Create the main snack plan
        $snackPlan = $this->snackPlanRepository->create($planData);
        $planId = $snackPlan->snack_plan_id;
        $details = [];
        foreach ($snackItems as $item) {
            $item['snack_plan_id'] = $planId;
            $details[] = $this->snackPlanDetailRepository->create($item);
        }
        $snackPlan->details = $details;
        return $snackPlan;
    }

    public function listSnackPlans(array $filters = [])
    {
        return $this->snackPlanRepository->list($filters);
    }

    public function updateSnackPlan(int $id, array $data)
    {
        return $this->snackPlanRepository->update($id, $data);
    }

    public function deleteSnackPlan(int $id)
    {
        return $this->snackPlanRepository->delete($id);
    }
}
