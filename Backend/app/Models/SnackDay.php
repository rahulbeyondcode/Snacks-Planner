<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnackDay extends Model
{
    protected $fillable = ['date', 'is_holiday', 'planned_by', 'notes', 'team_assignment_id'];

    public function teamAssignment()
    {
        return $this->belongsTo(TeamAssignment::class);
    }

    public function snackPlans()
    {
        return $this->hasMany(SnackPlan::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'planned_by');
    }
}
