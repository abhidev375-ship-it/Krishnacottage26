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
        Schema::table('gallery_albums', function (Blueprint $table) {
            $table->string('name', 120)->nullable()->change();
            if (!Schema::hasColumn('gallery_albums', 'title')) {
                $table->string('title', 150)->nullable()->after('name');
            }
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallery_albums', function (Blueprint $table) {
            if (Schema::hasColumn('gallery_albums', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
