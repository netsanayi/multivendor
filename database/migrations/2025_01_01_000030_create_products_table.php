<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('name'); // Ürün Adı
            $table->string('slug')->unique(); // SLUG (URL'deki ismi)
            $table->string('product_code')->unique(); // Ürün Kodu
            $table->text('description')->nullable(); // Açıklama (HTML)
            $table->string('meta_title')->nullable(); // Meta Başlık
            $table->text('meta_description')->nullable(); // Meta Açıklama
            $table->string('meta_keywords')->nullable(); // Meta Kelimeleri
            $table->jsonb('tags')->nullable(); // Ürün Etiketleri (JSONB dizisi)
            $table->string('barcode')->nullable(); // Barkod
            $table->decimal('default_price', 10, 2); // Varsayılan Ürün Fiyatı
            $table->foreignId('default_currency_id')->constrained('currencies'); // Varsayılan Döviz Cinsi
            $table->string('condition')->default('new'); // Ürün Durumu (new, used, refurbished)
            $table->integer('stock_quantity')->default(0); // Stok Adedi
            $table->integer('min_sale_quantity')->default(1); // Minimum Satış Miktarı
            $table->integer('max_sale_quantity')->nullable(); // Maksimum Satış Miktarı
            $table->decimal('length', 8, 2)->nullable(); // Ürün Uzunluğu (metre)
            $table->decimal('width', 8, 2)->nullable(); // Ürün Genişliği (metre)
            $table->decimal('height', 8, 2)->nullable(); // Ürün Yüksekliği (metre)
            $table->decimal('weight', 8, 2)->nullable(); // Ürün Ağırlığı (kilogram)
            $table->string('approval_status')->default('pending'); // Onay Durumu (approved, pending)
            $table->foreignId('category_id')->constrained('categories'); // Kategori
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null'); // Marka
            $table->jsonb('attributes')->nullable(); // Özellikler (dinamik özellikler için JSONB)
            $table->jsonb('similar_products')->nullable(); // Benzer Ürünler (ürün OID'leri dizisi)
            $table->jsonb('discount')->nullable(); // İndirim bilgileri
            $table->jsonb('images')->nullable(); // Resimler (uploads modülünden OID'ler)
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('slug');
            $table->index('product_code');
            $table->index('barcode');
            $table->index('approval_status');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('status');
            $table->index(['status', 'approval_status']);
            
            // JSONB indeksleri (PostgreSQL için GIN index)
            $table->index('tags', null, 'gin');
            $table->index('attributes', null, 'gin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
