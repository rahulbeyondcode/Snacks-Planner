<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubGroupMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sub_group_member_id';

    protected $fillable = [
        'sub_group_id',
        'user_id',
    ];

    public function subGroup()
    {
        return $this->belongsTo(SubGroup::class, 'sub_group_id', 'sub_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
