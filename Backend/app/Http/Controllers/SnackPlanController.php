<?php

namespace App\Http\Controllers;

use App\Services\SnackPlanServiceInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSnackPlanRequest;

class SnackPlanController extends Controller
{
    // Upload receipt for a snack plan detail
    public function uploadReceipt(Request $request, $detailId)
    {
        $request->validate([
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);
        $file = $request->file('receipt');
        $path = $file->store('receipts');
        $url = url('/storage/' . $path);

        // Optionally update the SnackPlanDetail record
        $detail = \App\Models\SnackPlanDetail::find($detailId);
        if ($detail) {
            $detail->upload_receipt = $url;
            $detail->save();
        }

        return response()->json(['url' => $url, 'detail' => $detail], 201);
    }

    // List all snack plans (with optional filters)
    public function index(Request $request)
    {
        $filters = $request->only(['user_id', 'date_from', 'date_to']);
        $plans = $this->snackPlanService->listSnackPlans($filters);
        return response()->json($plans);
    }

    protected $snackPlanService;

    public function __construct(SnackPlanServiceInterface $snackPlanService)
    {
        $this->snackPlanService = $snackPlanService;
    }

    public function store(StoreSnackPlanRequest $request)
    {
        $validated = $request->validated();
        $planData = [
            'snack_date' => $validated['snack_date'],
            'user_id' => $validated['user_id'],
            'total_amount' => $validated['total_amount'],
        ];
        $snackItems = $validated['snack_items'];

        // Handle file uploads for each snack item
        foreach ($snackItems as $i => $item) {
            if (isset($item['upload_receipt']) && $request->hasFile("snack_items.$i.upload_receipt")) {
                $file = $request->file("snack_items.$i.upload_receipt");
                $path = $file->store('receipts');
                $snackItems[$i]['upload_receipt'] = url('/storage/' . $path);
            } else {
                $snackItems[$i]['upload_receipt'] = null;
            }
        }

        $snackPlan = $this->snackPlanService->planFullSnackDay($planData, $snackItems);
        return (new \App\Http\Resources\SnackPlanResource($snackPlan))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $snackPlan = $this->snackPlanService->getSnackPlan($id);
        if (!$snackPlan) {
            return response()->json(['message' => 'Snack Plan not found'], 404);
        }
        return response()->json($snackPlan);
    }

    // Update a snack plan
    public function update(UpdateSnackPlanRequest $request, $id)
    {
        $updated = $this->snackPlanService->updateSnackPlan($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'Snack Plan not found'], 404);
        }
        return response()->json($updated);
    }

    // Delete a snack plan
    public function destroy($id)
    {
        $deleted = $this->snackPlanService->deleteSnackPlan($id);
        if (!$deleted) {
            return response()->json(['message' => 'Snack Plan not found'], 404);
        }
        return response()->json(['message' => 'Snack Plan deleted successfully']);
    }
}
