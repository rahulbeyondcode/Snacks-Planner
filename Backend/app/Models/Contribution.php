<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'date', 'status', 'remarks'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
