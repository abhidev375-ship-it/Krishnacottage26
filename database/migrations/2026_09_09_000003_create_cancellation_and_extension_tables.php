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
        // 1. Add counter booking & refund fields to reservations
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'refunded_amount')) {
                $table->decimal('refunded_amount', 10, 2)->default(0.00)->after('paid_amount');
            }
            if (!Schema::hasColumn('reservations', 'is_counter_booking')) {
                $table->boolean('is_counter_booking')->default(false)->after('status');
            }
            if (!Schema::hasColumn('reservations', 'counter_booked_by')) {
                $table->foreignId('counter_booked_by')->nullable()->after('is_counter_booking')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('reservations', 'id_proof_type')) {
                $table->string('id_proof_type', 50)->nullable()->after('counter_booked_by');
            }
            if (!Schema::hasColumn('reservations', 'id_proof_number')) {
                $table->string('id_proof_number', 100)->nullable()->after('id_proof_type');
            }
        });

        // 2. Cancellation Rules table (fully customizable by admin)
        if (!Schema::hasTable('cancellation_rules')) {
            Schema::create('cancellation_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete(); // null = global resort policy
                $table->integer('hours_before_checkin')->default(0); // e.g. 72, 48, 24, 0
                $table->decimal('refund_percentage', 5, 2)->default(0.00); // e.g. 100.00, 75.00, 50.00, 0.00
                $table->string('description', 255);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['branch_id', 'is_active']);
                $table->index('hours_before_checkin');
            });
        }

        // 3. Stay Extension Requests table
        if (!Schema::hasTable('stay_extension_requests')) {
            Schema::create('stay_extension_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
                $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->date('current_checkout_date');
                $table->date('requested_checkout_date');
                $table->integer('extra_nights')->default(1);
                $table->enum('allocation_type', ['same_room', 'single_room', 'multi_room'])->default('same_room');
                $table->json('allocated_room_ids')->nullable(); // JSON array of physical room IDs
                $table->decimal('standard_amount', 10, 2)->default(0.00);
                $table->decimal('offered_amount', 10, 2)->nullable(); // manager discount rate
                $table->decimal('manager_discount_percentage', 5, 2)->nullable();
                $table->enum('status', ['pending', 'offered', 'approved', 'declined', 'completed'])->default('pending');
                $table->enum('payment_status', ['pending', 'paid'])->default('pending');
                $table->string('payment_method', 50)->nullable(); // cash, pos_card, upi, folio, online
                $table->decimal('paid_amount', 10, 2)->default(0.00);
                $table->text('guest_notes')->nullable();
                $table->text('manager_notes')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->index(['reservation_id', 'status']);
                $table->index('payment_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stay_extension_requests');
        Schema::dropIfExists('cancellation_rules');
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['counter_booked_by']);
            $table->dropColumn(['refunded_amount', 'is_counter_booking', 'counter_booked_by', 'id_proof_type', 'id_proof_number']);
        });
    }
};
