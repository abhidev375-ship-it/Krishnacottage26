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
        // Room Types: Categories of rooms scoped to a branch
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 120);
            $table->string('code', 20)->nullable();
            $table->string('short_description', 255)->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('weekend_price', 10, 2)->nullable();
            $table->integer('max_guests')->default(2);
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(1);
            $table->string('bed_type', 100)->nullable(); // King, Twin, Queen
            $table->integer('size_sqft')->nullable();
            $table->json('amenities')->nullable(); // Array of strings: Wi-Fi, Balcony, Forest View, etc.
            $table->string('cover_image_url')->nullable();
            $table->json('gallery_images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_bookable')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'is_active']);
            $table->unique(['branch_id', 'slug']);
        });

        // Individual Physical Rooms
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('room_number', 30);
            $table->string('floor', 50)->nullable();
            $table->enum('operational_status', [
                'available',
                'occupied',
                'reserved',
                'maintenance',
                'blocked',
                'out_of_order'
            ])->default('available');
            $table->enum('housekeeping_status', [
                'clean',
                'dirty',
                'inspecting'
            ])->default('clean');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['branch_id', 'room_number']);
            $table->index(['branch_id', 'operational_status']);
            $table->index(['branch_id', 'housekeeping_status']);
        });

        // Room Blocks (temporary blocks, maintenance, owner hold)
        Schema::create('room_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason');
            $table->enum('block_type', ['maintenance', 'renovation', 'vip_hold', 'seasonal_closure'])->default('maintenance');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['room_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_blocks');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_types');
    }
};
