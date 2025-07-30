<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingDay extends Model
{
    protected $table = 'working_days';
    protected $primaryKey = 'id';
    protected $fillable = ['working_days', 'user_id'];
    protected $casts = [
        'working_days' => 'array',
    ];
}
