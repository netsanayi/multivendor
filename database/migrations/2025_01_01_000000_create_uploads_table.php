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
        Schema::create('uploads', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('name'); // Dosya adı
            $table->string('type'); // Türü (Ürün, Kategori, Marka, Kullanıcı, Ürün Özelliği, Müşteri Ürünleri, Banner)
            $table->unsignedBigInteger('relation_id'); // Türüne bağlı olarak ilişkili modülün OID'si
            $table->string('url'); // Dosyanın URL'si
            $table->string('file_name'); // Dosyanın adı
            $table->string('file_path'); // Dosyanın yolu
            $table->string('mime_type')->nullable(); // MIME tipi
            $table->unsignedBigInteger('file_size')->default(0); // Dosya boyutu (byte)
            $table->integer('order')->default(0); // Sıralama
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index(['type', 'relation_id']);
            $table->index('status');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
