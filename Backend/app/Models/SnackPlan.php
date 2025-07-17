<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnackPlan extends Model
{
    protected $fillable = ['snack_day_id', 'snack_item_id', 'quantity', 'delivery_charge', 'total', 'receipt', 'notes', 'planned_by'];

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
}
