<?php

namespace App\Services;

use App\Repositories\SnackPlanRepositoryInterface;
use App\Repositories\SnackPlanDetailRepositoryInterface;
use Illuminate\Support\Facades\DB;

interface SnackPlanServiceInterface
{
    public function planSnack(array $data);
    public function getSnackPlan(int $id);
    public function planFullSnackDay(array $planData, array $snackItems);
    public function listSnackPlans(array $filters = []);
    public function updateSnackPlan(int $id, array $planData, array $snackItems = []);
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
        $plans = $this->snackPlanRepository->list($filters);
        
        // Load details for each plan
        foreach ($plans as $plan) {
            $plan->details = $this->snackPlanDetailRepository->findByPlanId($plan->snack_plan_id);
        }
        
        return $plans;
    }

    public function updateSnackPlan(int $id, array $planData, array $snackItems = [])
    {
        // Update the main snack plan
        $snackPlan = $this->snackPlanRepository->update($id, $planData);       
        
        if (!$snackPlan) {
            return false;
        }
        
        // If snack items are provided, update them
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
    }

    public function deleteSnackPlan(int $id)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();           
           
            $this->snackPlanDetailRepository->deleteByPlanId($id);            
          
            $result = $this->snackPlanRepository->delete($id);
            
            DB::commit();
            
            return $result;
            
        } catch (\Exception $e) {           
            DB::rollback();
            throw new \Exception('Failed to delete snack plan: ' . $e->getMessage());
        }
    }
}
