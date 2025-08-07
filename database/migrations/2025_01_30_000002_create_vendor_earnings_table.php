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
        Schema::create('vendor_earnings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable(); // Siparişler tablosu ileride eklenecek
            $table->foreignId('order_item_id')->nullable(); // Sipariş kalemleri tablosu ileride eklenecek
            $table->decimal('gross_amount', 15, 2); // Brüt tutar
            $table->decimal('commission_amount', 15, 2); // Komisyon tutarı
            $table->decimal('net_amount', 15, 2); // Net tutar (vendor'a ödenecek)
            $table->decimal('tax_amount', 15, 2)->default(0); // Vergi tutarı
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'paypal', 'stripe', 'manual'])->nullable();
            $table->string('transaction_id')->nullable(); // Ödeme işlem numarası
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->timestamps();
            
            // İndeksler
            $table->index('vendor_id');
            $table->index('status');
            $table->index('paid_at');
            $table->index(['vendor_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_earnings');
    }
};
