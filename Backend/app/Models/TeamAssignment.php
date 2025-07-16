<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamAssignment extends Model
{
    protected $fillable = ['month', 'week_group', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function snackDays()
    {
        return $this->hasMany(SnackDay::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_assignments');
    }
}
