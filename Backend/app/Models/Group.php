<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $primaryKey = 'group_id';

    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at',
    ];
    

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class, 'group_id', 'group_id');
    }

    public function groupWeeklyOperations()
    {
        return $this->hasMany(GroupWeeklyOperation::class, 'group_id', 'group_id');
    }

    public function groupSnackSupplyDays()
    {
        return $this->hasMany(GroupSnackSupplyDay::class, 'group_id', 'group_id');
    }
}
