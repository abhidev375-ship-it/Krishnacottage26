@php
    $hContent = $homepageContent ?? app(\App\Http\Controllers\Admin\AdminController::class)->getHomepageContent();
    $hBrandTitle = $hContent['header_brand_title'] ?? 'Krishna Cottage';
    $hEyebrow = $hContent['header_eyebrow'] ?? 'A stay worth remembering';
@endphp

<!-- UNIFIED LUXURY CLIENT HEADER (Full-Width Natural Off-White Top Band #FAF7F0) -->
<header id="header" {{ ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->merge(['class' => 'fixed inset-x-0 top-0 z-50 w-full bg-[#FAF7F0] border-b border-forest/10 transition-all duration-300 ease-in-out translate-y-0']) }}>
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        <div id="headerBar" class="flex items-center justify-between h-17 sm:h-18 lg:h-19">
            <!-- Left: Mobile Menu Toggle + Brand Identity -->
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button in Header Left Corner -->
                <button id="menuBtn" class="grid h-9.5 w-9.5 place-items-center rounded-xl bg-forest/5 hover:bg-forest/10 text-forest lg:hidden touch-tap cursor-pointer transition" aria-label="Menu">
                    <i data-lucide="menu" class="h-5 w-5"></i>
                </button>
                <!-- Brand Identity -->
                <a href="{{ route('home') }}" class="flex items-center group">
                    <span class="leading-none">
                        <span class="block text-[8px] sm:text-[8.5px] font-bold uppercase tracking-[.26em] text-forest/50">{{ $hEyebrow }}</span>
                        <span class="mt-1 block serif text-base sm:text-lg font-bold text-forest group-hover:text-emerald transition tracking-tight">{{ $hBrandTitle }}</span>
                    </span>
                </a>
            </div>

            <!-- Center: Navigation Menus Directly in Header -->
            <nav class="hidden gap-7 text-[12.5px] font-semibold text-forest lg:flex items-center">
                <a href="{{ route('home') }}" class="nav-item-line hover:text-emerald transition {{ request()->routeIs('home') ? 'active text-emerald font-bold' : '' }}">Home</a>
                <a href="{{ route('rooms.index') }}" class="nav-item-line hover:text-emerald transition {{ request()->routeIs('rooms.*') || request()->routeIs('booking.*') ? 'active text-emerald font-bold' : '' }}">Stay</a>
                <a href="{{ route('dining.index') }}" class="nav-item-line hover:text-emerald transition {{ request()->routeIs('dining.*') ? 'active text-emerald font-bold' : '' }}">Dining</a>
                <a href="{{ route('spices.index') }}" class="nav-item-line hover:text-emerald transition {{ request()->routeIs('spices.*') ? 'active text-emerald font-bold' : '' }}">Krishna Spices</a>
                <a href="{{ route('facilities.index') }}" class="nav-item-line hover:text-emerald transition {{ request()->routeIs('facilities.*') ? 'active text-emerald font-bold' : '' }}">Facilities</a>
                <a href="{{ route('gallery.index') }}" class="nav-item-line hover:text-emerald transition {{ request()->routeIs('gallery.*') ? 'active text-emerald font-bold' : '' }}">Gallery</a>
                <a href="{{ route('nearby.index') }}" class="nav-item-line hover:text-emerald transition {{ request()->routeIs('nearby.*') ? 'active text-emerald font-bold' : '' }}">Discover</a>
                <a href="{{ route('contact.index') }}" class="nav-item-line hover:text-emerald transition {{ request()->routeIs('contact.*') ? 'active text-emerald font-bold' : '' }}">Contact</a>
            </nav>

            <!-- Right: Sign In / User & Book Stay CTA -->
            <div class="flex items-center gap-2.5">
                @guest
                <a href="{{ route('login', ['redirect' => route('customer.dashboard')]) }}" class="hidden md:inline-flex items-center gap-1.5 rounded-xl border border-forest/15 bg-white/80 hover:bg-white px-3.5 py-2 text-xs font-bold text-forest transition shadow-2xs" title="Sign In / Dashboard">
                    <i data-lucide="user" class="h-3.5 w-3.5 text-emerald"></i>
                    <span>Sign In</span>
                </a>
                <!-- Mobile Profile Button (Guest) -->
                <a href="{{ route('login', ['redirect' => route('customer.dashboard')]) }}" class="grid h-9.5 w-9.5 place-items-center rounded-xl bg-forest/5 hover:bg-forest/10 text-forest ring-1 ring-forest/10 md:hidden touch-tap" title="Sign In / Dashboard" aria-label="Sign In">
                    <i data-lucide="user" class="h-4.5 w-4.5 text-emerald"></i>
                </a>
                @else
                <button type="button" onclick="openChat()" class="hidden md:inline-flex items-center gap-1.5 rounded-xl border border-forest/15 bg-white/80 hover:bg-white px-3.5 py-2 text-xs font-bold text-forest transition shadow-2xs cursor-pointer" title="Open Concierge Chat">
                    <i data-lucide="message-circle" class="h-3.5 w-3.5 text-emerald"></i>
                    <span>Chat</span>
                </button>
                <a href="{{ route('customer.dashboard') }}" class="hidden md:inline-flex items-center gap-1.5 rounded-xl border border-forest/15 bg-white/80 hover:bg-white px-3.5 py-2 text-xs font-bold text-forest transition shadow-2xs" title="My Dashboard & Stay Pass">
                    <i data-lucide="user-check" class="h-3.5 w-3.5 text-emerald"></i>
                    <span class="max-w-[80px] truncate">{{ Auth::user()->name }}</span>
                </a>
                <!-- Mobile Profile Button (Logged-in) -->
                <a href="{{ route('customer.dashboard') }}" class="grid h-9.5 w-9.5 place-items-center rounded-xl bg-forest text-paper shadow-xs md:hidden touch-tap" title="My Dashboard" aria-label="Dashboard">
                    <i data-lucide="user-check" class="h-4.5 w-4.5 text-brass"></i>
                </a>
                @endguest
                <a href="{{ route('rooms.index') }}" class="hidden rounded-xl bg-forest px-4.5 py-2.5 text-xs font-bold text-paper shadow-card md:block hover:bg-forest/90 transition hover:-translate-y-0.5">Book Stay</a>
            </div>
        </div>
    </div>
</header>
