<?php

use App\Http\Controllers\Admin\AdminActionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CancellationRuleController;
use App\Http\Controllers\Admin\CustomerActionController;
use App\Http\Controllers\Admin\DiningActionController;
use App\Http\Controllers\Admin\InHouseGuestController;
use App\Http\Controllers\Admin\ReservationActionController;
use App\Http\Controllers\Admin\RoomActionController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SpiceActionController;
use App\Http\Controllers\Admin\SpiceReturnRuleController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\ChatController;
use App\Http\Controllers\Client\ClientDiningController;
use App\Http\Controllers\Client\ClientPageController;
use App\Http\Controllers\Client\ClientSpiceController;
use App\Http\Controllers\Client\CustomerPortalController;
use App\Http\Controllers\Client\StayExtensionController;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Models\Branch;
use App\Models\SpiceProduct;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client-Facing Web Routes (Customer Platform)
|--------------------------------------------------------------------------
*/

// Homepage (Cached for sub-3-second high performance)
Route::get('/', function () {
    $data = \Illuminate\Support\Facades\Cache::remember('homepage_payload', now()->addMinutes(15), function () {
        $adminController = app(AdminController::class);
        $branches = Branch::where('status', 'active')->orderBy('sort_order')->get();
        return [
            'homepageContent' => $adminController->getHomepageContent(),
            'branches' => $branches,
            'branchesWeather' => app(\App\Services\WeatherService::class)->getAllBranchesWeather($branches),
            'spiceProducts' => SpiceProduct::active()->get(),
            'galleryAlbums' => \App\Models\GalleryAlbum::with(['images' => function ($q) {
                $q->orderBy('sort_order');
            }])->where('is_published', true)->orderBy('sort_order')->take(6)->get(),
            'testimonials' => Testimonial::with('branch')->where('is_active', true)->orderBy('sort_order')->get(),
            'featuredRooms' => \App\Models\RoomType::with(['branch', 'category', 'amenitiesList'])->where('is_active', true)->where('is_bookable', true)->orderBy('sort_order')->take(8)->get(),
        ];
    });

    return view('welcome', $data);
})->name('home');

