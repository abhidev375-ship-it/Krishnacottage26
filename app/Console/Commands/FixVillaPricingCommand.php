<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RoomType;

class FixVillaPricingCommand extends Command
{
    protected $signature = 'rooms:fix-villa-pricing';
    protected $description = 'Normalize base_price to single-room unit rate for multi-room villas';

    public function handle(): int
    {
        $villas = RoomType::with('rooms')->get();
        $updated = 0;
        foreach ($villas as $villa) {
            $count = $villa->rooms ? $villa->rooms->count() : 0;
            if ($count > 1 && $villa->base_price >= 20000) {
                $old = $villa->base_price;
                $villa->base_price = round($villa->base_price / $count, 2);
                if ($villa->weekend_price && $villa->weekend_price >= 20000) {
                    $villa->weekend_price = round($villa->weekend_price / $count, 2);
                } else {
                    $villa->weekend_price = round($villa->base_price * 1.15, 2);
                }
                $villa->saveQuietly();
                $this->info("Updated {$villa->name} (ID: {$villa->id}): base_price changed from {$old} to {$villa->base_price} for {$count} rooms.");
                $updated++;
            }
        }
        \Illuminate\Support\Facades\Cache::forget('homepage_payload');
        $this->info("Completed. {$updated} accommodation(s) normalized.");
        return 0;
    }
}
