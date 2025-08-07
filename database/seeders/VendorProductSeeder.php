<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\VendorProducts\Models\VendorProduct;
use App\Modules\Products\Models\Product;
use App\Modules\Users\Models\User;
use App\Modules\Currencies\Models\Currency;

class VendorProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCurrency = Currency::where('code', 'TRY')->first();
        $usdCurrency = Currency::where('code', 'USD')->first();
        
        // Satıcıları al
        $vendors = User::role('vendor')->get();
        
        // Onaylanmış ürünleri al
        $products = Product::approved()->get();
        
        // Her satıcı için rastgele ürünler ekle
        foreach ($vendors as $index => $vendor) {
            // Her satıcı 5-10 arası ürün satacak
            $productCount = rand(5, 10);
            $selectedProducts = $products->random(min($productCount, $products->count()));
            
            foreach ($selectedProducts as $product) {
                // Satıcıya göre fiyat ayarlaması yap
                $priceMultiplier = 1 + (($index % 3) * 0.05); // %0, %5 veya %10 fark
                $vendorPrice = $product->default_price * $priceMultiplier;
                
                // Stok miktarını rastgele belirle
                $stockQuantity = rand(10, 100);
                
                // İndirim durumu
                $hasDiscount = rand(0, 100) < 30; // %30 ihtimalle indirimli
                $discount = null;
                
                if ($hasDiscount) {
                    $discount = [
                        'type' => rand(0, 1) ? 'percentage' : 'fixed',
                        'value' => rand(0, 1) ? rand(5, 25) : rand(50, 500),
                        'start_date' => now()->subDays(rand(1, 7)),
                        'end_date' => now()->addDays(rand(7, 30)),
                    ];
                }
                
                VendorProduct::create([
                    'relation_id' => $product->id,
                    'user_relation_id' => $vendor->id,
                    'price' => $vendorPrice,
                    'currency_id' => $defaultCurrency->id,
                    'condition' => $product->condition,
                    'stock_quantity' => $stockQuantity,
                    'min_sale_quantity' => 1,
                    'max_sale_quantity' => min(5, $stockQuantity),
                    'discount' => $discount,
                    'images' => [], // Satıcıya özel resimler eklenebilir
                    'status' => true,
                ]);
            }
            
            // Bazı satıcılar USD ile de satış yapsın
            if ($index % 2 == 0 && $usdCurrency) {
                $usdProduct = $products->random();
                VendorProduct::create([
                    'relation_id' => $usdProduct->id,
                    'user_relation_id' => $vendor->id,
                    'price' => round($usdProduct->default_price / $usdCurrency->exchange_rate, 2),
                    'currency_id' => $usdCurrency->id,
                    'condition' => $usdProduct->condition,
                    'stock_quantity' => rand(5, 20),
                    'min_sale_quantity' => 1,
                    'max_sale_quantity' => 3,
                    'status' => true,
                ]);
            }
        }
        
        // Bazı ürünleri stoksuz yap
        VendorProduct::inRandomOrder()->limit(5)->update(['stock_quantity' => 0]);
        
        // Bazı ürünleri pasif yap
        VendorProduct::inRandomOrder()->limit(3)->update(['status' => false]);
        
        $this->command->info('Satıcı ürünleri oluşturuldu.');
    }
}
