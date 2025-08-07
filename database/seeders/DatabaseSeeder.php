<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Önce para birimlerini oluştur
        $this->call(CurrencySeeder::class);
        
        // Dilleri oluştur
        $this->call(LanguageSeeder::class);
        
        // Rolleri oluştur
        $this->call(RoleSeeder::class);
        
        // Kullanıcıları oluştur
        $this->call(UserSeeder::class);
        
        // Kategorileri oluştur
        $this->call(CategorySeeder::class);
        
        // Markaları oluştur
        $this->call(BrandSeeder::class);
        
        // Özellik kategorilerini oluştur
        $this->call(AttributeCategorySeeder::class);
        
        // Ürün özelliklerini oluştur
        $this->call(ProductAttributeSeeder::class);
        
        // Ürünleri oluştur
        $this->call(ProductSeeder::class);
        
        // Satıcı ürünlerini oluştur
        $this->call(VendorProductSeeder::class);
        
        // Blog yazılarını oluştur
        $this->call(BlogSeeder::class);
        
        // Banner'ları oluştur
        $this->call(BannerSeeder::class);
    }
}
