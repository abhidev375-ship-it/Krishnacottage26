<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add is_active column to spice_products table
        Schema::table('spice_products', function (Blueprint $table) {
            if (!Schema::hasColumn('spice_products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_available');
                $table->index('is_active');
            }
        });

        // Ensure existing active products have is_active = true
        DB::table('spice_products')
            ->where('is_available', true)
            ->where('is_published', true)
            ->update(['is_active' => true]);

        // 2. Create spice_return_rules table (Customizable return and cancellation policy engine)
        if (!Schema::hasTable('spice_return_rules')) {
            Schema::create('spice_return_rules', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150); // e.g. Pre-Dispatch Full Cancellation
                $table->enum('applies_to', ['before_dispatch', 'after_delivery', 'general'])->default('before_dispatch');
                $table->integer('time_limit_hours')->default(24); // e.g. 24h before pack/ship, 168h (7 days) post-delivery
                $table->decimal('refund_percentage', 5, 2)->default(100.00); // 100.00, 90.00, 0.00
                $table->decimal('handling_fee', 10, 2)->default(0.00); // Flat deduction e.g. 0.00 or 50.00
                $table->text('description');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['is_active', 'applies_to']);
                $table->index('sort_order');
            });

            // Seed standard initial return rules
            DB::table('spice_return_rules')->insert([
                [
                    'name' => 'Pre-Dispatch Instant Cancellation',
                    'applies_to' => 'before_dispatch',
                    'time_limit_hours' => 24,
                    'refund_percentage' => 100.00,
                    'handling_fee' => 0.00,
                    'description' => '100% instant full refund for orders cancelled prior to plantation milling and dispatch packaging.',
                    'is_active' => true,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => '7-Day Fresh Harvest Return Guarantee',
                    'applies_to' => 'after_delivery',
                    'time_limit_hours' => 168,
                    'refund_percentage' => 90.00,
                    'handling_fee' => 50.00,
                    'description' => 'Return intact vacuum-sealed spice packages within 7 days of courier arrival. 90% refund minus ₹50 sanitization & handling fee.',
                    'is_active' => true,
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Damaged Seal & Quality Assurance',
                    'applies_to' => 'general',
                    'time_limit_hours' => 72,
                    'refund_percentage' => 100.00,
                    'handling_fee' => 0.00,
                    'description' => '100% instant replacement or full refund if parcel seal is broken or aroma quality does not meet Krishna Estate standards within 72 hours.',
                    'is_active' => true,
                    'sort_order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 3. Add return & refund tracking columns to spice_orders
        Schema::table('spice_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('spice_orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('spice_orders', 'spice_return_rule_id')) {
                $table->foreignId('spice_return_rule_id')->nullable()->after('cancellation_reason')->constrained('spice_return_rules')->nullOnDelete();
            }
            if (!Schema::hasColumn('spice_orders', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->default(0.00)->after('spice_return_rule_id');
            }
            if (!Schema::hasColumn('spice_orders', 'refund_status')) {
                $table->enum('refund_status', ['none', 'requested', 'approved', 'refunded', 'rejected'])->default('none')->after('refund_amount');
                $table->index('refund_status');
            }
            if (!Schema::hasColumn('spice_orders', 'return_notes')) {
                $table->text('return_notes')->nullable()->after('refund_status');
            }
            if (!Schema::hasColumn('spice_orders', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('return_notes');
            }
        });

        // 4. Seed default return policy description in settings table if not present
        if (Schema::hasTable('settings')) {
            $existing = DB::table('settings')->where('key', 'spice_return_policy_description')->first();
            if (!$existing) {
                DB::table('settings')->insert([
                    'key' => 'spice_return_policy_description',
                    'value' => 'All Krishna Spices are harvested and vacuum-ground on-demand after receiving your order to guarantee plantation freshness. You can cancel your order with a 100% full refund at any time before dispatch. Unopened, factory-sealed spice packs can be returned within 7 days of delivery under our 90% freshness guarantee.',
                    'group' => 'spice_shop',
                    'description' => 'Public estate spices return and cancellation policy displayed in client store and customer dashboard.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spice_orders', function (Blueprint $table) {
            $table->dropForeign(['spice_return_rule_id']);
            $table->dropColumn([
                'cancellation_reason',
                'spice_return_rule_id',
                'refund_amount',
                'refund_status',
                'return_notes',
                'returned_at',
            ]);
        });

        Schema::dropIfExists('spice_return_rules');

        Schema::table('spice_products', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
