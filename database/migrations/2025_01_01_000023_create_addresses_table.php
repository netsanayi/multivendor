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
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User ile bağlantı
            $table->string('address_name'); // Adres Adı (Ev, İş vb.)
            $table->string('city'); // İl
            $table->string('district'); // İlçe
            $table->string('street')->nullable(); // Sokak
            $table->string('road_name')->nullable(); // Yol Adı
            $table->string('door_no')->nullable(); // Kapı NO
            $table->string('building_no')->nullable(); // Bina NO
            $table->string('floor')->nullable(); // Kat
            $table->string('company_type')->default('individual'); // Şirket Türü (individual/corporate)
            $table->string('company_name')->nullable(); // Şirket Adı
            $table->string('tax_office')->nullable(); // Vergi Dairesi
            $table->string('tax_no')->nullable(); // Vergi NO
            $table->string('tc_id_no')->nullable(); // TC Kimlik NO
            $table->text('full_address')->nullable(); // Tam adres metni
            $table->boolean('is_default')->default(false); // Varsayılan adres mi?
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
