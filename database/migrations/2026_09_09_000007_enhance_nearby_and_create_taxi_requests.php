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
        // 1. Enhance nearby_locations table
        Schema::table('nearby_locations', function (Blueprint $table) {
            $table->boolean('is_available')->default(true)->after('image_url');
            $table->boolean('is_taxi_available')->default(true)->after('is_available');
            $table->string('address', 255)->nullable()->after('travel_time');
            $table->index(['branch_id', 'is_available']);
        });

        // 2. Create taxi_requests table for in-house guest excursion bookings
        Schema::create('taxi_requests', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference', 30)->unique(); // e.g. TAX-10024
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->date('pickup_date');
            $table->string('pickup_time', 50)->nullable(); // e.g. "08:30 AM"
            $table->integer('passengers_count')->default(1);
            $table->json('selected_location_ids')->nullable(); // JSON array of nearby_location IDs
            $table->text('extra_locations_notes')->nullable(); // Guest custom stops / notes
            $table->decimal('estimated_fare', 10, 2)->nullable(); // Quoted by manager
            $table->string('driver_details', 255)->nullable(); // e.g. "Driver Ramesh (+91 94470 11223) - Innova"
            $table->text('manager_notes')->nullable();
            $table->enum('status', ['pending', 'contacted', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('folio_charge_id')->nullable()->constrained('reservation_folio_charges')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reservation_id', 'status']);
            $table->index(['branch_id', 'pickup_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxi_requests');

        Schema::table('nearby_locations', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'is_available']);
            $table->dropColumn(['is_available', 'is_taxi_available', 'address']);
        });
    }
};
