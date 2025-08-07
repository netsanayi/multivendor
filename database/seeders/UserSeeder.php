<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Users\Models\User;
use App\Modules\Currencies\Models\Currency;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCurrency = Currency::where('code', 'TRY')->first();

        // Admin kullanıcı
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone_number' => '+905551234567',
                'password' => Hash::make('password'),
                'default_currency_id' => $defaultCurrency->id,
                'status' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Satıcı kullanıcılar
        for ($i = 1; $i <= 3; $i++) {
            $vendor = User::firstOrCreate(
                ['email' => "vendor{$i}@example.com"],
                [
                    'first_name' => "Satıcı",
                    'last_name' => "{$i}",
                    'phone_number' => "+90555123456{$i}",
                    'password' => Hash::make('password'),
                    'default_currency_id' => $defaultCurrency->id,
                    'status' => true,
                    'email_verified_at' => now(),
                ]
            );
            $vendor->assignRole('vendor');

            // Her satıcıya adres ekle
            $vendor->addresses()->create([
                'address_name' => 'İşyeri',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
                'street' => 'Bağdat Caddesi',
                'road_name' => 'Şaşkınbakkal Mahallesi',
                'building_no' => (string)($i * 10),
                'floor' => (string)$i,
                'door_no' => (string)($i * 2),
                'company_type' => 'corporate',
                'company_name' => "Satıcı {$i} Ltd. Şti.",
                'tax_office' => 'Kadıköy Vergi Dairesi',
                'tax_no' => "123456789{$i}",
                'is_default' => true,
                'status' => true,
            ]);
        }

        // Müşteri kullanıcılar
        for ($i = 1; $i <= 5; $i++) {
            $customer = User::firstOrCreate(
                ['email' => "customer{$i}@example.com"],
                [
                    'first_name' => "Müşteri",
                    'last_name' => "{$i}",
                    'phone_number' => "+90555987654{$i}",
                    'password' => Hash::make('password'),
                    'default_currency_id' => $defaultCurrency->id,
                    'status' => true,
                    'email_verified_at' => now(),
                ]
            );
            $customer->assignRole('customer');

            // Her müşteriye adres ekle
            $customer->addresses()->create([
                'address_name' => 'Ev',
                'city' => 'İstanbul',
                'district' => 'Beşiktaş',
                'street' => 'Barbaros Bulvarı',
                'road_name' => 'Yıldız Mahallesi',
                'building_no' => (string)($i * 5),
                'floor' => (string)($i + 1),
                'door_no' => (string)($i * 3),
                'company_type' => 'individual',
                'tc_id_no' => "1234567890{$i}",
                'is_default' => true,
                'status' => true,
            ]);
        }

        // Editor kullanıcı
        $editor = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'first_name' => 'Editor',
                'last_name' => 'User',
                'phone_number' => '+905559999999',
                'password' => Hash::make('password'),
                'default_currency_id' => $defaultCurrency->id,
                'status' => true,
                'email_verified_at' => now(),
            ]
        );
        $editor->assignRole('editor');

        $this->command->info('Kullanıcılar oluşturuldu.');
    }
}
