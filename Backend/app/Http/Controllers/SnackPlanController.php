<?php

namespace App\Http\Controllers;

use App\Models\SnackPlan;

use Illuminate\Http\Request;

class SnackPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasAnyRole(['admin', 'manager', 'operations'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        $snackPlans = SnackPlan::all();
        return response()->json([
            'success' => true,
            'data' => $snackPlans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Snack plan created (stub).']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $snackPlan = SnackPlan::find($id);
        if (!$snackPlan) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $snackPlan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $snackPlan = SnackPlan::find($id);
        if (!$snackPlan) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $snackPlan->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $snackPlan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $snackPlan = SnackPlan::find($id);
        if (!$snackPlan) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $snackPlan->delete();
        return response()->json([
            'success' => true,
            'message' => 'SnackPlan deleted.'
        ]);
    }

    /**
     * Upload a receipt for a snack plan.
     */
    public function uploadReceipt(Request $request, $snack_plan)
    {
        return response()->json([
            'success' => true,
            'message' => 'Receipt uploaded (stub).'
        ]);
    }

    /**
     * Get profit/loss for a snack plan.
     */
    public function profitLoss($snack_plan)
    {
        return response()->json([
            'success' => true,
            'message' => 'Profit/loss (stub).'
        ]);
    }
}
