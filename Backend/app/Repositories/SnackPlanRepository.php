<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\Models\SnackPlan;

interface SnackPlanRepositoryInterface
{
    public function create(array $data);
    public function find(int $id);
    public function list(array $filters = []);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getMonthlyExpense(string $month);
    public function getSnackSummary(string $month);
}

class SnackPlanRepository implements SnackPlanRepositoryInterface
{
    public function list(array $filters = [])
    {
        $query = SnackPlan::query();
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('snack_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('snack_date', '<=', $filters['date_to']);
        }
        return $query->orderByDesc('snack_date')->get();
    }

    public function update(int $id, array $data)
    {
        $plan = SnackPlan::find($id);
        if ($plan) {
            $plan->update($data);
        }
        return $plan;
    }

    public function delete(int $id)
    {
        $plan = SnackPlan::find($id);
        if ($plan) {
            $plan->delete();
            return true;
        }
        return false;
    }
    public function create(array $data)
    {
        return SnackPlan::create($data);
    }

    public function find(int $id)
    {
        return SnackPlan::find($id);
    }

    public function getMonthlyExpense(string $month)
    {
        // $month format: YYYY-MM
        return SnackPlan::whereRaw('DATE_FORMAT(snack_date, "%Y-%m") = ?', [$month])
            ->selectRaw('snack_date, total_amount')
            ->orderBy('snack_date')
            ->get()
            ->toArray();
    }

    public function getSnackSummary(string $month)
    {
        // Example: group by snack_item_id and sum quantity for the month
        return DB::table('snack_plan_details')
            ->join('snack_plans', 'snack_plan_details.snack_plan_id', '=', 'snack_plans.snack_plan_id')
            ->join('snack_items', 'snack_plan_details.snack_item_id', '=', 'snack_items.snack_item_id')
            ->whereRaw('DATE_FORMAT(snack_plans.snack_date, "%Y-%m") = ?', [$month])
            ->groupBy('snack_plan_details.snack_item_id', 'snack_items.name')
            ->select('snack_items.name as snack', DB::raw('SUM(snack_plan_details.quantity) as total_consumed'))
            ->get()
            ->toArray();
    }
}
