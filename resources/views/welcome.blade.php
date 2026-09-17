@extends('layouts.customer')

@php
    $hContent = $homepageContent ?? app(\App\Http\Controllers\Admin\AdminController::class)->getHomepageContent();
    $branchesList = $branches ?? \App\Models\Branch::where('status', 'active')->orderBy('sort_order')->get();
    
    // Featured Cottage Rooms for the Accommodations Carousel
    $roomsList = $featuredRooms ?? \App\Models\RoomType::with(['branch', 'category', 'amenitiesList'])
        ->where('is_active', true)
        ->where('is_bookable', true)
        ->orderBy('sort_order')
        ->take(8)
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
                'image' => $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1400&q=85'),
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

    // Curated Gallery Images
    $galleryPhotos = [];
    if (isset($galleryAlbums) && $galleryAlbums->isNotEmpty()) {
        foreach ($galleryAlbums as $album) {
            if ($album->images && $album->images->isNotEmpty()) {
                foreach ($album->images->take(2) as $img) {
                    $galleryPhotos[] = [
                        'url' => $img->image_url,
                        'caption' => $album->name,
                        'branch' => $album->branch?->city ?? 'Kerala'
                    ];
                }
            }
        }
    }
    if (empty($galleryPhotos)) {
        $galleryPhotos = [
            ['url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=85', 'caption' => 'Private Wooden Cottage', 'branch' => 'Rajakkad'],
            ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=85', 'caption' => 'Misty Morning Tea Estate', 'branch' => 'Munnar'],
            ['url' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=800&q=85', 'caption' => 'Clay-Pot Heritage Dining', 'branch' => 'Plantation Kitchen'],
            ['url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=800&q=85', 'caption' => 'Verandah Living & Nature', 'branch' => 'Idukki'],
            ['url' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=800&q=85', 'caption' => 'Estate Harvest Spices', 'branch' => 'Cardamom Hills'],
            ['url' => 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?auto=format&fit=crop&w=800&q=85', 'caption' => 'Tranquil Sunset Trails', 'branch' => 'Rajakkad'],
        ];
    }
    $galleryPhotos = array_slice($galleryPhotos, 0, 6);
@endphp

@section('title', 'Krishna Cottages — Best Luxury Cottages & Homestay in Idukki, Rajakkadu & Munnar, Kerala')
@section('meta_description', 'Discover Krishna Cottages in Rajakkad, Idukki. Book handcrafted wooden cottages, authentic plantation dining, and nature homestays near Munnar with cardamom plantation views.')

@section('content')

<!-- =========================================================================
     1. HERO SECTION: CLASSIC HOTEL IMAGE CAROUSEL & BOOKING DOCK
     ========================================================================= -->
<section class="relative w-full pt-2 sm:pt-4 pb-8 lg:pb-12 overflow-hidden">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <!-- Large-Format Framed Carousel Canvas -->
        <div id="hero-slider" class="relative w-full h-[460px] sm:h-[520px] lg:h-[580px] rounded-3xl lg:rounded-[36px] overflow-hidden shadow-2xl bg-forest group select-none">
            
            <!-- Slides Track -->
            <div id="hero-slides-track" class="relative w-full h-full">
                @foreach($heroSlidesList as $sIdx => $slide)
                    <div class="hero-slide-item absolute inset-0 transition-all duration-700 ease-in-out {{ $sIdx === 0 ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 pointer-events-none z-0' }}" data-index="{{ $sIdx }}">
                        <!-- Background High-Res Image -->
                        <img src="{{ $slide['image'] }}" 
                             alt="{{ $slide['title'] }} — Krishna Cottages Kerala" 
                             class="w-full h-full object-cover brightness-[0.82] transition-transform duration-1000 ease-out" 
                             {{ $sIdx === 0 ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"' }} />
                        
                        <!-- Rich Dark Ambient Gradients for Typography Contrast -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-black/25"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-transparent to-black/40"></div>

                        <!-- Top Floating Destination Tag -->
                        <div class="absolute top-5 left-5 sm:top-7 sm:left-7 z-20 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/40 backdrop-blur-md text-white text-[11px] font-semibold tracking-wider uppercase border border-white/20 shadow-xs">
                                <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i>
                                <span>{{ $slide['tag'] ?? 'Krishna Cottages' }}</span>
                            </span>
                            @if(!empty($slide['badge']))
                                <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-brass/25 backdrop-blur-md text-brass text-[10px] font-bold border border-brass/30">
                                    {{ $slide['badge'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Slide Counter Indicator -->
                        <div class="absolute top-5 right-5 sm:top-7 sm:right-7 z-20 text-white/70 bg-black/40 backdrop-blur-md px-3 py-1 rounded-full text-xs font-mono border border-white/15">
                            <span class="text-white font-bold">0{{ $sIdx + 1 }}</span> / 0{{ count($heroSlidesList) }}
                        </div>

                        <!-- Centered Main Typography & Hero CTAs -->
                        <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-6 max-w-4xl mx-auto text-white">
                            <!-- Eyebrow -->
                            <p class="eyebrow text-brass/90 text-xs sm:text-sm tracking-[.25em] mb-2 sm:mb-3 drop-shadow-xs">
                                {{ $hContent['hero_eyebrow'] ?? 'PEACEFUL NATURE RETREAT · KERALA' }}
                            </p>

                            <!-- Master Headline -->
                            <h1 class="serif text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.08] text-white drop-shadow-md max-w-3xl">
                                {!! $hContent['hero_heading_1'] ?? 'Come away to' !!}
                                <span class="italic font-normal text-[#E3C789]">{!! $hContent['hero_heading_2'] ?? 'somewhere better.' !!}</span>
                            </h1>

                            <!-- Description -->
                            <p class="mt-3 sm:mt-4 text-xs sm:text-sm lg:text-base text-white/85 max-w-2xl leading-relaxed drop-shadow-xs font-normal">
                                {{ $hContent['hero_description'] ?? 'Stay slow. Eat well. Explore more. Handcrafted wooden cottages, authentic clay-pot plantation dining, and fresh estate spices.' }}
                            </p>

                            <!-- Hero Buttons -->
                            <div class="mt-6 sm:mt-7 flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-forest hover:bg-emerald text-paper font-bold text-xs sm:text-sm shadow-xl hover:shadow-2xl hover:scale-105 transition duration-300 border border-emerald-400/30">
                                    <span>Explore Cottages</span>
                                    <i data-lucide="arrow-up-right" class="w-4 h-4 text-brass"></i>
                                </a>
                                <button type="button" onclick="openChat()" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-semibold text-xs sm:text-sm border border-white/30 transition duration-300 cursor-pointer">
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
                    class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-30 grid h-10 w-10 sm:h-12 sm:w-12 place-items-center rounded-full bg-white/25 hover:bg-white text-white hover:text-forest backdrop-blur-md border border-white/30 transition-all duration-300 shadow-md cursor-pointer hover:scale-105" 
                    aria-label="Previous Slide">
                <i data-lucide="chevron-left" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </button>
            <button type="button" 
                    onclick="nextHeroSlide()" 
                    class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-30 grid h-10 w-10 sm:h-12 sm:w-12 place-items-center rounded-full bg-white/25 hover:bg-white text-white hover:text-forest backdrop-blur-md border border-white/30 transition-all duration-300 shadow-md cursor-pointer hover:scale-105" 
                    aria-label="Next Slide">
                <i data-lucide="chevron-right" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </button>

            <!-- Bottom Indicator Dots -->
            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-30 flex items-center gap-2 bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/15">
                @foreach($heroSlidesList as $dIdx => $slide)
                    <button type="button" 
                            onclick="goToHeroSlide({{ $dIdx }})" 
                            class="hero-dot h-2 rounded-full transition-all duration-300 cursor-pointer {{ $dIdx === 0 ? 'w-6 bg-brass' : 'w-2 bg-white/40 hover:bg-white/80' }}" 
                            aria-label="Slide {{ $dIdx + 1 }}"></button>
                @endforeach
            </div>

        </div>

        <!-- =====================================================================
             CLASSIC HOTEL BOOKING ENGINE SEARCH BAR (Drives directly to /stay)
             ===================================================================== -->
        <div class="relative -mt-6 sm:-mt-8 z-40 max-w-5xl mx-auto">
            <form action="{{ route('rooms.index') }}" method="GET" class="w-full bg-white text-forest rounded-2xl lg:rounded-full p-3 lg:p-4 shadow-[0_16px_40px_rgba(6,63,52,0.12)] border border-forest/15">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 lg:gap-2 items-center">
                    
                    <!-- Field 1: Destination / Branch (lg:col-span-4) -->
                    <div class="lg:col-span-4 px-3 py-1.5 lg:px-4 bg-paper/60 rounded-xl lg:rounded-l-full border border-forest/10 hover:border-forest/30 transition">
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

                    <!-- Field 2: Check-In (lg:col-span-2) -->
                    <div class="lg:col-span-2 px-3 py-1.5 bg-paper/60 rounded-xl border border-forest/10 hover:border-forest/30 transition">
                        <label class="block text-[9px] uppercase tracking-wider font-bold text-forest/50 flex items-center gap-1.5 mb-0.5">
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

                    <!-- Field 3: Check-Out (lg:col-span-2) -->
                    <div class="lg:col-span-2 px-3 py-1.5 bg-paper/60 rounded-xl border border-forest/10 hover:border-forest/30 transition">
                        <label class="block text-[9px] uppercase tracking-wider font-bold text-forest/50 flex items-center gap-1.5 mb-0.5">
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

                    <!-- Field 4: Guests (lg:col-span-2) -->
                    <div class="lg:col-span-2 px-3 py-1.5 bg-paper/60 rounded-xl border border-forest/10 hover:border-forest/30 transition">
                        <label class="block text-[9px] uppercase tracking-wider font-bold text-forest/50 flex items-center gap-1.5 mb-0.5">
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

                    <!-- Field 5: Submit Action (lg:col-span-2) -->
                    <div class="lg:col-span-2 pt-1 sm:pt-0">
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl lg:rounded-r-full bg-brass hover:brightness-105 text-forest font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition cursor-pointer">
                            <i data-lucide="search" class="w-4 h-4 shrink-0"></i>
                            <span class="whitespace-nowrap">Check Stays</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</section>


<!-- =========================================================================
     2. WELCOME & PHILOSOPHY: CLEAN STORY + 4 VALUE BADGES
     ========================================================================= -->
<section class="py-12 sm:py-16 bg-[#FAF7F0]">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <!-- Section Heading & Narrative -->
        <div class="max-w-3xl mx-auto text-center space-y-3">
            <span class="eyebrow text-emerald font-bold">WELCOME TO KRISHNA COTTAGES</span>
            <h2 class="serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest tracking-tight">
                A Sanctuary of Slow Living &amp; Serenity
            </h2>
            <div class="w-16 h-0.5 bg-brass mx-auto my-3"></div>
            <p class="text-sm sm:text-base text-forest/75 leading-relaxed font-normal">
                Tucked into the misty slopes of Rajakkad, Idukki, and nearby Munnar, Krishna Cottages provides handcrafted private wooden residences surrounded by aromatic tea plantations, cardamoms, and cool mountain air.
            </p>
        </div>

        <!-- 4 Clean Hotel Highlight Cards -->
        <div class="mt-10 sm:mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
            
            <!-- Highlight 1 -->
            <div class="p-6 rounded-2xl bg-white border border-forest/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-300 space-y-3 text-center sm:text-left">
                <div class="w-12 h-12 rounded-xl bg-forest/5 flex items-center justify-center text-emerald mx-auto sm:mx-0">
                    <i data-lucide="home" class="w-6 h-6"></i>
                </div>
                <h3 class="serif text-lg font-bold text-forest">Wooden Cottages</h3>
                <p class="text-xs text-forest/70 leading-relaxed">
                    Handcrafted teak verandas, private sit-outs, and panoramic valley views designed for deep rest.
                </p>
            </div>

            <!-- Highlight 2 -->
            <div class="p-6 rounded-2xl bg-white border border-forest/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-300 space-y-3 text-center sm:text-left">
                <div class="w-12 h-12 rounded-xl bg-forest/5 flex items-center justify-center text-emerald mx-auto sm:mx-0">
                    <i data-lucide="utensils" class="w-6 h-6"></i>
                </div>
                <h3 class="serif text-lg font-bold text-forest">Clay-Pot Dining</h3>
                <p class="text-xs text-forest/70 leading-relaxed">
                    Authentic Kerala culinary recipes prepared in earthen pots with garden-fresh organic ingredients.
                </p>
            </div>

            <!-- Highlight 3 -->
            <div class="p-6 rounded-2xl bg-white border border-forest/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-300 space-y-3 text-center sm:text-left">
                <div class="w-12 h-12 rounded-xl bg-forest/5 flex items-center justify-center text-emerald mx-auto sm:mx-0">
                    <i data-lucide="sparkles" class="w-6 h-6"></i>
                </div>
                <h3 class="serif text-lg font-bold text-forest">Estate Spices</h3>
                <p class="text-xs text-forest/70 leading-relaxed">
                    Cardamom, black pepper, and clove trails. Hand-harvested spices available directly from our farm.
                </p>
            </div>

            <!-- Highlight 4 -->
            <div class="p-6 rounded-2xl bg-white border border-forest/10 shadow-xs hover:shadow-md hover:-translate-y-1 transition duration-300 space-y-3 text-center sm:text-left">
                <div class="w-12 h-12 rounded-xl bg-forest/5 flex items-center justify-center text-emerald mx-auto sm:mx-0">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <h3 class="serif text-lg font-bold text-forest">Peaceful Sanctuary</h3>
                <p class="text-xs text-forest/70 leading-relaxed">
                    Strictly quiet and undisturbed nature. No loud pool parties — pure stillness, mist, and serenity.
                </p>
            </div>

        </div>

    </div>
</section>


<!-- =========================================================================
     3. FEATURED COTTAGES CAROUSEL (ACCOMMODATIONS SLIDER)
     ========================================================================= -->
<section id="cottages" class="py-12 sm:py-16 bg-[#F4F1E8]">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <!-- Section Header with Prev/Next Controls -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
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
                <a href="{{ route('rooms.index') }}" class="ml-2 hidden sm:inline-flex items-center gap-1 text-xs font-bold text-emerald hover:text-forest underline transition">
                    View All
                </a>
            </div>
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
                <div class="snap-start shrink-0 w-[290px] sm:w-[340px] lg:w-[380px] rounded-2xl sm:rounded-3xl bg-white border border-forest/10 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                    
                    <div>
                        <!-- Cottage Photo -->
                        <div class="relative h-52 sm:h-60 w-full overflow-hidden bg-forest/5">
                            <a href="{{ route('rooms.show', $room->slug) }}" class="block w-full h-full">
                                <img src="{{ $roomCover }}" 
                                     alt="{{ $room->name }} — Krishna Cottages" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                                     loading="lazy" />
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
     5. PHOTO GALLERY: CLEAN 6-IMAGE GRID WITH LIGHTBOX PREVIEW
     ========================================================================= -->
<section class="py-12 sm:py-16 bg-[#F4F1E8]">
    <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div class="space-y-1">
                <span class="eyebrow text-emerald font-bold">VISUAL GALLERY</span>
                <h2 class="serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest tracking-tight">
                    Moments in the Mist
                </h2>
                <p class="text-xs sm:text-sm text-forest/70">
                    Click any photograph to view in full resolution.
                </p>
            </div>
            <a href="{{ route('gallery.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-forest text-paper text-xs font-bold hover:bg-emerald transition shadow-xs">
                <span>View Full Gallery</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
            </a>
        </div>

        <!-- 6-Photo Clean Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3.5 sm:gap-5">
            @foreach($galleryPhotos as $photo)
                <div onclick="openLightbox('{{ $photo['url'] }}')" 
                     class="group relative h-44 sm:h-56 lg:h-64 rounded-2xl overflow-hidden shadow-xs hover:shadow-xl transition duration-500 cursor-pointer bg-forest/5">
                    <img src="{{ $photo['url'] }}" 
                         alt="{{ $photo['caption'] }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                         loading="lazy" />
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/35 transition duration-300 flex items-center justify-center">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-white/90 text-forest opacity-0 group-hover:opacity-100 transition duration-300 shadow-md">
                            <i data-lucide="maximize-2" class="w-4 h-4"></i>
                        </span>
                    </div>
                    <div class="absolute bottom-2 left-2 right-2 sm:bottom-3 sm:left-3 sm:right-3 px-2.5 py-1 rounded-lg bg-black/40 backdrop-blur-md text-white text-[10px] font-medium truncate opacity-90">
                        {{ $photo['caption'] }} &middot; {{ $photo['branch'] }}
                    </div>
                </div>
            @endforeach
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

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();

        /* =====================================================================
           HERO IMAGE CAROUSEL ENGINE
           ===================================================================== */
        let currentHero = 0;
        const heroSlides = document.querySelectorAll('.hero-slide-item');
        const heroDots = document.querySelectorAll('.hero-dot');
        const totalHero = heroSlides.length;
        let heroTimer = null;

        function showHero(index) {
            if (totalHero === 0) return;
            currentHero = (index + totalHero) % totalHero;

            heroSlides.forEach((slide, idx) => {
                if (idx === currentHero) {
                    slide.classList.remove('opacity-0', 'scale-105', 'pointer-events-none', 'z-0');
                    slide.classList.add('opacity-100', 'scale-100', 'z-10');
                } else {
                    slide.classList.remove('opacity-100', 'scale-100', 'z-10');
                    slide.classList.add('opacity-0', 'scale-105', 'pointer-events-none', 'z-0');
                }
            });

            heroDots.forEach((dot, idx) => {
                if (idx === currentHero) {
                    dot.className = 'hero-dot h-2 rounded-full transition-all duration-300 cursor-pointer w-6 bg-brass';
                } else {
                    dot.className = 'hero-dot h-2 rounded-full transition-all duration-300 cursor-pointer w-2 bg-white/40 hover:bg-white/80';
                }
            });
        }

        window.nextHeroSlide = function() {
            showHero(currentHero + 1);
        };

        window.prevHeroSlide = function() {
            showHero(currentHero - 1);
        };

        window.goToHeroSlide = function(idx) {
            showHero(idx);
            resetHeroTimer();
        };

        function startHeroTimer() {
            if (totalHero > 1 && !heroTimer) {
                heroTimer = setInterval(window.nextHeroSlide, 5500);
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

        const sliderEl = document.getElementById('hero-slider');
        if (sliderEl) {
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
                    if (diff > 45) window.nextHeroSlide();
                    else if (diff < -45) window.prevHeroSlide();
                }
                startHeroTimer();
            }, { passive: true });
        }

        startHeroTimer();

        /* =====================================================================
           COTTAGES CAROUSEL HORIZONTAL TRACK
           ===================================================================== */
        const cottagesTrack = document.getElementById('cottages-track');
        window.scrollCottagesTrack = function(direction) {
            if (!cottagesTrack) return;
            const cardWidth = 360;
            const scrollAmount = direction === 'left' ? -cardWidth : cardWidth;
            cottagesTrack.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        };

        /* =====================================================================
           TESTIMONIALS QUOTES SLIDER
           ===================================================================== */
        let currentTestimonial = 0;
        const testTrack = document.getElementById('testimonial-track');
        const testDots = document.querySelectorAll('.testimonial-dot');
        const totalTestimonials = {{ count($reviewsList) }};
        let testTimer = null;

        function showTestimonial(idx) {
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
        };

        window.prevTestimonial = function() {
            showTestimonial(currentTestimonial - 1);
        };

        window.goToTestimonial = function(idx) {
            showTestimonial(idx);
            resetTestTimer();
        };

        function startTestTimer() {
            if (totalTestimonials > 1 && !testTimer) {
                testTimer = setInterval(window.nextTestimonial, 6000);
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

        const testContainer = document.getElementById('testimonials-carousel');
        if (testContainer) {
            testContainer.addEventListener('mouseenter', stopTestTimer);
            testContainer.addEventListener('mouseleave', startTestTimer);
        }

        startTestTimer();

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
    });
</script>
@endpush
