<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $primaryKey = 'shop_id';

    protected $fillable = [
        'name',
        'address',
        'contact_number',
        'created_at',
    ];

    public function snackPlanDetails()
    {
        return $this->hasMany(SnackPlanDetail::class, 'shop_id', 'shop_id');
    }
}
