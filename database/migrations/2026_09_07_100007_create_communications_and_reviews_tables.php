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
        // Enquiries / Messages: Multi-branch customer communications inbox
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique(); // e.g. ENQ-2026-0034
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 120);
            $table->string('customer_email', 120)->index();
            $table->string('customer_phone', 30)->nullable();
            $table->enum('topic', [
                'booking_related',
                'dining',
                'spices',
                'events',
                'general'
            ])->default('general');
            $table->string('subject', 200)->nullable();
            $table->enum('status', [
                'new',
                'in_progress',
                'waiting_customer',
                'resolved',
                'closed'
            ])->default('new');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('has_unread_messages')->default(true);
            $table->timestamp('last_message_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
        });

        // Enquiry Messages: Conversation thread and internal staff notes
        Schema::create('enquiry_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['customer', 'staff', 'system'])->default('customer');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // staff user
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal_note')->default(false); // Staff internal note
            $table->timestamps();

            $table->index('enquiry_id');
        });

        // Reviews: Verified guest reviews tied strictly to completed reservations
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->unique()->constrained()->cascadeOnDelete(); // 1 review per booking
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained()->nullOnDelete();
            $table->tinyInteger('rating')->unsigned(); // 1 to 5 stars
            $table->string('title', 150)->nullable();
            $table->text('comment');
            $table->string('stay_summary', 150)->nullable(); // e.g. "3 nights · Garden Residence"
            $table->enum('status', ['pending', 'approved', 'hidden', 'rejected'])->default('pending');
            $table->text('staff_reply')->nullable();
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('verified_stay')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'status']);
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('enquiry_messages');
        Schema::dropIfExists('enquiries');
    }
};
