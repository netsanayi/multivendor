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
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('name'); // Marka Adı
            $table->foreignId('image_id')->nullable()->constrained('uploads')->onDelete('set null'); // Marka Resmi
            $table->integer('order')->default(0); // Sıralama
            $table->jsonb('product_category_ids')->nullable(); // Kategori (Ürün kategorilerinden birden fazla seçilebilir)
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('name');
            $table->index('status');
            $table->index('order');
            
            // JSONB indeksi (PostgreSQL için GIN index)
            $table->index('product_category_ids', null, 'gin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
