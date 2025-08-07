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
        Schema::create('vendor_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('commission_rate', 5, 2)->default(10.00); // Komisyon oranı (%)
            $table->decimal('min_commission', 10, 2)->default(0); // Minimum komisyon tutarı
            $table->decimal('max_commission', 10, 2)->nullable(); // Maksimum komisyon tutarı
            $table->enum('commission_type', ['percentage', 'fixed', 'tiered'])->default('percentage');
            $table->json('tiered_rates')->nullable(); // Kademeli komisyon oranları
            $table->boolean('is_active')->default(true);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // İndeksler
            $table->index('vendor_id');
            $table->index('is_active');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_commissions');
    }
};
