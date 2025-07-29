<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = User::query();
        // Exclude users with the 'account_manager' role
        $query->whereHas('role', function($q) {
            $q->where('name', '!=', 'account_manager');
        });
        if (!empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }
        return $query->orderBy('name')->get();
    }

    public function find(int $id)
    {
        return User::find($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::find($id);
        if ($user && $user->role && $user->role->name === 'account_manager') {
            // Prevent update for account_manager role
            return null;
        }
        if ($user) {
            $user->update($data);
        }
        return $user;
    }

    public function delete(int $id)
    {
        $user = User::find($id);
        if ($user && $user->role && $user->role->name === 'account_manager') {
            // Prevent delete for account_manager role
            return false;
        }
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }

    public function assignRole(int $userId, int $roleId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->role_id = $roleId;
            $user->save();
            return $user;
        }
        return null;
    }
}
