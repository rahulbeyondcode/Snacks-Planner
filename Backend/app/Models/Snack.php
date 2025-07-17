<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Snack extends Model
{
    protected $fillable = ['category', 'snack_name', 'snack_size', 'snack_description'];
}
