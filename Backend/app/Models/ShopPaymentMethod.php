<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPaymentMethod extends Model
{
    protected $table = 'shop_payment_methods';
    protected $primaryKey = 'shop_payment_method_id';

    protected $fillable = [
        'shop_id',
        'payment_method',
    ];

    /**
     * Get the shop that belongs to this payment method mapping.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }
} 