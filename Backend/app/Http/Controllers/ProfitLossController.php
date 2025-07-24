<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ProfitLossServiceInterface;

class ProfitLossController extends Controller
{
    protected $profitLossService;

    public function __construct(ProfitLossServiceInterface $profitLossService)
    {
        $this->profitLossService = $profitLossService;
    }

    // GET /profit-loss (account_manager only)
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role->name !== 'account_manager') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $summary = $this->profitLossService->getProfitLossSummary($request->all());
        return response()->json($summary);
    }
}
