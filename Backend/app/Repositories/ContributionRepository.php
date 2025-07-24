<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

use App\Models\Contribution;

class ContributionRepository implements ContributionRepositoryInterface
{
    /**
     * List all contributions with optional filters and pagination.
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAll(array $filters = [])
    {
        $query = Contribution::query();
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }
        $perPage = $filters['per_page'] ?? 15;
        return $query->orderByDesc('created_at')->paginate($perPage);
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
            'total' => Contribution::sum('amount'),
            'by_user' => Contribution::select('user_id', \DB::raw('SUM(amount) as total_contributed'))
                ->groupBy('user_id')
                ->get()
                ->toArray(),
        ];
    }
}
