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
        // Spice Product Categories
        Schema::create('spice_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Spice Products (Catalogue)
        Schema::create('spice_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->string('sku', 50)->unique(); // e.g. KS-CRD-100
            $table->string('short_description', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('weight_grams')->default(100);
            $table->string('package_size', 50)->default('100g Jar');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->string('image_url')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock', 'discontinued'])->default('in_stock');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_published']);
            $table->index('is_featured');
        });

        // Spice Inventory Logs (Audit trail for stock adjustments)
        Schema::create('spice_inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_product_id')->constrained()->cascadeOnDelete();
            $table->enum('adjustment_type', [
                'stock_in',
                'stock_out',
                'reservation',
                'release',
                'correction',
                'damaged',
                'return'
            ]);
            $table->integer('quantity_change');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reason');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('spice_product_id');
        });

        // Spice Orders (E-commerce Order Management)
        Schema::create('spice_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique(); // e.g. SP-2026-0089
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 120);
            $table->string('customer_email', 120);
            $table->string('customer_phone', 30);

            // Shipping Address
            $table->string('shipping_address_line1', 255);
            $table->string('shipping_address_line2', 255)->nullable();
            $table->string('shipping_city', 100);
            $table->string('shipping_state', 100);
            $table->string('shipping_pincode', 20);
            $table->string('shipping_country', 100)->default('India');
            $table->string('shipping_courier', 100)->nullable(); // BlueDart, Delhivery, DTDC
            $table->string('tracking_number', 100)->nullable();

            $table->enum('status', [
                'pending_payment',
                'paid',
                'processing',
                'packed',
                'shipped',
                'out_for_delivery',
                'delivered',
                'cancelled',
                'return_requested',
                'returned',
                'refunded'
            ])->default('paid');

            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('paid');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_charge', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('customer_email');
        });

        // Spice Order Items
        Schema::create('spice_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spice_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spice_product_id')->constrained()->restrictOnDelete();
            $table->string('product_name', 150);
            $table->string('product_sku', 50);
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spice_order_items');
        Schema::dropIfExists('spice_orders');
        Schema::dropIfExists('spice_inventory_logs');
        Schema::dropIfExists('spice_products');
        Schema::dropIfExists('spice_categories');
    }
};
