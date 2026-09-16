@php
  if (!isset($homepageContent)) {
      $homepageContent = \App\Models\Setting::get('homepage_content') ?? [];
  }
  if (!isset($spiceProducts)) {
      $spiceProducts = \App\Models\SpiceProduct::where('status', 'active')->get();
  }
  if (!isset($branches)) {
      $branches = \App\Models\Branch::where('status', 'active')->orderBy('sort_order')->get();
  }
  if (!isset($branchesWeather)) {
      $branchesWeather = app(\App\Services\WeatherService::class)->getAllBranchesWeather($branches);
  }
  $pinnedSp1 = $spiceProducts->firstWhere('id', $homepageContent['spices_pinned_product_1_id'] ?? 1) ?? $spiceProducts->first();
  $pinnedSp2 = $spiceProducts->firstWhere('id', $homepageContent['spices_pinned_product_2_id'] ?? 2) ?? $spiceProducts->skip(1)->first() ?? $spiceProducts->first();

  $heroSlides = $homepageContent['hero_slides'] ?? null;
  if (empty($heroSlides) || !is_array($heroSlides)) {
      $heroSlides = [];
      foreach ($branches as $idx => $b) {
          $heroSlides[] = [
              'id' => $b->id,
              'title' => $b->display_name ?: $b->name,
              'subtitle' => $b->city ? ($b->city . ' Retreat') : 'Kerala Retreat',
              'description' => $b->tagline ?: 'Private wooden cottages, lush gardens & tranquil verandas.',
              'image' => $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1400&q=85'),
              'tag' => ($b->city ?: 'Kerala') . ' · ' . ($idx === 0 ? 'Hillside Cottages' : ($idx === 1 ? 'Backwater Haven' : 'Coastal Living')),
              'badge' => '★ 4.9 Rating',
              'link' => route('rooms.index', ['branch_id' => $b->id]),
              'sort_order' => $idx + 1,
              'status' => 'active',
          ];
      }
  }
  $heroSlides = array_values(array_filter($heroSlides, fn($s) => ($s['status'] ?? 'active') === 'active'));
  usort($heroSlides, fn($a, $b) => ($a['sort_order'] ?? 1) <=> ($b['sort_order'] ?? 1));
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#F4F1E8" />
  
  <!-- PRIMARY HIGH-RANKING SEO METADATA -->
  <title>Krishna Cottages — Best Luxury Cottages &amp; Homestay in Idukki, Rajakkadu, Rajakumari, Adimali &amp; Munnar, Kerala</title>
  <meta name="description" content="Discover Krishna Cottages in Rajakkad, Idukki. Book luxury wooden cottages, nature homestays &amp; private hillside villas across Rajakkadu, Rajakumari, Adimali &amp; Munnar, Kerala with organic dining &amp; plantation views." />
  <meta name="keywords" content="krishna cottage idukki, krishna cottages rajakkad, krishna resort idukki, krishna homestay idukki, idukki cottages, idukki resorts, resorts in idukki, rajakkadu, resort in rajakkadu, home stay in idukki, home stay in rajakkadu, home stay in rajakumari, resort in rajakumari, resort in adimali, cottage in adimali, home stay in adimali, idukki, kerala, best resort in idukki, best cottage in idukki, best cottage in munnar, munnar resorts, munnar homestay, cottages in rajakkad idukki, kuthumkal waterfalls resort, mailadumpara cottages idukki, ponmudi lake resort rajakkad, adivaram idukki homestay, wooden cottages in idukki, plantation homestay in rajakkadu, budget homestay in rajakumari, family resort in adimali idukki, honeymoon cottage in munnar idukki, kerala eco cottage idukki, ayurvedic resort in idukki, best resort in rajakkadu, best homestay in idukki, resorts near ponmudi dam" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <meta name="author" content="Krishna Cottages" />
  <link rel="canonical" href="{{ url()->current() }}" />

  <!-- GEO-LOCALIZATION TAGS (Rajakkad, Idukki, Kerala) -->
  <meta name="geo.region" content="IN-KL" />
  <meta name="geo.placename" content="Rajakkad, Idukki District, Kerala, India" />
  <meta name="geo.position" content="9.9784;77.0673" />
  <meta name="ICBM" content="9.9784, 77.0673" />

  <!-- OPEN GRAPH / FACEBOOK SOCIAL SHARING -->
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Krishna Cottages" />
  <meta property="og:url" content="{{ url()->current() }}" />
  <meta property="og:title" content="Krishna Cottages — Best Luxury Cottages &amp; Homestay in Idukki, Rajakkadu &amp; Munnar" />
  <meta property="og:description" content="Authentic wooden cottages, misty tea garden views, and peaceful hillside homestays in Rajakkad, Idukki, Kerala. Experience heritage living near Munnar." />
  <meta property="og:image" content="{{ $heroSlides[0]['image'] ?? 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85' }}" />
  <meta property="og:locale" content="en_IN" />

  <!-- TWITTER CARDS -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Krishna Cottages — Best Luxury Cottages &amp; Homestay in Idukki, Rajakkadu &amp; Munnar" />
  <meta name="twitter:description" content="Experience peaceful wooden cottages and estate living in Rajakkadu, Rajakumari, Adimali &amp; Munnar, Idukki, Kerala." />
  <meta name="twitter:image" content="{{ $heroSlides[0]['image'] ?? 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85' }}" />

  <!-- SPEED OPTIMIZATION & RESOURCE HINTS (Sub-3-Second Load Guarantee) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="dns-prefetch" href="https://unpkg.com" />
  <link rel="preconnect" href="https://images.unsplash.com" />
  @if(!empty($heroSlides[0]['image']))
    <link rel="preload" as="image" href="{{ $heroSlides[0]['image'] }}" fetchpriority="high" />
  @endif

  <!-- SCHEMA.ORG STRUCTURED DATA (JSON-LD) FOR GOOGLE FIRST-PAGE RANKING -->
  @php
    $resortSchema = [
      '@context' => 'https://schema.org',
      '@graph' => [
        [
          '@type' => ['LodgingBusiness', 'BedAndBreakfast'],
          '@id' => url('/') . '#cottages',
          'name' => 'Krishna Cottages',
          'alternateName' => ['Krishna Cottage Idukki', 'Krishna Cottages Rajakkad', 'Krishna Homestay Idukki'],
          'url' => url('/'),
          'telephone' => '+91 95676 02774',
          'priceRange' => '₹₹ - ₹₹₹',
          'description' => 'Premium botanical cottages and hillside homestays situated at Rajakkad, Kuthumkal, Mailadumpara Road, Adivaram, Idukki, Kerala. Offering private wooden villas, plantation dining, and authentic Kerala hospitality near Munnar, Rajakumari, and Adimali.',
          'image' => [
            $heroSlides[0]['image'] ?? 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85'
          ],
          'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Rajakkad, Kuthumkal, Mailadumpara Road, Adivaram',
            'addressLocality' => 'Rajakkad, Idukki',
            'addressRegion' => 'Kerala',
            'postalCode' => '685566',
            'addressCountry' => 'IN'
          ],
          'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 9.9784,
            'longitude' => 77.0673
          ],
          'starRating' => [
            '@type' => 'Rating',
            'ratingValue' => '4.9',
            'bestRating' => '5'
          ],
          'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.9',
            'reviewCount' => '128',
            'bestRating' => '5'
          ],
          'amenityFeature' => [
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Private Wooden Verandahs', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Clay-Pot Cuisine & Farm Dining', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Spice Plantation Walks', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Free High-Speed Wi-Fi', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => '24/7 Concierge & Front Desk', 'value' => true]
          ],
          'areaServed' => [
            ['@type' => 'AdministrativeArea', 'name' => 'Rajakkad, Idukki'],
            ['@type' => 'AdministrativeArea', 'name' => 'Rajakumari, Idukki'],
            ['@type' => 'AdministrativeArea', 'name' => 'Adimali, Idukki'],
            ['@type' => 'AdministrativeArea', 'name' => 'Munnar, Idukki'],
            ['@type' => 'AdministrativeArea', 'name' => 'Kerala']
          ]
        ],
        [
          '@type' => 'BreadcrumbList',
          'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Idukki Cottages', 'item' => route('rooms.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'Dining & Spices', 'item' => route('dining.index')]
          ]
        ]
      ]
    ];
  @endphp
  <script type="application/ld+json">
  {!! json_encode($resortSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
  </script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://unpkg.com/lucide@latest" defer></script>
  <style>
    :root { color-scheme: light; }
    * { box-sizing: border-box; }
    html, body { background: #F4F1E8; color: #17312A; }
    body { background-image: radial-gradient(circle at 20% 10%, rgba(201,168,106,.06), transparent 22%), radial-gradient(circle at 80% 40%, rgba(15,107,88,.04), transparent 26%); font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; overflow-x: hidden; }
    .serif { font-family: Georgia, "Times New Roman", serif; }
    .eyebrow { font-size: 10px; letter-spacing: .24em; text-transform: uppercase; font-weight: 700; }
    .hide-scrollbar::-webkit-scrollbar { display:none; }
    .hide-scrollbar { scrollbar-width:none; }
    .reveal { opacity:0; transform: translateY(18px); transition: opacity .8s cubic-bezier(.2,.7,.2,1), transform .8s cubic-bezier(.2,.7,.2,1); }
    .reveal.in { opacity:1; transform:none; }
    .d1{transition-delay:.08s}.d2{transition-delay:.16s}.d3{transition-delay:.24s}.d4{transition-delay:.32s}
    .img-zoom img { transition: transform .8s cubic-bezier(.2,.7,.2,1); }
    .img-zoom:hover img { transform: scale(1.035); }
    .soft-border { border: 1px solid rgba(23,49,42,.10); }
    .soft-divider { border-top: 1px solid rgba(23,49,42,.10); }
    .glass-dark { background: rgba(8,63,52,.72); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); }
    .glass-light { background: rgba(250,248,242,.82); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); }
    .hero-glow { box-shadow: 0 40px 100px rgba(8,63,52,.15); }
    .floating { animation: floaty 6s ease-in-out infinite; }
    @keyframes floaty { 0%,100% { transform: translateY(0) } 50% { transform: translateY(-5px) } }
    .pulse-dot { animation: pulse 2.2s infinite; }
    @keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(201,168,106,.35)} 50%{box-shadow:0 0 0 8px rgba(201,168,106,0)} }
    .marquee { animation: marquee 22s linear infinite; }
    @keyframes marquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }
    .fade-edge { mask-image: linear-gradient(to right, transparent, black 9%, black 91%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black 9%, black 91%, transparent); }
    .mobile-shell { border-radius: 28px; box-shadow: 0 24px 70px rgba(8,63,52,.16); }
    .snap-row { scroll-snap-type:x mandatory; }
    .snap-item { scroll-snap-align:start; }
    @media (max-width: 767px){
      .mobile-shell { border-radius: 20px; }
      .desktop-only { display:none !important; }
    }
    @media (min-width: 768px){ .mobile-only { display:none !important; } }

    .mobile-date-input { -webkit-appearance: none; -moz-appearance: none; appearance: none; min-width: 0; }
    .mobile-date-input::-webkit-calendar-picker-indicator { position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }

    .lift { transition: transform .55s cubic-bezier(.2,.8,.2,1), box-shadow .55s cubic-bezier(.2,.8,.2,1); }
    .lift:hover { transform: translateY(-5px); box-shadow: 0 18px 45px rgba(8,63,52,.11); }
    .nav-item-line { position:relative; }
    .nav-item-line::after { content:""; position:absolute; left:0; bottom:-6px; width:0; height:2px; background:#0B5D4B; transition:width .35s ease; }
    .nav-item-line:hover::after, .nav-item-line.active::after { width:100%; }
    .nav-item-line.active { color:#0B5D4B !important; font-weight:700; }
    .drawer-link { opacity:0; transform:translateX(18px); transition:opacity .45s ease, transform .55s cubic-bezier(.2,.8,.2,1); }
    #mobileNav:not(.hidden) .drawer-link { opacity:1; transform:none; }
    #mobileNav:not(.hidden) .drawer-link:nth-child(1){transition-delay:.05s} #mobileNav:not(.hidden) .drawer-link:nth-child(2){transition-delay:.09s}
    #mobileNav:not(.hidden) .drawer-link:nth-child(3){transition-delay:.13s} #mobileNav:not(.hidden) .drawer-link:nth-child(4){transition-delay:.17s}
    #mobileNav:not(.hidden) .drawer-link:nth-child(5){transition-delay:.21s} #mobileNav:not(.hidden) .drawer-link:nth-child(6){transition-delay:.25s}
    #mobileNav:not(.hidden) .drawer-link:nth-child(7){transition-delay:.29s} #mobileNav:not(.hidden) .drawer-link:nth-child(8){transition-delay:.33s}
    .section-rule { position:relative; }
    .section-rule::after { content:""; position:absolute; left:0; top:0; height:1px; width:80px; background:linear-gradient(90deg, rgba(201,168,106,.7), rgba(201,168,106,0)); }
    .magnetic:hover { transform: translateY(-2px); }
    .soft-breathe { animation: softBreathe 8s ease-in-out infinite; }
    @keyframes softBreathe { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-3px)} }
    .drawer-panel { transform:translateX(12px); opacity:0; transition:transform .55s cubic-bezier(.2,.8,.2,1), opacity .35s ease; }
    #mobileNav:not(.hidden) .drawer-panel { transform:none; opacity:1; }
    @media(prefers-reduced-motion:reduce){ *,*::before,*::after { animation-duration:.001ms!important; transition-duration:.001ms!important; scroll-behavior:auto!important; } }
  </style>
