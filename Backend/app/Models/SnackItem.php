<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SnackItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'snack_item_id';

    protected $fillable = [
        'name',
        'description',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function snackPlanDetails()
    {
        return $this->hasMany(SnackPlanDetail::class, 'snack_item_id', 'snack_item_id');
    }
}
