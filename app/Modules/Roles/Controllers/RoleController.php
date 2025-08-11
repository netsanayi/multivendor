<?php

namespace App\Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Roles\Models\Role;
use App\Modules\Roles\Requests\StoreRoleRequest;
use App\Modules\Roles\Requests\UpdateRoleRequest;
use App\Modules\Roles\Services\RoleService;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
        
        // Middleware
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only(['create', 'store']);
        $this->middleware('permission:roles.edit')->only(['edit', 'update']);
        $this->middleware('permission:roles.delete')->only('destroy');
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $roles = $this->roleService->getAllWithFilters($request->all());
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = $this->roleService->getGroupedPermissions();
        $modules = $this->roleService->getModulesWithPermissions();
        
        return view('roles.create', compact('permissions', 'modules'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            $role = $this->roleService->create($request->validated());
            
            // Sync permissions
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
            
            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Rol oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        // System roles cannot be edited
        if ($this->roleService->isSystemRole($role)) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Sistem rolleri düzenlenemez.');
        }
        
        $permissions = $this->roleService->getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        // System roles cannot be edited
        if ($this->roleService->isSystemRole($role)) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Sistem rolleri düzenlenemez.');
        }
        
        try {
            $this->roleService->update($role, $request->validated());
            
            // Sync permissions
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
            
            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol başarıyla güncellendi.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Rol güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        try {
            if (!$role->canBeDeleted()) {
                return redirect()
                    ->back()
                    ->with('error', 'Bu rol silinemez. Sistem rolü veya kullanıcıları var.');
            }
            
            $role->delete();
            
            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Rol silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Toggle role status (active/inactive)
     */
    public function toggleStatus(Role $role)
    {
        try {
            $role->toggleStatus();
            
            return redirect()
                ->back()
                ->with('success', 'Rol durumu güncellendi.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Durum güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Clone a role
     */
    public function clone(Role $role)
    {
        try {
            $newRole = $this->roleService->cloneRole($role);
            
            return redirect()
                ->route('admin.roles.edit', $newRole)
                ->with('success', 'Rol başarıyla kopyalandı. Lütfen yeni rol bilgilerini düzenleyin.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Rol kopyalanırken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
