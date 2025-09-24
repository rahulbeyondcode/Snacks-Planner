<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'contribution_id';

    protected $fillable = [
        'user_id',
        'status',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
