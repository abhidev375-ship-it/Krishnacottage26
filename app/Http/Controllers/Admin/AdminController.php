<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\CancellationRule;
use App\Models\Enquiry;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\FoodOrder;
use App\Models\GalleryAlbum;
use App\Models\Guest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\ModifierGroup;
use App\Models\NavigationItem;
use App\Models\NearbyLocation;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\Page;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\SpiceCategory;
use App\Models\SpiceInventoryLog;
use App\Models\SpiceOrder;
use App\Models\SpiceProduct;
use App\Models\StayExtensionRequest;
use App\Models\TaxiRequest;
use App\Models\Testimonial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->get('branch_id');
        if ($branchId === 'all' || $branchId === '' || !is_numeric($branchId)) {
            $branchId = null;
        }

        $user = auth()->user();
        $isBranchScoped = $user && $user->role === 'branch_manager' && $user->branch_access_type === 'specific';

        if ($isBranchScoped) {
            $branches = $user->branches()->orderBy('sort_order')->get();
            $allowedBranchIds = $branches->pluck('id')->toArray();
            if (!$branchId || !in_array((int)$branchId, $allowedBranchIds)) {
                $branchId = !empty($allowedBranchIds) ? $allowedBranchIds[0] : null;
            }
        } else {
            $branches = Branch::orderBy('sort_order')->get();
        }

        $selectedBranch = $branchId ? Branch::find($branchId) : null;

        // 1. Dashboard Operational Metrics
        $today = Carbon::today();
        
        $arrivalsCount = Reservation::forBranch($branchId)
            ->whereDate('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        $departuresCount = Reservation::forBranch($branchId)
            ->whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        $stayingCount = Reservation::forBranch($branchId)
            ->where('status', 'checked_in')
            ->count();

        $roomsQuery = Room::query();
        if ($branchId) {
            $roomsQuery->where('branch_id', $branchId);
        }
        $totalRoomsCount = (clone $roomsQuery)->count();
        $availableRoomsCount = (clone $roomsQuery)->where('operational_status', 'available')->count();
        $occupiedRoomsCount = (clone $roomsQuery)->where('operational_status', 'occupied')->count();
        $maintenanceRoomsCount = (clone $roomsQuery)->whereIn('operational_status', ['maintenance', 'blocked', 'out_of_order'])->count();

        $pendingFoodOrders = FoodOrder::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['new', 'accepted', 'preparing'])
            ->count();

        $lowStockProducts = 0; // Spices are fresh pack-on-order (Zero stock tracking)
        $freshSpiceOrdersCount = SpiceOrder::whereIn('status', ['processing', 'paid', 'new'])->count();

        $unreadMessagesCount = Enquiry::query()
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->where('has_unread_messages', true)
            ->count();
        $pendingReviewsCount = Review::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'pending')
            ->count();

        // 2. Reservations Data
        $reservations = Reservation::with(['branch', 'roomType', 'room', 'guest'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('created_at')
            ->get();

        // 2b. Checked-In Reservations (Centralised In-House Fleet)
        $checkedInReservations = Reservation::with([
            'branch', 'room', 'roomType', 'guest.user', 'folioCharges', 'extensionRequests', 'payments'
        ])
            ->where('status', 'checked_in')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('check_out_date', 'asc')
            ->get();

        // 3. Rooms & Room Types
        $roomTypes = RoomType::with(['branch', 'category', 'amenitiesList', 'rooms'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('sort_order')
            ->get();

        $roomCategories = RoomCategory::withCount('roomTypes')->orderBy('sort_order')->get();
        $amenities = Amenity::withCount('roomTypes')->orderBy('category')->orderBy('sort_order')->get();

        $rooms = Room::with(['branch', 'roomType'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('room_number')
            ->get();

        // 4. Guests
        $guests = Guest::orderByDesc('total_spent')->get();

        // 5. Dining Menu & Orders
        $menuCategories = MenuCategory::with(['branch', 'menuItems'])
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderBy('sort_order')
            ->get();

        $menuItems = MenuItem::with(['branch', 'category', 'modifierGroups.options', 'approvedReviews'])
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->where('branch_id', $branchId)->orWhere('is_all_branches', true)->orWhereNull('branch_id')->orWhereJsonContains('allocated_branch_ids', (int)$branchId)))
            ->orderBy('sort_order')
            ->get();

        $modifierGroups = ModifierGroup::with('options')
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->get();

        $foodOrders = FoodOrder::with(['branch', 'guest', 'items.menuItem', 'foodReview'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('ordered_at')
            ->get();

        $foodReviews = \App\Models\FoodReview::with(['order', 'menuItem', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('created_at')
            ->get();
        $pendingFoodReviewsCount = \App\Models\FoodReview::when($branchId, fn($q) => $q->where('branch_id', $branchId))->where('status', 'pending')->count();

        // 6. Spice Shop & Pack-on-Order Management
        $spiceCategories = SpiceCategory::with('products')->orderBy('sort_order')->get();
        $spiceProducts = SpiceProduct::with('category')->orderBy('sort_order')->get();
        $spiceOrders = SpiceOrder::with(['guest', 'items'])->orderByDesc('created_at')->get();
        $inventoryLogs = SpiceInventoryLog::with(['product', 'user'])->latest('created_at')->take(15)->get();

        // 7. Resort Content & Media
        $facilities = Facility::with('branch')
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderBy('sort_order')
            ->get();

        $facilityBookings = FacilityBooking::with(['facility', 'reservation.room', 'guest', 'branch', 'folioCharge'])
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->latest()
            ->get();

        $galleryAlbums = GalleryAlbum::with(['branch', 'images'])
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderBy('sort_order')
            ->get();

        $nearbyLocations = NearbyLocation::with('branch')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('sort_order')
            ->get();

        $taxiRequests = TaxiRequest::with(['reservation.room', 'guest', 'branch', 'folioCharge'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->get();

        // 8. Communication & Reviews
        $enquiries = Enquiry::with(['branch', 'guest', 'reservation', 'assignedStaff', 'messages'])
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        $reviews = Review::with(['branch', 'guest', 'roomType', 'reservation'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('created_at')
            ->get();

        $testimonials = Testimonial::with(['branch', 'review'])
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderBy('sort_order')
            ->get();

        // 9. CMS & System
        $pages = Page::with('branch')->orderBy('sort_order')->get();
        $navigationItems = NavigationItem::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
        $notificationLogs = NotificationLog::latest()->take(20)->get();
        $notificationTemplates = NotificationTemplate::where('is_active', true)->get();
        $staffMembers = User::with('branches')->orderBy('name')->get();
        $auditLogs = AuditLog::with('branch')->latest('created_at')->take(25)->get();
        $settings = Setting::all()->groupBy('group');
        $homepageContent = $this->getHomepageContent();

        $cancellationRules = CancellationRule::with('branch')->orderBy('sort_order')->orderByDesc('hours_before_checkin')->get();
        $cancellationPolicy = Setting::get('cancellation_policy_description', 'At Country Side Cottages, cancellations made 72+ hours prior receive 100% cashback. 48-72h prior receive 75%, and 24-48h prior receive 50%. Instant automated payback.');
        $stayExtensionRequests = StayExtensionRequest::with(['reservation.room', 'reservation.roomType', 'reservation.branch', 'guest', 'user', 'reviewer'])
            ->latest()
            ->take(30)
            ->get();

        // 10. Fast Flight Deck: Today's Expected Arrivals and Departures (1-Tap Operation)
        $todayArrivals = Reservation::forBranch($branchId)
            ->with(['branch', 'roomType', 'room', 'guest'])
            ->whereDate('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderBy('check_in_date')
            ->get();

        $todayDepartures = Reservation::forBranch($branchId)
            ->with(['branch', 'roomType', 'room', 'guest', 'folioCharges', 'payments'])
            ->whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->orderBy('check_out_date')
            ->get();

        // 11. Pre-Aggregated 7-Day Sellable Room Availability Matrix (Single Query Optimization)
        $matrixDates = [];
        for ($i = 0; $i < 7; $i++) {
            $matrixDates[] = $today->copy()->addDays($i)->toDateString();
        }
        $overlappingReservations = Reservation::whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<', $today->copy()->addDays(7)->toDateString())
            ->where('check_out_date', '>', $today->toDateString())
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get(['id', 'room_type_id', 'check_in_date', 'check_out_date']);

        $availabilityMatrix = [];
        foreach ($roomTypes as $rt) {
            $totalUnits = $rt->rooms->count();
            $availabilityMatrix[$rt->id] = [];
            foreach ($matrixDates as $date) {
                $booked = $overlappingReservations->filter(function ($res) use ($rt, $date) {
                    $ci = is_string($res->check_in_date) ? $res->check_in_date : $res->check_in_date->toDateString();
                    $co = is_string($res->check_out_date) ? $res->check_out_date : $res->check_out_date->toDateString();
                    return $res->room_type_id == $rt->id && $ci <= $date && $co > $date;
                })->count();
                $available = max(0, $totalUnits - $booked);
                $availabilityMatrix[$rt->id][$date] = [
                    'total' => $totalUnits,
                    'booked' => $booked,
                    'available' => $available,
                ];
            }
        }

        // 12. Homepage Featured Modules Status
        $diningModuleStatus = Setting::get('dining_module_status', 'active');
        $homepageModules = [
            'branches' => Setting::get('homepage_module_branches', '1') == '1',
            'categories' => Setting::get('homepage_module_categories', '1') == '1',
            'facilities' => Setting::get('homepage_module_facilities', '1') == '1',
            'dining' => $diningModuleStatus !== 'hidden' && Setting::get('homepage_module_dining', '1') == '1',
            'spices' => Setting::get('homepage_module_spices', '1') == '1',
            'reviews' => Setting::get('homepage_module_reviews', '1') == '1',
        ];

        return view('admin.index', compact(
            'branches',
            'selectedBranch',
            'branchId',
            'arrivalsCount',
            'departuresCount',
            'stayingCount',
            'totalRoomsCount',
            'availableRoomsCount',
            'occupiedRoomsCount',
            'maintenanceRoomsCount',
            'pendingFoodOrders',
            'lowStockProducts',
            'unreadMessagesCount',
            'pendingReviewsCount',
            'reservations',
            'roomTypes',
            'roomCategories',
            'amenities',
            'rooms',
            'guests',
            'menuCategories',
            'menuItems',
            'modifierGroups',
            'foodOrders',
            'spiceCategories',
            'spiceProducts',
            'spiceOrders',
            'freshSpiceOrdersCount',
            'inventoryLogs',
            'facilities',
            'facilityBookings',
            'galleryAlbums',
            'nearbyLocations',
            'taxiRequests',
            'enquiries',
            'reviews',
            'pages',
            'navigationItems',
            'notificationLogs',
            'notificationTemplates',
            'staffMembers',
            'auditLogs',
            'settings',
            'homepageContent',
            'foodReviews',
            'pendingFoodReviewsCount',
            'cancellationRules',
            'cancellationPolicy',
            'stayExtensionRequests',
            'checkedInReservations',
            'todayArrivals',
            'todayDepartures',
            'availabilityMatrix',
            'matrixDates',
            'homepageModules',
            'diningModuleStatus',
            'testimonials'
        ));
    }

    public function saveHomepageContent(Request $request)
    {
        $content = $request->input('content');
        if (!$content || !is_array($content)) {
            return response()->json(['success' => false, 'message' => 'Invalid content payload'], 422);
        }

        Setting::set('homepage_content', $content, 'homepage', 'Live visual editor homepage content');
        \Illuminate\Support\Facades\Cache::forget('homepage_payload');

        try {
            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'user_name' => auth()->user()->name ?? 'System Admin',
                'role' => auth()->user()->role ?? 'super_admin',
                'branch_id' => null,
                'action' => 'update_homepage_visual_content',
                'entity_type' => 'Homepage',
                'entity_id' => null,
                'new_values' => ['updated_at' => Carbon::now()->toDateTimeString()],
                'ip_address' => $request->ip(),
                'created_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            // Non-blocking
        }

        return response()->json([
            'success' => true,
            'message' => 'Homepage live content saved successfully! All updates are live.',
            'content' => $content
        ]);
    }

    public function resetHomepageContent(Request $request)
    {
        $default = $this->getDefaultHomepageContent();
        Setting::set('homepage_content', $default, 'homepage', 'Default visual editor homepage content');
        \Illuminate\Support\Facades\Cache::forget('homepage_payload');

        return response()->json([
            'success' => true,
            'message' => 'Homepage content reset to original brand defaults.',
            'content' => $default
        ]);
    }

    public function saveHeroSlides(Request $request)
    {
        $slides = $request->input('slides');
        if (!is_array($slides)) {
            return response()->json(['success' => false, 'message' => 'Invalid slides data'], 422);
        }

        $content = Setting::get('homepage_content', []);
        if (is_string($content)) {
            $decoded = json_decode($content, true);
            $content = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($content)) {
            $content = [];
        }
        $content['hero_slides'] = $slides;
        Setting::set('homepage_content', $content, 'homepage', 'Hero carousel slides update');
        \Illuminate\Support\Facades\Cache::forget('homepage_payload');

        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'System Admin',
                'role' => auth()->user()?->role ?? 'super_admin',
                'branch_id' => null,
                'action' => 'update_hero_carousel_slides',
                'entity_type' => 'HeroCarousel',
                'entity_id' => null,
                'new_values' => ['count' => count($slides), 'updated_at' => Carbon::now()->toDateTimeString()],
                'ip_address' => $request->ip(),
                'created_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            // Non-blocking audit log
        }

        return response()->json([
            'success' => true,
            'message' => 'Hero carousel slides saved successfully! All updates are live.',
            'slides' => $slides
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif,avif,svg|max:12288',
            'file' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif,avif,svg|max:12288',
            'images' => 'nullable|array',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,webp,gif,avif,svg|max:12288',
        ]);

        $destinationPath = public_path('uploads/media');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $uploadedUrls = [];

        // Handle single file (field name 'image' or 'file')
        $singleFile = $request->file('image') ?: $request->file('file');
        if ($singleFile) {
            $filename = 'krishna_' . time() . '_' . Str::random(8) . '.' . $singleFile->getClientOriginalExtension();
            $singleFile->move($destinationPath, $filename);
            $uploadedUrls[] = '/uploads/media/' . $filename;
        }

        // Handle multiple files (field name 'images')
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $filename = 'krishna_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $filename);
                    $uploadedUrls[] = '/uploads/media/' . $filename;
                }
            }
        }

        // Handle Base64 cropped image data from Cropper.js canvas
        if ($request->filled('image_data')) {
            $data = $request->input('image_data');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $type = 'jpg';
                }
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $filename = 'krishna_cropped_' . time() . '_' . Str::random(8) . '.' . $type;
                    file_put_contents($destinationPath . DIRECTORY_SEPARATOR . $filename, $decoded);
                    $uploadedUrls[] = '/uploads/media/' . $filename;
                }
            }
        }

        if (!empty($uploadedUrls)) {
            return response()->json([
                'success' => true,
                'url' => $uploadedUrls[0],
                'urls' => $uploadedUrls,
                'count' => count($uploadedUrls),
                'message' => count($uploadedUrls) > 1
                    ? count($uploadedUrls) . ' images uploaded successfully from your device!'
                    : 'Image uploaded successfully from your device!'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image file received.'], 400);
    }

    public function getHomepageContent(): array
    {
        $saved = Setting::get('homepage_content');
        if (is_string($saved)) {
            $decoded = json_decode($saved, true);
            if (is_array($decoded)) {
                $saved = $decoded;
            }
        }
        $default = $this->getDefaultHomepageContent();

        if (is_array($saved)) {
            $merged = array_merge($default, $saved);
            // Ensure newly added database branches are automatically included in homepage branches
            $dbBranches = Branch::where('status', 'active')->orderBy('sort_order')->get();
            $existingCardIds = collect($merged['branches'] ?? [])->pluck('branch_id')->filter()->all();
            foreach ($dbBranches as $b) {
                if (!in_array($b->id, $existingCardIds)) {
                    $merged['branches'][] = [
                        'branch_id' => $b->id,
                        'title' => $b->display_name ?: $b->name,
                        'tagline' => $b->tagline ?: ($b->city . ' · ' . $b->state),
                        'image' => $b->cover_image_url ?: ($b->hero_image_url ?: null)
                    ];
                }
            }

            // Ensure newly added database branches are automatically included in hero_slides
            $existingSlideIds = collect($merged['hero_slides'] ?? [])->pluck('id')->filter()->all();
            // If only the placeholder slide (id=1) exists and we have real branches, replace it
            if (count($merged['hero_slides'] ?? []) === 1 && ($merged['hero_slides'][0]['id'] ?? 0) === 1 && $dbBranches->isNotEmpty()) {
                $merged['hero_slides'] = [];
                $existingSlideIds = [];
            }
            $slideOrder = count($merged['hero_slides'] ?? []);
            foreach ($dbBranches as $b) {
                if (!in_array($b->id, $existingSlideIds)) {
                    $slideOrder++;
                    $merged['hero_slides'][] = [
                        'id' => $b->id,
                        'title' => $b->display_name ?: $b->name,
                        'subtitle' => $b->city ? ($b->city . ' Retreat') : 'Kerala Retreat',
                        'description' => $b->tagline ?: 'Private wooden cottages, lush gardens & tranquil verandas.',
                        'image' => $b->cover_image_url ?: ($b->hero_image_url ?: null),
                        'tag' => ($b->city ?: 'Kerala') . ' · Luxury Cottages',
                        'badge' => '★ 4.9 Rating',
                        'link' => route('rooms.index', ['branch_id' => $b->id], false),
                        'sort_order' => $slideOrder,
                        'status' => 'active',
                    ];
                }
            }
            return $merged;
        }

        return $default;
    }

    public function getDefaultHomepageContent(): array
    {
        $dbBranches = Branch::where('status', 'active')->orderBy('sort_order')->get();
        if ($dbBranches->isEmpty()) {
            $dbBranches = Branch::orderBy('sort_order')->get();
        }

        $branchCards = [];
        $heroSlides = [];
        foreach ($dbBranches as $idx => $b) {
            $branchCards[] = [
                'branch_id' => $b->id,
                'title' => $b->display_name ?: $b->name,
                'tagline' => $b->tagline ?: ($b->city . ' · ' . $b->state),
                'image' => $b->cover_image_url ?: ($b->hero_image_url ?: null)
            ];

            $heroSlides[] = [
                'id' => $b->id,
                'title' => $b->display_name ?: $b->name,
                'subtitle' => $b->city ? ($b->city . ' Retreat') : 'Kerala Retreat',
                'description' => $b->tagline ?: 'Private wooden cottages, lush gardens & tranquil verandas.',
                'image' => $b->cover_image_url ?: ($b->hero_image_url ?: null),
                'tag' => ($b->city ?: 'Kerala') . ' · ' . ($idx === 0 ? 'Hillside Cottages' : ($idx === 1 ? 'Backwater Haven' : 'Coastal Living')),
                'badge' => '★ 4.9 Rating',
                'link' => route('rooms.index', ['branch_id' => $b->id], false),
                'sort_order' => $idx + 1,
                'status' => 'active',
            ];
        }

        $branchCount = $dbBranches->count() ?: 3;

        return [
            'welcome_eyebrow' => 'Slow Living In Kerala · Est. 2024',
            'welcome_heading' => 'A Sanctuary of Hillside Serenity, Pure Mist & Handcrafted Wooden Cottages',
            'welcome_description' => 'Tucked into the misty emerald slopes of Idukki, Munnar, Rajakkadu, Rajakumari, Udumbanchola, and Adimali, Country Side Cottages was conceived as a timeless sanctuary where hurry gives way to stillness. Surrounded by towering silver oaks, organic cardamom valleys, and rolling tea estates, our private wooden residences invite you to breathe deeply and reconnect with what matters.',
            'welcome_description_2' => 'From the gentle clatter of earthenware clay pots simmering with authentic Kerala heirloom spices to quiet sunset hours on cedar verandas, every detail is considered for restorative, peaceful slow living.',
            'welcome_card_1_title' => 'Private Wooden Cottages',
            'welcome_card_1_desc' => 'Handcrafted teak & cedar residences featuring wide open verandas overlooking misty valleys.',
            'welcome_card_2_title' => 'Clay-Pot Estate Kitchen',
            'welcome_card_2_desc' => 'Authentic slow-cooked Kerala recipes made in traditional earthenware with single-origin spices.',
            'welcome_card_3_title' => 'Estate Trails & Waterfalls',
            'welcome_card_3_desc' => 'Guided morning walks through tea, cardamom, and clove groves leading to secret hillside streams.',
            'welcome_card_4_title' => '24/7 Dedicated Concierge',
            'welcome_card_4_desc' => 'Attentive in-cottage hosts, private jeep safaris, bonfire evenings, and custom excursions.',
            'welcome_quote' => 'We created Country Side Cottages as a haven where time gently pauses, hot spiced chai is poured by the rain, and mist greets you each sunrise.',
            'welcome_quote_author' => '— The Resident Hosts · Country Side Cottages, Idukki, Munnar & Rajakkadu',
            'welcome_btn_1_text' => 'Explore Accommodations',
            'welcome_btn_2_text' => 'Ask Concierge Desk',
            'welcome_img_main' => '/uploads/homepage/krishna_1788796146_NLXh47tR.jpg',
            'welcome_img_main_tag' => 'Hillside Sanctuaries',
            'welcome_img_main_title' => 'Private Verandas & Cool Mist',
            'welcome_img_inset' => '/uploads/homepage/krishna_1788796169_LMsr6Y0M.jpg',
            'welcome_img_inset_title' => 'Farm Clay-Pot Kitchen',
            'welcome_img_inset_subtitle' => 'Authentic Heirloom Spices',
            'welcome_rating_val' => '4.95 / 5',
            'welcome_rating_title' => 'Verified Stays',
            'welcome_rating_subtitle' => '500+ Guests · Rajakkad & Munnar',
            'welcome_stats_text' => '100% Organic Plantation · 24/7 Butler',
            'cottages_eyebrow' => 'ACCOMMODATIONS',
            'cottages_heading' => 'Featured Cottages & Suites',
            'cottages_description' => 'Explore our private wooden cottages and hillside suites crafted for restorative slow stays.',
            'cottages_view_all_text' => 'View All',
            'experience_heading' => 'Life at Country Side Cottages',
            'experience_description' => 'Immerse your senses in authentic Kerala dining, farm spices, and scenic hillside nature.',
            'experience_card_1_img' => '/uploads/homepage/krishna_1788796260_1mkPMtbr.jpg',
            'experience_card_1_tag' => 'CUISINE',
            'experience_card_1_title' => 'Plantation Kitchen',
            'experience_card_1_desc' => 'Clay-pot Kerala cuisine, slow-cooked curries, fresh cardamom chai, and wholesome countryside harvests served daily.',
            'experience_card_1_btn' => 'Explore Dining & Menu',
            'experience_card_2_img' => '/uploads/homepage/krishna_1788796320_VB1hdKl7.jpg',
            'experience_card_2_tag' => 'ESTATE HARVEST',
            'experience_card_2_title' => 'Country Side Spices',
            'experience_card_2_desc' => 'Experience genuine estate-cultivated cardamom, black pepper, and whole cloves delivered right to your cottage door.',
            'experience_card_2_btn' => 'Shop Estate Spices',
            'experience_card_3_img' => '/uploads/homepage/krishna_1788796146_NLXh47tR.jpg',
            'experience_card_3_tag' => 'ACTIVITIES',
            'experience_card_3_title' => 'Nature & Sights',
            'experience_card_3_desc' => 'Morning birdwatching trails, nearby Kuthumkal waterfalls, tea garden hikes, and peaceful evening campfires.',
            'experience_card_3_btn' => 'View Facilities & Trails',
            'destinations_eyebrow' => 'OUR RETREATS',
            'destinations_heading' => 'Our Sanctuaries Across Kerala',
            'destinations_description' => 'Explore our cottage locations in Idukki, Rajakkad, and Munnar hills.',
            'cta_heading' => 'Ready to unwind in the quiet hills of Kerala?',
            'cta_btn_1_text' => 'Book Your Stay',
            'cta_btn_2_text' => 'Chat with Concierge',
            'hero_eyebrow' => $branchCount . ' Kerala Retreat Destinations · One Experience',
            'hero_heading_1' => 'Come away to',
            'hero_heading_2' => 'somewhere better.',
            'hero_image_main' => null,
            'hero_image_secondary' => null,
            'hero_card_title' => 'Authentic Kerala Living',
            'hero_card_description' => 'Private wooden cottages immersed in nature & spice trails.',
            'hero_slides' => $heroSlides,
            'branch_section_eyebrow' => 'Choose your retreat',
            'branch_section_heading' => $branchCount . ' ways to stay.',
            'branches' => $branchCards,
            'stay_eyebrow' => 'Your stay',
            'stay_heading_1' => 'Comfort, calm',
            'stay_heading_2' => 'and room to breathe.',
            'stay_description' => 'Explore room categories, compare what suits your visit and continue directly into availability for the branch you choose.',
            'stay_image' => null,
            'stay_image_title' => 'Rooms for slower stays.',
            'stay_image_subtitle' => 'Room details, amenities and availability.',
            'experience_eyebrow' => 'The Country Side experience',
            'experience_heading_1' => 'More than a room.',
            'experience_heading_2' => 'A complete stay.',
            'experience_image_main' => null,
            'experience_image_local' => null,
            'experience_image_activities' => null,
            'experience_image_family' => null,
            'experience_image_events' => null,
            'experience_image_dining' => null,
            'experience_card_1_title' => 'Nature & open spaces',
            'experience_card_1_desc' => 'Green surroundings · quiet moments',
            'experience_card_2_title' => 'Local exploration',
            'experience_card_2_desc' => 'Nearby places, easy routes and branch-specific discoveries.',
            'experience_card_3_title' => 'Experiences & activities',
            'experience_card_3_desc' => 'Things to do, slow days and memorable moments around the stay.',
            'experience_card_4_title' => 'Family moments',
            'experience_card_4_desc' => 'Easy days, shared time and comfortable spaces.',
            'experience_card_5_title' => 'Events & celebrations',
            'experience_card_5_desc' => 'Gatherings, celebrations and special occasions at Country Side Cottages.',
            'experience_card_6_title' => 'Dining experiences',
            'experience_card_6_desc' => 'Restaurant, menu browsing and direct food ordering.',
            'experience_card_7_title' => 'Concierge & enquiry',
            'experience_card_7_desc' => 'Ask questions directly while planning or during your stay.',
            'experience_card_8_title' => 'Around your branch',
            'experience_card_8_desc' => 'Discover places nearby, with branch-specific suggestions.',
            'gallery_eyebrow' => 'See Country Side Cottages',
            'gallery_heading' => 'A stay you can picture.',
            'gallery_images' => [],
            'dining_eyebrow' => 'Dining',
            'dining_heading_1' => 'Good food,',
            'dining_heading_2' => 'close at hand.',
            'dining_image' => null,
            'dining_title' => 'Local table',
            'dining_description' => 'Seasonal dishes, branch menus and direct food ordering.',
            'spices_eyebrow' => 'Country Side Spices',
            'spices_heading_1' => 'Take a little',
            'spices_heading_2' => 'Country Side home.',
            'spices_pinned_product_1_id' => 1,
            'spices_pinned_product_2_id' => 2,
            'locations_eyebrow' => 'Kerala Sanctuaries',
            'locations_heading_1' => 'Our Locations',
            'locations_heading_2' => 'Visit our retreats.',
            'nearby_eyebrow' => 'Around you',
            'nearby_heading_1' => 'Discover the',
            'nearby_heading_2' => 'nearby.',
            'nearby_item_1_name' => 'River viewpoint',
            'nearby_item_1_dist' => '2.1 km',
            'nearby_item_1_tag' => 'Nature · easy access',
            'nearby_item_2_name' => 'Temple trail',
            'nearby_item_2_dist' => '4.3 km',
            'nearby_item_2_tag' => 'Culture · half day',
            'nearby_item_3_name' => 'Waterfall',
            'nearby_item_3_dist' => '8.2 km',
            'nearby_item_3_tag' => 'Nature · explore',
            'review_eyebrow' => 'Verified guest note',
            'review_quote' => '“Everything felt easy — from the room to dinner to knowing what was nearby.”',
            'review_author' => 'Ananya & Ravi',
            'review_subtitle' => 'Garden Residence · completed stay',
            'cta_eyebrow' => 'Your next Country Side moment',
            'cta_heading_1' => 'Stay for the place.',
            'cta_heading_2' => 'Remember the feeling.',
            'cta_description' => 'Choose a branch, check your dates, discover what is around you and talk directly to Country Side Cottages whenever you need.',
            'footer_brand_title' => 'Country Side Cottages',
            'footer_brand_tagline' => 'Luxury Cottages of Kerala',
            'footer_brand_desc' => 'Immersive, slow-living cottages nestled in the spice hills, misty valleys, and tranquil backwaters of Kerala.',
            'footer_badge' => 'Eco-Conscious Botanical Cottages',
            'footer_dest_title' => 'Destinations',
            'footer_exp_title' => 'Experiences',
            'footer_exp_link_1' => 'Plantation Kitchen & Dining',
            'footer_exp_link_2' => 'Country Side Spices Farm Shop',
            'footer_exp_link_3' => 'Cottages Visual Gallery',
            'footer_exp_link_4' => 'Guided Nature Discoveries',
            'footer_concierge_title' => 'Direct Concierge',
            'footer_concierge_desc' => 'Front desk assistance 24/7 for bespoke retreat arrangements.',
            'footer_phone' => '+91 484 290 0000',
            'footer_email' => 'concierge@countrysidecottages.com',
            'footer_chat_btn' => 'Chat with Concierge',
            'footer_copyright' => 'Country Side Cottages Hospitality Ltd. All rights reserved.',
            'footer_support_link' => 'Help & Support',
            'footer_policy_note' => '🌿 Strictly No Swimming Pool Policy'
        ];
    }

    /**
     * Stream CSV Export for Reservations, Food Orders, Spices, and Overall Ledger
     */
    public function exportCsv(Request $request)
    {
        $type = $request->get('type', 'reservations');
        $filename = "village_cottages_{$type}_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($type) {
            $file = fopen('php://output', 'w');

            if ($type === 'reservations' || $type === 'all') {
                fputcsv($file, [
                    'Booking Code', 'Guest Name', 'Guest Phone', 'Guest Email', 
                    'Branch', 'Room Type', 'Assigned Room', 'Check-In', 'Check-Out', 
                    'Nights', 'Adults', 'Children', 'Total Amount', 'Paid Amount', 
                    'Payment Status', 'Reservation Status', 'Booked At'
                ]);

                $reservations = Reservation::with(['guest', 'branch', 'roomType', 'room'])->latest()->get();
                foreach ($reservations as $r) {
                    $ci = is_string($r->check_in_date) ? $r->check_in_date : ($r->check_in_date ? $r->check_in_date->format('Y-m-d') : '');
                    $co = is_string($r->check_out_date) ? $r->check_out_date : ($r->check_out_date ? $r->check_out_date->format('Y-m-d') : '');
                    fputcsv($file, [
                        $r->booking_code,
                        $r->guest ? $r->guest->full_name : 'Guest',
                        $r->guest ? $r->guest->phone : '',
                        $r->guest ? $r->guest->email : '',
                        $r->branch ? $r->branch->name : '',
                        $r->roomType ? $r->roomType->name : '',
                        $r->room ? $r->room->room_number : 'Unassigned',
                        $ci,
                        $co,
                        $r->nights_count ?? 1,
                        $r->adults ?? 1,
                        $r->children ?? 0,
                        $r->total_amount,
                        $r->paid_amount ?? 0,
                        $r->payment_status,
                        $r->status,
                        $r->created_at ? $r->created_at->format('Y-m-d H:i') : '',
                    ]);
                }
            } elseif ($type === 'dining' || $type === 'food_orders') {
                fputcsv($file, [
                    'Order #', 'Customer', 'Phone', 'Branch', 'Table / Villa', 
                    'Items Count', 'Subtotal', 'Tax', 'Total Amount', 'Status', 'Ordered At'
                ]);
                $orders = FoodOrder::with(['branch', 'items'])->latest()->get();
                foreach ($orders as $fo) {
                    fputcsv($file, [
                        $fo->order_number,
                        $fo->customer_name,
                        $fo->customer_phone,
                        $fo->branch ? $fo->branch->name : '',
                        $fo->table_number ?? 'In-Villa',
                        $fo->items->count(),
                        $fo->subtotal,
                        $fo->tax_amount,
                        $fo->total_amount,
                        $fo->status,
                        $fo->ordered_at ? $fo->ordered_at->format('Y-m-d H:i') : '',
                    ]);
                }
            } elseif ($type === 'spices') {
                fputcsv($file, [
                    'Order #', 'Customer', 'Phone', 'City', 'Pincode', 
                    'Courier Partner', 'Tracking #', 'Total Amount', 'Payment Status', 'Status', 'Ordered At'
                ]);
                $orders = SpiceOrder::latest()->get();
                foreach ($orders as $so) {
                    fputcsv($file, [
                        $so->order_number,
                        $so->customer_name,
                        $so->customer_phone,
                        $so->shipping_city,
                        $so->shipping_pincode,
                        $so->shipping_courier,
                        $so->tracking_number,
                        $so->total_amount,
                        $so->payment_status,
                        $so->status,
                        $so->created_at ? $so->created_at->format('Y-m-d H:i') : '',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
