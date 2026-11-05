<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add is_returnable to spice_products table
        Schema::table('spice_products', function (Blueprint $table) {
            if (!Schema::hasColumn('spice_products', 'is_returnable')) {
                $table->boolean('is_returnable')->default(true)->after('is_active');
                $table->index('is_returnable');
            }
        });

        // Ensure all existing spice products default to returnable
        DB::table('spice_products')
            ->whereNull('is_returnable')
            ->update(['is_returnable' => true]);

        // 2. Ensure global spice_returns_enabled setting is seeded
        if (Schema::hasTable('settings')) {
            $existing = DB::table('settings')->where('key', 'spice_returns_enabled')->first();
            if (!$existing) {
                DB::table('settings')->insert([
                    'key' => 'spice_returns_enabled',
                    'value' => '1',
                    'type' => 'boolean',
                    'group' => 'spice_shop',
                    'description' => 'Master global toggle to enable or disable return and refund facility across all spices.',
                    'is_public' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spice_products', function (Blueprint $table) {
            if (Schema::hasColumn('spice_products', 'is_returnable')) {
                $table->dropColumn('is_returnable');
            }
        });

        DB::table('settings')->where('key', 'spice_returns_enabled')->delete();
    }
};