// Airbnb-Style Room Booking Engine
Route::get('/stay', [BookingController::class, 'index'])->name('rooms.index');
Route::get('/rooms', [BookingController::class, 'index']);
Route::get('/rooms/{slug}', [BookingController::class, 'show'])->name('rooms.show');
Route::get('/book/{id}', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::post('/book', [BookingController::class, 'store'])->name('booking.store');
Route::post('/booking/razorpay/create-order', [BookingController::class, 'createRazorpayOrder'])->name('booking.razorpay.create-order');
Route::get('/booking/confirmation/{code}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::post('/reviews', [BookingController::class, 'storeReview'])->name('reviews.store');
Route::match(['get', 'post'], '/api/availability/check', [BookingController::class, 'checkAvailability'])->name('api.availability.check');
Route::get('/api/weather/branches', function (\App\Services\WeatherService $weatherService) {
    $branches = \App\Models\Branch::where('status', 'active')->orderBy('sort_order')->get();
    return response()->json([
        'success' => true,
        'count' => $branches->count(),
        'weather' => array_values($weatherService->getAllBranchesWeather($branches)),
    ]);
})->name('api.weather.branches');

// Resort Facilities & Experiences
Route::get('/facilities', [ClientPageController::class, 'facilities'])->name('facilities.index');

// Dining Menu & Digital In-House Ordering
Route::get('/dining', [ClientDiningController::class, 'index'])->name('dining.index');
Route::post('/dining/order', [ClientDiningController::class, 'placeOrder'])->name('dining.order');

// Krishna Spices Shop & Checkout
Route::get('/spices', [ClientSpiceController::class, 'index'])->name('spices.index');
Route::get('/spices/checkout', [ClientSpiceController::class, 'checkout'])->name('spices.checkout');
Route::post('/spices/order', [ClientSpiceController::class, 'placeOrder'])->name('spices.order');
Route::post('/spices/razorpay/create-order', [ClientSpiceController::class, 'createRazorpayOrder'])->name('spices.razorpay.create-order');

// Razorpay Automated Webhook Handler
Route::post('/webhooks/razorpay', [\App\Http\Controllers\Payment\RazorpayWebhookController::class, 'handle'])->name('webhooks.razorpay');

// Resort Photo Gallery
Route::get('/gallery', [ClientPageController::class, 'gallery'])->name('gallery.index');

// Nearby Discoveries & Attractions
Route::get('/nearby', [ClientPageController::class, 'nearby'])->name('nearby.index');

// Contact & Concierge Enquiry
Route::get('/contact', [ClientPageController::class, 'contact'])->name('contact.index');
Route::post('/contact', [ClientPageController::class, 'submitContact'])->name('contact.submit');

// Dynamic XML Sitemap for Google & Search Engine Indexing
Route::get('/sitemap.xml', function () {
    $baseUrl = url('/');
    $rooms = \App\Models\Room::where('operational_status', 'available')->orWhereNull('operational_status')->get();
    $branches = \App\Models\Branch::where('status', 'active')->get();

    $urls = [
        ['loc' => $baseUrl, 'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => $baseUrl . '/stay', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['loc' => $baseUrl . '/dining', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => $baseUrl . '/spices', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => $baseUrl . '/facilities', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => $baseUrl . '/gallery', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => $baseUrl . '/nearby', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => $baseUrl . '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
    ];

    foreach ($branches as $branch) {
        $urls[] = [
            'loc' => $baseUrl . '/stay?branch_id=' . $branch->id,
            'priority' => '0.85',
            'changefreq' => 'daily'
        ];
    }

    foreach ($rooms as $room) {
        if (!empty($room->slug)) {
            $urls[] = [
                'loc' => $baseUrl . '/rooms/' . $room->slug,
                'priority' => '0.8',
                'changefreq' => 'weekly'
            ];
        }
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $u) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
        $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
        $xml .= "  </url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// Client-Facing Direct Concierge Messaging
Route::get('/chat/init', [ChatController::class, 'init'])->name('chat.init');
Route::get('/chat/messages/{enquiryId}', [ChatController::class, 'messages'])->name('chat.messages');
Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

// Customer Authentication (Phone & Email Login / Register / Google OAuth)
Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
Route::get('/auth/google', [CustomerAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [CustomerAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Customer Dashboard & Guest Portal (Requires Authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [CustomerPortalController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/account', [CustomerPortalController::class, 'dashboard'])->name('customer.account');
    Route::post('/dashboard/extend-stay', [CustomerPortalController::class, 'extendStay'])->name('customer.extend-stay');
    Route::post('/dashboard/quick-order-dining', [CustomerPortalController::class, 'quickOrderDining'])->name('customer.quick-order-dining');
    Route::post('/dashboard/quick-order-spices', [CustomerPortalController::class, 'quickOrderSpices'])->name('customer.quick-order-spices');
    Route::post('/dashboard/food-orders/{id}/review', [CustomerPortalController::class, 'submitFoodOrderReview'])->name('customer.food-order.review');

    // Dynamic Cancellation & Automated Cashback Routes
    Route::post('/dashboard/reservations/{id}/cancel-preview', [ReservationActionController::class, 'previewCancellation'])->name('customer.reservation.cancel-preview');
    Route::post('/dashboard/reservations/{id}/cancel', [ReservationActionController::class, 'cancel'])->name('customer.reservation.cancel');

    // Stay Extension & Smart Room Re-Allocation Routes
    Route::post('/dashboard/reservations/{id}/check-extension', [StayExtensionController::class, 'checkExtensionAvailability'])->name('customer.reservation.check-extension');
    Route::post('/dashboard/reservations/{id}/request-extension', [StayExtensionController::class, 'submitExtensionRequest'])->name('customer.reservation.request-extension');
    Route::post('/dashboard/stay-extensions/{id}/pay-online', [StayExtensionController::class, 'clientPayOnline'])->name('customer.stay-extension.pay-online');

    // In-House Facility & Experience Bookings
    Route::post('/dashboard/facilities/book', [CustomerPortalController::class, 'bookFacility'])->name('customer.facilities.book');
    Route::post('/dashboard/facilities/{id}/cancel', [CustomerPortalController::class, 'cancelFacilityBooking'])->name('customer.facilities.cancel');

    // In-House Excursions & Taxi Cab Bookings
    Route::post('/dashboard/taxi/book', [CustomerPortalController::class, 'bookTaxi'])->name('customer.taxi.book');
    Route::post('/dashboard/taxi/{id}/cancel', [CustomerPortalController::class, 'cancelTaxi'])->name('customer.taxi.cancel');

    // Customer Spice Return & Cancellation Routes
    Route::post('/dashboard/spices/orders/{id}/return-preview', [ClientSpiceController::class, 'previewReturn'])->name('customer.spices.return-preview');
    Route::post('/dashboard/spices/orders/{id}/request-return', [ClientSpiceController::class, 'requestReturn'])->name('customer.spices.request-return');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Krishna Resorts Management)
|--------------------------------------------------------------------------
*/

// Admin Authentication Routes (Publicly accessible)
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected Admin Operations Group
Route::prefix('admin')->middleware(AdminAuthMiddleware::class)->group(function () {
    // Live Visual Content Editor API
    Route::post('/api/homepage-content', [AdminController::class, 'saveHomepageContent'])->name('admin.homepage.save');
    Route::post('/api/homepage-content/reset', [AdminController::class, 'resetHomepageContent'])->name('admin.homepage.reset');
    Route::post('/api/hero-slides', [AdminController::class, 'saveHeroSlides'])->name('admin.hero_slides.save');
    Route::post('/api/upload-image', [AdminController::class, 'uploadImage'])->name('admin.image.upload');

    // Reservation Operational Actions
    Route::get('/reservations/available-rooms-for-dates', [ReservationActionController::class, 'getAvailableRoomsForDates'])->name('admin.reservations.available-rooms');
    Route::post('/reservations', [ReservationActionController::class, 'store'])->name('admin.reservations.store');
    Route::post('/reservations/{id}/check-in', [ReservationActionController::class, 'checkIn'])->name('admin.reservations.checkin');
    Route::post('/reservations/{id}/check-out', [ReservationActionController::class, 'checkOut'])->name('admin.reservations.checkout');
    Route::post('/reservations/{id}/cancel-preview', [ReservationActionController::class, 'previewCancellation'])->name('admin.reservations.cancel-preview');
    Route::post('/reservations/{id}/cancel', [ReservationActionController::class, 'cancel'])->name('admin.reservations.cancel');
    Route::post('/reservations/{id}/assign-room', [ReservationActionController::class, 'assignRoom'])->name('admin.reservations.assign-room');

    // Centralised Checked-In Clients & Individual Guest 360 Console Routes
    Route::get('/in-house', [InHouseGuestController::class, 'index'])->name('admin.in-house.index');
    Route::get('/in-house/{id}/details', [InHouseGuestController::class, 'show'])->name('admin.in-house.show');
    Route::post('/in-house/{id}/folio-charge', [InHouseGuestController::class, 'addFolioCharge'])->name('admin.in-house.folio-charge');
    Route::post('/in-house/{id}/record-payment', [InHouseGuestController::class, 'recordFolioPayment'])->name('admin.in-house.record-payment');
    Route::post('/in-house/{id}/send-message', [InHouseGuestController::class, 'sendMessage'])->name('admin.in-house.send-message');
    Route::post('/in-house/{id}/service-ticket', [InHouseGuestController::class, 'createServiceTicket'])->name('admin.in-house.service-ticket');
    Route::post('/in-house/tickets/{id}/status', [InHouseGuestController::class, 'updateTicketStatus'])->name('admin.in-house.ticket-status');
    Route::post('/in-house/{id}/express-checkout', [InHouseGuestController::class, 'expressCheckOut'])->name('admin.in-house.express-checkout');

    // Dynamic Cancellation Rules & Automated Payback Engine
    Route::post('/cancellation-rules', [CancellationRuleController::class, 'store'])->name('admin.cancellation-rules.store');
    Route::post('/cancellation-rules/{id}/update', [CancellationRuleController::class, 'update'])->name('admin.cancellation-rules.update');
    Route::delete('/cancellation-rules/{id}', [CancellationRuleController::class, 'destroy'])->name('admin.cancellation-rules.destroy');
    Route::post('/settings/cancellation-policy', [CancellationRuleController::class, 'updatePolicyDescription'])->name('admin.settings.cancellation-policy');

    // Stay Extension Management & On-Hand/Checkout Payments
    Route::post('/stay-extensions/{id}/offer', [StayExtensionController::class, 'offerExtensionDiscount'])->name('admin.stay-extensions.offer');
    Route::post('/stay-extensions/{id}/record-payment', [StayExtensionController::class, 'recordExtensionPayment'])->name('admin.stay-extensions.record-payment');

    // Room Operational Actions
    Route::post('/rooms/{id}/status', [RoomActionController::class, 'updateStatus'])->name('admin.rooms.status');
    Route::post('/rooms/{id}/block', [RoomActionController::class, 'blockRoom'])->name('admin.rooms.block');

    // Dining Operations
    Route::post('/dining/items/{id}/toggle-stock', [DiningActionController::class, 'toggleStock'])->name('admin.dining.toggle-stock');
    Route::post('/dining/items/{id}/toggle-daily', [DiningActionController::class, 'toggleDailyAvailability'])->name('admin.dining.toggle-daily');
    Route::post('/dining/orders/{id}/status', [DiningActionController::class, 'updateOrderStatus'])->name('admin.dining.order-status');
    Route::post('/food-reviews/{id}/approve', [DiningActionController::class, 'approveFoodReview'])->name('admin.food-reviews.approve');
    Route::post('/food-reviews/{id}/reject', [DiningActionController::class, 'rejectFoodReview'])->name('admin.food-reviews.reject');

    // Spice Shop Operations & Fresh Pack-on-Order Engine
    Route::post('/spices/inventory/{id}/adjust', [SpiceActionController::class, 'adjustStock'])->name('admin.spices.adjust-stock');
    Route::post('/spices/orders/{id}/status', [SpiceActionController::class, 'updateOrderStatus'])->name('admin.spices.order-status');

    // Spice Return & Cancellation Policy Rules (Mirroring room cancellation)
    Route::post('/spices/return-rules', [SpiceReturnRuleController::class, 'store'])->name('admin.spices.return-rules.store');
    Route::post('/spices/return-rules/{id}/update', [SpiceReturnRuleController::class, 'update'])->name('admin.spices.return-rules.update');
    Route::delete('/spices/return-rules/{id}', [SpiceReturnRuleController::class, 'destroy'])->name('admin.spices.return-rules.destroy');
    Route::post('/spices/return-rules/{id}/toggle', [SpiceReturnRuleController::class, 'toggleActive'])->name('admin.spices.return-rules.toggle');
    Route::post('/settings/spice-return-policy', [SpiceReturnRuleController::class, 'updatePolicyDescription'])->name('admin.settings.spice-return-policy');
    Route::post('/spices/orders/{id}/calculate-return', [SpiceReturnRuleController::class, 'calculatePreview'])->name('admin.spices.orders.calculate-return');
    Route::post('/spices/orders/{id}/process-return', [SpiceReturnRuleController::class, 'processOrderReturn'])->name('admin.spices.orders.process-return');

    // Customer Communications & Direct Messaging Platform
    Route::post('/enquiries/{id}/reply', [CustomerActionController::class, 'sendMessage'])->name('admin.enquiries.reply');
    Route::post('/reviews/{id}/moderate', [CustomerActionController::class, 'moderateReview'])->name('admin.reviews.moderate');

    // Testimonials & Review Promotion Engine (Dynamic Carousel Management)
    Route::post('/testimonials', [TestimonialController::class, 'store'])->name('admin.testimonials.store');
    Route::post('/testimonials/reorder', [TestimonialController::class, 'reorder'])->name('admin.testimonials.reorder');
    Route::post('/testimonials/promote-review/{id}', [TestimonialController::class, 'promoteFromReview'])->name('admin.testimonials.promote-review');
    Route::post('/testimonials/{id}/update', [TestimonialController::class, 'update'])->name('admin.testimonials.update');
    Route::post('/testimonials/{id}/toggle', [TestimonialController::class, 'toggle'])->name('admin.testimonials.toggle');
    Route::delete('/testimonials/{id}', [TestimonialController::class, 'destroy'])->name('admin.testimonials.destroy');
    Route::post('/chat/{id}/claim', [CustomerActionController::class, 'claimChat'])->name('admin.chat.claim');
    Route::post('/chat/{id}/relieve', [CustomerActionController::class, 'relieveChat'])->name('admin.chat.relieve');
    Route::post('/chat/{id}/force-unlock', [CustomerActionController::class, 'forceUnlockChat'])->name('admin.chat.force-unlock');
    Route::get('/chat/{id}/data', [CustomerActionController::class, 'getChatData'])->name('admin.chat.data');
    Route::post('/chat/{id}/send', [CustomerActionController::class, 'sendMessage'])->name('admin.chat.send');
    Route::get('/chat/templates', [CustomerActionController::class, 'getTemplates'])->name('admin.chat.templates');
    Route::post('/chat/templates', [CustomerActionController::class, 'storeTemplate'])->name('admin.chat.templates.store');
    Route::get('/chat/threads', [CustomerActionController::class, 'getThreads'])->name('admin.chat.threads');

    // Global Search
    Route::get('/api/search', [SearchController::class, 'search'])->name('admin.search');

    // Complete Wiring - Entities CRUD & Management (AdminActionController)
    Route::post('/branches', [AdminActionController::class, 'storeBranch'])->name('admin.branches.store');
    Route::post('/branches/{id}', [AdminActionController::class, 'updateBranch'])->name('admin.branches.update');
    Route::post('/branches/{id}/toggle-status', [AdminActionController::class, 'toggleBranchStatus'])->name('admin.branches.toggle-status');

    Route::post('/room-types', [AdminActionController::class, 'storeRoomType'])->name('admin.room-types.store');
    Route::post('/room-types/{id}', [AdminActionController::class, 'updateRoomType'])->name('admin.room-types.update');
    Route::post('/rooms', [AdminActionController::class, 'storeRoom'])->name('admin.rooms.store');

    Route::post('/room-categories', [AdminActionController::class, 'storeRoomCategory'])->name('admin.room-categories.store');
    Route::post('/room-categories/{id}', [AdminActionController::class, 'updateRoomCategory'])->name('admin.room-categories.update');
    Route::delete('/room-categories/{id}', [AdminActionController::class, 'deleteRoomCategory'])->name('admin.room-categories.delete');

    Route::post('/amenities', [AdminActionController::class, 'storeAmenity'])->name('admin.amenities.store');
    Route::post('/amenities/{id}', [AdminActionController::class, 'updateAmenity'])->name('admin.amenities.update');
    Route::delete('/amenities/{id}', [AdminActionController::class, 'deleteAmenity'])->name('admin.amenities.delete');

    Route::post('/dining/items', [AdminActionController::class, 'storeMenuItem'])->name('admin.dining.items.store');
    Route::post('/dining/items/{id}', [AdminActionController::class, 'updateMenuItem'])->name('admin.dining.items.update');
    Route::delete('/dining/items/{id}', [AdminActionController::class, 'deleteMenuItem'])->name('admin.dining.items.delete');
    Route::post('/dining/categories', [AdminActionController::class, 'storeMenuCategory'])->name('admin.dining.categories.store');
    Route::post('/dining/categories/{id}', [AdminActionController::class, 'updateMenuCategory'])->name('admin.dining.categories.update');
    Route::delete('/dining/categories/{id}', [AdminActionController::class, 'deleteMenuCategory'])->name('admin.dining.categories.delete');

    Route::post('/spices/products', [AdminActionController::class, 'storeSpiceProduct'])->name('admin.spices.products.store');
    Route::post('/spices/products/{id}', [AdminActionController::class, 'updateSpiceProduct'])->name('admin.spices.products.update');
    Route::delete('/spices/products/{id}', [AdminActionController::class, 'deleteSpiceProduct'])->name('admin.spices.products.delete');
    Route::post('/spices/products/{id}/toggle-availability', [SpiceActionController::class, 'toggleAvailability'])->name('admin.spices.products.toggle-availability');
    Route::post('/spices/toggle-global-returns', [SpiceActionController::class, 'toggleGlobalReturns'])->name('admin.spices.toggle-global-returns');
    Route::post('/spices/products/{id}/toggle-return', [SpiceActionController::class, 'toggleProductReturn'])->name('admin.spices.products.toggle-return');

    Route::post('/facilities', [AdminActionController::class, 'storeFacility'])->name('admin.facilities.store');
    Route::post('/facilities/bookings', [AdminActionController::class, 'storeFacilityBookingAdmin'])->name('admin.facilities.bookings.store');
    Route::post('/facilities/bookings/{id}/allocate', [AdminActionController::class, 'allocateFacilityTimeSlot'])->name('admin.facilities.bookings.allocate');
    Route::post('/facilities/bookings/{id}/status', [AdminActionController::class, 'updateFacilityBookingStatus'])->name('admin.facilities.bookings.status');

    Route::post('/nearby', [AdminActionController::class, 'storeNearbyLocation'])->name('admin.nearby.store');
    Route::post('/nearby/{id}', [AdminActionController::class, 'updateNearbyLocation'])->name('admin.nearby.update');
    Route::post('/nearby/{id}/toggle-availability', [AdminActionController::class, 'toggleNearbyAvailability'])->name('admin.nearby.toggle-availability');
    Route::post('/nearby/{id}/toggle-taxi', [AdminActionController::class, 'toggleNearbyTaxi'])->name('admin.nearby.toggle-taxi');
    Route::delete('/nearby/{id}', [AdminActionController::class, 'deleteNearbyLocation'])->name('admin.nearby.delete');

    // Taxi & Excursion Operational Actions
    Route::post('/taxi-requests/{id}/status', [AdminActionController::class, 'updateTaxiRequestStatus'])->name('admin.taxi-requests.status');
    Route::post('/taxi-requests/{id}/quote', [AdminActionController::class, 'quoteTaxiRequestFare'])->name('admin.taxi-requests.quote');

    // Quick Operational Toggles
    Route::post('/rooms/{id}/toggle-housekeeping', [AdminActionController::class, 'quickToggleHousekeeping'])->name('admin.rooms.toggle-housekeeping');

    // Reports CSV Export
    Route::get('/reports/export-csv', [AdminController::class, 'exportCsv'])->name('admin.reports.export-csv');

    // CMS Pages Management
    Route::post('/cms/pages', [AdminActionController::class, 'storePage'])->name('admin.cms.pages.store');
    Route::post('/cms/pages/{id}', [AdminActionController::class, 'updatePage'])->name('admin.cms.pages.update');
    Route::delete('/cms/pages/{id}', [AdminActionController::class, 'deletePage'])->name('admin.cms.pages.delete');
    Route::post('/cms/pages/{id}/toggle', [AdminActionController::class, 'togglePageStatus'])->name('admin.cms.pages.toggle');

    // CMS Navigation Menu Tree
    Route::post('/cms/navigation', [AdminActionController::class, 'storeNavigationItem'])->name('admin.cms.navigation.store');
    Route::post('/cms/navigation/{id}', [AdminActionController::class, 'updateNavigationItem'])->name('admin.cms.navigation.update');
    Route::delete('/cms/navigation/{id}', [AdminActionController::class, 'deleteNavigationItem'])->name('admin.cms.navigation.delete');

    // Homepage Featured Modules Toggles
    Route::post('/cms/homepage-modules', [AdminActionController::class, 'toggleHomepageModule'])->name('admin.cms.homepage-modules');

    Route::post('/gallery/albums', [AdminActionController::class, 'storeGalleryAlbum'])->name('admin.gallery.albums.store');
    Route::post('/gallery/albums/{id}/update', [AdminActionController::class, 'updateGalleryAlbum'])->name('admin.gallery.albums.update');
    Route::delete('/gallery/albums/{id}', [AdminActionController::class, 'deleteGalleryAlbum'])->name('admin.gallery.albums.delete');
    Route::get('/gallery/albums/{id}/photos', [AdminActionController::class, 'getAlbumPhotos'])->name('admin.gallery.albums.photos');
    Route::post('/gallery/albums/{id}/photos', [AdminActionController::class, 'uploadAlbumPhotos'])->name('admin.gallery.albums.photos.upload');
    Route::delete('/gallery/albums/{id}/photos/{photoId}', [AdminActionController::class, 'deleteAlbumPhoto'])->name('admin.gallery.albums.photos.delete');
    Route::post('/settings', [AdminActionController::class, 'updateSettings'])->name('admin.settings.update');
    Route::post('/settings/razorpay', [AdminActionController::class, 'updateRazorpaySettings'])->name('admin.settings.razorpay');
    Route::post('/settings/razorpay/test', [AdminActionController::class, 'testRazorpayConnection'])->name('admin.settings.razorpay.test');
    Route::get('/api/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'getNotificationsFeed'])->name('admin.api.notifications');
    Route::post('/api/notifications/mark-read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllRead'])->name('admin.api.notifications.mark-read');
    Route::post('/settings/whatsapp', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'saveWhatsAppSettings'])->name('admin.settings.whatsapp');
    Route::post('/settings/whatsapp/test', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'testWhatsApp'])->name('admin.settings.whatsapp.test');
    Route::post('/staff', [AdminActionController::class, 'storeStaff'])->name('admin.staff.store');
    Route::post('/staff/{id}', [AdminActionController::class, 'updateStaff'])->name('admin.staff.update');
    Route::delete('/staff/{id}', [AdminActionController::class, 'deleteStaff'])->name('admin.staff.delete');

    // Homepage Live Visual Content & Hero Slides Engine
    Route::post('/api/homepage-content', [AdminController::class, 'saveHomepageContent'])->name('admin.api.homepage-content');
    Route::post('/api/homepage-content/reset', [AdminController::class, 'resetHomepageContent'])->name('admin.api.homepage-content.reset');
    Route::post('/api/hero-slides', [AdminController::class, 'saveHeroSlides'])->name('admin.api.hero-slides');
    Route::post('/api/upload-image', [AdminController::class, 'uploadImage'])->name('admin.api.upload-image');

    // Main admin dashboard and all sections (Must remain after explicit routes)
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/{section}', [AdminController::class, 'index'])->name('admin.section');
});
