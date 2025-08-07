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
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('name'); // Banner Adı
            $table->foreignId('image_id')->nullable()->constrained('uploads')->onDelete('set null'); // Banner resmi
            $table->string('link')->nullable(); // Tıklandığında gidilecek link
            $table->string('position')->default('home'); // Görüneceği pozisyon (home, category, product vb.)
            $table->integer('order')->default(0); // Sıralama
            $table->timestamp('start_date')->nullable(); // Başlangıç tarihi
            $table->timestamp('end_date')->nullable(); // Bitiş tarihi
            $table->integer('click_count')->default(0); // Tıklanma sayısı
            $table->integer('view_count')->default(0); // Görüntülenme sayısı
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('position');
            $table->index('order');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
