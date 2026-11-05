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
        // Menu Categories: Scoped to Branch
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 120);
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'is_active']);
            $table->unique(['branch_id', 'slug']);
        });

        // Menu Items
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 170);
            $table->string('short_description', 255)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(5.00); // 5% GST on dining standard

            $table->enum('availability_state', [
                'in_stock',
                'limited_quantity',
                'out_of_stock',
                'hidden'
            ])->default('in_stock');

            $table->integer('stock_quantity')->nullable(); // For limited_quantity tracking
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_vegetarian')->default(true);
            $table->json('dietary_tags')->nullable(); // Vegan, Gluten-Free, Chef Special, Signature
            $table->integer('prep_time_minutes')->default(20);
            $table->string('image_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'availability_state']);
            $table->index(['menu_category_id', 'is_featured']);
        });

        // Modifier Groups: e.g. Spice Level, Preparation Style, Add-ons
        Schema::create('modifier_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete(); // null = all branches
            $table->string('name', 100);
            $table->enum('selection_type', ['required', 'optional', 'force_show'])->default('optional');
            $table->integer('min_selections')->default(0);
            $table->integer('max_selections')->default(1);
            $table->timestamps();
        });

        // Modifier Options: e.g. Mild (+0), Medium (+0), Extra Spicy (+20)
        Schema::create('modifier_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modifier_group_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('price_adjustment', 10, 2)->default(0.00);
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Pivot: Menu Item <-> Modifier Group
        Schema::create('menu_item_modifier_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('modifier_group_id')->constrained()->cascadeOnDelete();
            $table->unique(['menu_item_id', 'modifier_group_id']);
        });

        // Food Orders: Live dining tickets and guest food orders
        Schema::create('food_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique(); // e.g. FO-2026-0045
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete(); // for room service
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30)->nullable();
            $table->enum('order_type', ['dine_in', 'room_service', 'takeaway'])->default('dine_in');
            $table->string('table_number', 30)->nullable();

            $table->enum('status', [
                'new',
                'accepted',
                'preparing',
                'ready',
                'completed',
                'cancelled',
                'refund_pending',
                'refunded'
            ])->default('new');

            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index('ordered_at');
        });

        // Food Order Items
        Schema::create('food_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->restrictOnDelete();
            $table->string('item_name', 150);
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 10, 2);
            $table->json('selected_modifiers')->nullable();
            $table->string('special_instructions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_order_items');
        Schema::dropIfExists('food_orders');
        Schema::dropIfExists('menu_item_modifier_group');
        Schema::dropIfExists('modifier_options');
        Schema::dropIfExists('modifier_groups');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_categories');
    }
};
