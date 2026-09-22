<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krishna Cottages — Administration & Operations</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Custom Brand Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    zIndex: {
                        '60': '60',
                        '70': '70',
                        '80': '80',
                        '90': '90',
                        '100': '100',
                    },
                    colors: {
                        brand: {
                            deep: '#063F34',
                            primary: '#0B5D4B',
                            canvas: '#F7F5EF',
                            surface: '#FFFFFF',
                            text: '#14231E',
                            muted: '#5A6B65',
                            accent: '#C7A76A',
                            danger: '#B84A42',
                            warning: '#C68A35',
                            success: '#2E7D5B',
                        }
                    },
                    boxShadow: {
                        xs: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Cropper.js CSS & JS for Visual Image Cropping -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F7F5EF; color: #14231E; }
        .section { display: none; }
        .section.active { display: block; animation: fadeIn 0.2s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(3px); } to { opacity: 1; transform: translateY(0); } }
        .nav-item { border-left: 3px solid transparent; transition: all 0.15s; }
        .nav-item.nav-active { background-color: rgba(255, 255, 255, 0.12); color: #C7A76A; border-left-color: #C7A76A; font-weight: 600; }
        .nav-item:hover:not(.nav-active) { background-color: rgba(255, 255, 255, 0.06); color: #FFFFFF; }
        /* Smooth Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        /* Modal Stacking & Higher Z-Index Utilities */
        .z-60 { z-index: 60 !important; }
        .z-70 { z-index: 70 !important; }
        .z-80 { z-index: 80 !important; }
        .z-90 { z-index: 90 !important; }
        .z-100 { z-index: 100 !important; }
    </style>
</head>
<body class="flex h-full overflow-hidden text-brand-text bg-brand-canvas text-sm select-none">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobile-sidebar-backdrop" onclick="closeMobileSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

    <!-- ================= RESPONSIVE LEFT SIDEBAR ================= -->
    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-brand-deep text-white flex flex-col flex-shrink-0 h-full border-r border-brand-primary/40 transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:w-64 shrink-0 shadow-2xl lg:shadow-none">
        <!-- Brand Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-brand-primary/40 bg-brand-deep/95 shrink-0">
            <a href="/admin" class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-brand-accent flex items-center justify-center text-brand-deep font-extrabold text-base shadow-sm">
                    K
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-wide text-white">Krishna Cottages</h1>
                    <p class="text-[10px] text-brand-accent uppercase tracking-widest font-semibold">Admin Panel</p>
                </div>
            </a>
            <div class="flex items-center gap-1">
                <a href="/" target="_blank" class="text-white/60 hover:text-white p-1.5 rounded transition" title="View Public Cottages Website">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                </a>
                <button type="button" onclick="closeMobileSidebar()" class="lg:hidden text-white/60 hover:text-white p-1.5 rounded transition cursor-pointer" title="Close Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation Scrollable Area -->
        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-4 text-xs" id="sidebar-nav">
            <!-- 1. OPERATIONS -->
            <div>
                <div class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Operations</div>
                <a href="#dashboard" data-section="dashboard" onclick="navigateTo('dashboard')" class="nav-item nav-active flex items-center gap-3 px-3 py-2 rounded-r-md cursor-pointer">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Flight Deck
                </a>
            </div>

            <!-- 2. FRONT DESK & STAYS -->
            <div>
                <div class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Front Desk & Stays</div>
                <a href="#reservations" data-section="reservations" onclick="navigateTo('reservations')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="calendar-check" class="w-4 h-4 text-emerald-400"></i> Stays & Bookings</div>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-600 text-white font-bold">{{ $stayingCount }} In-House</span>
                </a>
                <a href="#rooms" data-section="rooms" onclick="navigateTo('rooms')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="bed-double" class="w-4 h-4"></i> Accommodations & Rooms</div>
                    <span class="px-1.5 py-0.2 rounded text-[10px] bg-brand-primary text-white font-bold">{{ $rooms->count() }}</span>
                </a>
            </div>

            <!-- 3. SERVICES & COMMERCE -->
            <div>
                <div class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Services & Commerce</div>
                <a href="#menu" data-section="menu" onclick="navigateTo('menu')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="utensils" class="w-4 h-4"></i> Dining & Kitchen</div>
                    @if($pendingFoodOrders > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-orange-500 text-white font-bold animate-pulse">{{ $pendingFoodOrders }}</span>
                    @endif
                </a>
                <a href="#spices" data-section="spices" onclick="navigateTo('spices')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="leaf" class="w-4 h-4"></i> Krishna Spices Store</div>
                    @if($lowStockProducts > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-500 text-white font-bold">{{ $lowStockProducts }} Low</span>
                    @endif
                </a>
            </div>

            <!-- 4. PROPERTY & WEB -->
            <div>
                <div class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Property & Web</div>
                <a href="#branches" data-section="branches" onclick="navigateTo('branches')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="map-pin" class="w-4 h-4"></i> Destinations & Travel</div>
                    <span class="px-1.5 py-0.2 rounded text-[10px] bg-brand-primary text-white font-bold">{{ $branches->count() }}</span>
                </a>
                <a href="#slides" data-section="slides" onclick="navigateTo('slides')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="presentation" class="w-4 h-4 text-brass"></i> Hero & Menu Slides</div>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-brand-accent text-brand-deep font-bold" id="sidebar-slides-count">{{ count($homepageContent['hero_slides'] ?? $branches) }}</span>
                </a>
                <a href="#cms" data-section="cms" onclick="navigateTo('cms')" class="nav-item flex items-center gap-3 px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <i data-lucide="globe" class="w-4 h-4"></i> Website Studio & CMS
                </a>
            </div>

            <!-- 5. GUESTS & COMMUNICATIONS -->
            <div>
                <div class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Guests & Support</div>
                <a href="#guests" data-section="guests" onclick="navigateTo('guests')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="users" class="w-4 h-4"></i> Guest Directory & CRM</div>
                    <span class="px-1.5 py-0.2 rounded text-[10px] bg-brand-primary text-white font-bold">{{ $guests->count() }}</span>
                </a>
                <a href="#messages" data-section="messages" onclick="navigateTo('messages')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="message-square" class="w-4 h-4"></i> Enquiries & Chat</div>
                    @if($unreadMessagesCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500 text-white font-bold">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>
                <a href="#reviews" data-section="reviews" onclick="navigateTo('reviews')" class="nav-item flex items-center justify-between px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <div class="flex items-center gap-3"><i data-lucide="star" class="w-4 h-4 text-amber-400"></i> Reviews & Testimonials</div>
                    @php
                        $totalReviewsBadge = ($pendingReviewsCount ?? 0) + ($pendingFoodReviewsCount ?? 0);
                    @endphp
                    @if($totalReviewsBadge > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500 text-white font-bold animate-pulse">{{ $totalReviewsBadge }}</span>
                    @endif
                </a>
            </div>

            <!-- 6. ANALYTICS & SYSTEM -->
            <div>
                <div class="px-3 text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">Analytics & System</div>
                <a href="#reports" data-section="reports" onclick="navigateTo('reports')" class="nav-item flex items-center gap-3 px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Reports & Insights
                </a>
                <a href="#system" data-section="system" onclick="navigateTo('system')" class="nav-item flex items-center gap-3 px-3 py-2 rounded-r-md text-white/70 cursor-pointer">
                    <i data-lucide="settings" class="w-4 h-4"></i> System Admin & Policies
                </a>
            </div>
        </nav>

        <!-- Current User Footer -->
        <div class="p-3.5 border-t border-brand-primary/40 bg-brand-deep/90 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-brand-accent text-brand-deep font-bold flex items-center justify-center text-xs">
                    {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'AD' }}
                </div>
                <div class="truncate">
                    <div class="font-bold text-xs text-white truncate">{{ auth()->user() ? auth()->user()->name : 'Administrator' }}</div>
                    <div class="text-[10px] text-white/60 truncate">{{ auth()->user() ? ucwords(str_replace('_', ' ', auth()->user()->role)) : 'Staff' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-white/40 hover:text-white transition" title="Logout">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT WRAPPER ================= -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <!-- Sticky Header (64px) -->
        <header class="h-16 bg-brand-surface border-b border-gray-200/80 flex items-center justify-between px-3.5 sm:px-6 shrink-0 sticky top-0 z-20">
            <!-- Left: Mobile Menu Toggle & Branch Selector -->
            <div class="flex items-center gap-2 sm:gap-3">
                <button type="button" onclick="toggleAdminSidebar()" class="p-2 -ml-1 rounded-lg text-brand-muted hover:text-brand-text hover:bg-gray-100 transition flex items-center justify-center shrink-0 cursor-pointer" title="Toggle Navigation Sidebar" id="sidebar-toggle-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="relative">
                    <select id="global-branch-selector" onchange="switchGlobalBranch(this.value)" class="appearance-none bg-brand-canvas border border-gray-200 text-brand-text rounded-lg py-1.5 pl-2.5 sm:pl-3 pr-7 sm:pr-8 focus:outline-none focus:ring-1 focus:ring-brand-primary cursor-pointer text-xs font-bold shadow-xs max-w-[135px] xs:max-w-[170px] sm:max-w-xs truncate">
                        <option value="all" {{ !$branchId ? 'selected' : '' }}>All Branches (Cottages Central)</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>
                                {{ $b->name }} ({{ $b->city }})
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-2 top-2 text-brand-muted pointer-events-none"></i>
                </div>
                @if($selectedBranch)
                    <span class="hidden md:inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        {{ $selectedBranch->code }} Active
                    </span>
                @endif
            </div>

            <!-- Right: Actions & Tools -->
            <div class="flex items-center gap-1.5 sm:gap-3">
                <!-- Mobile Search Icon Button -->
                <button type="button" onclick="openCommandPalette()" class="sm:hidden p-2 text-brand-muted hover:text-brand-text rounded-lg hover:bg-gray-100 transition" title="Search anywhere">
                    <i data-lucide="search" class="w-4.5 h-4.5"></i>
                </button>

                <!-- Desktop Search Button / Command Palette Trigger -->
                <button onclick="openCommandPalette()" class="hidden sm:flex items-center gap-2 bg-brand-canvas border border-gray-200 text-brand-muted px-3 py-1.5 rounded-lg hover:bg-gray-100 transition w-44 md:w-52 text-left group shadow-xs">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 group-hover:text-brand-primary"></i> 
                    <span class="flex-1 text-xs truncate">Search anywhere...</span>
                    <kbd class="hidden md:inline-block border border-gray-300 rounded px-1.5 text-[10px] bg-white group-hover:border-gray-400 font-mono">Ctrl K</kbd>
                </button>

                <!-- Quick Action New Reservation Button -->
                <button onclick="openNewReservationModal()" class="inline-flex items-center gap-1.5 bg-brand-primary hover:bg-brand-deep text-white px-2.5 sm:px-3.5 py-1.5 rounded-lg text-xs font-bold shadow-xs transition whitespace-nowrap">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">New Booking</span>
                    <span class="sm:hidden">Booking</span>
                </button>

                <!-- Interactive Notifications Center Bell & Dropdown -->
                <div class="relative" id="admin-notifications-container">
                    <button type="button" onclick="toggleNotificationsDropdown()" id="admin-notif-bell-btn" class="relative p-2 text-brand-muted hover:text-brand-text rounded-lg hover:bg-brand-canvas transition cursor-pointer" title="Operational Alerts & Notifications">
                        <i data-lucide="bell" class="w-4.5 h-4.5"></i>
                        <span id="admin-notif-badge" class="hidden absolute -top-1 -right-1 px-1.5 py-0.2 min-w-[18px] text-[10px] font-extrabold bg-rose-600 text-white rounded-full ring-2 ring-white text-center leading-tight shadow-xs animate-pulse">
                            0
                        </span>
                    </button>

                    <!-- Dropdown Overlay Menu -->
                    <div id="admin-notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl border border-gray-200/80 shadow-2xl z-60 overflow-hidden text-xs transform origin-top-right transition-all">
                        <!-- Dropdown Header -->
                        <div class="p-3.5 bg-brand-deep text-white flex items-center justify-between border-b border-brand-primary/40">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-brand-accent/20 flex items-center justify-center text-brand-accent">
                                    <i data-lucide="bell" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs text-white">Live Operations Feed</h4>
                                    <p class="text-[10px] text-brand-accent">Real-time alerts & guest requests</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="markAllNotificationsRead()" class="text-[10px] font-semibold text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-2 py-1 rounded transition cursor-pointer">
                                    Mark Read
                                </button>
                                <button type="button" onclick="closeNotificationsDropdown()" class="text-white/60 hover:text-white p-1 rounded cursor-pointer">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter Pills -->
                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-100 flex items-center gap-1 overflow-x-auto no-scrollbar text-[11px]">
                            <button type="button" onclick="filterAdminNotifications('all')" id="notif-tab-all" class="notif-filter-pill px-2.5 py-1 rounded-full font-bold bg-brand-primary text-white transition shrink-0 cursor-pointer">All (<span id="notif-cnt-all">0</span>)</button>
                            <button type="button" onclick="filterAdminNotifications('stays')" id="notif-tab-stays" class="notif-filter-pill px-2.5 py-1 rounded-full font-semibold bg-gray-200/70 text-brand-muted hover:bg-gray-200 transition shrink-0 cursor-pointer">Stays (<span id="notif-cnt-stays">0</span>)</button>
                            <button type="button" onclick="filterAdminNotifications('dining')" id="notif-tab-dining" class="notif-filter-pill px-2.5 py-1 rounded-full font-semibold bg-gray-200/70 text-brand-muted hover:bg-gray-200 transition shrink-0 cursor-pointer">Dining (<span id="notif-cnt-dining">0</span>)</button>
                            <button type="button" onclick="filterAdminNotifications('messages')" id="notif-tab-messages" class="notif-filter-pill px-2.5 py-1 rounded-full font-semibold bg-gray-200/70 text-brand-muted hover:bg-gray-200 transition shrink-0 cursor-pointer">Enquiries (<span id="notif-cnt-messages">0</span>)</button>
                            <button type="button" onclick="filterAdminNotifications('services')" id="notif-tab-services" class="notif-filter-pill px-2.5 py-1 rounded-full font-semibold bg-gray-200/70 text-brand-muted hover:bg-gray-200 transition shrink-0 cursor-pointer">Concierge (<span id="notif-cnt-services">0</span>)</button>
                            <button type="button" onclick="filterAdminNotifications('shop')" id="notif-tab-shop" class="notif-filter-pill px-2.5 py-1 rounded-full font-semibold bg-gray-200/70 text-brand-muted hover:bg-gray-200 transition shrink-0 cursor-pointer">Spices (<span id="notif-cnt-shop">0</span>)</button>
                        </div>

                        <!-- Notification List (Scrollable) -->
                        <div id="admin-notif-list" class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                            <!-- Populated dynamically via JS -->
                            <div class="py-10 text-center text-brand-muted">
                                <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-brand-primary opacity-60 mb-2"></i>
                                <p class="text-xs">Connecting to live feed...</p>
                            </div>
                        </div>

                        <!-- Dropdown Footer -->
                        <div class="p-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-[10px] text-brand-muted">
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                Live Polling (15s)
                            </span>
                            <button type="button" onclick="navigateTo('system'); switchSystemTab('notifs'); closeNotificationsDropdown();" class="text-brand-primary hover:underline font-bold">
                                View Delivery Logs &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Section Container -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 pb-24 lg:pb-6 relative" id="admin-main-viewport">
            <!-- 1. DASHBOARD (ADM-01) -->
            @include('admin.sections.dashboard')

            <!-- 2. RESERVATIONS (ADM-02) -->
            @include('admin.sections.reservations')

            <!-- 2B. CHECKED-IN CLIENTS & IN-HOUSE HUB (ADM-02B) -->
            @include('admin.sections.in_house')

            <!-- 3. AVAILABILITY MATRIX (ADM-04) -->
            @include('admin.sections.availability')

            <!-- 4. ROOMS & ROOM TYPES (ADM-05) -->
            @include('admin.sections.rooms')

            <!-- 5. ROOM STATUS BOARD (ADM-06) -->
            @include('admin.sections.room_status')

            <!-- 6. GUESTS CRM (ADM-08) -->
            @include('admin.sections.guests')

            <!-- 7. DINING MENU (ADM-13 & ADM-14) -->
            @include('admin.sections.dining')

            <!-- 8. FOOD ORDERS (ADM-15) -->
            @include('admin.sections.food_orders')

            <!-- 9. SPICE PRODUCTS (ADM-16) -->
            @include('admin.sections.spices')

            <!-- 10. SPICE INVENTORY (ADM-17) -->
            @include('admin.sections.inventory')

            <!-- 11. SPICE ORDERS (ADM-18) -->
            @include('admin.sections.spice_orders')

            <!-- 12. RESORT CONTENT & BRANCHES (ADM-09 to ADM-12) -->
            @include('admin.sections.resort')

            <!-- 13. COMMUNICATIONS & REVIEWS (ADM-19 & ADM-20) -->
            @include('admin.sections.communications')

            <!-- 13B. REVIEWS & TESTIMONIALS HUB -->
            @include('admin.sections.reviews')

            <!-- 14. CMS (ADM-22 to ADM-24) -->
            @include('admin.sections.cms')

            <!-- 14B. HERO & MENU CAROUSEL SLIDES CRUD (ADM-12B) -->
            @include('admin.sections.slides')

            <!-- 15. REPORTS (ADM-25) -->
            @include('admin.sections.reports')

            <!-- 16. SYSTEM & SETTINGS (ADM-26 to ADM-29) -->
            @include('admin.sections.system')
        </div>
    </main>

    <!-- ================= SLIDE-OVER DETAIL DRAWER (ADM-03) ================= -->
    @include('admin.components.drawer')

    <!-- ================= NEW RESERVATION MODAL (ADM-07) ================= -->
    @include('admin.components.new_reservation_modal')

    <!-- ================= SPICE STOCK ADJUSTMENT MODAL ================= -->
    @include('admin.components.stock_modal')

    <!-- ================= UNIVERSAL COMMAND PALETTE (ADM-30) ================= -->
    @include('admin.components.command_palette')

    <!-- ================= UNIVERSAL ENTITY CRUD MODALS ================= -->
    @include('admin.components.entity_modals')

    <!-- ================= INDIVIDUAL CLIENT 360° IN-HOUSE CONSOLE MODAL (ADM-02C) ================= -->
    @include('admin.components.guest_360_modal')

    <!-- ================= UNIVERSAL IMAGE PICKER & SWAP MODAL (TOPMOST z-[110]) ================= -->
    @include('admin.components.image_picker_modal')

    <!-- ================= HERO CAROUSEL SLIDE DESIGNER & IMAGE CROPPER MODAL (z-[105]) ================= -->
    @include('admin.components.hero_slide_modal')

    <!-- Toast Notification Container -->
        <!-- Mobile App Bottom Navigation Bar (< 1024px) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-brand-deep/95 backdrop-blur-md border-t border-brand-primary/40 px-2 py-1.5 flex items-center justify-around text-white shadow-2xl safe-area-pb" id="admin-mobile-bottom-nav">
        <a href="#dashboard" onclick="navigateTo('dashboard')" data-mobile-section="dashboard" class="flex flex-col items-center justify-center p-1.5 rounded-xl transition touch-tap text-white/60 hover:text-white mobile-nav-btn">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="text-[9px] font-semibold mt-0.5">Flight Deck</span>
        </a>
        <a href="#reservations" onclick="navigateTo('reservations')" data-mobile-section="reservations" class="flex flex-col items-center justify-center p-1.5 rounded-xl transition touch-tap text-white/60 hover:text-white mobile-nav-btn relative">
            <i data-lucide="calendar-check" class="w-5 h-5"></i>
            <span class="text-[9px] font-semibold mt-0.5">Stays</span>
            @if($stayingCount > 0)
                <span class="absolute top-1 right-2 w-2 h-2 rounded-full bg-emerald-400"></span>
            @endif
        </a>
        <a href="#rooms" onclick="navigateTo('rooms')" data-mobile-section="rooms" class="flex flex-col items-center justify-center p-1.5 rounded-xl transition touch-tap text-white/60 hover:text-white mobile-nav-btn">
            <i data-lucide="bed-double" class="w-5 h-5"></i>
            <span class="text-[9px] font-semibold mt-0.5">Rooms</span>
        </a>
        <a href="#menu" onclick="navigateTo('menu')" data-mobile-section="menu" class="flex flex-col items-center justify-center p-1.5 rounded-xl transition touch-tap text-white/60 hover:text-white mobile-nav-btn relative">
            <i data-lucide="utensils" class="w-5 h-5"></i>
            <span class="text-[9px] font-semibold mt-0.5">Dining</span>
            @if($pendingFoodOrders > 0)
                <span class="absolute top-1 right-2 w-2 h-2 rounded-full bg-orange-400"></span>
            @endif
        </a>
        <button type="button" onclick="toggleAdminSidebar()" class="flex flex-col items-center justify-center p-1.5 rounded-xl transition touch-tap text-white/60 hover:text-white cursor-pointer">
            <i data-lucide="menu" class="w-5 h-5"></i>
            <span class="text-[9px] font-semibold mt-0.5">Menu</span>
        </button>
    </nav>

    <div id="toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none" style="z-index: 9999;"></div>

    <!-- ================= INTERACTIVE ADMIN JAVASCRIPT ENGINE ================= -->
    <script>
        // Serialized Database Models for Instant Local Lookup
        const allReservations = @json($reservations);
        const allCheckedInReservations = @json($checkedInReservations);
        const allBranches = @json($branches);
        const allSpiceProducts = @json($spiceProducts);
        const allStayExtensions = @json($stayExtensionRequests);
        const allCancellationRules = @json($cancellationRules);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentReservationId = null;
        let activeImagePickerTargetId = null;

        // Global HTML Sanitizer Helper
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
        window.escapeHtml = escapeHtml;

        // Lucide Icons Helper
        function refreshIcons() {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }

        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
            refreshIcons();
            
            // Route to initial section via hash
            const hash = window.location.hash.replace('#', '') || 'dashboard';
            navigateTo(hash);
        });

        // Universal Navigation Sidebar Controls
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (!sidebar) return;

            if (window.innerWidth < 1024) {
                // Mobile slide-over drawer
                const isOpen = !sidebar.classList.contains('-translate-x-full');
                if (isOpen) {
                    sidebar.classList.add('-translate-x-full');
                    if (backdrop) backdrop.classList.add('hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    if (backdrop) backdrop.classList.remove('hidden');
                }
            } else {
                // Desktop toggle / collapse
                sidebar.classList.toggle('lg:hidden');
            }
            refreshIcons();
        }

        function openMobileSidebar() {
            toggleAdminSidebar();
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }

        function toggleMobileSidebar() {
            toggleAdminSidebar();
        }

        // Communications mobile view switcher
        function backToEnquiryThreads() {
            const threadCol = document.getElementById('comm-thread-list-col');
            const chatCol = document.getElementById('comm-chat-view-col');
            const secHeader = document.getElementById('comm-section-header');
            if (threadCol && chatCol) {
                threadCol.classList.remove('hidden');
                chatCol.classList.add('hidden');
                chatCol.classList.remove('flex');
            }
            if (secHeader) {
                secHeader.classList.remove('hidden');
            }
            refreshIcons();
        }

        // SPA Navigation Engine with Seamless Legacy Hash Mapping
        const legacyRouteMap = {
            'in-house': { hub: 'reservations', tab: () => switchReservationView('inhouse') },
            'stay-extensions': { hub: 'reservations', tab: () => switchReservationView('extensions') },
            'room-status': { hub: 'rooms', tab: () => switchRoomTab('floorplan') },
            'availability': { hub: 'rooms', tab: () => switchRoomTab('availability') },
            'room-types': { hub: 'rooms', tab: () => switchRoomTab('types') },
            'food-orders': { hub: 'menu', tab: () => switchDiningTab('orders') },
            'dining': { hub: 'menu', tab: () => switchDiningTab('items') },
            'products': { hub: 'spices', tab: () => switchSpiceTab('products') },
            'inventory': { hub: 'spices', tab: () => switchSpiceTab('inventory') },
            'shop-orders': { hub: 'spices', tab: () => switchSpiceTab('orders') },
            'pages': { hub: 'cms', tab: () => switchCmsTab('pages') },
            'navigation': { hub: 'cms', tab: () => switchCmsTab('nav') },
            'homepage': { hub: 'cms', tab: () => switchCmsTab('home') },
            'visual-editor': { hub: 'cms', tab: () => switchCmsTab('visual') },
            'slides': { hub: 'slides' },
            'menu-slides': { hub: 'slides' },
            'hero-slides': { hub: 'slides' },
        };

        function navigateTo(sectionId) {
            let activeHub = sectionId;
            let targetTabFn = null;

            if (legacyRouteMap[sectionId]) {
                activeHub = legacyRouteMap[sectionId].hub;
                targetTabFn = legacyRouteMap[sectionId].tab;
            }

            window.history.replaceState(null, null, `#${sectionId}`);

            // Auto-close mobile sidebar if open
            if (window.innerWidth < 1024) {
                closeMobileSidebar();
            }

            // Hide all sections
            document.querySelectorAll('.section').forEach(el => el.classList.remove('active'));

            // Show active section
            const targetSection = document.getElementById(activeHub);
            if (targetSection) {
                targetSection.classList.add('active');
            } else {
                const fallback = document.getElementById('dashboard');
                if (fallback) fallback.classList.add('active');
                activeHub = 'dashboard';
            }

            // If a tab switch function exists for this route, trigger it
            if (typeof targetTabFn === 'function') {
                targetTabFn();
            }

            // Update desktop sidebar active classes
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('nav-active'));
            const activeLink = document.querySelector(`[data-section="${activeHub}"]`) || document.querySelector(`[data-section="${sectionId}"]`);
            if (activeLink) activeLink.classList.add('nav-active');

            // Update mobile bottom nav active classes
            document.querySelectorAll('.mobile-nav-btn').forEach(btn => {
                btn.classList.remove('text-brand-accent', 'font-bold');
                btn.classList.add('text-white/60');
            });
            const activeMobileBtn = document.querySelector(`[data-mobile-section="${activeHub}"]`);
            if (activeMobileBtn) {
                activeMobileBtn.classList.add('text-brand-accent', 'font-bold');
                activeMobileBtn.classList.remove('text-white/60');
            }

            // Refresh icons in newly visible view
            setTimeout(() => {
                if (typeof refreshIcons === 'function') refreshIcons();
                else if (window.lucide && lucide.createIcons) lucide.createIcons();
            }, 50);
        }

        // Global Branch Switcher
        function switchGlobalBranch(branchId) {
            const url = new URL(window.location.href);
            if (branchId === 'all') {
                url.searchParams.delete('branch_id');
            } else {
                url.searchParams.set('branch_id', branchId);
            }
            window.location.href = url.toString();
        }

        // ================= RESERVATION ACTIONS =================
        function openReservationDrawer(resId) {
            const res = allReservations.find(r => r.id === resId);
            if (!res) return;

            currentReservationId = res.id;

            // Populate drawer
            document.getElementById('drawer-booking-code').innerText = res.booking_code;
            document.getElementById('drawer-created-at').innerText = 'Created ' + new Date(res.created_at).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
            
            const guestName = res.guest ? (res.guest.first_name + ' ' + (res.guest.last_name || '')) : 'Guest';
            document.getElementById('drawer-guest-name').innerText = guestName;
            document.getElementById('drawer-guest-avatar').innerText = guestName.charAt(0);
            document.getElementById('drawer-guest-contact').innerText = (res.guest ? res.guest.phone : '') + ' • ' + (res.guest ? res.guest.email : '');

            document.getElementById('drawer-checkin').innerText = new Date(res.check_in_date).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
            document.getElementById('drawer-checkout').innerText = new Date(res.check_out_date).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });

            document.getElementById('drawer-branch-name').innerText = res.branch ? res.branch.name : 'Resort';
            document.getElementById('drawer-room-type').innerText = res.room_type ? res.room_type.name : 'Standard';
            document.getElementById('drawer-room-unit').innerText = res.room ? ('Room ' + res.room.room_number + ' (' + res.room.floor + ')') : 'Unassigned';

            document.getElementById('drawer-rate').innerText = '₹' + Number(res.nightly_rate).toLocaleString();
            document.getElementById('drawer-subtotal').innerText = '₹' + Number(res.subtotal).toLocaleString();
            document.getElementById('drawer-tax').innerText = '₹' + Number(res.tax_amount).toLocaleString();
            document.getElementById('drawer-total').innerText = '₹' + Number(res.total_amount).toLocaleString();
            
            const payBadge = document.getElementById('drawer-payment-badge');
            payBadge.innerText = res.payment_status.toUpperCase();
            payBadge.className = res.payment_status === 'paid' ? 'font-bold text-emerald-700' : (res.payment_status === 'refunded' ? 'font-bold text-purple-700' : 'font-bold text-amber-700');

            const refundRow = document.getElementById('drawer-refund-row');
            if (refundRow) {
                if (Number(res.refunded_amount) > 0 || res.status === 'cancelled') {
                    refundRow.classList.remove('hidden');
                    document.getElementById('drawer-refunded-amount').innerText = '₹' + Number(res.refunded_amount || 0).toLocaleString();
                } else {
                    refundRow.classList.add('hidden');
                }
            }

            // Check if reservation has an unsettled approved stay extension
            const hasUnsettledExt = allStayExtensions.find(e => e.reservation_id === res.id && e.status === 'approved' && e.payment_status === 'pending');
            const alertEl = document.getElementById('drawer-extension-alert');
            if (alertEl) {
                if (hasUnsettledExt) {
                    alertEl.classList.remove('hidden');
                    document.getElementById('drawer-ext-desc').innerText = `Approved extension until ${new Date(hasUnsettledExt.requested_checkout_date).toLocaleDateString()} (₹${Number(hasUnsettledExt.offered_amount).toLocaleString()} due on-hand / checkout).`;
                    window.currentExtensionForDrawer = hasUnsettledExt;
                } else {
                    alertEl.classList.add('hidden');
                    window.currentExtensionForDrawer = null;
                }
            }

            document.getElementById('drawer-requests').innerText = res.special_requests || 'No special requests noted.';

            // Contextual Button visibility
            const btnCheckIn = document.getElementById('drawer-btn-checkin');
            const btnCheckOut = document.getElementById('drawer-btn-checkout');
            const btnCancel = document.getElementById('drawer-btn-cancel');

            if (res.status === 'confirmed') {
                btnCheckIn.classList.remove('hidden');
                btnCheckOut.classList.add('hidden');
                btnCancel.classList.remove('hidden');
            } else if (res.status === 'checked_in') {
                btnCheckIn.classList.add('hidden');
                btnCheckOut.classList.remove('hidden');
                btnCancel.classList.add('hidden');
            } else {
                btnCheckIn.classList.add('hidden');
                btnCheckOut.classList.add('hidden');
                btnCancel.classList.add('hidden');
            }

            // Open Drawer
            const drawer = document.getElementById('reservation-drawer');
            const backdrop = document.getElementById('drawer-backdrop');
            drawer.classList.remove('translate-x-full');
            backdrop.classList.remove('hidden');
        }

        function closeDrawer() {
            const drawer = document.getElementById('reservation-drawer');
            const backdrop = document.getElementById('drawer-backdrop');
            drawer.classList.add('translate-x-full');
            backdrop.classList.add('hidden');
        }

        async function triggerReservationCheckIn() {
            if (!currentReservationId) return;
            try {
                const res = await fetch(`/admin/reservations/${currentReservationId}/check-in`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeDrawer();
                    setTimeout(() => window.location.reload(), 800);
                }
            } catch (err) {
                showToast('Failed to check in reservation', 'error');
            }
        }

        async function triggerReservationCheckOut() {
            if (!currentReservationId) return;
            try {
                const res = await fetch(`/admin/reservations/${currentReservationId}/check-out`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeDrawer();
                    setTimeout(() => window.location.reload(), 800);
                }
            } catch (err) {
                showToast('Failed to check out reservation', 'error');
            }
        }

        async function triggerReservationCancel() {
            if (!currentReservationId || !confirm('Are you sure you want to cancel this reservation?')) return;
            try {
                const res = await fetch(`/admin/reservations/${currentReservationId}/cancel`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ reason: 'Cancelled via front-desk dashboard' })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeDrawer();
                    setTimeout(() => window.location.reload(), 800);
                }
            } catch (err) {
                showToast('Failed to cancel reservation', 'error');
            }
        }

        // Reservation Table Filter & Search
        function filterReservations(status, btn) {
            document.querySelectorAll('.res-filter-btn').forEach(b => {
                b.className = 'res-filter-btn px-3 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition';
            });
            btn.className = 'res-filter-btn px-3 py-1 rounded-lg text-xs font-semibold bg-brand-primary text-white transition';

            document.querySelectorAll('.res-row').forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function searchReservationTable() {
            const query = document.getElementById('res-search-input').value.toLowerCase();
            document.querySelectorAll('.res-row').forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // ================= ROOMS & STATUS (ADM-05) =================
        function switchRoomTab(tab) {
            const tabs = ['floorplan', 'availability', 'types', 'physical', 'categories', 'amenities'];
            tabs.forEach(t => {
                const el = document.getElementById(`room-tab-${t}`);
                const btn = document.getElementById(`tab-btn-${t}`);
                if (!el || !btn) return;

                if (t === tab) {
                    el.classList.remove('hidden');
                    btn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition shadow-xs shrink-0 flex items-center gap-1.5';
                } else {
                    el.classList.add('hidden');
                    btn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5';
                }
            });
            if (typeof refreshIcons === 'function') refreshIcons();
            else if (window.lucide && lucide.createIcons) lucide.createIcons();
        }

        function openEditRoomTypeModal(rt) {
            document.getElementById('edit-room-type-id').value = rt.id;
            document.getElementById('edit-rt-branch-id').value = rt.branch_id;
            document.getElementById('edit-rt-category-id').value = rt.room_category_id || '';
            document.getElementById('edit-rt-name').value = rt.name || '';
            document.getElementById('edit-rt-base-price').value = rt.base_price || '';
            document.getElementById('edit-rt-weekend-price').value = rt.weekend_price || '';
            document.getElementById('edit-rt-max-guests').value = rt.max_guests || ((rt.max_adults || 2) + (rt.max_children || 0));
            document.getElementById('edit-rt-max-adults').value = rt.max_adults || 2;
            document.getElementById('edit-rt-max-children').value = rt.max_children || 0;
            document.getElementById('edit-rt-size-sqft').value = rt.size_sqft || 450;
            document.getElementById('edit-rt-bed-type').value = rt.bed_type || '';
            document.getElementById('edit-rt-short-desc').value = rt.short_description || '';
            document.getElementById('edit-rt-description').value = rt.description || '';
            document.getElementById('edit-roomtype-image-url').value = rt.cover_image_url || '';
            updateFieldThumbnailPreview('edit-roomtype-image-url', rt.cover_image_url || '');

            const chipsContainer = document.getElementById('edit-rt-gallery-chips');
            if (chipsContainer) {
                chipsContainer.innerHTML = '';
                const images = Array.isArray(rt.gallery_images) ? rt.gallery_images : [];
                if (images.length > 0) {
                    images.forEach(imgUrl => addGalleryChipToContainer(chipsContainer, 'gallery_images[]', imgUrl));
                } else {
                    chipsContainer.innerHTML = '<span class="text-[10px] text-gray-400 self-center px-1">No additional gallery photos</span>';
                }
            }

            const linkedIds = (rt.amenities_list || []).map(a => String(a.id));
            document.querySelectorAll('.edit-rt-amenity-checkbox').forEach(cb => {
                cb.checked = linkedIds.includes(String(cb.value));
            });

            openModal('modal-edit-room-type');
        }

        async function handleEditRoomTypeSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const rtId = document.getElementById('edit-room-type-id').value;
            const formData = new FormData(form);

            try {
                const res = await fetch(`/admin/room-types/${rtId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-edit-room-type');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Validation error', 'error');
                }
            } catch (err) {
                showToast('Failed to update room type', 'error');
            }
        }

        function openEditCategoryModal(cat) {
            document.getElementById('edit-cat-id').value = cat.id;
            document.getElementById('edit-cat-name').value = cat.name || '';
            document.getElementById('edit-cat-icon').value = cat.icon || 'palmtree';
            document.getElementById('edit-cat-desc').value = cat.description || '';
            openModal('modal-edit-room-category');
        }

        async function handleEditCategorySubmit(e) {
            e.preventDefault();
            const form = e.target;
            const catId = document.getElementById('edit-cat-id').value;
            const formData = new FormData(form);

            try {
                const res = await fetch(`/admin/room-categories/${catId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-edit-room-category');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to update category', 'error');
                }
            } catch (err) {
                showToast('Network error updating category', 'error');
            }
        }

        async function deleteCategory(catId) {
            if (!confirm('Are you sure you want to delete this room category?')) return;
            try {
                const res = await fetch(`/admin/room-categories/${catId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Error deleting category', 'error');
                }
            } catch (err) {
                showToast('Error deleting category', 'error');
            }
        }

        function openEditAmenityModal(am) {
            document.getElementById('edit-am-id').value = am.id;
            document.getElementById('edit-am-name').value = am.name || '';
            document.getElementById('edit-am-category').value = am.category || 'General';
            document.getElementById('edit-am-icon').value = am.icon || 'sparkles';
            document.getElementById('edit-am-desc').value = am.description || '';
            document.getElementById('edit-am-is-featured').checked = Boolean(am.is_featured);
            openModal('modal-edit-amenity');
        }

        async function handleEditAmenitySubmit(e) {
            e.preventDefault();
            const form = e.target;
            const amId = document.getElementById('edit-am-id').value;
            const formData = new FormData(form);

            try {
                const res = await fetch(`/admin/amenities/${amId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-edit-amenity');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to update amenity', 'error');
                }
            } catch (err) {
                showToast('Network error updating amenity', 'error');
            }
        }

        async function deleteAmenity(amId) {
            if (!confirm('Are you sure you want to remove this amenity from the library?')) return;
            try {
                const res = await fetch(`/admin/amenities/${amId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Error deleting amenity', 'error');
                }
            } catch (err) {
                showToast('Error deleting amenity', 'error');
            }
        }

        async function toggleRoomHousekeeping(roomId, status) {
            try {
                const res = await fetch(`/admin/rooms/${roomId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ housekeeping_status: status })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                }
            } catch(e) {
                showToast('Failed to update room housekeeping', 'error');
            }
        }

        async function updateRoomOperationalStatus(roomId, status) {
            try {
                const res = await fetch(`/admin/rooms/${roomId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ operational_status: status })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                }
            } catch(e) {
                showToast('Failed to update room operational status', 'error');
            }
        }

        // ================= GUESTS CRM =================
        function searchGuestTable() {
            const query = document.getElementById('guest-search-input').value.toLowerCase();
            document.querySelectorAll('.guest-row').forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // ================= DINING ACTIONS =================
        function switchDiningTab(tab) {
            const tabs = ['orders', 'items', 'categories', 'daily', 'reviews', 'modifiers'];
            tabs.forEach(t => {
                const el = document.getElementById(`dining-tab-${t}`);
                const btn = document.getElementById(`dining-tab-btn-${t}`);
                if (!el || !btn) return;

                if (t === tab) {
                    el.classList.remove('hidden');
                    btn.className = 'px-3.5 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5 shrink-0';
                } else {
                    el.classList.add('hidden');
                    btn.className = 'px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition flex items-center gap-1.5 shrink-0';
                }
            });
            if (typeof refreshIcons === 'function') refreshIcons();
            else if (window.lucide && lucide.createIcons) lucide.createIcons();
        }

        function filterMenuItems(catClass, btn) {
            document.querySelectorAll('.menu-cat-btn').forEach(b => {
                b.className = 'menu-cat-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-surface border border-gray-200 text-brand-muted hover:bg-gray-100 shrink-0 transition';
            });
            btn.className = 'menu-cat-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-deep text-white shrink-0';

            document.querySelectorAll('.menu-item-card').forEach(card => {
                if (catClass === 'all' || card.classList.contains(catClass)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        async function updateMenuStock(itemId, state) {
            try {
                const res = await fetch(`/admin/dining/items/${itemId}/toggle-stock`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ availability_state: state })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const badge = document.getElementById(`dish-badge-${itemId}`);
                    if (badge) {
                        badge.innerHTML = state === 'in_stock'
                            ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">In Stock</span>'
                            : (state === 'limited_quantity'
                                ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Limited</span>'
                                : '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Out of Stock</span>');
                    }
                }
            } catch (err) {
                showToast('Failed to update dish availability', 'error');
            }
        }

        async function advanceFoodOrderStatus(orderId, nextStatus) {
            try {
                const res = await fetch(`/admin/dining/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ status: nextStatus })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                }
            } catch (err) {
                showToast('Failed to advance food order status', 'error');
            }
        }

        // ================= SPICE SHOP ACTIONS =================
        function openStockModal(productId, productName, currentQty) {
            document.getElementById('stock-modal-product-id').value = productId;
            document.getElementById('stock-modal-product-name').innerText = productName;
            document.getElementById('stock-modal-current-qty').innerText = currentQty + ' units';
            document.getElementById('stock-adjust-modal').classList.remove('hidden');
        }

        function closeStockModal() {
            document.getElementById('stock-adjust-modal').classList.add('hidden');
        }

        async function submitStockAdjustment(event) {
            event.preventDefault();
            const form = event.target;
            const productId = form.product_id.value;
            const payload = {
                adjustment_type: form.adjustment_type.value,
                quantity_change: parseInt(form.quantity_change.value),
                reason: form.reason.value
            };

            try {
                const res = await fetch(`/admin/spices/inventory/${productId}/adjust`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeStockModal();
                    setTimeout(() => window.location.reload(), 800);
                }
            } catch (err) {
                showToast('Failed to save stock adjustment', 'error');
            }
        }

        async function updateSpiceOrderStatus(orderId, status) {
            try {
                const res = await fetch(`/admin/spices/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ status: status })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                }
            } catch (err) {
                showToast('Failed to update spice order status', 'error');
            }
        }

        // ================= KRISHNA SPICES TABS & RETURN POLICY =================
        function switchSpiceTab(tab) {
            const tabs = ['orders', 'products', 'policy', 'inventory'];
            tabs.forEach(t => {
                const el = document.getElementById(`spice-tab-${t}`);
                const btn = document.getElementById(`spice-tab-btn-${t}`);
                if (!el || !btn) return;

                if (t === tab || (tab === 'inventory' && t === 'policy')) {
                    el.classList.remove('hidden');
                    btn.className = 'px-3.5 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5 shrink-0';
                } else {
                    el.classList.add('hidden');
                    btn.className = 'px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition flex items-center gap-1 shrink-0';
                }
            });
            if (typeof refreshIcons === 'function') refreshIcons();
            else if (window.lucide && lucide.createIcons) lucide.createIcons();
        }

        async function toggleSpiceAvailability(id, btn) {
            try {
                const res = await fetch(`/admin/spices/products/${id}/toggle-availability`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const badge = document.getElementById(`spice-avail-badge-${id}`);
                    if (badge) {
                        badge.className = `px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${data.is_active ? 'bg-emerald-500 text-white shadow-xs' : 'bg-gray-800/90 text-gray-200'}`;
                        badge.textContent = data.is_active ? '✓ Active in Store' : '✕ Paused / Inactive';
                    }
                    if (btn) {
                        btn.className = `px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 ${data.is_active ? 'bg-amber-100 text-amber-900 hover:bg-amber-200' : 'bg-emerald-600 text-white hover:bg-emerald-700'}`;
                        btn.innerHTML = `<i data-lucide="${data.is_active ? 'pause-circle' : 'play-circle'}" class="w-3.5 h-3.5"></i><span>${data.is_active ? 'Pause in Store' : 'Activate in Store'}</span>`;
                    }
                    if (typeof refreshIcons === 'function') refreshIcons();
                } else {
                    showToast(data.message || 'Error updating product', 'error');
                }
            } catch (err) {
                showToast('Network error updating product status', 'error');
            }
        }

        async function toggleGlobalSpiceReturns() {
            try {
                const res = await fetch('/admin/spices/toggle-global-returns', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const enabled = data.spice_returns_enabled;

                    // Update header switch
                    const headerBtn = document.getElementById('global-returns-toggle-btn');
                    const headerThumb = document.getElementById('global-returns-toggle-thumb');
                    const headerLabel = document.getElementById('global-returns-toggle-label');
                    if (headerBtn && headerThumb && headerLabel) {
                        headerBtn.className = `relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden ${enabled ? 'bg-emerald-600' : 'bg-gray-300'}`;
                        headerBtn.setAttribute('aria-checked', enabled ? 'true' : 'false');
                        headerThumb.className = `pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out ${enabled ? 'translate-x-5' : 'translate-x-0'}`;
                        headerLabel.className = `text-[11px] font-bold ${enabled ? 'text-emerald-700' : 'text-gray-500'}`;
                        headerLabel.textContent = enabled ? 'ON' : 'OFF';
                    }

                    // Update Tab 3 card
                    const tab3Card = document.getElementById('tab3-global-card');
                    const tab3Icon = document.getElementById('tab3-global-icon-box');
                    const tab3Badge = document.getElementById('tab3-global-status-badge');
                    const tab3Btn = document.getElementById('tab3-global-returns-toggle-btn');
                    const tab3BtnText = document.getElementById('tab3-global-returns-btn-text');
                    const tab3Warn = document.getElementById('tab3-global-warning');

                    if (tab3Card) {
                        tab3Card.className = `p-5 sm:p-6 rounded-2xl border transition-all duration-300 shadow-xs ${enabled ? 'bg-emerald-50/70 border-emerald-200' : 'bg-amber-50/90 border-amber-300'}`;
                    }
                    if (tab3Icon) {
                        tab3Icon.className = `w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 ${enabled ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white'}`;
                        tab3Icon.innerHTML = `<i data-lucide="${enabled ? 'rotate-ccw' : 'shield-alert'}" class="w-6 h-6"></i>`;
                    }
                    if (tab3Badge) {
                        tab3Badge.className = `px-2.5 py-0.5 rounded-full text-xs font-bold ${enabled ? 'bg-emerald-200 text-emerald-900 border border-emerald-300' : 'bg-amber-200 text-amber-900 border border-amber-400'}`;
                        tab3Badge.textContent = enabled ? '✓ Globally ACTIVE' : '✕ Globally TURNED OFF';
                    }
                    if (tab3Btn && tab3BtnText) {
                        tab3Btn.className = `px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 ${enabled ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'}`;
                        tab3Btn.innerHTML = `<i data-lucide="${enabled ? 'power-off' : 'power'}" class="w-4 h-4"></i><span>${enabled ? 'Turn OFF Globally' : 'Enable Globally'}</span>`;
                    }
                    if (tab3Warn) {
                        if (enabled) {
                            tab3Warn.classList.add('hidden');
                            tab3Warn.classList.remove('flex');
                        } else {
                            tab3Warn.classList.remove('hidden');
                            tab3Warn.classList.add('flex');
                        }
                    }

                    if (typeof refreshIcons === 'function') refreshIcons();
                } else {
                    showToast(data.message || 'Error updating global return setting', 'error');
                }
            } catch (err) {
                showToast('Failed to toggle global returns', 'error');
            }
        }

        async function toggleProductReturn(id, btn) {
            try {
                const res = await fetch(`/admin/spices/products/${id}/toggle-return`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const isRet = data.is_returnable;

                    // Update card button
                    if (btn) {
                        btn.className = `px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 ${isRet ? 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200'}`;
                        btn.innerHTML = `<i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i><span>${isRet ? 'Return: On' : 'Return: Off'}</span>`;
                    }

                    // Update card badge
                    const badge = document.getElementById(`spice-return-badge-${id}`);
                    if (badge) {
                        badge.className = `px-2 py-0.5 rounded-md backdrop-blur-xs text-[9px] font-bold flex items-center gap-1 shadow-xs ${isRet ? 'bg-blue-900/85 text-blue-100' : 'bg-rose-900/85 text-rose-100'}`;
                        badge.innerHTML = `<i data-lucide="${isRet ? 'rotate-ccw' : 'shield-alert'}" class="w-3 h-3 ${isRet ? 'text-blue-300' : 'text-rose-300'}"></i><span>${isRet ? 'Return Available' : 'No Return Policy'}</span>`;
                    }

                    // Update notice box on card
                    const notice = document.getElementById(`spice-return-notice-${id}`);
                    if (notice) {
                        if (isRet) {
                            notice.classList.add('hidden');
                            notice.classList.remove('flex');
                        } else {
                            notice.classList.remove('hidden');
                            notice.classList.add('flex');
                        }
                    }

                    if (typeof refreshIcons === 'function') refreshIcons();
                } else {
                    showToast(data.message || 'Error updating product return status', 'error');
                }
            } catch (err) {
                showToast('Failed to toggle product return status', 'error');
            }
        }

        async function deleteSpiceProduct(id, name) {
            if (!confirm(`Are you sure you want to delete spice listing "${name}"?`)) return;
            try {
                const res = await fetch(`/admin/spices/products/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Error deleting product', 'error');
                }
            } catch (err) {
                showToast('Failed to delete spice product', 'error');
            }
        }

        function openSpiceRuleModal(rule = null) {
            const title = document.getElementById('spice-rule-modal-title');
            const idInput = document.getElementById('spice-rule-id');
            const nameInput = document.getElementById('spice-rule-name-input');
            const appliesInput = document.getElementById('spice-rule-applies-select');
            const hoursInput = document.getElementById('spice-rule-hours-input');
            const pctInput = document.getElementById('spice-rule-pct-input');
            const feeInput = document.getElementById('spice-rule-fee-input');
            const descInput = document.getElementById('spice-rule-desc-input');
            const sortInput = document.getElementById('spice-rule-sort-input');
            const activeInput = document.getElementById('spice-rule-active-input');

            if (rule) {
                title.textContent = `Edit Spice Return Tier #${rule.id}`;
                idInput.value = rule.id;
                nameInput.value = rule.name;
                appliesInput.value = rule.applies_to;
                hoursInput.value = rule.time_limit_hours;
                pctInput.value = rule.refund_percentage;
                feeInput.value = rule.handling_fee || 0;
                descInput.value = rule.description;
                sortInput.value = rule.sort_order || 0;
                activeInput.checked = !!rule.is_active;
            } else {
                title.textContent = 'Add Spice Return / Cancellation Tier';
                idInput.value = '';
                nameInput.value = '';
                appliesInput.value = 'before_dispatch';
                hoursInput.value = '24';
                pctInput.value = '100';
                feeInput.value = '0';
                descInput.value = '';
                sortInput.value = '1';
                activeInput.checked = true;
            }

            openModal('modal-spice-return-rule');
        }

        async function submitSpiceRuleForm(e) {
            e.preventDefault();
            const id = document.getElementById('spice-rule-id').value;
            const url = id ? `/admin/spices/return-rules/${id}/update` : `/admin/spices/return-rules`;

            const payload = {
                name: document.getElementById('spice-rule-name-input').value,
                applies_to: document.getElementById('spice-rule-applies-select').value,
                time_limit_hours: document.getElementById('spice-rule-hours-input').value,
                refund_percentage: document.getElementById('spice-rule-pct-input').value,
                handling_fee: document.getElementById('spice-rule-fee-input').value || 0,
                description: document.getElementById('spice-rule-desc-input').value,
                sort_order: document.getElementById('spice-rule-sort-input').value || 0,
                is_active: document.getElementById('spice-rule-active-input').checked ? 1 : 0,
            };

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-spice-return-rule');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Validation error', 'error');
                }
            } catch (err) {
                showToast('Failed to save spice return rule', 'error');
            }
        }

        async function toggleSpiceRuleActive(id) {
            try {
                const res = await fetch(`/admin/spices/return-rules/${id}/toggle`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(data.message || 'Error updating rule', 'error');
                }
            } catch (err) {
                showToast('Failed to toggle return rule', 'error');
            }
        }

        async function deleteSpiceRule(id) {
            if (!confirm('Are you sure you want to delete this spice return rule tier?')) return;
            try {
                const res = await fetch(`/admin/spices/return-rules/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(data.message || 'Error deleting rule', 'error');
                }
            } catch (err) {
                showToast('Failed to delete return rule', 'error');
            }
        }

        async function openSpiceReturnModal(orderId, orderNum, billAmount) {
            document.getElementById('return-proc-order-id').value = orderId;
            document.getElementById('return-proc-order-num').textContent = `#${orderNum}`;
            document.getElementById('return-proc-bill').textContent = `₹${parseFloat(billAmount).toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

            try {
                const res = await fetch(`/admin/spices/orders/${orderId}/calculate-return`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success && data.calculation) {
                    const c = data.calculation;
                    document.getElementById('return-proc-rule-name').textContent = c.rule_name || 'Policy Term';
                    document.getElementById('return-proc-pct').textContent = `${c.percentage}%`;
                    document.getElementById('return-proc-fee').textContent = `₹${c.handling_fee.toFixed(2)}`;
                    document.getElementById('return-proc-cashback').textContent = `₹${c.cashback.toFixed(2)}`;
                    document.getElementById('return-proc-amount-input').value = c.cashback;
                    document.getElementById('return-proc-reason').textContent = (data.order && data.order.cancellation_reason) ? data.order.cancellation_reason : 'Customer requested return';
                }
            } catch (err) {
                console.error(err);
            }

            openModal('modal-spice-process-return');
        }

        async function submitSpiceReturnDecision(action) {
            const orderId = document.getElementById('return-proc-order-id').value;
            const refundAmount = document.getElementById('return-proc-amount-input').value;
            const notes = document.getElementById('return-proc-notes-input').value;

            try {
                const res = await fetch(`/admin/spices/orders/${orderId}/process-return`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({
                        action: action,
                        refund_amount: refundAmount,
                        manager_notes: notes
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-spice-process-return');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Error processing return', 'error');
                }
            } catch (err) {
                showToast('Failed to process return request', 'error');
            }
        }

        async function quickProcessSpiceReturn(orderId, action, amount = null) {
            const promptMsg = action === 'approve'
                ? `Approve return with ₹${amount || 0} cashback refund?`
                : 'Are you sure you want to reject this return request?';
            if (!confirm(promptMsg)) return;

            try {
                const res = await fetch(`/admin/spices/orders/${orderId}/process-return`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({
                        action: action,
                        refund_amount: amount,
                        manager_notes: action === 'approve' ? '1-Click approved by Manager' : 'Declined by Manager'
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Error processing return', 'error');
                }
            } catch (err) {
                showToast('Failed to process return', 'error');
            }
        }

        async function updateSpicePolicyCopy(e) {
            e.preventDefault();
            const text = document.getElementById('spice-policy-text-input').value.trim();
            const btn = document.getElementById('btn-save-spice-policy');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            try {
                const res = await fetch('/admin/settings/spice-return-policy', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ description: text })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Error saving policy', 'error');
                }
            } catch (err) {
                showToast('Failed to save spice return policy', 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Save Policy Description';
            }
        }

        // ================= RESORT CONTENT TABS =================
        function switchResortTab(tab) {
            ['branches', 'facilities', 'gallery', 'nearby', 'taxi'].forEach(t => {
                const el = document.getElementById(`resort-tab-${t}`);
                const btn = document.getElementById(`resort-tab-btn-${t}`);
                if (el) el.classList.add('hidden');
                if (btn) btn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition';
            });
            const activeEl = document.getElementById(`resort-tab-${tab}`);
            const activeBtn = document.getElementById(`resort-tab-btn-${tab}`);
            if (activeEl) activeEl.classList.remove('hidden');
            if (activeBtn) activeBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition';
        }

        // ================= RESORT FACILITIES & SCHEDULING =================
        function switchFacilitySubView(subview) {
            const dir = document.getElementById('fac-subview-directory');
            const bks = document.getElementById('fac-subview-bookings');
            const btnDir = document.getElementById('btn-fac-subview-directory');
            const btnBks = document.getElementById('btn-fac-subview-bookings');

            if (!dir || !bks) return;

            if (subview === 'directory') {
                dir.classList.remove('hidden');
                bks.classList.add('hidden');
                btnDir.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white shadow-xs transition';
                btnBks.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition flex items-center gap-1.5';
            } else {
                dir.classList.add('hidden');
                bks.classList.remove('hidden');
                btnBks.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5';
                btnDir.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition';
            }
            if (window.lucide) lucide.createIcons();
        }

        function filterFacilityBookings() {
            const val = document.getElementById('filter-fac-booking-status').value.toLowerCase();
            document.querySelectorAll('.fac-booking-row').forEach(row => {
                const status = row.getAttribute('data-status') || '';
                if (!val || status === val) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openAllocateSlotModal(bookingId, guestName, facilityName, currentSlot, currentStatus, totalAmount, isFolioBilled) {
            document.getElementById('alloc-booking-id').value = bookingId;
            document.getElementById('alloc-guest-name').textContent = guestName;
            document.getElementById('alloc-facility-name').textContent = facilityName;
            document.getElementById('alloc-time-slot').value = currentSlot || '';
            document.getElementById('alloc-status').value = currentStatus || 'confirmed';

            const amtDisplay = document.getElementById('alloc-total-amount');
            const folioBox = document.getElementById('alloc-folio-charge-container');

            if (totalAmount > 0) {
                amtDisplay.textContent = '₹' + totalAmount.toFixed(2);
                if (!isFolioBilled) {
                    folioBox.classList.remove('hidden');
                    document.getElementById('alloc-charge-folio').checked = true;
                } else {
                    folioBox.classList.add('hidden');
                }
            } else {
                amtDisplay.textContent = 'Complimentary';
                folioBox.classList.add('hidden');
            }

            openModal('modal-allocate-slot');
            if (window.lucide) lucide.createIcons();
        }

        function setSlotPreset(slotText) {
            document.getElementById('alloc-time-slot').value = slotText;
        }

        async function submitSlotAllocation(event) {
            event.preventDefault();
            const bookingId = document.getElementById('alloc-booking-id').value;
            const timeSlot = document.getElementById('alloc-time-slot').value.trim();
            const status = document.getElementById('alloc-status').value;
            const chargeFolio = document.getElementById('alloc-charge-folio') ? document.getElementById('alloc-charge-folio').checked : false;

            const btn = document.getElementById('btn-save-allocation');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Saving...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            try {
                const res = await fetch(`/admin/facilities/bookings/${bookingId}/allocate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        allocated_time_slot: timeSlot,
                        status: status,
                        charge_room_folio: chargeFolio
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    showToast(data.message || 'Slot allocated successfully!', 'success');
                    closeModal('modal-allocate-slot');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to allocate slot.', 'error');
                }
            } catch(err) {
                console.error(err);
                showToast('Server error while allocating slot.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        }

        // ================= COMMUNICATIONS & REVIEWS =================
        function switchCommTab(tab) {
            const tabMsgs = document.getElementById('comm-tab-messages');
            const tabRevs = document.getElementById('comm-tab-reviews');
            const btnMsgs = document.getElementById('comm-tab-btn-messages');
            const btnRevs = document.getElementById('comm-tab-btn-reviews');

            if (tab === 'messages') {
                tabMsgs.classList.remove('hidden');
                tabRevs.classList.add('hidden');
                btnMsgs.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition';
                btnRevs.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition';
            } else {
                tabMsgs.classList.add('hidden');
                tabRevs.classList.remove('hidden');
                btnRevs.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition';
                btnMsgs.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition';
            }
        }

        /* =========================================================================
           DIRECT GUEST MESSAGING & STAFF LOCKING PLATFORM (ADMIN ENGINE)
           ========================================================================= */
        let currentEnquiryId = {{ $enquiries->first() ? $enquiries->first()->id : 'null' }};
        let attachedCards = [];
        let chatTemplatesData = [];
        let chatCatalogsData = {};
        let adminChatPollingTimer = null;
        let activePickerTab = 'actions';

        // Load templates and entity catalogs on DOM ready & start live chat polling
        document.addEventListener('DOMContentLoaded', () => {
            loadChatTemplates();
            startAdminChatPolling();
            fetchAdminNotifications();
        });

        let _lastThreadsDigest = null;
        let _threadPollCounter = 0;

        function startAdminChatPolling() {
            if (adminChatPollingTimer) clearInterval(adminChatPollingTimer);
            adminChatPollingTimer = setInterval(async () => {
                const msgsSection = document.getElementById('messages');
                _threadPollCounter++;

                // Poll threads list every 8 seconds (every 2nd tick) when visible or unread
                if (_threadPollCounter % 2 === 0) {
                    refreshAdminEnquiryThreads();
                }

                // Poll real-time notifications feed every 12 seconds (every 3rd tick)
                if (_threadPollCounter % 3 === 0) {
                    fetchAdminNotifications();
                }

                if (!msgsSection || msgsSection.classList.contains('hidden') || !currentEnquiryId) return;

                const replyInput = document.getElementById('reply-message-input');
                if (replyInput && document.activeElement === replyInput && replyInput.value.length > 0) return;

                try {
                    const res = await fetch(`/admin/chat/${currentEnquiryId}/data`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success && data.enquiry && data.enquiry.id === currentEnquiryId) {
                        const container = document.getElementById('enquiry-chat-messages');
                        const currentMsgCount = container ? container.children.length : 0;
                        const newMsgCount = (data.messages || []).length;
                        
                        if (newMsgCount !== currentMsgCount || data.enquiry.is_locked !== (window._lastEnqLockedState ?? null)) {
                            window._lastEnqLockedState = data.enquiry.is_locked;
                            renderAdminChatConversation(data);
                        }
                    }
                } catch (e) {
                    // silent poll
                }
            }, 4000);
        }

        async function refreshAdminEnquiryThreads() {
            try {
                const res = await fetch('/admin/chat/threads', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success && data.threads) {
                    const digest = data.threads.map(t => `${t.id}:${t.has_unread_messages}:${t.messages_count}:${t.is_locked}:${t.status}`).join('|');
                    if (digest !== _lastThreadsDigest) {
                        _lastThreadsDigest = digest;
                        renderAdminThreadsList(data.threads);
                    }
                }
            } catch (err) {
                // silent
            }
        }

        function renderAdminThreadsList(threads) {
            const listContainer = document.getElementById('enquiry-threads-list');
            if (!listContainer) return;

            if (!threads || threads.length === 0) {
                listContainer.innerHTML = '<div class="p-8 text-center text-xs text-brand-muted">No active direct message threads.</div>';
                return;
            }

            const searchInput = document.getElementById('thread-search-input');
            const searchVal = searchInput ? searchInput.value.trim().toLowerCase() : '';

            listContainer.innerHTML = threads.map((enq, idx) => {
                const isSelected = (currentEnquiryId === enq.id) || (!currentEnquiryId && idx === 0);
                const searchStr = `${enq.customer_name} ${enq.ticket_number} ${enq.subject || ''} ${enq.topic || ''}`.toLowerCase();
                const isHidden = searchVal && !searchStr.includes(searchVal);

                let lockBadge = '';
                if (enq.is_locked) {
                    if (enq.is_locked_by_me) {
                        lockBadge = `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                            <i data-lucide="lock" class="w-3 h-3"></i> Locked to You
                        </span>`;
                    } else {
                        lockBadge = `<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 flex items-center gap-1">
                            <i data-lucide="lock" class="w-3 h-3 text-amber-700"></i> ${escapeHtml(enq.locked_by_name || 'Staff')}
                        </span>`;
                    }
                } else {
                    lockBadge = `<span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 flex items-center gap-1">
                        <i data-lucide="unlock" class="w-3 h-3 text-gray-400"></i> Open
                    </span>`;
                }

                let topicPill = '';
                if (enq.topic && enq.topic !== 'general') {
                    topicPill = `<span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200 uppercase">${escapeHtml(enq.topic.replace('_', ' '))}</span>`;
                }

                return `
                <div class="p-3.5 hover:bg-brand-canvas/70 cursor-pointer transition relative ${isSelected ? 'bg-brand-canvas border-l-3 border-brand-primary' : ''} ${isHidden ? 'hidden' : ''}" onclick="selectEnquiryThread(${enq.id})" id="enq-thread-item-${enq.id}" data-search="${escapeHtml(searchStr)}">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 truncate">
                            ${enq.has_unread_messages ? '<span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 animate-pulse" title="Unread Message"></span>' : ''}
                            <span class="font-bold text-xs text-brand-text truncate">${escapeHtml(enq.customer_name)}</span>
                        </div>
                        <span class="text-[10px] text-brand-muted shrink-0">${escapeHtml(enq.last_time || '')}</span>
                    </div>

                    <div class="text-xs text-brand-primary font-medium mt-1 flex items-center gap-1.5 truncate">
                        <span class="font-mono text-[10px] text-gray-500">${escapeHtml(enq.ticket_number)}</span>
                        <span>&middot;</span>
                        <span class="truncate">${escapeHtml(enq.subject || 'Direct Chat')}</span>
                    </div>

                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                        ${topicPill}
                        <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">${escapeHtml(enq.branch_code)}</span>
                    </div>

                    <div class="text-[11px] text-brand-muted truncate mt-1">
                        ${escapeHtml(enq.last_message || '')}
                    </div>

                    <div class="mt-2.5 flex items-center justify-between gap-1.5 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            ${lockBadge}
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                ${escapeHtml(enq.status.charAt(0).toUpperCase() + enq.status.slice(1))}
                            </span>
                        </div>
                        <span class="text-[10px] font-semibold text-brand-muted">${enq.messages_count} msgs</span>
                    </div>
                </div>
                `;
            }).join('');

            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        // ================= REAL-TIME NOTIFICATIONS & CALLMEBOT WHATSAPP =================
        let _adminNotifications = [];
        let _currentNotifFilter = 'all';
        let _knownNotifIds = new Set();
        let _notifFirstLoad = true;

        function toggleNotificationsDropdown() {
            const dropdown = document.getElementById('admin-notifications-dropdown');
            if (!dropdown) return;
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                fetchAdminNotifications();
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function closeNotificationsDropdown() {
            const dropdown = document.getElementById('admin-notifications-dropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const container = document.getElementById('admin-notifications-container');
            if (container && !container.contains(e.target)) {
                closeNotificationsDropdown();
            }
        });

        async function fetchAdminNotifications() {
            try {
                const branchSelector = document.getElementById('global-branch-selector');
                const branchId = branchSelector ? branchSelector.value : 'all';
                const res = await fetch(`/admin/api/notifications?branch_id=${encodeURIComponent(branchId)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    _adminNotifications = data.notifications || [];
                    updateNotificationBadgesAndCounts(data);

                    // Check for newly arrived alerts to trigger toast alert
                    if (!_notifFirstLoad) {
                        const newAlerts = _adminNotifications.filter(n => n.unread && !_knownNotifIds.has(n.id));
                        if (newAlerts.length > 0) {
                            const latest = newAlerts[0];
                            showToastNotification(`🛎️ ${latest.title}`, latest.subtitle);
                        }
                    }
                    _notifFirstLoad = false;
                    _adminNotifications.forEach(n => _knownNotifIds.add(n.id));

                    renderAdminNotificationsList(_currentNotifFilter);
                }
            } catch (err) {
                // silent background poll
            }
        }

        function updateNotificationBadgesAndCounts(data) {
            const badge = document.getElementById('admin-notif-badge');
            const totalUnread = data.total_unread ?? 0;
            if (badge) {
                if (totalUnread > 0) {
                    badge.textContent = totalUnread > 99 ? '99+' : totalUnread;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            const counts = data.counts_by_category || {};
            ['all', 'stays', 'dining', 'messages', 'services', 'shop'].forEach(cat => {
                const el = document.getElementById(`notif-cnt-${cat}`);
                if (el) el.textContent = counts[cat] ?? 0;
            });
        }

        function filterAdminNotifications(category) {
            _currentNotifFilter = category;
            document.querySelectorAll('.notif-filter-pill').forEach(btn => {
                btn.classList.remove('bg-brand-primary', 'text-white', 'font-bold');
                btn.classList.add('bg-gray-200/70', 'text-brand-muted', 'font-semibold');
            });
            const activeBtn = document.getElementById(`notif-tab-${category}`);
            if (activeBtn) {
                activeBtn.classList.remove('bg-gray-200/70', 'text-brand-muted', 'font-semibold');
                activeBtn.classList.add('bg-brand-primary', 'text-white', 'font-bold');
            }
            renderAdminNotificationsList(category);
        }

        function renderAdminNotificationsList(filterCat) {
            const container = document.getElementById('admin-notif-list');
            if (!container) return;

            let items = _adminNotifications;
            if (filterCat && filterCat !== 'all') {
                items = items.filter(n => n.category === filterCat);
            }

            if (!items || items.length === 0) {
                container.innerHTML = `
                    <div class="py-12 px-4 text-center text-brand-muted">
                        <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500 mx-auto mb-2 opacity-70"></i>
                        <p class="font-bold text-xs text-brand-text">All Clear!</p>
                        <p class="text-[11px] text-brand-muted mt-0.5">No pending customer alerts in this category.</p>
                    </div>
                `;
                if (window.lucide) window.lucide.createIcons();
                return;
            }

            const colorMap = {
                emerald: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                amber: 'bg-amber-50 text-amber-700 border-amber-200',
                orange: 'bg-orange-50 text-orange-700 border-orange-200',
                blue: 'bg-blue-50 text-blue-700 border-blue-200',
                teal: 'bg-teal-50 text-teal-700 border-teal-200',
                indigo: 'bg-indigo-50 text-indigo-700 border-indigo-200',
                rose: 'bg-rose-50 text-rose-700 border-rose-200'
            };

            container.innerHTML = items.map(n => {
                const colorClass = colorMap[n.color] || colorMap.emerald;
                return `
                    <div onclick="handleNotificationCardClick('${n.action_section}', '${n.action_type}', ${n.action_id})" class="p-3 hover:bg-brand-canvas/80 transition cursor-pointer flex items-start gap-3 ${n.unread ? 'bg-amber-50/20' : ''}">
                        <div class="w-8 h-8 rounded-xl ${colorClass} border flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                            <i data-lucide="${n.icon || 'bell'}" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-bold text-xs text-brand-text truncate">${escapeHtml(n.title)}</span>
                                <span class="text-[10px] text-brand-muted shrink-0">${escapeHtml(n.time_ago)}</span>
                            </div>
                            <p class="text-[11px] text-brand-muted mt-0.5 line-clamp-1">${escapeHtml(n.subtitle)}</p>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="text-[10px] font-semibold text-brand-primary font-mono">${escapeHtml(n.meta)}</span>
                                <span class="text-[10px] font-bold text-brand-primary hover:underline flex items-center gap-0.5">
                                    Action <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            if (window.lucide) window.lucide.createIcons();
        }

        async function markAllNotificationsRead() {
            try {
                const res = await fetch('/admin/api/notifications/mark-read', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const badge = document.getElementById('admin-notif-badge');
                    if (badge) badge.classList.add('hidden');
                    _adminNotifications.forEach(n => n.unread = false);
                    renderAdminNotificationsList(_currentNotifFilter);
                    showToastNotification('✓ Acknowledged', 'All operational alerts marked as read.');
                }
            } catch (err) {
                console.error(err);
            }
        }

        function handleNotificationCardClick(section, type, id) {
            closeNotificationsDropdown();
            if (section) {
                navigateTo(section);
            }

            if (type === 'open_reservation' && id) {
                setTimeout(() => {
                    if (typeof openReservationDrawer === 'function') openReservationDrawer(id);
                }, 200);
            } else if (type === 'open_enquiry' && id) {
                setTimeout(() => {
                    if (typeof selectEnquiryThread === 'function') selectEnquiryThread(id);
                }, 200);
            } else if (type === 'open_spice_returns') {
                setTimeout(() => {
                    if (typeof switchSpiceTab === 'function') switchSpiceTab('policy');
                }, 200);
            }
        }

        function showToastNotification(title, message) {
            let container = document.getElementById('admin-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'admin-toast-container';
                container.className = 'fixed bottom-5 right-5 z-80 flex flex-col gap-2 max-w-sm w-full pointer-events-none';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto bg-brand-deep text-white border border-brand-accent/40 p-3.5 rounded-xl shadow-2xl flex items-start gap-3 transition-all duration-300 transform translate-y-3 opacity-0';
            toast.innerHTML = `
                <div class="w-8 h-8 rounded-lg bg-brand-accent/20 text-brand-accent flex items-center justify-center shrink-0 mt-0.5">
                    <i data-lucide="bell-ring" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-xs text-white">${escapeHtml(title)}</div>
                    <div class="text-[11px] text-white/80 mt-0.5 truncate">${escapeHtml(message)}</div>
                </div>
                <button type="button" class="text-white/40 hover:text-white p-1 cursor-pointer" onclick="this.parentElement.remove()">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            `;
            container.appendChild(toast);
            if (window.lucide) window.lucide.createIcons();

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-3', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('translate-y-3', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4500);
        }

        // ================= SMTP EMAIL NOTIFICATIONS =================
        async function handleEmailSettingsSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-email');
            const text = document.getElementById('btn-save-email-text');
            if (btn) btn.disabled = true;
            if (text) text.textContent = 'Saving...';

            const payload = {
                smtp_notifications_enabled: document.getElementById('smtp_enabled').value === '1',
                smtp_host: document.getElementById('smtp_host').value.trim(),
                smtp_port: parseInt(document.getElementById('smtp_port').value) || 587,
                smtp_encryption: document.getElementById('smtp_encryption').value,
                smtp_username: document.getElementById('smtp_username').value.trim(),
                smtp_password: document.getElementById('smtp_password').value.trim(),
                smtp_from_address: document.getElementById('smtp_from_address').value.trim(),
                smtp_from_name: document.getElementById('smtp_from_name').value.trim(),
                smtp_recipient_email: document.getElementById('smtp_recipient_email').value.trim(),
            };

            try {
                const res = await fetch('/admin/settings/email-notifications', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showToastNotification('✓ Saved', data.message);
                } else {
                    alert(data.message || 'Failed to save SMTP settings.');
                }
            } catch (err) {
                alert('Network error saving SMTP settings.');
            } finally {
                if (btn) btn.disabled = false;
                if (text) text.textContent = 'Save Email Configuration';
            }
        }

        async function testEmailConnectionAdmin() {
            const host = document.getElementById('smtp_host')?.value.trim();
            const port = parseInt(document.getElementById('smtp_port')?.value) || 587;
            const encryption = document.getElementById('smtp_encryption')?.value;
            const username = document.getElementById('smtp_username')?.value.trim();
            const password = document.getElementById('smtp_password')?.value.trim();
            const fromAddress = document.getElementById('smtp_from_address')?.value.trim();
            const fromName = document.getElementById('smtp_from_name')?.value.trim();
            const recipient = document.getElementById('smtp_recipient_email')?.value.trim();

            const resultBox = document.getElementById('email-test-result');
            const btn = document.getElementById('btn-test-email');
            const btnText = document.getElementById('btn-test-email-text');

            if (!host || !username || !recipient) {
                alert('Please enter SMTP Host, Username, and Recipient Email before testing.');
                return;
            }

            if (btn) btn.disabled = true;
            if (btnText) btnText.textContent = 'Sending Test Email...';
            if (resultBox) {
                resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-blue-50 text-blue-800 border-blue-200';
                resultBox.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin text-blue-600"></i> Dispatching test email via SMTP server...';
                resultBox.classList.remove('hidden');
                if (window.lucide) window.lucide.createIcons();
            }

            try {
                const res = await fetch('/admin/settings/email-notifications/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        smtp_host: host,
                        smtp_port: port,
                        smtp_encryption: encryption,
                        smtp_username: username,
                        smtp_password: password,
                        smtp_from_address: fromAddress,
                        smtp_from_name: fromName,
                        recipient_email: recipient,
                    })
                });
                const data = await res.json();
                if (data.success) {
                    resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-emerald-50 text-emerald-800 border-emerald-300';
                    resultBox.innerHTML = '<i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i> Test email delivered successfully to ' + escapeHtml(recipient) + '! Check your inbox / spam.';
                } else {
                    resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-rose-50 text-rose-800 border-rose-300';
                    resultBox.innerHTML = '<i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i> SMTP Error: ' + escapeHtml(data.error || 'Check host, port and password.');
                }
            } catch (err) {
                resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-rose-50 text-rose-800 border-rose-300';
                resultBox.innerHTML = '<i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i> Network connection error while calling email test endpoint.';
            } finally {
                if (btn) btn.disabled = false;
                if (btnText) btnText.textContent = 'Send Test Email';
                if (window.lucide) window.lucide.createIcons();
            }
        }

        // ================= TELEGRAM BOT NOTIFICATIONS =================
        async function handleTelegramSettingsSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-telegram');
            const text = document.getElementById('btn-save-telegram-text');
            if (btn) btn.disabled = true;
            if (text) text.textContent = 'Saving...';

            const payload = {
                telegram_notifications_enabled: document.getElementById('tg_enabled').value === '1',
                telegram_bot_token: document.getElementById('tg_bot_token').value.trim(),
                telegram_chat_id: document.getElementById('tg_chat_id').value.trim(),
            };

            try {
                const res = await fetch('/admin/settings/telegram', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showToastNotification('✓ Saved', data.message);
                } else {
                    alert(data.message || 'Failed to save Telegram settings.');
                }
            } catch (err) {
                alert('Network error saving Telegram settings.');
            } finally {
                if (btn) btn.disabled = false;
                if (text) text.textContent = 'Save Telegram Configuration';
            }
        }

        async function testTelegramConnectionAdmin() {
            const token = document.getElementById('tg_bot_token')?.value.trim();
            const chatId = document.getElementById('tg_chat_id')?.value.trim();
            const resultBox = document.getElementById('telegram-test-result');
            const btn = document.getElementById('btn-test-telegram');
            const btnText = document.getElementById('btn-test-telegram-text');

            if (!chatId) {
                alert('Please enter your Telegram Chat ID / Channel ID before testing.');
                return;
            }

            if (btn) btn.disabled = true;
            if (btnText) btnText.textContent = 'Pinging Telegram...';
            if (resultBox) {
                resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-blue-50 text-blue-800 border-blue-200';
                resultBox.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin text-blue-600"></i> Dispatching test message via Telegram Bot API...';
                resultBox.classList.remove('hidden');
                if (window.lucide) window.lucide.createIcons();
            }

            try {
                const res = await fetch('/admin/settings/telegram/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ bot_token: token, chat_id: chatId })
                });
                const data = await res.json();
                if (data.success) {
                    resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-emerald-50 text-emerald-800 border-emerald-300';
                    resultBox.innerHTML = '<i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i> Test alert sent successfully to Telegram Chat ' + escapeHtml(chatId) + '! Check your Telegram app.';
                } else {
                    resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-rose-50 text-rose-800 border-rose-300';
                    resultBox.innerHTML = '<i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i> Telegram Error: ' + escapeHtml(data.error || 'Check your Bot Token and Chat ID.');
                }
            } catch (err) {
                resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-rose-50 text-rose-800 border-rose-300';
                resultBox.innerHTML = '<i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i> Network connection error while calling Telegram Bot API.';
            } finally {
                if (btn) btn.disabled = false;
                if (btnText) btnText.textContent = 'Send Test Telegram Ping';
                if (window.lucide) window.lucide.createIcons();
            }
        }

        async function loadChatTemplates() {
            try {
                const res = await fetch('/admin/chat/templates', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    chatTemplatesData = data.templates || [];
                    chatCatalogsData = data.catalogs || {};
                }
            } catch (err) {
                console.error('Failed to load chat templates:', err);
            }
        }

        /* -------------------------------------------------------------------------
           POPOVER PICKER & MULTI-CARD SELECTION ENGINE
           ------------------------------------------------------------------------- */
        function updatePickerAttachedBadge() {
            const badge = document.getElementById('picker-attached-badge');
            if (badge) {
                badge.textContent = `${attachedCards.length} Attached`;
                if (attachedCards.length > 0) {
                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-700 text-white shadow-xs';
                } else {
                    badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800';
                }
            }
        }

        function toggleCardActionPicker() {
            const popover = document.getElementById('card-action-picker-popover');
            if (!popover) return;
            const isHidden = popover.classList.contains('hidden');
            if (isHidden) {
                popover.classList.remove('hidden');
                updatePickerAttachedBadge();
                renderPickerItems();
                refreshIcons();
            } else {
                popover.classList.add('hidden');
            }
        }

        function openPickerWithTab(tab) {
            const popover = document.getElementById('card-action-picker-popover');
            if (popover) {
                popover.classList.remove('hidden');
                selectPickerTab(tab);
                updatePickerAttachedBadge();
            }
        }

        function selectPickerTab(tab) {
            activePickerTab = tab;
            ['actions', 'rooms', 'branches', 'spices', 'dining', 'nearby'].forEach(t => {
                const btn = document.getElementById(`picker-tab-btn-${t}`);
                if (btn) {
                    if (t === tab) {
                        btn.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-brand-primary text-white transition shrink-0';
                    } else {
                        btn.className = 'px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0';
                    }
                }
            });
            renderPickerItems();
        }

        function filterCardPickerItems() {
            renderPickerItems();
        }

        function renderPickerItems() {
            const grid = document.getElementById('picker-items-grid');
            if (!grid) return;
            const query = (document.getElementById('picker-search-input')?.value || '').toLowerCase().trim();

            grid.innerHTML = '';
            updatePickerAttachedBadge();

            if (activePickerTab === 'actions') {
                const filtered = chatTemplatesData.filter(t => 
                    !query || t.title.toLowerCase().includes(query) || (t.category || '').toLowerCase().includes(query) || (t.message || '').toLowerCase().includes(query)
                );
                if (filtered.length === 0) {
                    grid.innerHTML = '<div class="col-span-1 sm:col-span-2 text-center text-xs text-brand-muted py-6">No quick action templates match your search.</div>';
                    return;
                }
                filtered.forEach(t => {
                    const isAttached = t.type !== 'text' && attachedCards.some(c => c.card_type === t.type && String(c.entity_id) === String(t.entity_id));
                    const card = document.createElement('div');
                    card.className = `p-2.5 rounded-xl border transition flex flex-col justify-between space-y-1.5 ${isAttached ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500/60 shadow-xs' : 'border-gray-200 bg-gray-50/70 hover:bg-white hover:border-emerald-300'}`;
                    card.innerHTML = `
                        <div>
                            <div class="flex items-center justify-between gap-1.5">
                                <span class="font-bold text-xs text-brand-text truncate">${escapeAdminHtml(t.title)}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-200 text-gray-700 uppercase shrink-0">${escapeAdminHtml(t.category || 'General')}</span>
                            </div>
                            ${t.message ? `<p class="text-[10px] text-brand-muted mt-1 line-clamp-2">${escapeAdminHtml(t.message)}</p>` : ''}
                        </div>
                        <div class="pt-1 flex items-center justify-between">
                            <span class="text-[10px] font-semibold text-emerald-800">${t.type !== 'text' ? '📇 With ' + t.type + ' card' : '💬 Text reply'}</span>
                            <button type="button" onclick="applyTemplateToMessage(${t.id})" class="px-2.5 py-1 ${isAttached ? 'bg-emerald-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'} rounded-lg text-[10px] font-bold transition flex items-center gap-1 cursor-pointer">
                                ${isAttached ? '✓ Applied' : '+ Apply'}
                            </button>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            } else if (activePickerTab === 'rooms') {
                const rooms = (chatCatalogsData.room_types || []).filter(r => 
                    !query || r.name.toLowerCase().includes(query) || (r.branch ? r.branch.name.toLowerCase().includes(query) : false)
                );
                if (rooms.length === 0) {
                    grid.innerHTML = '<div class="col-span-1 sm:col-span-2 text-center text-xs text-brand-muted py-6">No room types found.</div>';
                    return;
                }
                rooms.forEach(r => {
                    const isAttached = attachedCards.some(c => c.card_type === 'room' && String(c.entity_id) === String(r.id));
                    const card = document.createElement('div');
                    card.className = `p-2.5 rounded-xl border transition flex items-center gap-2.5 ${isAttached ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500/60 shadow-xs' : 'border-gray-200 bg-white hover:border-emerald-300'}`;
                    card.innerHTML = `
                        ${r.cover_image_url ? `<img src="${r.cover_image_url}" class="w-12 h-12 rounded-lg object-cover shrink-0 bg-gray-100">` : `<div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-800 font-bold text-xs shrink-0">🏨</div>`}
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-xs text-brand-text truncate">${escapeAdminHtml(r.name)}</h5>
                            <p class="text-[10px] text-brand-muted truncate">${r.branch ? escapeAdminHtml(r.branch.name) : 'Resort'} &middot; ₹${Number(r.base_price).toLocaleString()}/night</p>
                        </div>
                        <button type="button" onclick="toggleCardAttachment('room', ${r.id}, '${escapeAdminHtml(r.name)}', '${r.branch ? escapeAdminHtml(r.branch.name) : ''} · Up to ${r.max_guests} Guests', '₹${Number(r.base_price).toLocaleString()}', '${r.cover_image_url || ''}', '/rooms/${r.slug}', 'View Room Details', 'Verified Cottage')" class="px-2.5 py-1 ${isAttached ? 'bg-emerald-700 text-white shadow-xs' : 'bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800'} rounded-lg text-[10px] font-bold shrink-0 transition cursor-pointer flex items-center gap-1">
                            ${isAttached ? '✓ Added' : '+ Add'}
                        </button>
                    `;
                    grid.appendChild(card);
                });
            } else if (activePickerTab === 'branches') {
                const branches = (chatCatalogsData.branches || []).filter(b => 
                    !query || b.name.toLowerCase().includes(query) || (b.city || '').toLowerCase().includes(query)
                );
                if (branches.length === 0) {
                    grid.innerHTML = '<div class="col-span-1 sm:col-span-2 text-center text-xs text-brand-muted py-6">No branches found.</div>';
                    return;
                }
                branches.forEach(b => {
                    const isAttached = attachedCards.some(c => c.card_type === 'branch' && String(c.entity_id) === String(b.id));
                    const card = document.createElement('div');
                    card.className = `p-2.5 rounded-xl border transition flex items-center gap-2.5 ${isAttached ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500/60 shadow-xs' : 'border-gray-200 bg-white hover:border-emerald-300'}`;
                    card.innerHTML = `
                        ${(b.hero_image_url || b.cover_image_url) ? `<img src="${b.hero_image_url || b.cover_image_url}" class="w-12 h-12 rounded-lg object-cover shrink-0 bg-gray-100">` : `<div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-800 font-bold text-xs shrink-0">📍</div>`}
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-xs text-brand-text truncate">${escapeAdminHtml(b.name)}</h5>
                            <p class="text-[10px] text-brand-muted truncate">${escapeAdminHtml(b.city || '')} &middot; ${escapeAdminHtml(b.tagline || 'Cottage Location')}</p>
                        </div>
                        <button type="button" onclick="toggleCardAttachment('branch', ${b.id}, '${escapeAdminHtml(b.name)}', '${escapeAdminHtml(b.city || '')} · Luxury Cottages of Kerala', '', '${b.hero_image_url || b.cover_image_url || ''}', '/stay?branch_id=${b.id}', 'Explore Branch & Stays', 'Cottage Branch')" class="px-2.5 py-1 ${isAttached ? 'bg-emerald-700 text-white shadow-xs' : 'bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800'} rounded-lg text-[10px] font-bold shrink-0 transition cursor-pointer flex items-center gap-1">
                            ${isAttached ? '✓ Added' : '+ Add'}
                        </button>
                    `;
                    grid.appendChild(card);
                });
            } else if (activePickerTab === 'spices') {
                const spices = (chatCatalogsData.spices || []).filter(s => 
                    !query || s.name.toLowerCase().includes(query)
                );
                if (spices.length === 0) {
                    grid.innerHTML = '<div class="col-span-1 sm:col-span-2 text-center text-xs text-brand-muted py-6">No spices found.</div>';
                    return;
                }
                spices.forEach(s => {
                    const isAttached = attachedCards.some(c => c.card_type === 'spice' && String(c.entity_id) === String(s.id));
                    const card = document.createElement('div');
                    card.className = `p-2.5 rounded-xl border transition flex items-center gap-2.5 ${isAttached ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500/60 shadow-xs' : 'border-gray-200 bg-white hover:border-emerald-300'}`;
                    card.innerHTML = `
                        ${s.image_url ? `<img src="${s.image_url}" class="w-12 h-12 rounded-lg object-cover shrink-0 bg-gray-100">` : `<div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-800 font-bold text-xs shrink-0">🌿</div>`}
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-xs text-brand-text truncate">${escapeAdminHtml(s.name)}</h5>
                            <p class="text-[10px] text-brand-muted truncate">${s.package_size || '250g'} &middot; ₹${Number(s.price).toLocaleString()}</p>
                        </div>
                        <button type="button" onclick="toggleCardAttachment('spice', ${s.id}, '${escapeAdminHtml(s.name)}', 'Pack Size: ${s.package_size || '250g'}', '₹${Number(s.price).toLocaleString()}', '${s.image_url || ''}', '/spices', 'View in Spices Shop', 'Organic Kerala Spice')" class="px-2.5 py-1 ${isAttached ? 'bg-emerald-700 text-white shadow-xs' : 'bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800'} rounded-lg text-[10px] font-bold shrink-0 transition cursor-pointer flex items-center gap-1">
                            ${isAttached ? '✓ Added' : '+ Add'}
                        </button>
                    `;
                    grid.appendChild(card);
                });
            } else if (activePickerTab === 'dining') {
                const dining = (chatCatalogsData.dining || []).filter(d => 
                    !query || d.name.toLowerCase().includes(query)
                );
                if (dining.length === 0) {
                    grid.innerHTML = '<div class="col-span-1 sm:col-span-2 text-center text-xs text-brand-muted py-6">No dining items found.</div>';
                    return;
                }
                dining.forEach(d => {
                    const isAttached = attachedCards.some(c => c.card_type === 'dining' && String(c.entity_id) === String(d.id));
                    const card = document.createElement('div');
                    card.className = `p-2.5 rounded-xl border transition flex items-center gap-2.5 ${isAttached ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500/60 shadow-xs' : 'border-gray-200 bg-white hover:border-emerald-300'}`;
                    card.innerHTML = `
                        <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center text-amber-800 font-bold text-xs shrink-0">🍲</div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-xs text-brand-text truncate">${escapeAdminHtml(d.name)}</h5>
                            <p class="text-[10px] text-brand-muted truncate">₹${Number(d.price).toLocaleString()}</p>
                        </div>
                        <button type="button" onclick="toggleCardAttachment('dining', ${d.id}, '${escapeAdminHtml(d.name)}', 'Authentic Plantation Dining', '₹${Number(d.price).toLocaleString()}', '', '/dining', 'View Dining Menu', 'Ayurvedic Special')" class="px-2.5 py-1 ${isAttached ? 'bg-emerald-700 text-white shadow-xs' : 'bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800'} rounded-lg text-[10px] font-bold shrink-0 transition cursor-pointer flex items-center gap-1">
                            ${isAttached ? '✓ Added' : '+ Add'}
                        </button>
                    `;
                    grid.appendChild(card);
                });
            } else if (activePickerTab === 'nearby') {
                const nearby = (chatCatalogsData.nearby || []).filter(n => 
                    !query || n.name.toLowerCase().includes(query)
                );
                if (nearby.length === 0) {
                    grid.innerHTML = '<div class="col-span-1 sm:col-span-2 text-center text-xs text-brand-muted py-6">No excursions found.</div>';
                    return;
                }
                nearby.forEach(n => {
                    const isAttached = attachedCards.some(c => c.card_type === 'nearby' && String(c.entity_id) === String(n.id));
                    const card = document.createElement('div');
                    card.className = `p-2.5 rounded-xl border transition flex items-center gap-2.5 ${isAttached ? 'border-emerald-500 bg-emerald-50/60 ring-1 ring-emerald-500/60 shadow-xs' : 'border-gray-200 bg-white hover:border-emerald-300'}`;
                    card.innerHTML = `
                        <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-800 font-bold text-xs shrink-0">🗺️</div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-xs text-brand-text truncate">${escapeAdminHtml(n.name)}</h5>
                            <p class="text-[10px] text-brand-muted truncate">${n.distance_km || '5'} km &middot; ${n.travel_time || '15 mins'}</p>
                        </div>
                        <button type="button" onclick="toggleCardAttachment('nearby', ${n.id}, '${escapeAdminHtml(n.name)}', '${n.distance_km || '5'} km · ${n.travel_time || 'Short Drive'}', '', '${n.image_url || ''}', '/nearby', 'View Excursion', 'Kerala Experience')" class="px-2.5 py-1 ${isAttached ? 'bg-emerald-700 text-white shadow-xs' : 'bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800'} rounded-lg text-[10px] font-bold shrink-0 transition cursor-pointer flex items-center gap-1">
                            ${isAttached ? '✓ Added' : '+ Add'}
                        </button>
                    `;
                    grid.appendChild(card);
                });
            }

            refreshIcons();
        }

        /* -------------------------------------------------------------------------
           MULTI-CARD ATTACHMENT TRAY & ACTIONS
           ------------------------------------------------------------------------- */
        function toggleCardAttachment(cardType, entityId, title, subtitle, price, imageUrl, linkUrl, actionText, badge) {
            const index = attachedCards.findIndex(c => c.card_type === cardType && String(c.entity_id) === String(entityId));
            if (index >= 0) {
                attachedCards.splice(index, 1);
                showToast(`Removed "${title}"`, 'info');
            } else {
                attachedCards.push({
                    card_type: cardType,
                    entity_id: entityId,
                    title: title,
                    subtitle: subtitle,
                    price: price,
                    image_url: imageUrl,
                    link_url: linkUrl,
                    action_text: actionText,
                    badge: badge
                });
                showToast(`Attached "${title}" (${attachedCards.length} total)`, 'success');
            }
            renderAttachedCardsTray();
            updatePickerAttachedBadge();
            renderPickerItems();
            refreshIcons();
        }

        function addCardToMessage(cardType, entityId, title, subtitle, price, imageUrl, linkUrl, actionText, badge) {
            toggleCardAttachment(cardType, entityId, title, subtitle, price, imageUrl, linkUrl, actionText, badge);
        }

        function applyTemplateToMessage(templateId) {
            const t = chatTemplatesData.find(x => String(x.id) === String(templateId));
            if (!t) return;

            const input = document.getElementById('reply-message-input');
            if (t.message && input) {
                input.value = input.value.trim() ? (input.value.trim() + "\n" + t.message) : t.message;
            }

            if (t.type !== 'text' && t.entity_id) {
                const catMap = {
                    'room': (chatCatalogsData.room_types || []).find(x => x.id === t.entity_id),
                    'branch': (chatCatalogsData.branches || []).find(x => x.id === t.entity_id),
                    'spice': (chatCatalogsData.spices || []).find(x => x.id === t.entity_id),
                    'dining': (chatCatalogsData.dining || []).find(x => x.id === t.entity_id),
                    'nearby': (chatCatalogsData.nearby || []).find(x => x.id === t.entity_id),
                };
                const item = catMap[t.type];
                if (item) {
                    if (t.type === 'room') {
                        toggleCardAttachment('room', item.id, item.name, (item.branch ? item.branch.name : '') + ' · Up to ' + item.max_guests + ' Guests', '₹' + Number(item.base_price).toLocaleString(), item.cover_image_url, '/rooms/' + item.slug, 'View Room Details', 'Verified Cottage');
                    } else if (t.type === 'branch') {
                        toggleCardAttachment('branch', item.id, item.name, (item.city || '') + ' · Luxury Cottages of Kerala', '', item.hero_image_url || item.cover_image_url, '/stay?branch_id=' + item.id, 'Explore Branch & Stays', 'Cottage Branch');
                    } else if (t.type === 'spice') {
                        toggleCardAttachment('spice', item.id, item.name, 'Pack Size: ' + (item.package_size || '250g'), '₹' + Number(item.price).toLocaleString(), item.image_url, '/spices', 'View in Spices Shop', 'Organic Kerala Spice');
                    } else if (t.type === 'dining') {
                        toggleCardAttachment('dining', item.id, item.name, 'Authentic Plantation Dining', '₹' + Number(item.price).toLocaleString(), '', '/dining', 'View Dining Menu', 'Ayurvedic Special');
                    } else if (t.type === 'nearby') {
                        toggleCardAttachment('nearby', item.id, item.name, (item.distance_km || '5') + ' km · ' + (item.travel_time || 'Short Drive'), '', item.image_url, '/nearby', 'View Excursion', 'Kerala Experience');
                    }
                } else {
                    toggleCardAttachment(t.type, t.entity_id, t.title, '', '', '', '', 'View Details', 'Quick Action');
                }
            } else {
                showToast(`Quick Action "${t.title}" applied!`, 'success');
            }

            renderAttachedCardsTray();
            updatePickerAttachedBadge();
            renderPickerItems();
        }

        function removeAttachedCard(index) {
            attachedCards.splice(index, 1);
            renderAttachedCardsTray();
            updatePickerAttachedBadge();
            renderPickerItems();
        }

        function clearCardAttachments() {
            attachedCards = [];
            renderAttachedCardsTray();
            updatePickerAttachedBadge();
            renderPickerItems();
        }

        function renderAttachedCardsTray() {
            const tray = document.getElementById('attached-cards-tray');
            const chips = document.getElementById('attached-cards-chips');
            const count = document.getElementById('attached-cards-count');
            if (!tray || !chips) return;

            if (attachedCards.length === 0) {
                tray.classList.add('hidden');
                updatePickerAttachedBadge();
                return;
            }

            tray.classList.remove('hidden');
            if (count) {
                count.textContent = `${attachedCards.length} Card${attachedCards.length > 1 ? 's' : ''}`;
            }

            chips.innerHTML = '';
            attachedCards.forEach((c, idx) => {
                const chip = document.createElement('div');
                chip.className = 'inline-flex items-center gap-2 px-2.5 py-1.5 rounded-xl bg-white border border-emerald-300 text-emerald-950 text-xs shadow-xs shrink-0 whitespace-nowrap';
                const badgeIcon = c.card_type === 'room' ? '🏨' : (c.card_type === 'branch' ? '📍' : (c.card_type === 'spice' ? '🌿' : (c.card_type === 'dining' ? '🍲' : (c.card_type === 'nearby' ? '🗺️' : '⚡'))));
                chip.innerHTML = `
                    <span class="text-sm">${badgeIcon}</span>
                    <div class="flex flex-col text-left">
                        <span class="font-bold text-xs max-w-[130px] sm:max-w-[180px] truncate">${escapeAdminHtml(c.title)}</span>
                        <span class="text-[9px] text-emerald-700 font-semibold uppercase tracking-wider">${escapeAdminHtml(c.card_type)}${c.price ? ' · ' + escapeAdminHtml(c.price) : ''}</span>
                    </div>
                    <button type="button" onclick="removeAttachedCard(${idx})" class="w-5 h-5 rounded-full hover:bg-red-50 text-gray-400 hover:text-red-600 font-bold flex items-center justify-center cursor-pointer ml-1 text-xs" title="Remove">✕</button>
                `;
                chips.appendChild(chip);
            });
            updatePickerAttachedBadge();
        }

        function handleReplyInputKey(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendEnquiryReply();
            }
        }

        /* -------------------------------------------------------------------------
           ACTIVE THREAD SELECTION & RENDERING
           ------------------------------------------------------------------------- */
        async function selectEnquiryThread(enquiryId) {
            currentEnquiryId = enquiryId;
            clearCardAttachments();

            // Highlight active thread in left sidebar
            document.querySelectorAll('[id^="enq-thread-item-"]').forEach(el => {
                el.classList.remove('bg-brand-canvas', 'border-l-3', 'border-brand-primary');
            });
            const activeItem = document.getElementById(`enq-thread-item-${enquiryId}`);
            if (activeItem) {
                activeItem.classList.add('bg-brand-canvas', 'border-l-3', 'border-brand-primary');
            }

            // Mobile responsive switch to chat view
            if (window.innerWidth < 1024) {
                const threadCol = document.getElementById('comm-thread-list-col');
                const chatCol = document.getElementById('comm-chat-view-col');
                const secHeader = document.getElementById('comm-section-header');
                if (threadCol && chatCol) {
                    threadCol.classList.add('hidden');
                    chatCol.classList.remove('hidden');
                    chatCol.classList.add('flex');
                }
                if (secHeader) {
                    secHeader.classList.add('hidden');
                }
            }

            try {
                const res = await fetch(`/admin/chat/${enquiryId}/data`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    renderAdminChatConversation(data);
                }
            } catch (err) {
                console.error('Failed to load thread data:', err);
                showToast('Failed to load conversation thread', 'error');
            }
        }

        function renderAdminChatConversation(data) {
            const enq = data.enquiry;
            const stay = data.upcoming_stay;
            const msgs = data.messages || [];

            // 1. Header Info
            const nameEl = document.getElementById('chat-header-guest-name');
            const ticketEl = document.getElementById('chat-header-ticket');
            const branchEl = document.getElementById('chat-header-branch');
            const contactEl = document.getElementById('chat-header-contact');

            if (nameEl) nameEl.textContent = enq.customer_name;
            if (ticketEl) ticketEl.textContent = enq.ticket_number;
            if (branchEl) branchEl.textContent = enq.branch_name;
            if (contactEl) {
                contactEl.innerHTML = `
                    <span>${enq.customer_email}</span>
                    ${enq.customer_phone ? `<span>&middot;</span><span class="text-white font-medium">${enq.customer_phone}</span>` : ''}
                `;
            }

            // 2. Lock Status Indicator & Actions
            const indicatorEl = document.getElementById('chat-lock-status-indicator');
            const actionsEl = document.getElementById('chat-lock-action-buttons');
            const replyInput = document.getElementById('reply-message-input');
            const replyInternal = document.getElementById('reply-is-internal');
            const sendBtn = document.getElementById('chat-send-btn');
            const plusBtn = document.getElementById('chat-plus-btn');

            if (!enq.is_locked) {
                if (indicatorEl) {
                    indicatorEl.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-white/60 font-medium">
                            <i data-lucide="unlock" class="w-4 h-4 text-white/40"></i>
                            <span>Unassigned &middot; Any manager can claim</span>
                        </span>
                    `;
                }
                if (actionsEl) {
                    actionsEl.innerHTML = `
                        <button onclick="claimChat(${enq.id})" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                            <i data-lucide="lock" class="w-3.5 h-3.5"></i> Accept & Lock Chat
                        </button>
                    `;
                }
                if (replyInput) replyInput.disabled = false;
                if (replyInternal) replyInternal.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
                if (plusBtn) plusBtn.disabled = false;
            } else if (enq.is_locked_by_current_user) {
                if (indicatorEl) {
                    indicatorEl.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-emerald-300 font-bold">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                            <span>Locked Exclusively to You (${enq.locked_at || 'Active'})</span>
                        </span>
                    `;
                }
                if (actionsEl) {
                    actionsEl.innerHTML = `
                        <button onclick="relieveChat(${enq.id})" class="px-3 py-1 bg-white/20 hover:bg-white/30 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition cursor-pointer">
                            <i data-lucide="unlock" class="w-3.5 h-3.5"></i> Relieve / Release Lock
                        </button>
                    `;
                }
                if (replyInput) replyInput.disabled = false;
                if (replyInternal) replyInternal.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
                if (plusBtn) plusBtn.disabled = false;
            } else {
                if (indicatorEl) {
                    indicatorEl.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-amber-300 font-bold">
                            <i data-lucide="lock" class="w-4 h-4 text-amber-400"></i>
                            <span>Locked Exclusively by ${enq.locked_by} (${enq.locked_at || 'Active'})</span>
                        </span>
                    `;
                }
                let superAdminBtn = '';
                @if(auth()->user()->isSuperAdmin())
                superAdminBtn = `
                    <button onclick="forceUnlockChat(${enq.id})" class="px-3 py-1 bg-red-600 hover:bg-red-500 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition cursor-pointer">
                        <i data-lucide="key" class="w-3.5 h-3.5"></i> Force Unlock (Admin)
                    </button>
                `;
                @endif
                if (actionsEl) actionsEl.innerHTML = superAdminBtn;
                if (replyInput) replyInput.disabled = true;
                if (replyInternal) replyInternal.disabled = true;
                if (sendBtn) sendBtn.disabled = true;
                if (plusBtn) plusBtn.disabled = true;
            }

            // 3. Upcoming / In-House Stay Strip
            const stayStrip = document.getElementById('chat-upcoming-stay-strip');
            const targetSelect = document.getElementById('reply-target-room');

            if (stayStrip) {
                if (stay) {
                    stayStrip.classList.remove('hidden');
                    const textEl = document.getElementById('chat-upcoming-stay-text');
                    const iconEl = stayStrip.querySelector('i');
                    if (stay.is_in_house) {
                        stayStrip.className = 'bg-emerald-500/15 border-b border-emerald-500/30 px-4 py-2 flex items-center justify-between text-xs text-emerald-950 shrink-0';
                        if (iconEl) iconEl.setAttribute('data-lucide', 'key');
                        if (textEl) {
                            textEl.innerHTML = `
                                <strong>Active In-House Guest:</strong> Checked in at <strong>${escapeAdminHtml(stay.branch_name)}</strong> &middot; Room(s): <strong class="text-emerald-900">${escapeAdminHtml(stay.rooms && stay.rooms.length > 0 ? stay.rooms.join(', ') : 'Assigned')}</strong> &middot; Booking <span class="font-mono font-bold">${escapeAdminHtml(stay.booking_code)}</span> (${escapeAdminHtml(stay.check_in)} &rarr; ${escapeAdminHtml(stay.check_out)}).
                            `;
                        }
                    } else {
                        stayStrip.className = 'bg-amber-500/15 border-b border-amber-500/30 px-4 py-2 flex items-center justify-between text-xs text-amber-900 shrink-0';
                        if (iconEl) iconEl.setAttribute('data-lucide', 'calendar-clock');
                        if (textEl) {
                            textEl.innerHTML = `
                                <strong>Upcoming Stay Alert:</strong> Guest holds booking <span class="font-mono font-bold">${escapeAdminHtml(stay.booking_code)}</span> at <strong>${escapeAdminHtml(stay.branch_name)}</strong> (${escapeAdminHtml(stay.check_in)} &rarr; ${escapeAdminHtml(stay.check_out)}). SMS notification triggered for Branch Manager.
                            `;
                        }
                    }
                } else {
                    stayStrip.classList.add('hidden');
                }
            }

            // Populate reply target room select if multi-room stay
            if (targetSelect) {
                if (stay && stay.is_multi_room && stay.rooms && stay.rooms.length > 1) {
                    targetSelect.classList.remove('hidden');
                    let opts = '<option value="">Target: All Rooms</option>';
                    stay.rooms.forEach(r => {
                        opts += `<option value="Room ${escapeAdminHtml(r)}">Room ${escapeAdminHtml(r)}</option>`;
                    });
                    targetSelect.innerHTML = opts;
                } else {
                    targetSelect.classList.add('hidden');
                    targetSelect.innerHTML = '<option value="">Target: All Rooms</option>';
                }
            }

            // 4. Messages Stream
            const messagesContainer = document.getElementById('enquiry-chat-messages');
            if (messagesContainer) {
                if (enq.is_locked_by_other) {
                    messagesContainer.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center text-center p-8 space-y-3">
                            <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
                                <i data-lucide="lock" class="w-6 h-6"></i>
                            </div>
                            <h4 class="font-bold text-brand-text text-sm">Conversation Locked by ${enq.locked_by}</h4>
                            <p class="text-xs text-brand-muted max-w-sm">Under Krishna Cottages direct messaging protocols, only the committed staff member can read or respond to this conversation until they release the lock.</p>
                        </div>
                    `;
                } else {
                    messagesContainer.innerHTML = '';
                    msgs.forEach(m => renderSingleAdminMessage(m, messagesContainer));
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }

            refreshIcons();
        }

        function renderSingleAdminMessage(msg, container) {
            const isCustomer = msg.sender_type === 'customer';
            const msgDiv = document.createElement('div');
            msgDiv.className = `flex flex-col ${isCustomer ? 'items-start' : 'items-end'} my-1`;

            let cardHtml = '';
            if (msg.card_payload) {
                const payload = msg.card_payload;
                const cardsList = (payload.cards && Array.isArray(payload.cards)) ? payload.cards : [payload];
                if (cardsList.length > 0 && cardsList[0].title) {
                    cardHtml = `
                        <div class="mt-2.5 ${cardsList.length > 1 ? 'grid grid-cols-1 sm:grid-cols-2 gap-2.5' : 'w-full'}">
                            ${cardsList.map(cp => `
                                <div class="bg-white rounded-2xl overflow-hidden border border-forest/15 shadow-xs text-brand-text flex flex-col justify-between">
                                    ${cp.image_url ? `
                                    <div class="h-28 w-full relative overflow-hidden bg-black/5">
                                        <img src="${cp.image_url}" alt="${escapeAdminHtml(cp.title || '')}" class="w-full h-full object-cover">
                                        ${cp.badge ? `<span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#063F34]/90 text-white uppercase">${escapeAdminHtml(cp.badge)}</span>` : ''}
                                    </div>` : ''}
                                    <div class="p-3 flex-1 flex flex-col justify-between space-y-1.5">
                                        <div>
                                            <div class="flex items-start justify-between gap-1.5">
                                                <h4 class="font-bold text-xs leading-snug">${escapeAdminHtml(cp.title || '')}</h4>
                                                ${cp.price ? `<span class="font-bold text-xs text-brand-primary shrink-0">${escapeAdminHtml(cp.price)}</span>` : ''}
                                            </div>
                                            ${cp.subtitle ? `<p class="text-[10px] text-brand-muted mt-0.5 line-clamp-2">${escapeAdminHtml(cp.subtitle)}</p>` : ''}
                                        </div>
                                        ${cp.link_url ? `
                                        <a href="${cp.link_url}" target="_blank" class="mt-2 w-full py-1.5 px-3 rounded-xl bg-[#0B5D4B] hover:bg-[#063F34] text-white text-[10px] font-bold flex items-center justify-center gap-1.5 shadow-xs transition">
                                            <span>${escapeAdminHtml(cp.action_text || 'View Details')}</span>
                                            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-[#C7A76A]"></i>
                                        </a>` : ''}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                }
            }

            const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) : 'Just now';

            msgDiv.innerHTML = `
                ${!isCustomer ? `
                    <div class="flex items-center gap-1.5 mb-1 px-1">
                        <span class="text-[10px] font-bold text-brand-primary flex items-center gap-1">
                            <i data-lucide="user-check" class="w-3 h-3"></i> ${msg.user ? escapeAdminHtml(msg.user.name) : 'Staff Member'}
                        </span>
                        <span class="text-[9px] text-brand-muted">&middot; ${timeStr}</span>
                    </div>
                ` : `
                    <div class="flex items-center gap-1.5 mb-1 px-1">
                        <span class="text-[10px] font-bold text-brand-text">${document.getElementById('chat-header-guest-name')?.textContent || 'Guest'}</span>
                        <span class="text-[9px] text-brand-muted">&middot; ${timeStr}</span>
                    </div>
                `}

                <div class="max-w-[85%] sm:max-w-[78%] p-3.5 rounded-2xl text-xs leading-relaxed ${msg.is_internal_note ? 'bg-amber-50 border border-amber-200 text-amber-900 shadow-xs' : (isCustomer ? 'bg-white border border-forest/10 text-brand-text rounded-tl-xs shadow-xs' : 'bg-[#0B5D4B] text-white rounded-tr-xs shadow-card')}">
                    ${msg.is_internal_note ? `
                        <div class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                            <i data-lucide="lock" class="w-3 h-3"></i> Internal Staff Note
                        </div>
                    ` : ''}

                    ${msg.target_room ? `
                        <div class="mb-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${isCustomer ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-white/20 text-brass border border-white/30'}">
                                <i data-lucide="door-open" class="w-3 h-3"></i> Target: ${escapeAdminHtml(msg.target_room)}
                            </span>
                        </div>
                    ` : ''}

                    ${msg.message ? `<p class="leading-relaxed whitespace-pre-line">${escapeAdminHtml(msg.message)}</p>` : ''}
                    ${cardHtml}
                </div>
                <span class="text-[9px] text-brand-muted mt-0.5 px-1">${timeStr}</span>
            `;

            container.appendChild(msgDiv);
        }

        async function claimChat(enquiryId) {
            const targetId = enquiryId || currentEnquiryId;
            try {
                const res = await fetch(`/admin/chat/${targetId}/claim`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    selectEnquiryThread(targetId);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                showToast('Failed to lock conversation', 'error');
            }
        }

        async function relieveChat(enquiryId) {
            const targetId = enquiryId || currentEnquiryId;
            try {
                const res = await fetch(`/admin/chat/${targetId}/relieve`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    selectEnquiryThread(targetId);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                showToast('Failed to relieve lock', 'error');
            }
        }

        async function forceUnlockChat(enquiryId) {
            const targetId = enquiryId || currentEnquiryId;
            if (!confirm('Execute emergency override to unlock this conversation?')) return;
            try {
                const res = await fetch(`/admin/chat/${targetId}/force-unlock`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    selectEnquiryThread(targetId);
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                showToast('Failed to execute force unlock', 'error');
            }
        }

        /* -------------------------------------------------------------------------
           SEND MESSAGE FUNCTION (CORRECTED: ALWAYS SENDS TO CURRENT ENQUIRY)
           ------------------------------------------------------------------------- */
        async function sendEnquiryReply(enquiryId) {
            const targetId = enquiryId || currentEnquiryId;
            if (!targetId) {
                showToast('Please select a conversation thread first.', 'warning');
                return;
            }

            const input = document.getElementById('reply-message-input');
            const isInternal = document.getElementById('reply-is-internal') ? document.getElementById('reply-is-internal').checked : false;
            const message = input ? input.value.trim() : '';

            if (!message && attachedCards.length === 0) {
                showToast('Please type a message or select an interactive card to send.', 'warning');
                return;
            }

            const sendBtn = document.getElementById('chat-send-btn');
            if (sendBtn) {
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<span>Sending...</span>';
            }

            const targetRoom = document.getElementById('reply-target-room')?.value || null;

            const payload = {
                message: message,
                is_internal_note: isInternal,
                target_room: targetRoom,
            };

            if (attachedCards.length > 0) {
                payload.cards = attachedCards.map(c => ({
                    card_type: c.card_type,
                    entity_id: c.entity_id,
                    card_payload: c
                }));
            }

            try {
                const res = await fetch(`/admin/chat/${targetId}/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    if (input) input.value = '';
                    clearCardAttachments();

                    // Close picker popover if open
                    const popover = document.getElementById('card-action-picker-popover');
                    if (popover) popover.classList.add('hidden');

                    if (data.sent_message) {
                        const messagesContainer = document.getElementById('enquiry-chat-messages');
                        if (messagesContainer) {
                            renderSingleAdminMessage(data.sent_message, messagesContainer);
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                            refreshIcons();
                        }
                    }
                } else {
                    showToast(data.message || 'Failed to send reply', 'error');
                }
            } catch (err) {
                showToast('Failed to send reply', 'error');
            } finally {
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<span>Send</span><i data-lucide="send" class="w-3.5 h-3.5 text-[#C7A76A]"></i>';
                    refreshIcons();
                }
            }
        }

        function filterEnquiryThreads() {
            const query = document.getElementById('thread-search-input').value.toLowerCase().trim();
            const items = document.querySelectorAll('#enquiry-threads-list [id^="enq-thread-item-"]');
            items.forEach(item => {
                const text = item.getAttribute('data-search') || '';
                item.style.display = text.includes(query) ? '' : 'none';
            });
        }

        function toggleTemplateEntityPicker(type) {
            const container = document.getElementById('tpl-entity-selector-container');
            const select = document.getElementById('tpl-entity-id');
            if (!container || !select) return;

            if (type === 'text') {
                container.classList.add('hidden');
                select.innerHTML = '<option value="">-- Choose Item --</option>';
                return;
            }

            container.classList.remove('hidden');
            select.innerHTML = '<option value="">-- Choose Item --</option>';

            const itemsMap = {
                'branch': chatCatalogsData.branches || [],
                'room': chatCatalogsData.room_types || [],
                'spice': chatCatalogsData.spices || [],
                'dining': chatCatalogsData.dining || [],
                'nearby': chatCatalogsData.nearby || []
            };

            const items = itemsMap[type] || [];
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.name + (item.city ? ` (${item.city})` : '');
                select.appendChild(opt);
            });
        }

        async function submitCreateTemplate(e) {
            e.preventDefault();
            const title = document.getElementById('tpl-title').value.trim();
            const category = document.getElementById('tpl-category').value;
            const type = document.getElementById('tpl-type').value;
            const entityId = document.getElementById('tpl-entity-id').value;
            const message = document.getElementById('tpl-message').value.trim();

            try {
                const res = await fetch('/admin/chat/templates', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({
                        title: title,
                        category: category,
                        type: type,
                        entity_id: entityId ? parseInt(entityId) : null,
                        message: message || null
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Quick Action template created successfully', 'success');
                    closeModal('modal-create-template');
                    loadChatTemplates();
                } else {
                    showToast(data.message || 'Error creating template', 'error');
                }
            } catch (err) {
                showToast('Failed to create template', 'error');
            }
        }

        function escapeAdminHtml(str) {
            return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
        }

        async function moderateReview(reviewId, status) {
            try {
                const res = await fetch(`/admin/reviews/${reviewId}/moderate`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ status: status })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const card = document.getElementById(`review-card-${reviewId}`);
                    if (card && status === 'rejected') {
                        card.style.opacity = '0.5';
                    }
                }
            } catch (err) {
                showToast('Failed to moderate review', 'error');
            }
        }

        // ================= CMS TABS =================
        function switchCmsTab(tab) {
            const tabs = ['visual', 'pages', 'nav', 'home'];
            tabs.forEach(t => {
                const el = document.getElementById(`cms-tab-${t}`);
                const btn = document.getElementById(`cms-tab-btn-${t}`);
                if (!el || !btn) return;

                if (t === tab) {
                    el.classList.remove('hidden');
                    btn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition shadow-xs shrink-0 flex items-center gap-1.5';
                } else {
                    el.classList.add('hidden');
                    btn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:text-brand-text transition shrink-0';
                }
            });
            if (typeof refreshIcons === 'function') refreshIcons();
            else if (window.lucide && lucide.createIcons) lucide.createIcons();
        }

        // ================= SYSTEM TABS =================
        function switchSystemTab(tab) {
            ['staff', 'cancellation', 'audit', 'settings', 'templates', 'notifs'].forEach(t => {
                const el = document.getElementById(`sys-tab-${t}`);
                const btn = document.getElementById(`sys-tab-btn-${t}`);
                if (el) el.classList.add('hidden');
                if (btn) btn.className = 'px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1';
            });
            const activeEl = document.getElementById(`sys-tab-${tab}`);
            const activeBtn = document.getElementById(`sys-tab-btn-${tab}`);
            if (activeEl) activeEl.classList.remove('hidden');
            if (activeBtn) activeBtn.className = 'px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition shrink-0 flex items-center gap-1';
            refreshIcons();
        }

        // ================= STAFF & BRANCH ACCESS PERMISSION LOGIC =================
        function toggleBranchAccessScope(mode, type) {
            const container = document.getElementById(`${mode}-staff-custom-branches`);
            const errEl = document.getElementById(`${mode}-staff-branch-error`);
            if (!container) return;

            if (type === 'assigned') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
                if (errEl) errEl.classList.add('hidden');
            }
            refreshIcons();
        }

        function selectAllStaffBranches(mode) {
            document.querySelectorAll(`.${mode}-staff-branch-cb`).forEach(cb => {
                cb.checked = true;
            });
            const errEl = document.getElementById(`${mode}-staff-branch-error`);
            if (errEl) errEl.classList.add('hidden');
        }

        function clearAllStaffBranches(mode) {
            document.querySelectorAll(`.${mode}-staff-branch-cb`).forEach(cb => {
                cb.checked = false;
            });
        }

        function handleStaffRoleChange(mode) {
            const roleSelect = document.getElementById(`${mode}-staff-role`);
            if (!roleSelect) return;
            // If super_admin, automatically switch to all branches
            if (roleSelect.value === 'super_admin') {
                const radioAll = document.getElementById(`${mode}-scope-all`);
                if (radioAll) {
                    radioAll.checked = true;
                    toggleBranchAccessScope(mode, 'all');
                }
            }
        }

        async function submitStaffCreateForm(e) {
            e.preventDefault();
            const form = e.target;
            const scopeVal = form.querySelector('input[name="branch_access_type"]:checked')?.value || 'all';
            const errEl = document.getElementById('add-staff-branch-error');

            if (scopeVal === 'assigned') {
                const checkedCount = form.querySelectorAll('.add-staff-branch-cb:checked').length;
                if (checkedCount === 0) {
                    if (errEl) errEl.classList.remove('hidden');
                    showToast('Please select at least one branch for customized branch access.', 'error');
                    return;
                }
            }
            if (errEl) errEl.classList.add('hidden');

            const submitBtn = document.getElementById('btn-create-staff');
            const originalHtml = submitBtn ? submitBtn.innerHTML : 'Create';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin mr-1">●</span> Creating...';
            }

            try {
                const formData = new FormData(form);
                const res = await fetch('{{ route("admin.staff.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Staff member created successfully!', 'success');
                    closeModal('modal-staff');
                    form.reset();
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to create staff');
                    showToast(msg, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error while creating staff account.', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            }
        }

        function openEditStaffModal(staff) {
            document.getElementById('edit-staff-id').value = staff.id;
            document.getElementById('edit-staff-modal-title').innerText = `Edit Staff: ${staff.name}`;
            document.getElementById('edit-staff-name').value = staff.name || '';
            document.getElementById('edit-staff-email').value = staff.email || '';
            document.getElementById('edit-staff-phone').value = staff.phone || '';
            document.getElementById('edit-staff-role').value = staff.role || 'branch_manager';
            document.getElementById('edit-staff-password').value = '';
            document.getElementById('edit-staff-is-active').value = staff.is_active ? '1' : '0';

            const scope = staff.branch_access_type === 'assigned' ? 'assigned' : 'all';
            if (scope === 'assigned') {
                document.getElementById('edit-scope-assigned').checked = true;
            } else {
                document.getElementById('edit-scope-all').checked = true;
            }
            toggleBranchAccessScope('edit', scope);

            const assignedIds = Array.isArray(staff.branch_ids) ? staff.branch_ids.map(Number) : [];
            document.querySelectorAll('.edit-staff-branch-cb').forEach(cb => {
                cb.checked = assignedIds.includes(parseInt(cb.value));
            });

            openModal('modal-edit-staff');
        }

        async function submitStaffEditForm(e) {
            e.preventDefault();
            const form = e.target;
            const staffId = document.getElementById('edit-staff-id').value;
            const scopeVal = form.querySelector('input[name="branch_access_type"]:checked')?.value || 'all';
            const errEl = document.getElementById('edit-staff-branch-error');

            if (scopeVal === 'assigned') {
                const checkedCount = form.querySelectorAll('.edit-staff-branch-cb:checked').length;
                if (checkedCount === 0) {
                    if (errEl) errEl.classList.remove('hidden');
                    showToast('Please select at least one branch for customized branch access.', 'error');
                    return;
                }
            }
            if (errEl) errEl.classList.add('hidden');

            const submitBtn = document.getElementById('btn-update-staff');
            const originalHtml = submitBtn ? submitBtn.innerHTML : 'Save';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin mr-1">●</span> Saving...';
            }

            try {
                const formData = new FormData(form);
                const res = await fetch(`/admin/staff/${staffId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Staff updated successfully!', 'success');
                    closeModal('modal-edit-staff');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Failed to update staff');
                    showToast(msg, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error while updating staff account.', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            }
        }

        async function deleteStaffMember(id, name) {
            if (!confirm(`Are you sure you want to delete staff member "${name}"? This action cannot be undone.`)) {
                return;
            }

            try {
                const res = await fetch(`/admin/staff/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Staff member deleted.', 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to delete staff member.', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error while deleting staff member.', 'error');
            }
        }

        // ================= NEW RESERVATION WIZARD & COUNTER BOOKING =================
        function openNewReservationModal() {
            document.getElementById('new-reservation-modal').classList.remove('hidden');
            onModalDatesOrBranchChanged();
        }

        function closeNewReservationModal() {
            document.getElementById('new-reservation-modal').classList.add('hidden');
        }

        async function onModalDatesOrBranchChanged() {
            const branchEl = document.getElementById('modal-branch-select');
            const roomTypeEl = document.getElementById('modal-room-type-select');
            const checkInEl = document.getElementById('modal-checkin-date');
            const checkOutEl = document.getElementById('modal-checkout-date');
            const selectEl = document.getElementById('modal-physical-room-select');
            const indicator = document.getElementById('modal-room-status-indicator');

            if (!branchEl || !checkInEl || !checkOutEl || !selectEl) return;

            const branchId = branchEl.value;
            const roomTypeId = roomTypeEl ? roomTypeEl.value : '';
            const checkIn = checkInEl.value;
            const checkOut = checkOutEl.value;

            if (!branchId || !checkIn || !checkOut) return;

            if (indicator) indicator.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span><span>Checking live dates...</span>`;

            try {
                const res = await fetch(`/admin/reservations/available-rooms-for-dates?branch_id=${branchId}&room_type_id=${roomTypeId}&check_in_date=${checkIn}&check_out_date=${checkOut}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    selectEl.innerHTML = `<option value="">⚡ Auto-Assign Next Available Free Unit (${data.count} free)</option>`;
                    data.rooms.forEach(r => {
                        selectEl.innerHTML += `<option value="${r.id}">Villa ${r.room_number} (${r.room_type_name} - Floor ${r.floor}) - ₹${r.base_price}/nt</option>`;
                    });

                    if (indicator) {
                        if (data.count > 0) {
                            indicator.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span class="text-emerald-700">${data.count} units free for dates</span>`;
                        } else {
                            indicator.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span><span class="text-red-700">Sold out for dates</span>`;
                        }
                    }
                }
            } catch (err) {
                console.error(err);
                if (indicator) indicator.innerHTML = `<span class="text-gray-400">Offline check</span>`;
            }
        }

        async function submitNewReservation(event) {
            event.preventDefault();
            const form = event.target;
            const btn = document.getElementById('submit-res-btn');
            btn.innerText = 'Registering & Locking Room...';
            btn.disabled = true;

            const payload = {
                branch_id: form.branch_id.value,
                room_type_id: form.room_type_id.value,
                room_id: form.room_id ? form.room_id.value : null,
                check_in_date: form.check_in_date.value,
                check_out_date: form.check_out_date.value,
                adults: form.adults.value,
                children: form.children.value,
                guest_name: form.guest_name.value,
                guest_email: form.guest_email.value,
                guest_phone: form.guest_phone.value,
                id_proof_type: form.id_proof_type ? form.id_proof_type.value : null,
                id_proof_number: form.id_proof_number ? form.id_proof_number.value : null,
                payment_method: form.payment_method ? form.payment_method.value : 'cash',
                paid_amount: form.paid_amount ? form.paid_amount.value : 0,
                instant_checkin: form.instant_checkin ? (form.instant_checkin.checked ? 1 : 0) : 0,
                is_counter_booking: 1,
                special_requests: form.special_requests.value
            };

            try {
                const res = await fetch('/admin/reservations', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeNewReservationModal();
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Validation error', 'error');
                    btn.innerText = 'Register & Lock Room';
                    btn.disabled = false;
                }
            } catch (err) {
                showToast('Failed to create reservation', 'error');
                btn.innerText = 'Register & Lock Room';
                btn.disabled = false;
            }
        }

        // ================= CANCELLATION & AUTOMATED PAYBACK ENGINE =================
        async function openCancellationPreviewModal() {
            if (!currentReservationId) return;
            const modal = document.getElementById('cancellation-preview-modal');
            modal.classList.remove('hidden');

            document.getElementById('cancel-modal-code').innerText = 'Calculating refund tiers...';
            document.getElementById('cancel-calc-hours').innerText = 'Calculating...';
            document.getElementById('cancel-calc-paid').innerText = '...';
            document.getElementById('cancel-calc-pct').innerText = '...';
            document.getElementById('cancel-calc-cashback').innerText = '...';
            document.getElementById('cancel-calc-retained').innerText = '...';
            document.getElementById('cancel-calc-rule').innerText = 'Matching active policy tiers...';

            try {
                const res = await fetch(`/admin/reservations/${currentReservationId}/cancel-preview`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('cancel-modal-code').innerText = `Booking Code: ${data.booking_code}`;
                    document.getElementById('cancel-calc-hours').innerText = `${data.hours_remaining} Hours remaining`;
                    document.getElementById('cancel-calc-paid').innerText = `₹${Number(data.paid_amount).toLocaleString()}`;
                    document.getElementById('cancel-calc-pct').innerText = `${data.refund_percentage}% Cashback`;
                    document.getElementById('cancel-calc-cashback').innerText = `₹${Number(data.cashback_amount).toLocaleString()}`;
                    document.getElementById('cancel-calc-retained').innerText = `₹${Number(data.retained_amount).toLocaleString()}`;
                    document.getElementById('cancel-calc-rule').innerText = data.rule_description;
                }
            } catch (err) {
                showToast('Failed to load cancellation calculation', 'error');
            }
        }

        function closeCancellationPreviewModal() {
            document.getElementById('cancellation-preview-modal').classList.add('hidden');
        }

        async function executeCancellationWithCashback() {
            if (!currentReservationId) return;
            const btn = document.getElementById('cancel-execute-btn');
            btn.disabled = true;
            btn.innerText = 'Processing refund...';

            const reason = document.getElementById('cancel-reason-input').value;

            try {
                const res = await fetch(`/admin/reservations/${currentReservationId}/cancel`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ reason: reason })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeCancellationPreviewModal();
                    closeDrawer();
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    showToast(data.message || 'Cancellation failed', 'error');
                    btn.disabled = false;
                    btn.innerText = 'Confirm & Auto-Refund';
                }
            } catch (err) {
                showToast('Failed to cancel reservation', 'error');
                btn.disabled = false;
                btn.innerText = 'Confirm & Auto-Refund';
            }
        }

        // ================= CANCELLATION RULES CRUD =================
        async function saveCancellationPolicyDescription() {
            const text = document.getElementById('cancellation-policy-textarea').value;
            const btn = document.getElementById('save-policy-desc-btn');
            btn.disabled = true;
            btn.innerText = 'Saving...';

            try {
                const res = await fetch('/admin/settings/cancellation-policy', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ description: text })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Could not save policy', 'error');
                }
            } catch (err) {
                showToast('Network error saving cancellation policy', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="save" class="w-3.5 h-3.5"></i> Save Terms`;
                refreshIcons();
            }
        }

        function openCancellationRuleModal() {
            document.getElementById('cancellation-rule-id').value = '';
            document.getElementById('cancellation-modal-title').innerText = 'Add Cancellation Cashback Tier';
            document.getElementById('rule-hours-input').value = '48';
            document.getElementById('rule-pct-input').value = '75';
            document.getElementById('rule-desc-input').value = '';
            document.getElementById('rule-branch-select').value = '';
            document.getElementById('rule-sort-input').value = '1';
            document.getElementById('rule-active-input').checked = true;
            openModal('modal-cancellation-rule');
        }

        function editCancellationRule(rule) {
            document.getElementById('cancellation-rule-id').value = rule.id;
            document.getElementById('cancellation-modal-title').innerText = `Edit Cancellation Tier #${rule.id}`;
            document.getElementById('rule-hours-input').value = rule.hours_before_checkin;
            document.getElementById('rule-pct-input').value = rule.refund_percentage;
            document.getElementById('rule-desc-input').value = rule.description;
            document.getElementById('rule-branch-select').value = rule.branch_id || '';
            document.getElementById('rule-sort-input').value = rule.sort_order || 0;
            document.getElementById('rule-active-input').checked = !!rule.is_active;
            openModal('modal-cancellation-rule');
        }

        async function submitCancellationRuleForm(e) {
            e.preventDefault();
            const id = document.getElementById('cancellation-rule-id').value;
            const url = id ? `/admin/cancellation-rules/${id}/update` : `/admin/cancellation-rules`;

            const payload = {
                hours_before_checkin: document.getElementById('rule-hours-input').value,
                refund_percentage: document.getElementById('rule-pct-input').value,
                description: document.getElementById('rule-desc-input').value,
                branch_id: document.getElementById('rule-branch-select').value || null,
                sort_order: document.getElementById('rule-sort-input').value || 0,
                is_active: document.getElementById('rule-active-input').checked ? 1 : 0,
            };

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-cancellation-rule');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Validation error', 'error');
                }
            } catch (err) {
                showToast('Failed to save cancellation rule tier', 'error');
            }
        }

        async function deleteCancellationRule(id) {
            if (!confirm('Are you sure you want to delete this cancellation rule tier?')) return;
            try {
                const res = await fetch(`/admin/cancellation-rules/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                }
            } catch (err) {
                showToast('Failed to delete rule', 'error');
            }
        }

        // ================= STAY EXTENSION & ON-HAND PAYMENTS =================
        function switchReservationView(view) {
            const containers = {
                inhouse: document.getElementById('view-inhouse-container'),
                bookings: document.getElementById('view-bookings-container'),
                extensions: document.getElementById('view-extensions-container'),
            };
            const buttons = {
                inhouse: document.getElementById('btn-view-inhouse'),
                bookings: document.getElementById('btn-view-bookings'),
                extensions: document.getElementById('btn-view-extensions'),
            };

            ['inhouse', 'bookings', 'extensions'].forEach(v => {
                if (containers[v]) {
                    if (v === view) {
                        containers[v].classList.remove('hidden');
                    } else {
                        containers[v].classList.add('hidden');
                    }
                }
                if (buttons[v]) {
                    if (v === view) {
                        buttons[v].className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-brand-text shadow-xs transition flex items-center gap-1.5 shrink-0';
                    } else {
                        buttons[v].className = 'px-3 py-1.5 rounded-lg text-xs font-bold text-brand-muted hover:text-brand-text transition flex items-center gap-1.5 shrink-0';
                    }
                }
            });
            if (typeof refreshIcons === 'function') refreshIcons();
            else if (window.lucide && lucide.createIcons) lucide.createIcons();
        }

        function openExtensionOfferModal(ext) {
            if (!ext) return;
            document.getElementById('ext-offer-id').value = ext.id;
            document.getElementById('ext-offer-guest').innerText = ext.guest ? (ext.guest.first_name + ' ' + (ext.guest.last_name || '')) : (ext.user ? ext.user.name : 'Guest');
            document.getElementById('ext-offer-dates').innerText = `${ext.current_checkout_date} to ${ext.requested_checkout_date} (+${ext.extra_nights} nts)`;
            document.getElementById('ext-offer-rooms').innerText = ext.allocated_room_ids ? `Units: ${ext.allocated_room_ids.join(', ')}` : 'Same Room';
            document.getElementById('ext-offer-standard').innerText = '₹' + Number(ext.standard_amount).toLocaleString();
            document.getElementById('ext-offer-amount-input').value = Math.round(Number(ext.standard_amount) * 0.85);
            openModal('modal-extension-offer');
        }

        async function submitExtensionOfferForm(e) {
            e.preventDefault();
            const id = document.getElementById('ext-offer-id').value;
            const amount = document.getElementById('ext-offer-amount-input').value;
            const notes = document.getElementById('ext-offer-notes').value;

            try {
                const res = await fetch(`/admin/stay-extensions/${id}/offer`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ offered_amount: amount, manager_notes: notes })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-extension-offer');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Error updating extension offer', 'error');
                }
            } catch (err) {
                showToast('Network error submitting extension offer', 'error');
            }
        }

        function openExtensionPaymentModal(ext) {
            if (!ext) return;
            document.getElementById('ext-pay-id').value = ext.id;
            const amt = ext.offered_amount || ext.standard_amount;
            document.getElementById('ext-pay-amount-label').innerText = '₹' + Number(amt).toLocaleString();
            document.getElementById('ext-pay-guest-label').innerText = `Guest: ${ext.guest ? ext.guest.full_name : 'Guest'} • Extension until ${ext.requested_checkout_date}`;
            document.getElementById('ext-pay-amount-input').value = amt;
            openModal('modal-extension-payment');
        }

        function openExtensionPaymentModalFromDrawer() {
            if (window.currentExtensionForDrawer) {
                openExtensionPaymentModal(window.currentExtensionForDrawer);
            }
        }

        async function submitExtensionPaymentForm(e) {
            e.preventDefault();
            const id = document.getElementById('ext-pay-id').value;
            const method = document.getElementById('ext-pay-method-select').value;
            const amount = document.getElementById('ext-pay-amount-input').value;
            const notes = document.getElementById('ext-pay-notes-input').value;

            try {
                const res = await fetch(`/admin/stay-extensions/${id}/record-payment`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ payment_method: method, paid_amount: amount, notes: notes })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-extension-payment');
                    closeDrawer();
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Error recording payment', 'error');
                }
            } catch (err) {
                showToast('Network error recording extension payment', 'error');
            }
        }

        // ================= COMMAND PALETTE (CTRL+K) =================
        function openCommandPalette() {
            const modal = document.getElementById('cmd-palette');
            modal.classList.remove('hidden');
            const input = document.getElementById('cmd-search-input');
            input.value = '';
            setTimeout(() => input.focus(), 80);
        }

        function closeCommandPalette() {
            document.getElementById('cmd-palette').classList.add('hidden');
        }

        let searchDebounceTimer;
        function handleCommandPaletteSearch(val) {
            clearTimeout(searchDebounceTimer);
            if (val.trim().length < 2) return;

            searchDebounceTimer = setTimeout(async () => {
                try {
                    const res = await fetch(`/admin/api/search?q=${encodeURIComponent(val)}`);
                    const data = await res.json();
                    renderCommandResults(data.results);
                } catch (e) {
                    console.error(e);
                }
            }, 200);
        }

        function renderCommandResults(results) {
            const container = document.getElementById('cmd-results-container');
            if (!results || results.length === 0) {
                container.innerHTML = '<div class="py-8 text-center text-brand-muted text-xs">No matching records found</div>';
                return;
            }

            let html = '<div class="p-2 text-[10px] font-bold text-brand-muted uppercase">Search Results</div>';
            results.forEach(item => {
                html += `
                    <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-gray-100 cursor-pointer transition" onclick="navigateTo('${item.section}'); closeCommandPalette(); ${item.type === 'reservation' ? `openReservationDrawer(${item.id})` : ''}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded bg-brand-canvas text-brand-primary flex items-center justify-center">
                                <i data-lucide="${item.icon}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="font-bold text-brand-text">${item.title}</div>
                                <div class="text-[11px] text-brand-muted">${item.subtitle}</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-brand-text uppercase">${item.badge}</span>
                    </div>
                `;
            });

            container.innerHTML = html;
            lucide.createIcons();
        }

        // Global Shortcuts (Ctrl+K, Esc)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                openCommandPalette();
            }
            if (e.key === 'Escape') {
                closeCommandPalette();
                closeDrawer();
                closeNewReservationModal();
                closeStockModal();
            }
        });

        // ================= TOAST NOTIFICATION UTILITY =================
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const bgClass = type === 'success' ? 'bg-emerald-800' : (type === 'error' ? 'bg-red-800' : 'bg-brand-deep');
            const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'alert-triangle' : 'info');

            toast.className = `${bgClass} text-white px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 text-xs font-semibold pointer-events-auto transform translate-y-3 opacity-0 transition-all duration-200 border border-white/15`;
            toast.innerHTML = `<i data-lucide="${icon}" class="w-4 h-4"></i> <span>${message}</span>`;

            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => toast.classList.remove('translate-y-3', 'opacity-0'), 10);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-3');
                setTimeout(() => toast.remove(), 250);
            }, 3200);
        }
        // ================= VISUAL LIVE HOMEPAGE EDITOR ENGINE =================
        function toggleDashboardView(mode) {
            const visualView = document.getElementById('visual-editor-view');
            const opsView = document.getElementById('operations-kpi-view');
            const btnVisual = document.getElementById('btn-view-visual');
            const btnOps = document.getElementById('btn-view-operations');

            if (mode === 'visual') {
                if (visualView) visualView.classList.remove('hidden');
                if (opsView) opsView.classList.add('hidden');
                if (btnVisual) {
                    btnVisual.className = 'px-3 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5';
                }
                if (btnOps) {
                    btnOps.className = 'px-3 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition flex items-center gap-1.5';
                }
            } else {
                if (visualView) visualView.classList.add('hidden');
                if (opsView) opsView.classList.remove('hidden');
                if (btnOps) {
                    btnOps.className = 'px-3 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5';
                }
                if (btnVisual) {
                    btnVisual.className = 'px-3 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition flex items-center gap-1.5';
                }
            }
            setTimeout(() => lucide.createIcons(), 50);
        }

        function updateFieldThumbnailPreview(fieldId, url) {
            const previewImg = document.getElementById(fieldId + '-preview');
            const placeholder = document.getElementById(fieldId + '-placeholder');
            if (previewImg) {
                if (url && url.trim().length > 3) {
                    previewImg.src = url;
                    previewImg.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                } else {
                    previewImg.src = '';
                    previewImg.classList.add('hidden');
                    if (placeholder) placeholder.classList.remove('hidden');
                }
            }
        }

        function openImagePicker(targetId) {
            activeImagePickerTargetId = targetId;
            const targetEl = document.getElementById(targetId);
            let currentSrc = '';
            if (targetEl) {
                if (targetEl.tagName === 'INPUT' || targetEl.tagName === 'TEXTAREA') {
                    currentSrc = targetEl.value || '';
                } else if (targetEl.tagName === 'IMG') {
                    currentSrc = targetEl.getAttribute('src') || '';
                } else {
                    currentSrc = targetEl.getAttribute('src') || targetEl.dataset.imageUrl || '';
                }
            }
            
            const input = document.getElementById('image-picker-url-input');
            if (input) input.value = currentSrc;
            previewImagePickerUrl(currentSrc);

            const badge = document.getElementById('image-picker-source-badge');
            if (badge) badge.innerText = 'Current Image';

            const status = document.getElementById('upload-status-indicator');
            if (status) status.classList.add('hidden');

            const fileInput = document.getElementById('image-picker-file-input');
            if (fileInput) fileInput.value = '';

            // Default to device tab
            switchImagePickerTab('device');

            const modal = document.getElementById('image-picker-modal');
            modal.classList.remove('hidden');
            setTimeout(() => lucide.createIcons(), 50);

            // Bind drag & drop once
            const dropzone = document.getElementById('image-dropzone');
            if (dropzone && !dropzone.dataset.bound) {
                dropzone.dataset.bound = 'true';
                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('border-brand-primary', 'bg-emerald-50/50');
                });
                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('border-brand-primary', 'bg-emerald-50/50');
                });
                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-brand-primary', 'bg-emerald-50/50');
                    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                        handleDeviceImageSelect(e.dataTransfer.files);
                    }
                });
            }
        }

        function closeImagePickerModal() {
            document.getElementById('image-picker-modal').classList.add('hidden');
            activeImagePickerTargetId = null;
        }

        function switchImagePickerTab(tab) {
            const tabs = ['device', 'url', 'presets'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-btn-${t}`);
                const panel = document.getElementById(`tab-panel-${t}`);
                if (btn && panel) {
                    if (t === tab) {
                        btn.className = 'px-3.5 py-2 text-xs font-bold text-brand-primary border-b-2 border-brand-primary flex items-center gap-1.5 transition';
                        panel.classList.remove('hidden');
                    } else {
                        btn.className = 'px-3.5 py-2 text-xs font-medium text-gray-500 hover:text-brand-text border-b-2 border-transparent flex items-center gap-1.5 transition';
                        panel.classList.add('hidden');
                    }
                }
            });
            setTimeout(() => lucide.createIcons(), 30);
        }

        async function handleDeviceImageSelect(files) {
            if (!files || files.length === 0) return;
            const file = files[0];

            if (!file.type.startsWith('image/')) {
                showToast('Please select a valid image file (PNG, JPG, WEBP, AVIF)', 'error');
                return;
            }

            if (file.size > 12 * 1024 * 1024) {
                showToast('Image is too large. Maximum file size is 12MB', 'error');
                return;
            }

            // 1. Instant local preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImagePickerUrl(e.target.result);
                const badge = document.getElementById('image-picker-source-badge');
                if (badge) badge.innerText = 'Device Upload (Transferring...)';
            };
            reader.readAsDataURL(file);

            // 2. Upload to backend
            const statusEl = document.getElementById('upload-status-indicator');
            const statusText = document.getElementById('upload-status-text');
            const applyBtn = document.getElementById('btn-apply-image-picker');

            if (statusEl) statusEl.classList.remove('hidden');
            if (statusText) statusText.innerText = `Uploading "${file.name}" to resort media storage...`;
            if (applyBtn) applyBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('image', file);

                const res = await fetch('/admin/api/upload-image', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                const data = await res.json();
                if (data.success && data.url) {
                    document.getElementById('image-picker-url-input').value = data.url;
                    previewImagePickerUrl(data.url);
                    const badge = document.getElementById('image-picker-source-badge');
                    if (badge) badge.innerText = 'Local Device Asset';
                    showToast(data.message || 'Image uploaded from device successfully!', 'success');
                } else {
                    showToast(data.message || 'Upload failed', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Error uploading image file from device', 'error');
            } finally {
                if (statusEl) statusEl.classList.add('hidden');
                if (applyBtn) applyBtn.disabled = false;
                setTimeout(() => lucide.createIcons(), 30);
            }
        }

        function previewImagePickerUrl(url) {
            const img = document.getElementById('image-picker-preview-img');
            const empty = document.getElementById('image-picker-preview-empty');
            if (url && url.trim().length > 3) {
                img.src = url;
                img.classList.remove('hidden');
                empty.classList.add('hidden');
            } else {
                img.classList.add('hidden');
                empty.classList.remove('hidden');
            }
        }

        function selectImagePreset(url) {
            document.getElementById('image-picker-url-input').value = url;
            previewImagePickerUrl(url);
            const badge = document.getElementById('image-picker-source-badge');
            if (badge) badge.innerText = 'Resort Preset';
            showToast('Preset selected', 'info');
        }

        function applyImagePickerSelection() {
            const newUrl = document.getElementById('image-picker-url-input').value.trim();
            if (!newUrl) {
                showToast('Please select or enter an image first', 'error');
                return;
            }

            if (activeImagePickerTargetId) {
                const targetEl = document.getElementById(activeImagePickerTargetId);
                if (targetEl) {
                    if (targetEl.tagName === 'INPUT' || targetEl.tagName === 'TEXTAREA') {
                        targetEl.value = newUrl;
                        targetEl.dispatchEvent(new Event('input', { bubbles: true }));
                        targetEl.dispatchEvent(new Event('change', { bubbles: true }));

                        updateFieldThumbnailPreview(activeImagePickerTargetId, newUrl);
                    } else if (targetEl.tagName === 'IMG') {
                        targetEl.src = newUrl;
                        targetEl.classList.remove('hidden');
                        const noImg = document.getElementById(`${activeImagePickerTargetId}-noimg`);
                        if (noImg) noImg.classList.add('hidden');
                        const overlay = document.getElementById(`${activeImagePickerTargetId}-overlay`);
                        if (overlay) overlay.classList.remove('hidden');

                        markHomepageUnsaved();
                        const companionInput = targetEl.closest('.img-picker-container')?.querySelector('input[type="text"], input[type="hidden"]');
                        if (companionInput) {
                            companionInput.value = newUrl;
                            companionInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    } else {
                        targetEl.dataset.imageUrl = newUrl;
                    }
                }
            }

            closeImagePickerModal();
            showToast('Photo applied successfully!', 'success');
        }

        function clearExpCardImage(imgId) {
            const img = document.getElementById(imgId);
            if (img) {
                img.src = '';
                img.classList.add('hidden');
            }
            const noImg = document.getElementById(`${imgId}-noimg`);
            if (noImg) noImg.classList.remove('hidden');
            const overlay = document.getElementById(`${imgId}-overlay`);
            if (overlay) overlay.classList.add('hidden');
            markHomepageUnsaved();
            showToast('Image removed from card', 'info');
        }

        // Direct device file upload helper for single-image inputs
        async function uploadFileDirectlyToInput(fileInput, targetFieldId) {
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) return;
            const file = fileInput.files[0];

            if (!file.type.startsWith('image/')) {
                showToast('Please select a valid image file (JPG, PNG, WEBP, AVIF)', 'error');
                fileInput.value = '';
                return;
            }

            if (file.size > 12 * 1024 * 1024) {
                showToast('Image exceeds 12MB limit', 'error');
                fileInput.value = '';
                return;
            }

            const targetInput = document.getElementById(targetFieldId);
            showToast(`Uploading "${file.name}" from device...`, 'info');

            try {
                const formData = new FormData();
                formData.append('image', file);

                const res = await fetch('/admin/api/upload-image', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                const data = await res.json();
                if (data.success && data.url) {
                    if (targetInput) {
                        targetInput.value = data.url;
                        targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                        targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    updateFieldThumbnailPreview(targetFieldId, data.url);
                    showToast('Image uploaded successfully from your device!', 'success');
                } else {
                    showToast(data.message || 'Failed to upload image', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Error uploading image file', 'error');
            } finally {
                fileInput.value = '';
            }
        }

        // Multi-file upload helper for gallery inputs
        async function uploadMultipleFilesForGallery(fileInput, containerId, hiddenInputName) {
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) return;
            const files = Array.from(fileInput.files);

            showToast(`Uploading ${files.length} photo(s) from device...`, 'info');

            try {
                const formData = new FormData();
                files.forEach(f => formData.append('images[]', f));

                const res = await fetch('/admin/api/upload-image', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                const data = await res.json();
                if (data.success && data.urls && data.urls.length > 0) {
                    const container = document.getElementById(containerId);
                    if (container) {
                        data.urls.forEach(url => {
                            addGalleryChipToContainer(container, hiddenInputName, url);
                        });
                    }
                    showToast(`${data.urls.length} photos uploaded and attached!`, 'success');
                } else {
                    showToast(data.message || 'Failed to upload photos', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Error uploading photos', 'error');
            } finally {
                fileInput.value = '';
            }
        }

        function addGalleryChipToContainer(container, inputName, url) {
            const chip = document.createElement('div');
            chip.className = 'relative group w-16 h-16 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 shrink-0 shadow-xs';
            chip.innerHTML = `
                <img src="${escapeAdminHtml(url)}" class="w-full h-full object-cover">
                <input type="hidden" name="${inputName}" value="${escapeAdminHtml(url)}">
                <button type="button" onclick="this.parentElement.remove()" class="absolute inset-0 bg-black/60 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center transition" title="Remove Photo">
                    <svg class="w-4 h-4 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            `;
            container.appendChild(chip);
        }

        function addManualUrlToGallery(urlInputId, containerId, hiddenInputName) {
            const input = document.getElementById(urlInputId);
            const url = input ? input.value.trim() : '';
            if (!url) {
                showToast('Please enter an image URL', 'error');
                return;
            }
            const container = document.getElementById(containerId);
            if (container) {
                addGalleryChipToContainer(container, hiddenInputName, url);
                input.value = '';
                showToast('Photo added to collection', 'success');
            }
        }

        // ================= GALLERY ALBUM ADMIN HANDLERS =================
        function filterAdminGalleryByBranch(branchId) {
            const cards = document.querySelectorAll('.admin-gallery-album-card');
            const buttons = document.querySelectorAll('.gallery-branch-tab-btn');

            buttons.forEach(btn => {
                if (btn.dataset.branchId === String(branchId)) {
                    btn.className = 'gallery-branch-tab-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white shadow-xs transition';
                } else {
                    btn.className = 'gallery-branch-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-brand-text bg-white border border-gray-200 hover:bg-gray-50 transition';
                }
            });

            cards.forEach(card => {
                const cardBranch = card.dataset.branchId;
                if (branchId === 'all' || cardBranch === String(branchId) || (branchId === 'none' && (!cardBranch || cardBranch === '0' || cardBranch === ''))) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        let currentManagingAlbumId = null;

        async function openAlbumPhotoManager(albumId) {
            currentManagingAlbumId = albumId;
            const modal = document.getElementById('modal-manage-album-photos');
            const grid = document.getElementById('album-photos-grid');
            const titleEl = document.getElementById('manage-album-photos-title');
            const countEl = document.getElementById('manage-album-photos-count');

            if (!modal) return;

            if (grid) grid.innerHTML = '<div class="col-span-full py-12 text-center text-gray-400"><div class="inline-block animate-spin w-6 h-6 border-2 border-brand-primary border-t-transparent rounded-full mb-2"></div><p class="text-xs">Loading album photos...</p></div>';
            modal.classList.remove('hidden');

            try {
                const res = await fetch(`/admin/gallery/albums/${albumId}/photos`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    if (titleEl) titleEl.innerText = data.album.name || data.album.title || 'Resort Album';
                    if (countEl) countEl.innerText = `${data.count} Photos`;
                    renderAlbumPhotosGrid(data.photos || []);
                } else {
                    showToast('Failed to load photos', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Error loading album photos', 'error');
            }
        }

        function renderAlbumPhotosGrid(photos) {
            const grid = document.getElementById('album-photos-grid');
            if (!grid) return;

            if (!photos || photos.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full py-12 text-center text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                        <i data-lucide="image" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="font-medium text-xs text-gray-500">No photos in this album yet.</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Upload photos from device or enter image URLs below.</p>
                    </div>
                `;
                setTimeout(() => lucide.createIcons(), 30);
                return;
            }

            grid.innerHTML = photos.map(p => `
                <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50 shadow-xs aspect-video sm:aspect-square flex items-center justify-center" id="album-photo-card-${p.id}">
                    <img src="${escapeAdminHtml(p.image_url)}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity p-2 flex flex-col justify-between text-white">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] bg-black/60 px-1.5 py-0.5 rounded font-mono">#${p.sort_order || p.id}</span>
                            <button type="button" onclick="deletePhotoFromAlbum(${currentManagingAlbumId}, ${p.id})" class="p-1 bg-red-600 hover:bg-red-700 text-white rounded-lg transition" title="Delete Photo">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <div class="text-[11px] truncate font-medium">${escapeAdminHtml(p.title || '')}</div>
                    </div>
                </div>
            `).join('');
            setTimeout(() => lucide.createIcons(), 30);
        }

        async function uploadPhotosToCurrentAlbum(fileInput) {
            if (!currentManagingAlbumId) return;
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) return;

            const files = Array.from(fileInput.files);
            showToast(`Uploading ${files.length} photo(s) to album...`, 'info');

            const formData = new FormData();
            files.forEach(f => formData.append('images[]', f));

            try {
                const res = await fetch(`/admin/gallery/albums/${currentManagingAlbumId}/photos`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Photos added successfully!', 'success');
                    const countEl = document.getElementById('manage-album-photos-count');
                    if (countEl) countEl.innerText = `${data.total_count} Photos`;
                    renderAlbumPhotosGrid(data.photos || []);

                    const badge = document.getElementById(`album-card-count-${currentManagingAlbumId}`);
                    if (badge) badge.innerText = `${data.total_count} Photos`;
                } else {
                    showToast(data.message || 'Failed to add photos', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Error uploading photos', 'error');
            } finally {
                fileInput.value = '';
            }
        }

        async function addUrlPhotoToCurrentAlbum() {
            if (!currentManagingAlbumId) return;
            const urlInput = document.getElementById('album-photo-new-url');
            const url = urlInput ? urlInput.value.trim() : '';
            if (!url) {
                showToast('Please enter an image URL', 'error');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('single_url', url);

                const res = await fetch(`/admin/gallery/albums/${currentManagingAlbumId}/photos`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Photo added successfully!', 'success');
                    urlInput.value = '';
                    const countEl = document.getElementById('manage-album-photos-count');
                    if (countEl) countEl.innerText = `${data.total_count} Photos`;
                    renderAlbumPhotosGrid(data.photos || []);

                    const badge = document.getElementById(`album-card-count-${currentManagingAlbumId}`);
                    if (badge) badge.innerText = `${data.total_count} Photos`;
                } else {
                    showToast(data.message || 'Failed to add photo', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Error adding photo URL', 'error');
            }
        }

        async function deletePhotoFromAlbum(albumId, photoId) {
            if (!confirm('Are you sure you want to remove this photo from the album?')) return;

            try {
                const res = await fetch(`/admin/gallery/albums/${albumId}/photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Photo deleted', 'success');
                    const el = document.getElementById(`album-photo-card-${photoId}`);
                    if (el) el.remove();
                    const countEl = document.getElementById('manage-album-photos-count');
                    if (countEl) countEl.innerText = `${data.total_count} Photos`;
                    const badge = document.getElementById(`album-card-count-${albumId}`);
                    if (badge) badge.innerText = `${data.total_count} Photos`;
                } else {
                    showToast(data.message || 'Failed to delete photo', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Error deleting photo', 'error');
            }
        }

        function openEditAlbumModal(album) {
            const modal = document.getElementById('modal-edit-gallery-album');
            if (!modal) return;

            document.getElementById('edit-album-id').value = album.id;
            document.getElementById('edit-album-title').value = album.name || album.title || '';
            document.getElementById('edit-album-branch').value = album.branch_id || '';
            document.getElementById('edit-album-category').value = album.category || 'resort';
            document.getElementById('edit-album-desc').value = album.description || '';
            document.getElementById('edit-album-cover-url').value = album.cover_image_url || '';
            updateFieldThumbnailPreview('edit-album-cover-url', album.cover_image_url || '');

            modal.classList.remove('hidden');
            setTimeout(() => lucide.createIcons(), 30);
        }

        async function deleteGalleryAlbum(albumId, albumName) {
            if (!confirm(`Are you sure you want to delete album "${albumName || 'this album'}"? All contained photos will also be deleted.`)) return;

            try {
                const res = await fetch(`/admin/gallery/albums/${albumId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Album deleted', 'success');
                    const card = document.getElementById(`admin-gallery-album-${albumId}`);
                    if (card) card.remove();
                } else {
                    showToast(data.message || 'Failed to delete album', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Error deleting album', 'error');
            }
        }

        function markHomepageUnsaved() {
            const indicator = document.getElementById('editor-status-indicator');
            if (indicator) {
                indicator.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span><span>Unsaved Modifications</span>';
                indicator.className = 'hidden sm:flex items-center gap-1.5 text-xs font-medium text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full';
            }
        }

        // Branch Card Dynamic Selection
        function onSelectBranchForCard(cardIdx, branchId) {
            const branch = allBranches.find(b => b.id == branchId);
            if (!branch) return;

            const titleEl = document.getElementById(`edit-branch-title-${cardIdx}`);
            const taglineEl = document.getElementById(`edit-branch-tagline-${cardIdx}`);
            const imgEl = document.getElementById(`edit-branch-img-${cardIdx}`);

            if (titleEl) titleEl.innerText = branch.name;
            if (taglineEl) taglineEl.innerText = branch.tagline || (branch.city + ' · ' + branch.state);
            if (imgEl && branch.hero_image_url) imgEl.src = branch.hero_image_url;

            markHomepageUnsaved();
            showToast(`Card ${cardIdx + 1} updated to ${branch.name}`, 'info');
        }

        // Pinned Spice Dynamic Selection (Requirement 6)
        function onSelectPinnedSpice(slot, spiceId) {
            const spice = allSpiceProducts.find(s => s.id == spiceId);
            if (!spice) return;

            const nameEl = document.getElementById(`pinned-spice-name-${slot}`);
            const priceEl = document.getElementById(`pinned-spice-price-${slot}`);
            const imgEl = document.getElementById(`pinned-spice-img-${slot}`);

            if (nameEl) nameEl.innerText = spice.name;
            if (priceEl) priceEl.innerText = '₹' + Number(spice.price).toLocaleString();
            if (imgEl && spice.image_url) imgEl.src = spice.image_url;

            markHomepageUnsaved();
            showToast(`Pinned Spice ${slot} set to ${spice.name}`, 'info');
        }

        // Auto detect typing on contenteditable elements
        document.addEventListener('input', (e) => {
            if (e.target && e.target.classList.contains('wysiwyg-text')) {
                markHomepageUnsaved();
            }
        });

        // Save All Visual Live Content to Database
        async function saveHomepageVisualContent() {
            const btn = document.getElementById('btn-save-homepage');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Saving...</span>';
            btn.disabled = true;
            lucide.createIcons();

            // Collect all editable fields
            const payload = {
                content: {
                    hero_slides: window.heroSlidesData || [],
                    hero_eyebrow: document.getElementById('edit-hero-eyebrow')?.innerText.trim() || '',
                    hero_heading_1: document.getElementById('edit-hero-heading-1')?.innerText.trim() || '',
                    hero_heading_2: document.getElementById('edit-hero-heading-2')?.innerText.trim() || '',
                    hero_description: document.getElementById('edit-hero-desc')?.innerText.trim() || '',
                    hero_image_main: document.getElementById('edit-hero-img-main')?.src || '',
                    hero_image_secondary: document.getElementById('edit-hero-img-sec')?.src || '',
                    hero_card_title: document.getElementById('edit-hero-card-title')?.innerText.trim() || '',
                    hero_card_description: document.getElementById('edit-hero-card-desc')?.innerText.trim() || '',
                    welcome_eyebrow: document.getElementById('edit-welcome-eyebrow')?.innerText.trim() || '',
                    welcome_heading: document.getElementById('edit-welcome-heading')?.innerText.trim() || '',
                    welcome_description: document.getElementById('edit-welcome-desc')?.innerText.trim() || '',
                    branch_section_eyebrow: document.getElementById('edit-branch-eyebrow')?.innerText.trim() || '',
                    branches: Array.from(document.querySelectorAll('[id^="branch-card-container-"]')).map((container, idx) => {
                        const select = container.querySelector('select');
                        return {
                            branch_id: parseInt(select?.value || (idx + 1)),
                            title: document.getElementById(`edit-branch-title-${idx}`)?.innerText.trim() || '',
                            tagline: document.getElementById(`edit-branch-tagline-${idx}`)?.innerText.trim() || '',
                            image: document.getElementById(`edit-branch-img-${idx}`)?.src || ''
                        };
                    }),
                    stay_eyebrow: document.getElementById('edit-stay-eyebrow')?.innerText.trim() || '',
                    stay_heading_1: document.getElementById('edit-stay-heading-1')?.innerText.trim() || '',
                    stay_heading_2: document.getElementById('edit-stay-heading-2')?.innerText.trim() || '',
                    stay_description: document.getElementById('edit-stay-desc')?.innerText.trim() || '',
                    stay_image: document.getElementById('edit-stay-img')?.src || '',
                    stay_image_title: document.getElementById('edit-stay-img-title')?.innerText.trim() || '',
                    stay_image_subtitle: document.getElementById('edit-stay-img-subtitle')?.innerText.trim() || '',
                    experience_eyebrow: document.getElementById('edit-exp-eyebrow')?.innerText.trim() || '',
                    experience_heading_1: document.getElementById('edit-exp-heading-1')?.innerText.trim() || '',
                    experience_heading_2: document.getElementById('edit-exp-heading-2')?.innerText.trim() || '',
                    experience_image_main: document.getElementById('edit-exp-img-main')?.src || '',
                    experience_image_local: document.getElementById('edit-exp-img-local')?.src || '',
                    experience_image_activities: document.getElementById('edit-exp-img-activities')?.src || '',
                    experience_image_family: document.getElementById('edit-exp-img-family')?.src || '',
                    experience_image_events: document.getElementById('edit-exp-img-events')?.src || '',
                    experience_image_dining: document.getElementById('edit-exp-img-dining')?.src || '',
                    experience_card_1_title: document.getElementById('edit-exp-card-1-title')?.innerText.trim() || '',
                    experience_card_1_desc: document.getElementById('edit-exp-card-1-desc')?.innerText.trim() || '',
                    experience_card_2_title: document.getElementById('edit-exp-card-2-title')?.innerText.trim() || '',
                    experience_card_2_desc: document.getElementById('edit-exp-card-2-desc')?.innerText.trim() || '',
                    experience_card_3_title: document.getElementById('edit-exp-card-3-title')?.innerText.trim() || '',
                    experience_card_3_desc: document.getElementById('edit-exp-card-3-desc')?.innerText.trim() || '',
                    experience_card_4_title: document.getElementById('edit-exp-card-4-title')?.innerText.trim() || '',
                    experience_card_4_desc: document.getElementById('edit-exp-card-4-desc')?.innerText.trim() || '',
                    experience_card_5_title: document.getElementById('edit-exp-card-5-title')?.innerText.trim() || '',
                    experience_card_5_desc: document.getElementById('edit-exp-card-5-desc')?.innerText.trim() || '',
                    experience_card_6_title: document.getElementById('edit-exp-card-6-title')?.innerText.trim() || '',
                    experience_card_6_desc: document.getElementById('edit-exp-card-6-desc')?.innerText.trim() || '',
                    experience_card_7_title: document.getElementById('edit-exp-card-7-title')?.innerText.trim() || '',
                    experience_card_7_desc: document.getElementById('edit-exp-card-7-desc')?.innerText.trim() || '',
                    experience_card_8_title: document.getElementById('edit-exp-card-8-title')?.innerText.trim() || '',
                    experience_card_8_desc: document.getElementById('edit-exp-card-8-desc')?.innerText.trim() || '',
                    gallery_eyebrow: document.getElementById('edit-gallery-eyebrow')?.innerText.trim() || '',
                    gallery_heading: document.getElementById('edit-gallery-heading')?.innerText.trim() || '',
                    gallery_images: [
                        {
                            url: document.getElementById('edit-gallery-img-0')?.src || '',
                            tag: document.getElementById('edit-gallery-tag-0')?.innerText.trim() || 'Rooms'
                        },
                        {
                            url: document.getElementById('edit-gallery-img-1')?.src || '',
                            tag: document.getElementById('edit-gallery-tag-1')?.innerText.trim() || 'Nature'
                        },
                        {
                            url: document.getElementById('edit-gallery-img-2')?.src || '',
                            tag: document.getElementById('edit-gallery-tag-2')?.innerText.trim() || 'Dining'
                        }
                    ],
                    dining_eyebrow: document.getElementById('edit-dining-eyebrow')?.innerText.trim() || '',
                    dining_heading_1: document.getElementById('edit-dining-heading-1')?.innerText.trim() || '',
                    dining_heading_2: document.getElementById('edit-dining-heading-2')?.innerText.trim() || '',
                    dining_image: document.getElementById('edit-dining-img')?.src || '',
                    dining_title: document.getElementById('edit-dining-title')?.innerText.trim() || '',
                    dining_description: document.getElementById('edit-dining-desc')?.innerText.trim() || '',
                    spices_eyebrow: document.getElementById('edit-spices-eyebrow')?.innerText.trim() || '',
                    spices_heading_1: document.getElementById('edit-spices-heading-1')?.innerText.trim() || '',
                    spices_heading_2: document.getElementById('edit-spices-heading-2')?.innerText.trim() || '',
                    spices_pinned_product_1_id: parseInt(document.getElementById('select-pinned-spice-1')?.value || '1'),
                    spices_pinned_product_2_id: parseInt(document.getElementById('select-pinned-spice-2')?.value || '2'),
                    nearby_eyebrow: document.getElementById('edit-nearby-eyebrow')?.innerText.trim() || '',
                    nearby_heading_1: document.getElementById('edit-nearby-heading-1')?.innerText.trim() || '',
                    nearby_heading_2: document.getElementById('edit-nearby-heading-2')?.innerText.trim() || '',
                    locations_eyebrow: document.getElementById('edit-locations-eyebrow')?.innerText.trim() || '',
                    locations_heading_1: document.getElementById('edit-locations-heading-1')?.innerText.trim() || '',
                    locations_heading_2: document.getElementById('edit-locations-heading-2')?.innerText.trim() || '',
                    nearby_item_1_name: document.getElementById('edit-nearby-item-1-name')?.innerText.trim() || '',
                    nearby_item_1_dist: document.getElementById('edit-nearby-item-1-dist')?.innerText.trim() || '',
                    nearby_item_1_tag: document.getElementById('edit-nearby-item-1-tag')?.innerText.trim() || '',
                    nearby_item_2_name: document.getElementById('edit-nearby-item-2-name')?.innerText.trim() || '',
                    nearby_item_2_dist: document.getElementById('edit-nearby-item-2-dist')?.innerText.trim() || '',
                    nearby_item_2_tag: document.getElementById('edit-nearby-item-2-tag')?.innerText.trim() || '',
                    nearby_item_3_name: document.getElementById('edit-nearby-item-3-name')?.innerText.trim() || '',
                    nearby_item_3_dist: document.getElementById('edit-nearby-item-3-dist')?.innerText.trim() || '',
                    nearby_item_3_tag: document.getElementById('edit-nearby-item-3-tag')?.innerText.trim() || '',
                    review_eyebrow: document.getElementById('edit-review-eyebrow')?.innerText.trim() || '',
                    review_quote: document.getElementById('edit-review-quote')?.innerText.trim() || '',
                    review_author: document.getElementById('edit-review-author')?.innerText.trim() || '',
                    review_subtitle: document.getElementById('edit-review-subtitle')?.innerText.trim() || '',
                    cta_eyebrow: document.getElementById('edit-cta-eyebrow')?.innerText.trim() || '',
                    cta_heading_1: document.getElementById('edit-cta-heading-1')?.innerText.trim() || '',
                    cta_heading_2: document.getElementById('edit-cta-heading-2')?.innerText.trim() || '',
                    cta_description: document.getElementById('edit-cta-desc')?.innerText.trim() || '',
                    footer_brand_title: document.getElementById('edit-footer-brand-title')?.innerText.trim() || '',
                    footer_brand_tagline: document.getElementById('edit-footer-brand-tagline')?.innerText.trim() || '',
                    footer_brand_desc: document.getElementById('edit-footer-brand-desc')?.innerText.trim() || '',
                    footer_badge: document.getElementById('edit-footer-badge')?.innerText.trim() || '',
                    footer_dest_title: document.getElementById('edit-footer-dest-title')?.innerText.trim() || '',
                    footer_exp_title: document.getElementById('edit-footer-exp-title')?.innerText.trim() || '',
                    footer_exp_link_1: document.getElementById('edit-footer-exp-1')?.innerText.trim() || '',
                    footer_exp_link_2: document.getElementById('edit-footer-exp-2')?.innerText.trim() || '',
                    footer_exp_link_3: document.getElementById('edit-footer-exp-3')?.innerText.trim() || '',
                    footer_exp_link_4: document.getElementById('edit-footer-exp-4')?.innerText.trim() || '',
                    footer_concierge_title: document.getElementById('edit-footer-concierge-title')?.innerText.trim() || '',
                    footer_concierge_desc: document.getElementById('edit-footer-concierge-desc')?.innerText.trim() || '',
                    footer_phone: document.getElementById('edit-footer-phone')?.innerText.trim() || '',
                    footer_email: document.getElementById('edit-footer-email')?.innerText.trim() || '',
                    footer_chat_btn: document.getElementById('edit-footer-chat-btn')?.innerText.trim() || '',
                    footer_copyright: document.getElementById('edit-footer-copyright')?.innerText.trim() || '',
                    footer_support_link: document.getElementById('edit-footer-support')?.innerText.trim() || '',
                    footer_policy_note: document.getElementById('edit-footer-policy')?.innerText.trim() || ''
                }
            };

            try {
                const res = await fetch('/admin/api/homepage-content', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const indicator = document.getElementById('editor-status-indicator');
                    if (indicator) {
                        indicator.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span><span>All Changes Saved & Live</span>';
                        indicator.className = 'hidden sm:flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full';
                    }
                } else {
                    showToast(data.message || 'Failed to save', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Failed to save homepage content', 'error');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                lucide.createIcons();
            }
        }

        async function resetHomepageVisualContent() {
            if (!confirm('Are you sure you want to reset all homepage text and photos to original brand defaults?')) return;
            try {
                const res = await fetch('/admin/api/homepage-content/reset', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                }
            } catch (err) {
                showToast('Failed to reset content', 'error');
            }
        }

        /* =========================================================================
           HERO CAROUSEL SLIDES MANAGER & CROPPER.JS ENGINE (PPT LIVE STUDIO)
           ========================================================================= */
        @php
            $heroSlidesForJs = $homepageContent['hero_slides'] ?? null;
            if (empty($heroSlidesForJs) || !is_array($heroSlidesForJs)) {
                $heroSlidesForJs = [];
                foreach ($branches as $idx => $b) {
                    $heroSlidesForJs[] = [
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
            }
            usort($heroSlidesForJs, fn($a, $b) => ($a['sort_order'] ?? 1) <=> ($b['sort_order'] ?? 1));
        @endphp

        window.heroSlidesData = @json($heroSlidesForJs);
        window.activeHeroCropper = null;
        window.currentAdminSlideIdx = 0;

        function refreshAdminCarouselPreview() {
            const container = document.getElementById('admin-slides-container');
            const dotsContainer = document.getElementById('admin-preview-dots');
            const counterEl = document.getElementById('admin-slide-current-idx');
            const totalEl = document.getElementById('admin-slide-total-count');
            const badgeEl = document.getElementById('hero-slides-counter-badge');

            if (!container || !window.heroSlidesData) return;

            const slides = window.heroSlidesData;
            if (totalEl) totalEl.innerText = slides.length;
            if (badgeEl) badgeEl.innerText = `${slides.length} Slides`;

            // Re-render preview slides
            container.innerHTML = slides.map((s, idx) => `
                <div class="admin-preview-slide absolute inset-0 transition-all duration-500 ease-out ${idx === window.currentAdminSlideIdx ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-95 pointer-events-none z-0'}" data-admin-slide-idx="${idx}">
                    <img src="${escapeAdminHtml(s.image)}" class="w-full h-full object-cover" alt="${escapeAdminHtml(s.title)}" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

                    <!-- Top Left Pill Tag -->
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-xs rounded-full px-3.5 py-1.5 text-[10px] font-bold uppercase text-[#063F34] border border-white/60 shadow-2xs flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-3 h-3 text-[#0B5D4B]"></i>
                        <span>${escapeAdminHtml(s.tag || 'Krishna Cottages')}</span>
                    </div>

                    <!-- Top Right Counter & Edit Button -->
                    <div class="absolute top-4 right-4 z-20 flex items-center gap-1.5">
                        <span class="bg-black/60 text-white rounded-full px-2.5 py-1 text-[10px] font-bold tracking-wider backdrop-blur-xs">
                            0${idx + 1} / 0${slides.length}
                        </span>
                        <button type="button" onclick="openHeroSlideModal(${idx})" class="p-1.5 rounded-full bg-white text-[#063F34] hover:bg-[#C7A76A] hover:text-white shadow-md transition cursor-pointer" title="Edit this slide with cropper">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <!-- Bottom Floating Glass Card -->
                    <div class="absolute inset-x-4 bottom-4 p-4 bg-[#063F34]/95 backdrop-blur-md text-white rounded-2xl border border-white/15 shadow-xl">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-serif font-bold text-sm text-white truncate">${escapeAdminHtml(s.title)}</h4>
                                    ${s.badge ? `<span class="shrink-0 rounded-full bg-[#C7A76A]/20 px-2 py-0.5 text-[9px] font-bold text-[#C7A76A] border border-[#C7A76A]/30">${escapeAdminHtml(s.badge)}</span>` : ''}
                                </div>
                                <p class="mt-1 text-[10px] text-white/75 leading-relaxed line-clamp-2">${escapeAdminHtml(s.description)}</p>
                            </div>
                            <div class="shrink-0 grid h-8 w-8 place-items-center rounded-xl bg-[#C7A76A] text-[#063F34] shadow-xs">
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            // Re-render dots
            if (dotsContainer) {
                dotsContainer.innerHTML = slides.map((_, idx) => `
                    <button type="button" onclick="goToAdminPreviewSlide(${idx})" class="admin-dot h-1.5 rounded-full transition-all duration-300 ${idx === window.currentAdminSlideIdx ? 'w-5 bg-[#063F34]' : 'w-1.5 bg-gray-300'}"></button>
                `).join('');
            }

            if (counterEl) counterEl.innerText = window.currentAdminSlideIdx + 1;
            refreshIcons();
        }

        window.currentSlidesCrudIdx = 0;

        function refreshHeroSlidesManagerList() {
            if (!window.heroSlidesData) return;
            const slides = window.heroSlidesData;

            // 1. Refresh count badges
            const sidebarBadge = document.getElementById('sidebar-slides-count');
            if (sidebarBadge) sidebarBadge.innerText = slides.length;
            const crudCounter = document.getElementById('slides-crud-counter-badge');
            if (crudCounter) crudCounter.innerText = `${slides.length} Active Slides`;
            const heroCounter = document.getElementById('hero-slides-counter-badge');
            if (heroCounter) heroCounter.innerText = `${slides.length} Slides`;

            // 2. Render Visual Editor slides manager list
            const listContainer = document.getElementById('hero-slides-manager-list');
            if (listContainer) {
                listContainer.innerHTML = slides.map((s, idx) => `
                    <div class="hero-slide-item rounded-2xl border border-gray-200 bg-[#F7F5EF]/60 p-3 flex flex-col justify-between space-y-3 hover:border-[#0B5D4B]/40 hover:shadow-md transition group" data-slide-index="${idx}">
                        <div class="space-y-2.5">
                            <div class="relative h-36 rounded-xl overflow-hidden bg-black/5 border border-gray-200 shadow-2xs">
                                <img src="${escapeAdminHtml(s.image)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="${escapeAdminHtml(s.title)}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/20"></div>
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-[#063F34] text-white text-[10px] font-bold shadow-xs">
                                    Slide #${s.sort_order ?? (idx + 1)}
                                </span>
                                <button type="button" onclick="toggleHeroSlideStatus(${idx})" class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[10px] font-bold cursor-pointer transition ${s.status === 'active' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-500 hover:bg-gray-600 text-white'}">
                                    ${s.status === 'active' ? 'Active' : 'Hidden'}
                                </button>
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-full bg-white/90 text-[#063F34] text-[9px] font-bold backdrop-blur-xs">
                                    ${escapeAdminHtml(s.tag || 'Kerala')}
                                </span>
                                ${s.badge ? `<span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-[#C7A76A] text-[#063F34] text-[9px] font-bold">${escapeAdminHtml(s.badge)}</span>` : ''}
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-[#063F34] line-clamp-1">${escapeAdminHtml(s.title)}</h4>
                                ${s.subtitle ? `<p class="text-[10px] font-semibold text-[#0B5D4B] mt-0.5">${escapeAdminHtml(s.subtitle)}</p>` : ''}
                                <p class="text-[10px] text-[#5A6B65] mt-1 line-clamp-2 leading-relaxed">${escapeAdminHtml(s.description)}</p>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-gray-200/70 flex items-center justify-between gap-1">
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="moveHeroSlide(${idx}, -1)" class="p-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 transition cursor-pointer disabled:opacity-40 disabled:pointer-events-none" title="Move Slide Earlier" ${idx === 0 ? 'disabled' : ''}>
                                    <i data-lucide="chevron-up" class="w-3.5 h-3.5"></i>
                                </button>
                                <button type="button" onclick="moveHeroSlide(${idx}, 1)" class="p-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 transition cursor-pointer disabled:opacity-40 disabled:pointer-events-none" title="Move Slide Later" ${idx === slides.length - 1 ? 'disabled' : ''}>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="openHeroSlideModal(${idx})" class="px-2.5 py-1 rounded-lg border border-[#0B5D4B]/30 bg-emerald-50/60 hover:bg-emerald-100 text-[#0B5D4B] text-[11px] font-bold transition flex items-center gap-1 cursor-pointer">
                                    <i data-lucide="edit-2" class="w-3 h-3"></i> Edit & Crop
                                </button>
                                <button type="button" onclick="deleteHeroSlide(${idx})" class="p-1 rounded-lg border border-red-200 bg-red-50/50 hover:bg-red-100 text-red-700 transition cursor-pointer" title="Delete Slide">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // 3. Render Dedicated Slides CRUD Section list
            const crudList = document.getElementById('slides-crud-manager-list');
            if (crudList) {
                crudList.innerHTML = slides.map((s, idx) => `
                    <div class="slide-crud-card rounded-2xl border border-gray-200 bg-[#FAF7F0]/70 p-4 flex flex-col justify-between space-y-3.5 hover:border-brand-primary/40 hover:shadow-lg transition duration-200 group" data-slide-index="${idx}">
                        <div class="space-y-3">
                            <div class="relative h-44 rounded-xl overflow-hidden bg-black/10 border border-gray-200 shadow-2xs">
                                <img src="${escapeAdminHtml(s.image)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="${escapeAdminHtml(s.title)}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-black/30"></div>
                                <span class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-lg bg-brand-deep text-white text-[11px] font-bold shadow-xs">
                                    #${s.sort_order ?? (idx + 1)}
                                </span>
                                <button type="button" onclick="toggleHeroSlideStatus(${idx})" class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-lg text-[10px] font-bold transition shadow-xs cursor-pointer flex items-center gap-1 ${s.status === 'active' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-600 hover:bg-gray-700 text-white'}" title="Click to toggle Active/Hidden">
                                    <span class="w-1.5 h-1.5 rounded-full ${s.status === 'active' ? 'bg-emerald-300' : 'bg-gray-300'}"></span>
                                    <span>${s.status === 'active' ? 'Active' : 'Hidden'}</span>
                                </button>
                                <span class="absolute bottom-2.5 left-2.5 px-3 py-1 rounded-full bg-white/95 text-brand-deep text-[10px] font-bold shadow-xs flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-brand-accent"></i>
                                    <span>${escapeAdminHtml(s.tag || 'Kerala Retreat')}</span>
                                </span>
                                ${s.badge ? `<span class="absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded-full bg-brand-accent text-brand-deep text-[10px] font-bold shadow-xs">${escapeAdminHtml(s.badge)}</span>` : ''}
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-bold text-sm text-brand-deep line-clamp-1">${escapeAdminHtml(s.title)}</h4>
                                ${s.subtitle ? `<p class="text-[11px] font-semibold text-brand-primary line-clamp-1">${escapeAdminHtml(s.subtitle)}</p>` : ''}
                                <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed pt-1">${escapeAdminHtml(s.description)}</p>
                            </div>
                            <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between text-[11px] text-gray-500 font-mono">
                                <span class="truncate max-w-[200px]" title="${escapeAdminHtml(s.link || '/rooms')}">
                                    <i data-lucide="link" class="w-3 h-3 inline mr-1 text-gray-400"></i>
                                    ${escapeAdminHtml(s.link || '/rooms')}
                                </span>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-200/80 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="moveHeroSlide(${idx}, -1)" class="p-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 transition cursor-pointer disabled:opacity-30 disabled:pointer-events-none shadow-2xs" title="Move Slide Earlier" ${idx === 0 ? 'disabled' : ''}>
                                    <i data-lucide="arrow-up" class="w-3.5 h-3.5"></i>
                                </button>
                                <button type="button" onclick="moveHeroSlide(${idx}, 1)" class="p-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 transition cursor-pointer disabled:opacity-30 disabled:pointer-events-none shadow-2xs" title="Move Slide Later" ${idx === slides.length - 1 ? 'disabled' : ''}>
                                    <i data-lucide="arrow-down" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="openHeroSlideModal(${idx})" class="px-3 py-1.5 rounded-xl border border-brand-primary/30 bg-emerald-50 hover:bg-emerald-100 text-brand-primary text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    <span>Edit & Crop</span>
                                </button>
                                <button type="button" onclick="deleteHeroSlide(${idx})" class="p-2 rounded-xl border border-red-200 bg-red-50/70 hover:bg-red-100 text-red-700 transition cursor-pointer shadow-2xs" title="Delete Slide">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // 4. Render Dedicated Slides CRUD preview track
            const crudTrack = document.getElementById('slides-crud-preview-track');
            const crudDots = document.getElementById('slides-crud-preview-dots');
            if (crudTrack) {
                crudTrack.innerHTML = slides.map((s, sIdx) => `
                    <div class="slides-crud-slide absolute inset-0 transition-all duration-700 ease-in-out ${sIdx === window.currentSlidesCrudIdx ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 pointer-events-none z-0'}" data-slide-index="${sIdx}">
                        <img src="${escapeAdminHtml(s.image)}" alt="${escapeAdminHtml(s.title)}" class="w-full h-full object-cover brightness-[0.78]" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-black/30"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/20 to-black/50"></div>
                        <div class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/50 backdrop-blur-md text-white text-[10px] sm:text-xs font-semibold uppercase tracking-wider border border-white/20">
                                <i data-lucide="map-pin" class="w-3 h-3 text-brand-accent"></i>
                                <span>${escapeAdminHtml(s.tag || s.subtitle || 'Krishna Cottages')}</span>
                            </span>
                            ${s.badge ? `<span class="inline-flex items-center px-2.5 py-1 rounded-full bg-brand-accent/25 backdrop-blur-md text-brand-accent text-[10px] font-bold border border-brand-accent/30">${escapeAdminHtml(s.badge)}</span>` : ''}
                        </div>
                        <div class="absolute top-4 right-4 sm:top-6 sm:right-6 z-20 flex items-center gap-2">
                            <div class="text-white/80 bg-black/50 backdrop-blur-md px-3 py-1 rounded-full text-xs font-mono border border-white/15">
                                <span class="text-white font-bold">0${sIdx + 1}</span> / 0${slides.length}
                            </div>
                            <button type="button" onclick="openHeroSlideModal(${sIdx})" class="p-1.5 rounded-full bg-white text-brand-deep hover:bg-brand-accent hover:text-white shadow-md transition cursor-pointer" title="Edit this slide">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-8 max-w-2xl mx-auto text-white">
                            <p class="text-brand-accent text-[10px] sm:text-xs tracking-[.25em] mb-2 font-bold uppercase">
                                ${escapeAdminHtml(s.subtitle || s.tag || 'HERITAGE SANCTUARY')}
                            </p>
                            <h3 class="serif text-2xl sm:text-4xl font-bold tracking-tight text-white drop-shadow-md">
                                ${escapeAdminHtml(s.title)}
                            </h3>
                            <p class="mt-2 text-xs sm:text-sm text-white/90 leading-relaxed line-clamp-2">
                                ${escapeAdminHtml(s.description)}
                            </p>
                            <div class="mt-4 flex items-center gap-2.5">
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-primary text-white font-bold text-xs shadow-md">
                                    <span>Explore Cottages</span>
                                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brand-accent"></i>
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white/20 backdrop-blur-md text-white font-semibold text-xs border border-white/20">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5 text-brand-accent"></i>
                                    <span>Concierge</span>
                                </span>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            if (crudDots) {
                crudDots.innerHTML = slides.map((_, dIdx) => `
                    <button type="button" onclick="goToSlidesCrudSlide(${dIdx})" class="slides-crud-dot h-2 rounded-full transition-all duration-300 cursor-pointer ${dIdx === window.currentSlidesCrudIdx ? 'w-6 bg-brand-accent' : 'w-2 bg-white/40'}"></button>
                `).join('');
            }

            refreshIcons();
        }

        async function toggleHeroSlideStatus(idx) {
            if (!window.heroSlidesData || !window.heroSlidesData[idx]) return;
            const s = window.heroSlidesData[idx];
            s.status = (s.status === 'active') ? 'hidden' : 'active';
            await persistHeroSlidesBackend();
            refreshAdminCarouselPreview();
            refreshHeroSlidesManagerList();
            showToast(`Slide "${s.title}" is now ${s.status === 'active' ? 'Active' : 'Hidden'}`, 'info');
        }

        function cycleSlidesCrudSlide(delta) {
            const total = window.heroSlidesData.length;
            if (total <= 1) return;
            window.currentSlidesCrudIdx = (window.currentSlidesCrudIdx + delta + total) % total;
            updateSlidesCrudActiveSlide();
        }

        function goToSlidesCrudSlide(idx) {
            window.currentSlidesCrudIdx = idx;
            updateSlidesCrudActiveSlide();
        }

        function updateSlidesCrudActiveSlide() {
            const slides = document.querySelectorAll('.slides-crud-slide');
            const dots = document.querySelectorAll('.slides-crud-dot');
            slides.forEach((s, i) => {
                if (i === window.currentSlidesCrudIdx) {
                    s.classList.remove('opacity-0', 'scale-105', 'pointer-events-none', 'z-0');
                    s.classList.add('opacity-100', 'scale-100', 'z-10');
                } else {
                    s.classList.remove('opacity-100', 'scale-100', 'z-10');
                    s.classList.add('opacity-0', 'scale-105', 'pointer-events-none', 'z-0');
                }
            });
            dots.forEach((d, i) => {
                if (i === window.currentSlidesCrudIdx) {
                    d.className = 'slides-crud-dot h-2 rounded-full transition-all duration-300 cursor-pointer w-6 bg-brand-accent';
                } else {
                    d.className = 'slides-crud-dot h-2 rounded-full transition-all duration-300 cursor-pointer w-2 bg-white/40';
                }
            });
        }

        function cycleAdminPreviewSlide(delta) {
            const total = window.heroSlidesData.length;
            if (total <= 1) return;
            window.currentAdminSlideIdx = (window.currentAdminSlideIdx + delta + total) % total;
            updateAdminPreviewActiveSlide();
        }

        function goToAdminPreviewSlide(idx) {
            window.currentAdminSlideIdx = idx;
            updateAdminPreviewActiveSlide();
        }

        function updateAdminPreviewActiveSlide() {
            const slides = document.querySelectorAll('.admin-preview-slide');
            const dots = document.querySelectorAll('.admin-dot');
            const counterEl = document.getElementById('admin-slide-current-idx');

            slides.forEach((s, i) => {
                if (i === window.currentAdminSlideIdx) {
                    s.classList.remove('opacity-0', 'scale-95', 'pointer-events-none', 'z-0');
                    s.classList.add('opacity-100', 'scale-100', 'z-10');
                } else {
                    s.classList.remove('opacity-100', 'scale-100', 'z-10');
                    s.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'z-0');
                }
            });

            dots.forEach((d, i) => {
                if (i === window.currentAdminSlideIdx) {
                    d.classList.remove('w-1.5', 'bg-gray-300');
                    d.classList.add('w-5', 'bg-[#063F34]');
                } else {
                    d.classList.remove('w-5', 'bg-[#063F34]');
                    d.classList.add('w-1.5', 'bg-gray-300');
                }
            });

            if (counterEl) counterEl.innerText = window.currentAdminSlideIdx + 1;
        }

        // ================= CROPPER.JS & SLIDE MODAL CONTROLS =================
        function openHeroSlideModal(slideIndex = null) {
            const modal = document.getElementById('modal-hero-slide');
            if (!modal) return;

            // Destroy previous cropper if open
            if (window.activeHeroCropper) {
                window.activeHeroCropper.destroy();
                window.activeHeroCropper = null;
            }

            document.getElementById('cropper-file-input').value = '';
            document.getElementById('cropper-url-input').value = '';
            document.getElementById('cropper-error-box').classList.add('hidden');
            document.getElementById('slide-branch-helper').value = '';

            const titleEl = document.getElementById('hero-slide-modal-title');

            if (slideIndex !== null && window.heroSlidesData[slideIndex]) {
                const s = window.heroSlidesData[slideIndex];
                if (titleEl) titleEl.innerText = 'Edit Hero Carousel Slide';
                document.getElementById('slide-edit-index').value = slideIndex;
                document.getElementById('slide-id').value = s.id || '';
                document.getElementById('slide-image-url').value = s.image || '';
                document.getElementById('slide-title').value = s.title || '';
                document.getElementById('slide-subtitle').value = s.subtitle || '';
                document.getElementById('slide-tag').value = s.tag || '';
                document.getElementById('slide-description').value = s.description || '';
                document.getElementById('slide-badge').value = s.badge || '';
                document.getElementById('slide-link').value = s.link || '';
                document.getElementById('slide-sort-order').value = s.sort_order || (slideIndex + 1);
                document.getElementById('slide-status').value = s.status || 'active';

                // Display applied preview box
                if (s.image) {
                    document.getElementById('cropper-applied-img').src = s.image;
                    document.getElementById('cropper-applied-preview-box').classList.remove('hidden');
                    document.getElementById('cropper-workspace').classList.add('hidden');
                } else {
                    document.getElementById('cropper-applied-preview-box').classList.add('hidden');
                    document.getElementById('cropper-workspace').classList.add('hidden');
                }
            } else {
                if (titleEl) titleEl.innerText = 'Add Hero Carousel Slide';
                document.getElementById('slide-edit-index').value = '';
                document.getElementById('slide-id').value = Date.now();
                document.getElementById('slide-image-url').value = '';
                document.getElementById('slide-title').value = '';
                document.getElementById('slide-subtitle').value = '';
                document.getElementById('slide-tag').value = '';
                document.getElementById('slide-description').value = '';
                document.getElementById('slide-badge').value = '★ 4.9 Rating';
                document.getElementById('slide-link').value = '/rooms';
                document.getElementById('slide-sort-order').value = (window.heroSlidesData.length + 1);
                document.getElementById('slide-status').value = 'active';

                document.getElementById('cropper-applied-preview-box').classList.add('hidden');
                document.getElementById('cropper-workspace').classList.add('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            refreshIcons();
        }

        function closeHeroSlideModal() {
            const modal = document.getElementById('modal-hero-slide');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            if (window.activeHeroCropper) {
                window.activeHeroCropper.destroy();
                window.activeHeroCropper = null;
            }
        }

        function handleHeroSlideFileInput(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(evt) {
                initHeroCropper(evt.target.result);
            };
            reader.readAsDataURL(file);
        }

        function loadHeroImageFromUrl() {
            const url = document.getElementById('cropper-url-input').value.trim();
            if (!url) {
                showToast('Please enter a valid image URL', 'warning');
                return;
            }
            initHeroCropper(url);
        }

        function reopenCropperWorkspace() {
            const currentImg = document.getElementById('slide-image-url').value;
            if (currentImg) {
                initHeroCropper(currentImg);
            }
        }

        function initHeroCropper(sourceUrl) {
            const workspace = document.getElementById('cropper-workspace');
            const previewBox = document.getElementById('cropper-applied-preview-box');
            const targetImg = document.getElementById('cropper-image-target');
            const errBox = document.getElementById('cropper-error-box');

            if (errBox) errBox.classList.add('hidden');
            if (previewBox) previewBox.classList.add('hidden');
            if (workspace) workspace.classList.remove('hidden');

            if (window.activeHeroCropper) {
                window.activeHeroCropper.destroy();
                window.activeHeroCropper = null;
            }

            targetImg.src = sourceUrl;

            // Wait until image loads to instantiate cropper
            targetImg.onload = function() {
                if (typeof Cropper === 'undefined') {
                    console.error('Cropper.js is not loaded');
                    showToast('Cropper library not ready', 'error');
                    return;
                }

                window.activeHeroCropper = new Cropper(targetImg, {
                    aspectRatio: 4 / 3, // Recommended ratio for resort card
                    viewMode: 2,
                    autoCropArea: 1,
                    responsive: true,
                    restore: false,
                    checkCrossOrigin: false,
                    background: false,
                    zoomable: true,
                    rotatable: true,
                    scalable: true,
                });
                refreshIcons();
            };
        }

        function setCropperRatio(ratio, btn) {
            if (!window.activeHeroCropper) return;
            window.activeHeroCropper.setAspectRatio(ratio);

            document.querySelectorAll('.cropper-ratio-btn').forEach(b => {
                b.className = 'cropper-ratio-btn px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white font-medium transition';
            });
            btn.className = 'cropper-ratio-btn active px-2.5 py-1 rounded-lg bg-brand-accent text-brand-deep font-bold transition';
        }

        function rotateCropper(deg) {
            if (window.activeHeroCropper) window.activeHeroCropper.rotate(deg);
        }

        function zoomCropper(ratio) {
            if (window.activeHeroCropper) window.activeHeroCropper.zoom(ratio);
        }

        function resetCropper() {
            if (window.activeHeroCropper) window.activeHeroCropper.reset();
        }

        async function applyAndUploadCrop() {
            if (!window.activeHeroCropper) {
                showToast('No active crop workspace open', 'warning');
                return;
            }

            const btn = document.getElementById('btn-crop-apply');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Uploading...</span>';
            refreshIcons();

            try {
                // Get high quality cropped canvas (1400x1050 max)
                const canvas = window.activeHeroCropper.getCroppedCanvas({
                    maxWidth: 1400,
                    maxHeight: 1050,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                const dataUrl = canvas.toDataURL('image/jpeg', 0.88);

                // Upload base64 cropped canvas to server
                const res = await fetch('/admin/api/upload-image', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ image_data: dataUrl })
                });

                const data = await res.json();
                if (data.success && data.url) {
                    document.getElementById('slide-image-url').value = data.url;
                    document.getElementById('cropper-applied-img').src = data.url;
                    document.getElementById('cropper-applied-preview-box').classList.remove('hidden');
                    document.getElementById('cropper-workspace').classList.add('hidden');

                    if (window.activeHeroCropper) {
                        window.activeHeroCropper.destroy();
                        window.activeHeroCropper = null;
                    }

                    showToast('Cropped image saved and attached to slide!', 'success');
                } else {
                    showToast(data.message || 'Failed to process image crop', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error uploading cropped image', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                refreshIcons();
            }
        }

        function handleBranchHelperSelect(branchId) {
            const select = document.getElementById('slide-branch-helper');
            const opt = select.options[select.selectedIndex];
            if (!opt || !branchId) return;

            const name = opt.getAttribute('data-name');
            const city = opt.getAttribute('data-city');
            const tagline = opt.getAttribute('data-tagline');
            const image = opt.getAttribute('data-image');
            const link = opt.getAttribute('data-link');

            if (name) document.getElementById('slide-title').value = name;
            if (city) {
                document.getElementById('slide-subtitle').value = city + ' Retreat';
                document.getElementById('slide-tag').value = city + ' · Heritage Cottages';
            }
            if (tagline) document.getElementById('slide-description').value = tagline;
            if (link) document.getElementById('slide-link').value = link;

            if (image && !document.getElementById('slide-image-url').value) {
                document.getElementById('slide-image-url').value = image;
                document.getElementById('cropper-applied-img').src = image;
                document.getElementById('cropper-applied-preview-box').classList.remove('hidden');
                document.getElementById('cropper-workspace').classList.add('hidden');
            }
            showToast(`Auto-filled details from ${name}`, 'info');
        }

        async function submitHeroSlideForm(e) {
            e.preventDefault();

            const imageUrl = document.getElementById('slide-image-url').value.trim();
            const title = document.getElementById('slide-title').value.trim();
            const subtitle = document.getElementById('slide-subtitle').value.trim();
            const tag = document.getElementById('slide-tag').value.trim();
            const description = document.getElementById('slide-description').value.trim();
            const badge = document.getElementById('slide-badge').value.trim();
            const link = document.getElementById('slide-link').value.trim();
            const sortOrder = parseInt(document.getElementById('slide-sort-order').value || '1');
            const status = document.getElementById('slide-status').value;
            const editIndexVal = document.getElementById('slide-edit-index').value;

            if (!imageUrl) {
                document.getElementById('cropper-error-box').classList.remove('hidden');
                document.getElementById('cropper-error-text').innerText = 'Please upload or crop a photo for this slide before saving.';
                showToast('Slide photo is required', 'warning');
                return;
            }

            const slideObj = {
                id: document.getElementById('slide-id').value || Date.now(),
                title: title,
                subtitle: subtitle,
                tag: tag,
                description: description,
                image: imageUrl,
                badge: badge,
                link: link,
                sort_order: sortOrder,
                status: status
            };

            if (editIndexVal !== '' && window.heroSlidesData[parseInt(editIndexVal)]) {
                window.heroSlidesData[parseInt(editIndexVal)] = slideObj;
            } else {
                window.heroSlidesData.push(slideObj);
            }

            // Sort by order
            window.heroSlidesData.sort((a, b) => (a.sort_order || 1) - (b.sort_order || 1));

            // Persist to backend
            await persistHeroSlidesBackend();

            // Refresh UI
            refreshAdminCarouselPreview();
            refreshHeroSlidesManagerList();
            closeHeroSlideModal();
        }

        async function moveHeroSlide(idx, direction) {
            const targetIdx = idx + direction;
            if (targetIdx < 0 || targetIdx >= window.heroSlidesData.length) return;

            // Swap elements
            const temp = window.heroSlidesData[idx];
            window.heroSlidesData[idx] = window.heroSlidesData[targetIdx];
            window.heroSlidesData[targetIdx] = temp;

            // Recalculate 1-based sort order
            window.heroSlidesData.forEach((s, i) => {
                s.sort_order = i + 1;
            });

            await persistHeroSlidesBackend();
            refreshAdminCarouselPreview();
            refreshHeroSlidesManagerList();
            showToast('Slide order updated and saved', 'success');
        }

        async function deleteHeroSlide(idx) {
            const slide = window.heroSlidesData[idx];
            if (!confirm(`Are you sure you want to remove slide "${slide ? slide.title : 'this slide'}" from the hero carousel?`)) {
                return;
            }

            window.heroSlidesData.splice(idx, 1);
            window.heroSlidesData.forEach((s, i) => {
                s.sort_order = i + 1;
            });

            if (window.currentAdminSlideIdx >= window.heroSlidesData.length) {
                window.currentAdminSlideIdx = Math.max(0, window.heroSlidesData.length - 1);
            }

            await persistHeroSlidesBackend();
            refreshAdminCarouselPreview();
            refreshHeroSlidesManagerList();
            showToast('Slide removed from carousel', 'info');
        }

        async function persistHeroSlidesBackend() {
            try {
                const res = await fetch('/admin/api/hero-slides', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ slides: window.heroSlidesData })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Hero carousel slides live and updated!', 'success');
                } else {
                    showToast(data.message || 'Failed to save slide order', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Failed to save slides to server', 'error');
            }
        }

        // ================= UNIVERSAL ENTITY CRUD HANDLERS =================
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => refreshIcons(), 30);
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('hidden');
        }

        async function handleEntitySubmit(e, endpointUrl, modalId) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerText : 'Save';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Saving...';
            }

            const formData = new FormData(form);

            try {
                const response = await fetch(endpointUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal(modalId);
                    form.reset();
                    setTimeout(() => window.location.reload(), 700);
                } else {
                    const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Error saving record.');
                    showToast(errMsg, 'error');
                }
            } catch (error) {
                console.error(error);
                showToast('Network error while processing request.', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                }
            }
        }

        // ================= RAZORPAY GATEWAY MANAGEMENT =================
        async function handleRazorpaySettingsSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btn-save-razorpay');
            const btnText = document.getElementById('btn-save-razorpay-text');
            const originalText = btnText ? btnText.innerText : 'Save';

            if (btn) {
                btn.disabled = true;
                if (btnText) btnText.innerText = 'Saving Credentials...';
            }

            try {
                const formData = new FormData(form);
                const res = await fetch('{{ route("admin.settings.razorpay") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 750);
                } else {
                    const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Error saving Razorpay settings.');
                    showToast(errMsg, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error while saving Razorpay settings.', 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    if (btnText) btnText.innerText = originalText;
                }
            }
        }

        async function testRazorpayConnectionAdmin() {
            const btn = document.getElementById('btn-test-razorpay');
            const btnText = document.getElementById('btn-test-text');
            const resultBox = document.getElementById('razorpay-test-result');
            const originalText = btnText ? btnText.innerText : 'Test API Connection';

            const keyId = document.getElementById('razorpay_key_id')?.value?.trim();
            const keySecret = document.getElementById('razorpay_key_secret')?.value?.trim();

            if (!keyId || !keySecret) {
                showToast('Please enter both Key ID and Key Secret before testing.', 'error');
                return;
            }

            if (btn) {
                btn.disabled = true;
                if (btnText) btnText.innerText = 'Testing API...';
            }
            if (resultBox) {
                resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-blue-50 text-blue-800 border-blue-200';
                resultBox.innerHTML = '<span class="animate-spin inline-block w-3.5 h-3.5 border-2 border-blue-600 border-t-transparent rounded-full"></span> Dispatching test order to Razorpay API...';
                resultBox.classList.remove('hidden');
            }

            try {
                const res = await fetch('{{ route("admin.settings.razorpay.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ key_id: keyId, key_secret: keySecret })
                });
                const data = await res.json();

                if (data.success) {
                    showToast(`Razorpay Connected! Mode: ${data.mode.toUpperCase()}`, 'success');
                    if (resultBox) {
                        resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-emerald-50 text-emerald-800 border-emerald-200';
                        resultBox.innerHTML = `<i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i> <span><strong>Connection Succeeded:</strong> Authenticated as <strong>${data.key_id}</strong> (${data.mode.toUpperCase()} MODE). Test order created (#${data.test_order_id}). Latency: ${data.latency_ms}ms.</span>`;
                    }
                } else {
                    showToast(data.message || 'Razorpay connection test failed.', 'error');
                    if (resultBox) {
                        resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-rose-50 text-rose-800 border-rose-200';
                        resultBox.innerHTML = `<i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i> <span><strong>Connection Failed:</strong> ${data.message}</span>`;
                    }
                }
                if (typeof refreshIcons === 'function') refreshIcons();
                else if (window.lucide && lucide.createIcons) lucide.createIcons();
            } catch (err) {
                console.error(err);
                showToast('Network error while testing connection.', 'error');
                if (resultBox) {
                    resultBox.className = 'p-3 rounded-lg text-xs font-medium border flex items-center gap-2 bg-rose-50 text-rose-800 border-rose-200';
                    resultBox.innerHTML = `<i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i> <span>Failed to contact test endpoint.</span>`;
                }
            } finally {
                if (btn) {
                    btn.disabled = false;
                    if (btnText) btnText.innerText = originalText;
                }
            }
        }

        function toggleSecretVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i data-lucide="eye" class="w-4 h-4"></i>';
            }
            if (typeof refreshIcons === 'function') refreshIcons();
            else if (window.lucide && lucide.createIcons) lucide.createIcons();
        }

        function copyWebhookUrl() {
            const input = document.getElementById('razorpay_webhook_url');
            if (!input) return;
            navigator.clipboard.writeText(input.value).then(() => {
                showToast('Webhook URL copied to clipboard!', 'success');
            }).catch(() => {
                input.select();
                document.execCommand('copy');
                showToast('Webhook URL copied to clipboard!', 'success');
            });
        }

        function openEditBranchModal(b) {
            if (!b) return;
            document.getElementById('edit-branch-id').value = b.id;
            document.getElementById('edit-branch-name').value = b.name || '';
            document.getElementById('edit-branch-code').value = b.code || '';
            document.getElementById('edit-branch-display-name').value = b.display_name || '';
            document.getElementById('edit-branch-city').value = b.city || '';
            document.getElementById('edit-branch-state').value = b.state || 'Kerala';
            document.getElementById('edit-branch-phone').value = b.phone || '';
            document.getElementById('edit-branch-email').value = b.email || '';
            document.getElementById('edit-branch-status').value = b.status || 'active';
            document.getElementById('edit-branch-tagline').value = b.tagline || '';
            document.getElementById('edit-branch-hero-url').value = b.hero_image_url || '';
            document.getElementById('edit-branch-cover-url').value = b.cover_image_url || '';
            document.getElementById('edit-branch-latitude').value = b.latitude || '';
            document.getElementById('edit-branch-longitude').value = b.longitude || '';
            updateFieldThumbnailPreview('edit-branch-hero-url', b.hero_image_url || '');
            updateFieldThumbnailPreview('edit-branch-cover-url', b.cover_image_url || '');
            const titleEl = document.getElementById('edit-branch-modal-title');
            if (titleEl) titleEl.innerText = `Edit Resort Branch: ${b.name}`;
            openModal('modal-edit-branch');
        }

        async function submitEditBranchForm(e) {
            e.preventDefault();
            const branchId = document.getElementById('edit-branch-id').value;
            if (!branchId) return;
            await handleEntitySubmit(e, `/admin/branches/${branchId}`, 'modal-edit-branch');
        }

        async function autoDetectBranchCoords(mode = 'add') {
            const isEdit = mode === 'edit';
            const cityInput = document.getElementById(isEdit ? 'edit-branch-city' : 'add-branch-city');
            const stateInput = document.getElementById(isEdit ? 'edit-branch-state' : 'add-branch-state');
            const latInput = document.getElementById(isEdit ? 'edit-branch-latitude' : 'add-branch-latitude');
            const lngInput = document.getElementById(isEdit ? 'edit-branch-longitude' : 'add-branch-longitude');

            const city = cityInput ? cityInput.value.trim() : '';
            if (!city) {
                showToast('Please enter a City/Region name first.', 'error');
                if (cityInput) cityInput.focus();
                return;
            }

            const state = stateInput ? stateInput.value.trim() : 'Kerala';
            showToast(`Resolving GPS coordinates for ${city}...`, 'info');

            try {
                const query = encodeURIComponent(`${city}, ${state}`);
                const res = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${query}&count=1&language=en&format=json`);
                const data = await res.json();

                if (data && data.results && data.results.length > 0) {
                    const result = data.results[0];
                    if (latInput) latInput.value = parseFloat(result.latitude).toFixed(6);
                    if (lngInput) lngInput.value = parseFloat(result.longitude).toFixed(6);
                    showToast(`GPS Coordinates synced for ${result.name} (${result.latitude.toFixed(4)}, ${result.longitude.toFixed(4)})`, 'success');
                } else {
                    const fallbackRes = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(city)}&count=1&language=en&format=json`);
                    const fallbackData = await fallbackRes.json();
                    if (fallbackData && fallbackData.results && fallbackData.results.length > 0) {
                        const fallbackResult = fallbackData.results[0];
                        if (latInput) latInput.value = parseFloat(fallbackResult.latitude).toFixed(6);
                        if (lngInput) lngInput.value = parseFloat(fallbackResult.longitude).toFixed(6);
                        showToast(`GPS Coordinates synced for ${fallbackResult.name} (${fallbackResult.latitude.toFixed(4)}, ${fallbackResult.longitude.toFixed(4)})`, 'success');
                    } else {
                        showToast(`Could not automatically locate "${city}". You may enter coordinates manually or leave blank for dynamic geocoding.`, 'warning');
                    }
                }
            } catch (err) {
                console.error(err);
                showToast('Geocoding request failed. Please check your network or enter coordinates manually.', 'error');
            }
        }

        async function toggleBranchStatus(branchId) {
            if (!confirm('Are you sure you want to toggle the operational status of this branch?')) return;
            try {
                const res = await fetch(`/admin/branches/${branchId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Could not update branch status.', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error while updating branch status.', 'error');
            }
        }

        // ================= NEARBY LOCATIONS & TAXI DISPATCH =================
        async function toggleNearbyAvailability(id) {
            try {
                const res = await fetch(`/admin/nearby/${id}/toggle-availability`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const btn = document.getElementById(`btn-nearby-avail-${id}`);
                    if (btn) {
                        btn.className = `px-2.5 py-1 rounded-full text-[10px] font-bold transition ${data.is_available ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'}`;
                        btn.innerText = data.is_available ? '✓ Open & Visible' : '✗ Temporarily Closed';
                    }
                } else {
                    showToast(data.message || 'Could not toggle location status.', 'error');
                }
            } catch (err) {
                showToast('Network error while toggling location status.', 'error');
            }
        }

        async function toggleNearbyTaxi(id) {
            try {
                const res = await fetch(`/admin/nearby/${id}/toggle-taxi`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const btn = document.getElementById(`btn-nearby-taxi-${id}`);
                    if (btn) {
                        btn.className = `px-2.5 py-1 rounded-full text-[10px] font-bold transition ${data.is_taxi_available ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-50 text-rose-600 hover:bg-rose-100'}`;
                        btn.innerText = data.is_taxi_available ? '🚕 Cab Available' : 'No Cab';
                    }
                } else {
                    showToast(data.message || 'Could not toggle cab status.', 'error');
                }
            } catch (err) {
                showToast('Network error while toggling cab status.', 'error');
            }
        }

        async function deleteNearby(id, name) {
            if (!confirm(`Are you sure you want to archive nearby discovery "${name}"?`)) return;
            try {
                const res = await fetch(`/admin/nearby/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(data.message || 'Could not archive location.', 'error');
                }
            } catch (err) {
                showToast('Network error while archiving location.', 'error');
            }
        }

        function openEditNearbyModal(loc) {
            document.getElementById('edit-nearby-id').value = loc.id;
            document.getElementById('edit-nearby-branch-id').value = loc.branch_id;
            document.getElementById('edit-nearby-category').value = loc.category || 'waterfall';
            document.getElementById('edit-nearby-name').value = loc.name || '';
            document.getElementById('edit-nearby-distance').value = loc.distance_km || 0;
            document.getElementById('edit-nearby-travel-time').value = loc.travel_time || '';
            document.getElementById('edit-nearby-description').value = loc.description || '';
            document.getElementById('edit-nearby-image-url').value = loc.image_url || '';
            updateFieldThumbnailPreview('edit-nearby-image-url', loc.image_url || '');
            document.getElementById('edit-nearby-is-available').checked = !!loc.is_available;
            document.getElementById('edit-nearby-is-taxi').checked = !!loc.is_taxi_available;
            openModal('modal-edit-nearby');
        }

        async function handleEditNearbySubmit(e) {
            e.preventDefault();
            const id = document.getElementById('edit-nearby-id').value;
            if (!id) return;
            await handleEntitySubmit(e, `/admin/nearby/${id}`, 'modal-edit-nearby');
        }

        function openQuoteTaxiModal(req) {
            document.getElementById('quote-taxi-id').value = req.id;
            document.getElementById('quote-taxi-ref').innerText = `#${req.booking_reference}`;
            document.getElementById('quote-taxi-guest').innerText = req.guest ? req.guest.full_name : 'Guest';
            
            const dests = (req.selected_locations || []).map(d => d.name).join(', ') || 'Local Excursion';
            const extra = req.extra_locations_notes ? ` | Extra stops: ${req.extra_locations_notes}` : '';
            document.getElementById('quote-taxi-details').innerText = `${dests}${extra} (${req.passengers_count} Passengers)`;
            
            document.getElementById('quote-taxi-fare').value = req.estimated_fare ? Math.round(req.estimated_fare) : '';
            document.getElementById('quote-taxi-time').value = req.pickup_time || '09:00 AM';
            document.getElementById('quote-taxi-driver').value = req.driver_details || '';
            document.getElementById('quote-taxi-status').value = req.status === 'pending' ? 'contacted' : req.status;

            openModal('modal-quote-taxi');
        }

        async function handleQuoteTaxiSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('quote-taxi-id').value;
            if (!id) return;
            await handleEntitySubmit(e, `/admin/taxi-requests/${id}/quote`, 'modal-quote-taxi');
        }

        // ================= IN-HOUSE HUB & GUEST 360° CONSOLE =================
        let activeGuest360ResId = null;
        let activeGuest360Data = null;
        let currentInHouseFilter = 'all';

        function filterInHouseList() {
            const query = (document.getElementById('in-house-search-input')?.value || '').toLowerCase().trim();
            
            // Filter mobile cards
            document.querySelectorAll('.in-house-card').forEach(card => {
                const name = card.dataset.guestName || '';
                const room = card.dataset.roomNumber || '';
                const phone = card.dataset.phone || '';
                const code = card.dataset.code || '';
                const isUnsettled = card.dataset.isUnsettled === '1';
                const hasFood = card.dataset.hasFood === '1';
                const hasExt = card.dataset.hasExtension === '1';
                const isDeparting = card.dataset.isDeparting === '1';

                let matchesSearch = !query || name.includes(query) || room.includes(query) || phone.includes(query) || code.includes(query);
                let matchesFilter = true;

                if (currentInHouseFilter === 'unsettled') matchesFilter = isUnsettled;
                else if (currentInHouseFilter === 'food') matchesFilter = hasFood;
                else if (currentInHouseFilter === 'extension') matchesFilter = hasExt;
                else if (currentInHouseFilter === 'departing') matchesFilter = isDeparting;

                if (matchesSearch && matchesFilter) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });

            // Filter desktop rows
            document.querySelectorAll('.in-house-row').forEach(row => {
                const name = row.dataset.guestName || '';
                const room = row.dataset.roomNumber || '';
                const phone = row.dataset.phone || '';
                const code = row.dataset.code || '';
                const isUnsettled = row.dataset.isUnsettled === '1';
                const hasFood = row.dataset.hasFood === '1';
                const hasExt = row.dataset.hasExtension === '1';
                const isDeparting = row.dataset.isDeparting === '1';

                let matchesSearch = !query || name.includes(query) || room.includes(query) || phone.includes(query) || code.includes(query);
                let matchesFilter = true;

                if (currentInHouseFilter === 'unsettled') matchesFilter = isUnsettled;
                else if (currentInHouseFilter === 'food') matchesFilter = hasFood;
                else if (currentInHouseFilter === 'extension') matchesFilter = hasExt;
                else if (currentInHouseFilter === 'departing') matchesFilter = isDeparting;

                if (matchesSearch && matchesFilter) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        function setInHouseFilter(filter) {
            currentInHouseFilter = filter;
            ['all', 'unsettled', 'food', 'extension', 'departing'].forEach(f => {
                const btn = document.getElementById(`filter-pill-${f}`);
                if (btn) {
                    if (f === filter) {
                        btn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white transition shrink-0 shadow-xs flex items-center gap-1';
                    } else {
                        btn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1';
                    }
                }
            });
            filterInHouseList();
            refreshIcons();
        }

        function refreshInHouseList() {
            window.location.reload();
        }

        async function openGuest360Modal(resId) {
            activeGuest360ResId = resId;
            const modal = document.getElementById('modal-guest-360');
            if (modal) modal.classList.remove('hidden');
            switchGuest360Tab('folio');
            
            // Set initial placeholder from serialized array if available
            const localRes = allCheckedInReservations.find(r => r.id === resId) || allReservations.find(r => r.id === resId);
            if (localRes) {
                document.getElementById('g360-guest-name').innerText = localRes.guest ? (localRes.guest.first_name + ' ' + (localRes.guest.last_name || '')) : 'Guest';
                document.getElementById('g360-room-badge').innerText = localRes.room ? localRes.room.room_number : '--';
                document.getElementById('g360-cottage-type').innerText = localRes.room_type ? localRes.room_type.name : 'Resort Cottage';
                document.getElementById('g360-code').innerText = localRes.booking_code;
                document.getElementById('g360-dates-summary').innerText = `${localRes.check_in_date} to ${localRes.check_out_date}`;
                if (localRes.guest && localRes.guest.phone) {
                    document.getElementById('g360-call-link').href = `tel:${localRes.guest.phone}`;
                    document.getElementById('g360-whatsapp-link').href = `https://wa.me/${localRes.guest.phone.replace(/[^0-9]/g, '')}`;
                }
            }

            // Fetch live complete 360° dataset
            try {
                const res = await fetch(`/admin/in-house/${resId}/details`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    activeGuest360Data = data.client_360;
                    renderGuest360Data(activeGuest360Data);
                } else {
                    showToast(data.message || 'Could not load guest profile', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error loading guest 360 data', 'error');
            }
            refreshIcons();
        }

        function closeGuest360Modal() {
            const modal = document.getElementById('modal-guest-360');
            if (modal) modal.classList.add('hidden');
            activeGuest360ResId = null;
            activeGuest360Data = null;
        }

        function switchGuest360Tab(tab) {
            ['folio', 'dining', 'spices', 'extensions', 'facilities', 'taxi', 'chat', 'profile'].forEach(t => {
                const content = document.getElementById(`g360-tab-${t}`);
                const btn = document.getElementById(`g360-tab-btn-${t}`);
                if (content) content.classList.add('hidden');
                if (btn) {
                    btn.className = 'g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5';
                }
            });

            const activeContent = document.getElementById(`g360-tab-${tab}`);
            const activeBtn = document.getElementById(`g360-tab-btn-${tab}`);
            if (activeContent) activeContent.classList.remove('hidden');
            if (activeBtn) {
                activeBtn.className = 'g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white shadow-xs transition shrink-0 flex items-center gap-1.5';
            }
            refreshIcons();
        }

        function renderGuest360Data(d) {
            if (!d) return;
            const res = d.reservation;
            const guest = d.guest;
            const room = d.room;
            const roomType = d.room_type;
            const folio = d.folio_summary;

            // 1. Header & Badges
            document.getElementById('g360-guest-name').innerText = guest ? guest.full_name : 'In-House Guest';
            document.getElementById('g360-room-badge').innerText = room ? room.room_number : '--';
            document.getElementById('g360-cottage-type').innerText = roomType ? roomType.name : 'Resort Cottage';
            document.getElementById('g360-code').innerText = res.booking_code;
            document.getElementById('g360-dates-summary').innerText = `${res.check_in_date} to ${res.check_out_date}`;
            
            const vipBadge = document.getElementById('g360-vip-badge');
            if (guest && guest.vip_level) {
                vipBadge.innerText = guest.vip_level.toUpperCase();
                vipBadge.className = guest.vip_level === 'platinum' ? 'px-2 py-0.2 rounded-full text-[9px] font-bold uppercase bg-purple-100 text-purple-900 border border-purple-300' :
                                    (guest.vip_level === 'gold' ? 'px-2 py-0.2 rounded-full text-[9px] font-bold uppercase bg-amber-400 text-brand-deep' :
                                    'px-2 py-0.2 rounded-full text-[9px] font-bold uppercase bg-gray-100 text-gray-800');
            }

            if (guest && guest.phone) {
                document.getElementById('g360-call-link').href = `tel:${guest.phone}`;
                document.getElementById('g360-whatsapp-link').href = `https://wa.me/${guest.phone.replace(/[^0-9]/g, '')}`;
            }

            // Net Due Pill in Header
            const netDuePill = document.getElementById('g360-net-due-pill');
            const netDueAmt = document.getElementById('g360-net-due-amount');
            if (folio.net_due <= 0.01) {
                netDuePill.className = 'px-3 py-1.5 rounded-xl bg-emerald-500/30 border border-emerald-400/40 text-right';
                netDueAmt.innerText = '₹0 Settled';
            } else {
                netDuePill.className = 'px-3 py-1.5 rounded-xl bg-amber-500/40 border border-amber-300 text-right';
                netDueAmt.innerText = `₹${Number(folio.net_due).toLocaleString()} Due`;
            }

            // 2. Tab 1: Folio Overview
            document.getElementById('g360-stat-room').innerText = `₹${Number(folio.room_total).toLocaleString()}`;
            document.getElementById('g360-stat-folio-charges').innerText = `₹${Number(folio.folio_charges_total).toLocaleString()}`;
            document.getElementById('g360-stat-paid').innerText = `₹${Number(folio.paid_total).toLocaleString()}`;
            document.getElementById('g360-stat-net-due').innerText = `₹${Number(folio.net_due).toLocaleString()}`;
            document.getElementById('g360-stat-payment-status').innerText = folio.is_settled ? '✓ Fully Settled' : 'Partial / Pending';
            document.getElementById('g360-stat-charges-count').innerText = `${(d.itemized_folio || []).length - 1} item(s) posted`;

            // Itemized Folio Table
            const folioTbody = document.getElementById('g360-itemized-folio-tbody');
            if (d.itemized_folio && d.itemized_folio.length > 0) {
                folioTbody.innerHTML = d.itemized_folio.map(item => `
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-4 py-2.5 font-semibold text-brand-text">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold ${item.type === 'room_tariff' ? 'bg-emerald-100 text-emerald-900' : 'bg-blue-50 text-blue-900 border border-blue-200'}">
                                ${item.category}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 font-bold text-brand-text">
                            ${item.title}
                            ${item.description ? `<div class="text-[10px] text-brand-muted font-normal">${item.description}</div>` : ''}
                        </td>
                        <td class="px-4 py-2.5 text-brand-muted text-[11px]">${item.date}</td>
                        <td class="px-4 py-2.5 text-right font-mono font-bold text-brand-text">₹${Number(item.amount).toLocaleString()}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${item.is_paid ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">
                                ${item.is_paid ? 'Paid' : 'Unsettled'}
                            </span>
                        </td>
                    </tr>
                `).join('');
            } else {
                folioTbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-brand-muted">No charges recorded.</td></tr>`;
            }

            // Payments Ledger Table
            const paymentsTbody = document.getElementById('g360-payments-tbody');
            if (d.payments && d.payments.length > 0) {
                paymentsTbody.innerHTML = d.payments.map(p => `
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-4 py-2.5 font-mono font-bold text-brand-text">${p.transaction_id || 'TXN-' + p.id}</td>
                        <td class="px-4 py-2.5 uppercase text-[11px] font-semibold text-brand-muted">${(p.payment_method || 'counter').replace('_', ' ')}</td>
                        <td class="px-4 py-2.5 text-[11px] text-brand-muted">${new Date(p.created_at).toLocaleString()}</td>
                        <td class="px-4 py-2.5 text-right font-mono font-extrabold text-emerald-700">₹${Number(p.amount).toLocaleString()}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${p.status === 'refunded' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800'}">
                                ${p.status ? p.status.toUpperCase() : 'SUCCESS'}
                            </span>
                        </td>
                    </tr>
                `).join('');
            } else {
                paymentsTbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-brand-muted">No payment transactions recorded yet.</td></tr>`;
            }

            // 3. Tab 2: Dining Orders
            const diningList = document.getElementById('g360-dining-orders-list');
            const diningBadge = document.getElementById('g360-badge-dining');
            const diningCount = (d.food_orders || []).length;
            document.getElementById('g360-dining-orders-count').innerText = `${diningCount} Orders`;
            if (diningCount > 0) {
                diningBadge.innerText = diningCount;
                diningBadge.classList.remove('hidden');
                diningList.innerHTML = d.food_orders.map(order => `
                    <div class="p-3.5 bg-white rounded-xl border border-gray-200 shadow-xs space-y-2">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div>
                                <span class="font-bold text-brand-text text-xs">Order #${order.order_number}</span>
                                <span class="text-[10px] text-brand-muted ml-2">${new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${
                                order.status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                                (order.status === 'ready' ? 'bg-purple-100 text-purple-800' :
                                (order.status === 'preparing' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'))
                            }">
                                ${order.status.replace('_', ' ')}
                            </span>
                        </div>
                        <div class="text-xs space-y-1">
                            ${(order.items || []).map(i => `
                                <div class="flex justify-between text-brand-muted">
                                    <span>${i.quantity}x ${i.menu_item ? i.menu_item.name : 'Dish'}</span>
                                    <span class="font-mono">₹${Number(i.total_price).toLocaleString()}</span>
                                </div>
                            `).join('')}
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                            <span class="font-bold text-brand-text">Total: ₹${Number(order.total_amount).toLocaleString()}</span>
                            <div class="flex items-center gap-1.5">
                                ${order.status !== 'completed' ? `
                                    <button type="button" onclick="advanceDiningOrderStatus(${order.id}, '${order.status}')" class="px-2.5 py-1 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg text-[11px] transition">
                                        Advance Status &rarr;
                                    </button>
                                ` : `<span class="text-emerald-700 text-[11px] font-bold">✓ Delivered</span>`}
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                diningBadge.classList.add('hidden');
                diningList.innerHTML = `<p class="text-xs text-brand-muted py-6 text-center">No dining orders found for this cottage.</p>`;
            }

            // 4. Tab 3: Spices
            const spicesList = document.getElementById('g360-spices-orders-list');
            const spicesCount = (d.spice_orders || []).length;
            document.getElementById('g360-spices-orders-count').innerText = `${spicesCount} Orders`;
            if (spicesCount > 0) {
                spicesList.innerHTML = d.spice_orders.map(so => `
                    <div class="p-3.5 bg-white rounded-xl border border-gray-200 shadow-xs space-y-2">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div>
                                <span class="font-bold text-brand-text text-xs">Spice Order #${so.order_number}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ml-2 ${so.delivery_mode === 'villa' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'}">
                                    ${so.delivery_mode === 'villa' ? '🏡 Villa Delivery' : '📦 Courier Ship'}
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-100 text-gray-800">
                                ${so.status}
                            </span>
                        </div>
                        <div class="text-xs space-y-1">
                            ${(so.items || []).map(si => `
                                <div class="flex justify-between text-brand-muted">
                                    <span>${si.product ? si.product.name : 'Spice'} (${si.selling_mode === 'loose' ? si.weight_in_kg + ' kg loose' : si.quantity + ' packets'})</span>
                                    <span class="font-mono">₹${Number(si.total_price).toLocaleString()}</span>
                                </div>
                            `).join('')}
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100 text-xs font-bold text-brand-text">
                            <span>Total: ₹${Number(so.total_amount).toLocaleString()}</span>
                            ${so.tracking_number ? `<span class="text-[11px] font-mono text-brand-muted">Tracking: ${so.tracking_number}</span>` : ''}
                        </div>
                    </div>
                `).join('');
            } else {
                spicesList.innerHTML = `<p class="text-xs text-brand-muted py-6 text-center">No spice boutique orders found for this guest.</p>`;
            }

            // 5. Tab 4: Stay Extensions
            const extList = document.getElementById('g360-extensions-list');
            const extBadge = document.getElementById('g360-badge-ext');
            const extCount = (d.extension_requests || []).length;
            if (extCount > 0) {
                extBadge.innerText = extCount;
                extBadge.classList.remove('hidden');
                extList.innerHTML = d.extension_requests.map(e => `
                    <div class="p-3.5 bg-white rounded-xl border border-gray-200 shadow-xs space-y-2">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div>
                                <span class="font-bold text-brand-text text-xs">Extension: ${e.current_checkout_date} &rarr; ${e.requested_checkout_date} (+${e.extra_nights} nts)</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${e.status === 'approved' ? 'bg-emerald-100 text-emerald-800' : (e.status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700')}">
                                ${e.status}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div>
                                <span class="text-brand-muted">Standard: ₹${Number(e.standard_amount).toLocaleString()}</span>
                                ${e.offered_amount ? `<span class="font-bold text-emerald-700 ml-2">Loyalty Rate: ₹${Number(e.offered_amount).toLocaleString()}</span>` : ''}
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold ${e.payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">
                                Payment: ${e.payment_status ? e.payment_status.toUpperCase() : 'PENDING'}
                            </span>
                        </div>
                    </div>
                `).join('');
            } else {
                extBadge.classList.add('hidden');
                extList.innerHTML = `<p class="text-xs text-brand-muted py-6 text-center">No stay extensions requested.</p>`;
            }

            // Facility & Experience Bookings
            const facList = document.getElementById('g360-facilities-list');
            const facBadge = document.getElementById('g360-badge-facilities');
            const facBookings = d.facility_bookings || [];

            if (facBadge) {
                if (facBookings.length > 0) {
                    facBadge.innerText = facBookings.length;
                    facBadge.classList.remove('hidden');
                } else {
                    facBadge.classList.add('hidden');
                }
            }

            if (facList) {
                if (facBookings.length === 0) {
                    facList.innerHTML = '<p class="text-xs text-brand-muted py-6 text-center">No experiences or facilities booked for this stay yet.</p>';
                } else {
                    facList.innerHTML = facBookings.map(fb => {
                        const isAllocated = !!fb.allocated_time_slot;
                        const statusClass = fb.status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' :
                                            (fb.status === 'completed' ? 'bg-blue-100 text-blue-800' :
                                            (fb.status === 'cancelled' ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-800'));
                        
                        return `
                            <div class="p-3.5 rounded-xl border border-gray-200 bg-gray-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h5 class="font-bold text-xs text-brand-text">${fb.facility ? fb.facility.name : 'Experience'}</h5>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase ${statusClass}">${fb.status}</span>
                                    </div>
                                    <div class="text-[11px] text-brand-muted mt-1 flex items-center gap-2 flex-wrap">
                                        <span>📅 ${fb.booking_date}</span>
                                        <span>&bull;</span>
                                        <span>👥 ${fb.guests_count} Guests</span>
                                        <span>&bull;</span>
                                        <span class="font-bold text-brand-text">${fb.total_amount > 0 ? '₹' + parseFloat(fb.total_amount).toFixed(2) : 'Complimentary'}</span>
                                    </div>
                                    <div class="mt-2">
                                        ${isAllocated ? `
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                                <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-600"></i>
                                                <span>Allocated: ${fb.allocated_time_slot}</span>
                                            </span>
                                        ` : `
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-amber-600"></i>
                                                <span>Pending Slot Allocation</span>
                                            </span>
                                        `}
                                    </div>
                                    ${fb.notes ? `<p class="text-[10px] text-brand-muted italic mt-1">"${fb.notes}"</p>` : ''}
                                </div>
                                <div class="shrink-0">
                                    <button type="button" 
                                            onclick="openAllocateSlotModal(${fb.id}, '${(guest ? guest.full_name : 'Guest').replace(/'/g, "\\'")}', '${(fb.facility ? fb.facility.name : 'Experience').replace(/'/g, "\\'")}', '${fb.allocated_time_slot || ''}', '${fb.status}', ${parseFloat(fb.total_amount) || 0}, ${!!fb.folio_charge_id})"
                                            class="px-2.5 py-1 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs transition">
                                        Allocate / Edit Slot
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            }

            // Taxi & Excursion Bookings
            const taxiList = document.getElementById('g360-taxi-list');
            const taxiBadge = document.getElementById('g360-badge-taxi');
            const taxiReqs = d.taxi_requests || [];

            if (taxiBadge) {
                if (taxiReqs.length > 0) {
                    taxiBadge.innerText = taxiReqs.length;
                    taxiBadge.classList.remove('hidden');
                } else {
                    taxiBadge.classList.add('hidden');
                }
            }

            if (taxiList) {
                if (taxiReqs.length === 0) {
                    taxiList.innerHTML = '<p class="text-xs text-brand-muted py-6 text-center">No cab excursions requested for this stay yet.</p>';
                } else {
                    taxiList.innerHTML = taxiReqs.map(t => {
                        const isQuoted = !!t.estimated_fare;
                        const statusClass = t.status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' :
                                            (t.status === 'contacted' ? 'bg-blue-100 text-blue-800' :
                                            (t.status === 'completed' ? 'bg-gray-100 text-gray-800' :
                                            (t.status === 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')));
                        
                        const dests = (t.selected_locations || []).map(dest => `<span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-brand-canvas text-brand-primary border border-brand-primary/20">${dest.name}</span>`).join(' ') || 'Local Sightseeing';

                        return `
                            <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/70 space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-xs text-brand-primary">#${t.booking_reference}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase ${statusClass}">${t.status}</span>
                                    </div>
                                    <span class="text-[11px] text-brand-muted font-mono">📅 ${t.pickup_date} &middot; ${t.pickup_time || 'Morning'}</span>
                                </div>
                                <div class="text-xs">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-brand-muted block mb-1">Destinations:</span>
                                    <div class="flex flex-wrap gap-1">${dests}</div>
                                    ${t.extra_locations_notes ? `
                                        <div class="text-[11px] text-amber-900 bg-amber-50 p-2 rounded-lg border border-amber-200/60 mt-2">
                                            <strong>Custom Stops / Requests:</strong> ${t.extra_locations_notes}
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-gray-200 text-xs">
                                    <div>
                                        <span class="text-[10px] text-brand-muted">Fare:</span>
                                        <span class="font-bold text-brand-text font-mono ml-1">${isQuoted ? '₹' + parseFloat(t.estimated_fare).toLocaleString() : '<span class="text-amber-600 italic">Pending Quote</span>'}</span>
                                        ${t.folio_charge_id ? '<span class="ml-2 text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">BILLED TO FOLIO</span>' : ''}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        ${guest && guest.phone ? `
                                            <a href="tel:${guest.phone}" class="px-2.5 py-1 rounded-lg border border-gray-300 text-gray-700 font-semibold text-[11px] hover:bg-gray-100 transition flex items-center gap-1">
                                                <i data-lucide="phone" class="w-3 h-3"></i> Call
                                            </a>
                                        ` : ''}
                                        <button type="button" onclick='openQuoteTaxiModal(${JSON.stringify(t)})' class="px-3 py-1 bg-brand-primary hover:bg-brand-deep text-white font-bold rounded-lg text-[11px] shadow-xs transition">
                                            ${isQuoted ? 'Update Quote / Dispatch' : 'Quote Fare & Dispatch'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                }
            }

            // 6. Tab 5: Live Chat & Help Desk
            const chatMessagesCont = document.getElementById('g360-chat-messages-container');
            const primaryChat = d.primary_chat;
            document.getElementById('g360-chat-ticket-ref').innerText = primaryChat ? `Ticket: #${primaryChat.ticket_number}` : 'Ticket: Standby';
            
            if (primaryChat && primaryChat.messages && primaryChat.messages.length > 0) {
                chatMessagesCont.innerHTML = primaryChat.messages.map(m => `
                    <div class="flex flex-col ${m.sender_type === 'staff' ? 'items-end' : 'items-start'}">
                        <div class="max-w-[85%] rounded-2xl p-3 text-xs shadow-xs ${
                            m.is_internal_note ? 'bg-amber-100 border border-amber-300 text-amber-950' :
                            (m.sender_type === 'staff' ? 'bg-brand-primary text-white' : 'bg-gray-100 text-brand-text border border-gray-200')
                        }">
                            ${m.is_internal_note ? '<div class="text-[9px] font-bold uppercase tracking-wider text-amber-800 mb-1">🔒 Internal Staff Note</div>' : ''}
                            <p>${m.message}</p>
                            <span class="text-[9px] mt-1 block opacity-70 text-right">${new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                    </div>
                `).join('');
                chatMessagesCont.scrollTop = chatMessagesCont.scrollHeight;
            } else {
                chatMessagesCont.innerHTML = `<div class="text-center text-xs text-brand-muted py-12">No messages in conversation. Send a message or staff note below.</div>`;
            }

            // Service Tickets list
            const ticketsList = document.getElementById('g360-service-tickets-list');
            const serviceTickets = (d.enquiries || []).filter(e => e.topic !== 'concierge' && e.topic !== '');
            if (serviceTickets.length > 0) {
                ticketsList.innerHTML = serviceTickets.map(t => `
                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-brand-text truncate">${t.subject}</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase ${t.status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">
                                ${t.status}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-brand-muted pt-1">
                            <span>#${t.ticket_number} · ${t.priority.toUpperCase()}</span>
                            ${t.status !== 'resolved' ? `
                                <button type="button" onclick="updateTicketStatusDirect(${t.id}, 'resolved')" class="text-emerald-700 font-bold hover:underline">
                                    ✓ Resolve
                                </button>
                            ` : `<span class="text-emerald-600 font-bold">Resolved</span>`}
                        </div>
                    </div>
                `).join('');
            } else {
                ticketsList.innerHTML = `<p class="text-xs text-brand-muted py-4 text-center">No active service tickets.</p>`;
            }

            // 7. Tab 6: Profile & Notes
            document.getElementById('g360-prof-name').innerText = guest ? guest.full_name : '--';
            document.getElementById('g360-prof-phone').innerText = guest ? (guest.phone || 'N/A') : '--';
            document.getElementById('g360-prof-email').innerText = guest ? (guest.email || 'N/A') : '--';
            document.getElementById('g360-prof-id').innerText = (res.id_proof_type ? res.id_proof_type.toUpperCase() + ': ' : '') + (res.id_proof_number || 'Not provided');
            document.getElementById('g360-prof-vip').innerText = guest ? guest.vip_level : 'Standard';
            document.getElementById('g360-prof-lifetime').innerText = guest ? `${guest.total_stays || 1} Stays / ₹${Number(guest.total_spent || folio.grand_total).toLocaleString()}` : '--';
            document.getElementById('g360-prof-address').innerText = guest ? `${guest.address || ''}, ${guest.city || ''}, ${guest.state || 'Kerala'}, ${guest.pincode || ''}` : 'No address on file';
            document.getElementById('g360-prof-preferences').innerText = guest && guest.preferences ? guest.preferences : 'None specified';
            document.getElementById('g360-prof-requests').innerText = res.special_requests ? res.special_requests : 'None';

            refreshIcons();
        }

        // ================= FOLIO CHARGES & PAYMENTS =================
        function openAddFolioChargeModal() {
            if (!activeGuest360ResId) return;
            document.getElementById('g360-charge-title').value = '';
            document.getElementById('g360-charge-amount').value = '';
            openModal('modal-g360-add-charge');
        }

        async function submitAddFolioCharge(e) {
            e.preventDefault();
            if (!activeGuest360ResId) return;

            const category = document.getElementById('g360-charge-cat').value;
            const title = document.getElementById('g360-charge-title').value;
            const amount = document.getElementById('g360-charge-amount').value;
            const submitBtn = document.getElementById('btn-submit-folio-charge');

            submitBtn.disabled = true;
            submitBtn.innerText = 'Posting...';

            try {
                const res = await fetch(`/admin/in-house/${activeGuest360ResId}/folio-charge`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ category, title, amount })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-g360-add-charge');
                    // Reload data
                    openGuest360Modal(activeGuest360ResId);
                } else {
                    showToast(data.message || 'Error posting charge', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error posting folio charge', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Post to Folio';
            }
        }

        function openRecordFolioPaymentModal() {
            if (!activeGuest360ResId || !activeGuest360Data) return;
            const folio = activeGuest360Data.folio_summary || {};
            const netDue = folio.net_due !== undefined ? folio.net_due : 0;
            const balEl = document.getElementById('g360-modal-pay-balance');
            const amtEl = document.getElementById('g360-pay-amount');
            if (balEl) balEl.innerText = `₹${Number(netDue).toLocaleString()} Due`;
            if (amtEl) amtEl.value = netDue > 0 ? netDue : '';
            openModal('modal-g360-record-payment');
        }

        async function submitRecordFolioPayment(e) {
            e.preventDefault();
            if (!activeGuest360ResId) return;

            const amount = document.getElementById('g360-pay-amount').value;
            const payment_method = document.getElementById('g360-pay-method').value;
            const notes = document.getElementById('g360-pay-notes').value;
            const submitBtn = document.getElementById('btn-submit-folio-payment');

            submitBtn.disabled = true;
            submitBtn.innerText = 'Recording...';

            try {
                const res = await fetch(`/admin/in-house/${activeGuest360ResId}/record-payment`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ amount, payment_method, notes })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-g360-record-payment');
                    // Reload data
                    openGuest360Modal(activeGuest360ResId);
                } else {
                    showToast(data.message || 'Error recording payment', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error recording folio payment', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Confirm Payment';
            }
        }

        // ================= IN-CONSOLE CHAT & TICKETS =================
        async function submitGuest360Message() {
            if (!activeGuest360ResId) return;
            const input = document.getElementById('g360-chat-reply-input');
            const isInternal = document.getElementById('g360-internal-note-toggle').checked;
            const message = (input.value || '').trim();
            if (!message) return;

            const sendBtn = document.getElementById('g360-chat-send-btn');
            sendBtn.disabled = true;

            try {
                const res = await fetch(`/admin/in-house/${activeGuest360ResId}/send-message`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ message, is_internal_note: isInternal })
                });
                const data = await res.json();
                if (data.success) {
                    input.value = '';
                    showToast(data.message, 'success');
                    // Reload 360 data to update conversation
                    openGuest360Modal(activeGuest360ResId);
                    switchGuest360Tab('chat');
                } else {
                    showToast(data.message || 'Error sending message', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error sending message', 'error');
            } finally {
                sendBtn.disabled = false;
            }
        }

        function toggleNewServiceTicketForm() {
            const form = document.getElementById('g360-new-ticket-form');
            if (form) form.classList.toggle('hidden');
        }

        async function submitServiceTicket() {
            if (!activeGuest360ResId) return;
            const topic = document.getElementById('g360-ticket-topic').value;
            const subject = document.getElementById('g360-ticket-subject').value;
            const priority = document.getElementById('g360-ticket-priority').value;

            if (!subject) {
                showToast('Please enter a request subject', 'warning');
                return;
            }

            try {
                const res = await fetch(`/admin/in-house/${activeGuest360ResId}/service-ticket`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ topic, subject, priority })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    toggleNewServiceTicketForm();
                    document.getElementById('g360-ticket-subject').value = '';
                    openGuest360Modal(activeGuest360ResId);
                    switchGuest360Tab('chat');
                } else {
                    showToast(data.message || 'Error dispatching ticket', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error dispatching ticket', 'error');
            }
        }

        async function updateTicketStatusDirect(ticketId, status) {
            try {
                const res = await fetch(`/admin/in-house/tickets/${ticketId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ status })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    if (activeGuest360ResId) {
                        openGuest360Modal(activeGuest360ResId);
                        switchGuest360Tab('chat');
                    }
                }
            } catch (err) {
                console.error(err);
                showToast('Error updating ticket status', 'error');
            }
        }

        async function advanceDiningOrderStatus(orderId, currentStatus) {
            const nextStatus = currentStatus === 'new' ? 'preparing' :
                               (currentStatus === 'preparing' ? 'ready' : 'completed');
            try {
                const res = await fetch(`/admin/dining/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ status: nextStatus })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(`Order status updated to '${nextStatus}'`, 'success');
                    if (activeGuest360ResId) {
                        openGuest360Modal(activeGuest360ResId);
                        switchGuest360Tab('dining');
                    }
                }
            } catch (err) {
                console.error(err);
                showToast('Error updating dining order status', 'error');
            }
        }

        // ================= EXPRESS CHECK-OUT ACTION =================
        async function executeGuest360ExpressCheckOut() {
            if (!activeGuest360ResId || !activeGuest360Data) return;
            const folio = activeGuest360Data.folio_summary;
            let forceOverride = false;

            if (folio.net_due > 0.01) {
                const confirmChoice = confirm(
                    `⚠️ OUTSTANDING FOLIO BALANCE DETECTED:\n\nGuest has a pending balance of ₹${Number(folio.net_due).toLocaleString()}.\n\nClick OK to apply Manager Override and proceed with Express Checkout.\nClick Cancel to settle payment first.`
                );
                if (!confirmChoice) {
                    openRecordFolioPaymentModal();
                    return;
                }
                forceOverride = true;
            } else {
                if (!confirm('Are you sure you want to perform Express Check-Out for this guest? Room will be released and marked dirty for housekeeping.')) {
                    return;
                }
            }

            const btn = document.getElementById('g360-btn-checkout');
            btn.disabled = true;
            btn.innerText = 'Checking out...';

            try {
                const res = await fetch(`/admin/in-house/${activeGuest360ResId}/express-checkout`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ force_override: forceOverride })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeGuest360Modal();
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    showToast(data.message || 'Check-out failed', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error executing express checkout', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="log-out" class="w-3.5 h-3.5 text-amber-400"></i> <span>Express Check-Out</span>`;
                refreshIcons();
            }
        }

        // ================= DASHBOARD & FAST FLIGHT DECK OPERATIONS =================
        function switchDashboardMode(mode) {
            const opsView = document.getElementById('operations-kpi-view');
            const visualView = document.getElementById('visual-editor-view');
            const btnOps = document.getElementById('btn-dashboard-mode-ops');
            const btnVisual = document.getElementById('btn-dashboard-mode-visual');

            if (!opsView || !visualView) return;

            if (mode === 'operations') {
                opsView.classList.remove('hidden');
                visualView.classList.add('hidden');
                if (btnOps) {
                    btnOps.className = 'px-3 py-1.5 rounded-lg transition bg-white text-brand-deep shadow-xs flex items-center gap-1.5';
                }
                if (btnVisual) {
                    btnVisual.className = 'px-3 py-1.5 rounded-lg transition text-gray-600 hover:text-brand-deep flex items-center gap-1.5';
                }
            } else {
                opsView.classList.add('hidden');
                visualView.classList.remove('hidden');
                if (btnVisual) {
                    btnVisual.className = 'px-3 py-1.5 rounded-lg transition bg-white text-brand-deep shadow-xs flex items-center gap-1.5';
                }
                if (btnOps) {
                    btnOps.className = 'px-3 py-1.5 rounded-lg transition text-gray-600 hover:text-brand-deep flex items-center gap-1.5';
                }
            }
            refreshIcons();
        }

        // Backward compatibility
        function toggleDashboardView(mode) {
            switchDashboardMode(mode === 'visual' ? 'visual' : 'operations');
        }

        async function quickCheckIn(reservationId, guestName, roomNumber) {
            if (!confirm(`Confirm 1-Tap Check-In for ${guestName} into Room ${roomNumber}?`)) {
                return;
            }

            try {
                showToast(`Checking in ${guestName}...`, 'info');
                const res = await fetch(`/admin/reservations/${reservationId}/check-in`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(`✓ Check-In complete for ${guestName} in Room ${roomNumber}!`, 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Check-in failed', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error performing check-in', 'error');
            }
        }

        function openAssignRoomModal(reservationId, guestName, roomTypeId, branchId) {
            const resIdInput = document.getElementById('assign-room-res-id');
            const guestLabel = document.getElementById('assign-room-guest-label');
            const roomSelect = document.getElementById('assign-room-select');

            if (resIdInput) resIdInput.value = reservationId;
            if (guestLabel) guestLabel.innerText = `Guest: ${guestName}`;

            if (roomSelect) {
                // Filter options
                Array.from(roomSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    const optBranch = opt.getAttribute('data-branch');
                    const optType = opt.getAttribute('data-type');
                    if (branchId && optBranch && String(optBranch) !== String(branchId)) {
                        opt.style.display = 'none';
                    } else if (roomTypeId && optType && String(optType) !== String(roomTypeId)) {
                        opt.style.display = 'none';
                    } else {
                        opt.style.display = '';
                    }
                });
                roomSelect.selectedIndex = 0;
            }

            openModal('modal-assign-room');
            refreshIcons();
        }

        function assignRoomAndCheckIn(autoCheckIn) {
            const flag = document.getElementById('assign-and-checkin-flag');
            if (flag) flag.value = autoCheckIn ? '1' : '0';
            const form = document.getElementById('form-assign-room');
            if (form) form.requestSubmit();
        }

        async function submitAssignRoomForm(e) {
            e.preventDefault();
            const resId = document.getElementById('assign-room-res-id').value;
            const roomId = document.getElementById('assign-room-select').value;
            const autoCheckIn = document.getElementById('assign-and-checkin-flag').value === '1';

            if (!roomId) {
                showToast('Please select an available physical room', 'error');
                return;
            }

            try {
                showToast('Assigning room to reservation...', 'info');
                const res = await fetch(`/admin/reservations/${resId}/assign-room`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ room_id: roomId })
                });
                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Room assignment failed', 'error');
                    return;
                }

                if (autoCheckIn) {
                    showToast('Room assigned! Performing 1-tap check-in...', 'info');
                    const ciRes = await fetch(`/admin/reservations/${resId}/check-in`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    });
                    const ciData = await ciRes.json();
                    if (ciData.success) {
                        showToast('✓ Room assigned and guest checked in successfully!', 'success');
                    } else {
                        showToast(ciData.message || 'Check-in failed after assignment', 'warning');
                    }
                } else {
                    showToast('✓ Physical room successfully assigned to reservation!', 'success');
                }

                closeModal('modal-assign-room');
                setTimeout(() => window.location.reload(), 800);
            } catch (err) {
                console.error(err);
                showToast('Network error during room assignment', 'error');
            }
        }

        async function quickCheckOut(reservationId, guestName, roomNumber, netDue) {
            let forceOverride = false;
            if (Number(netDue) > 0.01) {
                const proceed = confirm(
                    `⚠️ OUTSTANDING FOLIO BALANCE DETECTED:\n\n` +
                    `Guest ${guestName} (Room ${roomNumber}) has a pending balance of ₹${Number(netDue).toLocaleString()}.\n\n` +
                    `Click OK to apply Manager Override and proceed with Express Checkout.\n` +
                    `Click Cancel to open Guest 360 and settle payment first.`
                );
                if (!proceed) {
                    openGuest360Modal(reservationId);
                    switchGuest360Tab('folio');
                    return;
                }
                forceOverride = true;
            } else {
                if (!confirm(`Confirm Express Check-Out for ${guestName} (Room ${roomNumber})?\n\nRoom will be vacated and marked dirty for housekeeping.`)) {
                    return;
                }
            }

            try {
                showToast(`Executing checkout for ${guestName}...`, 'info');
                const res = await fetch(`/admin/in-house/${reservationId}/express-checkout`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ force_override: forceOverride })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Check-out completed!', 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Check-out failed', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error during express checkout', 'error');
            }
        }

        async function quickToggleHousekeeping(roomId, roomNumber, currentStatus, btn) {
            try {
                const res = await fetch(`/admin/rooms/${roomId}/toggle-housekeeping`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    const newStatus = data.housekeeping_status;
                    showToast(`Room ${roomNumber} marked as ${newStatus}!`, 'success');
                    if (btn) {
                        btn.className = `px-2 py-0.5 rounded text-[10px] font-bold uppercase transition hover:opacity-85 cursor-pointer flex items-center gap-1 ${newStatus === 'clean' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-red-100 text-red-800 hover:bg-red-200'}`;
                        btn.innerHTML = `<i data-lucide="${newStatus === 'clean' ? 'sparkles' : 'spray-can'}" class="w-3 h-3"></i> <span>${newStatus}</span>`;
                        btn.setAttribute('onclick', `quickToggleHousekeeping(${roomId}, '${roomNumber}', '${newStatus}', this)`);
                        refreshIcons();
                    }
                } else {
                    showToast(data.message || 'Failed to toggle housekeeping status', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error toggling room housekeeping', 'error');
            }
        }

        // ================= HOMEPAGE MODULES TOGGLE =================
        async function toggleHomepageModule(moduleKey, isChecked) {
            try {
                const res = await fetch(`/admin/cms/homepage-modules`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ module: moduleKey, is_enabled: isChecked ? 1 : 0 })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Failed to update module state', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error updating module', 'error');
            }
        }

        // ================= CMS PAGES & NAVIGATION CRUD =================
        function autoGenerateCmsSlug(title) {
            const slugInput = document.getElementById('cms-page-slug');
            if (!slugInput) return;
            slugInput.value = title
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function openNewPageModal() {
            const form = document.getElementById('form-cms-page');
            if (form) form.reset();
            openModal('modal-cms-page');
            refreshIcons();
        }

        async function submitCmsPage(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('submit-cms-page-btn');
            btn.disabled = true;
            btn.innerText = 'Publishing...';

            const formData = new FormData(form);

            try {
                const res = await fetch(`/admin/cms/pages`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-cms-page');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to create page', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error saving CMS page', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="check" class="w-3.5 h-3.5"></i> Save & Publish Page`;
                refreshIcons();
            }
        }

        function openEditPageModal(page) {
            document.getElementById('edit-cms-page-id').value = page.id;
            document.getElementById('edit-cms-page-title').value = page.title || '';
            document.getElementById('edit-cms-page-slug').value = page.slug || '';
            document.getElementById('edit-cms-page-branch').value = page.branch_id || '';
            document.getElementById('edit-cms-page-meta').value = page.meta_description || '';
            document.getElementById('edit-cms-page-content').value = page.content || '';
            document.getElementById('edit-cms-page-published').checked = !!page.is_published;

            openModal('modal-edit-cms-page');
            refreshIcons();
        }

        async function submitEditCmsPage(e) {
            e.preventDefault();
            const form = e.target;
            const pageId = document.getElementById('edit-cms-page-id').value;
            const btn = document.getElementById('submit-edit-cms-page-btn');
            btn.disabled = true;
            btn.innerText = 'Updating...';

            const formData = new FormData(form);

            try {
                const res = await fetch(`/admin/cms/pages/${pageId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-edit-cms-page');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to update page', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error updating CMS page', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Update Page';
            }
        }

        async function togglePageStatus(pageId) {
            try {
                const res = await fetch(`/admin/cms/pages/${pageId}/toggle`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to toggle status', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error toggling page status', 'error');
            }
        }

        async function deleteCmsPage(pageId, title) {
            if (!confirm(`Are you sure you want to permanently delete CMS page "${title}"?`)) {
                return;
            }

            try {
                const res = await fetch(`/admin/cms/pages/${pageId}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to delete page', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error deleting page', 'error');
            }
        }

        function openNewNavModal() {
            const form = document.getElementById('form-cms-nav');
            if (form) form.reset();
            openModal('modal-cms-nav');
            refreshIcons();
        }

        async function submitCmsNav(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('submit-cms-nav-btn');
            btn.disabled = true;
            btn.innerText = 'Saving...';

            const formData = new FormData(form);

            try {
                const res = await fetch(`/admin/cms/navigation`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-cms-nav');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to save navigation item', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error saving navigation item', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="check" class="w-3.5 h-3.5"></i> Save Navigation Link`;
                refreshIcons();
            }
        }

        async function deleteCmsNav(navId, label) {
            if (!confirm(`Are you sure you want to remove navigation link "${label}"?`)) {
                return;
            }

            try {
                const res = await fetch(`/admin/cms/navigation/${navId}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to delete navigation item', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error deleting navigation item', 'error');
            }
        }

        // ================= REPORTS FILTER =================
        function filterReportsDateRange(range) {
            const btnAll = document.getElementById('btn-rep-all');
            const btnMonth = document.getElementById('btn-rep-month');
            const btnToday = document.getElementById('btn-rep-today');

            [btnAll, btnMonth, btnToday].forEach(b => {
                if (b) {
                    b.className = 'px-2.5 py-1 rounded-md text-brand-muted hover:text-brand-text';
                }
            });

            if (range === 'all' && btnAll) {
                btnAll.className = 'px-2.5 py-1 rounded-md bg-white text-brand-deep shadow-2xs font-bold';
                showToast('Showing all-time gross financials across resort operations', 'info');
            } else if (range === 'month' && btnMonth) {
                btnMonth.className = 'px-2.5 py-1 rounded-md bg-white text-brand-deep shadow-2xs font-bold';
                showToast('Showing current calendar month financial performance', 'info');
            } else if (range === 'today' && btnToday) {
                btnToday.className = 'px-2.5 py-1 rounded-md bg-white text-brand-deep shadow-2xs font-bold';
                showToast("Showing today's real-time intraday transactions", 'info');
            }
        }

        // ================= REVIEWS & TESTIMONIALS HUB ENGINE =================
        function switchReviewsHubTab(tab) {
            const subMod = document.getElementById('subtab-reviews-moderation');
            const subTest = document.getElementById('subtab-reviews-testimonials');
            const btnMod = document.getElementById('tab-btn-guest-reviews');
            const btnTest = document.getElementById('tab-btn-welcome-testimonials');

            if (!subMod || !subTest) return;

            if (tab === 'testimonials') {
                subMod.classList.add('hidden');
                subTest.classList.remove('hidden');

                if (btnTest) {
                    btnTest.className = 'flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer bg-brand-deep text-white shadow-xs';
                }
                if (btnMod) {
                    btnMod.className = 'flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-brand-muted hover:text-brand-text hover:bg-white transition cursor-pointer';
                }
            } else {
                subTest.classList.add('hidden');
                subMod.classList.remove('hidden');

                if (btnMod) {
                    btnMod.className = 'flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer bg-brand-deep text-white shadow-xs';
                }
                if (btnTest) {
                    btnTest.className = 'flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-brand-muted hover:text-brand-text hover:bg-white transition cursor-pointer';
                }
            }
            refreshIcons();
        }

        function filterReviewsList(filter) {
            const cards = document.querySelectorAll('.review-card');
            const buttons = document.querySelectorAll('.review-filter-btn');

            buttons.forEach(b => {
                if (b.dataset.filter === filter) {
                    b.className = 'review-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-deep text-white shadow-2xs';
                } else {
                    b.className = 'review-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-canvas text-brand-muted hover:text-brand-text';
                }
            });

            cards.forEach(c => {
                const status = c.dataset.status;
                const type = c.dataset.type;

                let match = false;
                if (filter === 'all') match = true;
                else if (filter === 'pending' && status === 'pending') match = true;
                else if (filter === 'approved' && status === 'approved') match = true;
                else if (filter === 'rejected' && (status === 'rejected' || status === 'hidden')) match = true;
                else if (filter === 'stay' && type === 'stay') match = true;
                else if (filter === 'food' && type === 'food') match = true;

                c.style.display = match ? 'block' : 'none';
            });
        }

        async function moderateStayReview(reviewId, status) {
            try {
                const res = await fetch(`/admin/reviews/${reviewId}/moderate`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ status })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to moderate review', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error moderating stay review', 'error');
            }
        }

        async function moderateFoodReview(reviewId, action) {
            try {
                const res = await fetch(`/admin/food-reviews/${reviewId}/${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to moderate food review', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error moderating dining review', 'error');
            }
        }

        function toggleStaffReplyBox(reviewId) {
            const box = document.getElementById(`reply-box-${reviewId}`);
            if (box) box.classList.toggle('hidden');
        }

        async function submitStaffReply(reviewId) {
            const replyInput = document.getElementById(`reply-input-${reviewId}`);
            if (!replyInput) return;
            const staffReply = replyInput.value.trim();
            if (!staffReply) {
                showToast('Please type a reply message first', 'warning');
                return;
            }

            try {
                const res = await fetch(`/admin/reviews/${reviewId}/moderate`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ status: 'approved', staff_reply: staffReply })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Staff response saved and published successfully!', 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to save staff response', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error saving staff response', 'error');
            }
        }

        async function promoteReviewToTestimonial(reviewId, type = 'stay') {
            try {
                const res = await fetch(`/admin/testimonials/promote-review/${reviewId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ type })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.hash = '#reviews';
                        window.location.reload();
                    }, 800);
                } else {
                    showToast(data.message || 'Failed to promote review', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error promoting review to testimonial', 'error');
            }
        }

        function openAddTestimonialModal() {
            const form = document.getElementById('form-add-testimonial');
            if (form) form.reset();
            openModal('modal-add-testimonial');
            refreshIcons();
        }

        function openEditTestimonialModal(item) {
            document.getElementById('edit-testimonial-id').value = item.id;
            document.getElementById('edit-guest-name').value = item.guest_name;
            document.getElementById('edit-stay-title').value = item.stay_title;
            document.getElementById('edit-rating').value = item.rating || 5;
            document.getElementById('edit-branch-id').value = item.branch_id || '';
            document.getElementById('edit-sort-order').value = item.sort_order;
            document.getElementById('edit-quote').value = item.quote;
            document.getElementById('edit-is-active').checked = !!item.is_active;

            openModal('modal-edit-testimonial');
            refreshIcons();
        }

        async function submitAddTestimonial(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btn-save-testimonial');
            btn.disabled = true;
            btn.innerText = 'Saving...';

            const formData = new FormData(form);

            try {
                const res = await fetch('/admin/testimonials', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-add-testimonial');
                    setTimeout(() => {
                        window.location.hash = '#reviews';
                        window.location.reload();
                    }, 600);
                } else {
                    showToast(data.message || 'Failed to save testimonial', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error adding testimonial', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Save Testimonial';
            }
        }

        async function submitEditTestimonial(e) {
            e.preventDefault();
            const form = e.target;
            const id = document.getElementById('edit-testimonial-id').value;
            const btn = document.getElementById('btn-update-testimonial');
            btn.disabled = true;
            btn.innerText = 'Updating...';

            const formData = new FormData(form);

            try {
                const res = await fetch(`/admin/testimonials/${id}/update`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal('modal-edit-testimonial');
                    setTimeout(() => {
                        window.location.hash = '#reviews';
                        window.location.reload();
                    }, 600);
                } else {
                    showToast(data.message || 'Failed to update testimonial', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error updating testimonial', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Update Testimonial';
            }
        }

        async function deleteTestimonialItem(id, guestName) {
            if (!confirm(`Are you sure you want to delete testimonial by "${guestName}"?`)) {
                return;
            }

            try {
                const res = await fetch(`/admin/testimonials/${id}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    const row = document.getElementById(`testimonial-row-${id}`);
                    if (row) row.remove();
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to delete testimonial', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error deleting testimonial', 'error');
            }
        }

        async function toggleTestimonialActive(id) {
            try {
                const res = await fetch(`/admin/testimonials/${id}/toggle`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to toggle visibility', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error toggling testimonial visibility', 'error');
            }
        }

        async function moveTestimonialOrder(id, direction) {
            try {
                const res = await fetch('/admin/testimonials/reorder', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ id, direction })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to reorder testimonial', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error reordering testimonial', 'error');
            }
        }

        async function updateTestimonialSortOrder(id, sortOrder) {
            try {
                const res = await fetch('/admin/testimonials/reorder', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ id, sort_order: parseInt(sortOrder) || 1 })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Failed to update order', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error updating display order', 'error');
            }
        }
    </script>
</body>
</html>
