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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('name'); // Özellik Adı
            $table->foreignId('image_id')->nullable()->constrained('uploads')->onDelete('set null'); // Özellik Resmi
            $table->foreignId('attribute_category_id')->constrained('attribute_categories'); // Özellik Kategorisi
            $table->jsonb('product_category_ids')->nullable(); // Ürün Kategorisi (birden fazla seçilebilir)
            $table->integer('order')->default(0); // Özellik Sırası
            $table->jsonb('values')->nullable(); // Özellik Değeri (birden fazla değer)
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('name');
            $table->index('attribute_category_id');
            $table->index('order');
            $table->index('status');
            
            // JSONB indeksleri (PostgreSQL için GIN index)
            $table->index('product_category_ids', null, 'gin');
            $table->index('values', null, 'gin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
