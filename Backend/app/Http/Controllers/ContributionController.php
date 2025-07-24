<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateContributionStatusRequest;
use App\Services\ContributionServiceInterface;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    // Admin listing of all contributions with filters/pagination
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $filters = $request->only(['user_id', 'status', 'from', 'to', 'per_page']);
        $contributions = $this->contributionService->listAllContributions($filters);
        return \App\Http\Resources\ContributionResource::collection($contributions);
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
