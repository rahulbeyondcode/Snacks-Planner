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
        $user = Auth::user();
        if (!$user || !in_array($user->role->name, ['operation_manager', 'operation'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'contributors' => 'required|array|min:1',
            'contributors.*' => 'required|integer|exists:users,user_id',
        ]);
        $count = $this->contributionService->bulkUpdateStatus($data['contributors']);
        return response()->json(['updated' => $count]);
    }

    // Admin listing of all contributions with filters/pagination
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $filters = $request->only(['user_id', 'status', 'from', 'to', 'per_page']);
        $contributions = $this->contributionService->listAllContributions($filters);
        $resource = \App\Http\Resources\ContributionResource::collection($contributions);
        $response = $resource->response()->getData(true);
        $result = [];
        if (isset($response['data'])) $result['data'] = $response['data'];
        if (isset($response['meta'])) {
            unset($response['meta']['links']);
            $result['meta'] = $response['meta'];
        }
        return response()->json($result);
    }
    protected $contributionService;

    public function __construct(ContributionServiceInterface $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    // Only account_manager can mark paid/unpaid
    public function updateStatus(UpdateContributionStatusRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $validated = $request->validated();
        $contribution = $this->contributionService->updateContribution($id, ['status' => $validated['status']]);
        if (!$contribution) {
            return response()->json(['message' => 'Contribution not found'], 404);
        }
        return new \App\Http\Resources\ContributionResource($contribution);
    }

    // User can view their own contribution history
    public function myContributions()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $contributions = $this->contributionService->getUserContributions($user->user_id);
        return \App\Http\Resources\ContributionResource::collection($contributions);
    }
}