<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_method_id';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the shops that support this payment method.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_payment_methods', 'payment_method_id', 'shop_id')
                    ->withPivot('is_active', 'additional_details')
                    ->withTimestamps();
    }
}
