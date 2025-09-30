<?php

namespace App\Services;

use App\Repositories\SnackPlanRepositoryInterface;
use App\Repositories\SnackPlanDetailRepositoryInterface;
use App\Repositories\Traits\TransactionHelperTrait;

interface SnackPlanServiceInterface
{
    public function planSnack(array $data);
    public function getSnackPlan(int $id);
    public function planFullSnackDay(array $planData, array $snackItems);
    public function listSnackPlans(array $filters = []);
    public function updateSnackPlan(int $id, array $planData, array $snackItems = []);
    public function deleteSnackPlan(int $id);
}

class SnackPlanService extends BaseService implements SnackPlanServiceInterface
{
    protected $snackPlanDetailRepository;

    public function __construct(
        SnackPlanRepositoryInterface $snackPlanRepository,
        SnackPlanDetailRepositoryInterface $snackPlanDetailRepository
    ) {
        $this->repository = $snackPlanRepository;
        $this->snackPlanDetailRepository = $snackPlanDetailRepository;
    }

    public function planSnack(array $data)
    {
        // Business logic for planning a snack
        return $this->create($data);
    }

    public function getSnackPlan(int $id)
    {
        // Business logic for retrieving a snack plan with details
        $plan = $this->find($id);
        if ($plan) {
            $plan->details = $this->snackPlanDetailRepository->findByPlanId($id);
        }
        return $plan;
    }

    public function planFullSnackDay(array $planData, array $snackItems)
    {
        // Create the main snack plan with details within a transaction
        return $this->executeInTransaction(function () use ($planData, $snackItems) {
            $snackPlan = $this->create($planData);
            $planId = $snackPlan->snack_plan_id;
            $details = [];
            foreach ($snackItems as $item) {
                $item['snack_plan_id'] = $planId;
                $details[] = $this->snackPlanDetailRepository->create($item);
            }
            $snackPlan->details = $details;
            return $snackPlan;
        });
    }

    public function listSnackPlans(array $filters = [])
    {
        $plans = $this->list($filters);
        
        // Load details for each plan
        foreach ($plans as $plan) {
            $plan->details = $this->snackPlanDetailRepository->findByPlanId($plan->snack_plan_id);
        }
        
        return $plans;
    }

    public function updateSnackPlan(int $id, array $planData, array $snackItems = [])
    {
        return $this->executeInTransaction(function () use ($id, $planData, $snackItems) {
            // Update the main snack plan
            $snackPlan = $this->update($id, $planData);

            if (!$snackPlan) {
                return false;
            }

            // If snack items are provided, replace them atomically
            if (!empty($snackItems)) {
                $snackPlan->details = $this->snackPlanDetailRepository->findByPlanId($id);
                // Delete existing snack plan details
                $this->snackPlanDetailRepository->deleteByPlanId($id);

                // Create new snack plan details
                $details = [];
                foreach ($snackItems as $item) {
                    $item['snack_plan_id'] = $id;
                    $details[] = $this->snackPlanDetailRepository->create($item);
                }
                $snackPlan->details = $details;
            } else {
                // Load existing details if no new items provided
                $snackPlan->details = $this->snackPlanDetailRepository->findByPlanId($id);
            }

            return $snackPlan;
        });
    }

    public function deleteSnackPlan(int $id)
    {
        return $this->executeWithTransaction(
            function () use ($id) {
                $this->snackPlanDetailRepository->deleteByPlanId($id);
                return $this->delete($id);
            },
            function ($e) {
                throw new \Exception('Failed to delete snack plan: ' . $e->getMessage());
            }
        );
    }
}
