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
        // Check if roles table exists
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                // Add is_active column if it doesn't exist
                if (!Schema::hasColumn('roles', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('guard_name');
                }
                
                // Add description column if it doesn't exist
                if (!Schema::hasColumn('roles', 'description')) {
                    $table->text('description')->nullable()->after('is_active');
                }
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
                // Drop columns if they exist
                if (Schema::hasColumn('roles', 'is_active')) {
                    $table->dropColumn('is_active');
                }
                if (Schema::hasColumn('roles', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }
    }
};
