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
        // Notification Settings (Kullanıcı bildirim tercihleri)
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Email Bildirimleri
            $table->boolean('email_enabled')->default(true);
            $table->boolean('email_order_updates')->default(true);
            $table->boolean('email_product_updates')->default(true);
            $table->boolean('email_messages')->default(true);
            $table->boolean('email_tickets')->default(true);
            $table->boolean('email_promotions')->default(true);
            $table->boolean('email_wishlist_updates')->default(true);
            $table->boolean('email_price_alerts')->default(true);
            
            // SMS Bildirimleri
            $table->boolean('sms_enabled')->default(false);
            $table->string('sms_phone')->nullable();
            $table->boolean('sms_verified')->default(false);
            $table->boolean('sms_order_updates')->default(true);
            $table->boolean('sms_important_only')->default(true);
            
            // Push Bildirimleri
            $table->boolean('push_enabled')->default(true);
            $table->boolean('push_order_updates')->default(true);
            $table->boolean('push_messages')->default(true);
            $table->boolean('push_promotions')->default(false);
            
            // Bildirim Zamanlaması
            $table->boolean('quiet_hours_enabled')->default(false);
            $table->time('quiet_hours_start')->default('22:00');
            $table->time('quiet_hours_end')->default('08:00');
            $table->string('timezone')->default('Europe/Istanbul');
            
            // Özet Tercihler
            $table->enum('digest_frequency', ['never', 'daily', 'weekly', 'monthly'])->default('never');
            $table->time('digest_time')->default('09:00');
            $table->enum('digest_day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            
            $table->timestamps();
            
            $table->unique('user_id');
            $table->index('email_enabled');
            $table->index('sms_enabled');
            $table->index('push_enabled');
        });

        // Notification Templates
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('channel', ['email', 'sms', 'push', 'database']);
            $table->string('subject')->nullable(); // Email için
            $table->text('content'); // Template içeriği
            $table->json('variables')->nullable(); // Kullanılabilir değişkenler
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('channel');
            $table->index('is_active');
        });

        // Notification Queue (Gönderilecek bildirimler)
        Schema::create('notification_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // Notification tipi
            $table->enum('channel', ['email', 'sms', 'push', 'database']);
            $table->string('subject')->nullable();
            $table->text('content');
            $table->json('data')->nullable(); // Ek veri
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'processing', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_at')->nullable(); // Zamanlanmış gönderim
            $table->timestamp('sent_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('channel');
            $table->index('status');
            $table->index('priority');
            $table->index('scheduled_at');
            $table->index(['status', 'scheduled_at']);
        });

        // Notification History (Gönderilmiş bildirimler)
        Schema::create('notification_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->enum('channel', ['email', 'sms', 'push', 'database']);
            $table->string('subject')->nullable();
            $table->text('content');
            $table->json('data')->nullable();
            $table->timestamp('sent_at');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->boolean('is_success')->default(true);
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Tracking bilgileri
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('channel');
            $table->index('sent_at');
            $table->index('read_at');
            $table->index(['user_id', 'read_at']);
        });

        // Push Tokens (Push bildirimleri için)
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('token')->unique();
            $table->enum('platform', ['web', 'ios', 'android']);
            $table->string('device_id')->nullable();
            $table->string('device_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('token');
            $table->index('platform');
            $table->index('is_active');
            $table->index(['user_id', 'is_active']);
        });

        // SMS Credits (SMS kredileri)
        Schema::create('sms_credits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('credits')->default(0);
            $table->integer('used_credits')->default(0);
            $table->decimal('cost_per_sms', 8, 4)->default(0.10);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_credits');
        Schema::dropIfExists('push_tokens');
        Schema::dropIfExists('notification_history');
        Schema::dropIfExists('notification_queue');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_settings');
    }
};
