<?php

namespace App\Repositories;

use App\Models\SnackPlanDetail;

interface SnackPlanDetailRepositoryInterface
{
    public function create(array $data);
    public function findByPlanId(int $snackPlanId);
    public function deleteByPlanId(int $snackPlanId);
}

class SnackPlanDetailRepository implements SnackPlanDetailRepositoryInterface
{
    public function create(array $data)
    {
        return SnackPlanDetail::create($data);
    }

    public function findByPlanId(int $snackPlanId)
    {
        return SnackPlanDetail::select([
            'snack_plan_detail_id',
            'snack_plan_id',
            'snack_item_id',
            'shop_id',
            'quantity',
            'category_id',
            'price_per_item',
            'total_price',
            'payment_mode',
            'discount',
            'delivery_charge',
            'upload_receipt'
        ])->where('snack_plan_id', $snackPlanId)->get();
    }

    public function deleteByPlanId(int $snackPlanId)
    {        
        return SnackPlanDetail::where('snack_plan_id', $snackPlanId)->delete();
    }
}
