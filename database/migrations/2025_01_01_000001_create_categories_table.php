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
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('name'); // Kategori Adı
            $table->string('slug')->unique(); // URL'deki ismi (SLUG)
            $table->text('description')->nullable(); // Açıklama (HTML)
            $table->string('meta_title')->nullable(); // Meta Başlık
            $table->text('meta_description')->nullable(); // Meta Açıklama
            $table->string('meta_keywords')->nullable(); // Meta Kelimeleri
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade'); // Ana Kategori (kendi kendine referans)
            $table->foreignId('image_id')->nullable()->constrained('uploads')->onDelete('set null'); // Kategori Resmi
            $table->integer('column_count')->default(3); // Sütun Sayısı
            $table->integer('order')->default(0); // Sıralama
            $table->boolean('status')->default(true); // Kategori Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('slug');
            $table->index('parent_id');
            $table->index('status');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
