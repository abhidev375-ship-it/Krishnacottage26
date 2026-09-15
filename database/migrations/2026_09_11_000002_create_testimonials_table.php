<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name');
            $table->string('stay_title')->nullable();
            $table->string('avatar_url')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('quote');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('review_id')->nullable()->constrained('reviews')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
