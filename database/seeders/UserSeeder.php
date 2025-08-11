<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => true,
                'phone_number' => '+90 555 111 1111',
            ]
        );
        $superAdmin->assignRole('super-admin');

        // Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => true,
                'phone_number' => '+90 555 222 2222',
            ]
        );
        $admin->assignRole('admin');

        // Create Vendors
        $vendor1 = User::firstOrCreate(
            ['email' => 'vendor1@example.com'],
            [
                'name' => 'John Vendor',
                'first_name' => 'John',
                'last_name' => 'Vendor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => true,
                'phone_number' => '+90 555 333 3333',
            ]
        );
        $vendor1->assignRole('vendor');

        $vendor2 = User::firstOrCreate(
            ['email' => 'vendor2@example.com'],
            [
                'name' => 'Jane Seller',
                'first_name' => 'Jane',
                'last_name' => 'Seller',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => true,
                'phone_number' => '+90 555 444 4444',
            ]
        );
        $vendor2->assignRole('vendor');

        // Create Customers
        $customer1 = User::firstOrCreate(
            ['email' => 'customer1@example.com'],
            [
                'name' => 'Alice Customer',
                'first_name' => 'Alice',
                'last_name' => 'Customer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => true,
                'phone_number' => '+90 555 555 5555',
            ]
        );
        $customer1->assignRole('customer');

        $customer2 = User::firstOrCreate(
            ['email' => 'customer2@example.com'],
            [
                'name' => 'Bob Buyer',
                'first_name' => 'Bob',
                'last_name' => 'Buyer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => true,
                'phone_number' => '+90 555 666 6666',
            ]
        );
        $customer2->assignRole('customer');

        // Create test customer
        $testCustomer = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => true,
                'phone_number' => '+90 555 777 7777',
            ]
        );
        $testCustomer->assignRole('customer');

        $this->command->info('Users created successfully!');
        $this->command->table(
            ['Email', 'Name', 'Role'],
            [
                ['superadmin@example.com', 'Super Admin', 'super-admin'],
                ['admin@example.com', 'Admin User', 'admin'],
                ['vendor1@example.com', 'John Vendor', 'vendor'],
                ['vendor2@example.com', 'Jane Seller', 'vendor'],
                ['customer1@example.com', 'Alice Customer', 'customer'],
                ['customer2@example.com', 'Bob Buyer', 'customer'],
                ['test@example.com', 'Test User', 'customer'],
            ]
        );
        $this->command->info('Default password for all users: password');
    }
}
