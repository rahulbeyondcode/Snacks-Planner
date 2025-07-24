<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnackSuggestion extends Model
{
    use HasFactory;
    protected $primaryKey = 'snack_suggestion_id';
    protected $fillable = [
        'user_id',
        'snack_name',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
