<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SnackSuggestion extends BaseModel
{
    use HasFactory;
    protected $primaryKey = 'snack_suggestion_id';
    protected $fillable = [
        'user_id',
        'snack_name',
        'reason',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
