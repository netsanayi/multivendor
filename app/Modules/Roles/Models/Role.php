<?php

namespace App\Modules\Roles\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get users with this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.role_pivot_key'),
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Scope a query to only include active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if role is active
     */
    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    /**
     * Get user count for this role
     */
    public function getUserCountAttribute(): int
    {
        return $this->users()->count();
    }

    /**
     * Check if role can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Sistem rolleri silinemez
        $systemRoles = ['super-admin', 'admin', 'vendor', 'customer'];
        
        if (in_array($this->name, $systemRoles)) {
            return false;
        }
        
        // Kullanıcısı olan roller silinemez
        if ($this->users()->exists()) {
            return false;
        }
        
        return true;
    }

    /**
     * Toggle role status
     */
    public function toggleStatus()
    {
        $this->is_active = !$this->is_active;
        $this->save();
    }

    /**
     * Get formatted permissions
     */
    public function getFormattedPermissionsAttribute()
    {
        return $this->permissions->pluck('name')->toArray();
    }

    /**
     * Sync permissions
     */
    public function syncPermissionsByNames(array $permissionNames)
    {
        $permissions = \Spatie\Permission\Models\Permission::whereIn('name', $permissionNames)->get();
        $this->syncPermissions($permissions);
    }

    /**
     * Get grouped permissions
     */
    public function getGroupedPermissionsAttribute()
    {
        $grouped = [];
        
        foreach ($this->permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = $parts[0] ?? 'general';
            $action = $parts[1] ?? $permission->name;
            
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            
            $grouped[$module][] = $action;
        }
        
        return $grouped;
    }
}
