<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnackItem extends Model
{
    protected $fillable = ['category', 'snack_name', 'snack_size', 'snack_description'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function snackPlans()
    {
        return $this->hasMany(SnackPlan::class);
    }
}
