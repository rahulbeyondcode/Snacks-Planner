<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SnackRating extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'snack_rating_id';
    protected $fillable = [
        'user_id',
        'snack_item_id',
        'rating',
        'comment',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function snackItem()
    {
        return $this->belongsTo(SnackItem::class, 'snack_item_id', 'snack_item_id');
    }
}
