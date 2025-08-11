<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Tickets\Models\TicketCategory;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Genel Destek',
                'slug' => 'genel-destek',
                'description' => 'Genel sorular ve destek talepleri',
                'color' => '#6c757d',
                'order' => 1
            ],
            [
                'name' => 'Teknik Sorun',
                'slug' => 'teknik-sorun',
                'description' => 'Teknik problemler ve hatalar',
                'color' => '#dc3545',
                'order' => 2
            ],
            [
                'name' => 'Sipariş Sorunu',
                'slug' => 'siparis-sorunu',
                'description' => 'Sipariş ile ilgili sorunlar',
                'color' => '#ffc107',
                'order' => 3
            ],
            [
                'name' => 'Ödeme Problemi',
                'slug' => 'odeme-problemi',
                'description' => 'Ödeme ve fatura sorunları',
                'color' => '#fd7e14',
                'order' => 4
            ],
            [
                'name' => 'İade/Değişim',
                'slug' => 'iade-degisim',
                'description' => 'Ürün iade ve değişim talepleri',
                'color' => '#17a2b8',
                'order' => 5
            ],
            [
                'name' => 'Öneri/Şikayet',
                'slug' => 'oneri-sikayet',
                'description' => 'Öneri ve şikayetleriniz',
                'color' => '#28a745',
                'order' => 6
            ]
        ];

        foreach ($categories as $categoryData) {
            TicketCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Ticket kategorileri başarıyla oluşturuldu.');
    }
}