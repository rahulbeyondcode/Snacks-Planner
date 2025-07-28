<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'group_id';

    protected $fillable = [
        'name',
        'description',
        'group_status',
        'sort_order',
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

    public function setSortOrder($sortOrders)
    {
        foreach ($sortOrders as $groupId => $sortOrder) {
            $group = self::find($groupId);
            if ($group) {
                $group->update(['sort_order' => $sortOrder]);
            }
        }
    }
}
