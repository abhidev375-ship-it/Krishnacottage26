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
        Schema::create('reservation_folio_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->enum('category', [
                'minibar',
                'laundry',
                'extra_bed',
                'transport',
                'spa_wellness',
                'activity',
                'dining',
                'miscellaneous'
            ])->default('miscellaneous');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->boolean('is_paid')->default(false);
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'is_paid']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_folio_charges');
    }
};
