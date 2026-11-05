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
        // Facilities & Experiences (Scoped to branch, genuine amenities, no pool)
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->enum('category', [
                'wellness',
                'nature',
                'activities',
                'family',
                'events',
                'dining',
                'experiences',
                'services'
            ])->default('experiences');
            $table->string('short_description', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('operating_hours', 100)->nullable(); // e.g. 06:00 AM - 08:00 PM
            $table->string('image_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->boolean('show_in_navigation')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'category']);
            $table->unique(['branch_id', 'slug']);
        });

        // Gallery Albums
        Schema::create('gallery_albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete(); // null = resort-wide
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->enum('category', [
                'resort',
                'rooms',
                'nature',
                'dining',
                'facilities',
                'experiences',
                'events',
                'spices'
            ])->default('resort');
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('images_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'category']);
        });

        // Gallery Images (inside-album multi-image manager)
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_album_id')->constrained()->cascadeOnDelete();
            $table->string('image_url', 500);
            $table->string('title', 150)->nullable();
            $table->string('alt_text', 200)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('gallery_album_id');
        });

        // Nearby Locations (Branch-specific attractions & points of interest)
        Schema::create('nearby_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->enum('category', [
                'attraction',
                'landmark',
                'nature',
                'temple',
                'beach',
                'restaurant',
                'shopping',
                'transport',
                'airport',
                'other'
            ])->default('attraction');
            $table->text('description')->nullable();
            $table->decimal('distance_km', 5, 2);
            $table->string('travel_time', 50)->nullable(); // e.g. '15 mins drive'
            $table->string('image_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nearby_locations');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('gallery_albums');
        Schema::dropIfExists('facilities');
    }
};
