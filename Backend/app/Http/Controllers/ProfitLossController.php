<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ProfitLossServiceInterface;

class ProfitLossController extends BaseController
{
    protected $profitLossService;

    public function __construct(ProfitLossServiceInterface $profitLossService)
    {
        $this->profitLossService = $profitLossService;
    }

    // GET /profit-loss (account_manager only)
    public function index(Request $request)
    {
        return $this->executeWithAuth(function ($user) use ($request) {
            if ($user->role->name !== 'account_manager') {
                return $this->forbiddenResponse('Forbidden');
            }
            $summary = $this->profitLossService->getProfitLossSummary($request->all());
            return $this->successResponse('success', $summary);
        });
    }
}
