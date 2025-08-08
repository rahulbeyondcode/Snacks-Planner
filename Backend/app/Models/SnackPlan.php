<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SnackPlan extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'snack_plan_id';

    protected $fillable = [
        'snack_date',
        'user_id',
        'total_amount',
        'created_at',
    ];

    protected $casts = [
        'snack_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function snackPlanDetails()
    {
        return $this->hasMany(SnackPlanDetail::class, 'snack_plan_id', 'snack_plan_id');
    }
}
