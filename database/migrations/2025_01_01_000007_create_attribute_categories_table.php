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
        Schema::create('attribute_categories', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('name'); // Kategori Adı
            $table->foreignId('image_id')->nullable()->constrained('uploads')->onDelete('set null'); // Kategori Resmi
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_categories');
    }
};
