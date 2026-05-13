<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'company_id',
        'role_id',
        'default_branch_id',
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'status',
        'last_login_at',
        'login_attempts',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'role_id' => 'integer',
        'default_branch_id' => 'integer',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'login_attempts' => 'integer',
        'password' => 'hashed',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function defaultBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'default_branch_id');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_user')
            ->using(BranchUser::class)
            ->withPivot(['role_id', 'is_default', 'can_access'])
            ->withTimestamps();
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * All permission names this user has (via role).
     */
    public function permissionNames(): array
    {
        if ($this->isSuperAdmin()) {
            return ['*'];
        }

        $this->loadMissing('role.permissions');

        return $this->role?->permissions->pluck('name')->all() ?? [];
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissionNames(), true);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $names = $this->permissionNames();

        return collect($permissions)->contains(fn ($p) => in_array($p, $names, true));
    }

    public function isSuperAdmin(): bool
    {
        $this->loadMissing('role');

        return (bool) ($this->role?->is_system && strtolower($this->role->name) === 'super admin');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
