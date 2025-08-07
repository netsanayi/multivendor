<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Products\Models\Product;
use App\Modules\Categories\Models\Category;
use App\Modules\Brands\Models\Brand;
use App\Modules\Currencies\Models\Currency;
use App\Modules\ProductAttributes\Models\ProductAttribute;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCurrency = Currency::where('code', 'TRY')->first();
        
        // Kategorileri al
        $bilgisayarCat = Category::where('name', 'Bilgisayar')->first();
        $telefonCat = Category::where('name', 'Telefon')->first();
        $kadinGiyimCat = Category::where('name', 'Kadın Giyim')->first();
        $erkekGiyimCat = Category::where('name', 'Erkek Giyim')->first();
        $makyajCat = Category::where('name', 'Makyaj')->first();
        
        // Markaları al
        $apple = Brand::where('name', 'Apple')->first();
        $samsung = Brand::where('name', 'Samsung')->first();
        $dell = Brand::where('name', 'Dell')->first();
        $zara = Brand::where('name', 'Zara')->first();
        $hm = Brand::where('name', 'H&M')->first();
        $loreal = Brand::where('name', 'L\'Oréal')->first();
        
        // Özellikleri al
        $renkAttr = ProductAttribute::where('name', 'Renk')->first();
        $ramAttr = ProductAttribute::where('name', 'RAM Kapasitesi')->first();
        $storageAttr = ProductAttribute::where('name', 'Depolama Kapasitesi')->first();
        $bedenAttr = ProductAttribute::where('name', 'Beden')->first();
        
        $products = [
            // Bilgisayar ürünleri
            [
                'name' => 'MacBook Pro 14" M3',
                'product_code' => 'MBP14M3',
                'description' => '<p>Apple M3 çip ile güçlendirilmiş MacBook Pro 14 inç. Profesyoneller için tasarlandı.</p>',
                'meta_title' => 'MacBook Pro 14" M3 - Apple',
                'meta_description' => 'Apple M3 çipli MacBook Pro 14 inç laptop.',
                'tags' => ['macbook', 'laptop', 'apple', 'm3'],
                'barcode' => '1234567890123',
                'default_price' => 84999.00,
                'default_currency_id' => $defaultCurrency->id,
                'condition' => 'new',
                'stock_quantity' => 50,
                'min_sale_quantity' => 1,
                'max_sale_quantity' => 2,
                'weight' => 1.6,
                'approval_status' => 'approved',
                'category_id' => $bilgisayarCat->id,
                'brand_id' => $apple->id,
                'attributes' => [
                    $renkAttr->id => 'Uzay Grisi',
                    $ramAttr->id => '16GB',
                    $storageAttr->id => '512GB',
                ],
                'status' => true,
            ],
            [
                'name' => 'Dell XPS 15',
                'product_code' => 'DELLXPS15',
                'description' => '<p>Dell XPS 15, yüksek performans ve şık tasarımı bir araya getiriyor.</p>',
                'tags' => ['dell', 'laptop', 'xps'],
                'default_price' => 65999.00,
                'default_currency_id' => $defaultCurrency->id,
                'condition' => 'new',
                'stock_quantity' => 30,
                'min_sale_quantity' => 1,
                'max_sale_quantity' => 3,
                'weight' => 1.8,
                'approval_status' => 'approved',
                'category_id' => $bilgisayarCat->id,
                'brand_id' => $dell->id,
                'attributes' => [
                    $renkAttr->id => 'Gümüş',
                    $ramAttr->id => '32GB',
                    $storageAttr->id => '1TB',
                ],
                'status' => true,
            ],
            // Telefon ürünleri
            [
                'name' => 'iPhone 15 Pro',
                'product_code' => 'IP15PRO',
                'description' => '<p>A17 Pro çip ve titanium tasarım ile iPhone 15 Pro.</p>',
                'tags' => ['iphone', 'apple', 'telefon', '5g'],
                'default_price' => 64999.00,
                'default_currency_id' => $defaultCurrency->id,
                'condition' => 'new',
                'stock_quantity' => 100,
                'min_sale_quantity' => 1,
                'max_sale_quantity' => 2,
                'weight' => 0.187,
                'approval_status' => 'approved',
                'category_id' => $telefonCat->id,
                'brand_id' => $apple->id,
                'attributes' => [
                    $renkAttr->id => 'Doğal Titanyum',
                    $storageAttr->id => '256GB',
                ],
                'discount' => [
                    'type' => 'percentage',
                    'value' => 10,
                    'start_date' => now()->subDays(5),
                    'end_date' => now()->addDays(25),
                ],
                'status' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'product_code' => 'SGS24U',
                'description' => '<p>Galaxy AI ile güçlendirilmiş Samsung Galaxy S24 Ultra.</p>',
                'tags' => ['samsung', 'galaxy', 'telefon', '5g', 'ai'],
                'default_price' => 54999.00,
                'default_currency_id' => $defaultCurrency->id,
                'condition' => 'new',
                'stock_quantity' => 75,
                'min_sale_quantity' => 1,
                'max_sale_quantity' => 3,
                'weight' => 0.233,
                'approval_status' => 'approved',
                'category_id' => $telefonCat->id,
                'brand_id' => $samsung->id,
                'attributes' => [
                    $renkAttr->id => 'Titanium Black',
                    $ramAttr->id => '12GB',
                    $storageAttr->id => '512GB',
                ],
                'status' => true,
            ],
            // Giyim ürünleri
            [
                'name' => 'Zara Kadın Blazer Ceket',
                'product_code' => 'ZRBLZ001',
                'description' => '<p>Şık ve modern tasarım Zara kadın blazer ceket.</p>',
                'tags' => ['blazer', 'ceket', 'kadın', 'ofis'],
                'default_price' => 1299.00,
                'default_currency_id' => $defaultCurrency->id,
                'condition' => 'new',
                'stock_quantity' => 200,
                'min_sale_quantity' => 1,
                'max_sale_quantity' => 5,
                'weight' => 0.5,
                'approval_status' => 'approved',
                'category_id' => $kadinGiyimCat->id,
                'brand_id' => $zara->id,
                'attributes' => [
                    $renkAttr->id => 'Siyah',
                    $bedenAttr->id => 'M',
                ],
                'status' => true,
            ],
            [
                'name' => 'H&M Erkek Gömlek',
                'product_code' => 'HMGMK001',
                'description' => '<p>%100 pamuk, slim fit erkek gömlek.</p>',
                'tags' => ['gömlek', 'erkek', 'pamuk', 'slim fit'],
                'default_price' => 599.00,
                'default_currency_id' => $defaultCurrency->id,
                'condition' => 'new',
                'stock_quantity' => 150,
                'min_sale_quantity' => 1,
                'max_sale_quantity' => 10,
                'weight' => 0.2,
                'approval_status' => 'approved',
                'category_id' => $erkekGiyimCat->id,
                'brand_id' => $hm->id,
                'attributes' => [
                    $renkAttr->id => 'Beyaz',
                    $bedenAttr->id => 'L',
                ],
                'status' => true,
            ],
            // Kozmetik ürünleri
            [
                'name' => 'L\'Oréal True Match Fondöten',
                'product_code' => 'LORTM001',
                'description' => '<p>Doğal görünüm sağlayan True Match fondöten.</p>',
                'tags' => ['fondöten', 'makyaj', 'yüz'],
                'default_price' => 349.00,
                'default_currency_id' => $defaultCurrency->id,
                'condition' => 'new',
                'stock_quantity' => 300,
                'min_sale_quantity' => 1,
                'max_sale_quantity' => 5,
                'weight' => 0.03,
                'approval_status' => 'approved',
                'category_id' => $makyajCat->id,
                'brand_id' => $loreal->id,
                'status' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Onay bekleyen birkaç ürün ekle
        Product::create([
            'name' => 'Test Ürün - Onay Bekliyor',
            'product_code' => 'TEST001',
            'description' => '<p>Bu ürün onay bekliyor.</p>',
            'tags' => ['test'],
            'default_price' => 100.00,
            'default_currency_id' => $defaultCurrency->id,
            'condition' => 'new',
            'stock_quantity' => 10,
            'min_sale_quantity' => 1,
            'approval_status' => 'pending',
            'category_id' => $bilgisayarCat->id,
            'brand_id' => $dell->id,
            'status' => true,
        ]);

        $this->command->info('Ürünler oluşturuldu.');
    }
}
