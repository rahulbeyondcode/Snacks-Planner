<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateContributionStatusRequest;
use App\Services\ContributionServiceInterface;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    /**
     * Bulk insert or update status for multiple contributions for the current month.
     * Expects: [{user_id: int, status: string}, ...]
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !in_array($user->role->name, ['snack_manager', 'operation'])) {
                return apiResponse(
                    false,
                    'Access denied. Only snack managers and operations can bulk update contribution status.',
                    [],
                    403
                );
            }

            $data = $request->validate([
                'contributors' => 'required|array|min:1',
                'contributors.*' => 'required|integer|exists:users,user_id',
            ]);

            $count = $this->contributionService->bulkUpdateStatus($data['contributors'], $user->user_id);

            // Fetch all contributions for the current month
            $filters = [
                'per_page' => 1000 // or a sufficiently large number to get all
            ];
            $contributions = $this->contributionService->listAllContributions($filters);
            $resource = \App\Http\Resources\ContributionResource::collection($contributions);
            $response = $resource->response()->getData(true);

            $result = [];
            if (isset($response['data'])) $result['contributions'] = $response['data'];
            if (isset($response['meta'])) {
                unset($response['meta']['links']);
                $result['meta'] = $response['meta'];
            }
            $result['updated_count'] = $count;

            // Add counts for current month
            $counts = $this->contributionService->getCurrentMonthCounts();
            $result['paid_contributions'] = $counts['paid_contributions'];
            $result['unpaid_records'] = $counts['unpaid_records'];

            return response()->json([
                'success' => true,
                'message' => "Successfully updated the contributions status",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to bulk update contribution status: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // Listing of all contributions with filters/pagination (snack_manager and operation only)
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !in_array($user->role->name, ['snack_manager', 'operation'])) {
                return apiResponse(
                    false,
                    'Access denied. Only snack managers and operations can view contributions.',
                    [],
                    403
                );
            }

            $filters = $request->only(['user_id', 'status', 'from', 'to', 'per_page']);
            // Add support for employee name search (case-insensitive)
            if ($request->filled('search')) {
                $filters['search'] = $request->input('search');
            }

            $contributions = $this->contributionService->listAllContributions($filters);
            $resource = \App\Http\Resources\ContributionResource::collection($contributions);
            $response = $resource->response()->getData(true);

            $result = [];
            if (isset($response['data'])) $result['contributions'] = $response['data'];
            if (isset($response['meta'])) {
                unset($response['meta']['links']);
                $result['meta'] = $response['meta'];
            }

            // Add counts for current month
            $counts = $this->contributionService->getCurrentMonthCounts();
            $result['paid_contributions'] = $counts['paid_contributions'];
            $result['unpaid_records'] = $counts['unpaid_records'];

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve contributions: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
    protected $contributionService;

    public function __construct(ContributionServiceInterface $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    // Only account_manager can mark paid/unpaid
    public function updateStatus(UpdateContributionStatusRequest $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !in_array($user->role->name, ['snack_manager', 'operation'])) {
                return apiResponse(
                    false,
                    'Access denied. Only snack managers and operations can update contribution status.',
                    [],
                    403
                );
            }

            $validated = $request->validated();
            $contribution = $this->contributionService->updateContribution($id, ['status' => $validated['status']]);

            if (!$contribution) {
                return apiResponse(
                    false,
                    'Contribution not found',
                    [],
                    404
                );
            }

            return apiResponse(
                true,
                'Contribution status updated successfully',
                new \App\Http\Resources\ContributionResource($contribution),
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to update contribution status: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // User can view their own contribution history
    public function myContributions()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return apiResponse(
                    false,
                    'Authentication required to view contributions',
                    [],
                    401
                );
            }

            $contributions = $this->contributionService->getUserContributions($user->user_id);

            return apiResponse(
                true,
                'Your contributions retrieved successfully',
                \App\Http\Resources\ContributionResource::collection($contributions),
                200
            );
        } catch (\Exception $e) {
            return apiResponse(
                false,
                'Failed to retrieve your contributions: ' . $e->getMessage(),
                [],
                500
            );
        }
    }
}
