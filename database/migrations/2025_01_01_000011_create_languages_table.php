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
        Schema::create('languages', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('code', 10)->unique(); // Kodu (en-gb)
            $table->string('name'); // Dil Adı
            $table->string('locale'); // Locale (en_US.UTF-8, en_US, en-gb, en_gb, english)
            $table->foreignId('image_id')->nullable()->constrained('uploads')->onDelete('set null'); // Resim (bayrak)
            $table->integer('order')->default(0); // Sıralama
            $table->boolean('is_rtl')->default(false); // Sağdan sola yazılan dil mi?
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('code');
            $table->index('order');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
