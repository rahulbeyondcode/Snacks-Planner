<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MoneyPool extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'money_pool_id';

    protected $fillable = [
        'per_month_amount',
        'multiplier',
        'total_collected_amount',
        'total_pool_amount',
        'blocked_amount',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function blocks()
    {
        return $this->hasMany(MoneyPoolBlock::class, 'money_pool_id', 'money_pool_id');
    }
}
