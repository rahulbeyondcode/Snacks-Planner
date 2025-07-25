<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'role_id';

    protected $fillable = [
        'name',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class, 'role_id', 'role_id');
    }
}
