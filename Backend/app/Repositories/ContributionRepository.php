<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Contribution;
use App\Models\MoneyPool;
use App\Models\MoneyPoolSettings;
use App\Repositories\Traits\DateHelperTrait;
use App\Repositories\Traits\RoleFilterTrait;
use App\Repositories\Traits\TransactionHelperTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class ContributionRepository extends BaseRepository implements ContributionRepositoryInterface
{
    use DateHelperTrait, RoleFilterTrait, TransactionHelperTrait;

    public function __construct(Contribution $model)
    {
        parent::__construct($model);
    }

    /**
     * Apply filters to contribution query
     */
    protected function applyFilters($query, array $filters): void
    {
        // Qualify the column to avoid ambiguity when joins are present
        $this->applyCurrentMonthFilter($query, 'contributions.created_at');

        if (!empty($filters['search'])) {
            $searchTerm = strtolower($filters['search']);
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }

    /**
     * Bulk update status for multiple contributions.
     */
    public function bulkUpdateStatus(array $paidUserIds, $userId = null)
    {
        return $this->executeInTransaction(function () use ($paidUserIds, $userId) {
            // Validate that active money pool settings exist before proceeding
            $activeSettings = MoneyPoolSettings::orderByDesc('money_pool_setting_id')->first();
            if (!$activeSettings) {
                $validator = Validator::make([], []);
                $validator->errors()->add('money_pool_settings', 'No active money pool settings found. Please configure money pool settings before accepting contributions.');
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            $now = now();
            $monthStart = $now->copy()->startOfMonth();
            $monthEnd = $now->copy()->endOfMonth();

            // Get all users excluding account_manager role
            $allUsers = User::join('roles', 'users.role_id', '=', 'roles.role_id')
                ->where('roles.name', '!=', 'account_manager')
                ->pluck('users.user_id')
                ->toArray();

            $count = 0;
            foreach ($allUsers as $targetUserId) {
                $status = in_array($targetUserId, $paidUserIds) ? 'paid' : 'unpaid';
                $existing = $this->model->where('user_id', $targetUserId)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->first();

                if ($existing) {
                    if ($existing->status !== $status) {
                        $existing->status = $status;
                        $existing->save();
                        $count++;
                    }
                } else {
                    $this->model->create([
                        'user_id' => $targetUserId,
                        'status' => $status,
                        'created_at' => $now
                    ]);
                    $count++;
                }
            }

            // Update money pool logic
            $this->updateMoneyPool($monthStart, $monthEnd, $userId);

            return $count;
        });
    }

    /**
     * Update money pool based on contributions
     */
    private function updateMoneyPool($monthStart, $monthEnd, $userId)
    {
        // Get active money_pool_settings
        $pool = MoneyPool::whereDate('created_at', '>=', $monthStart)
            ->whereDate('created_at', '<=', $monthEnd)
            ->first();

        $setting = $pool
            ? MoneyPoolSettings::find($pool->money_pool_setting_id) ?? MoneyPoolSettings::orderByDesc('money_pool_setting_id')->first()
            : MoneyPoolSettings::orderByDesc('money_pool_setting_id')->first();

        if (!$setting) {
            return;
        }

        $perMonthAmount = $setting->per_month_amount;
        $multiplier = $setting->multiplier;

        // Count paid contributions for this month
        $paidCount = $this->model->where('status', 'paid')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $totalCollected = $paidCount * $perMonthAmount;
        $employerContribution = $totalCollected * $multiplier;
        $totalPoolAmount = $totalCollected + $employerContribution;

        // Calculate total_available_amount based on insert/update case
        $totalAvailableAmount = $totalPoolAmount; // Default for insert case

        $poolData = [
            'money_pool_setting_id' => $setting->money_pool_setting_id,
            'total_collected_amount' => $totalCollected,
            'employer_contribution' => $employerContribution,
            'total_pool_amount' => $totalPoolAmount,
            'total_available_amount' => $totalAvailableAmount,
        ];

        if ($pool) {
            // Update case: Calculate total_available_amount considering blocked_amount
            $blockedAmount = $pool->blocked_amount ?? 0;
            $poolData['total_available_amount'] = $totalPoolAmount - $blockedAmount;

            // Only update relevant fields, do not overwrite created_by
            $pool->update($poolData);
        } else {
            // Insert case: total_available_amount = total_pool_amount (no blocked amount yet)
            $poolData['created_by'] = $userId;
            $poolData['created_at'] = now();
            $poolData['updated_at'] = now();
            MoneyPool::create($poolData);
        }
    }

    /**
     * List all contributions with optional filters and pagination.
     */
    public function listAll(array $filters = [])
    {
        $query = $this->model->query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->select('contributions.*')
            ->where('roles.name', '!=', 'account_manager');

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        $perPage = $filters['per_page'] ?? 100;
        return $query->orderBy('contributions.user_id')->paginate($perPage);
    }

    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        return $this->model->create($data);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        $query = $this->model->query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->find($id, $columns);
    }

    public function findByUser(int $userId)
    {
        return $this->model->where('user_id', $userId)->orderBy('user_id')->get();
    }

    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model
    {
        // Validate that active money pool settings exist before updating contribution status
        if (isset($data['status'])) {
            $activeSettings = MoneyPoolSettings::orderByDesc('money_pool_setting_id')->first();
            if (!$activeSettings) {
                $validator = Validator::make([], []);
                $validator->errors()->add('money_pool_settings', 'No active money pool settings found. Please configure money pool settings before accepting contributions.');
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }

        $contribution = $this->find($id);
        if ($contribution) {
            $contribution->update($data);
        }
        return $contribution;
    }

    public function delete(int $id): bool
    {
        $contribution = $this->find($id);
        if ($contribution) {
            $contribution->delete();
            return true;
        }
        return false;
    }

    public function getTotalContributions()
    {
        $dateRange = $this->getCurrentMonthRange();

        return [
            'total_paid' => $this->getTotalByStatus('paid', $dateRange['start'], $dateRange['end']),
            'total_unpaid' => $this->getTotalByStatus('unpaid', $dateRange['start'], $dateRange['end']),
            'total_all' => $this->getTotalAll($dateRange['start'], $dateRange['end']),
            'by_user' => $this->getContributionsByUser($dateRange['start'], $dateRange['end']),
        ];
    }

    public function getCurrentMonthCounts()
    {
        $dateRange = $this->getCurrentMonthRange();

        return [
            'paid_contributions' => $this->getTotalByStatus('paid', $dateRange['start'], $dateRange['end']),
            'unpaid_records' => $this->getTotalByStatus('unpaid', $dateRange['start'], $dateRange['end']),
        ];
    }

    /**
     * Get total contributions by status
     */
    private function getTotalByStatus(string $status, string $startDate, string $endDate): int
    {
        return $this->model->query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $startDate)
            ->whereDate('contributions.created_at', '<=', $endDate)
            ->where('roles.name', '!=', 'account_manager')
            ->where('contributions.status', $status)
            ->count();
    }

    /**
     * Get total contributions count
     */
    private function getTotalAll(string $startDate, string $endDate): int
    {
        return $this->model->query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $startDate)
            ->whereDate('contributions.created_at', '<=', $endDate)
            ->where('roles.name', '!=', 'account_manager')
            ->count();
    }

    /**
     * Get contributions grouped by user
     */
    private function getContributionsByUser(string $startDate, string $endDate): array
    {
        return $this->model->select(
            'contributions.user_id',
            'users.name as user_name',
            DB::raw('COUNT(*) as total_records'),
            DB::raw('SUM(CASE WHEN contributions.status = "paid" THEN 1 ELSE 0 END) as paid_contributions'),
            DB::raw('SUM(CASE WHEN contributions.status = "unpaid" THEN 1 ELSE 0 END) as unpaid_records')
        )
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $startDate)
            ->whereDate('contributions.created_at', '<=', $endDate)
            ->where('roles.name', '!=', 'account_manager')
            ->groupBy('contributions.user_id', 'users.name')
            ->orderBy('contributions.user_id')
            ->get()
            ->toArray();
    }

    /**
     * Get contributions by date range
     */
    public function getByDateRange(string $dateFrom, string $dateTo)
    {
        return $this->model->query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->select('contributions.*', 'users.name as user_name')
            ->whereBetween('contributions.created_at', [$dateFrom, $dateTo])
            ->where('roles.name', '!=', 'account_manager')
            ->orderBy('contributions.created_at', 'desc')
            ->get();
    }
}
