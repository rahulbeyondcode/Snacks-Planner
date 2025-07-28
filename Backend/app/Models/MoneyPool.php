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
        'money_pool_setting_id',
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

    public function settings()
    {
        return $this->belongsTo(MoneyPoolSettings::class, 'money_pool_setting_id', 'money_pool_setting_id');
    }
}
