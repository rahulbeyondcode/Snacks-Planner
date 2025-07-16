<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = ['user_id', 'amount', 'date', 'status', 'remarks'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
