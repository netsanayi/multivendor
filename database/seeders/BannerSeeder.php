<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Banners\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'name' => 'Ana Sayfa - Yeni Sezon İndirimleri',
                'link' => '/kampanyalar/yeni-sezon',
                'position' => 'home',
                'order' => 1,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'click_count' => rand(100, 500),
                'view_count' => rand(1000, 5000),
                'status' => true,
            ],
            [
                'name' => 'Ana Sayfa - Elektronik Kampanyası',
                'link' => '/category/elektronik',
                'position' => 'home',
                'order' => 2,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'click_count' => rand(200, 800),
                'view_count' => rand(2000, 8000),
                'status' => true,
            ],
            [
                'name' => 'Ana Sayfa - Ücretsiz Kargo',
                'link' => '/hakkimizda/ucretsiz-kargo',
                'position' => 'home',
                'order' => 3,
                'start_date' => null, // Süresiz
                'end_date' => null,
                'click_count' => rand(50, 200),
                'view_count' => rand(500, 2000),
                'status' => true,
            ],
            [
                'name' => 'Kategori - Kış Koleksiyonu',
                'link' => '/kampanyalar/kis-koleksiyonu',
                'position' => 'category',
                'order' => 1,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(45),
                'click_count' => rand(100, 400),
                'view_count' => rand(1000, 4000),
                'status' => true,
            ],
            [
                'name' => 'Sidebar - Haftanın Fırsatları',
                'link' => '/kampanyalar/haftanin-firsatlari',
                'position' => 'sidebar',
                'order' => 1,
                'start_date' => now()->startOfWeek(),
                'end_date' => now()->endOfWeek(),
                'click_count' => rand(300, 1000),
                'view_count' => rand(3000, 10000),
                'status' => true,
            ],
            [
                'name' => 'Header - Yeni Üyelere Özel %20 İndirim',
                'link' => '/register',
                'position' => 'header',
                'order' => 1,
                'start_date' => null,
                'end_date' => null,
                'click_count' => rand(500, 2000),
                'view_count' => rand(5000, 20000),
                'status' => true,
            ],
            [
                'name' => 'Footer - Mobil Uygulama',
                'link' => '/mobil-uygulama',
                'position' => 'footer',
                'order' => 1,
                'start_date' => null,
                'end_date' => null,
                'click_count' => rand(100, 500),
                'view_count' => rand(1000, 5000),
                'status' => true,
            ],
            [
                'name' => 'Ürün Sayfası - İlgili Ürünler',
                'link' => null,
                'position' => 'product',
                'order' => 1,
                'start_date' => null,
                'end_date' => null,
                'click_count' => 0,
                'view_count' => rand(2000, 8000),
                'status' => true,
            ],
            // Süresi dolmuş banner
            [
                'name' => 'Eski Kampanya - Yılbaşı İndirimi',
                'link' => '/kampanyalar/yilbasi',
                'position' => 'home',
                'order' => 99,
                'start_date' => now()->subDays(45),
                'end_date' => now()->subDays(15),
                'click_count' => rand(1000, 3000),
                'view_count' => rand(10000, 30000),
                'status' => true, // Aktif ama süresi dolmuş
            ],
            // Pasif banner
            [
                'name' => 'Pasif Banner - Test',
                'link' => '/test',
                'position' => 'home',
                'order' => 100,
                'start_date' => null,
                'end_date' => null,
                'click_count' => 0,
                'view_count' => 0,
                'status' => false,
            ],
        ];
        
        foreach ($banners as $bannerData) {
            Banner::create($bannerData);
        }
        
        $this->command->info('Banner\'lar oluşturuldu.');
    }
}
