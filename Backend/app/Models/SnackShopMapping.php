<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnackShopMapping extends Model
{
    use HasFactory;

    protected $table = 'snack_shop_mapping';

    protected $fillable = [
        'snack_item_id',
        'shop_id',
        'snack_price',
        'is_available',
    ];

    protected $casts = [
        'snack_price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the snack item that belongs to this mapping.
     */
    public function snackItem()
    {
        return $this->belongsTo(SnackItem::class, 'snack_item_id', 'snack_item_id');
    }

    /**
     * Get the shop that belongs to this mapping.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }
}
