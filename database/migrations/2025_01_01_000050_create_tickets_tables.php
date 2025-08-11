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
        // Ticket Categories
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#000000'); // Hex color
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
            $table->index('order');
        });

        // Tickets
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_number')->unique();
            $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['open', 'pending', 'answered', 'on_hold', 'closed', 'resolved'])->default('open');
            $table->enum('user_type', ['customer', 'vendor']); // Ticket açan kişi türü
            $table->foreignId('related_order_id')->nullable(); // İlgili sipariş
            $table->foreignId('related_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('response_count')->default(0);
            $table->decimal('satisfaction_rating', 2, 1)->nullable(); // 1-5 arası puan
            $table->text('satisfaction_comment')->nullable();
            $table->json('tags')->nullable(); // Etiketler
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->timestamps();
            
            // İndeksler
            $table->index('ticket_number');
            $table->index('user_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('priority');
            $table->index('user_type');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
        });

        // Ticket Responses
        Schema::create('ticket_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_internal')->default(false); // Sadece yöneticilerin görebileceği notlar
            $table->boolean('is_solution')->default(false); // Bu cevap çözüm mü?
            $table->json('attachments')->nullable(); // Ek dosyalar
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('ticket_id');
            $table->index('user_id');
            $table->index('is_internal');
            $table->index('created_at');
        });

        // Ticket Attachments
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('cascade');
            $table->foreignId('response_id')->nullable()->constrained('ticket_responses')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size'); // bytes
            $table->timestamps();
            
            $table->index('ticket_id');
            $table->index('response_id');
        });

        // Ticket Templates (Hazır cevaplar)
        Schema::create('ticket_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained('ticket_categories')->onDelete('set null');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->json('variables')->nullable(); // Kullanılabilir değişkenler
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('category_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_templates');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_responses');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_categories');
    }
};
