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
        Schema::create('currencies', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('name'); // Döviz Adı
            $table->string('code', 3)->unique(); // Para birimi kodu (USD, EUR, TRY vb.)
            $table->string('symbol'); // Döviz Sembolü
            $table->string('position')->default('left'); // Sağda mı Solda mı?
            $table->decimal('exchange_rate', 10, 4)->default(1); // Döviz kuru
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
