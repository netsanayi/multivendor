<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Kategori izinleri
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            
            // Ürün izinleri
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'products.approve',
            
            // Satıcı ürün izinleri
            'vendor-products.view',
            'vendor-products.create',
            'vendor-products.edit',
            'vendor-products.delete',
            
            // Marka izinleri
            'brands.view',
            'brands.create',
            'brands.edit',
            'brands.delete',
            
            // Özellik izinleri
            'attributes.view',
            'attributes.create',
            'attributes.edit',
            'attributes.delete',
            
            // Kullanıcı izinleri
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Rol izinleri
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // Blog izinleri
            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'blogs.delete',
            
            // Banner izinleri
            'banners.view',
            'banners.create',
            'banners.edit',
            'banners.delete',
            
            // Para birimi izinleri
            'currencies.view',
            'currencies.create',
            'currencies.edit',
            'currencies.delete',
            
            // Dil izinleri
            'languages.view',
            'languages.create',
            'languages.edit',
            'languages.delete',
            
            // Ayar izinleri
            'settings.view',
            'settings.edit',
            
            // Aktivite log izinleri
            'activity-log.view',
            
            // Adres izinleri
            'addresses.view',
            'addresses.create',
            'addresses.edit',
            'addresses.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin rolü - tüm izinler
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Vendor (Satıcı) rolü
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $vendorRole->givePermissionTo([
            'vendor-products.view',
            'vendor-products.create',
            'vendor-products.edit',
            'vendor-products.delete',
            'products.view',
            'categories.view',
            'brands.view',
            'attributes.view',
            'addresses.view',
            'addresses.create',
            'addresses.edit',
            'addresses.delete',
        ]);

        // Customer (Müşteri) rolü
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'products.view',
            'categories.view',
            'brands.view',
            'blogs.view',
            'addresses.view',
            'addresses.create',
            'addresses.edit',
            'addresses.delete',
        ]);

        // Editor rolü
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'categories.view',
            'categories.create',
            'categories.edit',
            'products.view',
            'products.create',
            'products.edit',
            'brands.view',
            'brands.create',
            'brands.edit',
            'attributes.view',
            'attributes.create',
            'attributes.edit',
            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'banners.view',
            'banners.create',
            'banners.edit',
        ]);

        $this->command->info('Roller ve izinler oluşturuldu.');
    }
}
