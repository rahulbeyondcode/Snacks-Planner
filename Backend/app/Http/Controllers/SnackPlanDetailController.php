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
        $query = SnackPlanDetail::query();
        if ($planId) {
            $query->where('snack_plan_id', $planId);
        }
        return response()->json($query->get());
    }

    // Show a specific snack plan detail
    public function show($id)
    {
        $detail = SnackPlanDetail::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($detail);
    }
}
