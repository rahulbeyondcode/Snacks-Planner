<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'method',
    ];

    /**
     * Get the shops that support this payment method.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_payment_methods', 'id', 'shop_id')
                    ->withPivot('is_active', 'additional_details')
                    ->withTimestamps();
    }
}
