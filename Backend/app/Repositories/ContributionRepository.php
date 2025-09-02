<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Contribution;
use App\Models\MoneyPool;
use App\Models\MoneyPoolSettings;

class ContributionRepository implements ContributionRepositoryInterface
{
    /**
     * Bulk update status for multiple contributions.
     * @param array $contributions Array of ['id' => int, 'status' => string]
     * @return int Number of updated records
     */
    /**
     * Bulk update status for all users for the current month.
     * @param array $paidUserIds
     * @return int Number of updated records
     */
    public function bulkUpdateStatus(array $paidUserIds, $userId = null)
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        // Get all users
        $allUsers = User::pluck('user_id')->toArray();
        $count = 0;
        foreach ($allUsers as $targetUserId) {
            $status = in_array($targetUserId, $paidUserIds) ? 'paid' : 'unpaid';
            $existing = Contribution::where('user_id', $targetUserId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->first();
            if ($existing) {
                if ($existing->status !== $status) {
                    $existing->status = $status;
                    $existing->save();
                    $count++;
                }
            } else {
                Contribution::create([
                    'user_id' => $targetUserId,
                    'status' => $status,
                    'created_at' => $now
                ]);
                $count++;
            }
        }
        // --- Money Pool Insert/Update Logic ---
        // 1. Get active money_pool_settings (assuming 'active' means latest or with a status column)
        $pool = MoneyPool::whereDate('created_at', '>=', $monthStart)
            ->whereDate('created_at', '<=', $monthEnd)
            ->first();

        if ($pool) {
            // On update: use the MoneyPoolSettings associated with the existing pool
            $setting = MoneyPoolSettings::find($pool->money_pool_setting_id);
            if (!$setting) {
                // Fallback: use latest if missing (should not happen)
                $setting = MoneyPoolSettings::orderByDesc('money_pool_setting_id')->first();
            }
        } else {
            // On insert: use the latest MoneyPoolSettings
            $setting = MoneyPoolSettings::orderByDesc('money_pool_setting_id')->first();
        }

        if ($setting) {
            $perMonthAmount = $setting->per_month_amount;
            $multiplier = $setting->multiplier;
            // 2. Count paid contributions for this month
            $paidCount = Contribution::where('status', 'paid')
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
                $poolData['created_at'] = $now;
                $poolData['updated_at'] = $now;
                MoneyPool::create($poolData);
            }
        }
        // --- End Money Pool Logic ---
        return $count;
    }

    /**
     * List all contributions with optional filters and pagination.
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAll(array $filters = [])
    {
        // Always filter for current month
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd = $now->copy()->endOfMonth()->toDateString();
        $query = Contribution::query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->select('contributions.*')
            ->whereDate('contributions.created_at', '>=', $monthStart)
            ->whereDate('contributions.created_at', '<=', $monthEnd)
            ->where('roles.name', '!=', 'account_manager');
        if (!empty($filters['search'])) {
            $searchTerm = strtolower($filters['search']);
            $query->whereRaw('LOWER(users.name) LIKE ?', ['%' . $searchTerm . '%']);
        }
        if (!empty($filters['status'])) {
            $query->where('contributions.status', $filters['status']);
        }
        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('contributions.user_id')->paginate($perPage);
    }
    public function create(array $data)
    {
        return Contribution::create($data);
    }

    public function find(int $id)
    {
        return Contribution::find($id);
    }

    public function findByUser(int $userId)
    {
        return Contribution::where('user_id', $userId)->orderBy('user_id')->get();
    }

    public function update(int $id, array $data)
    {
        $contribution = Contribution::find($id);
        if ($contribution) {
            $contribution->update($data);
        }
        return $contribution;
    }

    public function delete(int $id)
    {
        $contribution = Contribution::find($id);
        if ($contribution) {
            $contribution->delete();
            return true;
        }
        return false;
    }

    public function getTotalContributions()
    {
        // Get current month date range
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd = $now->copy()->endOfMonth()->toDateString();

        // Get total count of paid contributions for current month, excluding account_manager role
        $totalPaid = Contribution::query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $monthStart)
            ->whereDate('contributions.created_at', '<=', $monthEnd)
            ->where('roles.name', '!=', 'account_manager')
            ->where('contributions.status', 'paid')
            ->count();

        // Get total count of unpaid contributions for current month, excluding account_manager role
        $totalUnpaid = Contribution::query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $monthStart)
            ->whereDate('contributions.created_at', '<=', $monthEnd)
            ->where('roles.name', '!=', 'account_manager')
            ->where('contributions.status', 'unpaid')
            ->count();

        // Get total count of all records for current month, excluding account_manager role
        $totalAll = Contribution::query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $monthStart)
            ->whereDate('contributions.created_at', '<=', $monthEnd)
            ->where('roles.name', '!=', 'account_manager')
            ->count();

        // Get contributions by user with user names and status breakdown for current month
        $byUser = Contribution::select(
            'contributions.user_id',
            'users.name as user_name',
            DB::raw('COUNT(*) as total_records'),
            DB::raw('SUM(CASE WHEN contributions.status = "paid" THEN 1 ELSE 0 END) as paid_contributions'),
            DB::raw('SUM(CASE WHEN contributions.status = "unpaid" THEN 1 ELSE 0 END) as unpaid_records')
        )
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $monthStart)
            ->whereDate('contributions.created_at', '<=', $monthEnd)
            ->where('roles.name', '!=', 'account_manager')
            ->groupBy('contributions.user_id', 'users.name')
            ->orderBy('contributions.user_id')
            ->get()
            ->toArray();

        return [
            'total_paid' => $totalPaid,
            'total_unpaid' => $totalUnpaid,
            'total_all' => $totalAll,
            'by_user' => $byUser,
        ];
    }

    public function getCurrentMonthCounts()
    {
        // Get current month date range
        $now = now();
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd = $now->copy()->endOfMonth()->toDateString();

        // Get counts for current month, excluding account_manager role
        $paidCount = Contribution::query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $monthStart)
            ->whereDate('contributions.created_at', '<=', $monthEnd)
            ->where('roles.name', '!=', 'account_manager')
            ->where('contributions.status', 'paid')
            ->count();

        $unpaidCount = Contribution::query()
            ->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereDate('contributions.created_at', '>=', $monthStart)
            ->whereDate('contributions.created_at', '<=', $monthEnd)
            ->where('roles.name', '!=', 'account_manager')
            ->where('contributions.status', 'unpaid')
            ->count();

        return [
            'paid_contributions' => $paidCount,
            'unpaid_records' => $unpaidCount,
        ];
    }
}
