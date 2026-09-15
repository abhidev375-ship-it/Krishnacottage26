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
        // 1. Enhance enquiries table for direct chat & staff locking
        Schema::table('enquiries', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('guest_id')->constrained()->nullOnDelete();
            $table->foreignId('locked_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable()->after('locked_by');
            $table->string('client_session_id', 80)->nullable()->after('ticket_number')->index();
        });

        // 2. Enhance enquiry_messages for rich interactive cards
        Schema::table('enquiry_messages', function (Blueprint $table) {
            $table->string('card_type', 30)->nullable()->after('message'); // branch, room, spice, dining, nearby
            $table->json('card_payload')->nullable()->after('card_type');
        });

        // 3. Create chat_templates table for predefined answers & quick actions
        Schema::create('chat_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('category', 50)->default('General'); // Branches, Rooms, Dining, Spices, Nearby, General
            $table->enum('type', ['text', 'branch', 'room', 'spice', 'dining', 'nearby'])->default('text');
            $table->text('message')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('card_payload')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_templates');

        Schema::table('enquiry_messages', function (Blueprint $table) {
            $table->dropColumn(['card_type', 'card_payload']);
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['locked_by']);
            $table->dropColumn(['user_id', 'locked_by', 'locked_at', 'client_session_id']);
        });
    }
};
