<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Brands\Models\Brand;
use App\Modules\Categories\Models\Category;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kategori ID'lerini al
        $elektronikCat = Category::where('name', 'Elektronik')->first();
        $bilgisayarCat = Category::where('name', 'Bilgisayar')->first();
        $telefonCat = Category::where('name', 'Telefon')->first();
        $tabletCat = Category::where('name', 'Tablet')->first();
        $giyimCat = Category::where('name', 'Giyim')->first();
        $kozmetikCat = Category::where('name', 'Kozmetik & Kişisel Bakım')->first();

        $brands = [
            // Elektronik markaları
            [
                'name' => 'Apple',
                'order' => 1,
                'status' => true,
                'product_category_ids' => [
                    $elektronikCat->id,
                    $bilgisayarCat->id,
                    $telefonCat->id,
                    $tabletCat->id,
                ],
            ],
            [
                'name' => 'Samsung',
                'order' => 2,
                'status' => true,
                'product_category_ids' => [
                    $elektronikCat->id,
                    $telefonCat->id,
                    $tabletCat->id,
                ],
            ],
            [
                'name' => 'Dell',
                'order' => 3,
                'status' => true,
                'product_category_ids' => [
                    $elektronikCat->id,
                    $bilgisayarCat->id,
                ],
            ],
            [
                'name' => 'HP',
                'order' => 4,
                'status' => true,
                'product_category_ids' => [
                    $elektronikCat->id,
                    $bilgisayarCat->id,
                ],
            ],
            [
                'name' => 'Lenovo',
                'order' => 5,
                'status' => true,
                'product_category_ids' => [
                    $elektronikCat->id,
                    $bilgisayarCat->id,
                    $tabletCat->id,
                ],
            ],
            [
                'name' => 'Xiaomi',
                'order' => 6,
                'status' => true,
                'product_category_ids' => [
                    $elektronikCat->id,
                    $telefonCat->id,
                ],
            ],
            // Giyim markaları
            [
                'name' => 'Nike',
                'order' => 7,
                'status' => true,
                'product_category_ids' => [$giyimCat->id],
            ],
            [
                'name' => 'Adidas',
                'order' => 8,
                'status' => true,
                'product_category_ids' => [$giyimCat->id],
            ],
            [
                'name' => 'Zara',
                'order' => 9,
                'status' => true,
                'product_category_ids' => [$giyimCat->id],
            ],
            [
                'name' => 'H&M',
                'order' => 10,
                'status' => true,
                'product_category_ids' => [$giyimCat->id],
            ],
            [
                'name' => 'Mango',
                'order' => 11,
                'status' => true,
                'product_category_ids' => [$giyimCat->id],
            ],
            // Kozmetik markaları
            [
                'name' => 'L\'Oréal',
                'order' => 12,
                'status' => true,
                'product_category_ids' => [$kozmetikCat->id],
            ],
            [
                'name' => 'Maybelline',
                'order' => 13,
                'status' => true,
                'product_category_ids' => [$kozmetikCat->id],
            ],
            [
                'name' => 'MAC',
                'order' => 14,
                'status' => true,
                'product_category_ids' => [$kozmetikCat->id],
            ],
            [
                'name' => 'Chanel',
                'order' => 15,
                'status' => true,
                'product_category_ids' => [$kozmetikCat->id],
            ],
            [
                'name' => 'Dior',
                'order' => 16,
                'status' => true,
                'product_category_ids' => [$kozmetikCat->id],
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::firstOrCreate(
                ['name' => $brandData['name']],
                $brandData
            );
        }

        $this->command->info('Markalar oluşturuldu.');
    }
}
