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
        // Message Threads (Konuşmalar)
        Schema::create('message_threads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('order_id')->nullable(); // Sipariş tablosu ileride eklenecek
            $table->enum('type', ['product_inquiry', 'order_inquiry', 'general', 'offer'])->default('general');
            $table->enum('status', ['active', 'archived', 'blocked'])->default('active');
            $table->timestamp('last_message_at')->nullable();
            $table->integer('message_count')->default(0);
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->timestamps();
            
            $table->index('product_id');
            $table->index('type');
            $table->index('status');
            $table->index('last_message_at');
        });

        // Message Thread Participants
        Schema::create('message_thread_participants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('thread_id')->constrained('message_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['customer', 'vendor', 'admin'])->default('customer');
            $table->timestamp('last_read_at')->nullable();
            $table->integer('unread_count')->default(0);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_muted')->default(false);
            $table->boolean('has_left')->default(false);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            
            $table->unique(['thread_id', 'user_id']);
            $table->index('thread_id');
            $table->index('user_id');
            $table->index(['thread_id', 'user_id']);
            $table->index('unread_count');
        });

        // Messages
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('thread_id')->constrained('message_threads')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->enum('type', ['text', 'image', 'file', 'offer', 'system'])->default('text');
            $table->json('attachments')->nullable(); // Ek dosyalar
            $table->decimal('offer_amount', 10, 2)->nullable(); // Teklif tutarı
            $table->enum('offer_status', ['pending', 'accepted', 'rejected', 'expired'])->nullable();
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->json('read_by')->nullable(); // Kimler okudu
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->timestamps();
            
            $table->index('thread_id');
            $table->index('sender_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['thread_id', 'created_at']);
        });

        // Message Attachments
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('message_id')->constrained('messages')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size'); // bytes
            $table->string('thumbnail_path')->nullable(); // Resimler için thumbnail
            $table->timestamps();
            
            $table->index('message_id');
        });

        // Quick Replies (Hızlı cevaplar)
        Schema::create('quick_replies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('shortcut')->nullable(); // Kısayol (örn: /merhaba)
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('shortcut');
            $table->index('is_active');
        });

        // Blocked Users (Engellenmiş kullanıcılar)
        Schema::create('blocked_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('blocked_user_id')->constrained('users')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->timestamp('blocked_at')->useCurrent();
            $table->timestamp('unblocked_at')->nullable();
            
            $table->unique(['user_id', 'blocked_user_id']);
            $table->index('user_id');
            $table->index('blocked_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_users');
        Schema::dropIfExists('quick_replies');
        Schema::dropIfExists('message_attachments');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('message_thread_participants');
        Schema::dropIfExists('message_threads');
    }
};
