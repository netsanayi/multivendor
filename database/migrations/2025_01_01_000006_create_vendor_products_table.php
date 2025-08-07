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
        Schema::create('vendor_products', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->foreignId('relation_id')->constrained('products'); // Ürünler Modülünden ürünler getirilecek ve ilişkilendirilecek
            $table->foreignId('user_relation_id')->constrained('users'); // Ürün ile hangi müşterinin ilişkisinin olduğu
            $table->decimal('price', 10, 2); // Ürün Fiyatı (satıcıya özel)
            $table->foreignId('currency_id')->constrained('currencies'); // Döviz Cinsi
            $table->string('condition')->default('new'); // Ürün Durumu (new, used, refurbished)
            $table->integer('stock_quantity')->default(0); // Stok Adedi (satıcıya özel)
            $table->integer('min_sale_quantity')->default(1); // Minimum Satış Miktarı (satıcıya özel)
            $table->integer('max_sale_quantity')->nullable(); // Maksimum Satış Miktarı (satıcıya özel)
            $table->jsonb('discount')->nullable(); // İndirim bilgileri
            $table->jsonb('images')->nullable(); // Resimler (vendor_products türünde)
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('relation_id');
            $table->index('user_relation_id');
            $table->index('status');
            $table->index(['user_relation_id', 'status']);
            $table->index(['relation_id', 'user_relation_id']);
            
            // Benzersiz kısıtlama: Bir satıcı bir ürünü sadece bir kez listeleyebilir
            $table->unique(['relation_id', 'user_relation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_products');
    }
};
