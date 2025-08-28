<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoneyPoolSettings extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'money_pool_setting_id';

    protected $fillable = [
        'per_month_amount',
        'multiplier',
    ];

    protected $casts = [
        'per_month_amount' => 'decimal:2',
        'multiplier' => 'integer',
    ];

    public function moneyPools()
    {
        return $this->hasMany(MoneyPool::class, 'money_pool_setting_id', 'money_pool_setting_id');
    }

    /**
     * Get total active users count (excluding account_manager and deleted users)
     */
    public function getTotalActiveUsersAttribute()
    {
        return User::join('roles', 'users.role_id', '=', 'roles.role_id')
            ->where('roles.name', '!=', 'account_manager')
            ->whereNull('users.deleted_at')
            ->count();
    }
}
