<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;

trait RoleFilterTrait
{
    /**
     * Exclude account manager role from query
     */
    protected function excludeAccountManager(Builder $query): void
    {
        $query->join('roles', 'users.role_id', '=', 'roles.role_id')
              ->where('roles.name', '!=', 'account_manager');
    }

    /**
     * Filter by specific role
     */
    protected function filterByRole(Builder $query, string $roleName): void
    {
        $query->join('roles', 'users.role_id', '=', 'roles.role_id')
              ->where('roles.name', $roleName);
    }

    /**
     * Filter by multiple roles
     */
    protected function filterByRoles(Builder $query, array $roleNames): void
    {
        $query->join('roles', 'users.role_id', '=', 'roles.role_id')
              ->whereIn('roles.name', $roleNames);
    }

    /**
     * Exclude multiple roles
     */
    protected function excludeRoles(Builder $query, array $roleNames): void
    {
        $query->join('roles', 'users.role_id', '=', 'roles.role_id')
              ->whereNotIn('roles.name', $roleNames);
    }

    /**
     * Filter by role with additional conditions
     */
    protected function filterByRoleWithConditions(Builder $query, string $roleName, array $additionalConditions = []): void
    {
        $query->join('roles', 'users.role_id', '=', 'roles.role_id')
              ->where('roles.name', $roleName);

        foreach ($additionalConditions as $column => $value) {
            $query->where($column, $value);
        }
    }

    /**
     * Get users by role with optional filters
     */
    protected function getUsersByRole(string $roleName, array $filters = []): Builder
    {
        $query = \DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->where('roles.name', $roleName);

        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } else {
                    $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Count users by role
     */
    protected function countUsersByRole(string $roleName): int
    {
        return \DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->where('roles.name', $roleName)
            ->count();
    }

    /**
     * Get role-based statistics
     */
    protected function getRoleStatistics(): array
    {
        $roles = ['account_manager', 'snack_manager', 'operation', 'employee'];

        $statistics = [];
        foreach ($roles as $role) {
            $statistics[$role] = $this->countUsersByRole($role);
        }

        return $statistics;
    }

    /**
     * Apply role filter with search
     */
    protected function applyRoleFilterWithSearch(Builder $query, ?string $roleName = null, ?string $searchTerm = null): void
    {
        if ($roleName) {
            $this->filterByRole($query, $roleName);
        }

        if ($searchTerm) {
            $query->where('users.name', 'like', '%' . $searchTerm . '%');
        }
    }

    /**
     * Filter by role excluding soft deleted users
     */
    protected function filterByRoleExcludingDeleted(string $roleName): Builder
    {
        return \DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.role_id')
            ->where('roles.name', $roleName)
            ->whereNull('users.deleted_at');
    }

    /**
     * Get active users by role
     */
    protected function getActiveUsersByRole(string $roleName): Builder
    {
        return $this->filterByRoleExcludingDeleted($roleName);
    }
}
