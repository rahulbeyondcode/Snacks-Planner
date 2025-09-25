<?php

namespace App\Repositories;

use App\Models\SnackPlan;
use App\Repositories\Traits\DateHelperTrait;
use Illuminate\Support\Facades\DB;

class SnackPlanRepository extends BaseRepository implements SnackPlanRepositoryInterface
{
    use DateHelperTrait;

    public function __construct(SnackPlan $model)
    {
        parent::__construct($model);
    }

    /**
     * Apply filters to snack plan query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('snack_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('snack_date', '<=', $filters['date_to']);
        }
    }

    public function list(array $filters = [])
    {
        $query = $this->model->select([
            'snack_plan_id',
            'snack_date',
            'user_id',
            'total_amount'
        ]);

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->orderByDesc('snack_date')->get();
    }

    public function update(int $id, array $data)
    {
        $plan = $this->find($id);
        if ($plan) {
            $plan->update($data);
        }
        return $plan;
    }

    public function delete(int $id)
    {
        $plan = $this->find($id);
        if ($plan) {
            $plan->delete();
            return true;
        }
        return false;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find(int $id)
    {
        return $this->model->select([
            'snack_plan_id',
            'snack_date',
            'user_id',
            'total_amount'
        ])->find($id);
    }

    public function getMonthlyExpense(string $month)
    {
        return $this->model->whereRaw('DATE_FORMAT(snack_date, "%Y-%m") = ?', [$month])
            ->selectRaw('snack_date, total_amount')
            ->orderBy('snack_date')
            ->get()
            ->toArray();
    }

    public function getSnackSummary(string $month)
    {
        return DB::table('snack_plan_details')
            ->join('snack_plans', 'snack_plan_details.snack_plan_id', '=', 'snack_plans.snack_plan_id')
            ->join('snack_items', 'snack_plan_details.snack_item_id', '=', 'snack_items.snack_item_id')
            ->whereRaw('DATE_FORMAT(snack_plans.snack_date, "%Y-%m") = ?', [$month])
            ->groupBy('snack_plan_details.snack_item_id', 'snack_items.name')
            ->select('snack_items.name as snack', DB::raw('SUM(snack_plan_details.quantity) as total_consumed'))
            ->get()
            ->toArray();
    }

    /**
     * Get snack plans for a specific user
     */
    public function getByUser(int $userId, array $filters = [])
    {
        $query = $this->model->where('user_id', $userId);

        if (!empty($filters['date_from'])) {
            $query->where('snack_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('snack_date', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('snack_date')->get();
    }

    /**
     * Get total expense for date range
     */
    public function getTotalExpense(string $dateFrom, string $dateTo)
    {
        return $this->model->whereBetween('snack_date', [$dateFrom, $dateTo])
            ->sum('total_amount');
    }
}
