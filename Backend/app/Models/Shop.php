<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'shop_id';

    protected $fillable = [
        'name',
        'address',
        'contact_number',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function snackPlanDetails()
    {
        return $this->hasMany(SnackPlanDetail::class, 'shop_id', 'shop_id');
    }

    /**
     * Get the snack mappings for this shop.
     */
    public function snackMappings()
    {
        return $this->hasMany(SnackShopMapping::class, 'shop_id', 'shop_id');
    }

    /**
     * Get the snack items sold by this shop.
     */
    public function snackItems()
    {
        return $this->belongsToMany(SnackItem::class, 'snack_shop_mapping', 'shop_id', 'snack_item_id')
                    ->withPivot('snack_price', 'is_available')
                    ->withTimestamps();
    }

    /**
     * Get the payment methods supported by this shop.
     */
    public function paymentMethods()
    {
        return $this->hasMany(ShopPaymentMethod::class, 'shop_id', 'shop_id');
    }

    /**
     * Get payment methods by their string identifiers (cash, card, upi, bank_transfer)
     */
    public function getPaymentMethodsByType()
    {
        return $this->paymentMethods()->pluck('payment_method')->toArray();
    }

    /**
     * Get available payment method types
     */
    public static function getAvailablePaymentMethodTypes()
    {
        return ['cash', 'bank_transfer', 'card', 'upi'];
    }
}
