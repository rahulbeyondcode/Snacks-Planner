<?php

namespace App\Services;

use App\Repositories\ContributionRepositoryInterface;
use App\Repositories\SnackPlanRepositoryInterface;
use App\Repositories\MoneyPoolRepositoryInterface;

class ProfitLossService implements ProfitLossServiceInterface
{
    protected $contributionRepo;
    protected $snackPlanRepo;
    protected $moneyPoolRepo;

    public function __construct(
        ContributionRepositoryInterface $contributionRepo,
        SnackPlanRepositoryInterface $snackPlanRepo,
        MoneyPoolRepositoryInterface $moneyPoolRepo
    ) {
        $this->contributionRepo = $contributionRepo;
        $this->snackPlanRepo = $snackPlanRepo;
        $this->moneyPoolRepo = $moneyPoolRepo;
    }

    public function getProfitLossSummary(array $filters = [])
    {
        $month = $filters['month'] ?? null;
        // Contributions
        $totalContributionsRaw = $this->contributionRepo->getTotalContributions();
        $totalContributions = is_array($totalContributionsRaw) && isset($totalContributionsRaw['total']) && is_numeric($totalContributionsRaw['total'])
            ? (float)$totalContributionsRaw['total']
            : (is_numeric($totalContributionsRaw) ? (float)$totalContributionsRaw : 0);
        // Expenses
        $expensesRaw = $month
            ? $this->snackPlanRepo->getMonthlyExpense($month)
            : $this->snackPlanRepo->getMonthlyExpense(date('Y-m'));
        if (is_array($expensesRaw)) {
            $totalExpenses = 0;
            foreach ($expensesRaw as $expense) {
                if (is_array($expense) && isset($expense['total_amount'])) {
                    $totalExpenses += (float)$expense['total_amount'];
                } elseif (is_object($expense) && isset($expense->total_amount)) {
                    $totalExpenses += (float)$expense->total_amount;
                }
            }
        } else {
            $totalExpenses = is_numeric($expensesRaw) ? (float)$expensesRaw : 0;
        }
        // Blocked Funds (if any)
        $blocked = 0;
        if ($month) {
            $pools = $this->moneyPoolRepo->query()->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])->get();
        } else {
            $pools = $this->moneyPoolRepo->query()->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [date('Y-m')])->get();
        }
        foreach ($pools as $pool) {
            $blocked += $this->moneyPoolRepo->getTotalBlocked($pool->money_pool_id);
        }
        $profit = $totalContributions - ($totalExpenses + $blocked);
        return [
            'total_contributions' => $totalContributions,
            'total_expenses' => $totalExpenses,
            'total_blocked' => $blocked,
            'profit_or_loss' => $profit,
            'month' => $month ?? date('Y-m'),
        ];
    }
}
