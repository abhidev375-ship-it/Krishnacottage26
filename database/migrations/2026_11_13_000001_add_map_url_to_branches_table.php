<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branches') && !Schema::hasColumn('branches', 'map_url')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->text('map_url')->nullable()->after('longitude');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('branches') && Schema::hasColumn('branches', 'map_url')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('map_url');
            });
        }
    }
};
