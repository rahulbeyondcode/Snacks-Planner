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
    public function bulkUpdateStatus(array $paidUserIds)
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        // Get all users
        $allUsers = \App\Models\User::pluck('user_id')->toArray();
        $count = 0;
        foreach ($allUsers as $userId) {
            $status = in_array($userId, $paidUserIds) ? 'paid' : 'unpaid';
            $existing = \App\Models\Contribution::where('user_id', $userId)
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
                    'user_id' => $userId,
                    'status' => $status,
                    'created_at' => $now
                ]);
                $count++;
            }
        }
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
