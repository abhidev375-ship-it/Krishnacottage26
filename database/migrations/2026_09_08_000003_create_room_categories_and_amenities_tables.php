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
        // 1. Room Categories (Luxury Villas, Heritage Cottages, Canopy Treehouses, etc.)
        Schema::create('room_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('icon', 50)->default('palmtree'); // Lucide icon name
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Amenities Library (Views, Wellness, Comfort, Tech with Lucide icons)
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('icon', 50)->default('sparkles'); // Lucide icon name
            $table->string('category', 60)->default('General'); // Views & Outdoors, Wellness & Bath, Room Comfort, Tech & Connectivity
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false); // Show on client explorer filter pills
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Pivot table linking Room Types and Amenities
        Schema::create('amenity_room_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('amenities')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['room_type_id', 'amenity_id']);
        });

        // 4. Add room_category_id to room_types
        Schema::table('room_types', function (Blueprint $table) {
            $table->foreignId('room_category_id')->nullable()->after('branch_id')->constrained('room_categories')->nullOnDelete();
        });

        // 5. Add target_room to enquiry_messages for multi-room targeting
        Schema::table('enquiry_messages', function (Blueprint $table) {
            $table->string('target_room', 50)->nullable()->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enquiry_messages', function (Blueprint $table) {
            $table->dropColumn('target_room');
        });

        Schema::table('room_types', function (Blueprint $table) {
            $table->dropForeign(['room_category_id']);
            $table->dropColumn('room_category_id');
        });

        Schema::dropIfExists('amenity_room_type');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('room_categories');
    }
};
