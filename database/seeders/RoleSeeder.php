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

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $vendor = Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Define permissions
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Role management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // Product management
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'products.approve',
            
            // Category management
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            
            // Brand management
            'brands.view',
            'brands.create',
            'brands.edit',
            'brands.delete',
            
            // Order management
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.process',
            
            // Vendor management
            'vendors.view',
            'vendors.create',
            'vendors.edit',
            'vendors.delete',
            'vendors.approve',
            
            // Settings
            'settings.view',
            'settings.edit',
            
            // Reports
            'reports.view',
            'reports.export',
            
            // Dashboard
            'dashboard.view',
            'dashboard.vendor',
            'dashboard.admin',
            
            // Tickets
            'tickets.view',
            'tickets.create',
            'tickets.respond',
            'tickets.manage',
            
            // Messages
            'messages.view',
            'messages.send',
            
            // Wishlists
            'wishlists.view',
            'wishlists.create',
            'wishlists.delete',
            
            // Blog management
            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'blogs.delete',
            'blogs.publish',
            
            // Address management
            'addresses.view',
            'addresses.create',
            'addresses.edit',
            'addresses.delete',
            'addresses.update',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        
        // Super Admin gets all permissions
        $superAdmin->givePermissionTo(Permission::all());
        
        // Admin gets most permissions except super admin specific ones
        $admin->givePermissionTo([
            'users.view',
            'users.create',
            'users.edit',
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'products.approve',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'brands.view',
            'brands.create',
            'brands.edit',
            'brands.delete',
            'orders.view',
            'orders.edit',
            'orders.process',
            'vendors.view',
            'vendors.edit',
            'vendors.approve',
            'settings.view',
            'reports.view',
            'reports.export',
            'dashboard.view',
            'dashboard.admin',
            'tickets.view',
            'tickets.respond',
            'tickets.manage',
            'messages.view',
            'messages.send',
            'addresses.view',
            'addresses.create',
            'addresses.edit',
            'addresses.delete',
            'addresses.update',
        ]);
        
        // Vendor permissions
        $vendor->givePermissionTo([
            'products.view',
            'products.create',
            'products.edit',
            'orders.view',
            'dashboard.view',
            'dashboard.vendor',
            'reports.view',
            'tickets.view',
            'tickets.create',
            'messages.view',
            'messages.send',
        ]);
        
        // Editor permissions
        $editor->givePermissionTo([
            'products.view',
            'categories.view',
            'brands.view',
            'dashboard.view',
            'blogs.view',
            'blogs.create',
            'blogs.edit',
            'blogs.publish',
            'messages.view',
            'messages.send',
        ]);
        
        // Customer permissions
        $customer->givePermissionTo([
            'products.view',
            'orders.view',
            'orders.create',
            'dashboard.view',
            'tickets.view',
            'tickets.create',
            'messages.view',
            'messages.send',
            'wishlists.view',
            'wishlists.create',
            'wishlists.delete',
            'addresses.view',
            'addresses.create',
            'addresses.edit',
            'addresses.delete',
        ]);
    }
}
