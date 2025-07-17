<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnackPlanDetail extends Model
{
    protected $fillable = [
        'snack_plan_id',
        'snack_item_id', 
        'shop_id',
        'quantity',
        'price_per_item',
        'category',
        'discount',
        'delivery_charge',
        'notes',
        'upload_receipt'
    ];

    public function snackPlan()
    {
        return $this->belongsTo(SnackPlan::class, 'snack_plan_id', 'snack_plan_id');
    }

    public function snackItem()
    {
        return $this->belongsTo(SnackItem::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }
} 