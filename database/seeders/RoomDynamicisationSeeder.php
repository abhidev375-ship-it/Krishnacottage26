<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\RoomCategory;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoomDynamicisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Standard Room Categories
        $categoriesData = [
            [
                'name' => 'Heritage Cottages',
                'slug' => 'heritage-cottages',
                'icon' => 'castle',
                'description' => 'Handcrafted Kerala architecture with traditional teakwood craftsmanship and secluded verandas.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Luxury Pool Villas',
                'slug' => 'luxury-pool-villas',
                'icon' => 'palmtree',
                'description' => 'Expansive private sanctuaries with temperature-controlled plunge pools and personal gardens.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Forest Canopy Suites',
                'slug' => 'forest-canopy-suites',
                'icon' => 'trees',
                'description' => 'Elevated living amidst dense foliage with panoramic views over the misty Western Ghats.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Waterfront Pavilions',
                'slug' => 'waterfront-pavilions',
                'icon' => 'droplet',
                'description' => 'Perched by tranquil backwaters or hill streams, featuring breezy decks and waterside loungers.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Plantation Bungalows',
                'slug' => 'plantation-bungalows',
                'icon' => 'home',
                'description' => 'Colonial-era charm surrounded by organic spice plantations, cardamoms, and coffee groves.',
                'sort_order' => 5,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = RoomCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // 2. Standard Amenities Library (Grouped with Lucide icons)
        $amenitiesData = [
            // Views & Outdoors
            [
                'name' => 'Forest Canopy View',
                'slug' => 'forest-canopy-view',
                'icon' => 'trees',
                'category' => 'Views & Outdoors',
                'description' => 'Uninterrupted panoramic vistas of the surrounding misty evergreen canopy.',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Private Veranda Deck',
                'slug' => 'private-veranda-deck',
                'icon' => 'sun',
                'category' => 'Views & Outdoors',
                'description' => 'Secluded wooden sit-out with outdoor cane loungers.',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Mountain Vista',
                'slug' => 'mountain-vista',
                'icon' => 'mountain',
                'category' => 'Views & Outdoors',
                'description' => 'Views facing the majestic Anamudi peak and mist-laden valleys.',
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Private Garden Courtyard',
                'slug' => 'private-garden-courtyard',
                'icon' => 'flower-2',
                'category' => 'Views & Outdoors',
                'description' => 'Personal lawn space planted with indigenous orchids and medicinal herbs.',
                'is_featured' => false,
                'sort_order' => 4,
            ],

            // Wellness & Bath
            [
                'name' => 'Ayurvedic Herbal Tub',
                'slug' => 'ayurvedic-herbal-tub',
                'icon' => 'sparkles',
                'category' => 'Wellness & Bath',
                'description' => 'Deep stone soaking tub infused with herbal bath salts and essential oils.',
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Open-Air Rain Shower',
                'slug' => 'open-air-rain-shower',
                'icon' => 'bath',
                'category' => 'Wellness & Bath',
                'description' => 'Under-the-stars stone shower with herbal aromatherapy amenities.',
                'is_featured' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Organic Kerala Toiletries',
                'slug' => 'organic-kerala-toiletries',
                'icon' => 'leaf',
                'category' => 'Wellness & Bath',
                'description' => 'Handcrafted botanical soaps, shampoo, and Ayurvedic body lotions.',
                'is_featured' => false,
                'sort_order' => 7,
            ],

            // Room Comfort
            [
                'name' => 'King Teak Bed',
                'slug' => 'king-teak-bed',
                'icon' => 'bed-double',
                'category' => 'Room Comfort',
                'description' => 'Hand-carved solid teakwood king bed with organic cotton linen.',
                'is_featured' => false,
                'sort_order' => 8,
            ],
            [
                'name' => 'Climate Control (AC & Heater)',
                'slug' => 'climate-control',
                'icon' => 'wind',
                'category' => 'Room Comfort',
                'description' => 'Silent climate conditioning for both crisp mountain nights and sunny afternoons.',
                'is_featured' => false,
                'sort_order' => 9,
            ],
            [
                'name' => 'Artisan Coffee & Spice Tea',
                'slug' => 'artisan-coffee-spice-tea',
                'icon' => 'coffee',
                'category' => 'Room Comfort',
                'description' => 'French press coffee with freshly roasted Wayanad beans and estate cardamom chai.',
                'is_featured' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'Kerala Breakfast Included',
                'slug' => 'kerala-breakfast-included',
                'icon' => 'utensils',
                'category' => 'Room Comfort',
                'description' => 'Daily farm-to-table breakfast served on banana leaves or in your veranda.',
                'is_featured' => true,
                'sort_order' => 11,
            ],

            // Tech & Connectivity
            [
                'name' => 'High-Speed Wi-Fi',
                'slug' => 'high-speed-wifi',
                'icon' => 'wifi',
                'category' => 'Tech & Connectivity',
                'description' => 'High-speed fiber connectivity suitable for streaming and remote workation.',
                'is_featured' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Smart Ultra-HD TV',
                'slug' => 'smart-tv',
                'icon' => 'tv',
                'category' => 'Tech & Connectivity',
                'description' => '50-inch 4K smart television with casting support.',
                'is_featured' => false,
                'sort_order' => 13,
            ],
            [
                'name' => 'Safe & Digital Locker',
                'slug' => 'safe-digital-locker',
                'icon' => 'shield-check',
                'category' => 'Tech & Connectivity',
                'description' => 'Electronic keypad vault for laptop and travel valuables.',
                'is_featured' => false,
                'sort_order' => 14,
            ],
        ];

        $amenityModels = [];
        foreach ($amenitiesData as $am) {
            $amenityModels[$am['slug']] = Amenity::firstOrCreate(
                ['slug' => $am['slug']],
                $am
            );
        }

        // 3. Link Existing Room Types to Categories and Amenities
        $roomTypes = RoomType::all();
        $catKeys = array_keys($categories);

        foreach ($roomTypes as $index => $rt) {
            // Assign category based on name keywords or round-robin
            $matchedCat = null;
            $nameLower = strtolower($rt->name);
            if (str_contains($nameLower, 'villa') || str_contains($nameLower, 'pool')) {
                $matchedCat = $categories['luxury-pool-villas'];
            } elseif (str_contains($nameLower, 'cottage') || str_contains($nameLower, 'heritage')) {
                $matchedCat = $categories['heritage-cottages'];
            } elseif (str_contains($nameLower, 'canopy') || str_contains($nameLower, 'tree') || str_contains($nameLower, 'valley')) {
                $matchedCat = $categories['forest-canopy-suites'];
            } elseif (str_contains($nameLower, 'water') || str_contains($nameLower, 'lake') || str_contains($nameLower, 'river')) {
                $matchedCat = $categories['waterfront-pavilions'];
            } else {
                $catKey = $catKeys[$index % count($catKeys)];
                $matchedCat = $categories[$catKey];
            }

            $rt->update([
                'room_category_id' => $matchedCat->id,
            ]);

            // Sync amenities: match by existing JSON or assign rich default set
            $amenityIdsToSync = [];
            if (!empty($rt->amenities) && is_array($rt->amenities)) {
                foreach ($rt->amenities as $amenityText) {
                    $slug = Str::slug($amenityText);
                    if (isset($amenityModels[$slug])) {
                        $amenityIdsToSync[] = $amenityModels[$slug]->id;
                    }
                }
            }

            // If none matched from string, provide a rich baseline set
            if (empty($amenityIdsToSync)) {
                $amenityIdsToSync = [
                    $amenityModels['forest-canopy-view']->id,
                    $amenityModels['private-veranda-deck']->id,
                    $amenityModels['ayurvedic-herbal-tub']->id,
                    $amenityModels['king-teak-bed']->id,
                    $amenityModels['kerala-breakfast-included']->id,
                    $amenityModels['high-speed-wifi']->id,
                ];
            }

            $rt->amenitiesList()->syncWithoutDetaching($amenityIdsToSync);
        }
    }
}
