<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateContributionStatusRequest;
use App\Services\ContributionServiceInterface;
use Illuminate\Support\Facades\Auth;

class ContributionController extends BaseController
{
    /**
     * Bulk insert or update status for multiple contributions for the current month.
     * Expects: [{user_id: int, status: string}, ...]
     */
    public function bulkUpdateStatus(Request $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
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

            return $this->successResponse("Successfully updated the contributions status", $result);
        }, ['snack_manager', 'operation'], 'Failed to bulk update contribution status');
    }

    // Listing of all contributions with filters/pagination (snack_manager and operation only)
    public function index(Request $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
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

            return $this->successResponse('Contributions retrieved successfully', $result);
        }, ['snack_manager', 'operation'], 'Failed to retrieve contributions');
    }
    protected $contributionService;

    public function __construct(ContributionServiceInterface $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    // Only account_manager can mark paid/unpaid
    public function updateStatus(UpdateContributionStatusRequest $request, $id)
    {
        return $this->executeWithAuth(function ($user) use ($request, $id) {
            $validated = $request->validated();
            $contribution = $this->contributionService->updateContribution($id, ['status' => $validated['status']]);

            if (!$contribution) {
                return $this->notFoundResponse('Contribution not found');
            }

            return $this->updatedResponse(
                new \App\Http\Resources\ContributionResource($contribution),
                'Contribution status updated successfully'
            );
        }, ['snack_manager', 'operation'], 'Failed to update contribution status');
    }

    // User can view their own contribution history
    public function myContributions()
    {
        return $this->executeWithAuth(function ($user) {
            $contributions = $this->contributionService->getUserContributions($user->user_id);

            return $this->resourceCollectionResponse(
                \App\Http\Resources\ContributionResource::collection($contributions),
                'Your contributions retrieved successfully'
            );
        }, null, 'Failed to retrieve your contributions');
    }
}
