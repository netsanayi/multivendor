<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Categories\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'description' => 'Elektronik ürünler ve aksesuarlar',
                'meta_title' => 'Elektronik Ürünler - En Uygun Fiyatlar',
                'meta_description' => 'Elektronik ürünler, bilgisayar, telefon, tablet ve aksesuarları en uygun fiyatlarla.',
                'meta_keywords' => 'elektronik, bilgisayar, telefon, tablet',
                'column_count' => 4,
                'order' => 1,
                'status' => true,
                'children' => [
                    [
                        'name' => 'Bilgisayar',
                        'description' => 'Masaüstü ve dizüstü bilgisayarlar',
                        'column_count' => 3,
                        'order' => 1,
                        'status' => true,
                    ],
                    [
                        'name' => 'Telefon',
                        'description' => 'Akıllı telefonlar ve aksesuarları',
                        'column_count' => 3,
                        'order' => 2,
                        'status' => true,
                    ],
                    [
                        'name' => 'Tablet',
                        'description' => 'Tabletler ve aksesuarları',
                        'column_count' => 3,
                        'order' => 3,
                        'status' => true,
                    ],
                    [
                        'name' => 'Aksesuarlar',
                        'description' => 'Elektronik aksesuarlar',
                        'column_count' => 3,
                        'order' => 4,
                        'status' => true,
                    ],
                ],
            ],
            [
                'name' => 'Giyim',
                'description' => 'Kadın, erkek ve çocuk giyim ürünleri',
                'meta_title' => 'Giyim ve Moda - Trend Ürünler',
                'meta_description' => 'En trend kadın, erkek ve çocuk giyim ürünleri uygun fiyatlarla.',
                'meta_keywords' => 'giyim, moda, kadın, erkek, çocuk',
                'column_count' => 4,
                'order' => 2,
                'status' => true,
                'children' => [
                    [
                        'name' => 'Kadın Giyim',
                        'description' => 'Kadın giyim ürünleri',
                        'column_count' => 3,
                        'order' => 1,
                        'status' => true,
                    ],
                    [
                        'name' => 'Erkek Giyim',
                        'description' => 'Erkek giyim ürünleri',
                        'column_count' => 3,
                        'order' => 2,
                        'status' => true,
                    ],
                    [
                        'name' => 'Çocuk Giyim',
                        'description' => 'Çocuk giyim ürünleri',
                        'column_count' => 3,
                        'order' => 3,
                        'status' => true,
                    ],
                    [
                        'name' => 'Ayakkabı',
                        'description' => 'Kadın, erkek ve çocuk ayakkabıları',
                        'column_count' => 3,
                        'order' => 4,
                        'status' => true,
                    ],
                ],
            ],
            [
                'name' => 'Ev & Yaşam',
                'description' => 'Ev dekorasyonu ve yaşam ürünleri',
                'meta_title' => 'Ev & Yaşam Ürünleri',
                'meta_description' => 'Ev dekorasyonu, mobilya ve yaşam ürünleri en uygun fiyatlarla.',
                'meta_keywords' => 'ev, dekorasyon, mobilya, yaşam',
                'column_count' => 4,
                'order' => 3,
                'status' => true,
                'children' => [
                    [
                        'name' => 'Mobilya',
                        'description' => 'Ev ve ofis mobilyaları',
                        'column_count' => 3,
                        'order' => 1,
                        'status' => true,
                    ],
                    [
                        'name' => 'Dekorasyon',
                        'description' => 'Ev dekorasyon ürünleri',
                        'column_count' => 3,
                        'order' => 2,
                        'status' => true,
                    ],
                    [
                        'name' => 'Mutfak',
                        'description' => 'Mutfak gereçleri ve ekipmanları',
                        'column_count' => 3,
                        'order' => 3,
                        'status' => true,
                    ],
                    [
                        'name' => 'Banyo',
                        'description' => 'Banyo ürünleri ve aksesuarları',
                        'column_count' => 3,
                        'order' => 4,
                        'status' => true,
                    ],
                ],
            ],
            [
                'name' => 'Kozmetik & Kişisel Bakım',
                'description' => 'Kozmetik ve kişisel bakım ürünleri',
                'meta_title' => 'Kozmetik & Kişisel Bakım',
                'meta_description' => 'En kaliteli kozmetik ve kişisel bakım ürünleri.',
                'meta_keywords' => 'kozmetik, bakım, güzellik, parfüm',
                'column_count' => 4,
                'order' => 4,
                'status' => true,
                'children' => [
                    [
                        'name' => 'Makyaj',
                        'description' => 'Makyaj ürünleri',
                        'column_count' => 3,
                        'order' => 1,
                        'status' => true,
                    ],
                    [
                        'name' => 'Parfüm',
                        'description' => 'Kadın ve erkek parfümleri',
                        'column_count' => 3,
                        'order' => 2,
                        'status' => true,
                    ],
                    [
                        'name' => 'Cilt Bakımı',
                        'description' => 'Cilt bakım ürünleri',
                        'column_count' => 3,
                        'order' => 3,
                        'status' => true,
                    ],
                    [
                        'name' => 'Saç Bakımı',
                        'description' => 'Saç bakım ürünleri',
                        'column_count' => 3,
                        'order' => 4,
                        'status' => true,
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $category = Category::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );

            // Alt kategorileri ekle
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                Category::firstOrCreate(
                    ['name' => $childData['name'], 'parent_id' => $category->id],
                    $childData
                );
            }
        }

        $this->command->info('Kategoriler oluşturuldu.');
    }
}
