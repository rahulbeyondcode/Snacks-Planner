<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'group_member_id';

    protected $fillable = [
        'group_id',
        'user_id',
        'role_id',
        'joined_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
