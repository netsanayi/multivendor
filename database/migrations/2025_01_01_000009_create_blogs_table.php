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
        Schema::create('blogs', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('title'); // Blog Başlığı
            $table->string('slug')->unique(); // URL slug
            $table->text('description'); // Blog Açıklama (HTML)
            $table->string('meta_title')->nullable(); // Meta Başlık
            $table->text('meta_description')->nullable(); // Meta Açıklama
            $table->string('meta_keywords')->nullable(); // Meta Kelimeleri
            $table->foreignId('image_id')->nullable()->constrained('uploads')->onDelete('set null'); // Kapak resmi
            $table->foreignId('author_id')->constrained('users'); // Yazar
            $table->integer('view_count')->default(0); // Görüntülenme sayısı
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamp('published_at')->nullable(); // Yayınlanma tarihi
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('slug');
            $table->index('author_id');
            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
