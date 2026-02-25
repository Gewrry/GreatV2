<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    // Tell Laravel to use 'uname' as the username field for authentication
    public function username()
    {
        return 'uname';
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uname',
        'password',
        'employee_id',
        'encoded_by',
        'encoded_date',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'encoded_date' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }

    public function encodedBy()
    {
        return $this->belongsTo(EmployeeInfo::class, 'encoded_by');
    }

    /**
     * The roles assigned to this user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Accessor for email from employee info.
     */
    public function getEmailAttribute()
    {
        return $this->employee->email ?? null;
    }

    // =========================================================================
    // RBAC HELPERS
    // =========================================================================

    /**
     * Check if this user is a super admin (bypasses all module checks).
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /**
     * Check if this user has access to a specific module by slug.
     */
    public function hasModuleAccess(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->roles()
            ->whereHas('modules', function ($q) use ($slug) {
                $q->where('slug', $slug)->where('is_active', true);
            })
            ->exists();
    }

    /**
     * Get all modules this user can access (for dynamic sidebar rendering).
     * Returns active modules ordered by sort_order.
     */
    public function accessibleModules(): Collection
    {
        if ($this->isSuperAdmin()) {
            return Module::active()->ordered()->get();
        }

        return Module::active()
            ->ordered()
            ->whereHas('roles', function ($q) {
                $q->whereIn('roles.id', $this->roles()->pluck('roles.id'));
            })
            ->get();
    }

    /**
     * Check if this user has a specific role by slug.
     */
    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }
}
