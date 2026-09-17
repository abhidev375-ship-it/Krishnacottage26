@extends('layouts.customer')

@php
    $hContent = $homepageContent ?? app(\App\Http\Controllers\Admin\AdminController::class)->getHomepageContent();
    $branchesList = $branches ?? \App\Models\Branch::where('status', 'active')->orderBy('sort_order')->get();
    
    // Featured Cottage Rooms for the Accommodations Carousel (All Branches)
    $roomsList = $featuredRooms ?? \App\Models\RoomType::with(['branch', 'category', 'amenitiesList'])
        ->where('is_active', true)
        ->where('is_bookable', true)
        ->orderBy('sort_order')
        ->get();

    // Hero Slides from dynamic settings or active branches
    $heroSlidesList = $hContent['hero_slides'] ?? [];
    if (empty($heroSlidesList) || !is_array($heroSlidesList)) {
        $heroSlidesList = [];
        foreach ($branchesList as $idx => $b) {
            $heroSlidesList[] = [
                'id' => $b->id,
                'title' => $b->display_name ?: $b->name,
                'subtitle' => $b->city ? ($b->city . ' Retreat') : 'Kerala Retreat',
                'description' => $b->tagline ?: 'Private wooden cottages, lush gardens & tranquil verandas.',
                'image' => $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1600&q=85'),
                'tag' => ($b->city ?: 'Kerala') . ' · ' . ($idx === 0 ? 'Hillside Cottages' : ($idx === 1 ? 'Backwater Haven' : 'Coastal Living')),
                'badge' => '★ 4.9 Rating',
                'link' => route('rooms.index', ['branch_id' => $b->id], false),
                'sort_order' => $idx + 1,
                'status' => 'active',
            ];
        }
    }
    $heroSlidesList = array_values(array_filter($heroSlidesList, fn($s) => ($s['status'] ?? 'active') === 'active'));
    usort($heroSlidesList, fn($a, $b) => ($a['sort_order'] ?? 1) <=> ($b['sort_order'] ?? 1));

    // Curated Testimonials
    $reviewsList = (isset($testimonials) && $testimonials->isNotEmpty()) 
        ? $testimonials->where('is_active', true) 
        : collect([
            (object)[
                'guest_name' => $hContent['review_author'] ?? 'Ananya & Ravi',
                'stay_title' => $hContent['review_subtitle'] ?? 'Garden Residence · Munnar Retreat',
                'quote' => $hContent['review_quote'] ?? 'Everything felt easy — from the wooden cottage to dinner to knowing what was nearby. Truly peaceful and pure.',
                'rating' => 5,
            ]
        ]);

    // Curated Gallery Albums (Published with High-Res Photo Collections)
    $galleryAlbumsList = (isset($galleryAlbums) && $galleryAlbums->isNotEmpty())
        ? $galleryAlbums
        : \App\Models\GalleryAlbum::with(['images' => fn($q) => $q->orderBy('sort_order'), 'branch'])
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();
    $galleryAlbumsList = $galleryAlbumsList->filter(fn($a) => $a->images && $a->images->isNotEmpty())->take(6);
@endphp

@section('title', 'Krishna Cottages — Best Luxury Cottages & Homestay in Idukki, Rajakkadu & Munnar, Kerala')
@section('meta_description', 'Discover Krishna Cottages in Rajakkad, Idukki. Book handcrafted wooden cottages, authentic plantation dining, and nature homestays near Munnar with cardamom plantation views.')

@push('styles')
<style>
    /* HERO CAROUSEL FULL-SCREEN / FULL-BLEED LUXURY STYLES */
    #hero-slider {
        position: relative;
        width: 100%;
        height: 74vh;
        min-height: 520px;
        border-radius: 0 !important;
        overflow: hidden;
        background-color: #062922;
        user-select: none;
        display: block;
    }
    @media (min-width: 640px) {
        #hero-slider {
            height: 80vh;
            min-height: 580px;
            border-radius: 0 !important;
        }
    }
    @media (min-width: 1024px) {
        #hero-slider {
            height: calc(100vh - 72px);
            min-height: 660px;
            max-height: 920px;
            border-radius: 0 !important;
        }
    }
    #hero-slides-track {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .hero-slide-item {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        pointer-events: none;
        transition: opacity 1.1s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: opacity;
        z-index: 1;
    }
    .hero-slide-item.active {
        opacity: 1 !important;
        pointer-events: auto !important;
        z-index: 10 !important;
    }
    .hero-slide-item.inactive {
        opacity: 0 !important;
        pointer-events: none !important;
        z-index: 1 !important;
    }
    /* Ken Burns Slow Cinematic Drift Zoom on Active Slide */
    .hero-slide-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transform: scale(1);
        transition: transform 7.5s cubic-bezier(0.25, 1, 0.5, 1), filter 0.8s ease;
        will-change: transform;
    }
    .hero-slide-item.active img {
        transform: scale(1.06);
    }

    /* Luxury Slide Nav Tab / Dot */
    .hero-dot-btn {
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: none;
        outline: none;
    }
    .hero-dot-btn.active {
        background-color: #C9A86A !important;
        color: #083F34 !important;
        box-shadow: 0 4px 14px rgba(201, 168, 106, 0.4);
    }
    .hero-dot-btn.inactive {
        background-color: transparent !important;
        color: rgba(255, 255, 255, 0.7) !important;
    }
    .hero-dot-btn.inactive:hover {
        background-color: rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
    }
</style>
@endpush

@section('content')

<!-- =========================================================================
     1. HERO SECTION: FULL-SCREEN IMMERSIVE CAROUSEL & LUXURY BOOKING STRIP
     ========================================================================= -->
