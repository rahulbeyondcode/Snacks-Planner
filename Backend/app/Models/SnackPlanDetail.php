<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SnackPlanDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'snack_plan_detail_id';

    protected $fillable = [
        'snack_plan_id',
        'snack_item_id',
        'shop_id',
        'quantity',
        'category',
        'price_per_item',
        'total_price',
        'payment_mode',
        'discount',
        'delivery_charge',
        'upload_receipt',
        'created_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function snackPlan()
    {
        return $this->belongsTo(SnackPlan::class, 'snack_plan_id', 'snack_plan_id');
    }

    public function snackItem()
    {
        return $this->belongsTo(SnackItem::class, 'snack_item_id', 'snack_item_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }
}
