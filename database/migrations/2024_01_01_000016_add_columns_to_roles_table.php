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
        // Add is_active column to roles table if it doesn't exist
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'is_active')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('guard_name');
                $table->text('description')->nullable()->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn(['is_active', 'description']);
            });
        }
    }
};
