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
        // Check if vendor_products table exists
        if (Schema::hasTable('vendor_products')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                // Add vendor_id column if it doesn't exist
                if (!Schema::hasColumn('vendor_products', 'vendor_id')) {
                    // If user_relation_id exists, rename it to vendor_id
                    if (Schema::hasColumn('vendor_products', 'user_relation_id')) {
                        $table->renameColumn('user_relation_id', 'vendor_id');
                    } else {
                        $table->unsignedBigInteger('vendor_id')->after('id');
                    }
                }
                
                // Add product_id column if it doesn't exist
                if (!Schema::hasColumn('vendor_products', 'product_id')) {
                    // If relation_id exists, rename it to product_id
                    if (Schema::hasColumn('vendor_products', 'relation_id')) {
                        $table->renameColumn('relation_id', 'product_id');
                    } else {
                        $table->unsignedBigInteger('product_id')->after('vendor_id');
                    }
                }
                
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('vendor_products', 'quantity')) {
                    if (Schema::hasColumn('vendor_products', 'stock_quantity')) {
                        $table->renameColumn('stock_quantity', 'quantity');
                    } else {
                        $table->integer('quantity')->default(0);
                    }
                }
                
                if (!Schema::hasColumn('vendor_products', 'is_active')) {
                    if (Schema::hasColumn('vendor_products', 'status')) {
                        $table->renameColumn('status', 'is_active');
                    } else {
                        $table->boolean('is_active')->default(true);
                    }
                }
                
                // Add indexes if they don't exist
                if (!Schema::hasIndex('vendor_products', 'vendor_products_vendor_id_index')) {
                    $table->index('vendor_id');
                }
                if (!Schema::hasIndex('vendor_products', 'vendor_products_product_id_index')) {
                    $table->index('product_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vendor_products')) {
            Schema::table('vendor_products', function (Blueprint $table) {
                // Revert column names
                if (Schema::hasColumn('vendor_products', 'vendor_id')) {
                    $table->renameColumn('vendor_id', 'user_relation_id');
                }
                if (Schema::hasColumn('vendor_products', 'product_id')) {
                    $table->renameColumn('product_id', 'relation_id');
                }
                if (Schema::hasColumn('vendor_products', 'quantity')) {
                    $table->renameColumn('quantity', 'stock_quantity');
                }
                if (Schema::hasColumn('vendor_products', 'is_active')) {
                    $table->renameColumn('is_active', 'status');
                }
                
                // Drop indexes
                $table->dropIndex(['vendor_id']);
                $table->dropIndex(['product_id']);
            });
        }
    }
};
