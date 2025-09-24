<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupWeeklyOperation extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'group_weekly_operation_id';

    protected $fillable = [
        'group_id',
        'week_start_date',
        'user_id',
        'assigned_by',
        'created_at',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'user_id');
    }

    public function details()
    {
        return $this->hasMany(GroupWeeklyOperationDetail::class, 'group_weekly_operation_id', 'group_weekly_operation_id');
    }
}
