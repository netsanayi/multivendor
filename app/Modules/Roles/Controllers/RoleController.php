<?php

namespace App\Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Roles\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::query();

        // Arama
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sıralama
        $query->orderBy('name', 'asc');

        $roles = $query->paginate(20);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = $this->getModules();
        return view('roles.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create($validated);

            // İzinleri sync et
            $this->syncPermissions($role, $validated['permissions']);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $role->toArray()])
                ->log('Rol oluşturuldu');

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Rol oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('users');
        $modules = $this->getModules();
        
        return view('roles.show', compact('role', 'modules'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $modules = $this->getModules();
        return view('roles.edit', compact('role', 'modules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldAttributes = $role->toArray();
            $role->update($validated);

            // İzinleri sync et
            $this->syncPermissions($role, $validated['permissions']);

            // Log aktiviteyi kaydet
            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldAttributes,
                    'attributes' => $role->toArray()
                ])
                ->log('Rol güncellendi');

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol başarıyla güncellendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Rol güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Kullanıcıları kontrol et
        if ($role->users()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Kullanıcıları olan bir rol silinemez.');
        }

        // Sistem rollerini silmeyi engelle
        if (in_array($role->name, ['Admin', 'Vendor', 'Customer'])) {
            return redirect()
                ->back()
                ->with('error', 'Sistem rolleri silinemez.');
        }

        DB::beginTransaction();
        try {
            // Log aktiviteyi kaydet
            activity()
                ->performedOn($role)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $role->toArray()])
                ->log('Rol silindi');

            $role->delete();

            DB::commit();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Rol başarıyla silindi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Rol silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Modülleri ve izinleri getir
     */
    private function getModules()
    {
        return [
            'categories' => [
                'name' => 'Kategoriler',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'products' => [
                'name' => 'Ürünler',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'brands' => [
                'name' => 'Markalar',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'vendor_products' => [
                'name' => 'Müşteri Ürünleri',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'product_attributes' => [
                'name' => 'Ürün Özellikleri',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'users' => [
                'name' => 'Kullanıcılar',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'roles' => [
                'name' => 'Roller',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'blogs' => [
                'name' => 'Blog',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'banners' => [
                'name' => 'Banner',
                'permissions' => ['view', 'create', 'edit', 'delete']
            ],
            'settings' => [
                'name' => 'Ayarlar',
                'permissions' => ['view', 'edit']
            ],
            'reports' => [
                'name' => 'Raporlar',
                'permissions' => ['view']
            ],
        ];
    }

    /**
     * Rol izinlerini sync et
     */
    private function syncPermissions(Role $role, array $permissions)
    {
        $formattedPermissions = [];
        
        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                $formattedPermissions[] = $module . '.' . $action;
            }
        }

        // Spatie paketini kullanıyorsak
        if (method_exists($role, 'syncPermissions')) {
            $role->syncPermissions($formattedPermissions);
        } else {
            // Manuel olarak permissions JSON'a kaydet
            $role->permissions = $permissions;
            $role->save();
        }
    }
}
