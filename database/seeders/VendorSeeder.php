<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\VendorDashboard\Models\VendorCommission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create vendor role if not exists
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        
        // Create vendor permissions
        $permissions = [
            'view-vendor-dashboard',
            'manage-own-products',
            'view-own-earnings',
            'request-payout',
            'view-own-analytics',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Assign permissions to vendor role
        $vendorRole->syncPermissions($permissions);
        
        // Create test vendors
        $vendors = [
            [
                'name' => 'Test Vendor 1',
                'email' => 'vendor1@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Test Vendor 2',
                'email' => 'vendor2@example.com',
                'password' => bcrypt('password'),
            ],
        ];
        
        foreach ($vendors as $vendorData) {
            $vendor = User::firstOrCreate(
                ['email' => $vendorData['email']],
                $vendorData
            );
            
            // Assign vendor role
            $vendor->assignRole('vendor');
            
            // Create commission settings
            VendorCommission::firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'commission_rate' => rand(5, 20),
                    'commission_type' => 'percentage',
                    'is_active' => true,
                    'notes' => 'Standart komisyon oranı',
                ]
            );
        }
        
        echo "Vendor test verileri oluşturuldu.\n";
        echo "Test hesapları:\n";
        echo "Email: vendor1@example.com, Şifre: password\n";
        echo "Email: vendor2@example.com, Şifre: password\n";
    }
}
