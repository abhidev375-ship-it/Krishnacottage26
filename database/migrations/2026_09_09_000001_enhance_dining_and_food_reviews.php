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
        // 1. Make branch_id nullable on menu_categories for global categories
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->change();
        });

        // 2. Enhance menu_items with multi-branch allocation, daily availability, and rating aggregates
        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->change();
            $table->boolean('is_all_branches')->default(false)->after('branch_id');
            $table->json('allocated_branch_ids')->nullable()->after('is_all_branches');
            $table->json('available_days')->nullable()->after('availability_state'); // ['mon','tue','wed','thu','fri','sat','sun']
            $table->boolean('is_available_today')->default(true)->after('available_days');
            $table->decimal('average_rating', 3, 2)->default(5.00)->after('sort_order');
            $table->integer('reviews_count')->default(0)->after('average_rating');

            $table->index('is_available_today');
            $table->index('is_all_branches');
        });

        // 3. Create food_reviews table for verified dining orders
        Schema::create('food_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 120);
            $table->tinyInteger('rating')->unsigned(); // 1 to 5
            $table->text('comment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index(['menu_item_id', 'status']);
            $table->index(['food_order_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_reviews');

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['is_available_today']);
            $table->dropIndex(['is_all_branches']);
            $table->dropColumn([
                'is_all_branches',
                'allocated_branch_ids',
                'available_days',
                'is_available_today',
                'average_rating',
                'reviews_count',
            ]);
        });
    }
};
