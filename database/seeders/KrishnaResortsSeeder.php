<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Enquiry;
use App\Models\EnquiryMessage;
use App\Models\Facility;
use App\Models\FoodOrder;
use App\Models\FoodOrderItem;
use App\Models\GalleryAlbum;
use App\Models\GalleryImage;
use App\Models\Guest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\ModifierGroup;
use App\Models\ModifierOption;
use App\Models\NavigationItem;
use App\Models\NearbyLocation;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\Page;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationStatusLog;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomBlock;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\SpiceCategory;
use App\Models\SpiceInventoryLog;
use App\Models\SpiceOrder;
use App\Models\SpiceOrderItem;
use App\Models\SpiceProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KrishnaResortsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Staff Users
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@krishnaresorts.com'],
            [
                'name' => 'Aditya Sharma',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'phone' => '+91 98450 11223',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
                'is_active' => true,
                'branch_access_type' => 'all',
            ]
        );

        $centralManager = User::updateOrCreate(
            ['email' => 'manager@krishnaresorts.com'],
            [
                'name' => 'Meera Nair',
                'password' => Hash::make('password123'),
                'role' => 'central_manager',
                'phone' => '+91 98450 44556',
                'avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80',
                'is_active' => true,
                'branch_access_type' => 'all',
            ]
        );

        $branchAManager = User::updateOrCreate(
            ['email' => 'manager.brancha@krishnaresorts.com'],
            [
                'name' => 'Rohan Varma',
                'password' => Hash::make('password123'),
                'role' => 'branch_manager',
                'phone' => '+91 98450 77889',
                'is_active' => true,
                'branch_access_type' => 'specific',
            ]
        );

        $receptionStaff = User::updateOrCreate(
            ['email' => 'reception@krishnaresorts.com'],
            [
                'name' => 'Anjali Pillai',
                'password' => Hash::make('password123'),
                'role' => 'reservation_staff',
                'phone' => '+91 98450 99001',
                'is_active' => true,
                'branch_access_type' => 'all',
            ]
        );

        // 2. Branches (Dynamic 3-Branch Model)
        $branchA = Branch::updateOrCreate(
            ['code' => 'BRA'],
            [
                'name' => 'Krishna · Branch A',
                'display_name' => 'Krishna Valley & Garden Retreat',
                'slug' => 'branch-a',
                'tagline' => 'Quiet views & garden stays amidst rolling mist',
                'short_description' => 'Nestled in lush valley terraces with organic spice gardens and private veranda suites.',
                'full_description' => 'Branch A provides an intimate nature sanctuary characterized by landscaped botanical verandas, quiet morning birdsong, and estate-sourced cuisine.',
                'address' => 'Tea Valley Hills, P.O. Box 12',
                'city' => 'Munnar',
                'state' => 'Kerala',
                'pincode' => '685612',
                'phone' => '+91 4865 230111',
                'email' => 'branch.a@krishnaresorts.com',
                'latitude' => 10.0889000,
                'longitude' => 77.0595000,
                'hero_image_url' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=85',
                'cover_image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=85',
                'status' => 'active',
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        $branchB = Branch::updateOrCreate(
            ['code' => 'BRB'],
            [
                'name' => 'Krishna · Branch B',
                'display_name' => 'Krishna Canopy Rainforest & Trails',
                'slug' => 'branch-b',
                'tagline' => 'Canopy stays & slow trails within virgin rainforest',
                'short_description' => 'Immersive treehouse lodges and timber villas sheltered beneath hundred-year-old teak forests.',
                'full_description' => 'Branch B invites nature lovers to experience elevated canopy villas, natural spring pools, guided night safaris, and cardamom estate trails.',
                'address' => 'Canopy Reserve Road, Sulthan Bathery',
                'city' => 'Wayanad',
                'state' => 'Kerala',
                'pincode' => '673592',
                'phone' => '+91 4936 220222',
                'email' => 'branch.b@krishnaresorts.com',
                'latitude' => 11.6854000,
                'longitude' => 76.1320000,
                'hero_image_url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=1200&q=85',
                'cover_image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=85',
                'status' => 'active',
                'is_published' => true,
                'sort_order' => 2,
            ]
        );

        $branchC = Branch::updateOrCreate(
            ['code' => 'BRC'],
            [
                'name' => 'Krishna · Branch C',
                'display_name' => 'Krishna Palm Horizons & Coastal Sanctuary',
                'slug' => 'branch-c',
                'tagline' => 'Sea air, swaying coconut groves & open horizons',
                'short_description' => 'Traditional thatched cottages and private courtyards steps away from serene sandy shores.',
                'full_description' => 'Branch C captures the serene rhythms of coastal Kerala with open-sky courtyards, Ayurveda wellness pavilions, and fresh catch coastal dining.',
                'address' => 'Beach Road, Mararikkulam North',
                'city' => 'Marari',
                'state' => 'Kerala',
                'pincode' => '688523',
                'phone' => '+91 4782 280333',
                'email' => 'branch.c@krishnaresorts.com',
                'latitude' => 9.6012000,
                'longitude' => 76.2974000,
                'hero_image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=85',
                'cover_image_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=85',
                'status' => 'active',
                'is_published' => true,
                'sort_order' => 3,
            ]
        );

        // Assign branch manager
        $branchAManager->branches()->syncWithoutDetaching([$branchA->id]);

        // 3. Room Types
        $rtDeluxeA = RoomType::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'deluxe-garden-residence'],
            [
                'name' => 'Deluxe Garden Residence',
                'code' => 'DGR-A',
                'short_description' => 'Spacious room opening into lush private spice gardens.',
                'description' => 'Features hand-crafted teak furniture, rain shower, private veranda overlooking the valley garden.',
                'base_price' => 6500.00,
                'weekend_price' => 7500.00,
                'max_guests' => 3,
                'max_adults' => 2,
                'max_children' => 1,
                'bed_type' => '1 King Bed',
                'size_sqft' => 480,
                'amenities' => ['Private Veranda', 'Rain Shower', 'Organic Toiletries', 'Wi-Fi 6', 'Espresso Maker', 'Mountain View'],
                'cover_image_url' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=85',
                'is_active' => true,
                'is_bookable' => true,
                'sort_order' => 1,
            ]
        );

        $rtVillaA = RoomType::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'valley-heritage-villa'],
            [
                'name' => 'Valley Heritage Villa',
                'code' => 'VHV-A',
                'short_description' => 'Standalone stone villa perched over the mist valley.',
                'description' => 'Two-tiered architecture with open sun deck, panoramic glass facades, and traditional wood interiors.',
                'base_price' => 11500.00,
                'weekend_price' => 13000.00,
                'max_guests' => 4,
                'max_adults' => 3,
                'max_children' => 2,
                'bed_type' => '1 King Bed + 1 Daybed',
                'size_sqft' => 750,
                'amenities' => ['Private Sun Deck', 'Fireplace', 'Butler Service', 'Luxury Bath', 'Espresso Machine', 'Valley View'],
                'cover_image_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=85',
                'is_active' => true,
                'is_bookable' => true,
                'sort_order' => 2,
            ]
        );

        $rtCanopyB = RoomType::updateOrCreate(
            ['branch_id' => $branchB->id, 'slug' => 'canopy-treehouse-suite'],
            [
                'name' => 'Canopy Treehouse Suite',
                'code' => 'CTS-B',
                'short_description' => 'Elevated timber suite nestled 40 feet above the forest floor.',
                'description' => 'Suspended amidst the rainforest canopy with 360-degree nature vistas and ambient night sounds.',
                'base_price' => 14000.00,
                'weekend_price' => 16000.00,
                'max_guests' => 2,
                'max_adults' => 2,
                'max_children' => 0,
                'bed_type' => '1 Super King Bed',
                'size_sqft' => 600,
                'amenities' => ['Canopy Balcony', 'Telescope', 'Binoculars', 'Open-sky Shower', 'Artisan Mini Bar'],
                'cover_image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=85',
                'is_active' => true,
                'is_bookable' => true,
                'sort_order' => 1,
            ]
        );

        $rtPalmC = RoomType::updateOrCreate(
            ['branch_id' => $branchC->id, 'slug' => 'palm-courtyard-cottage'],
            [
                'name' => 'Palm Courtyard Cottage',
                'code' => 'PCC-C',
                'short_description' => 'Coastal cottage with private open-to-sky shower courtyard.',
                'description' => 'Terracotta tiled roofs, breezy verandahs, and peaceful privacy shaded by mature coconut palms.',
                'base_price' => 8500.00,
                'weekend_price' => 9500.00,
                'max_guests' => 3,
                'max_adults' => 2,
                'max_children' => 1,
                'bed_type' => '1 Four-Poster King Bed',
                'size_sqft' => 520,
                'amenities' => ['Private Courtyard', 'Open-air Bath', 'Hammock', 'Beach Bag & Towels', 'Wi-Fi'],
                'cover_image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=800&q=85',
                'is_active' => true,
                'is_bookable' => true,
                'sort_order' => 1,
            ]
        );

        // 4. Physical Rooms
        $roomsData = [
            ['branch_id' => $branchA->id, 'room_type_id' => $rtDeluxeA->id, 'room_number' => '101', 'floor' => 'Ground Floor', 'operational_status' => 'occupied', 'housekeeping_status' => 'clean'],
            ['branch_id' => $branchA->id, 'room_type_id' => $rtDeluxeA->id, 'room_number' => '102', 'floor' => 'Ground Floor', 'operational_status' => 'available', 'housekeeping_status' => 'clean'],
            ['branch_id' => $branchA->id, 'room_type_id' => $rtDeluxeA->id, 'room_number' => '103', 'floor' => 'Ground Floor', 'operational_status' => 'reserved', 'housekeeping_status' => 'clean'],
            ['branch_id' => $branchA->id, 'room_type_id' => $rtDeluxeA->id, 'room_number' => '104', 'floor' => 'Ground Floor', 'operational_status' => 'available', 'housekeeping_status' => 'dirty'],
            ['branch_id' => $branchA->id, 'room_type_id' => $rtVillaA->id, 'room_number' => 'V-01', 'floor' => 'Upper Terrace', 'operational_status' => 'occupied', 'housekeeping_status' => 'clean'],
            ['branch_id' => $branchA->id, 'room_type_id' => $rtVillaA->id, 'room_number' => 'V-02', 'floor' => 'Upper Terrace', 'operational_status' => 'maintenance', 'housekeeping_status' => 'inspecting', 'notes' => 'Timber flooring polish'],
            ['branch_id' => $branchB->id, 'room_type_id' => $rtCanopyB->id, 'room_number' => 'T-01', 'floor' => 'Canopy Level 1', 'operational_status' => 'available', 'housekeeping_status' => 'clean'],
            ['branch_id' => $branchB->id, 'room_type_id' => $rtCanopyB->id, 'room_number' => 'T-02', 'floor' => 'Canopy Level 2', 'operational_status' => 'occupied', 'housekeeping_status' => 'clean'],
            ['branch_id' => $branchC->id, 'room_type_id' => $rtPalmC->id, 'room_number' => 'C-01', 'floor' => 'Palm Grove', 'operational_status' => 'available', 'housekeeping_status' => 'clean'],
            ['branch_id' => $branchC->id, 'room_type_id' => $rtPalmC->id, 'room_number' => 'C-02', 'floor' => 'Palm Grove', 'operational_status' => 'reserved', 'housekeeping_status' => 'clean'],
        ];

        $createdRooms = [];
        foreach ($roomsData as $r) {
            $createdRooms[$r['room_number']] = Room::updateOrCreate(
                ['branch_id' => $r['branch_id'], 'room_number' => $r['room_number']],
                $r
            );
        }

        // Room Block for V-02
        RoomBlock::updateOrCreate(
            ['room_id' => $createdRooms['V-02']->id, 'start_date' => Carbon::today()->toDateString()],
            [
                'branch_id' => $branchA->id,
                'end_date' => Carbon::today()->addDays(3)->toDateString(),
                'reason' => 'Annual timber polish and varnishing',
                'block_type' => 'renovation',
                'created_by' => $superAdmin->id,
            ]
        );

        // 5. Guests (CRM)
        $guest1 = Guest::updateOrCreate(
            ['email' => 'ananya.kulkarni@example.com'],
            [
                'first_name' => 'Ananya',
                'last_name' => 'Kulkarni',
                'phone' => '+91 98201 55667',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'vip_level' => 'gold',
                'preferences' => 'Prefers ground floor, quiet garden view, almond milk with tea.',
                'total_stays' => 3,
                'total_spent' => 48500.00,
            ]
        );

        $guest2 = Guest::updateOrCreate(
            ['email' => 'david.miller@example.co.uk'],
            [
                'first_name' => 'David',
                'last_name' => 'Miller',
                'phone' => '+44 7911 123456',
                'city' => 'London',
                'country' => 'United Kingdom',
                'vip_level' => 'silver',
                'preferences' => 'Allergic to shellfish. Interested in early morning birdwatching.',
                'total_stays' => 1,
                'total_spent' => 28000.00,
            ]
        );

        $guest3 = Guest::updateOrCreate(
            ['email' => 'rajesh.menon@example.com'],
            [
                'first_name' => 'Rajesh',
                'last_name' => 'Menon',
                'phone' => '+91 94471 22334',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'country' => 'India',
                'vip_level' => 'standard',
                'preferences' => 'Late check-in expected around 8:00 PM.',
                'total_stays' => 2,
                'total_spent' => 19500.00,
            ]
        );

        // 6. Reservations
        $res1 = Reservation::updateOrCreate(
            ['booking_code' => 'KR-2026-00101'],
            [
                'branch_id' => $branchA->id,
                'room_type_id' => $rtDeluxeA->id,
                'room_id' => $createdRooms['101']->id,
                'guest_id' => $guest1->id,
                'check_in_date' => Carbon::today()->subDays(1)->toDateString(),
                'check_out_date' => Carbon::today()->addDays(2)->toDateString(),
                'adults' => 2,
                'children' => 0,
                'rooms_count' => 1,
                'nightly_rate' => 6500.00,
                'subtotal' => 19500.00,
                'tax_amount' => 2340.00,
                'total_amount' => 21840.00,
                'paid_amount' => 21840.00,
                'status' => 'checked_in',
                'payment_status' => 'paid',
                'payment_method' => 'upi',
                'special_requests' => 'Quiet room facing cardamom plants.',
                'checked_in_at' => Carbon::today()->subDays(1)->setHour(14),
                'created_by' => $receptionStaff->id,
            ]
        );

        $res2 = Reservation::updateOrCreate(
            ['booking_code' => 'KR-2026-00102'],
            [
                'branch_id' => $branchA->id,
                'room_type_id' => $rtDeluxeA->id,
                'room_id' => $createdRooms['103']->id,
                'guest_id' => $guest3->id,
                'check_in_date' => Carbon::today()->toDateString(),
                'check_out_date' => Carbon::today()->addDays(3)->toDateString(),
                'adults' => 2,
                'children' => 1,
                'rooms_count' => 1,
                'nightly_rate' => 6500.00,
                'subtotal' => 19500.00,
                'tax_amount' => 2340.00,
                'total_amount' => 21840.00,
                'paid_amount' => 5000.00,
                'status' => 'confirmed',
                'payment_status' => 'partial',
                'payment_method' => 'credit_card',
                'special_requests' => 'Requires extra rollaway bed for child.',
                'created_by' => $receptionStaff->id,
            ]
        );

        $res3 = Reservation::updateOrCreate(
            ['booking_code' => 'KR-2026-00103'],
            [
                'branch_id' => $branchB->id,
                'room_type_id' => $rtCanopyB->id,
                'room_id' => $createdRooms['T-02']->id,
                'guest_id' => $guest2->id,
                'check_in_date' => Carbon::today()->subDays(2)->toDateString(),
                'check_out_date' => Carbon::today()->toDateString(),
                'adults' => 2,
                'children' => 0,
                'rooms_count' => 1,
                'nightly_rate' => 14000.00,
                'subtotal' => 28000.00,
                'tax_amount' => 3360.00,
                'total_amount' => 31360.00,
                'paid_amount' => 31360.00,
                'status' => 'checked_out',
                'payment_status' => 'paid',
                'payment_method' => 'credit_card',
                'checked_in_at' => Carbon::today()->subDays(2)->setHour(13),
                'checked_out_at' => Carbon::today()->setHour(10),
                'created_by' => $superAdmin->id,
            ]
        );

        // Reservation Status Log
        ReservationStatusLog::create([
            'reservation_id' => $res1->id,
            'from_status' => 'confirmed',
            'to_status' => 'checked_in',
            'user_id' => $receptionStaff->id,
            'note' => 'Guest arrived on time, ID verified, room key issued.',
            'created_at' => Carbon::today()->subDays(1)->setHour(14),
        ]);

        // Payment for Reservation 1
        Payment::create([
            'payable_type' => Reservation::class,
            'payable_id' => $res1->id,
            'branch_id' => $branchA->id,
            'transaction_id' => 'TXN-UPI-994821',
            'amount' => 21840.00,
            'payment_method' => 'upi',
            'gateway' => 'razorpay',
            'status' => 'successful',
            'notes' => 'Advance online payment completed',
            'created_by' => $receptionStaff->id,
        ]);

        // 7. Dining Menu
        $catBreakfast = MenuCategory::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'estate-breakfast'],
            ['name' => 'Estate Breakfast', 'description' => 'Served fresh from 07:00 AM to 10:30 AM', 'icon' => 'coffee', 'sort_order' => 1, 'is_active' => true]
        );

        $catKerala = MenuCategory::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'traditional-kerala-table'],
            ['name' => 'Traditional Kerala Table', 'description' => 'Authentic regional recipes prepared with estate coconut and spices', 'icon' => 'utensils', 'sort_order' => 2, 'is_active' => true]
        );

        $catBeverages = MenuCategory::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'harvest-beverages'],
            ['name' => 'Harvest Beverages', 'description' => 'Single-estate teas, filter coffee and cold pressed juices', 'icon' => 'glass-water', 'sort_order' => 3, 'is_active' => true]
        );

        // Menu items
        $item1 = MenuItem::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'appam-vegetable-stew'],
            [
                'menu_category_id' => $catBreakfast->id,
                'name' => 'Lacy Appams with Vegetable Stew',
                'short_description' => 'Fermented rice pancakes served with aromatic coconut milk stew.',
                'price' => 380.00,
                'tax_rate' => 5.00,
                'availability_state' => 'in_stock',
                'is_featured' => true,
                'is_vegetarian' => true,
                'dietary_tags' => ['Vegetarian', 'Gluten-Free', 'Signature'],
                'prep_time_minutes' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80',
                'sort_order' => 1,
            ]
        );

        $item2 = MenuItem::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'malabar-parotta-korma'],
            [
                'menu_category_id' => $catKerala->id,
                'name' => 'Layered Malabar Parotta with Cardamom Korma',
                'short_description' => 'Flaky hand-tossed bread paired with slow-simmered vegetable korma.',
                'price' => 450.00,
                'tax_rate' => 5.00,
                'availability_state' => 'limited_quantity',
                'stock_quantity' => 4,
                'is_featured' => true,
                'is_vegetarian' => true,
                'dietary_tags' => ['Vegetarian', 'Chef Special'],
                'prep_time_minutes' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=600&q=80',
                'sort_order' => 2,
            ]
        );

        $item3 = MenuItem::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'single-origin-estate-coffee'],
            [
                'menu_category_id' => $catBeverages->id,
                'name' => 'Monsooned Malabar Filter Coffee',
                'short_description' => 'Dark roast estate beans brewed with frothy fresh farm milk.',
                'price' => 180.00,
                'tax_rate' => 5.00,
                'availability_state' => 'in_stock',
                'is_featured' => false,
                'is_vegetarian' => true,
                'dietary_tags' => ['Vegetarian'],
                'prep_time_minutes' => 10,
                'sort_order' => 3,
            ]
        );

        $item4 = MenuItem::updateOrCreate(
            ['branch_id' => $branchA->id, 'slug' => 'wild-mushroom-thoran'],
            [
                'menu_category_id' => $catKerala->id,
                'name' => 'Forest Wild Mushroom Thoran',
                'short_description' => 'Foraged mushrooms stir-fried with freshly grated coconut and mustard seeds.',
                'price' => 420.00,
                'tax_rate' => 5.00,
                'availability_state' => 'out_of_stock',
                'is_featured' => false,
                'is_vegetarian' => true,
                'dietary_tags' => ['Vegan', 'Gluten-Free'],
                'prep_time_minutes' => 20,
                'sort_order' => 4,
            ]
        );

        // Modifier Group: Spice Level
        $modGroup = ModifierGroup::updateOrCreate(
            ['name' => 'Spice Level'],
            ['branch_id' => null, 'selection_type' => 'required', 'min_selections' => 1, 'max_selections' => 1]
        );

        ModifierOption::updateOrCreate(
            ['modifier_group_id' => $modGroup->id, 'name' => 'Mild & Subtle'],
            ['price_adjustment' => 0.00, 'is_available' => true, 'sort_order' => 1]
        );
        ModifierOption::updateOrCreate(
            ['modifier_group_id' => $modGroup->id, 'name' => 'Traditional Kerala Medium'],
            ['price_adjustment' => 0.00, 'is_available' => true, 'sort_order' => 2]
        );
        ModifierOption::updateOrCreate(
            ['modifier_group_id' => $modGroup->id, 'name' => 'Fiery Black Pepper Extra'],
            ['price_adjustment' => 20.00, 'is_available' => true, 'sort_order' => 3]
        );

        $item2->modifierGroups()->syncWithoutDetaching([$modGroup->id]);

        // Food Order
        $foodOrder1 = FoodOrder::updateOrCreate(
            ['order_number' => 'FO-2026-0042'],
            [
                'branch_id' => $branchA->id,
                'guest_id' => $guest1->id,
                'room_id' => $createdRooms['101']->id,
                'customer_name' => 'Ananya Kulkarni',
                'customer_phone' => '+91 98201 55667',
                'order_type' => 'room_service',
                'table_number' => 'Room 101',
                'status' => 'preparing',
                'payment_status' => 'paid',
                'subtotal' => 830.00,
                'tax_amount' => 41.50,
                'total_amount' => 871.50,
                'special_instructions' => 'Please bring extra paper napkins and water.',
                'ordered_at' => Carbon::now()->subMinutes(18),
            ]
        );

        FoodOrderItem::updateOrCreate(
            ['food_order_id' => $foodOrder1->id, 'menu_item_id' => $item1->id],
            [
                'item_name' => 'Lacy Appams with Vegetable Stew',
                'unit_price' => 380.00,
                'quantity' => 1,
                'subtotal' => 380.00,
            ]
        );

        FoodOrderItem::updateOrCreate(
            ['food_order_id' => $foodOrder1->id, 'menu_item_id' => $item2->id],
            [
                'item_name' => 'Layered Malabar Parotta with Cardamom Korma',
                'unit_price' => 450.00,
                'quantity' => 1,
                'subtotal' => 450.00,
                'selected_modifiers' => ['Spice Level: Traditional Kerala Medium'],
            ]
        );

        // 8. Krishna Spices (Shop)
        $scWhole = SpiceCategory::updateOrCreate(
            ['slug' => 'whole-estate-spices'],
            ['name' => 'Whole Estate Spices', 'description' => 'Sun-dried unbleached whole spices from organic hill estates', 'sort_order' => 1, 'is_active' => true]
        );

        $scGround = SpiceCategory::updateOrCreate(
            ['slug' => 'artisan-blends-and-ground'],
            ['name' => 'Artisan Blends & Ground', 'description' => 'Cold-milled fragrant powders and heritage masala blends', 'sort_order' => 2, 'is_active' => true]
        );

        $scGifts = SpiceCategory::updateOrCreate(
            ['slug' => 'curated-gift-boxes'],
            ['name' => 'Curated Gift Boxes', 'description' => 'Handcrafted wooden gift collections for culinary connoisseurs', 'sort_order' => 3, 'is_active' => true]
        );

        // Spice products
        $spPepper = SpiceProduct::updateOrCreate(
            ['sku' => 'KS-PEP-100'],
            [
                'spice_category_id' => $scWhole->id,
                'name' => 'Malabar Tellicherry Garbled Extra Bold Black Pepper',
                'slug' => 'malabar-tellicherry-black-pepper',
                'short_description' => 'TGSEB grade, bold berries hand-harvested from high-altitude vines.',
                'description' => 'Considered the gold standard of global pepper. Intensely fragrant with citrus and cedar notes followed by warm, clean pungency.',
                'weight_grams' => 150,
                'package_size' => '150g Glass Jar',
                'price' => 420.00,
                'compare_at_price' => 480.00,
                'stock_quantity' => 38,
                'reserved_quantity' => 2,
                'low_stock_threshold' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=500&q=80',
                'is_featured' => true,
                'is_published' => true,
                'status' => 'in_stock',
                'sort_order' => 1,
            ]
        );

        $spCardamom = SpiceProduct::updateOrCreate(
            ['sku' => 'KS-CRD-100'],
            [
                'spice_category_id' => $scWhole->id,
                'name' => 'Wayanad High-Grown Green Cardamom 8mm+',
                'slug' => 'wayanad-green-cardamom-8mm',
                'short_description' => 'Jumbo emerald green pods packed with sweet, menthol-rich essential oils.',
                'weight_grams' => 100,
                'package_size' => '100g Airtight Tin',
                'price' => 680.00,
                'compare_at_price' => 750.00,
                'stock_quantity' => 8, // Low stock!
                'reserved_quantity' => 1,
                'low_stock_threshold' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?auto=format&fit=crop&w=500&q=80',
                'is_featured' => true,
                'is_published' => true,
                'status' => 'low_stock',
                'sort_order' => 2,
            ]
        );

        $spCinnamon = SpiceProduct::updateOrCreate(
            ['sku' => 'KS-CIN-100'],
            [
                'spice_category_id' => $scWhole->id,
                'name' => 'True Ceylon Quills Organic Cinnamon',
                'slug' => 'true-ceylon-cinnamon-quills',
                'short_description' => 'Delicate multilayered sweet quills, non-cassia variety.',
                'weight_grams' => 100,
                'package_size' => '100g Kraft Pouch',
                'price' => 360.00,
                'stock_quantity' => 45,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1509358271058-acd22cc93898?auto=format&fit=crop&w=500&q=80',
                'is_featured' => false,
                'is_published' => true,
                'status' => 'in_stock',
                'sort_order' => 3,
            ]
        );

        // Spice Inventory Log
        SpiceInventoryLog::create([
            'spice_product_id' => $spCardamom->id,
            'adjustment_type' => 'stock_out',
            'quantity_change' => -5,
            'stock_before' => 13,
            'stock_after' => 8,
            'reason' => 'Online customer orders fulfilled',
            'user_id' => $superAdmin->id,
            'created_at' => Carbon::now()->subHours(3),
        ]);

        // Spice Order
        $spiceOrder1 = SpiceOrder::updateOrCreate(
            ['order_number' => 'SP-2026-0088'],
            [
                'guest_id' => $guest1->id,
                'customer_name' => 'Ananya Kulkarni',
                'customer_email' => 'ananya.kulkarni@example.com',
                'customer_phone' => '+91 98201 55667',
                'shipping_address_line1' => 'Flat 402, Sea Breeze Apts, Worli',
                'shipping_city' => 'Mumbai',
                'shipping_state' => 'Maharashtra',
                'shipping_pincode' => '400018',
                'shipping_country' => 'India',
                'shipping_courier' => 'BlueDart Express',
                'tracking_number' => 'BLD-884920199',
                'status' => 'shipped',
                'payment_status' => 'paid',
                'subtotal' => 1100.00,
                'shipping_charge' => 0.00,
                'tax_amount' => 55.00,
                'total_amount' => 1155.00,
                'shipped_at' => Carbon::yesterday(),
            ]
        );

        SpiceOrderItem::updateOrCreate(
            ['spice_order_id' => $spiceOrder1->id, 'spice_product_id' => $spPepper->id],
            ['product_name' => $spPepper->name, 'product_sku' => $spPepper->sku, 'unit_price' => 420.00, 'quantity' => 1, 'total_price' => 420.00]
        );

        SpiceOrderItem::updateOrCreate(
            ['spice_order_id' => $spiceOrder1->id, 'spice_product_id' => $spCardamom->id],
            ['product_name' => $spCardamom->name, 'product_sku' => $spCardamom->sku, 'unit_price' => 680.00, 'quantity' => 1, 'total_price' => 680.00]
        );

        // 9. Facilities (No swimming pool, genuine experiences)
        $facilitiesData = [
            [
                'branch_id' => $branchA->id,
                'name' => 'Ayur Veda Healing Sanctuary',
                'slug' => 'ayur-veda-healing-sanctuary',
                'category' => 'wellness',
                'short_description' => 'Traditional restorative herbal therapies and warm oil Abhyanga treatments.',
                'description' => 'Conducted by certified Vaidyas using seasonal estate-prepared medicinal oils.',
                'operating_hours' => '07:00 AM - 07:00 PM',
                'image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'branch_id' => $branchA->id,
                'name' => 'Private Cardamom & Pepper Plantation Walk',
                'slug' => 'plantation-walk',
                'category' => 'nature',
                'short_description' => 'Guided exploration of organic spice terraces with our senior naturalist.',
                'description' => 'Learn harvest secrets, rub raw pepper leaves, and taste estate-brewed cinnamon infusion.',
                'operating_hours' => '06:30 AM & 04:30 PM',
                'image_url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'branch_id' => $branchB->id,
                'name' => 'Twilight Canopy Birdwatching & Stargazing',
                'slug' => 'canopy-birdwatching-stargazing',
                'category' => 'activities',
                'short_description' => 'High-altitude spotting platform with precision optics for celestial and avian observing.',
                'operating_hours' => '05:30 PM - 09:00 PM',
                'image_url' => 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($facilitiesData as $f) {
            Facility::updateOrCreate(
                ['branch_id' => $f['branch_id'], 'slug' => $f['slug']],
                $f
            );
        }

        // 10. Gallery Albums & Images
        $album1 = GalleryAlbum::updateOrCreate(
            ['slug' => 'valley-and-garden-vistas'],
            [
                'branch_id' => $branchA->id,
                'name' => 'Valley & Garden Vistas',
                'category' => 'nature',
                'description' => 'Morning mist rolling over cardamom terraces and mountain views.',
                'cover_image_url' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=85',
                'is_featured' => true,
                'is_published' => true,
                'images_count' => 3,
                'sort_order' => 1,
            ]
        );

        GalleryImage::updateOrCreate(
            ['gallery_album_id' => $album1->id, 'image_url' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=85'],
            ['title' => 'Mist Rising at Dawn', 'alt_text' => 'Valley mist at dawn', 'is_featured' => true, 'sort_order' => 1]
        );
        GalleryImage::updateOrCreate(
            ['gallery_album_id' => $album1->id, 'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=85'],
            ['title' => 'Resort Main Lawn', 'alt_text' => 'Main landscape lawn', 'is_featured' => false, 'sort_order' => 2]
        );
        GalleryImage::updateOrCreate(
            ['gallery_album_id' => $album1->id, 'image_url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=1200&q=85'],
            ['title' => 'Canopy Trail Entrance', 'alt_text' => 'Canopy trail entrance path', 'is_featured' => false, 'sort_order' => 3]
        );

        // 11. Nearby Locations
        NearbyLocation::updateOrCreate(
            ['branch_id' => $branchA->id, 'name' => 'Attukad Cascading Waterfalls'],
            [
                'category' => 'nature',
                'description' => 'Dramatic seasonal multi-tier waterfalls surrounded by dense jungle greenery.',
                'distance_km' => 4.20,
                'travel_time' => '12 mins drive',
                'image_url' => 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        NearbyLocation::updateOrCreate(
            ['branch_id' => $branchA->id, 'name' => 'Ancient Tea Factory & Heritage Museum'],
            [
                'category' => 'landmark',
                'description' => 'Century-old roller machinery, orthodox black tea tasting and colonial archival photographs.',
                'distance_km' => 7.80,
                'travel_time' => '20 mins drive',
                'image_url' => 'https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        // 12. Enquiries & Messages
        $enquiry1 = Enquiry::updateOrCreate(
            ['ticket_number' => 'ENQ-2026-0031'],
            [
                'branch_id' => $branchA->id,
                'guest_id' => $guest2->id,
                'reservation_id' => $res3->id,
                'customer_name' => 'David Miller',
                'customer_email' => 'david.miller@example.co.uk',
                'customer_phone' => '+44 7911 123456',
                'topic' => 'booking_related',
                'subject' => 'Airport transfer arrangement from Cochin Airport',
                'status' => 'in_progress',
                'priority' => 'high',
                'assigned_to' => $receptionStaff->id,
                'has_unread_messages' => false,
                'last_message_at' => Carbon::now()->subHours(2),
            ]
        );

        EnquiryMessage::create([
            'enquiry_id' => $enquiry1->id,
            'sender_type' => 'customer',
            'message' => 'Hello Krishna team, we are landing at Cochin International (COK) on the 14th around 11:30 AM. Could you arrange a private Innova taxi to Branch A?',
            'is_internal_note' => false,
            'created_at' => Carbon::now()->subHours(4),
        ]);

        EnquiryMessage::create([
            'enquiry_id' => $enquiry1->id,
            'sender_type' => 'staff',
            'user_id' => $receptionStaff->id,
            'message' => 'Dear Mr. Miller, warm greetings from Krishna Resorts! We would be delighted to arrange a chauffeur-driven Toyota Innova Crysta for your pickup. The journey takes approximately 3.5 hours.',
            'is_internal_note' => false,
            'created_at' => Carbon::now()->subHours(2),
        ]);

        EnquiryMessage::create([
            'enquiry_id' => $enquiry1->id,
            'sender_type' => 'staff',
            'user_id' => $receptionStaff->id,
            'message' => 'Internal Note: Coordinated with Driver Suresh (KL-07-CD-4421). Chauffeur details to be sent 24h prior.',
            'is_internal_note' => true,
            'created_at' => Carbon::now()->subHours(1),
        ]);

        // 13. Verified Stay Reviews
        Review::updateOrCreate(
            ['reservation_id' => $res3->id],
            [
                'branch_id' => $branchB->id,
                'guest_id' => $guest2->id,
                'room_type_id' => $rtCanopyB->id,
                'rating' => 5,
                'title' => 'An unforgettable canopy experience',
                'comment' => 'Waking up to the morning mist over the rainforest canopy was sublime. The staff treated us like family, and the food was extraordinarily authentic and fresh.',
                'stay_summary' => '2 nights · Canopy Treehouse Suite',
                'status' => 'approved',
                'staff_reply' => 'Thank you so much Mr. Miller! We look forward to welcoming you back to Krishna soon.',
                'replied_by' => $superAdmin->id,
                'replied_at' => Carbon::now()->subHours(5),
                'is_featured' => true,
                'verified_stay' => true,
            ]
        );

        // 14. Notification Templates & Logs
        NotificationTemplate::updateOrCreate(
            ['code' => 'BOOKING_CONFIRMATION'],
            [
                'name' => 'Booking Confirmation',
                'channel' => 'email',
                'subject' => 'Reservation Confirmed: Krishna Resorts (Booking #{{booking_id}})',
                'body' => 'Dear {{guest_name}}, your reservation at {{branch_name}} from {{check_in}} to {{check_out}} is confirmed. We look forward to welcoming you!',
                'variables' => ['guest_name', 'booking_id', 'branch_name', 'check_in', 'check_out'],
                'is_active' => true,
            ]
        );

        NotificationLog::create([
            'event' => 'booking_confirmed',
            'channel' => 'email',
            'recipient' => 'ananya.kulkarni@example.com',
            'branch_id' => $branchA->id,
            'reference_type' => Reservation::class,
            'reference_id' => $res1->id,
            'subject' => 'Reservation Confirmed: Krishna Resorts (Booking #KR-2026-00101)',
            'message_body' => 'Dear Ananya Kulkarni, your reservation at Krishna · Branch A is confirmed.',
            'status' => 'delivered',
            'sent_at' => Carbon::today()->subDays(1)->setHour(14),
        ]);

        // 15. Audit Log
        AuditLog::create([
            'user_id' => $superAdmin->id,
            'user_name' => 'Aditya Sharma',
            'role' => 'super_admin',
            'branch_id' => $branchA->id,
            'action' => 'block_room',
            'entity_type' => 'Room',
            'entity_id' => $createdRooms['V-02']->id,
            'previous_values' => ['operational_status' => 'available'],
            'new_values' => ['operational_status' => 'maintenance', 'reason' => 'Timber polish'],
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::now()->subHours(6),
        ]);

        // 16. System Settings
        $settings = [
            ['group' => 'resort', 'key' => 'resort_name', 'value' => 'Krishna Resorts', 'type' => 'string', 'description' => 'Official brand name'],
            ['group' => 'booking', 'key' => 'check_in_time', 'value' => '14:00', 'type' => 'string', 'description' => 'Standard check-in time'],
            ['group' => 'booking', 'key' => 'check_out_time', 'value' => '11:00', 'type' => 'string', 'description' => 'Standard check-out time'],
            ['group' => 'booking', 'key' => 'room_hold_duration_minutes', 'value' => '15', 'type' => 'integer', 'description' => 'Temporary inventory hold during booking checkout'],
            ['group' => 'tax', 'key' => 'gst_room_standard', 'value' => '12.0', 'type' => 'decimal', 'description' => 'GST percentage on room tariff'],
            ['group' => 'tax', 'key' => 'gst_dining_standard', 'value' => '5.0', 'type' => 'decimal', 'description' => 'GST percentage on food & dining'],
            ['group' => 'cancellation', 'key' => 'free_cancellation_hours', 'value' => '48', 'type' => 'integer', 'description' => 'Hours before check-in eligible for full refund'],
            ['group' => 'homepage', 'key' => 'homepage_content', 'value' => 'settings/homepage_content.json', 'type' => 'json_file', 'description' => 'Dynamic homepage content configuration'],
            ['group' => 'contact', 'key' => 'contact_content', 'value' => 'settings/contact_content.json', 'type' => 'json_file', 'description' => 'Dynamic contact page configuration & FAQs'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
