<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sub_group_id';

    protected $fillable = [
        'group_id',
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    public function subGroupMembers()
    {
        return $this->hasMany(SubGroupMember::class, 'sub_group_id', 'sub_group_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'sub_group_members', 'sub_group_id', 'user_id', 'sub_group_id', 'user_id');
    }
}
