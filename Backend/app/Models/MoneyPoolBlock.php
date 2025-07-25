<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoneyPoolBlock extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'block_id';

    protected $fillable = [
        'money_pool_id',
        'amount',
        'reason',
        'block_date',
        'created_by',
        'created_at',
        'deleted_at',
    ];

    public function moneyPool()
    {
        return $this->belongsTo(MoneyPool::class, 'money_pool_id', 'money_pool_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
