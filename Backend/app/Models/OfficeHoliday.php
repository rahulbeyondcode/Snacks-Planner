<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeHoliday extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'holiday_id';

    protected $fillable = [
        'user_id',
        'holiday_date',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
