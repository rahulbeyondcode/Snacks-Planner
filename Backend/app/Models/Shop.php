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
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function snackPlanDetails()
    {
        return $this->hasMany(SnackPlanDetail::class, 'shop_id', 'shop_id');
    }
}
