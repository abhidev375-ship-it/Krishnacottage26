<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance reviews table for public & verified submissions
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('reviews', 'guest_name')) {
                    $table->string('guest_name', 120)->nullable()->after('guest_id');
                }
                if (!Schema::hasColumn('reviews', 'guest_email')) {
                    $table->string('guest_email', 120)->nullable()->after('guest_name');
                }
                if (!Schema::hasColumn('reviews', 'guest_phone')) {
                    $table->string('guest_phone', 30)->nullable()->after('guest_email');
                }
                if (!Schema::hasColumn('reviews', 'photos')) {
                    $table->json('photos')->nullable()->after('comment');
                }
                if (!Schema::hasColumn('reviews', 'videos')) {
                    $table->json('videos')->nullable()->after('photos');
                }
                if (!Schema::hasColumn('reviews', 'is_pinned')) {
                    $table->boolean('is_pinned')->default(false)->after('is_featured');
                }
            });

            // Make reservation_id nullable so non-account guests can submit via /share-your-experience
            try {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->foreignId('reservation_id')->nullable()->change();
                    $table->foreignId('guest_id')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // If change() is not supported on direct driver without doctrine/dbal, continue safely
            }
        }

        // 2. Enhance testimonials table for pinning and media preview
        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                if (!Schema::hasColumn('testimonials', 'is_pinned')) {
                    $table->boolean('is_pinned')->default(true)->after('is_active');
                }
                if (!Schema::hasColumn('testimonials', 'photos')) {
                    $table->json('photos')->nullable()->after('quote');
                }
                if (!Schema::hasColumn('testimonials', 'videos')) {
                    $table->json('videos')->nullable()->after('photos');
                }
            });
        }

        // 3. Enhance gallery_images table for video album support
        if (Schema::hasTable('gallery_images')) {
            Schema::table('gallery_images', function (Blueprint $table) {
                if (!Schema::hasColumn('gallery_images', 'media_type')) {
                    $table->string('media_type', 20)->default('image')->after('image_url');
                }
                if (!Schema::hasColumn('gallery_images', 'video_url')) {
                    $table->string('video_url', 500)->nullable()->after('media_type');
                }
                if (!Schema::hasColumn('gallery_images', 'thumbnail_url')) {
                    $table->string('thumbnail_url', 500)->nullable()->after('video_url');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $columns = ['guest_name', 'guest_email', 'guest_phone', 'photos', 'videos', 'is_pinned'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('reviews', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('testimonials')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $columns = ['is_pinned', 'photos', 'videos'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('testimonials', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('gallery_images')) {
            Schema::table('gallery_images', function (Blueprint $table) {
                $columns = ['media_type', 'video_url', 'thumbnail_url'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('gallery_images', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
