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
        Schema::create('vendor_payouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('payout_number')->unique(); // Ödeme numarası
            $table->decimal('amount', 15, 2); // Ödeme tutarı
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'paypal', 'stripe', 'manual']);
            $table->json('bank_details')->nullable(); // Banka bilgileri
            $table->string('transaction_id')->nullable(); // İşlem numarası
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('earnings_ids')->nullable(); // İlişkili kazanç ID'leri
            $table->timestamps();
            
            // İndeksler
            $table->index('vendor_id');
            $table->index('status');
            $table->index('payout_number');
            $table->index(['vendor_id', 'status']);
            $table->index('requested_at');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payouts');
    }
};
