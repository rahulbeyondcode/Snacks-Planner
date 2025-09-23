<?php

namespace App\Http\Controllers;

use App\Models\SnackPlanDetail;
use Illuminate\Http\Request;

class SnackPlanDetailController extends BaseController
{
    // List all details for a given snack plan
    public function index(Request $request)
    {
        $planId = $request->query('snack_plan_id');
        $query = SnackPlanDetail::select([
            'snack_plan_detail_id',
            'snack_plan_id',
            'snack_item_id',
            'shop_id',
            'quantity',
            'category',
            'price_per_item',
            'total_price',
            'payment_mode',
            'discount',
            'delivery_charge',
            'upload_receipt'
        ]);

        if ($planId) {
            $query->where('snack_plan_id', $planId);
        }

        $details = $query->get();
        return $this->successResponse(__('success'), $details);
    }

    // Show a specific snack plan detail
    public function show($id)
    {
        $detail = SnackPlanDetail::select([
            'snack_plan_detail_id',
            'snack_plan_id',
            'snack_item_id',
            'shop_id',
            'quantity',
            'category',
            'price_per_item',
            'total_price',
            'payment_mode',
            'discount',
            'delivery_charge',
            'upload_receipt'
        ])->find($id);

        if (!$detail) {
            return $this->notFoundResponse(__('not_found'));
        }
        return $this->successResponse(__('success'), $detail);
    }
}
