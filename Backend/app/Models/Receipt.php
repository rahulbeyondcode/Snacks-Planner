<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = ['file_path', 'snack_day_id'];

    public function snackDay()
    {
        return $this->belongsTo(SnackDay::class);
    }
}
