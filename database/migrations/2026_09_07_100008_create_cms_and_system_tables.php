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
        // Custom Pages (CMS)
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 150);
            $table->string('slug', 170)->unique();
            $table->string('short_description', 255)->nullable();
            $table->string('hero_image_url')->nullable();
            $table->json('content_blocks')->nullable(); // Predefined block components
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Navigation Structure
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->nullOnDelete();
            $table->string('label', 100);
            $table->string('url', 255);
            $table->string('target', 20)->default('_self');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Notification Logs: Outbound Email & SMS tracking
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event', 100); // e.g. booking_confirmed, payment_success
            $table->enum('channel', ['email', 'sms', 'whatsapp'])->default('email');
            $table->string('recipient', 120);
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_type', 100)->nullable(); // App\Models\Reservation, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('subject', 200)->nullable();
            $table->text('message_body');
            $table->enum('status', ['pending', 'delivered', 'failed'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['channel', 'status']);
        });

        // Notification Templates
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 80)->unique(); // e.g. BOOKING_CONFIRMATION
            $table->enum('channel', ['email', 'sms', 'whatsapp'])->default('email');
            $table->string('subject', 200)->nullable();
            $table->text('body');
            $table->json('variables')->nullable(); // e.g. ["guest_name", "booking_id", "check_in"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Audit Logs: Immutable system activity log
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name', 120)->nullable();
            $table->string('role', 50)->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50); // e.g. create, update, cancel, refund, block
            $table->string('entity_type', 100); // Reservation, Room, MenuItem, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('previous_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->index('action');
            $table->index('created_at');
        });

        // Central Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('general'); // resort, booking, dining, shop, tax
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string, boolean, integer, json
            $table->string('description', 255)->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('pages');
    }
};