</head>
<body class="selection:bg-brass selection:text-forest">
  <!-- ANIMATED LUXURY PAGE-TRANSITION LOADER (98% Visible, 2% Blur) -->
  <x-page-loader />

  <!-- Header: Full-Width Natural Off-White Top Band (Direct to Body Tone) -->
  <x-header />

  <main id="top">
    <!-- HERO: First Viewport Experience (Story + Centered Climate + Carousel + Search Dock) -->
    <section class="min-h-screen lg:h-screen flex flex-col justify-between pt-21 sm:pt-23 lg:pt-25 pb-5 lg:pb-7 relative">
      <div class="mx-auto max-w-[1480px] w-full px-4 md:px-6 lg:px-8 flex-1 flex flex-col justify-between">
        
        <!-- ================= HERO EDITORIAL STAGE (3 Columns: Left Story 5 cols + Centered Climate 2 cols + Carousel 5 cols) ================= -->
        <div class="grid gap-5 lg:gap-6 xl:gap-8 lg:grid-cols-12 items-start my-auto">
          
          <!-- Left Column (lg:col-span-5): Editorial Story & Identity -->
          <div class="reveal {{ !empty($branchesWeather) ? 'lg:col-span-5 xl:col-span-5' : 'lg:col-span-7' }} space-y-3.5 sm:space-y-4">
            <!-- Brand Title (Aligned Flush with Top of Carousel) -->
            <div class="leading-none pt-0.5">
              <h2 class="serif text-3xl sm:text-4xl lg:text-[40px] xl:text-[46px] font-bold text-forest tracking-[-0.03em] leading-none">
                Krishna Cottage
              </h2>
            </div>

            <!-- Refined Location Eyebrow -->
            <div class="flex items-center gap-2 text-forest/70 pt-0.5">
              <span class="h-2 w-2 rounded-full bg-brass pulse-dot"></span>
              <span class="eyebrow text-[10.5px] sm:text-[11.5px] tracking-[.24em] text-forest/65 font-bold">
                {{ $homepageContent['hero_eyebrow'] ?? ($branches->count() . ' Kerala Retreat Destinations · One Experience') }}
              </span>
            </div>

            <!-- Main Master Headline (Increased size to fill the space generously) -->
            <h1 class="serif text-3xl sm:text-4xl lg:text-[2.9rem] xl:text-[3.35rem] leading-[1.08] tracking-[-.03em] text-forest">
              {!! $homepageContent['hero_heading_1'] ?? 'Come away to' !!}<br>
              <span class="italic text-emerald">{!! $homepageContent['hero_heading_2'] ?? 'somewhere better.' !!}</span>
            </h1>

            <!-- Mature & Authentic Description (Increased size to match space) -->
            <p class="max-w-xl text-sm sm:text-[14.5px] lg:text-[15px] leading-relaxed text-forest/80 font-normal">
              {!! $homepageContent['hero_description'] ?? 'Stay slow. Eat well. Explore more. Krishna brings private wooden cottages, authentic plantation dining, and estate-harvested spices into one considered Kerala journey.' !!}
            </p>

            <!-- MOBILE SEARCH DOCK (lg:hidden): Directly in First Viewport on Mobile -->
            <div class="lg:hidden pt-1">
              <form id="mobile-search-form" action="{{ route('rooms.index') }}" method="GET" class="w-full bg-white text-forest rounded-2xl p-3 shadow-card border border-forest/10 space-y-2.5">
                <!-- Row 1: Destination with Icon -->
                <div class="flex items-center gap-2.5 bg-paper/80 rounded-xl px-3 py-2 border border-forest/10">
                  <i data-lucide="map-pin" class="w-4 h-4 text-emerald shrink-0"></i>
                  <div class="flex-1 min-w-0">
                    <label class="block text-[8px] uppercase tracking-wider font-bold text-forest/50">Destination</label>
                    <select name="branch_id" class="w-full bg-transparent text-xs font-bold text-forest focus:outline-hidden cursor-pointer truncate">
                      <option value="">All Branches</option>
                      @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <!-- Row 2: Check-in & Check-out (2 Equal 50/50 Columns) -->
                <div class="grid grid-cols-2 gap-2">
                  <!-- Check-in -->
                  <div class="relative bg-paper/80 rounded-xl px-2.5 py-1.5 border border-forest/10 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                    <div class="flex-1 min-w-0">
                      <label class="block text-[8px] uppercase tracking-wider font-bold text-forest/50">Check-in</label>
                      <input type="date" name="check_in" value="{{ date('Y-m-d', strtotime('+1 day')) }}" min="{{ date('Y-m-d') }}" class="mobile-date-input w-full bg-transparent text-[11px] font-bold text-forest focus:outline-hidden cursor-pointer" />
                    </div>
                  </div>

                  <!-- Check-out -->
                  <div class="relative bg-paper/80 rounded-xl px-2.5 py-1.5 border border-forest/10 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                    <div class="flex-1 min-w-0">
                      <label class="block text-[8px] uppercase tracking-wider font-bold text-forest/50">Check-out</label>
                      <input type="date" name="check_out" value="{{ date('Y-m-d', strtotime('+3 days')) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="mobile-date-input w-full bg-transparent text-[11px] font-bold text-forest focus:outline-hidden cursor-pointer" />
                    </div>
                  </div>
                </div>

                <!-- Row 3: Guests & Search Button (2 Equal 50/50 Columns) -->
                <div class="grid grid-cols-2 gap-2">
                  <!-- Guests Popover Trigger (Mobile) -->
                  <div class="relative bg-paper/80 rounded-xl px-2.5 py-1.5 border border-forest/10 flex items-center gap-2 cursor-pointer" id="mobile-guest-picker-container">
                    <input type="hidden" name="adults" id="mobile-adults-input" value="2">
                    <input type="hidden" name="children" id="mobile-children-input" value="0">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                    <div class="flex-1 min-w-0" onclick="toggleGuestPopover('mobile')">
                      <label class="block text-[8px] uppercase tracking-wider font-bold text-forest/50">Guests</label>
                      <div class="text-[11px] font-bold text-forest truncate" id="mobile-guest-label">2 Adults, 0 Kids</div>
                    </div>

                    <!-- Floating Mobile Popover Card -->
                    <div id="mobile-guest-popover" class="hidden absolute top-full left-0 right-[-100%] sm:right-0 mt-2 rounded-2xl bg-[#FAF8F5] border border-forest/15 p-3.5 shadow-2xl z-50 animate-in fade-in zoom-in-95 duration-150">
                      <div class="space-y-3" onclick="event.stopPropagation()">
                        <!-- Adults Row -->
                        <div class="flex items-center justify-between">
                          <div>
                            <p class="text-xs font-bold text-forest">Adults</p>
                            <p class="text-[9px] text-forest/50">Ages 13+</p>
                          </div>
                          <div class="flex items-center gap-2">
                            <button type="button" onclick="adjustGuestCount('mobile', 'adults', -1)" id="mobile-adults-minus" class="grid h-6 w-6 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer">-</button>
                            <span id="mobile-adults-val" class="w-4 text-center text-xs font-bold text-forest">2</span>
                            <button type="button" onclick="adjustGuestCount('mobile', 'adults', 1)" id="mobile-adults-plus" class="grid h-6 w-6 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
                          </div>
                        </div>

                        <div class="h-px bg-forest/10"></div>

                        <!-- Children Row -->
                        <div class="flex items-center justify-between">
                          <div>
                            <p class="text-xs font-bold text-forest">Children</p>
                            <p class="text-[9px] text-forest/50">Ages 0 to 12</p>
                          </div>
                          <div class="flex items-center gap-2">
                            <button type="button" onclick="adjustGuestCount('mobile', 'children', -1)" id="mobile-children-minus" class="grid h-6 w-6 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer">-</button>
                            <span id="mobile-children-val" class="w-4 text-center text-xs font-bold text-forest">0</span>
                            <button type="button" onclick="adjustGuestCount('mobile', 'children', 1)" id="mobile-children-plus" class="grid h-6 w-6 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
                          </div>
                        </div>

                        <!-- Policy Notice -->
                        <div class="p-2 rounded-lg bg-white soft-border text-[9px] text-forest/65 leading-snug">
                          Infants &amp; toddlers under 5 stay complimentary using existing bedding.
                        </div>

                        <!-- Apply Button -->
                        <button type="button" onclick="closeGuestPopover('mobile')" class="w-full py-1.5 rounded-lg bg-forest text-paper text-[11px] font-bold hover:bg-emerald transition cursor-pointer">
                          Done
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Search Stays Action Button -->
                  <button type="submit" class="w-full h-full min-h-[38px] py-1.5 px-2 rounded-xl bg-brass hover:brightness-105 text-forest font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition hover:shadow-md cursor-pointer" title="Search Availability">
                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    <span class="tracking-wide">Search Stays</span>
                  </button>
                </div>
              </form>
            </div>

            <!-- Dynamic Mobile Weather Glance (lg:hidden, Direct to Body, Minimal & Clean) -->
            @if(!empty($branchesWeather))
            <div class="lg:hidden pt-1">
              <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar snap-row py-0.5 text-forest/70">
                <span class="text-[8.5px] font-bold uppercase tracking-wider text-forest/40 shrink-0">Climate:</span>
                @foreach($branchesWeather as $bId => $w)
                  <button type="button" onclick="selectWeatherBranch({{ $bId }})" class="weather-branch-card snap-item shrink-0 flex items-center gap-1.5 px-2 py-1 text-xs cursor-pointer focus:outline-hidden hover:text-emerald transition" data-branch-id="{{ $bId }}">
                    <i data-lucide="{{ $w['icon'] }}" class="w-3 h-3 {{ $w['icon_color'] ?? 'text-amber-500' }}"></i>
                    <span class="font-bold text-forest text-[11px]">{{ $w['city'] }}</span>
                    <span class="text-forest/50 text-[10px] font-mono">{{ $w['formatted_temp'] }}</span>
                  </button>
                @endforeach
              </div>
            </div>
            @endif

            <!-- 3 Genuine Kerala Heritage Amenities (Zero gimmicks) -->
            <div class="hidden sm:flex flex-wrap items-center gap-2 sm:gap-2.5 text-xs font-semibold text-forest/80 pt-1">
              <span class="inline-flex items-center gap-1.5 rounded-full border border-forest/10 bg-white/75 px-3.5 py-1.5 shadow-2xs backdrop-blur-xs">
                <i data-lucide="home" class="h-3.5 w-3.5 text-emerald"></i>
                <span>Wooden Verandahs</span>
              </span>
              <span class="inline-flex items-center gap-1.5 rounded-full border border-forest/10 bg-white/75 px-3.5 py-1.5 shadow-2xs backdrop-blur-xs">
                <i data-lucide="utensils" class="h-3.5 w-3.5 text-emerald"></i>
                <span>Clay-Pot Cuisine</span>
              </span>
              <span class="inline-flex items-center gap-1.5 rounded-full border border-forest/10 bg-white/75 px-3.5 py-1.5 shadow-2xs backdrop-blur-xs">
                <i data-lucide="sparkles" class="h-3.5 w-3.5 text-emerald"></i>
                <span>Estate Harvest Spices</span>
              </span>
            </div>

            <!-- Primary CTAs (Desktop / Tablet) -->
            <div class="hidden lg:flex flex-wrap items-center gap-3.5 pt-1">
              <a href="{{ route('rooms.index') }}" class="group inline-flex items-center gap-2 rounded-xl bg-forest px-6 py-3 text-xs sm:text-sm font-bold text-paper shadow-card transition hover:bg-forest/90 hover:-translate-y-0.5">
                <span>Explore Cottages</span>
                <i data-lucide="arrow-up-right" class="h-4 w-4 text-brass transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5"></i>
              </a>
              <button onclick="openChat()" type="button" class="inline-flex items-center gap-2 rounded-xl border border-forest/15 bg-white/75 px-5 py-3 text-xs sm:text-sm font-bold text-forest transition hover:bg-white hover:-translate-y-0.5 cursor-pointer">
                <i data-lucide="message-circle" class="h-4 w-4 text-emerald"></i>
                <span>Chat with Concierge</span>
              </button>
            </div>
          </div>

          <!-- ================= MIDDLE COLUMN: VERTICAL CLIMATE (Direct to Body, Centric, No Borders) ================= -->
          @if(!empty($branchesWeather))
          <div class="hidden lg:flex lg:col-span-2 xl:col-span-2 flex-col items-center text-center w-full pt-1">
            <!-- Minimal Eyebrow Centered -->
            <div class="flex items-center justify-center gap-1.5 mb-3 text-forest/50">
              <span class="h-1.5 w-1.5 rounded-full bg-emerald animate-pulse"></span>
              <span class="text-[9.5px] font-bold uppercase tracking-[0.24em]">Climate</span>
            </div>

            <!-- Vertical Branch List Direct to Body (No background card, no borders, centered) -->
            <div class="w-full space-y-3 sm:space-y-3.5">
              @foreach($branchesWeather as $bId => $w)
                <button type="button" 
                        onclick="selectWeatherBranch({{ $bId }})" 
                        class="weather-branch-card group/w text-center w-full transition-all duration-300 cursor-pointer focus:outline-hidden block py-1.5 hover:scale-[1.02]" 
                        data-branch-id="{{ $bId }}" 
                        title="Click to view stays in {{ $w['city'] }}">
                  
                  <!-- City & Live Temp Centered -->
                  <div class="flex items-center justify-center gap-1.5">
                    <span class="text-[13px] sm:text-[14px] font-bold text-forest group-hover/w:text-emerald transition tracking-tight">
                      {{ $w['city'] }}
                    </span>
                    <div class="flex items-center gap-1">
                      <i data-lucide="{{ $w['icon'] }}" class="w-4 h-4 {{ $w['icon_color'] ?? 'text-amber-500' }}"></i>
                      <span class="serif text-sm sm:text-base font-bold text-forest">{{ $w['temperature'] }}°</span>
                    </div>
                  </div>

                  <!-- Condition & Wind Centered -->
                  <p class="text-[10px] sm:text-[10.5px] text-forest/55 font-medium mt-0.5">
                    {{ $w['short_label'] }} <span class="text-forest/30">·</span> <span class="font-mono text-[9px] text-forest/45">{{ $w['wind'] }}</span>
                  </p>

                  <!-- 3-Day Forecast Centered in Minimal Clean Font -->
                  @if(!empty($w['forecast']))
                  <div class="flex items-center justify-center gap-1.5 text-[9px] sm:text-[9.5px] text-forest/50 mt-1.5 font-mono tracking-tight">
                    @foreach(array_slice($w['forecast'], 0, 3) as $fc)
                      <span class="{{ $loop->first ? 'text-forest/75 font-semibold' : '' }}">{{ $fc['day'] }}:{{ $fc['temp_max'] }}°</span>
                      @if(!$loop->last)
                        <span class="text-forest/25">·</span>
                      @endif
                    @endforeach
                  </div>
                  @endif

                  @if(!$loop->last)
                    <div class="w-12 h-px bg-forest/10 mx-auto mt-2.5"></div>
                  @endif

                </button>
              @endforeach
            </div>

            <!-- Subtle Live Telemetry Indicator Centered -->
            <div class="mt-3 flex items-center justify-center gap-1.5 text-[8.5px] text-forest/40 tracking-wider font-medium">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald/70"></span>
              <span>Live Open-Meteo telemetry</span>
            </div>

          </div>
          @endif

          <!-- Right Column: Animated Branch Carousel Stage (5 cols) -->
          <div class="reveal d1 {{ !empty($branchesWeather) ? 'lg:col-span-5 xl:col-span-5' : 'lg:col-span-5' }}">
            <div id="hero-carousel" class="relative overflow-hidden rounded-[28px] sm:rounded-[34px] lg:rounded-[38px] bg-mint shadow-xl border border-white/60 h-[320px] sm:h-[360px] lg:h-[400px] xl:h-[450px] group select-none">
              
              <!-- Slides Container -->
              <div id="hero-slides-wrapper" class="relative w-full h-full">
                @foreach($heroSlides as $sIdx => $slide)
                  <div class="hero-slide absolute inset-0 transition-all duration-700 ease-out {{ $sIdx === 0 ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-95 pointer-events-none z-0' }}" data-slide-index="{{ $sIdx }}" data-branch-id="{{ $slide['branch_id'] ?? '' }}" data-city="{{ strtolower($slide['tag'] ?? '') }}">
                    
                    <!-- Slide Photo (Optimized with fetchpriority & SEO alt) -->
                    <img src="{{ $slide['image'] }}" 
                         class="h-full w-full object-cover transition-transform duration-1000 ease-out group-hover:scale-105" 
                         alt="{{ $slide['title'] }} — Best Luxury Cottages in {{ $slide['tag'] ?? 'Rajakkadu, Idukki, Munnar, Kerala' }}"
                         {{ $sIdx === 0 ? 'fetchpriority="high" loading="eager" decoding="sync"' : 'loading="lazy" decoding="async"' }} />
                    
                    <!-- Subtle Vignette / Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                    <!-- Top Left Tag Badge -->
                    <div class="absolute top-4 left-4 z-20 glass-light rounded-full px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-forest border border-white/50 shadow-2xs flex items-center gap-1.5">
                      <i data-lucide="map-pin" class="w-3 h-3 text-emerald"></i>
                      <span>{{ $slide['tag'] ?? 'Krishna Cottages' }}</span>
                    </div>

                    <!-- Slide Counter (Top Right) -->
                    <div class="absolute top-4 right-4 z-20 glass-dark text-paper rounded-full px-2.5 py-1 text-[10px] font-bold tracking-widest border border-white/10">
                      <span>0{{ $sIdx + 1 }}</span><span class="text-white/40"> / 0{{ count($heroSlides) }}</span>
                    </div>

                    <!-- Floating Bottom Glass Overlay Card -->
                    <div class="absolute inset-x-4 bottom-4 z-20 rounded-2xl glass-dark p-4 sm:p-5 text-paper backdrop-blur-md border border-white/15 shadow-xl">
                      <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                          <div class="flex items-center gap-2">
                            <h3 class="serif text-base sm:text-lg font-bold text-paper truncate">{{ $slide['title'] }}</h3>
                            @if(!empty($slide['badge']))
                              <span class="shrink-0 rounded-full bg-brass/20 px-2 py-0.5 text-[9px] font-bold text-brass border border-brass/30">{{ $slide['badge'] }}</span>
                            @endif
                          </div>
                          <p class="mt-1 text-xs text-white/75 leading-relaxed line-clamp-2">{{ $slide['description'] }}</p>
                        </div>
                        @if(!empty($slide['link']))
                          <a href="{{ $slide['link'] }}" class="shrink-0 mt-0.5 grid h-9 w-9 place-items-center rounded-xl bg-brass hover:brightness-110 text-forest shadow-xs transition hover:scale-105" title="Explore this destination">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                          </a>
                        @endif
                      </div>
                    </div>

                  </div>
                @endforeach
              </div>

              <!-- Carousel Controls: Previous / Next Buttons -->
              <button type="button" onclick="prevHeroSlide()" class="absolute left-3 top-1/2 -translate-y-1/2 z-30 grid h-9 w-9 place-items-center rounded-full glass-light text-forest opacity-0 group-hover:opacity-100 transition-opacity duration-300 shadow-md hover:bg-white cursor-pointer" aria-label="Previous Slide">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
              </button>
              <button type="button" onclick="nextHeroSlide()" class="absolute right-3 top-1/2 -translate-y-1/2 z-30 grid h-9 w-9 place-items-center rounded-full glass-light text-forest opacity-0 group-hover:opacity-100 transition-opacity duration-300 shadow-md hover:bg-white cursor-pointer" aria-label="Next Slide">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
              </button>

              <!-- Slide Indicator Pills / Dots (Positioned above bottom card) -->
              <div class="absolute bottom-[96px] sm:bottom-[106px] left-1/2 -translate-x-1/2 z-30 flex items-center gap-1.5 bg-black/35 backdrop-blur-xs px-2.5 py-1 rounded-full border border-white/10">
                @foreach($heroSlides as $dIdx => $slide)
                  <button type="button" onclick="goToHeroSlide({{ $dIdx }})" class="hero-carousel-dot h-1.5 rounded-full transition-all duration-300 cursor-pointer {{ $dIdx === 0 ? 'w-5 bg-brass' : 'w-1.5 bg-white/40 hover:bg-white/70' }}" aria-label="Go to slide {{ $dIdx + 1 }}"></button>
                @endforeach
              </div>

            </div>
          </div>

        </div>

        <!-- ================= DEDICATED CENTERED BOOKING SEARCH DOCK (Desktop) ================= -->
        <div id="search-section" class="mt-3 sm:mt-4 lg:mt-5 max-w-5xl mx-auto w-full reveal hidden lg:block mb-1 lg:mb-3">
          <!-- DESKTOP SEARCH BAR (lg:block): Fluid, Spacious Pill -->
          <form id="desktop-search-form" action="{{ route('rooms.index') }}" method="GET" class="w-full bg-white text-forest rounded-full p-2 sm:p-2.5 shadow-[0_15px_45px_rgba(6,63,52,0.12)] flex items-center divide-x divide-forest/10 border border-forest/15 transition-all duration-300 hover:shadow-[0_20px_55px_rgba(6,63,52,0.18)] hover:border-forest/25 relative z-40">
            <!-- 1. DESTINATION / BRANCH (Ultra-Stylish Card Matching Guest Picker) -->
            <div class="relative px-6 py-2.5 flex-[1.3] hover:bg-forest/[0.03] rounded-l-full transition-colors cursor-pointer group" onclick="const s=document.getElementById('branchSelect'); if(s){s.focus(); try{s.showPicker();}catch(e){}}">
              <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-3 h-3 text-emerald shrink-0"></i>
                <span>Destination</span>
              </label>
              <div class="relative flex items-center justify-between mt-0.5">
                <select name="branch_id" id="branchSelect" class="w-full bg-transparent text-sm font-semibold text-forest focus:outline-none cursor-pointer appearance-none pr-6 truncate z-10">
                  <option value="">All Branches (Cottages Central)</option>
                  @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                  @endforeach
                </select>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-forest/40 group-hover:text-forest transition-transform duration-200 shrink-0 absolute right-0 pointer-events-none"></i>
              </div>
            </div>

            <!-- 2. CHECK-IN / CHECK-OUT DATES (Stylish Double Card with Clickable Calendar Trigger) -->
            <div class="flex-[1.6] flex items-center divide-x divide-forest/10">
              <!-- Check-in Subcard -->
              <div class="flex-1 px-5 py-2.5 hover:bg-forest/[0.03] transition-colors cursor-pointer group relative" onclick="openDateCalendar('welcome-check-in-input')">
                <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                  <i data-lucide="calendar" class="w-3 h-3 text-emerald shrink-0"></i>
                  <span>Check-in</span>
                </label>
                <div class="flex items-center justify-between mt-0.5">
                  <span id="welcome-check-in-display" class="text-sm font-semibold text-forest truncate">
                    {{ date('D, d M', strtotime('+1 day')) }}
                  </span>
                  <i data-lucide="calendar" class="w-3.5 h-3.5 text-forest/30 group-hover:text-emerald transition shrink-0 ml-1"></i>
                </div>
                <input type="date" 
                       name="check_in" 
                       id="welcome-check-in-input" 
                       value="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                       min="{{ date('Y-m-d') }}" 
                       onchange="updateDateDisplay('welcome-check-in-input', 'welcome-check-in-display'); updateMinCheckOut('welcome-check-in-input', 'welcome-check-out-input');" 
                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
              </div>

              <!-- Check-out Subcard -->
              <div class="flex-1 px-5 py-2.5 hover:bg-forest/[0.03] transition-colors cursor-pointer group relative" onclick="openDateCalendar('welcome-check-out-input')">
                <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                  <i data-lucide="calendar-check-2" class="w-3 h-3 text-emerald shrink-0"></i>
                  <span>Check-out</span>
                </label>
                <div class="flex items-center justify-between mt-0.5">
                  <span id="welcome-check-out-display" class="text-sm font-semibold text-forest truncate">
                    {{ date('D, d M', strtotime('+3 days')) }}
                  </span>
                  <i data-lucide="calendar" class="w-3.5 h-3.5 text-forest/30 group-hover:text-emerald transition shrink-0 ml-1"></i>
                </div>
                <input type="date" 
                       name="check_out" 
                       id="welcome-check-out-input" 
                       value="{{ date('Y-m-d', strtotime('+3 days')) }}" 
                       min="{{ date('Y-m-d', strtotime('+2 days')) }}" 
                       onchange="updateDateDisplay('welcome-check-out-input', 'welcome-check-out-display')" 
                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
              </div>
            </div>

            <!-- 3. GUESTS COUNTER (ADULTS & CHILDREN POPOVER) -->
            <div class="relative px-6 py-2.5 w-56 hover:bg-forest/[0.03] transition-colors cursor-pointer group" id="desktop-guest-picker-container">
              <input type="hidden" name="adults" id="desktop-adults-input" value="2">
              <input type="hidden" name="children" id="desktop-children-input" value="0">
              <div onclick="toggleGuestPopover('desktop')" class="w-full">
                <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                  <i data-lucide="users" class="w-3 h-3 text-emerald shrink-0"></i>
                  <span>Guests</span>
                </label>
                <div class="flex items-center justify-between text-sm font-semibold text-forest mt-0.5 select-none">
                  <span id="desktop-guest-label" class="truncate">2 Adults, 0 Kids</span>
                  <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-forest/40 group-hover:text-forest transition-transform duration-200 shrink-0 ml-1" id="desktop-guest-chevron"></i>
                </div>
              </div>

              <!-- Floating Popover Card (Opens upwards by default to prevent overflow, with dynamic positioning) -->
              <div id="desktop-guest-popover" class="hidden absolute bottom-full right-0 mb-3 w-72 rounded-2xl bg-[#FAF8F5] border border-forest/15 p-4 shadow-2xl z-50 animate-in fade-in zoom-in-95 duration-150">
                <div class="space-y-3.5" onclick="event.stopPropagation()">
                  <!-- Adults Row -->
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-xs font-bold text-forest">Adults</p>
                      <p class="text-[10px] text-forest/50">Ages 13 and above</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                      <button type="button" onclick="adjustGuestCount('desktop', 'adults', -1)" id="desktop-adults-minus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer">-</button>
                      <span id="desktop-adults-val" class="w-4 text-center text-xs font-bold text-forest">2</span>
                      <button type="button" onclick="adjustGuestCount('desktop', 'adults', 1)" id="desktop-adults-plus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
                    </div>
                  </div>

                  <div class="h-px bg-forest/10"></div>

                  <!-- Children Row -->
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-xs font-bold text-forest">Children</p>
                      <p class="text-[10px] text-forest/50">Ages 0 to 12</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                      <button type="button" onclick="adjustGuestCount('desktop', 'children', -1)" id="desktop-children-minus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer">-</button>
                      <span id="desktop-children-val" class="w-4 text-center text-xs font-bold text-forest">0</span>
                      <button type="button" onclick="adjustGuestCount('desktop', 'children', 1)" id="desktop-children-plus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
                    </div>
                  </div>

                  <!-- Policy Notice -->
                  <div class="p-2 rounded-xl bg-white soft-border text-[10px] text-forest/65 flex items-start gap-1.5 leading-snug">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-emerald shrink-0 mt-0.5"></i>
                    <span>Infants &amp; toddlers under 5 stay complimentary using existing bedding.</span>
                  </div>

                  <!-- Done Button -->
                  <button type="button" onclick="closeGuestPopover('desktop')" class="w-full py-1.5 rounded-xl bg-forest text-paper text-xs font-bold hover:bg-emerald transition cursor-pointer">
                    Apply Guests
                  </button>
                </div>
              </div>
            </div>

            <!-- SEARCH SUBMIT BUTTON -->
            <div class="p-1.5 flex items-center justify-end shrink-0">
              <button type="submit" class="px-8 py-3.5 rounded-full bg-brass hover:brightness-105 text-forest font-bold text-sm flex items-center justify-center gap-2 shadow-xs transition hover:shadow-md cursor-pointer whitespace-nowrap">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Search Stays</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Mobile Bottom Secondary CTAs & Heritage Tags (lg:hidden) -->
        <div class="lg:hidden mt-4 space-y-3">
          <div class="flex flex-wrap items-center justify-center gap-2 text-[10px] font-semibold text-forest/75">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-forest/10 bg-white/80 px-2.5 py-1 shadow-2xs">
              <i data-lucide="home" class="h-3 w-3 text-emerald"></i>
              <span>Wooden Verandahs</span>
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-forest/10 bg-white/80 px-2.5 py-1 shadow-2xs">
              <i data-lucide="utensils" class="h-3 w-3 text-emerald"></i>
              <span>Clay-Pot Cuisine</span>
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-forest/10 bg-white/80 px-2.5 py-1 shadow-2xs">
              <i data-lucide="sparkles" class="h-3 w-3 text-emerald"></i>
              <span>Estate Spices</span>
            </span>
          </div>
          <div class="grid grid-cols-2 gap-2 pt-0.5">
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-forest px-3 py-2.5 text-[11px] font-bold text-paper shadow-xs">
              <span>Explore Cottages</span>
              <i data-lucide="arrow-up-right" class="h-3.5 w-3.5 text-brass"></i>
            </a>
            <button onclick="openChat()" type="button" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-forest/15 bg-white/80 px-3 py-2.5 text-[11px] font-bold text-forest">
              <i data-lucide="message-circle" class="h-3.5 w-3.5 text-emerald"></i>
              <span>Concierge Chat</span>
            </button>
          </div>
        </div>

      </div>
    </section>

    <!-- ================= SECOND VIEWPORT: LIVE AVAILABILITY & 4 PILLAR CARDS ================= -->
    <section id="overview" class="py-12 lg:py-16">
      <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">

        <!-- ================= LIVE IN-PAGE AVAILABILITY CONTAINER ================= -->
        <div id="live-availability-container" class="hidden mb-10 transition-all duration-500 ease-out">
          <div class="relative overflow-hidden rounded-[26px] bg-white border border-forest/15 shadow-2xl p-5 md:p-8">
            <!-- Header bar inside results -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-forest/10">
              <div class="flex items-center gap-3">
                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald/10 text-emerald">
                  <i data-lucide="sparkles" class="w-5 h-5 text-emerald"></i>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald animate-ping"></span>
                    <h3 class="serif text-xl md:text-2xl font-bold text-forest">Available Stays & Suites</h3>
                  </div>
                  <p id="live-availability-summary" class="text-xs md:text-sm text-forest/65 font-medium mt-0.5">
                    Checking best options for your stay...
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-forest/15 bg-paper/80 hover:bg-paper text-xs font-semibold text-forest transition">
                  <span>View Full Directory</span>
                  <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                </a>
                <button type="button" onclick="closeLiveAvailability()" class="grid h-9 w-9 place-items-center rounded-xl bg-forest/5 hover:bg-forest/10 text-forest/70 hover:text-forest transition" title="Dismiss">
                  <i data-lucide="x" class="w-4 h-4"></i>
                </button>
              </div>
            </div>

            <!-- Loading State Spinner -->
            <div id="live-availability-loading" class="hidden py-12 text-center">
              <div class="inline-flex items-center justify-center p-4 rounded-full bg-mint/50 mb-3 text-emerald animate-spin">
                <i data-lucide="loader-2" class="w-7 h-7"></i>
              </div>
              <p class="text-sm font-semibold text-forest">Checking real-time capacity & calendars...</p>
              <p class="text-xs text-forest/50 mt-1">Verifying room allocations and reservation availability</p>
            </div>

            <!-- Error State -->
            <div id="live-availability-error" class="hidden py-8 text-center">
              <div class="inline-flex items-center justify-center p-3 rounded-full bg-amber-50 text-amber-700 mb-2">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
              </div>
              <p id="live-availability-error-msg" class="text-sm font-medium text-forest">Unable to fetch availability.</p>
            </div>

            <!-- Empty State -->
            <div id="live-availability-empty" class="hidden py-10 text-center">
              <div class="inline-flex items-center justify-center p-4 rounded-full bg-forest/5 text-forest/40 mb-3">
                <i data-lucide="bed-double" class="w-8 h-8"></i>
              </div>
              <h4 class="text-base font-bold text-forest">No suites available for this party size & date range</h4>
              <p class="text-xs text-forest/60 max-w-md mx-auto mt-1">All suites matching your requested capacity are booked for these dates, or your party size exceeds single suite capacity limits. Try adjusting dates or browse other categories.</p>
              <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-forest text-paper text-xs font-bold shadow-xs hover:brightness-110 transition">Browse Full Directory</a>
            </div>

            <!-- Results Grid -->
            <div id="live-availability-grid" class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
              <!-- Dynamically populated room cards -->
            </div>
          </div>
        </div>

        <!-- ================= 4 BALANCED RESORT PILLAR CARDS ================= -->
        <div class="mt-8 max-w-5xl mx-auto reveal d2">
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
            
            <!-- 01 Cottages & Suites -->
            <a href="{{ route('rooms.index') }}" class="group rounded-2xl bg-white/80 p-4 sm:p-5 soft-border shadow-2xs hover:shadow-md transition hover:-translate-y-1 block">
              <div class="h-10 w-10 rounded-xl bg-forest/5 flex items-center justify-center text-emerald group-hover:bg-forest group-hover:text-brass transition">
                <i data-lucide="bed-double" class="h-5 w-5"></i>
              </div>
              <p class="mt-3.5 text-xs sm:text-sm font-bold text-forest group-hover:text-emerald transition">Cottages &amp; Suites</p>
              <p class="mt-1 text-[10px] sm:text-[11px] text-forest/60 leading-snug">Timber verandas, nature views &amp; private living</p>
              <div class="mt-3 flex items-center gap-1 text-[10px] font-bold text-emerald group-hover:translate-x-1 transition">
                <span>Explore</span>
                <i data-lucide="arrow-right" class="w-3 h-3"></i>
              </div>
            </a>

            <!-- 02 Dining & Cuisine -->
            <a href="{{ route('dining.index') }}" class="group rounded-2xl bg-white/80 p-4 sm:p-5 soft-border shadow-2xs hover:shadow-md transition hover:-translate-y-1 block">
              <div class="h-10 w-10 rounded-xl bg-forest/5 flex items-center justify-center text-emerald group-hover:bg-forest group-hover:text-brass transition">
                <i data-lucide="utensils" class="h-5 w-5"></i>
              </div>
              <p class="mt-3.5 text-xs sm:text-sm font-bold text-forest group-hover:text-emerald transition">Plantation Dining</p>
              <p class="mt-1 text-[10px] sm:text-[11px] text-forest/60 leading-snug">Clay-pot Kerala cuisine &amp; fresh garden harvests</p>
              <div class="mt-3 flex items-center gap-1 text-[10px] font-bold text-emerald group-hover:translate-x-1 transition">
                <span>Explore</span>
                <i data-lucide="arrow-right" class="w-3 h-3"></i>
              </div>
            </a>

            <!-- 03 Krishna Spices -->
            <a href="{{ route('spices.index') }}" class="group rounded-2xl bg-white/80 p-4 sm:p-5 soft-border shadow-2xs hover:shadow-md transition hover:-translate-y-1 block">
              <div class="h-10 w-10 rounded-xl bg-forest/5 flex items-center justify-center text-emerald group-hover:bg-forest group-hover:text-brass transition">
                <i data-lucide="shopping-bag" class="h-5 w-5"></i>
              </div>
              <p class="mt-3.5 text-xs sm:text-sm font-bold text-forest group-hover:text-emerald transition">Krishna Spices</p>
              <p class="mt-1 text-[10px] sm:text-[11px] text-forest/60 leading-snug">Direct estate pepper, cloves &amp; cardamom</p>
              <div class="mt-3 flex items-center gap-1 text-[10px] font-bold text-emerald group-hover:translate-x-1 transition">
                <span>Shop</span>
                <i data-lucide="arrow-right" class="w-3 h-3"></i>
              </div>
            </a>

            <!-- 04 Photo Gallery -->
            <a href="{{ route('gallery.index') }}" class="group rounded-2xl bg-white/80 p-4 sm:p-5 soft-border shadow-2xs hover:shadow-md transition hover:-translate-y-1 block">
              <div class="h-10 w-10 rounded-xl bg-forest/5 flex items-center justify-center text-emerald group-hover:bg-forest group-hover:text-brass transition">
                <i data-lucide="images" class="h-5 w-5"></i>
              </div>
              <p class="mt-3.5 text-xs sm:text-sm font-bold text-forest group-hover:text-emerald transition">Cottages Gallery</p>
              <p class="mt-1 text-[10px] sm:text-[11px] text-forest/60 leading-snug">Moments captured across our 3 Kerala retreats</p>
              <div class="mt-3 flex items-center gap-1 text-[10px] font-bold text-emerald group-hover:translate-x-1 transition">
                <span>View</span>
                <i data-lucide="arrow-right" class="w-3 h-3"></i>
              </div>
            </a>

          </div>
        </div>

      </div>
    </section>

    <!-- Branch rail -->
    <section id="branches" class="py-8 md:py-12">
      <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
          <div class="reveal">
            <p class="eyebrow text-emerald">{{ $homepageContent['branch_section_eyebrow'] ?? 'Choose your Krishna' }}</p>
            <h2 class="serif mt-2 text-4xl leading-none tracking-[-.04em] text-forest md:text-6xl">{!! $homepageContent['branch_section_heading'] ?? ($branches->count() . ' ways to stay.') !!}</h2>
          </div>
          <p class="hidden max-w-xs text-right text-xs leading-5 text-forest/50 md:block">Select a branch to explore its rooms, dining dishes, ayurvedic facilities, and nearby trails.</p>
        </div>
        @php
          $colCount = $branches->count();
          $gridCols = match(true) {
            $colCount === 1 => 'grid-cols-1 max-w-md mx-auto',
            $colCount === 2 => 'grid-cols-1 md:grid-cols-2 max-w-4xl mx-auto',
            $colCount === 3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
            default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
          };
        @endphp
        <div class="mt-6 grid gap-4 {{ $gridCols }}">
          @foreach($branches as $bIdx => $b)
          @php
            $customCard = collect($homepageContent['branches'] ?? [])->firstWhere('branch_id', $b->id)
                          ?? collect($homepageContent['branches'] ?? [])->get($bIdx);
            $bTitle = $customCard['title'] ?? $b->display_name ?? $b->name;
            $bTagline = $customCard['tagline'] ?? $b->tagline ?? ($b->city . ' · ' . $b->state);
            $bImage = $customCard['image'] ?? $b->cover_image_url ?? $b->hero_image_url ?? 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1100&q=85';
          @endphp
          <div class="branch-card group reveal d{{ ($bIdx % 4) + 1 }} relative overflow-hidden rounded-[22px] {{ $bIdx === 0 ? 'bg-forest text-paper' : 'bg-white text-forest soft-border' }} text-left flex flex-col justify-between shadow-xs hover:shadow-md transition" data-branch-id="{{ $b->id }}" data-branch="{{ $bTitle }}">
            <div>
              <div class="img-zoom relative h-[220px] overflow-hidden">
                <img src="{{ $bImage }}" class="h-full w-full object-cover opacity-95" alt="{{ $bTitle }}"/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                <div class="absolute left-3 top-3 rounded-full {{ $bIdx === 0 ? 'bg-white/80 text-forest' : 'bg-forest/80 text-paper' }} px-2.5 py-1.5 text-[9px] font-bold">0{{ $bIdx + 1 }}</div>
                @if($b->city)
                <div class="absolute right-3 top-3 rounded-full bg-black/40 backdrop-blur-xs text-white px-2.5 py-1 text-[9px] font-semibold flex items-center gap-1">
                  <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i> {{ $b->city }}
                </div>
                @endif
              </div>
              <div class="p-4 space-y-1">
                <p class="text-base font-semibold">{{ $bTitle }}</p>
                <p class="text-[11px] leading-relaxed {{ $bIdx === 0 ? 'text-paper/60' : 'text-forest/50' }}">{{ $bTagline }}</p>
                @if($b->phone)
                <div class="pt-1.5 flex items-center gap-2">
                  <a href="tel:{{ preg_replace('/[^0-9+]/', '', $b->phone) }}" class="inline-flex items-center gap-1.5 text-[11px] font-semibold {{ $bIdx === 0 ? 'text-brass hover:text-white' : 'text-emerald hover:text-forest' }} hover:underline transition" title="Direct Phone: Call {{ $bTitle }}">
                    <i data-lucide="phone-call" class="w-3 h-3"></i>
                    <span>{{ $b->phone }}</span>
                  </a>
                </div>
                @endif
              </div>
            </div>
            <div class="px-4 pb-4 pt-1 flex items-center justify-between border-t {{ $bIdx === 0 ? 'border-white/10' : 'border-forest/5' }}">
              <a href="{{ route('rooms.index', ['branch_id' => $b->id]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold {{ $bIdx === 0 ? 'text-brass hover:text-white' : 'text-emerald hover:text-forest' }} transition">
                <span>View Rooms</span>
                <i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i>
              </a>
              <div class="flex items-center gap-2">
                @if($b->phone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $b->phone) }}" class="text-[10px] uppercase font-bold tracking-wider {{ $bIdx === 0 ? 'text-brass hover:text-white' : 'text-emerald hover:text-forest' }} flex items-center gap-1" title="Call Branch">
                  <i data-lucide="phone" class="w-3 h-3"></i> Call
                </a>
                <span class="{{ $bIdx === 0 ? 'text-white/20' : 'text-forest/20' }}">&middot;</span>
                @endif
                <button type="button" onclick="selectBranchQuick({{ $b->id }}, '{{ addslashes($bTitle) }}')" class="text-[10px] uppercase font-bold tracking-wider {{ $bIdx === 0 ? 'text-paper/40 hover:text-paper' : 'text-forest/40 hover:text-forest' }} cursor-pointer">
                  Select
                </button>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Stay overview -->
    <section id="stay" class="py-10 md:py-14">
      <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        <div class="grid gap-3 lg:grid-cols-12">
          <div class="reveal lg:col-span-7">
            <div class="relative overflow-hidden rounded-[24px] bg-mint p-5 md:p-7">
              <div class="grid gap-6 md:grid-cols-[1.05fr_.95fr] md:items-end">
                <div>
                  <p class="eyebrow text-emerald">{{ $homepageContent['stay_eyebrow'] ?? 'Your stay' }}</p>
                  <h2 class="serif mt-3 text-4xl leading-[.95] tracking-[-.04em] text-forest md:text-5xl">{!! $homepageContent['stay_heading_1'] ?? 'Comfort, calm' !!}<br><span class="italic text-emerald">{!! $homepageContent['stay_heading_2'] ?? 'and room to breathe.' !!}</span></h2>
                  <p class="mt-4 max-w-md text-sm leading-6 text-forest/55">{{ $homepageContent['stay_description'] ?? 'Explore room categories, compare what suits your visit and continue directly into availability for the branch you choose.' }}</p>
                </div>
                <div class="rounded-[20px] bg-white/70 p-4 soft-border">
                  <div class="flex items-center justify-between"><span class="eyebrow text-forest/40">Stay basics</span><i data-lucide="bed-double" class="h-4 w-4 text-emerald"></i></div>
                  <div class="mt-4 grid grid-cols-2 gap-2">
                    <div class="rounded-xl bg-paper p-3"><p class="text-[10px] font-bold text-forest">Room choices</p><p class="mt-1 text-[9px] text-forest/45">Browse by branch</p></div>
                    <div class="rounded-xl bg-paper p-3"><p class="text-[10px] font-bold text-forest">Guest needs</p><p class="mt-1 text-[9px] text-forest/45">Guests · dates · stay</p></div>
                    <div class="rounded-xl bg-paper p-3"><p class="text-[10px] font-bold text-forest">Easy booking</p><p class="mt-1 text-[9px] text-forest/45">Availability first</p></div>
                    <div class="rounded-xl bg-paper p-3"><p class="text-[10px] font-bold text-forest">Confirmation</p><p class="mt-1 text-[9px] text-forest/45">Email + SMS</p></div>
                  </div>
                </div>
              </div>
              <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('rooms.index') }}" class="magnetic inline-flex items-center gap-2 rounded-xl bg-forest px-4 py-3 text-[11px] font-bold text-paper transition">
                  <span>Check availability & book</span>
                  <i data-lucide="arrow-up-right" class="h-4 w-4 text-brass"></i>
                </a>
                <a href="{{ route('rooms.index') }}" class="rounded-xl bg-white/70 px-4 py-3 text-[11px] font-bold text-forest ring-1 ring-forest/10 hover:bg-white transition">
                  Compare all rooms
                </a>
              </div>
            </div>
          </div>
          <div class="reveal d1 lg:col-span-5">
            <div class="grid h-full gap-2.5 sm:grid-cols-2 lg:grid-cols-1">
              <div class="img-zoom overflow-hidden rounded-[24px]">
                <div class="relative h-[230px]">
                  <img src="{{ $homepageContent['stay_image'] ?? 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1200&q=85' }}" class="h-full w-full object-cover" alt="Krishna guest room"/>
                  <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-4"><p class="text-sm font-semibold text-white">{{ $homepageContent['stay_image_title'] ?? 'Rooms for slower stays.' }}</p><p class="mt-1 text-[10px] text-white/65">{{ $homepageContent['stay_image_subtitle'] ?? 'Room details, amenities and availability.' }}</p></div>
                </div>
              </div>
              <div class="rounded-[24px] bg-white p-5 soft-border">
                <p class="eyebrow text-emerald">Stay journey</p>
                <div class="mt-4 space-y-3">
                  <div class="flex items-start gap-3"><span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-mint text-[9px] font-bold text-emerald">01</span><div><p class="text-[11px] font-bold text-forest">Choose a branch</p><p class="mt-1 text-[9px] text-forest/45">See branch-specific rooms and surroundings.</p></div></div>
                  <div class="flex items-start gap-3"><span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-mint text-[9px] font-bold text-emerald">02</span><div><p class="text-[11px] font-bold text-forest">Check your dates</p><p class="mt-1 text-[9px] text-forest/45">See what is available for your stay.</p></div></div>
                  <div class="flex items-start gap-3"><span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-mint text-[9px] font-bold text-emerald">03</span><div><p class="text-[11px] font-bold text-forest">Reserve with confidence</p><p class="mt-1 text-[9px] text-forest/45">Receive your booking updates by email and SMS.</p></div></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Experiences & facilities -->
    <section id="experience" class="py-10 md:py-14">
      <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4">
          <div class="reveal">
            <p class="eyebrow text-emerald">{{ $homepageContent['experience_eyebrow'] ?? 'The Krishna experience' }}</p>
            <h2 class="serif mt-2 text-4xl tracking-[-.04em] text-forest md:text-6xl">{!! $homepageContent['experience_heading_1'] ?? 'More than a room.' !!}<br><span class="italic text-emerald">{!! $homepageContent['experience_heading_2'] ?? 'A complete stay.' !!}</span></h2>
          </div>
          <span class="hidden rounded-full border border-forest/10 bg-white/60 px-3 py-2 text-[9px] font-bold uppercase tracking-[.18em] text-forest/45 md:block">Nature · activities · ayurveda · dining</span>
        </div>
        <div class="mt-6 grid gap-2.5 md:grid-cols-4">
          <article class="reveal d1 lift relative overflow-hidden rounded-[20px] bg-forest text-paper md:row-span-2">
            <img src="{{ $homepageContent['experience_image_main'] ?? 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=1000&q=85' }}" class="h-full min-h-[310px] w-full object-cover opacity-80" alt="Krishna nature surroundings"/>
            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent p-4">
              <div class="flex items-center gap-2">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10 backdrop-blur-md"><i data-lucide="trees" class="h-4 w-4 text-brass"></i></span>
                <div>
                  <p class="text-sm font-semibold">{{ $homepageContent['experience_card_1_title'] ?? 'Nature & open spaces' }}</p>
                  <p class="text-[10px] text-paper/55">{{ $homepageContent['experience_card_1_desc'] ?? 'Green surroundings · quiet moments' }}</p>
                </div>
              </div>
            </div>
          </article>
          {{-- Card 2: Local exploration --}}
          @if(!empty($homepageContent['experience_image_local']))
            <a href="{{ route('nearby.index') }}" class="reveal d2 lift relative overflow-hidden rounded-[20px] bg-forest text-paper block group h-[160px] md:h-auto min-h-[155px]">
              <img src="{{ $homepageContent['experience_image_local'] }}" class="absolute inset-0 h-full w-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="{{ $homepageContent['experience_card_2_title'] ?? 'Local exploration' }}"/>
              <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
              <div class="relative z-10 flex h-full flex-col justify-between p-4">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20 backdrop-blur-md text-paper"><i data-lucide="footprints" class="h-4 w-4 text-brass"></i></span>
                <div>
                  <p class="text-base font-semibold text-paper">{{ $homepageContent['experience_card_2_title'] ?? 'Local exploration' }}</p>
                  <p class="mt-1 text-[11px] leading-tight text-paper/75 line-clamp-2">{{ $homepageContent['experience_card_2_desc'] ?? 'Nearby places, easy routes and branch-specific discoveries.' }}</p>
                </div>
              </div>
            </a>
          @else
            <a href="{{ route('nearby.index') }}" class="reveal d2 lift rounded-[20px] bg-white p-4 soft-border block">
              <div class="flex h-[150px] flex-col justify-between">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-mint text-emerald"><i data-lucide="footprints" class="h-4 w-4"></i></span>
                <div>
                  <p class="text-lg font-semibold text-forest">{{ $homepageContent['experience_card_2_title'] ?? 'Local exploration' }}</p>
                  <p class="mt-2 text-[11px] leading-5 text-forest/45">{{ $homepageContent['experience_card_2_desc'] ?? 'Nearby places, easy routes and branch-specific discoveries.' }}</p>
                </div>
              </div>
            </a>
          @endif

          {{-- Card 3: Experiences & activities --}}
          @if(!empty($homepageContent['experience_image_activities']))
            <a href="{{ route('facilities.index') }}" class="reveal d3 lift relative overflow-hidden rounded-[20px] bg-forest text-paper block group h-[160px] md:h-auto min-h-[155px]">
              <img src="{{ $homepageContent['experience_image_activities'] }}" class="absolute inset-0 h-full w-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="{{ $homepageContent['experience_card_3_title'] ?? 'Experiences & activities' }}"/>
              <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
              <div class="relative z-10 flex h-full flex-col justify-between p-4">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20 backdrop-blur-md text-paper"><i data-lucide="sparkles" class="h-4 w-4 text-brass"></i></span>
                <div>
                  <p class="text-base font-semibold text-paper">{{ $homepageContent['experience_card_3_title'] ?? 'Experiences & activities' }}</p>
                  <p class="mt-1 text-[11px] leading-tight text-paper/75 line-clamp-2">{{ $homepageContent['experience_card_3_desc'] ?? 'Things to do, slow days and memorable moments around the stay.' }}</p>
                </div>
              </div>
            </a>
          @else
            <a href="{{ route('facilities.index') }}" class="reveal d3 lift rounded-[20px] bg-paper p-4 soft-border block">
              <div class="flex h-[150px] flex-col justify-between">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-white text-emerald"><i data-lucide="sparkles" class="h-4 w-4"></i></span>
                <div>
                  <p class="text-lg font-semibold text-forest">{{ $homepageContent['experience_card_3_title'] ?? 'Experiences & activities' }}</p>
                  <p class="mt-2 text-[11px] leading-5 text-forest/45">{{ $homepageContent['experience_card_3_desc'] ?? 'Things to do, slow days and memorable moments around the stay.' }}</p>
                </div>
              </div>
            </a>
          @endif

          <article class="reveal d4 lift relative overflow-hidden rounded-[20px] bg-forest text-paper">
            <img src="{{ $homepageContent['experience_image_family'] ?? 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?auto=format&fit=crop&w=900&q=85' }}" class="h-full min-h-[182px] w-full object-cover opacity-55" alt="Krishna family experience"/>
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-4">
              <p class="text-sm font-semibold">{{ $homepageContent['experience_card_4_title'] ?? 'Family moments' }}</p>
              <p class="mt-1 text-[10px] text-paper/55">{{ $homepageContent['experience_card_4_desc'] ?? 'Easy days, shared time and comfortable spaces.' }}</p>
            </div>
          </article>

          {{-- Card 5: Events & celebrations --}}
          @if(!empty($homepageContent['experience_image_events']))
            <a href="{{ route('facilities.index', ['category' => 'events']) }}" class="reveal d1 lift relative overflow-hidden rounded-[20px] bg-forest text-paper block group h-[160px] md:h-auto min-h-[155px]">
              <img src="{{ $homepageContent['experience_image_events'] }}" class="absolute inset-0 h-full w-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="{{ $homepageContent['experience_card_5_title'] ?? 'Events & celebrations' }}"/>
              <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
              <div class="relative z-10 flex h-full flex-col justify-between p-4">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20 backdrop-blur-md text-paper"><i data-lucide="calendar-days" class="h-4 w-4 text-brass"></i></span>
                <div>
                  <p class="text-base font-semibold text-paper">{{ $homepageContent['experience_card_5_title'] ?? 'Events & celebrations' }}</p>
                  <p class="mt-1 text-[11px] leading-tight text-paper/75 line-clamp-2">{{ $homepageContent['experience_card_5_desc'] ?? 'Gatherings, celebrations and special occasions at Krishna.' }}</p>
                </div>
              </div>
            </a>
          @else
            <a href="{{ route('facilities.index', ['category' => 'events']) }}" class="reveal d1 lift rounded-[20px] bg-white p-4 soft-border block">
              <div class="flex h-[150px] flex-col justify-between">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-mint text-emerald"><i data-lucide="calendar-days" class="h-4 w-4"></i></span>
                <div>
                  <p class="text-lg font-semibold text-forest">{{ $homepageContent['experience_card_5_title'] ?? 'Events & celebrations' }}</p>
                  <p class="mt-2 text-[11px] leading-5 text-forest/45">{{ $homepageContent['experience_card_5_desc'] ?? 'Gatherings, celebrations and special occasions at Krishna.' }}</p>
                </div>
              </div>
            </a>
          @endif

          {{-- Card 6: Dining experiences --}}
          @if(!empty($homepageContent['experience_image_dining']))
            <a href="{{ route('dining.index') }}" class="reveal d2 lift relative overflow-hidden rounded-[20px] bg-forest text-paper block group h-[160px] md:h-auto min-h-[155px]">
              <img src="{{ $homepageContent['experience_image_dining'] }}" class="absolute inset-0 h-full w-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="{{ $homepageContent['experience_card_6_title'] ?? 'Dining experiences' }}"/>
              <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
              <div class="relative z-10 flex h-full flex-col justify-between p-4">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20 backdrop-blur-md text-paper"><i data-lucide="utensils-crossed" class="h-4 w-4 text-brass"></i></span>
                <div>
                  <p class="text-base font-semibold text-paper">{{ $homepageContent['experience_card_6_title'] ?? 'Dining experiences' }}</p>
                  <p class="mt-1 text-[11px] leading-tight text-paper/75 line-clamp-2">{{ $homepageContent['experience_card_6_desc'] ?? 'Restaurant, menu browsing and direct food ordering.' }}</p>
                </div>
              </div>
            </a>
          @else
            <a href="{{ route('dining.index') }}" class="reveal d2 lift rounded-[20px] bg-paper p-4 soft-border block">
              <div class="flex h-[150px] flex-col justify-between">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-white text-emerald"><i data-lucide="utensils-crossed" class="h-4 w-4"></i></span>
                <div>
                  <p class="text-lg font-semibold text-forest">{{ $homepageContent['experience_card_6_title'] ?? 'Dining experiences' }}</p>
                  <p class="mt-2 text-[11px] leading-5 text-forest/45">{{ $homepageContent['experience_card_6_desc'] ?? 'Restaurant, menu browsing and direct food ordering.' }}</p>
                </div>
              </div>
            </a>
          @endif
          <button type="button" onclick="openChat()" class="reveal d3 lift rounded-[20px] bg-gradient-to-br from-[#063F34] to-[#0A5243] hover:from-[#053229] hover:to-[#084236] text-paper p-3.5 text-left transition shadow-card flex flex-col justify-between h-[135px] cursor-pointer group">
            <div class="flex items-center justify-between">
              <span class="grid h-8 w-8 place-items-center rounded-xl bg-brass text-forest shadow-xs group-hover:scale-110 transition">
                <i data-lucide="headphones" class="h-4 w-4"></i>
              </span>
              <span class="text-[9px] font-bold uppercase tracking-wider text-brass bg-white/10 px-2 py-0.5 rounded-full">24/7 Desk</span>
            </div>
            <div>
              <p class="text-base font-bold text-paper group-hover:text-brass transition leading-snug">{{ $homepageContent['experience_card_7_title'] ?? 'Concierge & enquiry' }}</p>
              <p class="mt-1 text-[10px] leading-tight text-paper/70 line-clamp-2">{{ $homepageContent['experience_card_7_desc'] ?? 'Ask questions directly while planning or during your stay.' }}</p>
            </div>
          </button>
          <a href="{{ route('nearby.index') }}" class="reveal d4 lift rounded-[20px] bg-emerald p-4 text-paper block">
            <div class="flex h-[150px] flex-col justify-between">
              <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-brass"><i data-lucide="map-pinned" class="h-4 w-4"></i></span>
              <div>
                <p class="text-lg font-semibold">{{ $homepageContent['experience_card_8_title'] ?? 'Around your branch' }}</p>
                <p class="mt-2 text-[11px] leading-5 text-paper/55">{{ $homepageContent['experience_card_8_desc'] ?? 'Discover places nearby, with branch-specific suggestions.' }}</p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- Gallery -->
    <section id="gallery" class="py-10 md:py-14">
      <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
          <div class="reveal">
            <p class="eyebrow text-emerald">{{ $homepageContent['gallery_eyebrow'] ?? 'Visual Journeys' }}</p>
            <h2 class="serif mt-2 text-4xl tracking-[-.04em] text-forest md:text-6xl">{!! $homepageContent['gallery_heading'] ?? 'Cottage Albums &amp; Stillness.' !!}</h2>
          </div>
          <a href="{{ route('gallery.index') }}" class="rounded-xl bg-forest px-4 py-2.5 text-[10px] font-bold text-paper hover:bg-forest/90 transition inline-flex items-center gap-1.5 shadow-xs">
            <span>Explore all albums</span>
            <i data-lucide="arrow-up-right" class="h-3.5 w-3.5 text-brass"></i>
          </a>
        </div>

        <!-- ALBUM ROTATED PHOTO STACKS GRID -->
        @php
          $displayAlbums = (!empty($galleryAlbums) && $galleryAlbums->isNotEmpty()) ? $galleryAlbums->take(3) : collect();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
          @forelse($displayAlbums as $idx => $album)
          @php
            $albumImages = $album->images ?: collect();
            $frontPhoto = $album->cover_image_url ?: ($albumImages->first()?->image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=85');
            $backPhoto1 = $albumImages->skip(1)->first()?->image_url ?: ($albumImages->first()?->image_url ?: 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=800&q=85');
            $backPhoto2 = $albumImages->skip(2)->first()?->image_url ?: ($albumImages->skip(1)->first()?->image_url ?: 'https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?auto=format&fit=crop&w=800&q=85');
            $photoCount = max(3, $albumImages->count() ?: ($album->images_count ?: 3));
          @endphp
          <div class="reveal d{{ ($idx % 3) + 1 }}">
            <a href="{{ route('gallery.index', ['branch_id' => $album->branch_id]) }}" class="group block cursor-pointer">
              <!-- Physical Rotated Photo Stack -->
              <div class="relative w-full aspect-[4/3] mb-6 flex items-center justify-center pt-2">
                <!-- Back Photo 2 (Tilted Left, rotated corner showing behind) -->
                <div class="absolute inset-x-5 inset-y-1 rounded-[22px] overflow-hidden shadow-md -rotate-6 group-hover:-rotate-9 group-hover:-translate-x-3 transition-all duration-500 ease-out bg-white p-1.5 border border-forest/10 opacity-70 group-hover:opacity-90">
                  <img src="{{ $backPhoto2 }}" alt="{{ $album->name }}" class="w-full h-full object-cover rounded-2xl brightness-90">
                </div>

                <!-- Back Photo 1 (Tilted Right, rotated corner showing behind) -->
                <div class="absolute inset-x-5 inset-y-1 rounded-[22px] overflow-hidden shadow-md rotate-6 group-hover:rotate-9 group-hover:translate-x-3 transition-all duration-500 ease-out bg-white p-1.5 border border-forest/10 opacity-80 group-hover:opacity-95">
                  <img src="{{ $backPhoto1 }}" alt="{{ $album->name }}" class="w-full h-full object-cover rounded-2xl brightness-95">
                </div>

                <!-- Front Main Photo (Straight foreground with badge) -->
                <div class="relative z-10 w-full h-full rounded-[22px] overflow-hidden shadow-xl bg-white p-1.5 border border-forest/15 group-hover:scale-[1.02] transition-transform duration-500 ease-out">
                  <div class="relative w-full h-full rounded-2xl overflow-hidden bg-mint">
                    <img src="{{ $frontPhoto }}" alt="{{ $album->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">

                    <!-- Branch / Category Badge -->
                    <div class="absolute top-3 left-3 bg-forest/85 backdrop-blur-md text-paper px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider flex items-center gap-1 shadow-xs">
                      <i data-lucide="image" class="w-3 h-3 text-brass"></i>
                      <span>{{ $album->branch ? $album->branch->name : 'Kerala Retreat' }}</span>
                    </div>

                    <!-- Photo Count Badge -->
                    <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur-md text-forest px-2.5 py-1 rounded-full text-[10px] font-bold shadow-xs flex items-center gap-1">
                      <i data-lucide="layers" class="w-3 h-3 text-emerald"></i>
                      <span>{{ $photoCount }} photos</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Album Text Details -->
              <div class="space-y-1 px-1">
                <div class="flex items-center justify-between">
                  <h3 class="serif text-lg sm:text-xl font-bold text-forest group-hover:text-emerald transition duration-200 line-clamp-1">
                    {{ $album->name }}
                  </h3>
                  <i data-lucide="arrow-up-right" class="w-4 h-4 text-forest/40 group-hover:text-emerald group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition duration-200 shrink-0 ml-2"></i>
                </div>
                <p class="text-xs text-forest/65 leading-relaxed line-clamp-2">
                  {{ $album->description ?: 'Explore curated captures of our plantation villas, verandahs, and mist-covered estate nature.' }}
                </p>
              </div>
            </a>
          </div>
          @empty
          <div class="col-span-3 text-center py-8 text-forest/50 text-xs">
            Gallery albums will appear here.
          </div>
          @endforelse
        </div>
      </div>
    </section>

    <!-- Dining + Spices -->
    <section class="py-10 md:py-14">
      <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        <div class="grid gap-3 lg:grid-cols-2">
          <!-- Dining Card -->
          <div id="dining" class="reveal rounded-[24px] bg-forest p-5 text-paper md:p-7 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between">
                <div>
                  <p class="eyebrow text-paper/45">{{ $homepageContent['dining_eyebrow'] ?? 'Dining' }}</p>
                  <h2 class="serif mt-2 text-4xl">{!! $homepageContent['dining_heading_1'] ?? 'Good food,' !!}<br><span class="italic text-[#E3C789]">{!! $homepageContent['dining_heading_2'] ?? 'close at hand.' !!}</span></h2>
                </div>
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-white/8"><i data-lucide="utensils" class="h-4 w-4 text-brass"></i></div>
              </div>
              <div class="mt-6 grid grid-cols-2 gap-2.5">
                <div class="img-zoom overflow-hidden rounded-[18px]"><img src="{{ $homepageContent['dining_image'] ?? 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=800&q=85' }}" class="h-44 w-full object-cover" alt="Dish"/></div>
                <div class="rounded-[18px] bg-white/6 p-4 ring-1 ring-white/8 flex flex-col justify-between">
                  <div>
                    <p class="eyebrow text-paper/35">Featured</p>
                    <p class="serif mt-2 text-2xl">{{ $homepageContent['dining_title'] ?? 'Plantation Kitchen' }}</p>
                    <p class="mt-2 text-[10px] leading-5 text-paper/50">{{ $homepageContent['dining_description'] ?? 'Seasonal dishes, clay pot preparations and direct food ordering.' }}</p>
                  </div>
                  <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('dining.index') }}" class="rounded-xl bg-brass px-3 py-2 text-[10px] font-bold text-forest hover:brightness-105 transition">Browse menu</a>
                    <a href="{{ route('dining.index') }}" class="rounded-xl border border-white/12 bg-white/5 px-3 py-2 text-[10px] font-bold text-paper hover:bg-white/10 transition">Order food</a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Spices Card -->
          <div id="spices" class="reveal d1 rounded-[24px] bg-paper p-5 soft-border md:p-7 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between">
                <div>
                  <p class="eyebrow text-emerald">{{ $homepageContent['spices_eyebrow'] ?? 'Krishna Spices' }}</p>
                  <h2 class="serif mt-2 text-4xl text-forest">{!! $homepageContent['spices_heading_1'] ?? 'Take a little' !!}<br><span class="italic text-emerald">{!! $homepageContent['spices_heading_2'] ?? 'Krishna home.' !!}</span></h2>
                </div>
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-white text-emerald"><i data-lucide="shopping-bag" class="h-4 w-4"></i></div>
              </div>
              <div class="mt-6 grid grid-cols-3 gap-2.5">
                <a href="{{ route('spices.index') }}" class="rounded-[18px] bg-white p-3 soft-border block group hover:shadow-card transition">
                  <div class="img-zoom overflow-hidden rounded-xl"><img src="{{ $pinnedSp1->image_url ?? 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=500&q=85' }}" class="h-32 w-full object-cover" alt="{{ $pinnedSp1->name ?? 'Signature spice' }}"/></div>
                  <p class="mt-3 text-[11px] font-bold text-forest group-hover:text-emerald transition line-clamp-1">{{ $pinnedSp1->name ?? 'Signature spice' }}</p>
                  <p class="mt-1 text-[9px] text-forest/45">₹ {{ number_format($pinnedSp1->price ?? 320, 0) }}</p>
                </a>
                <a href="{{ route('spices.index') }}" class="rounded-[18px] bg-white p-3 soft-border block group hover:shadow-card transition">
                  <div class="img-zoom overflow-hidden rounded-xl"><img src="{{ $pinnedSp2->image_url ?? 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?auto=format&fit=crop&w=500&q=85' }}" class="h-32 w-full object-cover" alt="{{ $pinnedSp2->name ?? 'Daily blend' }}"/></div>
                  <p class="mt-3 text-[11px] font-bold text-forest group-hover:text-emerald transition line-clamp-1">{{ $pinnedSp2->name ?? 'Daily blend' }}</p>
                  <p class="mt-1 text-[9px] text-forest/45">₹ {{ number_format($pinnedSp2->price ?? 280, 0) }}</p>
                </a>
                <a href="{{ route('spices.index') }}" class="flex flex-col justify-between rounded-[18px] bg-emerald p-3 text-left text-paper group hover:bg-forest transition">
                  <i data-lucide="arrow-up-right" class="ml-auto h-4 w-4 text-brass group-hover:translate-x-0.5 transition"></i>
                  <div>
                    <p class="text-[11px] font-bold">Shop all</p>
                    <p class="mt-1 text-[9px] leading-4 text-paper/55">Explore the full Krishna spice collection.</p>
                    <span class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-2.5 py-1.5 text-[9px] font-semibold"><i data-lucide="shopping-cart" class="h-3 w-3"></i> View shop</span>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Locations + Review -->
    <section id="nearby" class="py-10 md:py-14">
      <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        <div id="reviews" class="grid gap-3 lg:grid-cols-12">
          <div class="reveal rounded-[24px] bg-white p-5 soft-border lg:col-span-7 md:p-7">
            <div class="flex items-center justify-between">
              <div>
                <p class="eyebrow text-emerald">{{ $homepageContent['locations_eyebrow'] ?? 'Kerala Sanctuaries' }}</p>
                <h2 class="serif mt-2 text-4xl text-forest">{!! $homepageContent['locations_heading_1'] ?? 'Our Locations' !!}<br><span class="italic text-emerald">{!! $homepageContent['locations_heading_2'] ?? 'Visit our retreats.' !!}</span></h2>
              </div>
              <a href="{{ route('rooms.index') }}" class="rounded-full bg-mint px-3.5 py-1.5 text-[9px] font-bold text-emerald hover:bg-emerald hover:text-white transition flex items-center gap-1 shadow-xs">
                <i data-lucide="map-pin" class="w-3 h-3"></i>
                <span>{{ $branches->count() }} Destinations</span>
              </a>
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-3">
              @foreach($branches as $b)
              @php
                $mapsUrl = $b->google_maps_url ?: ('https://www.google.com/maps/search/?api=1&query=' . urlencode($b->name . ' ' . $b->city . ' Kerala India'));
                $branchImg = $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80');
              @endphp
              <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="group relative overflow-hidden rounded-[20px] bg-forest text-paper h-[210px] sm:h-[225px] flex flex-col justify-between p-3.5 soft-border shadow-card lift transition duration-300 block">
                <!-- Branch Background Photo -->
                <img src="{{ $branchImg }}" alt="{{ $b->name }}" class="absolute inset-0 h-full w-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out brightness-90">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                <!-- Top Badge: Location & Maps External Icon -->
                <div class="relative z-10 flex items-center justify-between">
                  <span class="bg-forest/85 backdrop-blur-md px-2.5 py-1 rounded-full text-[9px] font-bold text-paper uppercase tracking-wider flex items-center gap-1 shadow-xs">
                    <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i>
                    <span>{{ $b->city }}</span>
                  </span>
                  <span class="grid h-7 w-7 place-items-center rounded-full bg-white/20 backdrop-blur-md text-white group-hover:bg-brass group-hover:text-forest transition shadow-xs" title="Open Google Maps">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                  </span>
                </div>

                <!-- Bottom Content: Branch Name & Google Maps Link -->
                <div class="relative z-10 space-y-1">
                  <h4 class="serif text-base font-bold text-white group-hover:text-brass transition leading-snug">
                    {{ $b->name }}
                  </h4>
                  <div class="flex items-center gap-1 text-[10px] text-paper/80 font-medium">
                    <span class="underline group-hover:text-brass transition">Google Maps</span>
                    <i data-lucide="arrow-up-right" class="w-3 h-3 text-brass"></i>
                  </div>
                </div>
              </a>
              @endforeach
            </div>
          </div>
          @php
            $activeTestimonials = (isset($testimonials) && $testimonials->isNotEmpty()) 
              ? $testimonials->where('is_active', true) 
              : collect([
                  (object)[
                    'guest_name' => $homepageContent['review_author'] ?? 'Ananya & Ravi',
                    'stay_title' => $homepageContent['review_subtitle'] ?? 'Garden Residence · Munnar Retreat',
                    'quote' => $homepageContent['review_quote'] ?? 'Everything felt easy — from the room to dinner to knowing what was nearby.',
                    'rating' => 5,
                    'avatar_url' => null,
                  ]
                ]);
            if ($activeTestimonials->isEmpty()) {
              $activeTestimonials = collect([
                (object)[
                  'guest_name' => 'Ananya & Ravi',
                  'stay_title' => 'Garden Residence · Munnar Retreat',
                  'quote' => 'Everything felt easy — from the room to dinner to knowing what was nearby.',
                  'rating' => 5,
                  'avatar_url' => null,
                ]
              ]);
            }
          @endphp

          <!-- Dynamic Sliding Testimonials Carousel -->
          <div id="testimonialsCarousel" class="reveal d1 rounded-[24px] bg-mint p-5 lg:col-span-5 md:p-7 flex flex-col justify-between relative overflow-hidden group select-none shadow-sm" style="min-height: 320px;">
            <!-- Top Controls: Header + Slide Counter + Prev/Next Buttons -->
            <div class="flex items-center justify-between z-10">
              <div class="flex items-center gap-2">
                <span class="eyebrow text-emerald">{{ $homepageContent['review_eyebrow'] ?? 'Verified guest note' }}</span>
                <span class="text-[10px] font-bold text-forest/50 bg-forest/5 px-2 py-0.5 rounded-full" id="testimonialCounter">
                  1 / {{ $activeTestimonials->count() }}
                </span>
              </div>
              <div class="flex items-center gap-1.5">
                <button type="button" onclick="slidePrevTestimonial()" class="grid h-7 w-7 place-items-center rounded-lg bg-white/70 hover:bg-forest hover:text-white text-forest transition shadow-2xs cursor-pointer touch-tap" aria-label="Previous Testimonial">
                  <i data-lucide="chevron-left" class="h-4 w-4"></i>
                </button>
                <button type="button" onclick="slideNextTestimonial()" class="grid h-7 w-7 place-items-center rounded-lg bg-white/70 hover:bg-forest hover:text-white text-forest transition shadow-2xs cursor-pointer touch-tap" aria-label="Next Testimonial">
                  <i data-lucide="chevron-right" class="h-4 w-4"></i>
                </button>
              </div>
            </div>

            <!-- Slides Track Viewport -->
            <div class="overflow-hidden w-full my-auto py-4 relative" id="testimonialSwipeArea">
              <div id="testimonialTrack" class="flex transition-transform duration-500 ease-out" style="transform: translateX(0%);">
                @foreach($activeTestimonials as $t)
                  <div class="testimonial-slide w-full shrink-0 flex flex-col justify-between pr-1">
                    <div class="space-y-3">
                      <div class="flex items-center gap-1 text-brass text-sm">
                        @for($i = 1; $i <= ($t->rating ?? 5); $i++)
                          <span>★</span>
                        @endfor
                      </div>
                      <p class="serif text-2xl md:text-3xl leading-snug text-forest">
                        “{{ $t->quote }}”
                      </p>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <!-- Bottom: Author Info + Pagination Dots + Read Reviews CTA -->
            <div class="pt-4 border-t border-forest/10 flex items-center justify-between z-10">
              <div class="flex items-center gap-3">
                <div id="testimonialAuthorContainer">
                  @foreach($activeTestimonials as $idx => $t)
                    <div class="testimonial-author-card {{ $loop->first ? 'block' : 'hidden' }}" data-index="{{ $idx }}">
                      <p class="text-sm font-bold text-forest">{{ $t->guest_name }}</p>
                      <p class="mt-0.5 text-[9.5px] text-forest/50 font-medium">{{ $t->stay_title }}</p>
                    </div>
                  @endforeach
                </div>
              </div>

              <!-- Pagination Dots -->
              <div class="flex items-center gap-1.5">
                @foreach($activeTestimonials as $idx => $t)
                  <button type="button" onclick="goToTestimonialSlide({{ $idx }})" class="testimonial-dot h-1.5 rounded-full transition-all duration-300 {{ $loop->first ? 'w-5 bg-forest' : 'w-1.5 bg-forest/20' }}" aria-label="Slide {{ $idx + 1 }}"></button>
                @endforeach
              </div>

              <a href="{{ route('rooms.index') }}#reviews" class="rounded-xl bg-white px-3 py-2 text-[9px] font-bold text-forest hover:bg-forest hover:text-white transition shadow-2xs">Read reviews</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="py-10 pb-28 md:py-16 md:pb-20">
      <div class="mx-auto max-w-[1480px] px-4 md:px-6 lg:px-8">
        <div class="reveal overflow-hidden rounded-[28px] bg-forest p-6 text-paper md:p-10 lg:p-12">
          <div class="grid gap-8 lg:grid-cols-[1.2fr_.8fr] lg:items-end">
            <div>
              <p class="eyebrow text-brass">{{ $homepageContent['cta_eyebrow'] ?? 'Your next Krishna moment' }}</p>
              <h2 class="serif mt-3 max-w-3xl text-5xl leading-[.9] tracking-[-.04em] md:text-7xl">{!! $homepageContent['cta_heading_1'] ?? 'Stay for the place.' !!}<br><span class="italic text-[#E3C789]">{!! $homepageContent['cta_heading_2'] ?? 'Remember the feeling.' !!}</span></h2>
              <p class="mt-5 max-w-xl text-sm leading-6 text-paper/55">{{ $homepageContent['cta_description'] ?? 'Choose a branch, check your dates, discover what is around you and talk directly to Krishna whenever you need.' }}</p>
            </div>
            <div class="grid gap-2.5 sm:grid-cols-2">
              <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center rounded-xl bg-paper px-4 py-3.5 text-[11px] font-bold text-forest hover:bg-white transition">Book your stay</a>
              <button type="button" onclick="openChat()" class="inline-flex items-center justify-center rounded-xl border border-paper/15 bg-white/5 px-4 py-3.5 text-[11px] font-bold text-paper hover:bg-white/10 transition">Chat with Concierge</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- SHARED LUXURY EDITORIAL FOOTER (VISUAL LIVE EDITOR POWERED) -->
  <x-footer class="mt-4" />

  <!-- Mobile floating app navigation -->
  <div class="mobile-only fixed bottom-3 left-3 right-3 z-50">
    <div class="mobile-shell border border-forest/10 bg-paper/92 p-1.5 backdrop-blur-xl">
      <div class="grid grid-cols-5 gap-1">
        <a href="{{ route('home') }}" class="rounded-xl px-2 py-2 text-center bg-white/80 text-forest font-bold shadow-xs">
          <i data-lucide="compass" class="mx-auto h-4 w-4 text-forest stroke-[2.5]"></i>
          <span class="mt-1 block text-[8px]">Explore</span>
        </a>
        <a href="{{ route('rooms.index') }}" class="rounded-xl px-2 py-2 text-center text-forest/60 hover:text-forest">
          <i data-lucide="bed-double" class="mx-auto h-4 w-4"></i>
          <span class="mt-1 block text-[8px]">Stay</span>
        </a>
        <a href="{{ route('dining.index') }}" class="rounded-xl px-2 py-2 text-center text-forest/60 hover:text-forest">
          <i data-lucide="utensils" class="mx-auto h-4 w-4"></i>
          <span class="mt-1 block text-[8px]">Dining</span>
        </a>
        <a href="{{ route('spices.index') }}" class="rounded-xl px-2 py-2 text-center text-forest/60 hover:text-forest">
          <i data-lucide="leaf" class="mx-auto h-4 w-4"></i>
          <span class="mt-1 block text-[8px]">Spices</span>
        </a>
        @guest
        <a href="{{ route('login') }}" class="rounded-xl px-2 py-2 text-center text-forest/60 hover:text-forest">
          <i data-lucide="user" class="mx-auto h-4 w-4"></i>
          <span class="mt-1 block text-[8px]">Login</span>
        </a>
        @else
        <button onclick="openChat()" class="rounded-xl px-2 py-2 text-center text-forest/60 hover:text-forest">
          <i data-lucide="message-circle" class="mx-auto h-4 w-4"></i>
          <span class="mt-1 block text-[8px]">Chat</span>
        </button>
        @endguest
      </div>
    </div>
  </div>

  <!-- Mobile Slide-Over Drawer -->
  <div id="mobileNav" class="fixed inset-0 z-[80] hidden bg-forest/35 p-3 backdrop-blur-md">
    <div class="drawer-panel ml-auto flex h-full w-full max-w-[440px] flex-col rounded-[28px] bg-paper p-4 text-forest shadow-[0_30px_100px_rgba(0,0,0,.22)] md:p-5">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <span class="grid h-10 w-10 place-items-center rounded-xl bg-forest text-sm font-bold text-paper">K</span>
          <div>
            <p class="text-sm font-semibold">Krishna Cottages</p>
            <p class="mt-0.5 text-[9px] uppercase tracking-[.18em] text-forest/40">Luxury Cottages of Kerala</p>
          </div>
        </div>
        <button id="closeMenu" class="grid h-10 w-10 place-items-center rounded-xl bg-white text-forest ring-1 ring-forest/10" aria-label="Close menu">
          <i data-lucide="x" class="h-5 w-5"></i>
        </button>
      </div>

      <div class="mt-6">
        <div class="flex items-center justify-between">
          <p class="eyebrow text-forest/40">Navigation</p>
          <span class="text-[9px] font-semibold text-emerald">01 — 08</span>
        </div>
        <nav class="mt-4 grid gap-1">
          <a class="drawer-link rounded-2xl px-3 py-3 text-xl serif hover:bg-white flex items-center justify-between transition {{ request()->routeIs('home') ? 'bg-mint text-emerald font-bold border-l-4 border-emerald' : 'text-forest' }}" href="{{ route('home') }}">
            <span class="flex items-center"><span class="mr-3 text-[11px] font-sans {{ request()->routeIs('home') ? 'text-emerald font-bold' : 'text-forest/40' }}">01</span>Home</span>
            <i data-lucide="arrow-right" class="w-4 h-4 {{ request()->routeIs('home') ? 'text-emerald' : 'text-forest/30' }}"></i>
          </a>
          <a class="drawer-link rounded-2xl px-3 py-3 text-xl serif hover:bg-white flex items-center justify-between transition {{ request()->routeIs('rooms.*') || request()->routeIs('booking.*') ? 'bg-mint text-emerald font-bold border-l-4 border-emerald' : 'text-forest' }}" href="{{ route('rooms.index') }}">
            <span class="flex items-center"><span class="mr-3 text-[11px] font-sans {{ request()->routeIs('rooms.*') ? 'text-emerald font-bold' : 'text-forest/40' }}">02</span>Stay</span>
            <i data-lucide="arrow-right" class="w-4 h-4 {{ request()->routeIs('rooms.*') ? 'text-emerald' : 'text-forest/30' }}"></i>
          </a>
          <a class="drawer-link rounded-2xl px-3 py-3 text-xl serif hover:bg-white flex items-center justify-between transition {{ request()->routeIs('dining.*') ? 'bg-mint text-emerald font-bold border-l-4 border-emerald' : 'text-forest' }}" href="{{ route('dining.index') }}">
            <span class="flex items-center"><span class="mr-3 text-[11px] font-sans {{ request()->routeIs('dining.*') ? 'text-emerald font-bold' : 'text-forest/40' }}">03</span>Dining</span>
            <i data-lucide="arrow-right" class="w-4 h-4 {{ request()->routeIs('dining.*') ? 'text-emerald' : 'text-forest/30' }}"></i>
          </a>
          <a class="drawer-link rounded-2xl px-3 py-3 text-xl serif hover:bg-white flex items-center justify-between transition {{ request()->routeIs('spices.*') ? 'bg-mint text-emerald font-bold border-l-4 border-emerald' : 'text-forest' }}" href="{{ route('spices.index') }}">
            <span class="flex items-center"><span class="mr-3 text-[11px] font-sans {{ request()->routeIs('spices.*') ? 'text-emerald font-bold' : 'text-forest/40' }}">04</span>Krishna Spices</span>
            <i data-lucide="arrow-right" class="w-4 h-4 {{ request()->routeIs('spices.*') ? 'text-emerald' : 'text-forest/30' }}"></i>
          </a>
          <a class="drawer-link rounded-2xl px-3 py-3 text-xl serif hover:bg-white flex items-center justify-between transition {{ request()->routeIs('facilities.*') ? 'bg-mint text-emerald font-bold border-l-4 border-emerald' : 'text-forest' }}" href="{{ route('facilities.index') }}">
            <span class="flex items-center"><span class="mr-3 text-[11px] font-sans {{ request()->routeIs('facilities.*') ? 'text-emerald font-bold' : 'text-forest/40' }}">05</span>Facilities</span>
            <i data-lucide="arrow-right" class="w-4 h-4 {{ request()->routeIs('facilities.*') ? 'text-emerald' : 'text-forest/30' }}"></i>
          </a>
          <a class="drawer-link rounded-2xl px-3 py-3 text-xl serif hover:bg-white flex items-center justify-between transition {{ request()->routeIs('gallery.*') ? 'bg-mint text-emerald font-bold border-l-4 border-emerald' : 'text-forest' }}" href="{{ route('gallery.index') }}">
            <span class="flex items-center"><span class="mr-3 text-[11px] font-sans {{ request()->routeIs('gallery.*') ? 'text-emerald font-bold' : 'text-forest/40' }}">06</span>Gallery</span>
            <i data-lucide="arrow-right" class="w-4 h-4 {{ request()->routeIs('gallery.*') ? 'text-emerald' : 'text-forest/30' }}"></i>
          </a>
          <a class="drawer-link rounded-2xl px-3 py-3 text-xl serif hover:bg-white flex items-center justify-between transition {{ request()->routeIs('nearby.*') ? 'bg-mint text-emerald font-bold border-l-4 border-emerald' : 'text-forest' }}" href="{{ route('nearby.index') }}">
            <span class="flex items-center"><span class="mr-3 text-[11px] font-sans {{ request()->routeIs('nearby.*') ? 'text-emerald font-bold' : 'text-forest/40' }}">07</span>Discover</span>
            <i data-lucide="arrow-right" class="w-4 h-4 {{ request()->routeIs('nearby.*') ? 'text-emerald' : 'text-forest/30' }}"></i>
          </a>
          <a class="drawer-link rounded-2xl px-3 py-3 text-xl serif hover:bg-white flex items-center justify-between transition {{ request()->routeIs('contact.*') ? 'bg-mint text-emerald font-bold border-l-4 border-emerald' : 'text-forest' }}" href="{{ route('contact.index') }}">
            <span class="flex items-center"><span class="mr-3 text-[11px] font-sans {{ request()->routeIs('contact.*') ? 'text-emerald font-bold' : 'text-forest/40' }}">08</span>Contact</span>
            <i data-lucide="arrow-right" class="w-4 h-4 {{ request()->routeIs('contact.*') ? 'text-emerald' : 'text-forest/30' }}"></i>
          </a>
        </nav>
      </div>

      <div class="mt-auto pt-4">
        <div class="grid grid-cols-2 gap-2">
          <button onclick="openChat();closeMobileNav()" class="rounded-2xl border border-forest/10 bg-white p-3 text-left">
            <i data-lucide="message-circle" class="h-4 w-4 text-emerald"></i>
            <p class="mt-2 text-[11px] font-bold">Concierge Chat</p>
            <p class="mt-0.5 text-[9px] text-forest/45">Direct enquiry</p>
          </button>
          @guest
          <a href="{{ route('login', ['redirect' => route('customer.dashboard')]) }}" class="rounded-2xl border border-forest/10 bg-white p-3 text-left">
            <i data-lucide="user" class="h-4 w-4 text-emerald"></i>
            <p class="mt-2 text-[11px] font-bold">Sign In</p>
            <p class="mt-0.5 text-[9px] text-forest/45">Guest Portal</p>
          </a>
          @else
          <a href="{{ route('customer.dashboard') }}" class="rounded-2xl border border-forest/10 bg-white p-3 text-left">
            <div class="flex items-center justify-between">
              <i data-lucide="user-check" class="h-4 w-4 text-emerald"></i>
              <span class="text-[8px] bg-mint text-emerald font-bold px-1.5 py-0.5 rounded uppercase">Pass</span>
            </div>
            <p class="mt-2 text-[11px] font-bold">My Dashboard</p>
            <p class="mt-0.5 text-[9px] text-forest/45">Stay & Orders</p>
          </a>
          @endguest
        </div>
        <a href="{{ route('rooms.index') }}" class="mt-2.5 flex w-full items-center justify-between rounded-2xl bg-forest px-4 py-3.5 text-[11px] font-bold text-paper shadow-card">
          <span>Book Stay</span>
          <i data-lucide="arrow-up-right" class="h-4 w-4 text-brass"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- DIRECT CONCIERGE LIVE MESSAGING PLATFORM -->
  <div id="conciergeModal" class="fixed inset-0 z-[110] hidden bg-forest/45 backdrop-blur-sm transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-paper border border-forest/15 rounded-t-[28px] sm:rounded-[26px] shadow-2xl w-full max-w-lg overflow-hidden transform transition-all h-[90vh] sm:h-[620px] flex flex-col">
      <!-- Header -->
      <div class="bg-forest text-paper p-4 sm:p-4.5 flex items-center justify-between shrink-0 shadow-xs">
        <div class="flex items-center gap-3">
          <div class="relative">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-brass">
              <i data-lucide="message-circle" class="w-5 h-5"></i>
            </span>
            <span class="absolute -top-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-400 ring-2 ring-forest animate-pulse"></span>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h3 class="serif text-base sm:text-lg font-bold">Cottage Concierge Desk</h3>
              <span class="px-2 py-0.5 rounded-full text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold uppercase tracking-wider">Live</span>
            </div>
            <p class="text-[10px] text-paper/60 flex items-center gap-1.5 mt-0.5">
              <span>Direct guest messaging &middot; Ticket: <strong id="chatTicketNumber" class="text-brass font-mono">Connecting...</strong></span>
            </p>
          </div>
        </div>
        <button onclick="closeChat()" class="h-8 w-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-paper transition">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <!-- Active Stay Concierge Banner (Dynamic) -->
      <div id="activeStayBanner" class="hidden bg-[#0B5D4B] text-paper px-4 py-2.5 border-b border-emerald-500/30 flex items-center justify-between gap-3 text-xs shrink-0 shadow-xs">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="h-7 w-7 rounded-lg bg-white/10 flex items-center justify-center text-brass shrink-0">
            <i data-lucide="key" class="w-4 h-4"></i>
          </div>
          <div class="truncate">
            <div class="flex items-center gap-2">
              <span id="stayBadge" class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-400 text-emerald-950 uppercase tracking-wider">Checked In</span>
              <span id="stayBranch" class="font-bold text-paper text-xs truncate">Cottage Branch</span>
            </div>
            <p class="text-[10px] text-paper/80 mt-0.5 truncate" id="stayRoomAndDates">
              Room 101 &middot; Stays
            </p>
          </div>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
          <span class="text-[10px] font-mono text-brass font-bold bg-black/25 px-2 py-1 rounded border border-brass/30" id="stayBookingCode">KR-1234</span>
          <a id="stayBranchCallLink" href="#" class="hidden px-2 py-1 rounded bg-emerald-400 text-emerald-950 hover:bg-white text-[10px] font-bold items-center gap-1 transition shadow-xs" title="Call Branch Front Desk">
            <i data-lucide="phone-call" class="w-3 h-3"></i>
            <span id="stayBranchCallLabel">Call Desk</span>
          </a>
        </div>
      </div>

      <!-- Guest Mini-Profile Bar (if unauthenticated) -->
      @guest
      <div id="guestProfileBar" class="bg-white/80 border-b border-forest/10 px-4 py-2 flex items-center justify-between gap-3 text-xs shrink-0">
        <div class="flex items-center gap-2 text-forest/70 truncate">
          <i data-lucide="user" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
          <span id="guestDisplayLabel" class="truncate font-medium">Guest Visitor</span>
        </div>
        <button onclick="toggleGuestDetailsPrompt()" class="text-[10px] text-forest font-bold underline hover:text-emerald shrink-0">
          Update Contact
        </button>
      </div>
      <div id="guestDetailsPrompt" class="hidden bg-white px-4 py-3 border-b border-forest/15 space-y-2 text-xs shrink-0">
        <div class="grid grid-cols-2 gap-2">
          <input type="text" id="guestInputName" placeholder="Your Name" class="p-2 border border-forest/15 rounded-lg bg-paper/50 text-xs text-forest">
          <input type="tel" id="guestInputPhone" placeholder="WhatsApp / Phone" class="p-2 border border-forest/15 rounded-lg bg-paper/50 text-xs text-forest">
        </div>
        <div class="flex items-center justify-end gap-2">
          <button type="button" onclick="saveGuestContactDetails()" class="px-3 py-1 bg-forest text-paper rounded-lg text-xs font-bold">Save Info</button>
        </div>
      </div>
      @endguest

      <!-- Messages Stream Container -->
      <div id="chatMessagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#F7F5EE]">
        <div id="chatLoadingSpinner" class="flex items-center justify-center py-8 text-xs text-forest/40 gap-2">
          <div class="w-4 h-4 rounded-full border-2 border-forest/30 border-t-forest animate-spin"></div>
          <span>Connecting to Concierge Desk...</span>
        </div>
      </div>

      <!-- Multi-Room Target Selector (Shown if multiple rooms booked) -->
      <div id="roomTargetChipsBar" class="hidden px-3 py-1.5 bg-[#FAF8F5] border-t border-forest/10 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0 text-xs">
        <span class="text-[9px] uppercase font-bold text-forest/50 tracking-wider shrink-0 mr-1 flex items-center gap-1">
          <i data-lucide="door-open" class="w-3 h-3 text-forest/60"></i> Room:
        </span>
        <div id="roomTargetChipsContainer" class="flex items-center gap-1.5">
          <!-- Injected dynamically via JS -->
        </div>
      </div>

      <!-- Quick Suggestions Pills -->
      <div id="chatQuickPillsContainer" class="px-3 py-2 bg-white/70 border-t border-forest/10 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0">
        <span class="text-[9px] uppercase font-bold text-forest/40 tracking-wider shrink-0 mr-1">Quick:</span>
        <div id="quickPillsList" class="flex items-center gap-1.5">
          <button type="button" onclick="sendQuickPrompt('Where all do you have cottage branches?')" class="text-[10px] whitespace-nowrap bg-paper hover:bg-white text-forest px-2.5 py-1 rounded-full border border-forest/15 font-semibold transition">
            📍 Where are your branches?
          </button>
          <button type="button" onclick="sendQuickPrompt('Can you share room details and availability?')" class="text-[10px] whitespace-nowrap bg-paper hover:bg-white text-forest px-2.5 py-1 rounded-full border border-forest/15 font-semibold transition">
            🏨 Room Details & Rates
          </button>
          <button type="button" onclick="sendQuickPrompt('What spices can I purchase from the farm?')" class="text-[10px] whitespace-nowrap bg-paper hover:bg-white text-forest px-2.5 py-1 rounded-full border border-forest/15 font-semibold transition">
            🌿 Farm Spices
          </button>
          <button type="button" onclick="sendQuickPrompt('What are the Ayurvedic dining options?')" class="text-[10px] whitespace-nowrap bg-paper hover:bg-white text-forest px-2.5 py-1 rounded-full border border-forest/15 font-semibold transition">
            🍲 Dining Options
          </button>
        </div>
      </div>

      <!-- Input Bar -->
      <div class="p-3 sm:p-3.5 bg-white border-t border-forest/15 shrink-0">
        <form id="chatMessageForm" onsubmit="handleSendChatMessage(event)" class="flex items-center gap-2">
          <input type="text" id="chatTextInput" autocomplete="off" placeholder="Type message to cottage manager..." class="flex-1 text-xs py-2.5 px-3.5 rounded-xl border border-forest/15 bg-paper/60 text-forest placeholder-forest/40 focus:outline-none focus:ring-1 focus:ring-forest">
          <button type="submit" id="chatSendBtn" class="h-9 w-9 rounded-xl bg-forest hover:bg-emerald text-paper flex items-center justify-center shadow-card transition shrink-0">
            <i data-lucide="send" class="w-4 h-4 text-brass"></i>
          </button>
        </form>
      </div>
    </div>
  </div>

  <div id="lightbox" class="fixed inset-0 z-[100] hidden bg-black/90 p-4"><button onclick="closeLightbox()" class="absolute right-4 top-4 z-10 grid h-11 w-11 place-items-center rounded-full bg-white/10 text-white"><i data-lucide="x" class="h-5 w-5"></i></button><div class="grid h-full place-items-center"><img id="lightboxImg" src="" alt="Gallery preview" class="max-h-[88vh] max-w-[92vw] rounded-[20px] object-contain"></div></div>
  <div id="toast" class="pointer-events-none fixed left-1/2 top-5 z-[120] hidden -translate-x-1/2 rounded-xl bg-forest px-4 py-3 text-[11px] font-bold text-paper shadow-float"></div>

  <script>
    function refreshIcons() {
      if (window.lucide) lucide.createIcons();
      else if (window.createIcons) window.createIcons();
    }
    refreshIcons();

    /* =========================================================================
       HERO ANIMATED BRANCH CAROUSEL ENGINE
       ========================================================================= */
    let currentHeroSlide = 0;
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroDots = document.querySelectorAll('.hero-carousel-dot');
    const totalHeroSlides = heroSlides.length;
    let heroAutoPlayTimer = null;

    function showHeroSlide(index) {
      if (totalHeroSlides === 0) return;
      currentHeroSlide = (index + totalHeroSlides) % totalHeroSlides;

      heroSlides.forEach((slide, idx) => {
        if (idx === currentHeroSlide) {
          slide.classList.remove('opacity-0', 'scale-95', 'pointer-events-none', 'z-0');
          slide.classList.add('opacity-100', 'scale-100', 'z-10');
        } else {
          slide.classList.remove('opacity-100', 'scale-100', 'z-10');
          slide.classList.add('opacity-0', 'scale-95', 'pointer-events-none', 'z-0');
        }
      });

      heroDots.forEach((dot, idx) => {
        if (idx === currentHeroSlide) {
          dot.classList.remove('w-1.5', 'bg-white/40');
          dot.classList.add('w-5', 'bg-brass');
        } else {
          dot.classList.remove('w-5', 'bg-brass');
          dot.classList.add('w-1.5', 'bg-white/40');
        }
      });
      refreshIcons();
    }

    function nextHeroSlide() {
      showHeroSlide(currentHeroSlide + 1);
    }

    function prevHeroSlide() {
      showHeroSlide(currentHeroSlide - 1);
    }

    function goToHeroSlide(index) {
      showHeroSlide(index);
      restartHeroAutoPlay();
    }

    function startHeroAutoPlay() {
      if (heroAutoPlayTimer || totalHeroSlides <= 1) return;
      heroAutoPlayTimer = setInterval(nextHeroSlide, 5000);
    }

    function stopHeroAutoPlay() {
      if (heroAutoPlayTimer) {
        clearInterval(heroAutoPlayTimer);
        heroAutoPlayTimer = null;
      }
    }

    function restartHeroAutoPlay() {
      stopHeroAutoPlay();
      startHeroAutoPlay();
    }

    // Hover & touch event listeners for carousel
    const carouselEl = document.getElementById('hero-carousel');
    if (carouselEl) {
      carouselEl.addEventListener('mouseenter', stopHeroAutoPlay);
      carouselEl.addEventListener('mouseleave', startHeroAutoPlay);

      // Mobile Touch Swiping
      let touchStartX = 0;
      let touchEndX = 0;

      carouselEl.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        stopHeroAutoPlay();
      }, { passive: true });

      carouselEl.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        if (touchStartX - touchEndX > 45) {
          nextHeroSlide(); // Swipe left -> next
        } else if (touchEndX - touchStartX > 45) {
          prevHeroSlide(); // Swipe right -> prev
        }
        startHeroAutoPlay();
      }, { passive: true });
    }

    // Initialize auto-play
    startHeroAutoPlay();

    /* =========================================================================
       DYNAMIC TESTIMONIALS SLIDING CAROUSEL ENGINE
       ========================================================================= */
    let currentTestimonialSlide = 0;
    const testimonialSlides = document.querySelectorAll('.testimonial-slide');
    const testimonialAuthors = document.querySelectorAll('.testimonial-author-card');
    const testimonialDots = document.querySelectorAll('.testimonial-dot');
    const totalTestimonialSlides = testimonialSlides.length;
    const testimonialTrack = document.getElementById('testimonialTrack');
    const testimonialCounter = document.getElementById('testimonialCounter');
    let testimonialAutoPlayTimer = null;

    function goToTestimonialSlide(index) {
      if (totalTestimonialSlides === 0) return;
      currentTestimonialSlide = (index + totalTestimonialSlides) % totalTestimonialSlides;

      if (testimonialTrack) {
        testimonialTrack.style.transform = `translateX(-${currentTestimonialSlide * 100}%)`;
      }

      testimonialAuthors.forEach((card, idx) => {
        if (idx === currentTestimonialSlide) {
          card.classList.remove('hidden');
          card.classList.add('block');
        } else {
          card.classList.add('hidden');
          card.classList.remove('block');
        }
      });

      testimonialDots.forEach((dot, idx) => {
        if (idx === currentTestimonialSlide) {
          dot.className = 'testimonial-dot h-1.5 rounded-full transition-all duration-300 w-5 bg-forest';
        } else {
          dot.className = 'testimonial-dot h-1.5 rounded-full transition-all duration-300 w-1.5 bg-forest/20';
        }
      });

      if (testimonialCounter) {
        testimonialCounter.textContent = `${currentTestimonialSlide + 1} / ${totalTestimonialSlides}`;
      }
    }

    function slideNextTestimonial() {
      goToTestimonialSlide(currentTestimonialSlide + 1);
    }

    function slidePrevTestimonial() {
      goToTestimonialSlide(currentTestimonialSlide - 1);
    }

    function startTestimonialAutoPlay() {
      if (testimonialAutoPlayTimer || totalTestimonialSlides <= 1) return;
      testimonialAutoPlayTimer = setInterval(slideNextTestimonial, 6000);
    }

    function pauseTestimonialAutoPlay() {
      if (testimonialAutoPlayTimer) {
        clearInterval(testimonialAutoPlayTimer);
        testimonialAutoPlayTimer = null;
      }
    }

    // Touch Swipe & Hover listeners for Testimonials Carousel
    const testimonialCarousel = document.getElementById('testimonialsCarousel');
    if (testimonialCarousel) {
      testimonialCarousel.addEventListener('mouseenter', pauseTestimonialAutoPlay);
      testimonialCarousel.addEventListener('mouseleave', startTestimonialAutoPlay);

      let touchStartX = 0;
      let touchEndX = 0;
      testimonialCarousel.addEventListener('touchstart', e => {
        if (e.changedTouches && e.changedTouches[0]) {
          touchStartX = e.changedTouches[0].screenX;
        }
        pauseTestimonialAutoPlay();
      }, { passive: true });

      testimonialCarousel.addEventListener('touchend', e => {
        if (e.changedTouches && e.changedTouches[0]) {
          touchEndX = e.changedTouches[0].screenX;
          if (touchStartX - touchEndX > 40) {
            slideNextTestimonial();
          } else if (touchEndX - touchStartX > 40) {
            slidePrevTestimonial();
          }
        }
        startTestimonialAutoPlay();
      }, { passive: true });

      startTestimonialAutoPlay();
    }

    const headerEl = document.getElementById('header');
    const bottomNavEl = document.getElementById('appBottomNav');

    function updateScrollNavigation() {
      const currentScrollY = window.scrollY;
      const isScrolledDown = currentScrollY > 70;

      if (headerEl) {
        if (isScrolledDown) {
          headerEl.classList.add('-translate-y-full');
          headerEl.classList.add('shadow-md');
        } else {
          headerEl.classList.remove('-translate-y-full');
          headerEl.classList.remove('shadow-md');
        }
      }

      if (bottomNavEl) {
        if (isScrolledDown) {
          bottomNavEl.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
          bottomNavEl.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
        } else {
          bottomNavEl.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
          bottomNavEl.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }
      }
    }

    window.addEventListener('scroll', updateScrollNavigation, {passive:true});
    updateScrollNavigation();

    const observer = new IntersectionObserver((entries) => entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('in'); }), {threshold:.12});
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    function scrollToId(id){ document.getElementById(id)?.scrollIntoView({behavior:'smooth', block:'start'}); }
    function availabilityToast(msg='Availability search is ready for your selected branch.'){ const t=document.getElementById('toast'); if(!t) return; t.textContent=msg; t.classList.remove('hidden'); clearTimeout(window.__t); window.__t=setTimeout(()=>t.classList.add('hidden'),2800); }
    
    // Lightbox
    function openLightbox(src){ document.getElementById('lightboxImg').src=src; document.getElementById('lightbox').classList.remove('hidden'); document.body.style.overflow='hidden'; }
    function closeLightbox(){ document.getElementById('lightbox').classList.add('hidden'); document.body.style.overflow=''; }
    document.getElementById('lightbox')?.addEventListener('click', e => { if(e.target.id==='lightbox') closeLightbox(); });
    document.addEventListener('keydown', e => { if(e.key==='Escape'){ closeLightbox(); closeMobileNav(); closeChat(); } });

    function selectBranchQuick(branchId, branchTitle) {
      const select = document.getElementById('branchSelect');
      if (select) {
        select.value = branchId;
      }
      availabilityToast((branchTitle || 'Selected branch') + ' chosen — searching availability.');
      scrollToId('booking');
    }

    function selectWeatherBranch(branchId) {
      if (!branchId) return;

      // 1. Sync destination dropdowns (desktop and mobile search forms)
      const selects = document.querySelectorAll('select[name="branch_id"]');
      selects.forEach(sel => {
        sel.value = branchId;
      });

      // 2. Highlight clicked weather item and soften inactive ones seamlessly
      document.querySelectorAll('.weather-branch-card').forEach(card => {
        const id = card.getAttribute('data-branch-id');
        const citySpan = card.querySelector('span');
        if (id == branchId) {
          card.classList.add('text-emerald');
          card.classList.remove('opacity-60');
          if (citySpan) citySpan.classList.add('text-emerald');
        } else {
          card.classList.remove('text-emerald');
          card.classList.add('opacity-60');
          if (citySpan) citySpan.classList.remove('text-emerald');
        }
      });

      // 3. Jump hero carousel to corresponding branch slide if available
      const slides = document.querySelectorAll('.hero-slide');
      let matched = false;
      slides.forEach((slide, idx) => {
        const sBranchId = slide.getAttribute('data-branch-id');
        if (sBranchId && sBranchId == branchId && !matched) {
          goToHeroSlide(idx);
          matched = true;
        }
      });
    }

    const mobileNav = document.getElementById('mobileNav');
    document.getElementById('menuBtn')?.addEventListener('click', () => { mobileNav.classList.remove('hidden'); document.body.style.overflow='hidden'; });
    document.getElementById('closeMenu')?.addEventListener('click', closeMobileNav);
    mobileNav?.addEventListener('click', e => { if(e.target === mobileNav) closeMobileNav(); });
    mobileNav?.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMobileNav));
    function closeMobileNav(){ mobileNav?.classList.add('hidden'); document.body.style.overflow=''; }

    /* =========================================================================
       DIRECT CONCIERGE MESSAGING PLATFORM (CLIENT ENGINE)
       ========================================================================= */
    let activeEnquiryId = null;
    let lastMessageId = 0;
    let chatPollInterval = null;
    let currentActiveStay = null;
    let selectedTargetRoom = null;
    let guestContact = {
      name: localStorage.getItem('krishna_guest_name') || '',
      phone: localStorage.getItem('krishna_guest_phone') || ''
    };

    const conciergeModal = document.getElementById('conciergeModal');
    const chatMessagesContainer = document.getElementById('chatMessagesContainer');
    const chatTextInput = document.getElementById('chatTextInput');
    const chatTicketNumber = document.getElementById('chatTicketNumber');

    function openChat() {
      if (conciergeModal) {
        conciergeModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        refreshIcons();
        initChatConversation();
      }
    }

    function closeChat() {
      if (conciergeModal) {
        conciergeModal.classList.add('hidden');
        document.body.style.overflow = '';
        if (chatPollInterval) {
          clearInterval(chatPollInterval);
          chatPollInterval = null;
        }
      }
    }

    if (conciergeModal) {
      conciergeModal.addEventListener('click', e => {
        if (e.target === conciergeModal) closeChat();
      });
    }

    // Initialize chat
    async function initChatConversation() {
      try {
        const res = await fetch('/chat/init', {
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
          activeEnquiryId = data.enquiry.id;
          if (chatTicketNumber) {
            chatTicketNumber.textContent = data.enquiry.ticket_number;
          }
          if (data.user) {
            guestContact.name = data.user.name;
            guestContact.phone = data.user.phone || '';
          } else if (guestContact.name) {
            const label = document.getElementById('guestDisplayLabel');
            if (label) label.textContent = guestContact.name + (guestContact.phone ? ' (' + guestContact.phone + ')' : '');
          }

          // Handle Active In-House or Upcoming Stay
          currentActiveStay = data.active_stay || null;
          applyActiveStayUi(currentActiveStay);

          renderMessagesList(data.messages || []);
          startChatPolling();
        }
      } catch (err) {
        console.error('Chat init error:', err);
      }
    }

    function applyActiveStayUi(stay) {
      const banner = document.getElementById('activeStayBanner');
      const chipsBar = document.getElementById('roomTargetChipsBar');
      const chipsContainer = document.getElementById('roomTargetChipsContainer');
      const quickList = document.getElementById('quickPillsList');

      if (stay) {
        if (banner) {
          banner.classList.remove('hidden');
          const badge = document.getElementById('stayBadge');
          const branch = document.getElementById('stayBranch');
          const roomDates = document.getElementById('stayRoomAndDates');
          const code = document.getElementById('stayBookingCode');

          if (badge) {
            badge.textContent = stay.is_in_house ? 'Checked In' : 'Confirmed Stay';
            badge.className = stay.is_in_house 
              ? 'px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-400 text-emerald-950 uppercase tracking-wider'
              : 'px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-400 text-amber-950 uppercase tracking-wider';
          }
          if (branch) branch.textContent = stay.branch_name;
          if (roomDates) {
            const roomsLabel = stay.rooms && stay.rooms.length > 0 ? ('Rooms: ' + stay.rooms.join(', ')) : stay.room_type;
            roomDates.innerHTML = `${escapeHtml(roomsLabel)} &middot; ${escapeHtml(stay.check_in)} &rarr; ${escapeHtml(stay.check_out)}`;
          }
          if (code) code.textContent = stay.booking_code;
          const callLink = document.getElementById('stayBranchCallLink');
          if (callLink) {
            if (stay.branch_phone) {
              const cleanPhone = stay.branch_phone.replace(/[^0-9+]/g, '');
              callLink.href = `tel:${cleanPhone}`;
              callLink.title = `Call ${stay.branch_name} (${stay.branch_phone})`;
              callLink.classList.remove('hidden');
              callLink.classList.add('inline-flex');
            } else {
              callLink.classList.add('hidden');
              callLink.classList.remove('inline-flex');
            }
          }
        }

        // Render Multi-Room Targeting Selector Chips
        if (chipsBar && chipsContainer && stay.is_multi_room && stay.rooms && stay.rooms.length > 1) {
          chipsBar.classList.remove('hidden');
          let chipsHtml = `
            <button type="button" onclick="selectRoomTarget('all', this)" class="room-chip px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-forest text-paper border border-forest transition">
              All Rooms
            </button>
          `;
          stay.rooms.forEach(r => {
            chipsHtml += `
              <button type="button" onclick="selectRoomTarget('${escapeHtml(r)}', this)" class="room-chip px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-white text-forest/80 border border-forest/20 hover:border-forest/40 transition">
                Room ${escapeHtml(r)}
              </button>
            `;
          });
          chipsContainer.innerHTML = chipsHtml;
        } else if (chipsBar) {
          chipsBar.classList.add('hidden');
        }

        // Adapt Quick Action Pills for In-Stay Requests
        if (quickList && stay.is_in_house) {
          quickList.innerHTML = `
            <button type="button" onclick="sendQuickPrompt('🛎️ Requesting extra towels and room housekeeping service.')" class="text-[10px] whitespace-nowrap bg-emerald-50 hover:bg-emerald-100 text-emerald-950 px-2.5 py-1 rounded-full border border-emerald-200 font-semibold transition flex items-center gap-1">
              🛎️ Extra Towels & Clean
            </button>
            <button type="button" onclick="sendQuickPrompt('🍽️ Would like to order dining & herbal tea to our cottage.')" class="text-[10px] whitespace-nowrap bg-amber-50 hover:bg-amber-100 text-amber-950 px-2.5 py-1 rounded-full border border-amber-200 font-semibold transition flex items-center gap-1">
              🍽️ In-Room Dining & Tea
            </button>
            <button type="button" onclick="sendQuickPrompt('🌿 Please send organic farm spices & estate tea selection.')" class="text-[10px] whitespace-nowrap bg-paper hover:bg-white text-forest px-2.5 py-1 rounded-full border border-forest/15 font-semibold transition flex items-center gap-1">
              🌿 Farm Spices Delivery
            </button>
            <button type="button" onclick="sendQuickPrompt('🧳 Need luggage assistance / checkout porter service.')" class="text-[10px] whitespace-nowrap bg-paper hover:bg-white text-forest px-2.5 py-1 rounded-full border border-forest/15 font-semibold transition flex items-center gap-1">
              🧳 Luggage & Porter
            </button>
          `;
        }
      } else {
        if (banner) banner.classList.add('hidden');
        if (chipsBar) chipsBar.classList.add('hidden');
      }
    }

    function selectRoomTarget(room, btn) {
      selectedTargetRoom = (room === 'all') ? null : room;
      document.querySelectorAll('.room-chip').forEach(c => {
        c.className = 'room-chip px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-white text-forest/80 border border-forest/20 hover:border-forest/40 transition';
      });
      if (btn) {
        btn.className = 'room-chip px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-forest text-paper border border-forest transition';
      }
    }

    // Render full list of messages
    function renderMessagesList(messages) {
      if (!chatMessagesContainer) return;
      chatMessagesContainer.innerHTML = '';
      lastMessageId = 0;

      messages.forEach(msg => {
        appendSingleMessage(msg);
        if (msg.id > lastMessageId) lastMessageId = msg.id;
      });

      scrollChatToBottom();
      refreshIcons();
    }

    // Render a single message (Text or Interactive Card)
    function appendSingleMessage(msg) {
      if (!chatMessagesContainer) return;
      const isCustomer = msg.sender_type === 'customer';
      const msgDiv = document.createElement('div');
      msgDiv.className = `flex flex-col ${isCustomer ? 'items-end' : 'items-start'} my-2`;

      let cardHtml = '';
      if (msg.card_payload) {
        const p = msg.card_payload;
        const cardsList = (p.cards && Array.isArray(p.cards)) ? p.cards : (p.title ? [p] : []);
        if (cardsList.length > 0) {
          cardHtml = `
            <div class="mt-2 ${cardsList.length > 1 ? 'grid grid-cols-1 sm:grid-cols-2 gap-2.5 w-full' : 'w-full max-w-[280px] sm:max-w-[310px]'}">
              ${cardsList.map(card => `
                <div class="bg-white rounded-2xl overflow-hidden soft-border shadow-card text-forest flex flex-col justify-between">
                  ${card.image_url ? `
                  <div class="h-28 sm:h-32 w-full relative overflow-hidden bg-mint">
                    <img src="${card.image_url}" alt="${escapeHtml(card.title || '')}" class="w-full h-full object-cover">
                    ${card.badge ? `<span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-bold bg-forest/90 backdrop-blur-md text-paper uppercase">${escapeHtml(card.badge)}</span>` : ''}
                  </div>` : ''}
                  <div class="p-3 flex-1 flex flex-col justify-between space-y-1.5">
                    <div>
                      <div class="flex items-start justify-between gap-1.5">
                        <h4 class="serif font-bold text-xs leading-snug">${escapeHtml(card.title || '')}</h4>
                        ${card.price ? `<span class="font-bold text-xs text-forest shrink-0">${escapeHtml(card.price)}</span>` : ''}
                      </div>
                      ${card.subtitle ? `<p class="text-[10px] text-forest/60 mt-0.5 line-clamp-2">${escapeHtml(card.subtitle)}</p>` : ''}
                    </div>
                    ${card.link_url ? `
                    <a href="${card.link_url}" class="mt-2 w-full py-2 px-3 rounded-xl bg-forest hover:bg-forest/90 text-paper text-[10px] font-bold flex items-center justify-center gap-1.5 shadow-xs transition">
                      <span>${escapeHtml(card.action_text || 'View Details')}</span>
                      <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
                    </a>` : ''}
                  </div>
                </div>
              `).join('')}
            </div>
          `;
        }
      }

      const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) : 'Just now';

      let targetRoomBadge = '';
      if (msg.target_room) {
        targetRoomBadge = `
          <div class="mb-1">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold ${isCustomer ? 'bg-white/20 text-brass' : 'bg-amber-100 text-amber-900 border border-amber-200'}">
              <i data-lucide="door-open" class="w-3 h-3"></i> Room: ${escapeHtml(msg.target_room)}
            </span>
          </div>
        `;
      }

      msgDiv.innerHTML = `
        ${!isCustomer ? `
        <div class="flex items-center gap-1.5 mb-1 px-1">
          <span class="text-[10px] font-bold text-forest">Krishna Concierge</span>
          <span class="text-[9px] text-forest/40">&middot; ${timeStr}</span>
        </div>` : ''}
        <div class="max-w-[85%] sm:max-w-[78%] px-3.5 py-2.5 rounded-2xl text-xs leading-relaxed ${isCustomer ? 'bg-forest text-paper rounded-br-xs shadow-card' : 'bg-white text-forest soft-border rounded-bl-xs shadow-xs'}">
          ${targetRoomBadge}
          ${msg.message ? `<p class="whitespace-pre-line">${escapeHtml(msg.message)}</p>` : ''}
          ${cardHtml}
        </div>
        ${isCustomer ? `<span class="text-[9px] text-forest/40 mt-1 px-1">${timeStr}</span>` : ''}
      `;

      chatMessagesContainer.appendChild(msgDiv);
    }

    // Send chat message
    async function handleSendChatMessage(e) {
      e.preventDefault();
      const text = chatTextInput ? chatTextInput.value.trim() : '';
      if (!text || !activeEnquiryId) return;

      const targetToSend = selectedTargetRoom;

      // Optimistic UI
      appendSingleMessage({
        id: ++lastMessageId,
        sender_type: 'customer',
        message: text,
        target_room: targetToSend,
        created_at: new Date().toISOString()
      });
      chatTextInput.value = '';
      scrollChatToBottom();
      refreshIcons();

      try {
        const res = await fetch('/chat/send', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          },
          body: JSON.stringify({
            enquiry_id: activeEnquiryId,
            message: text,
            target_room: targetToSend,
            customer_name: guestContact.name,
            customer_phone: guestContact.phone,
          })
        });
        const data = await res.json();
        if (data.success && data.message) {
          lastMessageId = Math.max(lastMessageId, data.message.id);
        }
      } catch (err) {
        console.error('Send message error:', err);
      }
    }

    // Send quick prompt chip
    function sendQuickPrompt(promptText) {
      if (chatTextInput) {
        chatTextInput.value = promptText;
        handleSendChatMessage(new Event('submit'));
      }
    }

    // Polling loop
    function startChatPolling() {
      if (chatPollInterval) clearInterval(chatPollInterval);
      chatPollInterval = setInterval(async () => {
        if (!activeEnquiryId || !conciergeModal || conciergeModal.classList.contains('hidden')) return;
        try {
          const res = await fetch(`/chat/messages/${activeEnquiryId}?after_id=${lastMessageId}`, {
            headers: { 'Accept': 'application/json' }
          });
          const data = await res.json();
          if (data.success && data.messages && data.messages.length > 0) {
            data.messages.forEach(msg => {
              if (msg.id > lastMessageId) {
                appendSingleMessage(msg);
                lastMessageId = msg.id;
              }
            });
            scrollChatToBottom();
            refreshIcons();
          }
        } catch (err) {
          console.error('Poll error:', err);
        }
      }, 3800);
    }

    function scrollChatToBottom() {
      if (chatMessagesContainer) {
        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
      }
    }

    function toggleGuestDetailsPrompt() {
      const p = document.getElementById('guestDetailsPrompt');
      if (p) p.classList.toggle('hidden');
    }

    function saveGuestContactDetails() {
      const name = document.getElementById('guestInputName').value.trim();
      const phone = document.getElementById('guestInputPhone').value.trim();
      if (name) {
        guestContact.name = name;
        localStorage.setItem('krishna_guest_name', name);
      }
      if (phone) {
        guestContact.phone = phone;
        localStorage.setItem('krishna_guest_phone', phone);
      }
      const label = document.getElementById('guestDisplayLabel');
      if (label) label.textContent = (guestContact.name || 'Guest') + (guestContact.phone ? ' (' + guestContact.phone + ')' : '');
      toggleGuestDetailsPrompt();
      availabilityToast('Contact details saved for concierge desk.');
    }

    function escapeHtml(str) {
      return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }


    /* ==========================================================================
       DATE PICKER CONTROLS & HELPERS
       ========================================================================== */
    function openDateCalendar(inputId) {
      const input = document.getElementById(inputId);
      if (!input) return;
      try {
        input.focus();
        if (typeof input.showPicker === 'function') {
          input.showPicker();
        } else {
          input.click();
        }
      } catch (e) {
        input.focus();
        input.click();
      }
    }

    function formatDisplayDate(dateStr) {
      if (!dateStr) return '';
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        return d.toLocaleDateString('en-US', { weekday: 'short', day: '2-digit', month: 'short' });
      }
      return dateStr;
    }

    function updateDateDisplay(inputId, displayId) {
      const input = document.getElementById(inputId);
      const display = document.getElementById(displayId);
      if (input && display && input.value) {
        display.textContent = formatDisplayDate(input.value);
      }
    }

    function updateMinCheckOut(checkInId, checkOutId) {
      const checkIn = document.getElementById(checkInId);
      const checkOut = document.getElementById(checkOutId);
      if (!checkIn || !checkOut || !checkIn.value) return;
      const parts = checkIn.value.split('-');
      if (parts.length === 3) {
        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        d.setDate(d.getDate() + 1);
        const minYear = d.getFullYear();
        const minMonth = String(d.getMonth() + 1).padStart(2, '0');
        const minDay = String(d.getDate()).padStart(2, '0');
        const minDateStr = `${minYear}-${minMonth}-${minDay}`;
        checkOut.min = minDateStr;
        if (checkOut.value <= checkIn.value) {
          checkOut.value = minDateStr;
          const displayOutId = checkOutId.replace('-input', '-display');
          updateDateDisplay(checkOutId, displayOutId);
        }
      }
    }

    /* ==========================================================================
       ADULTS & CHILDREN GUEST PICKER POPOVER CONTROLLER
       ========================================================================== */
    window.guestState = { adults: 2, children: 0 };

    function adjustGuestCount(formType, guestType, delta) {
      if (guestType === 'adults') {
        guestState.adults = Math.min(10, Math.max(1, guestState.adults + delta));
      } else if (guestType === 'children') {
        guestState.children = Math.min(6, Math.max(0, guestState.children + delta));
      }
      syncGuestPickers();
    }

    function syncGuestPickers() {
      const label = `${guestState.adults} Adult${guestState.adults > 1 ? 's' : ''}${guestState.children > 0 ? ', ' + guestState.children + ' Kid' + (guestState.children > 1 ? 's' : '') : ', 0 Kids'}`;

      ['desktop', 'mobile'].forEach(type => {
        const adultsInput = document.getElementById(`${type}-adults-input`);
        const childrenInput = document.getElementById(`${type}-children-input`);
        const adultsVal = document.getElementById(`${type}-adults-val`);
        const childrenVal = document.getElementById(`${type}-children-val`);
        const labelEl = document.getElementById(`${type}-guest-label`);
        const adultsMinus = document.getElementById(`${type}-adults-minus`);
        const childrenMinus = document.getElementById(`${type}-children-minus`);

        if (adultsInput) adultsInput.value = guestState.adults;
        if (childrenInput) childrenInput.value = guestState.children;
        if (adultsVal) adultsVal.textContent = guestState.adults;
        if (childrenVal) childrenVal.textContent = guestState.children;
        if (labelEl) labelEl.textContent = label;
        if (adultsMinus) adultsMinus.disabled = (guestState.adults <= 1);
        if (childrenMinus) childrenMinus.disabled = (guestState.children <= 0);
      });
    }

    function positionGuestPopover(type) {
      const target = document.getElementById(`${type}-guest-popover`);
      const container = document.getElementById(`${type}-guest-picker-container`);
      if (!target || !container) return;

      const rect = container.getBoundingClientRect();
      const spaceBelow = window.innerHeight - rect.bottom;
      const popoverHeight = 310;

      if (spaceBelow < popoverHeight && rect.top > 250) {
        target.classList.remove('top-full', 'mt-3');
        target.classList.add('bottom-full', 'mb-3');
      } else if (spaceBelow >= popoverHeight) {
        target.classList.remove('bottom-full', 'mb-3');
        target.classList.add('top-full', 'mt-3');
      } else {
        target.classList.remove('top-full', 'mt-3');
        target.classList.add('bottom-full', 'mb-3');
      }
    }

    function toggleGuestPopover(type) {
      const target = document.getElementById(`${type}-guest-popover`);
      const otherType = type === 'desktop' ? 'mobile' : 'desktop';
      const other = document.getElementById(`${otherType}-guest-popover`);
      const chevron = document.getElementById(`${type}-guest-chevron`);

      if (other) other.classList.add('hidden');

      if (target) {
        const isHidden = target.classList.contains('hidden');
        if (isHidden) {
          positionGuestPopover(type);
          target.classList.remove('hidden');
          if (chevron) {
            chevron.style.transform = 'rotate(180deg)';
          }
        } else {
          target.classList.add('hidden');
          if (chevron) {
            chevron.style.transform = 'rotate(0deg)';
          }
        }
      }
    }

    function closeGuestPopover(type) {
      const target = document.getElementById(`${type}-guest-popover`);
      const chevron = document.getElementById(`${type}-guest-chevron`);
      if (target) target.classList.add('hidden');
      if (chevron) chevron.style.transform = 'rotate(0deg)';
    }

    // Dismiss popovers when clicking outside
    document.addEventListener('click', (e) => {
      const desktopPicker = document.getElementById('desktop-guest-picker-container');
      const mobilePicker = document.getElementById('mobile-guest-picker-container');
      const desktopPopover = document.getElementById('desktop-guest-popover');
      const mobilePopover = document.getElementById('mobile-guest-popover');

      if (desktopPicker && !desktopPicker.contains(e.target) && desktopPopover && !desktopPopover.classList.contains('hidden')) {
        closeGuestPopover('desktop');
      }
      if (mobilePicker && !mobilePicker.contains(e.target) && mobilePopover && !mobilePopover.classList.contains('hidden')) {
        closeGuestPopover('mobile');
      }
    });

    /* ==========================================================================
       REAL-TIME IN-PAGE AVAILABILITY ENGINE & BOOKING AUTH GATE
       ========================================================================== */
    window.isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    window.activeBookingSuite = null;

    function closeLiveAvailability() {
      const container = document.getElementById('live-availability-container');
      if (container) {
        container.classList.add('hidden');
      }
    }

    function scrollToLiveAvailability() {
      const container = document.getElementById('live-availability-container');
      if (!container) return;

      requestAnimationFrame(() => {
        const header = document.getElementById('header') || document.querySelector('header');
        const headerOffset = header ? (header.offsetHeight + 18) : 84;
        const rect = container.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const targetY = scrollTop + rect.top - headerOffset;

        window.scrollTo({
          top: Math.max(0, targetY),
          behavior: 'smooth'
        });
      });
    }

    async function executeLiveAvailabilityCheck(formData) {
      const container = document.getElementById('live-availability-container');
      const loading = document.getElementById('live-availability-loading');
      const errorDiv = document.getElementById('live-availability-error');
      const emptyDiv = document.getElementById('live-availability-empty');
      const grid = document.getElementById('live-availability-grid');
      const summary = document.getElementById('live-availability-summary');

      if (!container) return;

      // Reveal container and smoothly scroll into position
      container.classList.remove('hidden');
      scrollToLiveAvailability();

      // Reset states
      loading.classList.remove('hidden');
      errorDiv.classList.add('hidden');
      emptyDiv.classList.add('hidden');
      grid.innerHTML = '';
      summary.textContent = 'Verifying real-time cottage capacity and rates...';

      const params = new URLSearchParams(formData);

      try {
        const response = await fetch(`/api/availability/check?${params.toString()}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const data = await response.json();
        loading.classList.add('hidden');

        if (!data.success) {
          errorDiv.classList.remove('hidden');
          document.getElementById('live-availability-error-msg').textContent = data.message || 'Unable to verify availability.';
          return;
        }

        const guestSummary = `${data.adults} Adult${data.adults > 1 ? 's' : ''}${data.children > 0 ? ', ' + data.children + ' Kid' + (data.children > 1 ? 's' : '') : ''}`;

        if (data.count === 0) {
          emptyDiv.classList.remove('hidden');
          summary.textContent = `${guestSummary} · ${data.nights} Night(s) (${data.check_in} to ${data.check_out}) · 0 Suites Available`;
          return;
        }

        summary.textContent = `${guestSummary} · ${data.nights} Night(s) (${data.check_in} to ${data.check_out}) · ${data.count} Available Suite(s) Found`;

        // Populate cards
        data.results.forEach(suite => {
          const card = document.createElement('div');
          card.className = 'group flex flex-col justify-between overflow-hidden rounded-[24px] sm:rounded-[26px] bg-white border border-forest/10 shadow-[0_8px_30px_rgba(6,63,52,0.06)] hover:shadow-[0_20px_45px_rgba(6,63,52,0.12)] transition-all duration-500 hover:-translate-y-1.5';

          const detailUrl = `/rooms/${encodeURIComponent(suite.slug)}?check_in=${encodeURIComponent(data.check_in)}&check_out=${encodeURIComponent(data.check_out)}&adults=${encodeURIComponent(data.adults)}&children=${encodeURIComponent(data.children)}`;

          const amenitiesHtml = (suite.amenities || []).slice(0, 3).map(a => 
            `<span class="inline-flex items-center gap-1 rounded-lg bg-mint/50 px-2 py-0.5 text-[10px] font-semibold text-forest border border-emerald/15">
               <i data-lucide="check" class="w-2.5 h-2.5 text-emerald"></i>
               <span>${escapeHtml(a)}</span>
             </span>`
          ).join('');

          card.innerHTML = `
            <div>
              <!-- Photo Section -->
              <div class="relative h-56 sm:h-60 w-full overflow-hidden bg-[#ECE8DF]">
                <a href="${detailUrl}" class="block h-full w-full">
                  <img src="${escapeHtml(suite.cover_image_url)}" alt="${escapeHtml(suite.name)}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy">
                </a>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none"></div>

                <!-- Top Floating Badges: Destination & Category -->
                <div class="absolute top-3.5 left-3.5 flex flex-wrap items-center gap-1.5 z-10">
                  <span class="glass-dark text-paper text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider backdrop-blur-md border border-white/15 shadow-xs flex items-center gap-1">
                    <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i>
                    <span>${escapeHtml(suite.branch_city || suite.branch_name)}</span>
                  </span>
                  <span class="bg-white/95 text-forest text-[10px] font-bold px-2.5 py-1 rounded-full backdrop-blur-md shadow-xs flex items-center gap-1">
                    <i data-lucide="sparkles" class="w-3 h-3 text-emerald"></i>
                    <span>${escapeHtml(suite.category_name)}</span>
                  </span>
                </div>

                <!-- Top Right: Availability Indicator -->
                <div class="absolute top-3.5 right-3.5 z-10">
                  ${suite.available_rooms_count <= 2 
                    ? `<span class="bg-amber-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 animate-pulse">
                         <i data-lucide="flame" class="w-3 h-3"></i> Only ${suite.available_rooms_count} Left
                       </span>`
                    : `<span class="glass-light text-forest text-[10px] font-bold px-2.5 py-1 rounded-full border border-white/60 shadow-xs flex items-center gap-1">
                         <span class="h-1.5 w-1.5 rounded-full bg-emerald"></span> ${suite.available_rooms_count} Available
                       </span>`
                  }
                </div>

                <!-- Bottom Overlay Bar on Image -->
                <div class="absolute bottom-3 left-3.5 right-3.5 flex items-center justify-between text-paper pointer-events-none z-10">
                  <div class="glass-dark text-white/95 text-[11px] font-semibold px-2.5 py-0.5 rounded-full backdrop-blur-md border border-white/15 flex items-center gap-1.5 shadow-xs">
                    <i data-lucide="maximize" class="w-3 h-3 text-brass"></i>
                    <span>${suite.size_sqft} sq.ft &middot; ${escapeHtml(suite.bed_type)}</span>
                  </div>
                  <div class="glass-dark text-white text-[11px] font-bold px-2 py-0.5 rounded-full backdrop-blur-md border border-white/15 flex items-center gap-1 shadow-xs">
                    <i data-lucide="star" class="w-3 h-3 fill-brass text-brass"></i>
                    <span>4.95</span>
                  </div>
                </div>
              </div>

              <!-- Content Details -->
              <div class="p-5">
                <div class="flex items-start justify-between gap-2">
                  <a href="${detailUrl}" class="serif text-lg sm:text-xl font-bold text-forest hover:text-emerald transition leading-snug line-clamp-1">
                    ${escapeHtml(suite.name)}
                  </a>
                </div>
                <p class="mt-1 text-xs text-forest/55 flex items-center gap-1.5 font-medium">
                  <i data-lucide="compass" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                  <span class="truncate">${escapeHtml(suite.branch_name)}</span>
                </p>

                <p class="mt-2 text-xs text-forest/70 line-clamp-2 leading-relaxed font-normal">
                  ${escapeHtml(suite.short_description)}
                </p>

                <!-- Capacity Breakdown Banner -->
                <div class="mt-3.5 flex items-center justify-between p-2.5 rounded-xl bg-forest/[0.03] border border-forest/10 text-xs">
                  <div class="flex items-center gap-1.5 text-forest font-semibold">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                    <span>Up to ${suite.max_guests} Guests</span>
                  </div>
                  <span class="text-[10px] text-forest/60 font-medium">
                    (${suite.max_adults} Adults${suite.max_children ? ' + ' + suite.max_children + ' Kids' : ''})
                  </span>
                </div>

                <!-- Amenity Chips -->
                ${amenitiesHtml ? `<div class="mt-3 flex flex-wrap gap-1.5">${amenitiesHtml}</div>` : ''}
              </div>
            </div>

            <!-- Card Footer: Pricing & CTAs -->
            <div class="p-5 pt-0">
              <div class="pt-3.5 border-t border-forest/10 flex items-center justify-between gap-3">
                <div>
                  <div class="flex items-baseline gap-1">
                    <span class="serif text-xl sm:text-2xl font-bold text-forest">${escapeHtml(suite.formatted_price)}</span>
                    <span class="text-[11px] text-forest/50 font-normal">/ night</span>
                  </div>
                  <div class="text-[10px] text-forest/60 mt-0.5">
                    <span class="font-bold text-forest/85">${escapeHtml(suite.formatted_grand_total)}</span> total incl. 12% GST
                  </div>
                </div>

                <div class="flex items-center gap-1.5">
                  <a href="${detailUrl}" class="hidden sm:inline-flex items-center justify-center h-9 w-9 rounded-xl border border-forest/15 bg-paper hover:bg-white text-forest transition shadow-2xs" title="View details">
                    <i data-lucide="info" class="w-4 h-4"></i>
                  </a>
                  <button type="button" class="btn-book-suite px-4 py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center gap-1.5 shadow-card hover:shadow-lg transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <span>Reserve</span>
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
                  </button>
                </div>
              </div>
            </div>
          `;

          // Bind book click
          const bookBtn = card.querySelector('.btn-book-suite');
          bookBtn.addEventListener('click', () => {
            handleSuiteBooking(suite);
          });

          grid.appendChild(card);
        });

        if (window.lucide) {
          lucide.createIcons();
        }

      } catch (err) {
        console.error('Availability check failed:', err);
        loading.classList.add('hidden');
        errorDiv.classList.remove('hidden');
        document.getElementById('live-availability-error-msg').textContent = 'Network or server error checking availability. Please try again.';
      }
    }

    // Initialize Form Listeners
    document.addEventListener('DOMContentLoaded', () => {
      const desktopForm = document.getElementById('desktop-search-form');
      const mobileForm = document.getElementById('mobile-search-form');

      if (desktopForm) {
        desktopForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const formData = new FormData(desktopForm);
          executeLiveAvailabilityCheck(formData);
        });
      }

      if (mobileForm) {
        mobileForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const formData = new FormData(mobileForm);
          executeLiveAvailabilityCheck(formData);
        });
      }
    });

    // Booking auth flow
    function handleSuiteBooking(suite) {
      if (window.isUserLoggedIn) {
        window.location.href = suite.checkout_url;
        return;
      }

      window.activeBookingSuite = suite;
      openBookingAuthModal(suite);
    }

    function openBookingAuthModal(suite) {
      const modal = document.getElementById('booking-auth-modal');
      if (!modal) return;

      document.getElementById('modal-suite-name').textContent = suite.name;
      document.getElementById('modal-suite-branch').textContent = `${suite.branch_name} (${suite.branch_city})`;
      document.getElementById('modal-suite-total').textContent = suite.formatted_grand_total;

      // Update google button redirect parameter
      const googleBtn = document.getElementById('modal-google-btn');
      if (googleBtn && suite && suite.checkout_url) {
        googleBtn.href = `{{ route('auth.google') }}?redirect=${encodeURIComponent(suite.checkout_url)}`;
      }

      // Hide error
      const errBox = document.getElementById('modal-auth-error');
      if (errBox) errBox.classList.add('hidden');

      switchAuthTab('login');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeBookingAuthModal() {
      const modal = document.getElementById('booking-auth-modal');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    }

    function switchAuthTab(tab) {
      const loginForm = document.getElementById('modal-login-form');
      const regForm = document.getElementById('modal-register-form');
      const tabLogin = document.getElementById('tab-btn-login');
      const tabReg = document.getElementById('tab-btn-register');
      const errBox = document.getElementById('modal-auth-error');
      if (errBox) errBox.classList.add('hidden');

      if (tab === 'login') {
        loginForm.classList.remove('hidden');
        regForm.classList.add('hidden');
        tabLogin.className = 'flex-1 pb-3 text-xs sm:text-sm font-bold text-forest border-b-2 border-forest transition cursor-pointer';
        tabReg.className = 'flex-1 pb-3 text-xs sm:text-sm font-semibold text-forest/45 border-b-2 border-transparent hover:text-forest transition cursor-pointer';
      } else {
        loginForm.classList.add('hidden');
        regForm.classList.remove('hidden');
        tabLogin.className = 'flex-1 pb-3 text-xs sm:text-sm font-semibold text-forest/45 border-b-2 border-transparent hover:text-forest transition cursor-pointer';
        tabReg.className = 'flex-1 pb-3 text-xs sm:text-sm font-bold text-forest border-b-2 border-forest transition cursor-pointer';
      }
    }

    async function handleModalLogin(e) {
      e.preventDefault();
      const loginVal = document.getElementById('modal-login-input').value.trim();
      const passVal = document.getElementById('modal-login-password').value;
      const rememberVal = document.getElementById('modal-login-remember').checked;
      const submitBtn = document.getElementById('modal-login-submit');
      const errBox = document.getElementById('modal-auth-error');
      const errText = document.getElementById('modal-auth-error-text');

      if (!loginVal || !passVal) return;

      const origBtnText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = `<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-1"></i> Signing In...`;
      if (window.lucide) lucide.createIcons();

      try {
        const res = await fetch('/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            login: loginVal,
            password: passVal,
            remember: rememberVal,
            redirect_to: window.activeBookingSuite ? window.activeBookingSuite.checkout_url : ''
          })
        });

        const data = await res.json();
        if (data.success) {
          submitBtn.innerHTML = `<i data-lucide="check" class="w-4 h-4 inline mr-1 text-emerald"></i> Success! Redirecting...`;
          if (window.lucide) lucide.createIcons();
          window.isUserLoggedIn = true;
          window.location.href = data.redirect || (window.activeBookingSuite ? window.activeBookingSuite.checkout_url : '/stay');
        } else {
          submitBtn.disabled = false;
          submitBtn.innerHTML = origBtnText;
          errBox.classList.remove('hidden');
          errText.textContent = data.message || 'Invalid credentials. Please try again.';
          if (window.lucide) lucide.createIcons();
        }
      } catch (err) {
        console.error('Login error:', err);
        submitBtn.disabled = false;
        submitBtn.innerHTML = origBtnText;
        errBox.classList.remove('hidden');
        errText.textContent = 'A connection error occurred. Please try again.';
      }
    }

    async function handleModalRegister(e) {
      e.preventDefault();
      const name = document.getElementById('modal-reg-name').value.trim();
      const email = document.getElementById('modal-reg-email').value.trim();
      const phone = document.getElementById('modal-reg-phone').value.trim();
      const password = document.getElementById('modal-reg-password').value;
      const password_confirmation = document.getElementById('modal-reg-password-confirm').value;
      const submitBtn = document.getElementById('modal-reg-submit');
      const errBox = document.getElementById('modal-auth-error');
      const errText = document.getElementById('modal-auth-error-text');

      const origBtnText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = `<i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-1"></i> Creating Account...`;
      if (window.lucide) lucide.createIcons();

      try {
        const res = await fetch('/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            name,
            email,
            phone,
            password,
            password_confirmation,
            redirect_to: window.activeBookingSuite ? window.activeBookingSuite.checkout_url : ''
          })
        });

        const data = await res.json();
        if (data.success) {
          submitBtn.innerHTML = `<i data-lucide="check" class="w-4 h-4 inline mr-1 text-emerald"></i> Account Ready! Redirecting...`;
          if (window.lucide) lucide.createIcons();
          window.isUserLoggedIn = true;
          window.location.href = data.redirect || (window.activeBookingSuite ? window.activeBookingSuite.checkout_url : '/stay');
        } else {
          submitBtn.disabled = false;
          submitBtn.innerHTML = origBtnText;
          errBox.classList.remove('hidden');
          errText.textContent = data.message || 'Registration failed. Please review the details.';
          if (window.lucide) lucide.createIcons();
        }
      } catch (err) {
        console.error('Register error:', err);
        submitBtn.disabled = false;
        submitBtn.innerHTML = origBtnText;
        errBox.classList.remove('hidden');
        errText.textContent = 'A network error occurred. Please try again.';
      }
    }
  </script>

  <!-- ================= FAST BOOKING AUTH GATE MODAL ================= -->
  <div id="booking-auth-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-sm transition-opacity duration-300">
    <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-[#FAF8F5] p-6 sm:p-8 shadow-2xl border border-forest/15 animate-in fade-in zoom-in-95 duration-200">
      <!-- Close button -->
      <button type="button" onclick="closeBookingAuthModal()" class="absolute right-4 sm:right-6 top-4 sm:top-6 grid h-9 w-9 place-items-center rounded-full bg-forest/5 hover:bg-forest/10 text-forest/60 hover:text-forest transition cursor-pointer" title="Close modal">
        <i data-lucide="x" class="h-4 w-4"></i>
      </button>

      <!-- Header & Stay Summary Card -->
      <div class="mb-5">
        <div class="flex items-center gap-2 mb-1.5">
          <span class="grid h-7 w-7 place-items-center rounded-lg bg-forest text-paper text-xs font-bold">K</span>
          <span class="text-[10px] font-bold uppercase tracking-[.2em] text-emerald">Fast Reservation Gate</span>
        </div>
        <h3 class="serif text-2xl sm:text-3xl font-bold text-forest tracking-tight">Confirm Your Reservation</h3>
        <p class="text-xs text-forest/60 mt-1">Please sign in or create an account to proceed with your booking.</p>

        <!-- Stay Summary Capsule -->
        <div id="modal-stay-summary" class="mt-3.5 rounded-2xl bg-white p-3.5 border border-forest/10 shadow-xs">
          <div class="flex items-center justify-between gap-3">
            <div>
              <p id="modal-suite-name" class="text-xs sm:text-sm font-bold text-forest">Suite Name</p>
              <p id="modal-suite-branch" class="text-[11px] text-forest/60 mt-0.5">Krishna Cottages &middot; 2 Nights</p>
            </div>
            <div class="text-right shrink-0">
              <span id="modal-suite-total" class="text-sm sm:text-base font-extrabold text-forest">₹0</span>
              <span class="block text-[9px] text-forest/45 uppercase tracking-wider">Total Incl. Taxes</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Google Fast-Auth Button (Clean, Spacious) -->
      <div class="mb-4">
        <a id="modal-google-btn" href="{{ route('auth.google') }}" class="w-full py-3.5 px-4 rounded-2xl border border-forest/15 bg-white hover:bg-forest/5 hover:border-forest/30 transition-all flex items-center justify-center gap-3 text-xs sm:text-sm font-bold text-forest shadow-xs hover:shadow-md group cursor-pointer">
          <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.14-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
          </svg>
          <span class="group-hover:translate-x-0.5 transition">Continue with Google</span>
        </a>

        <!-- Divider -->
        <div class="relative flex items-center justify-center my-3 sm:my-3.5">
          <div class="border-t border-forest/10 w-full"></div>
          <span class="bg-[#FAF8F5] px-3 text-[10px] font-bold uppercase tracking-wider text-forest/40">or with account</span>
          <div class="border-t border-forest/10 w-full"></div>
        </div>
      </div>

      <!-- Auth Tabs -->
      <div class="flex border-b border-forest/10 mb-4">
        <button id="tab-btn-login" type="button" onclick="switchAuthTab('login')" class="flex-1 pb-3 text-xs sm:text-sm font-bold text-forest border-b-2 border-forest transition cursor-pointer">
          Sign In
        </button>
        <button id="tab-btn-register" type="button" onclick="switchAuthTab('register')" class="flex-1 pb-3 text-xs sm:text-sm font-semibold text-forest/45 border-b-2 border-transparent hover:text-forest transition cursor-pointer">
          Create Account
        </button>
      </div>

      <!-- Error Alert Container -->
      <div id="modal-auth-error" class="hidden mb-4 p-3 rounded-2xl bg-red-50 text-red-700 text-xs flex items-center gap-2.5 border border-red-200">
        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-red-600"></i>
        <span id="modal-auth-error-text" class="flex-1"></span>
      </div>

      <!-- Login Form -->
      <form id="modal-login-form" onsubmit="handleModalLogin(event)" class="space-y-4">
        <div class="space-y-1.5">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Email or Phone Number</label>
          <div class="relative flex items-center">
            <i data-lucide="user" class="w-4 h-4 text-forest/40 absolute left-3.5 pointer-events-none"></i>
            <input type="text" id="modal-login-input" required class="w-full pl-10 pr-3.5 py-3 rounded-2xl bg-white border border-forest/15 text-xs sm:text-sm text-forest placeholder:text-forest/35 focus:outline-hidden focus:ring-2 focus:ring-forest/10 focus:border-forest transition shadow-xs" placeholder="e.g. guest@example.com or 9876543210">
          </div>
        </div>
        <div class="space-y-1.5">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Password</label>
          <div class="relative flex items-center">
            <i data-lucide="lock" class="w-4 h-4 text-forest/40 absolute left-3.5 pointer-events-none"></i>
            <input type="password" id="modal-login-password" required class="w-full pl-10 pr-3.5 py-3 rounded-2xl bg-white border border-forest/15 text-xs sm:text-sm text-forest placeholder:text-forest/35 focus:outline-hidden focus:ring-2 focus:ring-forest/10 focus:border-forest transition shadow-xs" placeholder="••••••••">
          </div>
        </div>
        <div class="flex items-center justify-between text-xs">
          <label class="flex items-center gap-2 cursor-pointer text-forest/70">
            <input type="checkbox" id="modal-login-remember" checked class="rounded border-forest/20 text-forest focus:ring-forest">
            <span>Remember me</span>
          </label>
        </div>
        <button type="submit" id="modal-login-submit" class="w-full py-3.5 px-4 rounded-2xl bg-forest hover:bg-forest/90 text-paper font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition cursor-pointer">
          <span>Sign In & Continue to Booking</span>
          <i data-lucide="arrow-right" class="w-4 h-4 text-brass"></i>
        </button>
      </form>

      <!-- Register Form -->
      <form id="modal-register-form" onsubmit="handleModalRegister(event)" class="space-y-3.5 hidden">
        <div class="space-y-1">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Full Name</label>
          <div class="relative flex items-center">
            <i data-lucide="user" class="w-4 h-4 text-forest/40 absolute left-3.5 pointer-events-none"></i>
            <input type="text" id="modal-reg-name" required class="w-full pl-10 pr-3.5 py-2.5 rounded-2xl bg-white border border-forest/15 text-xs text-forest placeholder:text-forest/35 focus:outline-hidden focus:ring-2 focus:ring-forest/10 focus:border-forest transition shadow-xs" placeholder="e.g. Ananya Nair">
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
          <div class="space-y-1">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Email</label>
            <div class="relative flex items-center">
              <i data-lucide="mail" class="w-4 h-4 text-forest/40 absolute left-3.5 pointer-events-none"></i>
              <input type="email" id="modal-reg-email" required class="w-full pl-10 pr-3 py-2.5 rounded-2xl bg-white border border-forest/15 text-xs text-forest placeholder:text-forest/35 focus:outline-hidden focus:ring-2 focus:ring-forest/10 focus:border-forest transition shadow-xs" placeholder="name@domain.com">
            </div>
          </div>
          <div class="space-y-1">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Phone</label>
            <div class="relative flex items-center">
              <i data-lucide="phone" class="w-4 h-4 text-forest/40 absolute left-3.5 pointer-events-none"></i>
              <input type="tel" id="modal-reg-phone" required class="w-full pl-10 pr-3 py-2.5 rounded-2xl bg-white border border-forest/15 text-xs text-forest placeholder:text-forest/35 focus:outline-hidden focus:ring-2 focus:ring-forest/10 focus:border-forest transition shadow-xs" placeholder="+91 98450...">
            </div>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
          <div class="space-y-1">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Password</label>
            <div class="relative flex items-center">
              <i data-lucide="lock" class="w-4 h-4 text-forest/40 absolute left-3.5 pointer-events-none"></i>
              <input type="password" id="modal-reg-password" minlength="8" required class="w-full pl-10 pr-3 py-2.5 rounded-2xl bg-white border border-forest/15 text-xs text-forest placeholder:text-forest/35 focus:outline-hidden focus:ring-2 focus:ring-forest/10 focus:border-forest transition shadow-xs" placeholder="Min 8 chars">
            </div>
          </div>
          <div class="space-y-1">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-forest/70">Confirm</label>
            <div class="relative flex items-center">
              <i data-lucide="lock-keyhole" class="w-4 h-4 text-forest/40 absolute left-3.5 pointer-events-none"></i>
              <input type="password" id="modal-reg-password-confirm" minlength="8" required class="w-full pl-10 pr-3 py-2.5 rounded-2xl bg-white border border-forest/15 text-xs text-forest placeholder:text-forest/35 focus:outline-hidden focus:ring-2 focus:ring-forest/10 focus:border-forest transition shadow-xs" placeholder="Repeat">
            </div>
          </div>
        </div>
        <button type="submit" id="modal-reg-submit" class="w-full py-3.5 px-4 mt-2 rounded-2xl bg-forest hover:bg-forest/90 text-paper font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition cursor-pointer">
          <span>Create Account & Continue</span>
          <i data-lucide="arrow-right" class="w-4 h-4 text-brass"></i>
        </button>
      </form>
    </div>
  </div>
  <!-- ================= MOBILE APP-LIKE FLOATING BOTTOM NAVIGATOR ================= -->
  <aside id="appBottomNav" class="fixed inset-x-0 bottom-4 sm:bottom-6 z-50 flex justify-center px-4 pointer-events-none transition-all duration-300 ease-out translate-y-24 opacity-0">
    <div class="pointer-events-auto bg-[#FAF7F0]/95 backdrop-blur-xl border border-forest/15 rounded-full px-2.5 sm:px-3.5 py-1.5 sm:py-2 shadow-[0_16px_40px_rgba(8,63,52,0.18)] flex items-center gap-1 sm:gap-1.5">
      <!-- Home -->
      <a href="{{ route('home') }}" class="flex flex-col items-center justify-center px-2.5 sm:px-3 py-1 rounded-full text-forest hover:text-emerald hover:bg-forest/5 transition group {{ request()->routeIs('home') ? 'text-emerald font-bold' : '' }}" title="Home">
        <i data-lucide="home" class="w-4 h-4 {{ request()->routeIs('home') ? 'text-emerald' : 'text-forest/70' }} group-hover:text-emerald group-hover:scale-110 transition"></i>
        <span class="text-[9px] sm:text-[10px] font-semibold mt-0.5 tracking-tight">Home</span>
      </a>

      <!-- Stays (Cottages & Suites) -->
      <a href="{{ route('rooms.index') }}" class="flex flex-col items-center justify-center px-2.5 sm:px-3 py-1 rounded-full text-forest hover:text-emerald hover:bg-forest/5 transition group {{ request()->routeIs('rooms.*') ? 'text-emerald font-bold' : '' }}" title="Cottages & Suites">
        <i data-lucide="bed-double" class="w-4 h-4 {{ request()->routeIs('rooms.*') ? 'text-emerald' : 'text-forest/70' }} group-hover:text-emerald group-hover:scale-110 transition"></i>
        <span class="text-[9px] sm:text-[10px] font-semibold mt-0.5 tracking-tight">Stays</span>
      </a>

      <!-- Dining -->
      <a href="{{ route('dining.index') }}" class="flex flex-col items-center justify-center px-2.5 sm:px-3 py-1 rounded-full text-forest hover:text-emerald hover:bg-forest/5 transition group {{ request()->routeIs('dining.*') ? 'text-emerald font-bold' : '' }}" title="Plantation Dining">
        <i data-lucide="utensils" class="w-4 h-4 {{ request()->routeIs('dining.*') ? 'text-emerald' : 'text-forest/70' }} group-hover:text-emerald group-hover:scale-110 transition"></i>
        <span class="text-[9px] sm:text-[10px] font-semibold mt-0.5 tracking-tight">Dining</span>
      </a>

      <!-- Spices -->
      <a href="{{ route('spices.index') }}" class="flex flex-col items-center justify-center px-2.5 sm:px-3 py-1 rounded-full text-forest hover:text-emerald hover:bg-forest/5 transition group {{ request()->routeIs('spices.*') ? 'text-emerald font-bold' : '' }}" title="Krishna Spices">
        <i data-lucide="sparkles" class="w-4 h-4 {{ request()->routeIs('spices.*') ? 'text-emerald' : 'text-forest/70' }} group-hover:text-emerald group-hover:scale-110 transition"></i>
        <span class="text-[9px] sm:text-[10px] font-semibold mt-0.5 tracking-tight">Spices</span>
      </a>

      <!-- Concierge Chat -->
      <button type="button" onclick="openChat()" class="flex flex-col items-center justify-center px-2.5 sm:px-3 py-1 rounded-full text-forest hover:text-emerald hover:bg-forest/5 transition group cursor-pointer" title="Concierge Desk">
        <i data-lucide="message-circle" class="w-4 h-4 text-forest/70 group-hover:text-emerald group-hover:scale-110 transition"></i>
        <span class="text-[9px] sm:text-[10px] font-semibold mt-0.5 tracking-tight">Concierge</span>
      </button>

      <!-- Quick Book Stay Action Pill -->
      <a href="{{ route('rooms.index') }}" class="ml-1 sm:ml-1.5 inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-full bg-forest hover:bg-emerald text-paper text-[11px] sm:text-xs font-bold shadow-md hover:scale-105 transition cursor-pointer">
        <span>Book Stay</span>
        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
      </a>
    </div>
  </aside>
</body>
</html>
