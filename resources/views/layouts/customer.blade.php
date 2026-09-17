<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F4F1E8">
    <title>@yield('title', 'Krishna Cottages — Best Luxury Cottages & Homestay in Idukki, Rajakkadu, Munnar, Kerala')</title>

    <!-- PRIMARY HIGH-RANKING SEO METADATA -->
    <meta name="description" content="@yield('meta_description', 'Discover Krishna Cottages in Rajakkad, Idukki. Book luxury wooden cottages, nature homestays &amp; private hillside villas across Rajakkadu, Rajakumari, Adimali &amp; Munnar, Kerala with organic dining &amp; plantation views.')">
    <meta name="keywords" content="@yield('meta_keywords', 'krishna cottage idukki, krishna cottages rajakkad, krishna resort idukki, krishna homestay idukki, idukki cottages, idukki resorts, resorts in idukki, rajakkadu, resort in rajakkadu, home stay in idukki, home stay in rajakkadu, home stay in rajakumari, resort in rajakumari, resort in adimali, cottage in adimali, home stay in adimali, idukki, kerala, best resort in idukki, best cottage in idukki, best cottage in munnar, munnar resorts, munnar homestay, cottages in rajakkad idukki, kuthumkal waterfalls resort, mailadumpara cottages idukki, ponmudi lake resort rajakkad, adivaram idukki homestay, wooden cottages in idukki, plantation homestay in rajakkadu, budget homestay in rajakumari, family resort in adimali idukki, honeymoon cottage in munnar idukki, kerala eco cottage idukki, ayurvedic resort in idukki')">
    <meta name="robots" content="index, follow" />
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
    <meta property="og:title" content="@yield('og_title', 'Krishna Cottages — Best Luxury Cottages & Homestay in Idukki, Rajakkadu & Munnar')" />
    <meta property="og:description" content="@yield('og_description', 'Authentic wooden cottages and peaceful hillside homestays in Rajakkad, Idukki, Kerala.')" />
    <meta property="og:image" content="@yield('og_image', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85')" />
    <meta property="og:locale" content="en_IN" />

    <!-- TWITTER CARDS -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('twitter_title', 'Krishna Cottages — Best Luxury Cottages & Homestay in Idukki, Rajakkadu & Munnar')" />
    <meta name="twitter:description" content="@yield('twitter_description', 'Experience peaceful wooden cottages and estate living in Rajakkadu, Rajakumari, Adimali & Munnar, Idukki, Kerala.')" />

    <!-- SPEED OPTIMIZATION & RESOURCE HINTS (Sub-3-Second Load Guarantee) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="dns-prefetch" href="https://unpkg.com" />
    <link rel="preconnect" href="https://images.unsplash.com" />

    <!-- Vite Assets (App CSS & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons (Non-blocking Defer) -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        html, body { background: #F4F1E8; color: #17312A; }
        body {
            background-image: radial-gradient(circle at 20% 10%, rgba(201,168,106,.06), transparent 22%), radial-gradient(circle at 80% 40%, rgba(15,107,88,.04), transparent 26%);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            overflow-x: hidden;
        }
        .serif { font-family: Georgia, "Times New Roman", serif; }
        .eyebrow { font-size: 10px; letter-spacing: .24em; text-transform: uppercase; font-weight: 700; }
        .hide-scrollbar::-webkit-scrollbar { display:none; }
        .hide-scrollbar { scrollbar-width:none; }
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { scrollbar-width:none; -ms-overflow-style:none; }
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
        .mobile-shell { border-radius: 28px; box-shadow: 0 24px 70px rgba(8,63,52,.16); }
        @media (max-width: 767px){
            .mobile-shell { border-radius: 20px; }
            .desktop-only { display:none !important; }
        }
        @media (min-width: 768px){ .mobile-only { display:none !important; } }
        .lift { transition: transform .55s cubic-bezier(.2,.8,.2,1), box-shadow .55s cubic-bezier(.2,.8,.2,1); }
        .lift:hover { transform: translateY(-5px); box-shadow: 0 18px 45px rgba(8,63,52,.11); }
        .nav-item-line { position:relative; }
        .nav-item-line::after { content:""; position:absolute; left:0; bottom:-6px; width:0; height:2px; background:#0B5D4B; transition:width .35s ease; }
        .nav-item-line:hover::after, .nav-item-line.active::after { width:100%; }
        .nav-item-line.active { color:#0B5D4B !important; font-weight:700; }
        .drawer-link { opacity:0; transform:translateX(18px); transition:opacity .45s ease, transform .55s cubic-bezier(.2,.8,.2,1); }
        #mobileNav:not(.hidden) .drawer-link { opacity:1; transform:none; }
        #mobileNav:not(.hidden) .drawer-link:nth-child(1){transition-delay:.05s}
        #mobileNav:not(.hidden) .drawer-link:nth-child(2){transition-delay:.09s}
        #mobileNav:not(.hidden) .drawer-link:nth-child(3){transition-delay:.13s}
        #mobileNav:not(.hidden) .drawer-link:nth-child(4){transition-delay:.17s}
        #mobileNav:not(.hidden) .drawer-link:nth-child(5){transition-delay:.21s}
        #mobileNav:not(.hidden) .drawer-link:nth-child(6){transition-delay:.25s}
        #mobileNav:not(.hidden) .drawer-link:nth-child(7){transition-delay:.29s}
        #mobileNav:not(.hidden) .drawer-link:nth-child(8){transition-delay:.33s}
        .drawer-panel { transform:translateX(12px); opacity:0; transition:transform .55s cubic-bezier(.2,.8,.2,1), opacity .35s ease; }
        #mobileNav:not(.hidden) .drawer-panel { transform:none; opacity:1; }
        .touch-tap:active { transform: scale(0.97); transition: transform 0.1s ease; }
    </style>
    @stack('styles')
</head>
<body class="bg-paper text-ink font-sans antialiased min-h-screen flex flex-col pb-24 md:pb-0 selection:bg-brass selection:text-forest">
    <!-- ANIMATED LUXURY PAGE-TRANSITION LOADER (98% Visible, 2% Blur) -->
    <x-page-loader />

    <!-- UNIFIED NATURAL OFF-WHITE LUXURY CLIENT HEADER (SAME AS WELCOME PAGE) -->
    <x-header />

    <!-- MAIN BODY CONTENT -->
    <main id="top" class="pt-17 sm:pt-18 lg:pt-19 flex-1 w-full">
        @yield('content')
    </main>

    <!-- SHARED LUXURY EDITORIAL FOOTER (DYNAMIC PPT VISUAL EDITOR POWERED) -->
    <x-footer class="mt-16 md:mt-24" />

    <!-- MOBILE FLOATING APP NAVIGATION (< 768px) -->
    <div class="mobile-only fixed bottom-3 left-3 right-3 z-50">
        <div class="mobile-shell border border-forest/10 bg-paper/92 p-1.5 backdrop-blur-xl">
            <div class="grid grid-cols-5 gap-1">
                <!-- 1. EXPLORE / HOME -->
                <a href="{{ route('home') }}" class="rounded-xl px-2 py-2 text-center transition touch-tap {{ request()->routeIs('home') ? 'bg-white/80 text-forest font-bold shadow-xs' : 'text-forest/60' }}">
                    <i data-lucide="compass" class="mx-auto h-4 w-4 {{ request()->routeIs('home') ? 'text-forest stroke-[2.5]' : 'text-forest/60' }}"></i>
                    <span class="mt-1 block text-[8px]">Explore</span>
                </a>

                <!-- 2. STAY -->
                <a href="{{ route('rooms.index') }}" class="rounded-xl px-2 py-2 text-center transition touch-tap {{ request()->routeIs('rooms.*') || request()->routeIs('booking.*') ? 'bg-white/80 text-forest font-bold shadow-xs' : 'text-forest/60' }}">
                    <i data-lucide="bed-double" class="mx-auto h-4 w-4 {{ request()->routeIs('rooms.*') || request()->routeIs('booking.*') ? 'text-forest stroke-[2.5]' : 'text-forest/60' }}"></i>
                    <span class="mt-1 block text-[8px]">Stay</span>
                </a>

                <!-- 3. DINING -->
                <a href="{{ route('dining.index') }}" class="rounded-xl px-2 py-2 text-center transition touch-tap {{ request()->routeIs('dining.*') ? 'bg-white/80 text-forest font-bold shadow-xs' : 'text-forest/60' }}">
                    <i data-lucide="utensils" class="mx-auto h-4 w-4 {{ request()->routeIs('dining.*') ? 'text-forest stroke-[2.5]' : 'text-forest/60' }}"></i>
                    <span class="mt-1 block text-[8px]">Dining</span>
                </a>

                <!-- 4. SPICES -->
                <a href="{{ route('spices.index') }}" class="rounded-xl px-2 py-2 text-center transition touch-tap {{ request()->routeIs('spices.*') ? 'bg-white/80 text-forest font-bold shadow-xs' : 'text-forest/60' }}">
                    <i data-lucide="leaf" class="mx-auto h-4 w-4 {{ request()->routeIs('spices.*') ? 'text-forest stroke-[2.5]' : 'text-forest/60' }}"></i>
                    <span class="mt-1 block text-[8px]">Spices</span>
                </a>


                @guest
                <!-- 5. LOGIN -->
                <a href="{{ route('login') }}" class="rounded-xl px-2 py-2 text-center transition touch-tap text-forest/60">
                    <i data-lucide="user" class="mx-auto h-4 w-4 text-forest/60"></i>
                    <span class="mt-1 block text-[8px]">Login</span>
                </a>
                @else
                <!-- 5. CHAT -->
                <button onclick="openChat()" class="rounded-xl px-2 py-2 text-center transition touch-tap text-forest/60">
                    <i data-lucide="message-circle" class="mx-auto h-4 w-4 text-forest/60"></i>
                    <span class="mt-1 block text-[8px]">Chat</span>
                </button>
                @endguest
            </div>
        </div>
    </div>

    <!-- SLIDE-OVER MOBILE DRAWER -->
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
                    <button onclick="openChat();closeMobileNav()" class="rounded-2xl border border-forest/10 bg-white p-3 text-left touch-tap">
                        <i data-lucide="message-circle" class="h-4 w-4 text-emerald"></i>
                        <p class="mt-2 text-[11px] font-bold">Concierge Chat</p>
                        <p class="mt-0.5 text-[9px] text-forest/45">Direct enquiry</p>
                    </button>
                    @guest
                    <a href="{{ route('login', ['redirect' => route('customer.dashboard')]) }}" class="rounded-2xl border border-forest/10 bg-white p-3 text-left touch-tap">
                        <i data-lucide="user" class="h-4 w-4 text-emerald"></i>
                        <p class="mt-2 text-[11px] font-bold">Sign In</p>
                        <p class="mt-0.5 text-[9px] text-forest/45">Guest Portal</p>
                    </a>
                    @else
                    <a href="{{ route('customer.dashboard') }}" class="rounded-2xl border border-forest/10 bg-white p-3 text-left touch-tap">
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

            <!-- Quick Suggestions Pills (Placed at the TOP as Requested) -->
            <div id="chatQuickPillsContainer" class="px-3 py-2.5 bg-white/90 border-b border-forest/10 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0 shadow-2xs">
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

            <!-- Messages Stream Container (Middle Scrollable Area) -->
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

            <!-- In-Villa Dining 1-Tap Ordering Drawer (In-House Guests) -->
            <div id="chatFoodCarousel" class="hidden px-3 py-2.5 bg-[#F3F8F5] border-t border-forest/15 shrink-0 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs">🍽️</span>
                        <span class="text-[11px] font-bold text-forest">In-Villa Dining Menu</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-forest/10 text-forest font-semibold uppercase tracking-wider">1-Tap Order</span>
                    </div>
                    <button type="button" onclick="toggleChatFoodCarousel(false)" class="text-forest/40 hover:text-forest text-[11px] p-0.5">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
                <div id="chatFoodItemsContainer" class="flex items-center gap-2.5 overflow-x-auto no-scrollbar pb-1">
                    <!-- Injected dynamically via JS -->
                </div>
            </div>

            <!-- Chat Text Input Bar (Placed at BOTTOM as Requested) -->
            <div class="p-3 sm:p-3.5 bg-white border-t border-forest/15 shrink-0 shadow-xs z-10">
                <form id="chatMessageForm" onsubmit="handleSendChatMessage(event)" class="flex items-center gap-2">
                    <input type="text" id="chatTextInput" autocomplete="off" placeholder="Ask anything: cottage availability, dining, travel..." class="flex-1 text-xs py-2.5 px-3.5 rounded-xl border border-forest/20 bg-paper/60 text-forest placeholder-forest/50 focus:outline-none focus:ring-2 focus:ring-emerald focus:border-transparent font-medium">
                    <button type="submit" id="chatSendBtn" class="h-9 w-9 rounded-xl bg-forest hover:bg-emerald text-paper flex items-center justify-center shadow-md transition shrink-0 cursor-pointer" title="Send Message">
                        <i data-lucide="send" class="w-4 h-4 text-brass"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- GLOBAL TOAST NOTIFICATION -->
    <div id="toast" class="pointer-events-none fixed left-1/2 top-5 z-[120] hidden -translate-x-1/2 rounded-xl bg-forest px-4 py-3 text-[11px] font-bold text-paper shadow-float"></div>

    <script>
        // Lucide Icons Init
        function refreshIcons() {
            if (window.lucide) lucide.createIcons();
            else if (window.createIcons) window.createIcons();
        }
        document.addEventListener('DOMContentLoaded', refreshIcons);

        // Header Scroll Shadow
        const headerBar = document.getElementById('headerBar');
        if (headerBar) {
            window.addEventListener('scroll', () => {
                const active = window.scrollY > 24;
                headerBar.classList.toggle('bg-paper/96', active);
                headerBar.classList.toggle('bg-paper/92', !active);
                headerBar.classList.toggle('shadow-card', active);
            }, {passive:true});
        }

        // Reveal animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('in'); });
        }, {threshold:.10});
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // Toast Helpers
        function availabilityToast(msg='Ready for your selected branch.') {
            const t = document.getElementById('toast');
            if (!t) return;
            t.textContent = msg;
            t.classList.remove('hidden');
            clearTimeout(window.__t);
            window.__t = setTimeout(() => t.classList.add('hidden'), 2800);
        }
        function showToast(msg, type='success') {
            availabilityToast(msg);
        }

        // Mobile Nav Drawer
        const mobileNav = document.getElementById('mobileNav');
        const menuBtn = document.getElementById('menuBtn');
        const closeMenu = document.getElementById('closeMenu');

        if (menuBtn && mobileNav) {
            menuBtn.addEventListener('click', () => {
                mobileNav.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        }
        if (closeMenu && mobileNav) {
            closeMenu.addEventListener('click', closeMobileNav);
            mobileNav.addEventListener('click', e => { if(e.target === mobileNav) closeMobileNav(); });
            mobileNav.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMobileNav));
        }
        function closeMobileNav() {
            if (mobileNav) {
                mobileNav.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        /* =========================================================================
           DIRECT CONCIERGE MESSAGING PLATFORM (CLIENT ENGINE)
           ========================================================================= */
        let activeEnquiryId = null;
        let lastMessageId = 0;
        let chatPollInterval = null;
        let currentActiveStay = null;
        let selectedTargetRoom = null;
        let branchDiningItems = [];
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
                setTimeout(() => {
                    const input = document.getElementById('chatTextInput');
                    if (input) input.focus();
                }, 120);
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
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeChat();
                closeMobileNav();
            }
        });

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
                    branchDiningItems = data.dining_items || (data.active_stay ? data.active_stay.dining_items : []) || [];
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
                        <button type="button" onclick="toggleChatFoodCarousel()" class="text-[10px] whitespace-nowrap bg-forest text-paper hover:bg-forest/90 px-2.5 py-1 rounded-full border border-forest font-bold transition flex items-center gap-1 shadow-xs">
                            🍽️ In-Villa Dining (Order Now)
                        </button>
                        <button type="button" onclick="sendQuickPrompt('🛎️ Requesting extra towels and room housekeeping service.')" class="text-[10px] whitespace-nowrap bg-emerald-50 hover:bg-emerald-100 text-emerald-950 px-2.5 py-1 rounded-full border border-emerald-200 font-semibold transition flex items-center gap-1">
                            🛎️ Extra Towels & Clean
                        </button>
                        <button type="button" onclick="sendQuickPrompt('🍽️ Would like to order dining & herbal tea to our cottage.')" class="text-[10px] whitespace-nowrap bg-amber-50 hover:bg-amber-100 text-amber-950 px-2.5 py-1 rounded-full border border-amber-200 font-semibold transition flex items-center gap-1">
                            🍵 Herbal Tea
                        </button>
                        <button type="button" onclick="sendQuickPrompt('🌿 Please send organic farm spices & estate tea selection.')" class="text-[10px] whitespace-nowrap bg-paper hover:bg-white text-forest px-2.5 py-1 rounded-full border border-forest/15 font-semibold transition flex items-center gap-1">
                            🌿 Farm Spices Delivery
                        </button>
                        <button type="button" onclick="sendQuickPrompt('🧳 Need luggage assistance / checkout porter service.')" class="text-[10px] whitespace-nowrap bg-paper hover:bg-white text-forest px-2.5 py-1 rounded-full border border-forest/15 font-semibold transition flex items-center gap-1">
                            🧳 Luggage & Porter
                        </button>
                    `;
                    renderChatFoodCarousel(branchDiningItems);
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

        function toggleChatFoodCarousel(show = null) {
            const carousel = document.getElementById('chatFoodCarousel');
            if (!carousel) return;
            if (show === null) {
                carousel.classList.toggle('hidden');
            } else if (show) {
                carousel.classList.remove('hidden');
            } else {
                carousel.classList.add('hidden');
            }
            refreshIcons();
        }

        function renderChatFoodCarousel(items) {
            const container = document.getElementById('chatFoodItemsContainer');
            if (!container) return;
            if (!items || items.length === 0) {
                container.innerHTML = `<p class="text-[10px] text-forest/50 py-1">No items currently available for today at this branch.</p>`;
                return;
            }

            container.innerHTML = items.map(dish => `
                <div class="w-36 shrink-0 bg-white rounded-xl p-2 soft-border shadow-xs flex flex-col justify-between">
                    <div class="flex items-start gap-1.5">
                        ${dish.image_url ? `<img src="${dish.image_url}" alt="${escapeHtml(dish.name)}" class="w-8 h-8 rounded-md object-cover bg-mint shrink-0">` : `<div class="w-8 h-8 rounded-md bg-mint flex items-center justify-center text-[10px] shrink-0">🍲</div>`}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full ${dish.is_vegetarian ? 'bg-green-600' : 'bg-red-600'} shrink-0"></span>
                                <h5 class="text-[10px] font-bold text-forest truncate" title="${escapeHtml(dish.name)}">${escapeHtml(dish.name)}</h5>
                            </div>
                            <div class="text-[10px] font-extrabold text-forest mt-0.5">₹${dish.price}</div>
                        </div>
                    </div>
                    <button type="button" onclick="placeChatFoodOrder(${dish.id}, '${escapeHtml(dish.name).replace(/'/g, "\\'")}', ${dish.price})" class="mt-1.5 w-full py-1 px-1.5 rounded-lg bg-forest hover:bg-forest/90 text-paper text-[9px] font-bold flex items-center justify-center gap-1 transition shadow-xs">
                        <span>1-Tap Order</span>
                        <i data-lucide="plus" class="w-2.5 h-2.5 text-brass"></i>
                    </button>
                </div>
            `).join('');
            refreshIcons();
        }

        async function placeChatFoodOrder(itemId, itemName, itemPrice) {
            if (!currentActiveStay) {
                availabilityToast('In-room food ordering is exclusively for in-house guests.');
                return;
            }

            const targetRoom = selectedTargetRoom || (currentActiveStay.rooms && currentActiveStay.rooms[0]) || 'In-House Villa';
            const confirmMsg = `Confirm in-villa delivery of 1x "${itemName}" (₹${itemPrice} + 5% GST)?\\nThis will be billed to ${targetRoom}.`;
            if (!confirm(confirmMsg)) return;

            availabilityToast(`Transmitting order for ${itemName}...`);

            try {
                const res = await fetch('/dashboard/quick-order-dining', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        reservation_id: currentActiveStay.reservation_id,
                        branch_id: currentActiveStay.branch_id,
                        target_room: targetRoom,
                        payment_choice: 'charge_to_room',
                        special_instructions: 'Ordered via Concierge Live Chat',
                        items: [{ id: itemId, quantity: 1 }]
                    })
                });
                const data = await res.json();
                if (data.success) {
                    availabilityToast(`Order #${data.order_number} confirmed! Sent to kitchen.`);
                    toggleChatFoodCarousel(false);

                    // Automatically post to chat so customer and staff see the live ticket
                    const orderChatMsg = `🍽️ [In-Villa Dining Order #${data.order_number}]\\nItem: 1x ${itemName} (₹${itemPrice})\\nFolio: Billed to ${targetRoom}\\nStatus: Received by Kitchen`;

                    appendSingleMessage({
                        id: ++lastMessageId,
                        sender_type: 'customer',
                        message: orderChatMsg,
                        target_room: targetRoom,
                        created_at: new Date().toISOString()
                    });
                    scrollChatToBottom();
                    refreshIcons();

                    if (activeEnquiryId) {
                        await fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                enquiry_id: activeEnquiryId,
                                message: orderChatMsg,
                                target_room: targetRoom,
                                customer_name: guestContact.name,
                                customer_phone: guestContact.phone,
                            })
                        });
                    }
                } else {
                    alert(data.message || 'Unable to place order. Please try again or chat with concierge.');
                }
            } catch (err) {
                console.error('Chat food order error:', err);
                alert('Connection error. Please try again.');
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
    </script>
    @stack('scripts')
</body>
</html>
