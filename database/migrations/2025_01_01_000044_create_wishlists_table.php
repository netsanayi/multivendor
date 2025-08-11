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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('priority')->default(0); // Öncelik sıralaması
            $table->text('notes')->nullable(); // Kullanıcı notları
            $table->timestamp('added_at')->useCurrent();
            $table->timestamp('notified_at')->nullable(); // Fiyat düşüşü bildirimi
            $table->decimal('price_when_added', 10, 2)->nullable(); // Eklendiğindeki fiyat
            $table->boolean('notify_on_sale')->default(true); // İndirim bildirimi
            $table->timestamps();
            
            // Unique constraint - Bir kullanıcı aynı ürünü birden fazla kez ekleyemez
            $table->unique(['user_id', 'product_id']);
            
            // İndeksler
            $table->index('user_id');
            $table->index('product_id');
            $table->index('added_at');
            $table->index(['user_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
