<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\ProductAttributes\Models\ProductAttribute;
use App\Modules\AttributeCategories\Models\AttributeCategory;
use App\Modules\Categories\Models\Category;

class ProductAttributeSeeder extends Seeder
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
        $giyimCat = Category::where('name', 'Giyim')->first();
        $kozmetikCat = Category::where('name', 'Kozmetik & Kişisel Bakım')->first();

        // Özellik kategorilerini al
        $genelOzellikler = AttributeCategory::where('name', 'Genel Özellikler')->first();
        $teknikOzellikler = AttributeCategory::where('name', 'Teknik Özellikler')->first();
        $fizikselOzellikler = AttributeCategory::where('name', 'Fiziksel Özellikler')->first();
        $renkDesen = AttributeCategory::where('name', 'Renk ve Desen')->first();
        $malzeme = AttributeCategory::where('name', 'Malzeme')->first();
        $performans = AttributeCategory::where('name', 'Performans')->first();
        $baglanti = AttributeCategory::where('name', 'Bağlantı')->first();

        $attributes = [
            // Genel özellikler
            [
                'name' => 'Renk',
                'attribute_category_id' => $renkDesen->id,
                'product_category_ids' => [
                    $elektronikCat->id,
                    $giyimCat->id,
                    $kozmetikCat->id,
                ],
                'order' => 1,
                'values' => ['Siyah', 'Beyaz', 'Gri', 'Mavi', 'Kırmızı', 'Yeşil', 'Sarı', 'Turuncu', 'Mor', 'Pembe'],
                'status' => true,
            ],
            [
                'name' => 'Beden',
                'attribute_category_id' => $fizikselOzellikler->id,
                'product_category_ids' => [$giyimCat->id],
                'order' => 2,
                'values' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '38', '40', '42', '44', '46', '48'],
                'status' => true,
            ],
            [
                'name' => 'Numara',
                'attribute_category_id' => $fizikselOzellikler->id,
                'product_category_ids' => [
                    Category::where('name', 'Ayakkabı')->first()->id,
                ],
                'order' => 3,
                'values' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'status' => true,
            ],
            // Elektronik özellikleri
            [
                'name' => 'RAM Kapasitesi',
                'attribute_category_id' => $teknikOzellikler->id,
                'product_category_ids' => [
                    $bilgisayarCat->id,
                    $telefonCat->id,
                ],
                'order' => 4,
                'values' => ['2GB', '4GB', '6GB', '8GB', '12GB', '16GB', '32GB', '64GB'],
                'status' => true,
            ],
            [
                'name' => 'Depolama Kapasitesi',
                'attribute_category_id' => $teknikOzellikler->id,
                'product_category_ids' => [
                    $bilgisayarCat->id,
                    $telefonCat->id,
                    Category::where('name', 'Tablet')->first()->id,
                ],
                'order' => 5,
                'values' => ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB', '2TB'],
                'status' => true,
            ],
            [
                'name' => 'İşlemci',
                'attribute_category_id' => $teknikOzellikler->id,
                'product_category_ids' => [$bilgisayarCat->id],
                'order' => 6,
                'values' => ['Intel Core i3', 'Intel Core i5', 'Intel Core i7', 'Intel Core i9', 'AMD Ryzen 3', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9', 'Apple M1', 'Apple M2'],
                'status' => true,
            ],
            [
                'name' => 'Ekran Boyutu',
                'attribute_category_id' => $fizikselOzellikler->id,
                'product_category_ids' => [
                    $bilgisayarCat->id,
                    $telefonCat->id,
                    Category::where('name', 'Tablet')->first()->id,
                ],
                'order' => 7,
                'values' => ['5"', '5.5"', '6"', '6.5"', '7"', '8"', '10"', '11"', '13"', '14"', '15"', '17"'],
                'status' => true,
            ],
            [
                'name' => 'İşletim Sistemi',
                'attribute_category_id' => $teknikOzellikler->id,
                'product_category_ids' => [
                    $bilgisayarCat->id,
                    $telefonCat->id,
                ],
                'order' => 8,
                'values' => ['Windows 10', 'Windows 11', 'macOS', 'Linux', 'Android', 'iOS'],
                'status' => true,
            ],
            // Giyim özellikleri
            [
                'name' => 'Kumaş Türü',
                'attribute_category_id' => $malzeme->id,
                'product_category_ids' => [$giyimCat->id],
                'order' => 9,
                'values' => ['%100 Pamuk', 'Polyester', 'Viskon', 'Keten', 'Yün', 'İpek', 'Denim', 'Karışım'],
                'status' => true,
            ],
            [
                'name' => 'Kalıp',
                'attribute_category_id' => $fizikselOzellikler->id,
                'product_category_ids' => [$giyimCat->id],
                'order' => 10,
                'values' => ['Slim Fit', 'Regular Fit', 'Oversize', 'Skinny', 'Straight', 'Relaxed'],
                'status' => true,
            ],
            // Bağlantı özellikleri
            [
                'name' => 'Bağlantı Türü',
                'attribute_category_id' => $baglanti->id,
                'product_category_ids' => [$elektronikCat->id],
                'order' => 11,
                'values' => ['Wi-Fi', 'Bluetooth', 'USB', 'HDMI', 'USB-C', 'Lightning', '3.5mm Jack'],
                'status' => true,
            ],
            // Kozmetik özellikleri
            [
                'name' => 'Cilt Tipi',
                'attribute_category_id' => $genelOzellikler->id,
                'product_category_ids' => [$kozmetikCat->id],
                'order' => 12,
                'values' => ['Normal Cilt', 'Kuru Cilt', 'Yağlı Cilt', 'Karma Cilt', 'Hassas Cilt'],
                'status' => true,
            ],
            [
                'name' => 'SPF Değeri',
                'attribute_category_id' => $performans->id,
                'product_category_ids' => [$kozmetikCat->id],
                'order' => 13,
                'values' => ['SPF 15', 'SPF 30', 'SPF 50', 'SPF 50+'],
                'status' => true,
            ],
        ];

        foreach ($attributes as $attributeData) {
            ProductAttribute::firstOrCreate(
                [
                    'name' => $attributeData['name'],
                    'attribute_category_id' => $attributeData['attribute_category_id'],
                ],
                $attributeData
            );
        }

        $this->command->info('Ürün özellikleri oluşturuldu.');
    }
}
