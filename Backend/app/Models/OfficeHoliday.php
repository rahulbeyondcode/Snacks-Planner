<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeHoliday extends Model
{
    use HasFactory;

    protected $primaryKey = 'holiday_id';

    protected $fillable = [
        'user_id',
        'holiday_date',
        'description',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
