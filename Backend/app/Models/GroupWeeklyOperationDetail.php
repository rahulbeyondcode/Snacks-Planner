<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupWeeklyOperationDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'group_weekly_operation_detail_id';

    protected $fillable = [
        'group_weekly_operation_id',
        'task_description',
        'status',
        'created_at',
    ];

    public function groupWeeklyOperation()
    {
        return $this->belongsTo(GroupWeeklyOperation::class, 'group_weekly_operation_id', 'group_weekly_operation_id');
    }
}