<section class="relative w-full overflow-hidden bg-[#FAF7F0]">
    
    <!-- Full-Bleed Edge-to-Edge Carousel Canvas (No Card/Box Framing) -->
    <div id="hero-slider" class="relative w-full overflow-hidden group select-none">
        
        <!-- Slides Track -->
        <div id="hero-slides-track">
            @foreach($heroSlidesList as $sIdx => $slide)
                <div class="hero-slide-item {{ $sIdx === 0 ? 'active' : 'inactive' }}" data-index="{{ $sIdx }}">
                    <!-- Background High-Res Image with Ken Burns zoom -->
                    <img src="{{ $slide['image'] }}" 
                         alt="{{ $slide['title'] }} — Krishna Cottages Kerala" 
                         class="w-full h-full object-cover object-center brightness-[0.78]" 
                         {!! $sIdx === 0 ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"' !!}
                         onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1920&q=85';" />
                    
                    <!-- Ambient Vignette & Cinema Gradients -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#062922]/95 via-black/45 to-black/30"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-black/55 via-transparent to-black/55"></div>

                    <!-- Top Destination Tag -->
                    <div class="absolute top-5 left-5 sm:top-8 sm:left-10 z-20 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-black/45 backdrop-blur-md text-white text-[11px] sm:text-xs font-semibold tracking-wider uppercase border border-white/20 shadow-xs">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-brass"></i>
                            <span>{{ $slide['tag'] ?? ($slide['subtitle'] ?? 'Krishna Cottages') }}</span>
                        </span>
                        @if(!empty($slide['badge']))
                            <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1 rounded-full bg-brass/25 backdrop-blur-md text-brass text-[11px] font-bold border border-brass/30">
                                {{ $slide['badge'] }}
                            </span>
                        @endif
                    </div>

                    <!-- Slide Counter Indicator -->
                    <div class="absolute top-5 right-5 sm:top-8 sm:right-10 z-20 text-white/90 bg-black/45 backdrop-blur-md px-3 sm:px-4 py-1.5 rounded-full text-xs sm:text-sm font-mono border border-white/15">
                        <span class="text-brass font-bold">0{{ $sIdx + 1 }}</span> / 0{{ count($heroSlidesList) }}
                    </div>

                    <!-- Centered Main Typography & Hero CTAs -->
                    <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-8 max-w-5xl mx-auto text-white">
                        <!-- Eyebrow -->
                        <p class="eyebrow text-brass text-xs sm:text-sm tracking-[.28em] mb-2.5 sm:mb-3.5 drop-shadow-xs font-bold uppercase">
                            {{ $slide['subtitle'] ?? ($slide['tag'] ?? 'PEACEFUL NATURE SANCTUARY') }}
                        </p>

                        <!-- Master Headline -->
                        <h1 class="serif text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight leading-[1.08] text-white drop-shadow-xl max-w-4xl">
                            {{ $slide['title'] }}
                        </h1>

                        <!-- Description -->
                        <p class="mt-3 sm:mt-5 text-sm sm:text-base lg:text-lg text-white/90 max-w-2xl leading-relaxed drop-shadow-xs font-normal line-clamp-2 sm:line-clamp-3">
                            {{ $slide['description'] }}
                        </p>

                        <!-- Hero CTAs -->
                        <div class="mt-6 sm:mt-8 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                            <a href="{{ $slide['link'] ?? route('rooms.index') }}" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-brass hover:bg-white text-forest font-bold text-xs sm:text-sm shadow-xl hover:shadow-2xl transition duration-300 transform hover:-translate-y-0.5 cursor-pointer">
                                <span>Explore Cottages</span>
                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-forest"></i>
                            </a>
                            <button type="button" onclick="openChat()" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-semibold text-xs sm:text-sm border border-white/30 transition duration-300 transform hover:-translate-y-0.5 cursor-pointer">
                                <i data-lucide="message-circle" class="w-4 h-4 text-brass"></i>
                                <span>Chat with Concierge</span>
                            </button>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Left & Right Arrow Navigation Controls -->
        <button type="button" 
                onclick="prevHeroSlide()" 
                class="absolute left-3 sm:left-6 lg:left-8 top-1/2 -translate-y-1/2 z-30 grid h-11 w-11 sm:h-13 sm:w-13 place-items-center rounded-full bg-black/40 hover:bg-brass text-white hover:text-forest backdrop-blur-md border border-white/30 hover:border-brass transition-all duration-300 shadow-xl cursor-pointer hover:scale-105" 
                aria-label="Previous Slide">
            <i data-lucide="chevron-left" class="w-5 h-5 sm:w-6 sm:h-6"></i>
        </button>
        <button type="button" 
                onclick="nextHeroSlide()" 
                class="absolute right-3 sm:right-6 lg:right-8 top-1/2 -translate-y-1/2 z-30 grid h-11 w-11 sm:h-13 sm:w-13 place-items-center rounded-full bg-black/40 hover:bg-brass text-white hover:text-forest backdrop-blur-md border border-white/30 hover:border-brass transition-all duration-300 shadow-xl cursor-pointer hover:scale-105" 
                aria-label="Next Slide">
            <i data-lucide="chevron-right" class="w-5 h-5 sm:w-6 sm:h-6"></i>
        </button>

        <!-- Bottom Luxury Navigation Bar (Responsive Luxury Tabs with Numbers & Names) -->
        <div class="absolute bottom-16 sm:bottom-20 lg:bottom-24 left-1/2 -translate-x-1/2 z-30 flex items-center gap-1.5 sm:gap-2.5 bg-black/40 backdrop-blur-md p-1.5 sm:px-3 sm:py-2 rounded-full border border-white/15 shadow-xl">
            @foreach($heroSlidesList as $dIdx => $slide)
                <button type="button" 
                        onclick="goToHeroSlide({{ $dIdx }})" 
                        class="hero-dot-btn {{ $dIdx === 0 ? 'active' : 'inactive' }} flex items-center gap-2 px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-full text-[11px] sm:text-xs font-semibold" 
                        aria-label="Slide {{ $dIdx + 1 }}">
                    <span class="font-mono font-bold text-xs">{{ sprintf('%02d', $dIdx + 1) }}</span>
                    <span class="hidden md:inline whitespace-nowrap tracking-wide">{{ $slide['title'] }}</span>
                </button>
            @endforeach
        </div>

    </div>

    <!-- =====================================================================
         CLASSIC HOTEL BOOKING ENGINE SEARCH BAR (Clean Floating Pill)
         ===================================================================== -->
    <div class="relative z-40 -mt-8 sm:-mt-10 lg:-mt-12 max-w-5xl mx-auto px-4 mb-6 sm:mb-10">
        <form action="{{ route('rooms.index') }}" method="GET" class="w-full bg-white text-forest rounded-2xl lg:rounded-full p-2.5 sm:p-3 lg:p-3.5 shadow-[0_20px_50px_rgba(6,63,52,0.18)] border border-forest/15">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2 sm:gap-2.5 items-center">
                
                <!-- Field 1: Destination / Branch (lg:col-span-4) -->
                <div class="lg:col-span-4 px-3.5 py-2 bg-paper/60 rounded-xl lg:rounded-l-full border border-forest/10 hover:border-forest/30 transition">
                    <label class="block text-[9px] uppercase tracking-wider font-bold text-forest/50 flex items-center gap-1.5 mb-0.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                        <span>Destination</span>
                    </label>
                    <select name="branch_id" class="w-full bg-transparent text-xs sm:text-sm font-bold text-forest focus:outline-hidden cursor-pointer truncate">
                        <option value="">All Branches (Cottages Central)</option>
                        @foreach($branchesList as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Field 2: Check-In & Check-Out (lg:col-span-4, 2 equal columns side-by-side) -->
                <div class="lg:col-span-4 grid grid-cols-2 gap-2">
                    <!-- Check-In -->
                    <div class="px-3 py-2 bg-paper/60 rounded-xl border border-forest/10 hover:border-forest/30 transition">
                        <label class="block text-[9px] uppercase tracking-wider font-bold text-forest/50 flex items-center gap-1 mb-0.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                            <span>Check-In</span>
                        </label>
                        <input type="date" 
                                name="check_in" 
                                id="home-check-in"
                                value="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                min="{{ date('Y-m-d') }}"
                                onchange="updateCheckOutMin()"
                                class="w-full bg-transparent text-xs sm:text-sm font-bold text-forest focus:outline-hidden cursor-pointer" />
                    </div>

                    <!-- Check-Out -->
                    <div class="px-3 py-2 bg-paper/60 rounded-xl border border-forest/10 hover:border-forest/30 transition">
                        <label class="block text-[9px] uppercase tracking-wider font-bold text-forest/50 flex items-center gap-1 mb-0.5">
                            <i data-lucide="calendar-check-2" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                            <span>Check-Out</span>
                        </label>
                        <input type="date" 
                                name="check_out" 
                                id="home-check-out"
                                value="{{ date('Y-m-d', strtotime('+3 days')) }}" 
                                min="{{ date('Y-m-d', strtotime('+2 days')) }}"
                                class="w-full bg-transparent text-xs sm:text-sm font-bold text-forest focus:outline-hidden cursor-pointer" />
                    </div>
                </div>

                <!-- Field 3: Guests & Submit Action (lg:col-span-4) -->
                <div class="lg:col-span-4 grid grid-cols-2 gap-2">
                    <!-- Guests -->
                    <div class="px-3 py-2 bg-paper/60 rounded-xl border border-forest/10 hover:border-forest/30 transition">
                        <label class="block text-[9px] uppercase tracking-wider font-bold text-forest/50 flex items-center gap-1 mb-0.5">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                            <span>Guests</span>
                        </label>
                        <select name="adults" class="w-full bg-transparent text-xs sm:text-sm font-bold text-forest focus:outline-hidden cursor-pointer">
                            <option value="1">1 Guest</option>
                            <option value="2" selected>2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                            <option value="5">5+ Guests</option>
                        </select>
                        <input type="hidden" name="children" value="0">
                    </div>

                    <!-- Search Button -->
                    <div class="flex items-center">
                        <button type="submit" class="w-full h-full min-h-[44px] py-2.5 px-3 rounded-xl lg:rounded-r-full bg-brass hover:brightness-105 text-forest font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md hover:shadow-lg transition cursor-pointer">
                            <i data-lucide="search" class="w-4 h-4 shrink-0"></i>
                            <span class="whitespace-nowrap">Check Stays</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

</section>



<!-- =========================================================================
     2. WELCOME & PHILOSOPHY: EDITORIAL STORY & RESORT SHOWCASE
     ========================================================================= -->
<section class="py-14 sm:py-20 lg:py-24 bg-[#FAF7F0] overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            
            <!-- Left Column: Rich Narrative & Signature Experiences (7 Cols) -->
            <div class="lg:col-span-7 space-y-6 sm:space-y-8">
                
                <!-- Eyebrow & Brand Seal -->
                <div class="space-y-2.5">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-forest/5 border border-forest/15 text-forest text-xs font-bold uppercase tracking-[0.22em] shadow-2xs">
                        <i data-lucide="compass" class="w-3.5 h-3.5 text-brass"></i>
                        <span>{{ $hContent['welcome_eyebrow'] ?? 'Slow Living In Kerala · Est. 2024' }}</span>
                    </div>
                    <h2 class="serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest tracking-tight leading-[1.14]">
                        {{ $hContent['welcome_heading'] ?? 'A Sanctuary of Hillside Serenity, Pure Mist & Handcrafted Wooden Cottages' }}
                    </h2>
                </div>

                <!-- Narrative Story -->
                <div class="space-y-3.5 text-forest/80 text-sm sm:text-base leading-relaxed font-normal">
                    <p>
                        {{ $hContent['welcome_description'] ?? 'Tucked into the misty emerald slopes of Rajakkad, Idukki, and neighboring Munnar, Krishna Cottages was conceived as a timeless sanctuary where hurry gives way to stillness. Surrounded by towering silver oaks, organic cardamom valleys, and rolling tea estates, our private wooden residences invite you to breathe deeply and reconnect with what matters.' }}
                    </p>
                    <p class="hidden sm:block text-forest/70 text-xs sm:text-sm">
                        From the gentle clatter of earthenware clay pots simmering with authentic Kerala heirloom spices to quiet sunset hours on cedar verandas, every detail is considered for restorative, peaceful slow living.
                    </p>
                </div>

                <!-- 4 Signature Experience Cards (2x2 Grid) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4 pt-1">
                    <!-- Card 1: Handcrafted Cottages -->
                    <div class="p-4 rounded-2xl bg-white border border-forest/10 shadow-2xs hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-xl bg-forest/5 group-hover:bg-forest group-hover:text-brass text-emerald flex items-center justify-center mb-3 transition">
                            <i data-lucide="home" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-forest text-sm">Private Wooden Cottages</h4>
                        <p class="text-xs text-forest/70 mt-1 leading-relaxed">Handcrafted teak &amp; cedar residences featuring wide open verandas overlooking misty valleys.</p>
                    </div>

                    <!-- Card 2: Clay-Pot Farm Dining -->
                    <div class="p-4 rounded-2xl bg-white border border-forest/10 shadow-2xs hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-xl bg-forest/5 group-hover:bg-forest group-hover:text-brass text-emerald flex items-center justify-center mb-3 transition">
                            <i data-lucide="utensils" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-forest text-sm">Clay-Pot Estate Kitchen</h4>
                        <p class="text-xs text-forest/70 mt-1 leading-relaxed">Authentic slow-cooked Kerala recipes made in traditional earthenware with single-origin spices.</p>
                    </div>

                    <!-- Card 3: Cardamom Trails -->
                    <div class="p-4 rounded-2xl bg-white border border-forest/10 shadow-2xs hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-xl bg-forest/5 group-hover:bg-forest group-hover:text-brass text-emerald flex items-center justify-center mb-3 transition">
                            <i data-lucide="trees" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-forest text-sm">Estate Trails &amp; Waterfalls</h4>
                        <p class="text-xs text-forest/70 mt-1 leading-relaxed">Guided morning walks through tea, cardamom, and clove groves leading to secret hillside streams.</p>
                    </div>

                    <!-- Card 4: Dedicated Butler & Concierge -->
                    <div class="p-4 rounded-2xl bg-white border border-forest/10 shadow-2xs hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-xl bg-forest/5 group-hover:bg-forest group-hover:text-brass text-emerald flex items-center justify-center mb-3 transition">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-forest text-sm">24/7 Dedicated Concierge</h4>
                        <p class="text-xs text-forest/70 mt-1 leading-relaxed">Attentive in-cottage hosts, private jeep safaris, bonfire evenings, and custom excursions.</p>
                    </div>
                </div>

                <!-- Host Welcome Quote Card -->
                <div class="p-4 sm:p-5 rounded-2xl bg-white border-l-4 border-brass border border-forest/10 shadow-2xs flex items-start gap-4">
                    <div class="w-9 h-9 rounded-full bg-forest text-brass flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="quote" class="w-4 h-4"></i>
                    </div>
                    <div class="space-y-1 text-xs sm:text-sm text-forest/80 leading-relaxed">
                        <p class="serif italic">
                            "We created Krishna Cottages as a haven where time gently pauses, hot spiced chai is poured by the rain, and mist greets you each sunrise."
                        </p>
                        <span class="block text-[11px] font-bold text-forest/60 uppercase tracking-wider">— The Resident Hosts &middot; Krishna Cottages, Rajakkad &amp; Munnar</span>
                    </div>
                </div>

                <!-- Action Links -->
                <div class="flex flex-wrap items-center gap-3.5 pt-2">
                    <a href="#cottages" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-forest hover:bg-emerald text-paper font-bold text-xs sm:text-sm shadow-md transition cursor-pointer">
                        <span>Explore Accommodations</span>
                        <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-brass"></i>
                    </a>
                    <button type="button" onclick="openChat()" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white hover:bg-paper text-forest font-semibold text-xs sm:text-sm border border-forest/15 shadow-2xs transition cursor-pointer">
                        <i data-lucide="message-circle" class="w-4 h-4 text-emerald"></i>
                        <span>Ask Concierge Desk</span>
                    </button>
                </div>

            </div>

            <!-- Right Column: Editorial Multi-Layer Photography Showcase (5 Cols) -->
            <div class="lg:col-span-5 relative mt-6 lg:mt-0">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    
                    <!-- Main Cottage Veranda Photo -->
                    <div class="relative overflow-hidden rounded-3xl shadow-2xl border-4 border-white bg-forest/10 aspect-[4/5] sm:h-[490px]">
                        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1000&q=85" 
                             alt="Krishna Cottages Hillside Wooden Veranda" 
                             class="w-full h-full object-cover object-center hover:scale-105 transition duration-700" 
                             loading="lazy" 
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=85';" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <p class="text-[11px] uppercase tracking-wider text-brass font-bold">Hillside Sanctuaries</p>
                            <h3 class="serif text-lg font-bold text-white leading-snug">Private Verandas &amp; Cool Mist</h3>
                        </div>
                    </div>

                    <!-- Floating Overlapping Culinary / Farm Inset Photo (Bottom Left) -->
                    <div class="absolute -bottom-6 -left-4 sm:-left-8 w-44 sm:w-52 h-36 sm:h-44 rounded-2xl overflow-hidden shadow-2xl border-4 border-white bg-forest/10 group">
                        <img src="https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=85" 
                             alt="Authentic Kerala Clay Pot Dining at Krishna Cottages" 
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-500" 
                             loading="lazy" 
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=85';" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-2.5 left-2.5 right-2.5 text-white">
                            <span class="text-[10px] font-bold block leading-tight text-white">Farm Clay-Pot Kitchen</span>
                            <span class="text-[9px] text-brass block">Authentic Heirloom Spices</span>
                        </div>
                    </div>

                    <!-- Floating Gold Rating Seal Badge (Top Right) -->
                    <div class="absolute -top-5 -right-2 sm:-right-6 bg-forest text-paper p-3.5 sm:p-4 rounded-2xl shadow-xl border border-brass/30 flex items-center gap-3 backdrop-blur-md">
                        <div class="text-brass">
                            <div class="flex items-center gap-0.5 text-brass">
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                            </div>
                            <span class="text-base sm:text-lg font-black text-white">4.95 / 5</span>
                        </div>
                        <div class="border-l border-white/20 pl-3">
                            <span class="block text-[11px] font-bold text-white uppercase tracking-wider">Verified Stays</span>
                            <span class="block text-[9px] text-paper/70">500+ Guests &middot; Rajakkad &amp; Munnar</span>
                        </div>
                    </div>

                    <!-- Floating Micro-Stats Strip (Bottom Right) -->
                    <div class="hidden sm:flex absolute bottom-3 right-3 bg-white/95 backdrop-blur-md text-forest px-3.5 py-2 rounded-xl shadow-lg border border-forest/10 items-center gap-2 text-[11px] font-bold">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald"></i>
                        <span>100% Organic Plantation &middot; 24/7 Butler</span>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>


<!-- =========================================================================
     3. FEATURED COTTAGES CAROUSEL (WITH LIVE BRANCH FILTER)
     ========================================================================= -->
<section id="cottages" class="py-12 sm:py-16 bg-[#F4F1E8]">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <!-- Section Header with Prev/Next Controls -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
            <div class="space-y-1">
                <span class="eyebrow text-emerald font-bold">ACCOMMODATIONS</span>
                <h2 class="serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest tracking-tight">
                    Featured Cottages &amp; Suites
                </h2>
                <p class="text-xs sm:text-sm text-forest/70 max-w-xl">
                    Explore our private wooden cottages and hillside suites crafted for restorative slow stays.
                </p>
            </div>
            
            <div class="flex items-center gap-2 self-end sm:self-auto">
                <button type="button" 
                        onclick="scrollCottagesTrack('left')" 
                        class="grid h-10 w-10 place-items-center rounded-full bg-white border border-forest/15 hover:bg-forest hover:text-white text-forest transition shadow-xs cursor-pointer" 
                        aria-label="Scroll Left">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <button type="button" 
                        onclick="scrollCottagesTrack('right')" 
                        class="grid h-10 w-10 place-items-center rounded-full bg-white border border-forest/15 hover:bg-forest hover:text-white text-forest transition shadow-xs cursor-pointer" 
                        aria-label="Scroll Right">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
                <a id="cottages-view-all-link" href="{{ route('rooms.index') }}" class="ml-2 hidden sm:inline-flex items-center gap-1 text-xs font-bold text-emerald hover:text-forest underline transition">
                    View All
                </a>
            </div>
        </div>

        <!-- Branch Filter Pills Row -->
        <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar pb-3 mb-6 border-b border-forest/10">
            <button type="button" 
                    onclick="filterCottagesByBranch('all')" 
                    id="branch-pill-all"
                    class="cottage-branch-pill px-4 py-2 rounded-full text-xs font-bold transition shadow-xs bg-forest text-paper cursor-pointer shrink-0">
                All Retreats ({{ $roomsList->count() }})
            </button>
            @foreach($branchesList as $b)
                @php $bRoomCount = $roomsList->where('branch_id', $b->id)->count(); @endphp
                <button type="button" 
                        onclick="filterCottagesByBranch({{ $b->id }})" 
                        id="branch-pill-{{ $b->id }}"
                        class="cottage-branch-pill px-4 py-2 rounded-full text-xs font-semibold transition bg-white text-forest/70 hover:text-forest hover:bg-forest/5 border border-forest/15 cursor-pointer shrink-0">
                    {{ $b->city ?: $b->name }} ({{ $bRoomCount }})
                </button>
            @endforeach
        </div>

        <!-- Empty State Container -->
        <div id="cottages-empty-state" class="hidden w-full text-center py-12 px-4 rounded-3xl bg-white border border-forest/10 my-4 space-y-2">
            <i data-lucide="bed" class="w-10 h-10 text-brass mx-auto mb-2 opacity-60"></i>
            <h3 class="serif text-lg font-bold text-forest">No Cottages Found</h3>
            <p class="text-xs text-forest/60 max-w-sm mx-auto">There are no cottages currently listed for this location. Please select "All Retreats".</p>
            <button type="button" onclick="filterCottagesByBranch('all')" class="mt-2 inline-flex items-center gap-1 px-4 py-1.5 rounded-full bg-forest text-paper text-xs font-bold cursor-pointer">
                View All Retreats
            </button>
        </div>

        <!-- Cottages Horizontal Carousel Track -->
        <div id="cottages-track" class="flex gap-6 overflow-x-auto hide-scrollbar pb-4 snap-x snap-mandatory scroll-smooth -mx-4 px-4 sm:mx-0 sm:px-0">
            @forelse($roomsList as $room)
                @php
                    $roomCover = $room->cover_image_url ?: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=85';
                    $amenities = $room->amenities ?? ['Balcony', 'King Bed', 'Nature View'];
                    if (is_string($amenities)) {
                        $amenities = json_decode($amenities, true) ?: [$amenities];
                    }
                @endphp
                <div class="cottage-card snap-start shrink-0 w-[290px] sm:w-[340px] lg:w-[380px] rounded-2xl sm:rounded-3xl bg-white border border-forest/10 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group"
                     data-branch-id="{{ $room->branch_id }}">
                    
                    <div>
                        <!-- Cottage Photo -->
                        <div class="relative h-52 sm:h-60 w-full overflow-hidden bg-forest/5">
                            <a href="{{ route('rooms.show', $room->slug) }}" class="block w-full h-full">
                                <img src="{{ $roomCover }}" 
                                     alt="{{ $room->name }} — Krishna Cottages" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                                     loading="lazy" 
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=85';" />
                            </a>
                            
                            <!-- Destination Chip -->
                            <div class="absolute top-3 left-3 z-10">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/50 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i>
                                    <span>{{ $room->branch->city ?? ($room->branch->name ?? 'Kerala') }}</span>
                                </span>
                            </div>

                            <!-- Rating Badge -->
                            <div class="absolute top-3 right-3 z-10">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white/90 backdrop-blur-md text-forest text-[11px] font-bold shadow-xs">
                                    <i data-lucide="star" class="w-3 h-3 fill-brass text-brass"></i>
                                    <span>4.9</span>
                                </span>
                            </div>
                        </div>

                        <!-- Cottage Details -->
                        <div class="p-5 space-y-2.5">
                            <a href="{{ route('rooms.show', $room->slug) }}" class="serif text-xl font-bold text-forest hover:text-emerald transition line-clamp-1 block">
                                {{ $room->name }}
                            </a>
                            
                            <!-- Specs Line -->
                            <div class="flex items-center gap-3 text-xs text-forest/65 font-medium">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-emerald"></i>
                                    <span>Up to {{ $room->max_guests }} Guests</span>
                                </span>
                                <span>&middot;</span>
                                <span class="flex items-center gap-1">
                                    <i data-lucide="bed" class="w-3.5 h-3.5 text-emerald"></i>
                                    <span>{{ $room->bed_type ?? 'King Bed' }}</span>
                                </span>
                            </div>

                            <p class="text-xs text-forest/70 line-clamp-2 leading-relaxed font-normal">
                                {{ $room->short_description ?: 'Handcrafted wooden villa nestled in tea gardens with private sit-out and panoramic estate views.' }}
                            </p>

                            <!-- Amenities Pills -->
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach(array_slice($amenities, 0, 3) as $am)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-paper text-forest/80 text-[10px] font-semibold border border-forest/10">
                                        <i data-lucide="check" class="w-2.5 h-2.5 text-emerald"></i>
                                        <span>{{ is_string($am) ? $am : ($am['name'] ?? 'Feature') }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Price & Booking CTAs -->
                    <div class="p-5 pt-0">
                        <div class="pt-3.5 border-t border-forest/10 flex items-center justify-between gap-3">
                            <div>
                                <span class="serif text-xl font-bold text-forest">₹{{ number_format($room->base_price, 0) }}</span>
                                <span class="text-[11px] text-forest/55">/ night</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('rooms.show', $room->slug) }}" class="px-3 py-2 rounded-xl border border-forest/20 text-forest text-xs font-bold hover:bg-forest/5 transition">
                                    Details
                                </a>
                                <a href="{{ route('booking.checkout', $room->id) }}" class="px-4 py-2 rounded-xl bg-forest hover:bg-emerald text-paper text-xs font-bold shadow-xs hover:shadow-md transition flex items-center gap-1">
                                    <span>Book</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3 text-brass"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div class="w-full text-center py-12 text-forest/60 text-sm">
                    No cottage rooms currently available. Please check back shortly.
                </div>
            @endforelse
        </div>


        <!-- Mobile View All CTA -->
        <div class="mt-4 text-center sm:hidden">
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald underline">
                <span>View All Accommodations</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

    </div>
</section>


<!-- =========================================================================
     4. CURATED EXPERIENCES: CLEAN 3-CARD SHOWCASE
     ========================================================================= -->
<section class="py-12 sm:py-16 bg-[#FAF7F0]">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-2">
            <span class="eyebrow text-emerald font-bold">IMMERSIVE EXPERIENCES</span>
            <h2 class="serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest tracking-tight">
                Life at Krishna Cottages
            </h2>
            <p class="text-xs sm:text-sm text-forest/70">
                Immerse your senses in authentic Kerala dining, farm spices, and scenic hillside nature.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            
            <!-- Experience 1: Dining -->
            <div class="group rounded-3xl overflow-hidden bg-white border border-forest/10 shadow-xs hover:shadow-xl transition duration-300 flex flex-col justify-between">
                <div>
                    <div class="relative h-60 w-full overflow-hidden bg-forest/5">
                        <img src="{{ $hContent['dining_image'] ?? 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=800&q=85' }}" 
                             alt="Plantation Kitchen Dining" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                             loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 left-4 text-white">
                            <span class="eyebrow text-brass text-[9px]">CUISINE</span>
                            <h3 class="serif text-xl font-bold">Plantation Kitchen</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-2">
                        <p class="text-xs sm:text-sm text-forest/75 leading-relaxed">
                            Clay-pot Kerala cuisine, slow-cooked curries, fresh cardamom chai, and wholesome village harvests served daily.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <a href="{{ route('dining.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald group-hover:text-forest transition">
                        <span>Explore Dining &amp; Menu</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition"></i>
                    </a>
                </div>
            </div>

            <!-- Experience 2: Spices -->
            <div class="group rounded-3xl overflow-hidden bg-white border border-forest/10 shadow-xs hover:shadow-xl transition duration-300 flex flex-col justify-between">
                <div>
                    <div class="relative h-60 w-full overflow-hidden bg-forest/5">
                        <img src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=800&q=85" 
                             alt="Krishna Spices Farm" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                             loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 left-4 text-white">
                            <span class="eyebrow text-brass text-[9px]">ESTATE HARVEST</span>
                            <h3 class="serif text-xl font-bold">Krishna Spices</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-2">
                        <p class="text-xs sm:text-sm text-forest/75 leading-relaxed">
                            Experience genuine estate-cultivated cardamom, black pepper, and whole cloves delivered right to your cottage door.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <a href="{{ route('spices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald group-hover:text-forest transition">
                        <span>Shop Estate Spices</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition"></i>
                    </a>
                </div>
            </div>

            <!-- Experience 3: Nature & Facilities -->
            <div class="group rounded-3xl overflow-hidden bg-white border border-forest/10 shadow-xs hover:shadow-xl transition duration-300 flex flex-col justify-between">
                <div>
                    <div class="relative h-60 w-full overflow-hidden bg-forest/5">
                        <img src="{{ $hContent['experience_image_main'] ?? 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=800&q=85' }}" 
                             alt="Nature Surroundings" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                             loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 left-4 text-white">
                            <span class="eyebrow text-brass text-[9px]">ACTIVITIES</span>
                            <h3 class="serif text-xl font-bold">Nature &amp; Sights</h3>
                        </div>
                    </div>
                    <div class="p-6 space-y-2">
                        <p class="text-xs sm:text-sm text-forest/75 leading-relaxed">
                            Morning birdwatching trails, nearby Kuthumkal waterfalls, tea garden hikes, and peaceful evening campfires.
                        </p>
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <a href="{{ route('facilities.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald group-hover:text-forest transition">
                        <span>View Facilities &amp; Trails</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>


<!-- =========================================================================
     5. PHOTO GALLERY ALBUMS: OPENABLE COLLECTIONS & LIGHTBOX PREVIEW
     ========================================================================= -->
<section class="py-12 sm:py-16 bg-[#F4F1E8]">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div class="space-y-1">
                <span class="eyebrow text-emerald font-bold">VISUAL ALBUMS</span>
                <h2 class="serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest tracking-tight">
                    Moments in the Mist
                </h2>
                <p class="text-xs sm:text-sm text-forest/70">
                    Click any album to explore high-resolution photograph collections.
                </p>
            </div>
            <a href="{{ route('gallery.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-forest text-paper text-xs font-bold hover:bg-emerald transition shadow-xs">
                <span>View All Albums</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
            </a>
        </div>

        <!-- 6 Real Gallery Album Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            @forelse($galleryAlbumsList as $album)
                @php
                    $coverImg = $album->cover_image_url ?: ($album->images->first()->image_url ?? 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80');
                    $photoCount = $album->images->count();
                @endphp
                <div onclick="openAlbumViewer({{ $album->id }})" 
                     class="group bg-white rounded-3xl overflow-hidden border border-forest/10 shadow-xs hover:shadow-xl transition duration-500 cursor-pointer flex flex-col justify-between">
                    
                    <!-- Cover Image Container -->
                    <div class="relative h-56 sm:h-60 w-full overflow-hidden bg-forest/5">
                        <img src="{{ $coverImg }}" 
                             alt="{{ $album->name }}" 
                             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80';"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                             loading="lazy" />
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>

                        <!-- Top Badges -->
                        <div class="absolute top-3 left-3 right-3 flex items-center justify-between pointer-events-none">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/50 backdrop-blur-md text-white text-[10px] font-bold">
                                <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i>
                                <span>{{ $album->branch ? $album->branch->name : 'All Retreats' }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-paper text-[10px] font-bold">
                                <i data-lucide="images" class="w-3 h-3 text-brass"></i>
                                <span>{{ $photoCount }} {{ \Illuminate\Support\Str::plural('Photo', $photoCount) }}</span>
                            </span>
                        </div>

                        <!-- Center Hover Indicator -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 pointer-events-none">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/95 text-forest text-xs font-bold shadow-lg transform translate-y-2 group-hover:translate-y-0 transition duration-300">
                                <span>Open Album</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-emerald"></i>
                            </span>
                        </div>

                        <!-- Bottom Title in Cover -->
                        <div class="absolute bottom-3 left-3.5 right-3.5 text-white">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-brass block mb-0.5 capitalize">
                                {{ $album->category ?: 'Sanctuary' }}
                            </span>
                            <h3 class="serif text-lg sm:text-xl font-bold text-white leading-snug line-clamp-1">
                                {{ $album->name }}
                            </h3>
                        </div>
                    </div>

                    <!-- Album Card Body & Thumbnails Preview -->
                    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-3">
                        <p class="text-xs text-forest/70 leading-relaxed line-clamp-2">
                            {{ $album->description ?: 'Explore high-resolution photographs from this curated resort collection.' }}
                        </p>

                        <!-- Thumbnail Strip (up to 4 thumbnails) -->
                        @if($album->images->isNotEmpty())
                            <div class="grid grid-cols-4 gap-1.5 pt-1">
                                @foreach($album->images->take(4) as $tIdx => $tImg)
                                    <div class="relative h-12 rounded-lg overflow-hidden bg-forest/5 border border-forest/10">
                                        <img src="{{ $tImg->image_url }}" 
                                             alt="{{ $tImg->title ?? 'Photo' }}" 
                                             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=300&q=70';"
                                             class="w-full h-full object-cover"
                                             loading="lazy" />
                                        @if($tIdx === 3 && $photoCount > 4)
                                            <div class="absolute inset-0 bg-black/60 backdrop-blur-2xs flex items-center justify-center text-white text-[10px] font-bold">
                                                +{{ $photoCount - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Card Footer CTA -->
                        <div class="pt-2 border-t border-forest/10 flex items-center justify-between text-xs font-bold text-forest">
                            <span class="inline-flex items-center gap-1 text-emerald group-hover:text-forest transition">
                                <span>Browse {{ $photoCount }} Photos</span>
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
                            </span>
                            <span class="text-[10px] text-forest/40 font-normal">Click to open</span>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-forest/10">
                    <i data-lucide="image" class="w-10 h-10 text-brass mx-auto mb-2 opacity-60"></i>
                    <h3 class="serif text-lg font-bold text-forest">Visual Album Collection</h3>
                    <p class="text-xs text-forest/60 mt-1">Our curated albums are currently being refreshed.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>


<!-- =========================================================================
     6. GUEST TESTIMONIALS: CLEAN QUOTES CAROUSEL
     ========================================================================= -->
<section class="py-12 sm:py-16 bg-[#FAF7F0]">
    <div class="mx-auto max-w-4xl px-4 md:px-6 text-center">
        
        <span class="eyebrow text-emerald font-bold">GUEST MEMORIES</span>
        <h2 class="serif text-3xl sm:text-4xl font-bold text-forest tracking-tight mt-1 mb-8">
            Stories from Our Visitors
        </h2>

        <!-- Sliding Quotes Card -->
        <div id="testimonials-carousel" class="relative bg-white rounded-3xl p-6 sm:p-10 border border-forest/10 shadow-lg select-none">
            
            <!-- Slides Track -->
            <div id="testimonial-track-wrapper" class="overflow-hidden">
                <div id="testimonial-track" class="flex transition-transform duration-500 ease-out">
                    @foreach($reviewsList as $tIdx => $t)
                        <div class="testimonial-item w-full shrink-0 px-2 sm:px-6 space-y-4">
                            <!-- 5 Stars -->
                            <div class="flex items-center justify-center gap-1 text-brass text-base">
                                @for($i = 1; $i <= ($t->rating ?? 5); $i++)
                                    <span>★</span>
                                @endfor
                            </div>
                            <!-- Quote -->
                            <p class="serif text-lg sm:text-2xl text-forest leading-relaxed font-normal italic">
                                “{{ $t->quote }}”
                            </p>
                            <!-- Author -->
                            <div class="pt-2">
                                <p class="text-sm font-bold text-forest">{{ $t->guest_name }}</p>
                                <p class="text-xs text-forest/50 font-medium mt-0.5">{{ $t->stay_title }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Prev / Next Chevrons -->
            <button type="button" 
                    onclick="prevTestimonial()" 
                    class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 grid h-9 w-9 place-items-center rounded-full bg-paper hover:bg-forest hover:text-white text-forest transition shadow-xs cursor-pointer" 
                    aria-label="Previous Review">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <button type="button" 
                    onclick="nextTestimonial()" 
                    class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 grid h-9 w-9 place-items-center rounded-full bg-paper hover:bg-forest hover:text-white text-forest transition shadow-xs cursor-pointer" 
                    aria-label="Next Review">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>

            <!-- Indicator Dots -->
            <div class="mt-6 flex items-center justify-center gap-1.5">
                @foreach($reviewsList as $idx => $t)
                    <button type="button" 
                            onclick="goToTestimonial({{ $idx }})" 
                            class="testimonial-dot h-1.5 rounded-full transition-all duration-300 {{ $idx === 0 ? 'w-5 bg-forest' : 'w-1.5 bg-forest/20' }}" 
                            aria-label="Slide {{ $idx + 1 }}"></button>
                @endforeach
            </div>

        </div>

    </div>
</section>


<!-- =========================================================================
     7. OUR DESTINATIONS: BRANCH SANCTUARIES
     ========================================================================= -->
<section class="py-12 sm:py-16 bg-[#F4F1E8]">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <div class="max-w-2xl mx-auto text-center mb-10 space-y-1">
            <span class="eyebrow text-emerald font-bold">OUR RETREATS</span>
            <h2 class="serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest tracking-tight">
                Our Sanctuaries Across Kerala
            </h2>
            <p class="text-xs sm:text-sm text-forest/70">
                Explore our cottage locations in Idukki, Rajakkad, and Munnar hills.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($branchesList as $b)
                @php
                    $bImage = $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=85');
                @endphp
                <div class="rounded-3xl overflow-hidden bg-white border border-forest/10 shadow-xs hover:shadow-lg transition duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="relative h-52 w-full overflow-hidden bg-forest/5">
                            <img src="{{ $bImage }}" 
                                 alt="{{ $b->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                                 loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/40 backdrop-blur-md text-white text-[10px] font-bold">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i>
                                    <span>{{ $b->city }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="p-5 space-y-1.5">
                            <h3 class="serif text-xl font-bold text-forest">{{ $b->name }}</h3>
                            <p class="text-xs text-forest/60 line-clamp-2">
                                {{ $b->tagline ?: ($b->city . ', Kerala. Private wooden cottages immersed in nature.') }}
                            </p>
                            @if($b->phone)
                                <div class="pt-2">
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $b->phone) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald hover:underline">
                                        <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                                        <span>{{ $b->phone }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-5 pt-0">
                        <div class="pt-3 border-t border-forest/10 flex items-center justify-between">
                            <a href="{{ route('rooms.index', ['branch_id' => $b->id]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald hover:text-forest transition">
                                <span>Browse Branch Stays</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                            <a href="{{ route('rooms.index', ['branch_id' => $b->id]) }}" class="px-3 py-1.5 rounded-lg bg-forest text-paper text-[11px] font-bold hover:bg-emerald transition">
                                Book
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>


<!-- =========================================================================
     8. WARM CALL-TO-ACTION BANNER
     ========================================================================= -->
<section class="py-12 sm:py-16 bg-[#F4F1E8]">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <div class="rounded-3xl lg:rounded-[36px] bg-forest text-paper p-8 sm:p-12 lg:p-14 shadow-2xl relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-emerald/20 blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 max-w-3xl space-y-4">
                <span class="eyebrow text-brass font-bold">RESERVE YOUR ESCAPE</span>
                <h2 class="serif text-3xl sm:text-5xl font-bold tracking-tight leading-tight">
                    Ready to unwind in the quiet hills of Kerala?
                </h2>
                <p class="text-xs sm:text-sm text-paper/80 leading-relaxed max-w-xl font-normal">
                    Direct bookings receive best rate guarantee, freshly prepared garden breakfast, and personalized concierge desk service for a peaceful getaway.
                </p>

                <div class="pt-4 flex flex-wrap items-center gap-3.5">
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-paper hover:bg-white text-forest font-bold text-xs sm:text-sm shadow-md transition duration-300">
                        <span>Book Your Stay</span>
                        <i data-lucide="arrow-up-right" class="w-4 h-4 text-brass"></i>
                    </a>
                    <button type="button" onclick="openChat()" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-paper font-semibold text-xs sm:text-sm transition cursor-pointer">
                        <i data-lucide="message-circle" class="w-4 h-4 text-brass"></i>
                        <span>Chat with Concierge</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================================
     INTERACTIVE ALBUM VIEWER MODAL
     ========================================================================= -->
<div id="album-viewer-modal" class="hidden fixed inset-0 z-[95] bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div class="bg-[#FAF7F0] w-full max-w-5xl rounded-3xl shadow-2xl border border-forest/10 overflow-hidden flex flex-col max-h-[92vh] my-auto">
        <!-- MODAL HEADER -->
        <div class="bg-forest text-paper p-5 sm:p-6 relative shrink-0">
            <button type="button" onclick="closeAlbumViewer()" class="absolute top-4 sm:top-5 right-4 sm:right-5 text-paper/80 hover:text-paper bg-white/10 hover:bg-white/20 p-2 rounded-full transition cursor-pointer" aria-label="Close modal">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="max-w-2xl pr-8">
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span id="viewer-branch" class="eyebrow text-brass bg-white/10 px-2.5 py-0.5 rounded-full text-[10px]"></span>
                    <span id="viewer-category" class="text-[10px] uppercase font-bold tracking-wider text-paper/70 bg-white/5 px-2 py-0.5 rounded-full"></span>
                    <span id="viewer-count" class="text-[10px] font-bold text-brass bg-brass/20 px-2 py-0.5 rounded-full"></span>
                </div>
                <h2 id="viewer-title" class="serif text-2xl sm:text-3xl font-bold text-paper"></h2>
                <p id="viewer-desc" class="text-xs sm:text-sm text-paper/70 mt-2 leading-relaxed"></p>
            </div>
        </div>

        <!-- MODAL BODY: PHOTO GRID -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1">
            <div id="viewer-photos-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Populated dynamically via JS -->
            </div>
            <div id="viewer-empty-state" class="hidden py-12 text-center text-forest/50 text-xs">
                No photographs in this album yet.
            </div>
        </div>

        <!-- MODAL FOOTER -->
        <div class="p-4 bg-white border-t border-forest/10 flex items-center justify-between text-xs text-forest/60 shrink-0">
            <span>Click any photograph to view in full resolution</span>
            <button type="button" onclick="closeAlbumViewer()" class="px-4 py-1.5 rounded-full bg-forest/10 hover:bg-forest/20 text-forest font-semibold transition cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>

<!-- =========================================================================
     FULLSCREEN LIGHTBOX SLIDESHOW MODAL
     ========================================================================= -->
<div id="lightbox-modal" class="hidden fixed inset-0 z-[120] bg-black/95 flex flex-col justify-between p-4 sm:p-6 select-none">
    <!-- Top toolbar -->
    <div class="flex items-center justify-between text-white z-50">
        <div class="flex items-center gap-3">
            <span id="lightbox-counter" class="text-xs font-mono bg-white/15 px-3 py-1 rounded-full text-brass">1 / 1</span>
            <span id="lightbox-album-tag" class="text-xs text-white/70 hidden sm:inline"></span>
        </div>
        <button type="button" onclick="closeLightbox()" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 p-2.5 rounded-full transition cursor-pointer" aria-label="Close lightbox">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Main display with Prev / Next Navigation -->
    <div class="relative flex-1 flex items-center justify-center my-2 overflow-hidden">
        <!-- Prev button -->
        <button id="lightbox-prev-btn" type="button" onclick="lightboxPrev(event)" class="absolute left-2 sm:left-6 z-40 text-white/80 hover:text-white bg-black/60 hover:bg-black/80 p-3 rounded-full backdrop-blur-xs transition shadow-lg cursor-pointer" aria-label="Previous photo">
            <i data-lucide="chevron-left" class="w-6 h-6 sm:w-8 sm:h-8"></i>
        </button>

        <img id="lightbox-img" src="" alt="Cottage Photograph" class="max-h-[75vh] max-w-[92vw] sm:max-w-[85vw] rounded-xl sm:rounded-2xl object-contain shadow-2xl transition duration-200">

        <!-- Next button -->
        <button id="lightbox-next-btn" type="button" onclick="lightboxNext(event)" class="absolute right-2 sm:right-6 z-40 text-white/80 hover:text-white bg-black/60 hover:bg-black/80 p-3 rounded-full backdrop-blur-xs transition shadow-lg cursor-pointer" aria-label="Next photo">
            <i data-lucide="chevron-right" class="w-6 h-6 sm:w-8 sm:h-8"></i>
        </button>
    </div>

    <!-- Bottom Caption Bar -->
    <div class="text-center py-2 z-50">
        <p id="lightbox-caption" class="serif text-base sm:text-lg font-bold text-white max-w-2xl mx-auto"></p>
        <p class="text-[11px] text-white/40 mt-1">Use Left / Right arrow keys to navigate &middot; Esc to close</p>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    /* =====================================================================
       HERO IMAGE CAROUSEL ENGINE (Rock-Solid Self-Contained)
       ===================================================================== */
    let currentHero = 0;
    let heroTimer = null;
    let isHeroInitialized = false;

    function getHeroSlides() {
        return document.querySelectorAll('#hero-slider .hero-slide-item');
    }

    function getHeroDots() {
        return document.querySelectorAll('#hero-slider .hero-dot-btn');
    }

    function showHero(index) {
        const slides = getHeroSlides();
        const dots = getHeroDots();
        const total = slides.length;
        if (total === 0) return;

        currentHero = (index + total) % total;

        slides.forEach((slide, idx) => {
            if (idx === currentHero) {
                slide.classList.remove('inactive');
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
                slide.classList.add('inactive');
            }
        });

        dots.forEach((dot, idx) => {
            if (idx === currentHero) {
                dot.classList.remove('inactive');
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
                dot.classList.add('inactive');
            }
        });
    }

    window.nextHeroSlide = function() {
        showHero(currentHero + 1);
        resetHeroTimer();
    };

    window.prevHeroSlide = function() {
        showHero(currentHero - 1);
        resetHeroTimer();
    };

    window.goToHeroSlide = function(idx) {
        showHero(idx);
        resetHeroTimer();
    };

    function startHeroTimer() {
        const slides = getHeroSlides();
        if (slides.length > 1 && !heroTimer) {
            heroTimer = setInterval(() => {
                showHero(currentHero + 1);
            }, 5500);
        }
    }

    function stopHeroTimer() {
        if (heroTimer) {
            clearInterval(heroTimer);
            heroTimer = null;
        }
    }

    function resetHeroTimer() {
        stopHeroTimer();
        startHeroTimer();
    }

    function initHeroCarousel() {
        if (isHeroInitialized) return;
        const sliderEl = document.getElementById('hero-slider');
        if (!sliderEl) return;
        isHeroInitialized = true;

        showHero(0);
        startHeroTimer();

        sliderEl.addEventListener('mouseenter', stopHeroTimer);
        sliderEl.addEventListener('mouseleave', startHeroTimer);

        // Mobile Touch Swipe
        let touchStartX = 0;
        sliderEl.addEventListener('touchstart', (e) => {
            if (e.changedTouches && e.changedTouches[0]) {
                touchStartX = e.changedTouches[0].screenX;
            }
            stopHeroTimer();
        }, { passive: true });

        sliderEl.addEventListener('touchend', (e) => {
            if (e.changedTouches && e.changedTouches[0]) {
                const diff = touchStartX - e.changedTouches[0].screenX;
                if (diff > 40) window.nextHeroSlide();
                else if (diff < -40) window.prevHeroSlide();
            }
            startHeroTimer();
        }, { passive: true });
    }

    /* =====================================================================
       COTTAGES CAROUSEL HORIZONTAL TRACK
       ===================================================================== */
    window.scrollCottagesTrack = function(direction) {
        const cottagesTrack = document.getElementById('cottages-track');
        if (!cottagesTrack) return;
        const cardWidth = 360;
        const scrollAmount = direction === 'left' ? -cardWidth : cardWidth;
        cottagesTrack.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    };

    /* =====================================================================
       BRANCH COTTAGES DYNAMIC FILTER ENGINE
       ===================================================================== */
    window.filterCottagesByBranch = function(branchId) {
        const targetBranch = String(branchId);
        const cards = document.querySelectorAll('.cottage-card');
        const emptyState = document.getElementById('cottages-empty-state');
        const viewAllLink = document.getElementById('cottages-view-all-link');
        const pills = document.querySelectorAll('.cottage-branch-pill');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardBranch = card.getAttribute('data-branch-id');
            if (targetBranch === 'all' || cardBranch === targetBranch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        const cottagesTrack = document.getElementById('cottages-track');
        if (cottagesTrack) {
            cottagesTrack.scrollTo({ left: 0, behavior: 'smooth' });
        }

        pills.forEach(pill => {
            const pillId = pill.id;
            const isMatch = (targetBranch === 'all' && pillId === 'branch-pill-all') || 
                            (pillId === 'branch-pill-' + targetBranch);
            if (isMatch) {
                pill.className = 'cottage-branch-pill px-4 py-2 rounded-full text-xs font-bold transition shadow-xs bg-forest text-paper cursor-pointer shrink-0';
            } else {
                pill.className = 'cottage-branch-pill px-4 py-2 rounded-full text-xs font-semibold transition bg-white text-forest/70 hover:text-forest hover:bg-forest/5 border border-forest/15 cursor-pointer shrink-0';
            }
        });

        if (viewAllLink) {
            if (targetBranch === 'all') {
                viewAllLink.href = '{{ route('rooms.index') }}';
            } else {
                viewAllLink.href = '{{ route('rooms.index') }}?branch_id=' + targetBranch;
            }
        }
    };

    /* =====================================================================
       GALLERY ALBUMS & FULLSCREEN LIGHTBOX VIEWER
       ===================================================================== */
    const homeAlbums = {!! json_encode($galleryAlbumsList->map(function($a) {
        return [
            'id' => $a->id,
            'name' => $a->name ?: 'Cottage Album',
            'branch' => $a->branch ? $a->branch->name : 'All Retreats',
            'category' => ucfirst($a->category ?: 'Sanctuary'),
            'description' => $a->description ?: 'Visual moments captured at Krishna Cottages.',
            'cover_url' => $a->cover_image_url ?: ($a->images->first()->image_url ?? 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80'),
            'images' => $a->images->values()->map(function($img) {
                return [
                    'id' => $img->id,
                    'url' => $img->image_url,
                    'title' => $img->title ?: 'Photograph'
                ];
            })
        ];
    })->values()) !!};

    let currentLightboxList = [];
    let currentLightboxIndex = 0;

    window.openAlbumViewer = function(albumId) {
        const album = homeAlbums.find(a => a.id === Number(albumId));
        if (!album) return;

        const branchEl = document.getElementById('viewer-branch');
        const catEl = document.getElementById('viewer-category');
        const countEl = document.getElementById('viewer-count');
        const titleEl = document.getElementById('viewer-title');
        const descEl = document.getElementById('viewer-desc');

        if (branchEl) branchEl.textContent = album.branch;
        if (catEl) catEl.textContent = album.category;
        if (countEl) countEl.textContent = `${album.images.length} Photos`;
        if (titleEl) titleEl.textContent = album.name;
        if (descEl) descEl.textContent = album.description;

        const grid = document.getElementById('viewer-photos-grid');
        const emptyEl = document.getElementById('viewer-empty-state');
        if (grid) {
            grid.innerHTML = '';
            if (album.images.length === 0) {
                if (emptyEl) emptyEl.classList.remove('hidden');
            } else {
                if (emptyEl) emptyEl.classList.add('hidden');
                album.images.forEach((img, idx) => {
                    const item = document.createElement('div');
                    item.className = 'group relative rounded-2xl overflow-hidden bg-forest/5 shadow-xs cursor-pointer h-48 sm:h-52 transition hover:shadow-lg border border-forest/10';
                    item.onclick = function() { window.openLightboxForAlbum(albumId, idx); };
                    item.innerHTML = `
                        <img src="${img.url}" alt="${img.title}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=600&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-3.5 text-white">
                            <span class="text-xs font-bold leading-tight line-clamp-1">${img.title}</span>
                            <span class="text-[10px] text-brass mt-0.5 flex items-center gap-1"><i data-lucide="zoom-in" class="w-3 h-3"></i> Full Resolution</span>
                        </div>
                    `;
                    grid.appendChild(item);
                });
            }
        }

        const modal = document.getElementById('album-viewer-modal');
        if (modal) modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    };

    window.closeAlbumViewer = function() {
        const modal = document.getElementById('album-viewer-modal');
        if (modal) modal.classList.add('hidden');
        const lbModal = document.getElementById('lightbox-modal');
        if (!lbModal || lbModal.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }
    };

    window.openLightboxForAlbum = function(albumId, photoIndex) {
        const album = homeAlbums.find(a => a.id === Number(albumId));
        if (!album || !album.images.length) return;

        currentLightboxList = album.images;
        currentLightboxIndex = photoIndex;
        const tagEl = document.getElementById('lightbox-album-tag');
        if (tagEl) tagEl.textContent = `${album.name} · ${album.branch}`;

        renderLightboxCurrent();
        const lbModal = document.getElementById('lightbox-modal');
        if (lbModal) lbModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    };

    window.openLightbox = function(url, caption) {
        currentLightboxList = [{ url: url, title: caption || 'Cottage Photograph' }];
        currentLightboxIndex = 0;
        const tagEl = document.getElementById('lightbox-album-tag');
        if (tagEl) tagEl.textContent = '';
        renderLightboxCurrent();
        const lbModal = document.getElementById('lightbox-modal');
        if (lbModal) lbModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    };

    function renderLightboxCurrent() {
        if (!currentLightboxList.length) return;
        const photo = currentLightboxList[currentLightboxIndex];
        const imgEl = document.getElementById('lightbox-img');
        const capEl = document.getElementById('lightbox-caption');
        const counterEl = document.getElementById('lightbox-counter');
        const prevBtn = document.getElementById('lightbox-prev-btn');
        const nextBtn = document.getElementById('lightbox-next-btn');

        if (imgEl) {
            imgEl.src = photo.url;
            imgEl.alt = photo.title || 'Cottage Photograph';
        }
        if (capEl) capEl.textContent = photo.title || '';
        if (counterEl) counterEl.textContent = `${currentLightboxIndex + 1} / ${currentLightboxList.length}`;

        if (currentLightboxList.length <= 1) {
            if (prevBtn) prevBtn.classList.add('hidden');
            if (nextBtn) nextBtn.classList.add('hidden');
        } else {
            if (prevBtn) prevBtn.classList.remove('hidden');
            if (nextBtn) nextBtn.classList.remove('hidden');
        }
    }

    window.lightboxPrev = function(e) {
        if (e) e.stopPropagation();
        if (currentLightboxList.length <= 1) return;
        currentLightboxIndex = (currentLightboxIndex - 1 + currentLightboxList.length) % currentLightboxList.length;
        renderLightboxCurrent();
    };

    window.lightboxNext = function(e) {
        if (e) e.stopPropagation();
        if (currentLightboxList.length <= 1) return;
        currentLightboxIndex = (currentLightboxIndex + 1) % currentLightboxList.length;
        renderLightboxCurrent();
    };

    window.closeLightbox = function() {
        const lbModal = document.getElementById('lightbox-modal');
        if (lbModal) lbModal.classList.add('hidden');
        const avModal = document.getElementById('album-viewer-modal');
        if (!avModal || avModal.classList.contains('hidden')) {
            document.body.style.overflow = '';
        }
    };

    /* =====================================================================
       TESTIMONIALS QUOTES SLIDER
       ===================================================================== */
    let currentTestimonial = 0;
    let testTimer = null;

    function getTestDots() {
        return document.querySelectorAll('.testimonial-dot');
    }

    function showTestimonial(idx) {
        const testTrack = document.getElementById('testimonial-track');
        const testDots = getTestDots();
        const totalTestimonials = {{ count($reviewsList) }};
        if (totalTestimonials === 0 || !testTrack) return;

        currentTestimonial = (idx + totalTestimonials) % totalTestimonials;
        testTrack.style.transform = `translateX(-${currentTestimonial * 100}%)`;

        testDots.forEach((d, i) => {
            d.className = i === currentTestimonial 
                ? 'testimonial-dot h-1.5 rounded-full transition-all duration-300 w-5 bg-forest' 
                : 'testimonial-dot h-1.5 rounded-full transition-all duration-300 w-1.5 bg-forest/20';
        });
    }

    window.nextTestimonial = function() {
        showTestimonial(currentTestimonial + 1);
        resetTestTimer();
    };

    window.prevTestimonial = function() {
        showTestimonial(currentTestimonial - 1);
        resetTestTimer();
    };

    window.goToTestimonial = function(idx) {
        showTestimonial(idx);
        resetTestTimer();
    };

    function startTestTimer() {
        const totalTestimonials = {{ count($reviewsList) }};
        if (totalTestimonials > 1 && !testTimer) {
            testTimer = setInterval(() => {
                showTestimonial(currentTestimonial + 1);
            }, 6000);
        }
    }

    function stopTestTimer() {
        if (testTimer) {
            clearInterval(testTimer);
            testTimer = null;
        }
    }

    function resetTestTimer() {
        stopTestTimer();
        startTestTimer();
    }

    /* =====================================================================
       BOOKING DATE SYNCHRONIZATION
       ===================================================================== */
    window.updateCheckOutMin = function() {
        const checkInInput = document.getElementById('home-check-in');
        const checkOutInput = document.getElementById('home-check-out');
        if (!checkInInput || !checkOutInput) return;

        const checkInDate = new Date(checkInInput.value);
        if (!isNaN(checkInDate.getTime())) {
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            const minStr = nextDay.toISOString().split('T')[0];
            checkOutInput.min = minStr;

            if (new Date(checkOutInput.value) <= checkInDate) {
                const defaultEnd = new Date(checkInDate);
                defaultEnd.setDate(defaultEnd.getDate() + 2);
                checkOutInput.value = defaultEnd.toISOString().split('T')[0];
            }
        }
    };

    function initPageFeatures() {
        initHeroCarousel();

        const testContainer = document.getElementById('testimonials-carousel');
        if (testContainer) {
            testContainer.addEventListener('mouseenter', stopTestTimer);
            testContainer.addEventListener('mouseleave', startTestTimer);
            startTestTimer();
        }

        // Modal backdrop click listeners
        const avModal = document.getElementById('album-viewer-modal');
        if (avModal) {
            avModal.addEventListener('click', function(e) {
                if (e.target.id === 'album-viewer-modal') window.closeAlbumViewer();
            });
        }

        const lbModal = document.getElementById('lightbox-modal');
        if (lbModal) {
            lbModal.addEventListener('click', function(e) {
                if (e.target.id === 'lightbox-modal') window.closeLightbox();
            });
        }

        // Global keydown listeners for escape and arrows
        document.addEventListener('keydown', function(e) {
            const lb = document.getElementById('lightbox-modal');
            const av = document.getElementById('album-viewer-modal');

            if (lb && !lb.classList.contains('hidden')) {
                if (e.key === 'Escape') {
                    window.closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    window.lightboxPrev();
                } else if (e.key === 'ArrowRight') {
                    window.lightboxNext();
                }
            } else if (av && !av.classList.contains('hidden')) {
                if (e.key === 'Escape') {
                    window.closeAlbumViewer();
                }
            }
        });

        // Periodic Lucide icon check in case Lucide loaded after scripts
        let iconTries = 0;
        const iconInterval = setInterval(() => {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
                iconTries++;
                if (iconTries > 3) clearInterval(iconInterval);
            }
        }, 300);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPageFeatures);
    } else {
        initPageFeatures();
    }
})();
</script>
@endpush
