<?php

namespace App\Http\Controllers;

use App\Models\SnackPlan;
use App\Models\SnackPlanDetail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SnackPlanResource;

class SnackPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        // if (!$user || !$user->hasAnyRole(['admin', 'manager', 'operations'])) {
        //     return response()->json(['message' => 'Forbidden.'], 403);
        // }
        $snackPlans = SnackPlan::with([
            'planner', // planned_by -> users table
            'snackPlanDetails.snackItem', // snack_item_id -> snack_items table
            'snackPlanDetails.shop' // shop_id -> shops table
        ])->get();

        // dd($snackPlans);
        $plans = [];
        foreach ($snackPlans as $snackPlan) {
            $snackDetails = [];
            foreach ($snackPlan->snackPlanDetails as $detail) {
                $snackDetails[] = [
                    'snack_plan_detail_id' => $detail->id,
                    'snack_name' => $detail->snackItem->snack_name,
                    'shop_id' => $detail->shop->shop_name,
                    'quantity' => $detail->quantity,
                    'price_per_item' => $detail->price_per_item,
                    'category' => $detail->category,
                    'discount' => $detail->discount,
                    'delivery_charge' => $detail->delivery_charge,
                ];
            }
            $plans[] = [
                'snack_plan_id' => $snackPlan->snack_plan_id,
                'snack_date' => $snackPlan->snack_date,
                'planned_by' => $snackPlan->planner->name,
                'total_amount' => $snackPlan->total_amount,
                'snack_plan_description' => $snackPlan->snack_plan_description,
                'snack_details' => $snackDetails,
            ];
        }
        return response()->json($plans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate snack plan data
        $validatedPlan = $request->validate([
            'snack_date' => 'required|date',
            'planned_by' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'snack_details' => 'required|array|min:1',
            'snack_details.*.snack_item_id' => 'required|exists:snack_items,id',
            'snack_details.*.shop_id' => 'required|exists:shops,shop_id',
            'snack_details.*.quantity' => 'required|integer|min:1',
            'snack_details.*.price_per_item' => 'required|numeric|min:0',
            'snack_details.*.category' => 'required|in:veg,non-veg,other',
            'snack_details.*.discount' => 'nullable|numeric|min:0',
            'snack_details.*.delivery_charge' => 'nullable|numeric|min:0',
            'snack_details.*.notes' => 'nullable|string|max:255',
            'snack_details.*.upload_receipt' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Create snack plan
            $snackPlan = SnackPlan::create([
                'snack_date' => $validatedPlan['snack_date'],
                'planned_by' => $validatedPlan['planned_by'],
                'total_amount' => $validatedPlan['total_amount'],
            ]);

            // Create multiple snack plan detailssnack_date
            foreach ($validatedPlan['snack_details'] as $detail) {
                SnackPlanDetail::create([
                    'snack_plan_id' => $snackPlan->snack_plan_id,
                    'snack_item_id' => $detail['snack_item_id'],
                    'shop_id' => $detail['shop_id'],
                    'quantity' => $detail['quantity'],
                    'price_per_item' => $detail['price_per_item'],
                    'category' => $detail['category'],
                    'discount' => $detail['discount'] ?? null,
                    'delivery_charge' => $detail['delivery_charge'] ?? null,
                    'notes' => $detail['notes'] ?? null,
                    'upload_receipt' => $detail['upload_receipt'] ?? null,
                ]);
            }

            DB::commit();

            // Load the snack plan with its details
            $snackPlan->load('snackPlanDetails.snackItem', 'snackPlanDetails.shop');

            return response()->json([
                'success' => true,
                'data' => $snackPlan,
                'message' => 'Snack plan created successfully with details.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create snack plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $snackPlan = SnackPlan::with([
            'planner', // planned_by -> users table
            'snackPlanDetails.snackItem', // snack_item_id -> snack_items table
            'snackPlanDetails.shop' // shop_id -> shops table
        ])->find($id);

        if (!$snackPlan) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        return new SnackPlanResource($snackPlan);
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

        $validatedPlan = $request->validate([
            'snack_date' => 'required|date',
            'planned_by' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'snack_details' => 'required|array|min:1',
            'snack_details.*.snack_item_id' => 'required|exists:snack_items,id',
            'snack_details.*.shop_id' => 'required|exists:shops,shop_id',
            'snack_details.*.quantity' => 'required|integer|min:1',
            'snack_details.*.price_per_item' => 'required|numeric|min:0',
            'snack_details.*.category' => 'required|in:veg,non-veg,other',
            'snack_details.*.discount' => 'nullable|numeric|min:0',
            'snack_details.*.delivery_charge' => 'nullable|numeric|min:0',
            'snack_details.*.notes' => 'nullable|string|max:255',
            'snack_details.*.upload_receipt' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Update the main snack plan
            $snackPlan->update([
                'snack_date' => $validatedPlan['snack_date'],
                'planned_by' => $validatedPlan['planned_by'],
                'total_amount' => $validatedPlan['total_amount'],
            ]);

            // Delete old details
            $snackPlan->snackPlanDetails()->delete();

            // Insert new details
            foreach ($validatedPlan['snack_details'] as $detail) {
                $snackPlan->snackPlanDetails()->create([
                    'snack_item_id' => $detail['snack_item_id'],
                    'shop_id' => $detail['shop_id'],
                    'quantity' => $detail['quantity'],
                    'price_per_item' => $detail['price_per_item'],
                    'category' => $detail['category'],
                    'discount' => $detail['discount'] ?? null,
                    'delivery_charge' => $detail['delivery_charge'] ?? null,
                    'notes' => $detail['notes'] ?? null,
                    'upload_receipt' => $detail['upload_receipt'] ?? null,
                ]);
            }

            DB::commit();

            // Reload with relations
            $snackPlan->load('snackPlanDetails.snackItem', 'snackPlanDetails.shop', 'planner');

            return response()->json([
                'success' => true,
                'data' => $snackPlan,
                'message' => 'Snack plan updated successfully with details.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update snack plan: ' . $e->getMessage()
            ], 500);
        }
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
