<?php

namespace App\Http\Controllers;

use App\Models\SnackPlanDetail;
use Illuminate\Http\Request;

class SnackPlanDetailController extends Controller
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
        return apiResponse(true, __('success'), $details, 200);
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
            return apiResponse(false, __('not_found'), null, 404);
        }
        return apiResponse(true, __('success'), $detail, 200);
    }
}
