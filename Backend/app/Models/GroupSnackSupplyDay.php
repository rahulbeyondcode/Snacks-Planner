<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupSnackSupplyDay extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'group_snack_supply_day_id';

    protected $fillable = [
        'group_id',
        'supply_date',
        'set_by',
        'created_at'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by', 'user_id');
    }
}
