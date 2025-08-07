<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Currencies\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Türk Lirası',
                'code' => 'TRY',
                'symbol' => '₺',
                'position' => 'right',
                'exchange_rate' => 1.0000,
                'status' => true,
            ],
            [
                'name' => 'ABD Doları',
                'code' => 'USD',
                'symbol' => '$',
                'position' => 'left',
                'exchange_rate' => 32.5000,
                'status' => true,
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'position' => 'left',
                'exchange_rate' => 35.2000,
                'status' => true,
            ],
            [
                'name' => 'İngiliz Sterlini',
                'code' => 'GBP',
                'symbol' => '£',
                'position' => 'left',
                'exchange_rate' => 41.5000,
                'status' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }

        $this->command->info('Para birimleri oluşturuldu.');
    }
}
