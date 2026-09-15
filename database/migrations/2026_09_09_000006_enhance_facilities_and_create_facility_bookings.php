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
        // 1. Enhance facilities table: allow nullable branch_id for resort-wide facilities,
        // flexible category, bookable toggle, rate, and scheduling toggle.
        Schema::table('facilities', function (Blueprint $table) {
            // Drop foreign key temporarily to allow modifying branch_id column to nullable
            try {
                $table->dropForeign(['branch_id']);
            } catch (\Throwable $e) {
                // Ignore if not found or already dropped
            }
        });

        DB::statement("ALTER TABLE facilities MODIFY branch_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE facilities MODIFY category VARCHAR(100) NULL DEFAULT NULL");

        // Re-add foreign key with set null on delete
        Schema::table('facilities', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();

            if (!Schema::hasColumn('facilities', 'is_bookable')) {
                $table->boolean('is_bookable')->default(false)->after('category');
            }
            if (!Schema::hasColumn('facilities', 'rate')) {
                $table->decimal('rate', 10, 2)->default(0.00)->after('is_bookable');
            }
            if (!Schema::hasColumn('facilities', 'has_scheduling')) {
                $table->boolean('has_scheduling')->default(false)->after('rate');
            }
        });

        // 2. Create facility_bookings table for tracking in-house guest bookings and manager time allocations
        if (!Schema::hasTable('facility_bookings')) {
            Schema::create('facility_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
                $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
                $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->date('booking_date');
                $table->integer('guests_count')->default(1);
                $table->decimal('rate', 10, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->string('allocated_time_slot', 120)->nullable(); // Set by manager, e.g. "09:00 AM - 10:30 AM"
                $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
                $table->text('notes')->nullable();
                $table->foreignId('folio_charge_id')->nullable()->constrained('reservation_folio_charges')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reservation_id', 'status']);
                $table->index(['booking_date', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_bookings');

        Schema::table('facilities', function (Blueprint $table) {
            if (Schema::hasColumn('facilities', 'has_scheduling')) {
                $table->dropColumn('has_scheduling');
            }
            if (Schema::hasColumn('facilities', 'rate')) {
                $table->dropColumn('rate');
            }
            if (Schema::hasColumn('facilities', 'is_bookable')) {
                $table->dropColumn('is_bookable');
            }
        });
    }
};
