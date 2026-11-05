<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\RoomType;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Automatically adjusts base_price and weekend_price for multi-room villas
     * where the entire villa rate was saved as the single-room tariff.
     */
    public function up(): void
    {
        try {
            $villas = RoomType::with('rooms')->get();
            foreach ($villas as $villa) {
                $roomsCount = $villa->rooms ? $villa->rooms->count() : 0;
                // If the accommodation has multiple rooms and base_price is set to the full villa sum (>= 20000)
                // e.g. Krishna studio apartment at 35,000 for 4 rooms
                if ($roomsCount > 1 && $villa->base_price >= 20000) {
                    $oldBase = (float) $villa->base_price;
                    $oldWeekend = (float) ($villa->weekend_price ?: ($oldBase * 1.15));

                    $newBase = round($oldBase / $roomsCount, 2);
                    $newWeekend = ($oldWeekend >= 20000) ? round($oldWeekend / $roomsCount, 2) : round($newBase * 1.15, 2);

                    $villa->base_price = $newBase;
                    $villa->weekend_price = $newWeekend;
                    $villa->saveQuietly();
                }
            }
            \Illuminate\Support\Facades\Cache::forget('homepage_payload');
        } catch (\Throwable $e) {
            // Silently continue if tables not yet fully migrated
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible safe data migration
    }
};
