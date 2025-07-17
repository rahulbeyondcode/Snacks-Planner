<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoneyPool extends Model
{
    protected $fillable = ['month', 'per_person_amount', 'multiplier', 'total_collected', 'final_pool'];

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }
}
