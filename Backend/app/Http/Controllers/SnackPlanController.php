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
use Illuminate\Support\Facades\Response;

class SnackPlanController extends BaseController
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

        $detail = SnackPlanDetail::find($detailId);
        if ($detail) {
            $detail->upload_receipt = $url;
            $detail->save();
        }

        return $this->createdResponse(['url' => $url, 'detail' => $detail], __('success'));
    }

    // List all snack plans (with optional filters)
    public function index(Request $request)
    {
        $filters = $request->only(['snack_plan_id', 'snack_date', 'user_id', 'total_amount']);
        $plans = $this->snackPlanService->listSnackPlans($filters);
        return $this->resourceCollectionResponse(SnackPlanResource::collection($plans));
    }

    public function __construct(SnackPlanServiceInterface $snackPlanService)
    {
        $this->snackPlanService = $snackPlanService;
    }

    public function store(StoreSnackPlanRequest $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            $validated = $request->validated();
            $snackItems = $validated['snack_items'];

            try {
                $snackDate = Carbon::createFromFormat('d-m-Y', trim($validated['snack_date']))->format('Y-m-d');
            } catch (\Exception $e) {
                return $this->errorResponse('Invalid date format. Please use DD-MM-YYYY format.', ['error' => $e->getMessage()], 400);
            }

            $planData = [
                'snack_date' => $snackDate,
                'user_id' => $user->user_id,
                'total_amount' => $validated['total_amount'],
            ];

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

            return $this->createdResponse(new SnackPlanResource($snackPlan));
        }, null, 'Failed to create snack plan');
    }

    public function show($id)
    {
        $snackPlan = $this->snackPlanService->getSnackPlan($id);
        if (!$snackPlan) {
            return $this->notFoundResponse(__('messages.error'));
        }
        return $this->successResponse(__('success'), $snackPlan);
    }

    // Update a snack plan
    public function update(UpdateSnackPlanRequest $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            $validated = $request->all();
            $snackItems = $validated['snack_items'] ?? [];

            $planData = [];
            if (isset($validated['snack_date'])) {
                try {
                    $snackDate = Carbon::createFromFormat('d-m-Y', trim($validated['snack_date']))->format('Y-m-d');
                    $planData['snack_date'] = $snackDate;
                } catch (\Exception $e) {
                    return $this->validationErrorResponse(__('Invalid date format. Please use DD-MM-YYYY format'));
                }
            }

            if (isset($validated['total_amount'])) {
                $planData['total_amount'] = $validated['total_amount'];
            }

            $planData['user_id'] = $user->user_id;

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
                return $this->notFoundResponse(__('Snack Plan not found'));
            }

            return $this->updatedResponse($updated, 'Snack plan updated successfully');
        }, null, 'Failed to update snack plan');
    }

    // Delete a snack plan
    public function destroy($id)
    {
        return $this->executeWithExceptionHandling(function () use ($id) {
            $deleted = $this->snackPlanService->deleteSnackPlan($id);
            if (!$deleted) {
                return $this->notFoundResponse(__('Snack Plan not found'));
            }
            return $this->noContentResponse();
        }, 'Failed to delete snack plan');
    }
}
