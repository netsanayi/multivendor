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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id'); // OID
            $table->string('first_name'); // Adı
            $table->string('last_name'); // Soyadı
            $table->string('email')->unique(); // Eposta Adresi
            $table->timestamp('email_verified_at')->nullable(); // E-posta doğrulama zamanı
            $table->string('phone_number')->nullable(); // Telefon Numarası
            $table->string('password'); // Şifre
            $table->foreignId('default_currency_id')->nullable()->constrained('currencies')->onDelete('set null'); // Varsayılan Döviz Cinsi
            $table->boolean('status')->default(true); // Durumu (Aktif-Pasif)
            $table->text('two_factor_secret')->nullable(); // 2FA gizli anahtarı
            $table->text('two_factor_recovery_codes')->nullable(); // 2FA kurtarma kodları
            $table->timestamp('two_factor_confirmed_at')->nullable(); // 2FA onay zamanı
            $table->foreignId('current_team_id')->nullable(); // Jetstream team support
            $table->rememberToken(); // Beni hatırla token'ı
            $table->string('profile_photo_path', 2048)->nullable(); // Profil fotoğrafı yolu
            $table->timestamps(); // created_at ve updated_at
            
            // İndeksler
            $table->index('email');
            $table->index('phone_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
