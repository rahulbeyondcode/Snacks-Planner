<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'block_date' => 'date',
    ];

    public function moneyPool(): BelongsTo
    {
        return $this->belongsTo(MoneyPool::class, 'money_pool_id', 'money_pool_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
