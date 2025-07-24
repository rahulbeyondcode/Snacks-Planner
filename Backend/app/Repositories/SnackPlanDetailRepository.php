<?php

namespace App\Repositories;

use App\Models\SnackPlanDetail;

interface SnackPlanDetailRepositoryInterface
{
    public function create(array $data);
    public function findByPlanId(int $snackPlanId);
}

class SnackPlanDetailRepository implements SnackPlanDetailRepositoryInterface
{
    public function create(array $data)
    {
        return SnackPlanDetail::create($data);
    }

    public function findByPlanId(int $snackPlanId)
    {
        return SnackPlanDetail::where('snack_plan_id', $snackPlanId)->get();
    }
}
