<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\Models\Contribution;

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
        $allUsers = \App\Models\User::pluck('user_id')->toArray();
        $count = 0;
        foreach ($allUsers as $targetUserId) {
            $status = in_array($targetUserId, $paidUserIds) ? 'paid' : 'unpaid';
            $existing = \App\Models\Contribution::where('user_id', $targetUserId)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->first();
            if ($existing) {
                if ($existing->status !== $status) {
                    $existing->status = $status;
                    $existing->save();
                    $count++;
                }
            } else {
                \App\Models\Contribution::create([
                    'user_id' => $targetUserId,
                    'status' => $status,
                    'created_at' => $now
                ]);
                $count++;
            }
        }
        // --- Money Pool Insert/Update Logic ---
        // 1. Get active money_pool_settings (assuming 'active' means latest or with a status column)
        $activeSetting = \App\Models\MoneyPoolSettings::orderByDesc('money_pool_setting_id')->first();
        if ($activeSetting) {
            $perMonthAmount = $activeSetting->per_month_amount;
            $multiplier = $activeSetting->multiplier;
            // 2. Count paid contributions for this month
            $paidCount = \App\Models\Contribution::where('status', 'paid')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            $totalCollected = $paidCount * $perMonthAmount;
            $employerContribution = $totalCollected * $multiplier;
            $totalPoolAmount = $totalCollected + $employerContribution;
            // 3. Insert or update money_pools row for this month
            $pool = \App\Models\MoneyPool::whereDate('created_at', '>=', $monthStart)
                ->whereDate('created_at', '<=', $monthEnd)
                ->first();
            $poolData = [
                'money_pool_setting_id' => $activeSetting->money_pool_setting_id,
                'total_collected_amount' => $totalCollected,
                'employer_contribution' => $employerContribution,
                'total_pool_amount' => $totalPoolAmount,
            ];
            if ($pool) {
                // Only update relevant fields, do not overwrite created_by
                $pool->update($poolData);
            } else {
                $poolData['created_by'] = $userId;
                $poolData['created_at'] = $now;
                $poolData['updated_at'] = $now;
                \App\Models\MoneyPool::create($poolData);
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
        $query = Contribution::query()->join('users', 'contributions.user_id', '=', 'users.user_id')
            ->select('contributions.*');
        if (!empty($filters['search'])) {
            $searchTerm = strtolower($filters['search']);
            $query->whereRaw('LOWER(users.name) LIKE ?', ['%' . $searchTerm . '%']);
        }
        if (!empty($filters['status'])) {
            $query->where('contributions.status', $filters['status']);
        }
        if (!empty($filters['from'])) {
            $query->whereDate('contributions.created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('contributions.created_at', '<=', $filters['to']);
        }
        $perPage = $filters['per_page'] ?? 15;
        return $query->orderByDesc('contributions.created_at')->paginate($perPage);
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
        return Contribution::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
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
        // Example: sum by user and overall
        return [
            'total' => Contribution::count(),
            'by_user' => Contribution::select('user_id', \DB::raw('COUNT(*) as total_contributions'))
                ->groupBy('user_id')
                ->get()
                ->toArray(),
        ];
    }
}
