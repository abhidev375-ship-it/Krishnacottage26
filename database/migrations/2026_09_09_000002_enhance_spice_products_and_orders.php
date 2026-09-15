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
        // 1. Enhance spice_products
        Schema::table('spice_products', function (Blueprint $table) {
            if (!Schema::hasColumn('spice_products', 'selling_mode')) {
                $table->enum('selling_mode', ['packet', 'loose', 'both'])->default('both')->after('package_size');
            }
            if (!Schema::hasColumn('spice_products', 'price_per_kg')) {
                $table->decimal('price_per_kg', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('spice_products', 'min_loose_weight_kg')) {
                $table->decimal('min_loose_weight_kg', 6, 3)->default(0.100)->after('price_per_kg');
            }
            if (!Schema::hasColumn('spice_products', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(5.00)->after('min_loose_weight_kg');
            }
            if (!Schema::hasColumn('spice_products', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('is_published');
            }
        });

        // 2. Enhance spice_orders
        Schema::table('spice_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('spice_orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('guest_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('spice_orders', 'delivery_mode')) {
                $table->enum('delivery_mode', ['courier', 'villa'])->default('courier')->after('customer_phone');
            }
            if (!Schema::hasColumn('spice_orders', 'room_number')) {
                $table->string('room_number', 50)->nullable()->after('delivery_mode');
            }
            if (!Schema::hasColumn('spice_orders', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(5.00)->after('tax_amount');
            }
        });

        // 3. Enhance spice_order_items
        Schema::table('spice_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('spice_order_items', 'pricing_type')) {
                $table->enum('pricing_type', ['packet', 'loose'])->default('packet')->after('product_sku');
            }
            if (!Schema::hasColumn('spice_order_items', 'weight_kg')) {
                $table->decimal('weight_kg', 8, 3)->nullable()->after('pricing_type');
            }
            if (!Schema::hasColumn('spice_order_items', 'weight_display')) {
                $table->string('weight_display', 100)->nullable()->after('weight_kg');
            }
            if (!Schema::hasColumn('spice_order_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(5.00)->after('total_price');
            }
            if (!Schema::hasColumn('spice_order_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0.00)->after('tax_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spice_order_items', function (Blueprint $table) {
            $table->dropColumn(['pricing_type', 'weight_kg', 'weight_display', 'tax_rate', 'tax_amount']);
        });

        Schema::table('spice_orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'delivery_mode', 'room_number', 'tax_rate']);
        });

        Schema::table('spice_products', function (Blueprint $table) {
            $table->dropColumn(['selling_mode', 'price_per_kg', 'min_loose_weight_kg', 'tax_rate', 'is_available']);
        });
    }
};
