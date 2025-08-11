<?php

namespace App\Modules\Roles\Services;

use App\Modules\Roles\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleService
{
    /**
     * System roles that cannot be modified
     */
    protected $systemRoles = ['super-admin', 'admin', 'vendor', 'customer'];

    /**
     * Get all roles with filters
     */
    public function getAllWithFilters($filters = [])
    {
        $query = Role::query()->withCount('users');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate(20);
    }

    /**
     * Create a new role
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        
        try {
            $data['guard_name'] = $data['guard_name'] ?? 'web';
            $role = Role::create($data);
            
            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update a role
     */
    public function update(Role $role, array $data)
    {
        if ($this->isSystemRole($role)) {
            throw new \Exception('Sistem rolleri güncellenemez.');
        }

        DB::beginTransaction();
        
        try {
            $role->update($data);
            
            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a role
     */
    public function delete(Role $role)
    {
        if (!$role->canBeDeleted()) {
            throw new \Exception('Bu rol silinemez.');
        }

        return $role->delete();
    }

    /**
     * Check if role is a system role
     */
    public function isSystemRole(Role $role)
    {
        return in_array($role->name, $this->systemRoles);
    }

    /**
     * Clone a role
     */
    public function cloneRole(Role $role)
    {
        DB::beginTransaction();
        
        try {
            $newRole = Role::create([
                'name' => $role->name . '-copy-' . Str::random(4),
                'guard_name' => $role->guard_name,
                'description' => $role->description . ' (Kopya)',
                'is_active' => false,
            ]);

            // Copy permissions
            $newRole->syncPermissions($role->permissions);
            
            DB::commit();
            return $newRole;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get modules with permissions for role creation/editing
     */
    public function getModulesWithPermissions()
    {
        $modules = [
            'users' => ['name' => 'Kullanıcılar', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'roles' => ['name' => 'Roller', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'products' => ['name' => 'Ürünler', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'categories' => ['name' => 'Kategoriler', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'brands' => ['name' => 'Markalar', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'orders' => ['name' => 'Siparişler', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'vendors' => ['name' => 'Satıcılar', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'vendor_products' => ['name' => 'Satıcı Ürünleri', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'blogs' => ['name' => 'Blog Yazıları', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'banners' => ['name' => 'Bannerlar', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'addresses' => ['name' => 'Adresler', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'currencies' => ['name' => 'Para Birimleri', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'languages' => ['name' => 'Diller', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'settings' => ['name' => 'Ayarlar', 'permissions' => ['view', 'edit']],
            'tickets' => ['name' => 'Destek Talepleri', 'permissions' => ['view', 'create', 'edit', 'delete']],
            'messages' => ['name' => 'Mesajlar', 'permissions' => ['view', 'create', 'delete']],
            'wishlists' => ['name' => 'Favoriler', 'permissions' => ['view', 'delete']],
            'notifications' => ['name' => 'Bildirimler', 'permissions' => ['view', 'create', 'delete']],
            'activity_log' => ['name' => 'Aktivite Logları', 'permissions' => ['view', 'delete']],
        ];

        return $modules;
    }

    /**
     * Get grouped permissions for display
     */
    public function getGroupedPermissions()
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = $parts[0] ?? 'general';
            $action = $parts[1] ?? $permission->name;
            
            if (!isset($grouped[$module])) {
                $grouped[$module] = [
                    'name' => $this->getModuleDisplayName($module),
                    'permissions' => []
                ];
            }
            
            $grouped[$module]['permissions'][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action,
                'display_name' => $this->getActionDisplayName($action)
            ];
        }

        return $grouped;
    }

    /**
     * Get module display name
     */
    protected function getModuleDisplayName($module)
    {
        $names = [
            'users' => 'Kullanıcılar',
            'roles' => 'Roller',
            'products' => 'Ürünler',
            'categories' => 'Kategoriler',
            'brands' => 'Markalar',
            'orders' => 'Siparişler',
            'vendors' => 'Satıcılar',
            'settings' => 'Ayarlar',
            'reports' => 'Raporlar',
            'dashboard' => 'Panel',
            'tickets' => 'Destek Talepleri',
            'messages' => 'Mesajlar',
            'wishlists' => 'Favoriler',
        ];

        return $names[$module] ?? ucfirst($module);
    }

    /**
     * Get action display name
     */
    protected function getActionDisplayName($action)
    {
        $names = [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'edit' => 'Düzenleme',
            'delete' => 'Silme',
            'approve' => 'Onaylama',
            'export' => 'Dışa Aktarma',
            'import' => 'İçe Aktarma',
            'manage' => 'Yönetim',
            'process' => 'İşleme',
            'respond' => 'Yanıtlama',
            'send' => 'Gönderme',
            'vendor' => 'Satıcı Paneli',
            'admin' => 'Admin Paneli',
        ];

        return $names[$action] ?? ucfirst($action);
    }

    /**
     * Assign role to users
     */
    public function assignToUsers(Role $role, array $userIds)
    {
        $users = \App\Models\User::whereIn('id', $userIds)->get();
        
        foreach ($users as $user) {
            $user->assignRole($role);
        }
        
        return count($users);
    }

    /**
     * Remove role from users
     */
    public function removeFromUsers(Role $role, array $userIds)
    {
        $users = \App\Models\User::whereIn('id', $userIds)->get();
        
        foreach ($users as $user) {
            $user->removeRole($role);
        }
        
        return count($users);
    }

    /**
     * Get role statistics
     */
    public function getStatistics()
    {
        return [
            'total_roles' => Role::count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'system_roles' => count($this->systemRoles),
            'custom_roles' => Role::whereNotIn('name', $this->systemRoles)->count(),
            'total_permissions' => Permission::count(),
            'most_used_role' => Role::withCount('users')
                ->orderBy('users_count', 'desc')
                ->first(),
        ];
    }
}
