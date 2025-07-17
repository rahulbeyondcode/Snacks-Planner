<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundBlock extends Model
{
    protected $fillable = ['date', 'amount', 'purpose'];
}
