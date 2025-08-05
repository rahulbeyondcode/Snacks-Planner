<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeHoliday extends Model
{
    use HasFactory, SoftDeletes;

    // Type constants
    public const TYPE_OFFICE_HOLIDAY = 'office_holiday';
    public const TYPE_NO_SNACKS_DAY = 'no_snacks_day';

    protected $primaryKey = 'holiday_id';

    protected $fillable = [
        'user_id',
        'type',
        'group_id',
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

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    // Scope for office holidays only
    public function scopeOfficeHolidays($query)
    {
        return $query->where('type', self::TYPE_OFFICE_HOLIDAY);
    }

    // Scope for no snacks days only
    public function scopeNoSnacksDays($query)
    {
        return $query->where('type', self::TYPE_NO_SNACKS_DAY);
    }

    // Scope for specific group
    public function scopeForGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }
}