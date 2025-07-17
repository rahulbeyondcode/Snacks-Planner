<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnackPlan extends Model
{
    protected $primaryKey = 'snack_plan_id';
    
    protected $fillable = [
        'snack_date', 
        'planned_by', 
        'total_amount'
    ];

    public function snackDay()
    {
        return $this->belongsTo(SnackDay::class);
    }

    public function snackItem()
    {
        return $this->belongsTo(SnackItem::class);
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'planned_by');
    }

    public function snackPlanDetails()
    {
        return $this->hasMany(SnackPlanDetail::class, 'snack_plan_id', 'snack_plan_id');
    }
}
