<?php

namespace App\Http\Controllers;

use App\Models\SnackItem;
use App\Models\SnackPlanDetail;
use App\Services\SnackPlanServiceInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSnackPlanRequest;
use App\Http\Requests\UpdateSnackPlanRequest;
use App\Http\Resources\SnackPlanResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SnackPlanController extends Controller
{
    protected $snackPlanService;

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
        $detail = SnackPlanDetail::find($detailId);
        if ($detail) {
            $detail->upload_receipt = $url;
            $detail->save();
        }

        return apiResponse(true, __('success'), ['url' => $url, 'detail' => $detail], 201);
    }

    // List all snack plans (with optional filters)
    public function index(Request $request)
    {
        $filters = $request->only(['snack_plan_id', 'snack_date', 'user_id', 'total_amount']);
        $plans = $this->snackPlanService->listSnackPlans($filters);
        return SnackPlanResource::collection($plans);
    }

    public function __construct(SnackPlanServiceInterface $snackPlanService)
    {
        $this->snackPlanService = $snackPlanService;
    }

    public function store(StoreSnackPlanRequest $request)
    {
        try {
            $validated = $request->validated();
            $snackItems = $validated['snack_items'];

            // Check if user is authenticated
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Convert date from d-m-Y to Y-m-d format for database
            try {
                $snackDate = Carbon::createFromFormat('d-m-Y', trim($validated['snack_date']))->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Please use DD-MM-YYYY format.',
                    'error' => $e->getMessage()
                ], 400);
            }

            $planData = [
                'snack_date' => $snackDate,
                'user_id' => $user->user_id,
                'total_amount' => $validated['total_amount'],
            ];

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

            return (new SnackPlanResource($snackPlan))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create snack plan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $snackPlan = $this->snackPlanService->getSnackPlan($id);
        if (!$snackPlan) {
            return response()->internalServerError(__('messages.error'));
        }
        return apiResponse(true, __('success'), $snackPlan, 200);
    }

    // Update a snack plan
    public function update(UpdateSnackPlanRequest $request, $id)
    {
        try {
            $validated = $request->all();
            $snackItems = $validated['snack_items'] ?? [];

            // Convert date from d-m-Y to Y-m-d format for database if provided
            $planData = [];
            if (isset($validated['snack_date'])) {
                try {
                    $snackDate = Carbon::createFromFormat('d-m-Y', trim($validated['snack_date']))->format('Y-m-d');
                    $planData['snack_date'] = $snackDate;
                } catch (\Exception $e) {
                    return response()->internalServerError(__('Invalid date format. Please use DD-MM-YYYY format'));
                }
            }

            if (isset($validated['total_amount'])) {
                $planData['total_amount'] = $validated['total_amount'];
            }

            // Check if user is authenticated for update
            $user = Auth::user();
            if (!$user) {
                return response()->internalServerError(__('User not authenticated'));
            }
            $planData['user_id'] = $user->user_id;

            // Handle file uploads for each snack item if provided
            if (!empty($snackItems)) {
                foreach ($snackItems as $i => $item) {
                    if (isset($item['upload_receipt']) && $request->hasFile("snack_items.$i.upload_receipt")) {
                        $file = $request->file("snack_items.$i.upload_receipt");
                        $path = $file->store('receipts');
                        $snackItems[$i]['upload_receipt'] = url('/storage/' . $path);
                    } else {
                        $snackItems[$i]['upload_receipt'] = $item['upload_receipt'] ?? null;
                    }
                }
            }

            $updated = $this->snackPlanService->updateSnackPlan($id, $planData, $snackItems);
            if (!$updated) {
                return response()->internalServerError(__('Snack Plan not found'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Snack plan updated successfully',
                'data' => $updated
            ], 200);
        } catch (\Exception $e) {
            return response()->internalServerError(__('Failed to update snack plan'));
        }
    }

    // Delete a snack plan
    public function destroy($id)
    {
        try {
            $deleted = $this->snackPlanService->deleteSnackPlan($id);
            if (!$deleted) {
                return response()->notFound(__('Snack Plan not found'));
            }
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->internalServerError(__('Failed to delete snack plan'));
        }
    }
}
