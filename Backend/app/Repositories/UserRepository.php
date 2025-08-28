<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = User::query();

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

        return $query->with('role')->orderBy('user_id')->get();
    }

    public function find(int $id)
    {
        return User::with('role')->whereNull('deleted_at')->find($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::whereNull('deleted_at')->find($id);
        if ($user) {
            $user->update($data);
            return $user->fresh('role');
        }
        return null;
    }

    public function delete(int $id)
    {
        $user = User::whereNull('deleted_at')->find($id);
        if ($user) {
            // Soft delete - sets deleted_at timestamp
            $user->delete();
            return true;
        }
        return false;
    }

    public function assignRole(int $userId, int $roleId)
    {
        $user = User::whereNull('deleted_at')->find($userId);
        if ($user) {
            $user->role_id = $roleId;
            $user->save();
            return $user->fresh('role');
        }
        return null;
    }
}
