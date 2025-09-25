<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Traits\RoleFilterTrait;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use RoleFilterTrait;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Apply filters to user query
     */
    protected function applyFilters($query, array $filters): void
    {
        // Exclude soft-deleted users by default
        $query->whereNull('deleted_at');

        if (!empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['exclude_roles'])) {
            $query->whereHas('role', function ($q) use ($filters) {
                $q->whereNotIn('name', $filters['exclude_roles']);
            });
        }
    }

    public function all(array $columns = ['*'], array $relations = [], array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->orderBy('name')->get($columns);
    }

    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        } else {
            $query->with('role'); // Default relationship for users
        }

        return $query->whereNull('deleted_at')->find($id, $columns);
    }

    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?\Illuminate\Database\Eloquent\Model
    {
        $user = $this->find($id);
        if ($user) {
            $user->update($data);
            return $user->fresh('role');
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $user = $this->find($id);
        if ($user) {
            // Soft delete - sets deleted_at timestamp
            $user->delete();
            return true;
        }
        return false;
    }

    public function assignRole(int $userId, int $roleId)
    {
        $user = $this->find($userId);
        if ($user) {
            $user->role_id = $roleId;
            $user->save();
            return $user->fresh('role');
        }
        return null;
    }

    /**
     * Get users by role name
     */
    public function getByRole(string $roleName, array $filters = [])
    {
        $query = $this->model->query()
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->where('roles.name', $roleName)
            ->whereNull('users.deleted_at');

        if (!empty($filters['search'])) {
            $query->where('users.name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->select('users.*')->with('role')->get();
    }

    /**
     * Get users excluding specific roles
     */
    public function getExcludingRoles(array $roleNames)
    {
        return $this->model->query()
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->whereNotIn('roles.name', $roleNames)
            ->whereNull('users.deleted_at')
            ->select('users.*')
            ->with('role')
            ->get();
    }
}
