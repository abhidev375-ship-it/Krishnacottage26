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
        $pendingSpiceReturnsCount = SpiceOrder::where('refund_status', 'requested')->count();

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
        $spiceOrders = SpiceOrder::with(['guest', 'items', 'returnRule'])->orderByDesc('created_at')->get();
        $spiceReturnRules = \App\Models\SpiceReturnRule::orderBy('sort_order')->get();
        $spiceReturnOrders = SpiceOrder::with(['items', 'returnRule'])->where('refund_status', '!=', 'none')->orderByDesc('updated_at')->get();
        $spiceReturnPolicyDescription = Setting::get(
            'spice_return_policy_description',
            'All Krishna Spices are harvested and vacuum-ground on-demand after receiving your order to guarantee plantation freshness. You can cancel your order with a 100% full refund at any time before dispatch. Unopened, factory-sealed spice packs can be returned within 7 days of delivery under our 90% freshness guarantee.'
        );
        $globalSpiceReturnsEnabled = (bool) Setting::get('spice_returns_enabled', true);
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
        $cancellationPolicy = Setting::get('cancellation_policy_description', 'At Krishna Cottages, cancellations made 72+ hours prior receive 100% cashback. 48-72h prior receive 75%, and 24-48h prior receive 50%. Instant automated payback.');
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
        $homepageModules = [
            'branches' => Setting::get('homepage_module_branches', '1') == '1',
            'categories' => Setting::get('homepage_module_categories', '1') == '1',
            'facilities' => Setting::get('homepage_module_facilities', '1') == '1',
            'dining' => Setting::get('homepage_module_dining', '1') == '1',
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
            'pendingSpiceReturnsCount',
            'spiceReturnRules',
            'spiceReturnOrders',
            'spiceReturnPolicyDescription',
            'globalSpiceReturnsEnabled',
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
        $content['hero_slides'] = $slides;
        Setting::set('homepage_content', $content, 'homepage', 'Hero carousel slides update');
        \Illuminate\Support\Facades\Cache::forget('homepage_payload');

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'update_hero_carousel_slides',
            'entity_type' => 'HeroCarousel',
            'entity_id' => null,
            'new_values' => ['count' => count($slides), 'updated_at' => Carbon::now()->toDateTimeString()],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

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
                        'image' => $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1100&q=85')
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
                'image' => $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1100&q=85')
            ];

            $heroSlides[] = [
                'id' => $b->id,
                'title' => $b->display_name ?: $b->name,
                'subtitle' => $b->city ? ($b->city . ' Retreat') : 'Kerala Retreat',
                'description' => $b->tagline ?: 'Private wooden cottages, lush gardens & tranquil verandas.',
                'image' => $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1400&q=85'),
                'tag' => ($b->city ?: 'Kerala') . ' · ' . ($idx === 0 ? 'Hillside Cottages' : ($idx === 1 ? 'Backwater Haven' : 'Coastal Living')),
                'badge' => '★ 4.9 Rating',
                'link' => route('rooms.index', ['branch_id' => $b->id], false),
                'sort_order' => $idx + 1,
                'status' => 'active',
            ];
        }

        $branchCount = $dbBranches->count() ?: 3;

        return [
            'hero_eyebrow' => $branchCount . ' Kerala Retreat Destinations · One Experience',
            'hero_heading_1' => 'Come away to',
            'hero_heading_2' => 'somewhere better.',
            'hero_description' => 'Stay slow. Eat well. Explore more. Krishna brings private wooden cottages, authentic plantation dining, and estate-harvested spices into one considered Kerala journey.',
            'hero_image_main' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1400&q=85',
            'hero_image_secondary' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=700&q=85',
            'hero_card_title' => 'Authentic Kerala Living',
            'hero_card_description' => 'Private wooden cottages immersed in nature & spice trails.',
            'hero_slides' => $heroSlides,
            'branch_section_eyebrow' => 'Choose your Krishna',
            'branch_section_heading' => $branchCount . ' ways to stay.',
            'branches' => $branchCards,
            'stay_eyebrow' => 'Your stay',
            'stay_heading_1' => 'Comfort, calm',
            'stay_heading_2' => 'and room to breathe.',
            'stay_description' => 'Explore room categories, compare what suits your visit and continue directly into availability for the branch you choose.',
            'stay_image' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1200&q=85',
            'stay_image_title' => 'Rooms for slower stays.',
            'stay_image_subtitle' => 'Room details, amenities and availability.',
            'experience_eyebrow' => 'The Krishna experience',
            'experience_heading_1' => 'More than a room.',
            'experience_heading_2' => 'A complete stay.',
            'experience_image_main' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=1000&q=85',
            'experience_image_local' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=900&q=85',
            'experience_image_activities' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=900&q=85',
            'experience_image_family' => 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?auto=format&fit=crop&w=900&q=85',
            'experience_image_events' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=900&q=85',
            'experience_image_dining' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=900&q=85',
            'experience_card_1_title' => 'Nature & open spaces',
            'experience_card_1_desc' => 'Green surroundings · quiet moments',
            'experience_card_2_title' => 'Local exploration',
            'experience_card_2_desc' => 'Nearby places, easy routes and branch-specific discoveries.',
            'experience_card_3_title' => 'Experiences & activities',
            'experience_card_3_desc' => 'Things to do, slow days and memorable moments around the stay.',
            'experience_card_4_title' => 'Family moments',
            'experience_card_4_desc' => 'Easy days, shared time and comfortable spaces.',
            'experience_card_5_title' => 'Events & celebrations',
            'experience_card_5_desc' => 'Gatherings, celebrations and special occasions at Krishna.',
            'experience_card_6_title' => 'Dining experiences',
            'experience_card_6_desc' => 'Restaurant, menu browsing and direct food ordering.',
            'experience_card_7_title' => 'Concierge & enquiry',
            'experience_card_7_desc' => 'Ask questions directly while planning or during your stay.',
            'experience_card_8_title' => 'Around your branch',
            'experience_card_8_desc' => 'Discover places nearby, with branch-specific suggestions.',
            'gallery_eyebrow' => 'See Krishna',
            'gallery_heading' => 'A stay you can picture.',
            'gallery_images' => [
                [
                    'url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=85',
                    'tag' => 'Rooms'
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=900&q=85',
                    'tag' => 'Nature'
                ],
                [
                    'url' => 'https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?auto=format&fit=crop&w=1000&q=85',
                    'tag' => 'Dining'
                ]
            ],
            'dining_eyebrow' => 'Dining',
            'dining_heading_1' => 'Good food,',
            'dining_heading_2' => 'close at hand.',
            'dining_image' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=800&q=85',
            'dining_title' => 'Local table',
            'dining_description' => 'Seasonal dishes, branch menus and direct food ordering.',
            'spices_eyebrow' => 'Krishna Spices',
            'spices_heading_1' => 'Take a little',
            'spices_heading_2' => 'Krishna home.',
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
            'cta_eyebrow' => 'Your next Krishna moment',
            'cta_heading_1' => 'Stay for the place.',
            'cta_heading_2' => 'Remember the feeling.',
            'cta_description' => 'Choose a branch, check your dates, discover what is around you and talk directly to Krishna whenever you need.',
            'footer_brand_title' => 'Krishna Cottages',
            'footer_brand_tagline' => 'Luxury Cottages of Kerala',
            'footer_brand_desc' => 'Immersive, slow-living cottages nestled in the spice hills, misty valleys, and tranquil backwaters of Kerala.',
            'footer_badge' => 'Eco-Conscious Botanical Cottages',
            'footer_dest_title' => 'Destinations',
            'footer_exp_title' => 'Experiences',
            'footer_exp_link_1' => 'Plantation Kitchen & Dining',
            'footer_exp_link_2' => 'Krishna Spices Farm Shop',
            'footer_exp_link_3' => 'Cottages Visual Gallery',
            'footer_exp_link_4' => 'Guided Nature Discoveries',
            'footer_concierge_title' => 'Direct Concierge',
            'footer_concierge_desc' => 'Front desk assistance 24/7 for bespoke retreat arrangements.',
            'footer_phone' => '+91 484 290 0000',
            'footer_email' => 'concierge@krishnacottages.com',
            'footer_chat_btn' => 'Chat with Concierge',
            'footer_copyright' => 'Krishna Cottages Hospitality Ltd. All rights reserved.',
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
        $filename = "krishna_cottages_{$type}_" . date('Ymd_His') . ".csv";

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
