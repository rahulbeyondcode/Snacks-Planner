<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $timestamps = false;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role_id',
        'email',
        'preference',
        'created_at',
        'password', // If you use password authentication
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class, 'user_id', 'user_id');
    }

    public function officeHolidays()
    {
        return $this->hasMany(OfficeHoliday::class, 'user_id', 'user_id');
    }

    public function groupWeeklyOperationsAsEmployee()
    {
        return $this->hasMany(GroupWeeklyOperation::class, 'employee_id', 'user_id');
    }

    public function groupWeeklyOperationsAssigned()
    {
        return $this->hasMany(GroupWeeklyOperation::class, 'assigned_by', 'user_id');
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'user_id', 'user_id');
    }

    public function moneyPoolsCreated()
    {
        return $this->hasMany(MoneyPool::class, 'created_by', 'user_id');
    }

    public function moneyPoolBlocksCreated()
    {
        return $this->hasMany(MoneyPoolBlock::class, 'created_by', 'user_id');
    }

    public function snackPlans()
    {
        return $this->hasMany(SnackPlan::class, 'user_id', 'user_id');
    }

    public function groupSnackSupplyDaysSet()
    {
        return $this->hasMany(GroupSnackSupplyDay::class, 'set_by', 'user_id');
    }
}
