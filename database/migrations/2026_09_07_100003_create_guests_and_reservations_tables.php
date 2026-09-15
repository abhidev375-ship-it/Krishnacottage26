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
        // Guests CRM: unified customer profile across stays, dining, and spices
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('email', 120)->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->string('secondary_phone', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('India');
            $table->string('pincode', 20)->nullable();
            $table->enum('id_proof_type', ['passport', 'national_id', 'driving_license', 'other'])->nullable();
            $table->string('id_proof_number', 100)->nullable();
            $table->enum('vip_level', ['standard', 'silver', 'gold', 'platinum'])->default('standard');
            $table->text('preferences')->nullable();
            $table->text('internal_notes')->nullable();
            $table->integer('total_stays')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
        });

        // Reservations: Accommodation booking management
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 30)->unique(); // e.g. KR-2026-00123
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete(); // Physical room assignment
            $table->foreignId('guest_id')->constrained()->restrictOnDelete();

            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('rooms_count')->default(1);

            $table->decimal('nightly_rate', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);

            $table->enum('status', [
                'inquiry',
                'hold',
                'pending_payment',
                'confirmed',
                'checked_in',
                'checked_out',
                'cancelled',
                'no_show'
            ])->default('confirmed');

            $table->enum('payment_status', [
                'pending',
                'partial',
                'paid',
                'refunded',
                'failed'
            ])->default('pending');

            $table->string('payment_method', 50)->nullable(); // cash, credit_card, upi, bank_transfer, online
            $table->text('special_requests')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('hold_expires_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index(['check_in_date', 'check_out_date']);
            $table->index('payment_status');
        });

        // Reservation Status Transition Audit
        Schema::create('reservation_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Payments: Unified financial ledger for stays, dining, and spices
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable'); // reservation, food_order, spice_order
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_id', 100)->nullable()->index();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50); // upi, card, netbanking, cash, pos
            $table->string('gateway', 50)->nullable(); // razorpay, stripe, manual
            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('successful');
            $table->json('gateway_response')->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('reservation_status_logs');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('guests');
    }
};
