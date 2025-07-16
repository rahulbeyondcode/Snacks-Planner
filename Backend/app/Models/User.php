<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Added for Sanctum API tokens

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if the user has a given role by name.
     */
    public function hasRole($role)
    {
        return $this->roles->pluck('name')->contains($role);
    }

    /**
     * Check if the user has any of the given roles.
     * @param array|string $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function teamAssignments()
    {
        return $this->hasMany(TeamAssignment::class);
    }

    public function snackPlans()
    {
        return $this->hasMany(SnackPlan::class, 'planned_by');
    }

    public function teams()
    {
        return $this->belongsToMany(TeamAssignment::class, 'team_assignments');
    }
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
}
