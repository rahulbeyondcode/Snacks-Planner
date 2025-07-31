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
    public $timestamps = false;
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
        return $this->hasMany(GroupWeeklyOperation::class, 'user_id', 'user_id');
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

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($module, $action, $resource = null)
    {
        return $this->role && $this->role->hasPermission($module, $action, $resource);
    }

    /**
     * Check if user has any permission for a module.
     */
    public function hasModulePermission($module)
    {
        return $this->role && $this->role->hasModulePermission($module);
    }

    /**
     * Get all permissions for the user's role.
     */
    public function getPermissions()
    {
        return $this->role ? $this->role->permissions()->wherePivot('is_active', true)->get() : collect();
    }

    /**
     * Get permissions grouped by module.
     */
    public function getPermissionsByModule()
    {
        $permissions = $this->getPermissions();
        
        return $permissions->groupBy('module')->map(function ($modulePermissions) {
            return $modulePermissions->pluck('action')->unique()->values()->toArray();
        });
    }

    /**
     * Check if user has any of the specified roles.
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        return $this->role && in_array($this->role->name, $roles);
    }

    /**
     * Check if user has all of the specified roles.
     */
    public function hasAllRoles($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        return $this->role && in_array($this->role->name, $roles);
    }
}
