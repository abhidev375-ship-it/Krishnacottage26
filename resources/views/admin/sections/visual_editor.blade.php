@php
    $heroSlidesList = $homepageContent['hero_slides'] ?? [];
    if (empty($heroSlidesList) || !is_array($heroSlidesList)) {
        $heroSlidesList = [];
        foreach ($branches as $idx => $b) {
            $heroSlidesList[] = [
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
    }
    usort($heroSlidesList, fn($a, $b) => ($a['sort_order'] ?? 1) <=> ($b['sort_order'] ?? 1));

    $featuredRoomsList = \App\Models\RoomType::with(['branch', 'category'])
        ->where('is_active', true)
        ->where('is_bookable', true)
        ->orderBy('sort_order')
        ->take(6)
        ->get();

    $branchesList = (isset($branches) && $branches->isNotEmpty()) 
        ? $branches 
        : \App\Models\Branch::where('status', 'active')->orderBy('sort_order')->get();
    if ($branchesList->isEmpty()) {
        $branchesList = \App\Models\Branch::orderBy('sort_order')->get();
    }

    $galleryAlbumsList = \App\Models\GalleryAlbum::with(['images' => fn($q) => $q->orderBy('sort_order'), 'branch'])
        ->where('is_published', true)
        ->orderBy('sort_order')
        ->take(6)
        ->get();
@endphp

<!-- VISUAL LIVE "PPT-STYLE" HOMEPAGE EDITOR (ADM-01 DYNAMIC CANVAS) -->
<div id="visual-editor-container" class="space-y-6">
    <!-- Sticky Floating Action Toolbar -->
    <div class="sticky top-0 z-30 bg-brand-surface/95 backdrop-blur-md p-3.5 rounded-2xl border border-gray-200/80 shadow-md flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="inline-flex p-1 bg-brand-canvas rounded-xl border border-gray-200 text-xs font-bold">
                <button type="button" onclick="toggleDashboardView('visual')" id="btn-view-visual" class="px-3 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5">
                    <i data-lucide="palette" class="w-3.5 h-3.5"></i> Visual Live Editor
                </button>
                <button type="button" onclick="toggleDashboardView('operations')" id="btn-view-operations" class="px-3 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition flex items-center gap-1.5">
                    <i data-lucide="bar-chart-2" class="w-3.5 h-3.5"></i> Operations & KPIs
                </button>
            </div>

            <!-- Save Status Indicator -->
            <div id="editor-status-indicator" class="hidden sm:flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Live Changes Ready</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="navigateTo('slides')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-deep bg-brand-accent/20 hover:bg-brand-accent/30 border border-brand-accent/40 transition flex items-center gap-1.5">
                <i data-lucide="presentation" class="w-3.5 h-3.5"></i> Manage Slides CRUD
            </button>
            <button type="button" onclick="resetHomepageVisualContent()" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 border border-gray-200 transition flex items-center gap-1" title="Revert to original brand text and images">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
            </button>
            <a href="/" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-primary bg-brand-canvas hover:bg-gray-200 border border-gray-200 transition flex items-center gap-1">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Preview Site
            </a>
            <button type="button" onclick="saveHomepageVisualContent()" id="btn-save-homepage" class="px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-brand-primary hover:bg-brand-deep shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    <!-- Live Client Homepage Canvas Wrapper -->
    <div class="rounded-3xl border border-gray-300 shadow-xl overflow-hidden bg-[#FAF7F0] text-[#063F34]">
        <!-- Canvas Top Info Banner -->
        <div class="bg-brand-deep text-white px-5 py-2.5 flex items-center justify-between text-xs border-b border-brand-primary/40">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-accent animate-pulse"></span>
                <span class="font-bold tracking-wide">Interactive Homepage Canvas (Welcome Blade Preview)</span>
                <span class="text-white/60 text-[11px]">— Click any text directly to type · Hover to see live changes</span>
            </div>
            <span class="text-brand-accent text-[11px] font-semibold uppercase tracking-wider">Live WYSIWYG Mode</span>
        </div>

        <!-- CANVAS MAIN CONTENT (Mirrors welcome.blade.php layout with inline edit controls) -->
        <div class="p-4 sm:p-8 md:p-10 space-y-16 select-text" id="homepage-canvas">

            <!-- =========================================================================
                 1. HERO SECTION: EXACT WELCOME SCREEN CANVAS & PPT LIVE STUDIO
                 ========================================================================= -->
            <section class="relative w-full pt-1 pb-4">
                
                <!-- Section Header Toolbar -->
                <div class="flex items-center justify-between mb-3 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#063F34] flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-[#0B5D4B]"></i>
                            <span>Section 1: Hero Carousel Canvas (Live Welcome Replica)</span>
                        </span>
                        <span class="hidden sm:inline-block text-[11px] text-[#5A6B65] bg-white border border-gray-200 px-2.5 py-0.5 rounded-full font-medium">
                            ✍ Click any text below to edit directly like on PowerPoint
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openHeroSlideModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#063F34] hover:bg-[#0B5D4B] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                            <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-[#C7A76A]"></i> Add Slide
                        </button>
                    </div>
                </div>

                <!-- Full-Bleed Edge-to-Edge Carousel Canvas (Exact Welcome Screen Replica) -->
                <div id="admin-hero-carousel-preview" class="relative w-full min-h-[460px] sm:min-h-[500px] lg:min-h-[540px] rounded-2xl overflow-hidden shadow-xl bg-[#062922] group select-none flex flex-col justify-between">
                    
                    <!-- Slides Container -->
                    <div id="admin-slides-container" class="relative w-full h-full flex-1 min-h-[460px] sm:min-h-[500px] lg:min-h-[540px]">
                        @foreach($heroSlidesList as $sIdx => $s)
                            <div class="admin-preview-slide absolute inset-0 transition-all duration-700 ease-out {{ $sIdx === 0 ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 pointer-events-none z-0' }}" data-admin-slide-idx="{{ $sIdx }}">
                                <!-- Natural Bright Background Image (center 38% for cottage & mountain framing) -->
                                <img src="{{ $s['image'] }}" class="w-full h-full object-cover" style="object-position: center 38%;" alt="{{ $s['title'] }}" />
                                
                                <!-- Soft Luxury Scrim Gradient (No dark black shadows) -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-black/20 to-transparent sm:from-black/50 sm:via-transparent sm:to-black/20 pointer-events-none"></div>

                                <!-- Top Destination Tag -->
                                <div class="absolute top-4 left-4 sm:top-6 sm:left-7 z-20 flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/40 backdrop-blur-md text-white text-[10px] sm:text-xs font-semibold tracking-wider uppercase border border-white/20 shadow-xs">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-[#C7A76A]"></i>
                                        <span class="wysiwyg-text" contenteditable="true" title="Click to edit tag">{{ $s['tag'] ?? ($s['subtitle'] ?? 'Country Side Cottages') }}</span>
                                    </span>
                                    @if(!empty($s['badge']))
                                        <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#C7A76A]/25 backdrop-blur-md text-[#C7A76A] text-[10px] sm:text-[11px] font-bold border border-[#C7A76A]/30">
                                            <span class="wysiwyg-text" contenteditable="true" title="Click to edit badge">{{ $s['badge'] }}</span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Top Right Counter & Direct Slide Photo Action -->
                                <div class="absolute top-4 right-4 sm:top-6 sm:right-7 z-20 flex items-center gap-2">
                                    <div class="text-white/90 bg-black/40 backdrop-blur-md px-2.5 sm:px-3.5 py-1 rounded-full text-[11px] sm:text-xs font-mono border border-white/15">
                                        <span class="text-[#C7A76A] font-bold">0{{ $sIdx + 1 }}</span> / 0{{ count($heroSlidesList) }}
                                    </div>
                                    <button type="button" onclick="openHeroSlideModal({{ $sIdx }})" class="px-3 py-1.5 rounded-full bg-white hover:bg-[#C7A76A] text-[#063F34] hover:text-[#063F34] shadow-md transition font-bold text-xs flex items-center gap-1.5 cursor-pointer" title="Change or Crop image with 16:9 ratio">
                                        <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                        <span>Change / Crop Photo</span>
                                    </button>
                                </div>

                                <!-- Centered Main Typography (Clean PPT Editable, High Breathing Room) -->
                                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-8 max-w-3xl mx-auto text-white pb-6 sm:pb-36 lg:pb-28 pt-4 pointer-events-none">
                                    <!-- Eyebrow -->
                                    <p class="text-[#C7A76A] text-[10px] sm:text-xs tracking-[.28em] mb-2 sm:mb-2.5 drop-shadow-xs font-bold uppercase pointer-events-auto">
                                        <span id="edit-hero-eyebrow" class="wysiwyg-text" contenteditable="true" title="Click to edit Eyebrow">{{ $homepageContent['hero_eyebrow'] ?? ($s['subtitle'] ?? 'PEACEFUL NATURE SANCTUARY') }}</span>
                                    </p>

                                    <!-- Headline -->
                                    <h1 class="serif text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight leading-tight sm:leading-[1.1] text-white drop-shadow-md max-w-2xl pointer-events-auto">
                                        <span id="edit-hero-heading-1" class="wysiwyg-text" contenteditable="true" title="Click to edit Headline">{{ $homepageContent['hero_heading_1'] ?? $s['title'] }}</span>
                                    </h1>

                                    <!-- Description -->
                                    <p class="mt-2.5 text-xs sm:text-sm md:text-base text-white/90 max-w-lg leading-relaxed drop-shadow-xs font-normal line-clamp-2 sm:line-clamp-3 pointer-events-auto">
                                        <span id="edit-hero-desc" class="wysiwyg-text" contenteditable="true" title="Click to edit Description">{{ $homepageContent['hero_description'] ?? $s['description'] }}</span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Navigation Arrows (Hidden on mobile to avoid text cut, Refined on desktop) -->
                    <button type="button" onclick="cycleAdminPreviewSlide(-1)" class="hidden md:grid absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-30 h-10 w-10 sm:h-11 sm:w-11 place-items-center rounded-full bg-black/25 hover:bg-[#C7A76A] text-white hover:text-[#063F34] backdrop-blur-md border border-white/20 hover:border-[#C7A76A] transition-all shadow-md cursor-pointer" aria-label="Previous Slide">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>
                    <button type="button" onclick="cycleAdminPreviewSlide(1)" class="hidden md:grid absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-30 h-10 w-10 sm:h-11 sm:w-11 place-items-center rounded-full bg-black/25 hover:bg-[#C7A76A] text-white hover:text-[#063F34] backdrop-blur-md border border-white/20 hover:border-[#C7A76A] transition-all shadow-md cursor-pointer" aria-label="Next Slide">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>

                    @if(count($heroSlidesList) > 1)
                    <!-- Bottom Slide Navigation Tabs (Positioned cleanly above search bar) -->
                    <div class="hidden md:flex absolute bottom-24 lg:bottom-22 left-1/2 -translate-x-1/2 z-30 items-center gap-1.5 sm:gap-2 bg-black/35 backdrop-blur-md p-1 sm:px-2.5 sm:py-1.5 rounded-full border border-white/20 shadow-md" id="admin-preview-dots">
                        @foreach($heroSlidesList as $dIdx => $s)
                            <button type="button" onclick="goToAdminPreviewSlide({{ $dIdx }})" class="admin-dot flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-semibold cursor-pointer transition-all {{ $dIdx === 0 ? 'bg-[#C7A76A] text-[#063F34] font-bold shadow-xs' : 'text-white/80 hover:bg-white/15' }}">
                                <span class="font-mono font-bold">{{ sprintf('%02d', $dIdx + 1) }}</span>
                                <span class="hidden md:inline whitespace-nowrap">{{ $s['title'] }}</span>
                            </button>
                        @endforeach
                    </div>
                    @endif

                    <!-- Classic Hotel Booking Engine Search Bar Replica (Pinned Inside Slide at Bottom as Last) -->
                    <div class="absolute bottom-5 lg:bottom-6 inset-x-0 z-40 max-w-5xl mx-auto px-4 pointer-events-auto">
                        <div class="w-full bg-white text-[#063F34] rounded-2xl lg:rounded-full p-2.5 sm:p-3 lg:p-3.5 shadow-[0_20px_50px_rgba(0,0,0,0.35)] border border-[#063F34]/15">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2 sm:gap-2.5 items-center">
                                <!-- Field 1: Destination -->
                                <div class="lg:col-span-4 px-3.5 py-2 bg-[#FAF7F0]/60 rounded-xl lg:rounded-l-full border border-[#063F34]/10">
                                    <label class="block text-[9px] uppercase tracking-wider font-bold text-[#063F34]/50 flex items-center gap-1.5 mb-0.5">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#0B5D4B]"></i> Destination
                                    </label>
                                    <span class="text-xs sm:text-sm font-bold text-[#063F34]">All Branches (Cottages Central)</span>
                                </div>

                                <!-- Field 2: Check-in / Check-out -->
                                <div class="lg:col-span-4 grid grid-cols-2 gap-2">
                                    <div class="px-3 py-2 bg-[#FAF7F0]/60 rounded-xl border border-[#063F34]/10">
                                        <label class="block text-[9px] uppercase tracking-wider font-bold text-[#063F34]/50 flex items-center gap-1 mb-0.5">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#0B5D4B]"></i> Check-In
                                        </label>
                                        <span class="text-xs font-bold text-[#063F34]">Tomorrow</span>
                                    </div>
                                    <div class="px-3 py-2 bg-[#FAF7F0]/60 rounded-xl border border-[#063F34]/10">
                                        <label class="block text-[9px] uppercase tracking-wider font-bold text-[#063F34]/50 flex items-center gap-1 mb-0.5">
                                            <i data-lucide="calendar-check-2" class="w-3.5 h-3.5 text-[#0B5D4B]"></i> Check-Out
                                        </label>
                                        <span class="text-xs font-bold text-[#063F34]">+2 Days</span>
                                    </div>
                                </div>

                                <!-- Field 3: Guests & Button -->
                                <div class="lg:col-span-4 grid grid-cols-2 gap-2">
                                    <div class="px-3 py-2 bg-[#FAF7F0]/60 rounded-xl border border-[#063F34]/10">
                                        <label class="block text-[9px] uppercase tracking-wider font-bold text-[#063F34]/50 flex items-center gap-1 mb-0.5">
                                            <i data-lucide="users" class="w-3.5 h-3.5 text-[#0B5D4B]"></i> Guests
                                        </label>
                                        <span class="text-xs font-bold text-[#063F34]">2 Guests</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-full h-full min-h-[44px] py-2.5 px-3 rounded-xl lg:rounded-r-full bg-[#C7A76A] text-[#063F34] font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md">
                                            <i data-lucide="search" class="w-4 h-4 shrink-0"></i>
                                            <span class="whitespace-nowrap">Check Stays</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ================= HERO CAROUSEL SLIDES MANAGER & REORDER PANEL ================= -->
                <div class="mt-8 bg-white rounded-2xl border border-gray-200/90 p-5 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#C7A76A]"></span>
                                <h3 class="font-bold text-sm text-[#063F34]">Hero Carousel Slides & Branch Showcase</h3>
                                <span id="hero-slides-counter-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#063F34]/10 text-[#063F34]">{{ count($heroSlidesList) }} Slides</span>
                            </div>
                            <p class="text-[11px] text-[#5A6B65] mt-0.5">Add custom destination slides, crop photos to 4:3 sizing, and adjust the slide sequence displayed in the hero carousel.</p>
                        </div>
                        <button type="button" onclick="openHeroSlideModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#063F34] hover:bg-[#0B5D4B] text-white text-xs font-bold shadow-xs transition cursor-pointer shrink-0">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-[#C7A76A]"></i>
                            <span>Add New Slide</span>
                        </button>
                    </div>

                    <!-- Slide Cards Grid / List -->
                    <div id="hero-slides-manager-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($heroSlidesList as $sIdx => $s)
                            <div class="hero-slide-item rounded-2xl border border-gray-200 bg-[#F7F5EF]/60 p-3 flex flex-col justify-between space-y-3 hover:border-[#0B5D4B]/40 hover:shadow-md transition group" data-slide-index="{{ $sIdx }}">
                                <div class="space-y-2.5">
                                    <!-- Thumbnail with Order & Tag -->
                                    <div class="relative h-36 rounded-xl overflow-hidden bg-black/5 border border-gray-200 shadow-2xs">
                                        <img src="{{ $s['image'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $s['title'] }}">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/20"></div>
                                        
                                        <!-- Order Badge -->
                                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-[#063F34] text-white text-[10px] font-bold shadow-xs">
                                            Slide #{{ $s['sort_order'] ?? ($sIdx + 1) }}
                                        </span>

                                        <!-- Status Badge with 1-click toggle -->
                                        <button type="button" onclick="toggleHeroSlideStatus({{ $sIdx }})" class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[10px] font-bold cursor-pointer transition {{ ($s['status'] ?? 'active') === 'active' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-500 hover:bg-gray-600 text-white' }}">
                                            {{ ucfirst($s['status'] ?? 'active') }}
                                        </button>

                                        <!-- Tag (Bottom Left) -->
                                        <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-full bg-white/90 text-[#063F34] text-[9px] font-bold backdrop-blur-xs">
                                            {{ $s['tag'] ?? 'Kerala' }}
                                        </span>

                                        @if(!empty($s['badge']))
                                            <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-[#C7A76A] text-[#063F34] text-[9px] font-bold">
                                                {{ $s['badge'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Content Info -->
                                    <div>
                                        <h4 class="font-bold text-xs text-[#063F34] line-clamp-1">{{ $s['title'] }}</h4>
                                        @if(!empty($s['subtitle']))
                                            <p class="text-[10px] font-semibold text-[#0B5D4B] mt-0.5">{{ $s['subtitle'] }}</p>
                                        @endif
                                        <p class="text-[10px] text-[#5A6B65] mt-1 line-clamp-2 leading-relaxed">{{ $s['description'] }}</p>
                                    </div>
                                </div>

                                <!-- Action Toolbar: Reorder Up/Down, Edit with Cropper, Delete -->
                                <div class="pt-2 border-t border-gray-200/70 flex items-center justify-between gap-1">
                                    <!-- Move Order Controls -->
                                    <div class="flex items-center gap-1">
                                        <button type="button" onclick="moveHeroSlide({{ $sIdx }}, -1)" class="p-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 transition cursor-pointer disabled:opacity-40 disabled:pointer-events-none" title="Move Slide Earlier" {{ $sIdx === 0 ? 'disabled' : '' }}>
                                            <i data-lucide="chevron-up" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="button" onclick="moveHeroSlide({{ $sIdx }}, 1)" class="p-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 transition cursor-pointer disabled:opacity-40 disabled:pointer-events-none" title="Move Slide Later" {{ $sIdx === count($heroSlidesList) - 1 ? 'disabled' : '' }}>
                                            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>

                                    <!-- Edit & Delete -->
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" onclick="openHeroSlideModal({{ $sIdx }})" class="px-2.5 py-1 rounded-lg border border-[#0B5D4B]/30 bg-emerald-50/60 hover:bg-emerald-100 text-[#0B5D4B] text-[11px] font-bold transition flex items-center gap-1 cursor-pointer">
                                            <i data-lucide="edit-2" class="w-3 h-3"></i> Edit & Crop
                                        </button>
                                        <button type="button" onclick="deleteHeroSlide({{ $sIdx }})" class="p-1 rounded-lg border border-red-200 bg-red-50/50 hover:bg-red-100 text-red-700 transition cursor-pointer" title="Delete Slide">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Hidden elements for backward compatibility -->
                <div class="hidden">
                    <img id="edit-hero-img-main" src="{{ $homepageContent['hero_image_main'] ?? '' }}" alt="Main photo">
                    <img id="edit-hero-img-sec" src="{{ $homepageContent['hero_image_secondary'] ?? '' }}" alt="Secondary photo">
                    <span id="edit-hero-card-title">{{ $homepageContent['hero_card_title'] ?? '' }}</span>
                    <span id="edit-hero-card-desc">{{ $homepageContent['hero_card_description'] ?? '' }}</span>
                    <span id="edit-hero-heading-2">{{ $homepageContent['hero_heading_2'] ?? '' }}</span>
                </div>
            </section>

            <!-- =========================================================================
                 2. WELCOME & PHILOSOPHY: EDITORIAL STORY & RESORT SHOWCASE
                 ========================================================================= -->
            <section class="pt-10 border-t border-gray-200/80">
                <div class="flex items-center justify-between mb-4 px-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#063F34] flex items-center gap-1.5">
                        <i data-lucide="compass" class="w-3.5 h-3.5 text-[#0B5D4B]"></i>
                        <span>Section 2: Welcome &amp; Philosophy (Live Preview)</span>
                    </span>
                    <span class="text-[11px] text-[#5A6B65] bg-white border border-gray-200 px-2.5 py-0.5 rounded-full font-medium">
                        ✍ Click any headline, story or card below to edit directly
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center bg-[#FAF7F0] p-6 sm:p-8 rounded-3xl border border-gray-200/90 shadow-xs">
                    <!-- Left Column: Rich Narrative & Signature Experiences (7 Cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Eyebrow & Brand Seal -->
                        <div class="space-y-2.5">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#063F34]/5 border border-[#063F34]/15 text-[#063F34] text-xs font-bold uppercase tracking-[0.22em]">
                                <i data-lucide="compass" class="w-3.5 h-3.5 text-[#C7A76A]"></i>
                                <span id="edit-welcome-eyebrow" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_eyebrow'] ?? 'Slow Living In Kerala · Est. 2024' }}</span>
                            </div>
                            <h2 class="serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#063F34] tracking-tight leading-[1.18]">
                                <span id="edit-welcome-heading" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_heading'] ?? 'A Sanctuary of Hillside Serenity, Pure Mist & Handcrafted Wooden Cottages' }}</span>
                            </h2>
                        </div>

                        <!-- Narrative Story -->
                        <div class="space-y-3 text-[#063F34]/80 text-xs sm:text-sm leading-relaxed font-normal">
                            <p>
                                <span id="edit-welcome-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_description'] ?? 'Tucked into the misty emerald slopes of Idukki, Munnar, Rajakkadu, Rajakumari, Udumbanchola, and Adimali, Country Side Cottages was conceived as a timeless sanctuary where hurry gives way to stillness. Surrounded by towering silver oaks, organic cardamom valleys, and rolling tea estates, our private wooden residences invite you to breathe deeply and reconnect with what matters.' }}</span>
                            </p>
                            <p>
                                <span id="edit-welcome-desc-2" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_description_2'] ?? 'From the gentle clatter of earthenware clay pots simmering with authentic Kerala heirloom spices to quiet sunset hours on cedar verandas, every detail is considered for restorative, peaceful slow living.' }}</span>
                            </p>
                        </div>

                        <!-- 4 Signature Experience Cards (2x2 Grid) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <!-- Card 1 -->
                            <div class="p-3.5 rounded-2xl bg-white border border-gray-200/90 shadow-2xs hover:border-[#0B5D4B]/40 transition group">
                                <div class="w-8 h-8 rounded-xl bg-[#063F34]/5 text-[#0B5D4B] flex items-center justify-center mb-2.5">
                                    <i data-lucide="home" class="w-4 h-4"></i>
                                </div>
                                <h4 class="font-bold text-[#063F34] text-xs">
                                    <span id="edit-welcome-card-1-title" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_card_1_title'] ?? 'Private Wooden Cottages' }}</span>
                                </h4>
                                <p class="text-[11px] text-[#5A6B65] mt-1 leading-relaxed">
                                    <span id="edit-welcome-card-1-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_card_1_desc'] ?? 'Handcrafted teak & cedar residences featuring wide open verandas overlooking misty valleys.' }}</span>
                                </p>
                            </div>

                            <!-- Card 2 -->
                            <div class="p-3.5 rounded-2xl bg-white border border-gray-200/90 shadow-2xs hover:border-[#0B5D4B]/40 transition group">
                                <div class="w-8 h-8 rounded-xl bg-[#063F34]/5 text-[#0B5D4B] flex items-center justify-center mb-2.5">
                                    <i data-lucide="utensils" class="w-4 h-4"></i>
                                </div>
                                <h4 class="font-bold text-[#063F34] text-xs">
                                    <span id="edit-welcome-card-2-title" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_card_2_title'] ?? 'Clay-Pot Estate Kitchen' }}</span>
                                </h4>
                                <p class="text-[11px] text-[#5A6B65] mt-1 leading-relaxed">
                                    <span id="edit-welcome-card-2-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_card_2_desc'] ?? 'Authentic slow-cooked Kerala recipes made in traditional earthenware with single-origin spices.' }}</span>
                                </p>
                            </div>

                            <!-- Card 3 -->
                            <div class="p-3.5 rounded-2xl bg-white border border-gray-200/90 shadow-2xs hover:border-[#0B5D4B]/40 transition group">
                                <div class="w-8 h-8 rounded-xl bg-[#063F34]/5 text-[#0B5D4B] flex items-center justify-center mb-2.5">
                                    <i data-lucide="trees" class="w-4 h-4"></i>
                                </div>
                                <h4 class="font-bold text-[#063F34] text-xs">
                                    <span id="edit-welcome-card-3-title" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_card_3_title'] ?? 'Estate Trails & Waterfalls' }}</span>
                                </h4>
                                <p class="text-[11px] text-[#5A6B65] mt-1 leading-relaxed">
                                    <span id="edit-welcome-card-3-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_card_3_desc'] ?? 'Guided morning walks through tea, cardamom, and clove groves leading to secret hillside streams.' }}</span>
                                </p>
                            </div>

                            <!-- Card 4 -->
                            <div class="p-3.5 rounded-2xl bg-white border border-gray-200/90 shadow-2xs hover:border-[#0B5D4B]/40 transition group">
                                <div class="w-8 h-8 rounded-xl bg-[#063F34]/5 text-[#0B5D4B] flex items-center justify-center mb-2.5">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                                </div>
                                <h4 class="font-bold text-[#063F34] text-xs">
                                    <span id="edit-welcome-card-4-title" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_card_4_title'] ?? '24/7 Dedicated Concierge' }}</span>
                                </h4>
                                <p class="text-[11px] text-[#5A6B65] mt-1 leading-relaxed">
                                    <span id="edit-welcome-card-4-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_card_4_desc'] ?? 'Attentive in-cottage hosts, private jeep safaris, bonfire evenings, and custom excursions.' }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Host Welcome Quote Card -->
                        <div class="p-4 rounded-2xl bg-white border-l-4 border-[#C7A76A] border border-gray-200 shadow-2xs flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#063F34] text-[#C7A76A] flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="quote" class="w-4 h-4"></i>
                            </div>
                            <div class="space-y-1 text-xs text-[#063F34]/80">
                                <p class="serif italic">
                                    "<span id="edit-welcome-quote" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_quote'] ?? 'We created Country Side Cottages as a haven where time gently pauses, hot spiced chai is poured by the rain, and mist greets you each sunrise.' }}</span>"
                                </p>
                                <span class="block text-[11px] font-bold text-[#063F34]/60 uppercase tracking-wider">
                                    <span id="edit-welcome-quote-author" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_quote_author'] ?? '— The Resident Hosts · Country Side Cottages, Idukki, Munnar & Rajakkadu' }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Action Links -->
                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#063F34] text-white font-bold text-xs shadow-sm">
                                <span id="edit-welcome-btn-1-text" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['welcome_btn_1_text'] ?? 'Explore Accommodations' }}</span>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-[#C7A76A]"></i>
                            </span>
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-[#063F34] font-semibold text-xs border border-gray-200 shadow-2xs">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5 text-[#0B5D4B]"></i>
                                <span id="edit-welcome-btn-2-text" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_btn_2_text'] ?? 'Ask Concierge Desk' }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Right Column: Editorial Multi-Layer Photography Showcase (5 Cols) -->
                    <div class="lg:col-span-5 relative mt-4 lg:mt-0">
                        <div class="relative mx-auto max-w-md lg:max-w-none">
                            <!-- Main Cottage Veranda Photo -->
                            <div class="relative overflow-hidden rounded-3xl shadow-2xl border-4 border-white bg-[#063F34]/10 aspect-[4/5] sm:h-[430px] group/mainimg">
                                <img id="edit-welcome-img-main" src="{{ $homepageContent['welcome_img_main'] ?? '' }}" 
                                     alt="Country Side Cottages Veranda" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/mainimg:opacity-100 transition-opacity flex items-center justify-center p-2 z-30">
                                    <button type="button" onclick="openImagePicker('edit-welcome-img-main')" class="px-3 py-1.5 rounded-xl bg-white/95 hover:bg-white text-[#063F34] font-bold text-xs shadow-lg flex items-center gap-1.5 transition cursor-pointer">
                                        <i data-lucide="image" class="w-4 h-4 text-[#0B5D4B]"></i>
                                        <span>Change Veranda Photo</span>
                                    </button>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                                <div class="absolute bottom-4 left-4 right-4 text-white z-20">
                                    <p class="text-[10px] uppercase tracking-wider text-[#C7A76A] font-bold">
                                        <span id="edit-welcome-img-main-tag" class="wysiwyg-text text-[#C7A76A]" contenteditable="true">{{ $homepageContent['welcome_img_main_tag'] ?? 'Hillside Sanctuaries' }}</span>
                                    </p>
                                    <h3 class="serif text-base sm:text-lg font-bold text-white leading-snug">
                                        <span id="edit-welcome-img-main-title" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['welcome_img_main_title'] ?? 'Private Verandas & Cool Mist' }}</span>
                                    </h3>
                                </div>
                            </div>

                            <!-- Floating Inset Photo (Bottom Left) -->
                            <div class="absolute -bottom-5 -left-3 sm:-left-6 w-40 sm:w-48 h-32 sm:h-40 rounded-2xl overflow-hidden shadow-2xl border-4 border-white bg-[#063F34]/10 group/insetimg z-30">
                                <img id="edit-welcome-img-inset" src="{{ $homepageContent['welcome_img_inset'] ?? '' }}" 
                                     alt="Authentic Kerala Clay Pot Dining" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/insetimg:opacity-100 transition-opacity flex items-center justify-center p-2 z-40">
                                    <button type="button" onclick="openImagePicker('edit-welcome-img-inset')" class="px-2.5 py-1 rounded-lg bg-white/95 hover:bg-white text-[#063F34] font-bold text-[11px] shadow-lg flex items-center gap-1 transition cursor-pointer">
                                        <i data-lucide="image" class="w-3.5 h-3.5 text-[#0B5D4B]"></i>
                                        <span>Change</span>
                                    </button>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent pointer-events-none"></div>
                                <div class="absolute bottom-2 left-2 right-2 text-white z-20 pointer-events-auto">
                                    <span class="text-[10px] font-bold block leading-tight text-white">
                                        <span id="edit-welcome-img-inset-title" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['welcome_img_inset_title'] ?? 'Farm Clay-Pot Kitchen' }}</span>
                                    </span>
                                    <span class="text-[9px] text-[#C7A76A] block">
                                        <span id="edit-welcome-img-inset-subtitle" class="wysiwyg-text text-[#C7A76A]" contenteditable="true">{{ $homepageContent['welcome_img_inset_subtitle'] ?? 'Authentic Heirloom Spices' }}</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Floating Gold Rating Seal Badge (Top Right) -->
                            <div class="absolute -top-4 -right-2 sm:-right-4 bg-[#063F34] text-white p-3 rounded-2xl shadow-xl border border-[#C7A76A]/30 flex items-center gap-2.5 z-30">
                                <div class="text-[#C7A76A]">
                                    <div class="flex items-center gap-0.5 text-[#C7A76A]">
                                        <i data-lucide="star" class="w-3 h-3 fill-[#C7A76A] text-[#C7A76A]"></i>
                                        <i data-lucide="star" class="w-3 h-3 fill-[#C7A76A] text-[#C7A76A]"></i>
                                        <i data-lucide="star" class="w-3 h-3 fill-[#C7A76A] text-[#C7A76A]"></i>
                                        <i data-lucide="star" class="w-3 h-3 fill-[#C7A76A] text-[#C7A76A]"></i>
                                        <i data-lucide="star" class="w-3 h-3 fill-[#C7A76A] text-[#C7A76A]"></i>
                                    </div>
                                    <span class="text-sm sm:text-base font-black text-white">
                                        <span id="edit-welcome-rating-val" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['welcome_rating_val'] ?? '4.95 / 5' }}</span>
                                    </span>
                                </div>
                                <div class="border-l border-white/20 pl-2.5">
                                    <span class="block text-[10px] font-bold text-white uppercase tracking-wider">
                                        <span id="edit-welcome-rating-title" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['welcome_rating_title'] ?? 'Verified Stays' }}</span>
                                    </span>
                                    <span class="block text-[9px] text-white/70">
                                        <span id="edit-welcome-rating-subtitle" class="wysiwyg-text text-white/80" contenteditable="true">{{ $homepageContent['welcome_rating_subtitle'] ?? '500+ Guests · Rajakkad & Munnar' }}</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Floating Micro-Stats Strip (Bottom Right) -->
                            <div class="hidden sm:flex absolute bottom-3 right-3 bg-white/95 text-[#063F34] px-3 py-1.5 rounded-xl shadow-lg border border-gray-200 items-center gap-2 text-[10px] font-bold z-20">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#0B5D4B]"></i>
                                <span id="edit-welcome-stats-text" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['welcome_stats_text'] ?? '100% Organic Plantation · 24/7 Butler' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 3. FEATURED COTTAGES CAROUSEL (WITH LIVE BRANCH FILTER)
                 ========================================================================= -->
            <section class="pt-10 border-t border-gray-200/80">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-4 gap-3 px-1">
                    <div class="space-y-1">
                        <span id="edit-cottages-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                            {{ $homepageContent['cottages_eyebrow'] ?? 'ACCOMMODATIONS' }}
                        </span>
                        <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                            <span id="edit-cottages-heading" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['cottages_heading'] ?? 'Featured Cottages & Suites' }}
                            </span>
                        </h2>
                        <p class="text-xs text-[#5A6B65]">
                            <span id="edit-cottages-desc" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['cottages_description'] ?? 'Explore our private wooden cottages and hillside suites crafted for restorative slow stays.' }}
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-[#0B5D4B]">
                            <span id="edit-cottages-view-all-text" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['cottages_view_all_text'] ?? 'View All' }}
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Notice banner explaining dynamic rooms -->
                <div class="p-3 mb-4 rounded-xl bg-emerald-50/70 border border-emerald-200 text-[#063F34] text-xs flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-[#0B5D4B] shrink-0"></i>
                        <span><strong>Live Database Cottages:</strong> The cards below preview your active room types. Pricing, amenities, and room photos are managed dynamically in the <strong>Stays &gt; Rooms</strong> section.</span>
                    </div>
                    <button type="button" onclick="navigateTo('rooms')" class="px-2.5 py-1 rounded-lg bg-[#063F34] text-white font-bold text-[11px] shrink-0 hover:bg-[#0B5D4B] transition cursor-pointer">
                        Manage Rooms
                    </button>
                </div>

                <!-- Cottages Grid Preview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($featuredRoomsList->take(3) as $r)
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs hover:shadow-md transition">
                            <div class="relative h-44 overflow-hidden flex items-center justify-center bg-gray-100">
                                @if($r->cover_image_url)
                                <img src="{{ $r->cover_image_url }}" class="w-full h-full object-cover" alt="{{ $r->name }}">
                                @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-400 p-2 text-center">
                                    <i data-lucide="bed-double" class="w-8 h-8 text-gray-300 mb-1"></i>
                                    <span class="text-[11px] font-medium text-gray-500">{{ $r->name }}</span>
                                </div>
                                @endif
                                <span class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-full bg-white/90 text-[#063F34] text-[10px] font-bold">
                                    {{ $r->branch ? $r->branch->city : 'Kerala' }}
                                </span>
                            </div>
                            <div class="p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-sm text-[#063F34]">{{ $r->name }}</h4>
                                    <span class="text-xs font-extrabold text-[#0B5D4B]">₹{{ number_format($r->base_price, 0) }}/nt</span>
                                </div>
                                <p class="text-xs text-[#5A6B65] line-clamp-2">{{ $r->tagline ?: 'Handcrafted luxury cottage with garden sit-out.' }}</p>
                                <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-[#5A6B65]">
                                    <span>Up to {{ $r->max_guests }} Guests</span>
                                    <span class="font-bold text-[#0B5D4B]">Book &rarr;</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- =========================================================================
                 4. CURATED EXPERIENCES: CLEAN 3-CARD SHOWCASE
                 ========================================================================= -->
            <section class="pt-10 border-t border-gray-200/80">
                <div class="text-center max-w-2xl mx-auto mb-6 space-y-1">
                    <span id="edit-experience-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                        {{ $homepageContent['experience_eyebrow'] ?? 'IMMERSIVE EXPERIENCES' }}
                    </span>
                    <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                        <span id="edit-experience-heading" class="wysiwyg-text" contenteditable="true">
                            {{ $homepageContent['experience_heading'] ?? 'Life at Country Side Cottages' }}
                        </span>
                    </h2>
                    <p class="text-xs sm:text-sm text-[#063F34]/70">
                        <span id="edit-experience-desc" class="wysiwyg-text" contenteditable="true">
                            {{ $homepageContent['experience_description'] ?? 'Immerse your senses in authentic Kerala dining, farm spices, and scenic hillside nature.' }}
                        </span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Experience Card 1: Dining -->
                    <div class="rounded-3xl overflow-hidden bg-white border border-gray-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                        <div>
                            <div class="relative h-56 w-full overflow-hidden bg-gray-100 group/exp1">
                                <img id="edit-experience-card-1-img" src="{{ $homepageContent['experience_card_1_img'] ?? ($homepageContent['dining_image'] ?? '') }}" 
                                     alt="Plantation Kitchen Dining" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/exp1:opacity-100 transition-opacity flex items-center justify-center p-2 z-30">
                                    <button type="button" onclick="openImagePicker('edit-experience-card-1-img')" class="px-3 py-1.5 rounded-xl bg-white/95 hover:bg-white text-[#063F34] font-bold text-xs shadow-lg flex items-center gap-1.5 transition cursor-pointer">
                                        <i data-lucide="image" class="w-4 h-4 text-[#0B5D4B]"></i>
                                        <span>Change Card Photo</span>
                                    </button>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                                <div class="absolute bottom-3 left-4 right-4 text-white z-20">
                                    <span class="text-[9px] uppercase tracking-wider font-bold text-[#C7A76A] block">
                                        <span id="edit-experience-card-1-tag" class="wysiwyg-text text-[#C7A76A]" contenteditable="true">{{ $homepageContent['experience_card_1_tag'] ?? 'CUISINE' }}</span>
                                    </span>
                                    <h3 class="serif text-lg font-bold text-white">
                                        <span id="edit-experience-card-1-title" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['experience_card_1_title'] ?? 'Plantation Kitchen' }}</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="p-5 space-y-2">
                                <p class="text-xs text-[#063F34]/75 leading-relaxed">
                                    <span id="edit-experience-card-1-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['experience_card_1_desc'] ?? 'Clay-pot Kerala cuisine, slow-cooked curries, fresh cardamom chai, and wholesome countryside harvests served daily.' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0B5D4B]">
                                <span id="edit-experience-card-1-btn" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['experience_card_1_btn'] ?? 'Explore Dining & Menu' }}</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Experience Card 2: Spices -->
                    <div class="rounded-3xl overflow-hidden bg-white border border-gray-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                        <div>
                            <div class="relative h-56 w-full overflow-hidden bg-gray-100 group/exp2">
                                <img id="edit-experience-card-2-img" src="{{ $homepageContent['experience_card_2_img'] ?? '' }}" 
                                     alt="Country Side Spices Farm" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/exp2:opacity-100 transition-opacity flex items-center justify-center p-2 z-30">
                                    <button type="button" onclick="openImagePicker('edit-experience-card-2-img')" class="px-3 py-1.5 rounded-xl bg-white/95 hover:bg-white text-[#063F34] font-bold text-xs shadow-lg flex items-center gap-1.5 transition cursor-pointer">
                                        <i data-lucide="image" class="w-4 h-4 text-[#0B5D4B]"></i>
                                        <span>Change Card Photo</span>
                                    </button>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                                <div class="absolute bottom-3 left-4 right-4 text-white z-20">
                                    <span class="text-[9px] uppercase tracking-wider font-bold text-[#C7A76A] block">
                                        <span id="edit-experience-card-2-tag" class="wysiwyg-text text-[#C7A76A]" contenteditable="true">{{ $homepageContent['experience_card_2_tag'] ?? 'ESTATE HARVEST' }}</span>
                                    </span>
                                    <h3 class="serif text-lg font-bold text-white">
                                        <span id="edit-experience-card-2-title" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['experience_card_2_title'] ?? 'Country Side Spices' }}</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="p-5 space-y-2">
                                <p class="text-xs text-[#063F34]/75 leading-relaxed">
                                    <span id="edit-experience-card-2-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['experience_card_2_desc'] ?? 'Experience genuine estate-cultivated cardamom, black pepper, and whole cloves delivered right to your cottage door.' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0B5D4B]">
                                <span id="edit-experience-card-2-btn" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['experience_card_2_btn'] ?? 'Shop Estate Spices' }}</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Experience Card 3: Nature & Facilities -->
                    <div class="rounded-3xl overflow-hidden bg-white border border-gray-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                        <div>
                            <div class="relative h-56 w-full overflow-hidden bg-gray-100 group/exp3">
                                <img id="edit-experience-card-3-img" src="{{ $homepageContent['experience_card_3_img'] ?? ($homepageContent['experience_image_main'] ?? '') }}" 
                                     alt="Nature Surroundings" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/exp3:opacity-100 transition-opacity flex items-center justify-center p-2 z-30">
                                    <button type="button" onclick="openImagePicker('edit-experience-card-3-img')" class="px-3 py-1.5 rounded-xl bg-white/95 hover:bg-white text-[#063F34] font-bold text-xs shadow-lg flex items-center gap-1.5 transition cursor-pointer">
                                        <i data-lucide="image" class="w-4 h-4 text-[#0B5D4B]"></i>
                                        <span>Change Card Photo</span>
                                    </button>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                                <div class="absolute bottom-3 left-4 right-4 text-white z-20">
                                    <span class="text-[9px] uppercase tracking-wider font-bold text-[#C7A76A] block">
                                        <span id="edit-experience-card-3-tag" class="wysiwyg-text text-[#C7A76A]" contenteditable="true">{{ $homepageContent['experience_card_3_tag'] ?? 'ACTIVITIES' }}</span>
                                    </span>
                                    <h3 class="serif text-lg font-bold text-white">
                                        <span id="edit-experience-card-3-title" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['experience_card_3_title'] ?? 'Nature & Sights' }}</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="p-5 space-y-2">
                                <p class="text-xs text-[#063F34]/75 leading-relaxed">
                                    <span id="edit-experience-card-3-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['experience_card_3_desc'] ?? 'Morning birdwatching trails, nearby Kuthumkal waterfalls, tea garden hikes, and peaceful evening campfires.' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="p-5 pt-0">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0B5D4B]">
                                <span id="edit-experience-card-3-btn" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['experience_card_3_btn'] ?? 'View Facilities & Trails' }}</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 5. PHOTO GALLERY ALBUMS: OPENABLE COLLECTIONS & LIGHTBOX PREVIEW
                 ========================================================================= -->
            <section class="pt-10 border-t border-gray-200/80">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4 px-1">
                    <div class="space-y-1">
                        <span id="edit-gallery-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                            {{ $homepageContent['gallery_eyebrow'] ?? 'VISUAL ALBUMS' }}
                        </span>
                        <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                            <span id="edit-gallery-heading" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['gallery_heading'] ?? 'Moments in the Mist' }}
                            </span>
                        </h2>
                        <p class="text-xs text-[#5A6B65]">
                            <span id="edit-gallery-desc" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['gallery_description'] ?? 'Click any album to explore high-resolution photograph collections.' }}
                            </span>
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#063F34] text-white text-xs font-bold shadow-xs">
                        <span id="edit-gallery-btn-text" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['gallery_btn_text'] ?? 'View All Albums' }}</span>
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-[#C7A76A]"></i>
                    </span>
                </div>

                <!-- Dynamic Notice -->
                <div class="p-3 mb-4 rounded-xl bg-emerald-50/70 border border-emerald-200 text-[#063F34] text-xs flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="image" class="w-4 h-4 text-[#0B5D4B] shrink-0"></i>
                        <span><strong>Dynamic Media Albums:</strong> The albums below are pulled live from your media database. Manage albums and upload high-res photos under <strong>Resort &gt; Gallery</strong>.</span>
                    </div>
                    <button type="button" onclick="navigateTo('gallery')" class="px-2.5 py-1 rounded-lg bg-[#063F34] text-white font-bold text-[11px] shrink-0 hover:bg-[#0B5D4B] transition cursor-pointer">
                        Manage Gallery
                    </button>
                </div>

                <!-- Live Gallery Albums Grid Preview -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse($galleryAlbumsList->take(3) as $album)
                        @php
                            $coverImg = $album->cover_image_url ?: ($album->images->first()->image_url ?? null);
                            $photoCount = $album->images->count();
                        @endphp
                        <div class="bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-xs flex flex-col justify-between">
                            <div class="relative h-44 w-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                @if($coverImg)
                                <img src="{{ $coverImg }}" alt="{{ $album->name }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-400 p-2 text-center">
                                    <i data-lucide="image" class="w-8 h-8 text-gray-300 mb-1"></i>
                                    <span class="text-[11px] font-medium text-gray-500">{{ $album->name }}</span>
                                </div>
                                @endif
                                <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded-full bg-black/60 text-white text-[10px] font-bold">
                                        {{ $album->branch ? $album->branch->name : 'All Retreats' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full bg-black/60 text-[#C7A76A] text-[10px] font-bold">
                                        {{ $photoCount }} Photos
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 space-y-1">
                                <h4 class="font-bold text-sm text-[#063F34]">{{ $album->name }}</h4>
                                <p class="text-xs text-[#5A6B65] line-clamp-2">{{ $album->description ?: 'Curated retreat photographic memories.' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 bg-white rounded-2xl border border-gray-200 text-xs text-gray-500">
                            No albums published yet. Add albums in Resort &gt; Gallery.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- =========================================================================
                 6. GUEST TESTIMONIALS: CLEAN QUOTES CAROUSEL
                 ========================================================================= -->
            <section class="pt-10 border-t border-gray-200/80">
                <div class="text-center max-w-2xl mx-auto mb-6 space-y-1">
                    <span id="edit-review-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                        {{ $homepageContent['review_eyebrow'] ?? 'GUEST MEMORIES' }}
                    </span>
                    <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                        <span id="edit-review-heading" class="wysiwyg-text" contenteditable="true">
                            {{ $homepageContent['review_heading'] ?? 'Stories from Our Visitors' }}
                        </span>
                    </h2>
                </div>

                <div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-3xl border border-gray-200 shadow-sm text-center space-y-4">
                    <div class="flex justify-center text-[#C7A76A] gap-1">
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                    </div>
                    <blockquote class="serif italic text-base sm:text-lg text-[#063F34] leading-relaxed">
                        &ldquo;<span id="edit-review-quote" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['review_quote'] ?? 'Everything felt easy — from the wooden cottage to dinner to knowing what was nearby. Truly peaceful and pure.' }}</span>&rdquo;
                    </blockquote>
                    <div>
                        <div class="font-bold text-xs text-[#063F34]">
                            <span id="edit-review-author" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['review_author'] ?? 'Ananya & Ravi' }}</span>
                        </div>
                        <div class="text-[11px] text-[#5A6B65]">
                            <span id="edit-review-subtitle" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['review_subtitle'] ?? 'Garden Residence · Munnar Retreat' }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 7. OUR DESTINATIONS: BRANCH SANCTUARIES
                 ========================================================================= -->
            <section class="pt-10 border-t border-gray-200/80">
                <div class="max-w-2xl mx-auto text-center mb-6 space-y-1">
                    <span id="edit-destinations-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                        {{ $homepageContent['destinations_eyebrow'] ?? 'OUR RETREATS' }}
                    </span>
                    <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                        <span id="edit-destinations-heading" class="wysiwyg-text" contenteditable="true">
                            {{ $homepageContent['destinations_heading'] ?? 'Our Sanctuaries Across Kerala' }}
                        </span>
                    </h2>
                    <p class="text-xs text-[#5A6B65]">
                        <span id="edit-destinations-desc" class="wysiwyg-text" contenteditable="true">
                            {{ $homepageContent['destinations_description'] ?? 'Explore our cottage locations in Idukki, Rajakkad, and Munnar hills.' }}
                        </span>
                    </p>
                </div>

                <!-- Dynamic Notice -->
                <div class="p-3 mb-4 rounded-xl bg-emerald-50/70 border border-emerald-200 text-[#063F34] text-xs flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-[#0B5D4B] shrink-0"></i>
                        <span><strong>Dynamic Branch Sanctuaries:</strong> Branches reflect database settings. Manage branch locations, contact phone numbers, and descriptions under <strong>Resort &gt; Branches</strong>.</span>
                    </div>
                    <button type="button" onclick="navigateTo('branches')" class="px-2.5 py-1 rounded-lg bg-[#063F34] text-white font-bold text-[11px] shrink-0 hover:bg-[#0B5D4B] transition cursor-pointer">
                        Manage Branches
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($branchesList as $b)
                        @php
                            $bImage = $b->cover_image_url ?: ($b->hero_image_url ?: null);
                        @endphp
                        <div class="rounded-2xl overflow-hidden bg-white border border-gray-200 shadow-xs flex flex-col justify-between">
                            <div class="relative h-44 w-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                @if($bImage)
                                <img src="{{ $bImage }}" alt="{{ $b->name }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-400 p-2 text-center">
                                    <i data-lucide="map-pin" class="w-8 h-8 text-gray-300 mb-1"></i>
                                    <span class="text-[11px] font-medium text-gray-500">{{ $b->name }}</span>
                                </div>
                                @endif
                                <div class="absolute top-2.5 right-2.5">
                                    <span class="px-2 py-0.5 rounded-full bg-black/60 text-white text-[10px] font-bold">
                                        {{ $b->city }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 space-y-1">
                                <h3 class="font-bold text-sm text-[#063F34]">{{ $b->name }}</h3>
                                <p class="text-xs text-[#5A6B65] line-clamp-2">{{ $b->tagline ?: ($b->city . ', Kerala. Private wooden cottages immersed in nature.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- =========================================================================
                 8. WARM CALL-TO-ACTION BANNER
                 ========================================================================= -->
            <section class="pt-10 border-t border-gray-200/80">
                <div class="rounded-3xl bg-[#063F34] text-white p-6 sm:p-10 shadow-xl relative overflow-hidden">
                    <div class="relative z-10 max-w-2xl space-y-3">
                        <span id="edit-cta-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#C7A76A]" contenteditable="true">
                            {{ $homepageContent['cta_eyebrow'] ?? 'RESERVE YOUR ESCAPE' }}
                        </span>
                        <h2 class="serif text-2xl sm:text-3xl font-bold tracking-tight leading-tight text-white">
                            <span id="edit-cta-heading" class="wysiwyg-text text-white" contenteditable="true">
                                {{ $homepageContent['cta_heading'] ?? 'Ready to unwind in the quiet hills of Kerala?' }}
                            </span>
                        </h2>
                        <p class="text-xs sm:text-sm text-white/80 leading-relaxed font-normal">
                            <span id="edit-cta-desc" class="wysiwyg-text text-white/90" contenteditable="true">
                                {{ $homepageContent['cta_description'] ?? 'Direct bookings receive best rate guarantee, freshly prepared garden breakfast, and personalized concierge desk service for a peaceful getaway.' }}
                            </span>
                        </p>
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-[#063F34] font-bold text-xs shadow-md">
                                <span id="edit-cta-btn-1-text" class="wysiwyg-text text-[#063F34]" contenteditable="true">{{ $homepageContent['cta_btn_1_text'] ?? 'Book Your Stay' }}</span>
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-[#C7A76A]"></i>
                            </span>
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/10 text-white font-semibold text-xs border border-white/20">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5 text-[#C7A76A]"></i>
                                <span id="edit-cta-btn-2-text" class="wysiwyg-text text-white" contenteditable="true">{{ $homepageContent['cta_btn_2_text'] ?? 'Chat with Concierge' }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 9. PRE-FOOTER CONCIERGE & DIRECT CONTACT
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80 bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-8 space-y-2">
                        <span class="text-[#0B5D4B] text-[10px] font-bold uppercase tracking-wider">ALWAYS HERE FOR YOU</span>
                        <h3 class="serif text-xl sm:text-2xl font-bold text-[#063F34]">
                            <span id="edit-prefooter-concierge-title" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['prefooter_concierge_title'] ?? ($homepageContent['footer_concierge_title'] ?? 'Direct Concierge & Instant Enquiries') }}
                            </span>
                        </h3>
                        <p class="text-xs text-[#5A6B65] leading-relaxed max-w-xl">
                            <span id="edit-prefooter-concierge-desc" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['prefooter_concierge_desc'] ?? ($homepageContent['footer_concierge_desc'] ?? 'Planning your retreat or require special arrangements? Connect directly with our front desk team 24/7.') }}
                            </span>
                        </p>
                        <div class="flex flex-wrap items-center gap-4 text-xs pt-1">
                            <div class="flex items-center gap-2 text-[#0B5D4B] font-bold">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                                <span id="edit-prefooter-phone" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['prefooter_phone'] ?? ($homepageContent['footer_phone'] ?? '+91 94470 00000') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-[#063F34]/70">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                                <span id="edit-prefooter-email" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['prefooter_email'] ?? ($homepageContent['footer_email'] ?? 'concierge@countrysidecottages.com') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-4 flex items-center justify-start lg:justify-end gap-2">
                        <span class="px-4 py-2.5 rounded-full bg-[#C7A76A] text-[#063F34] font-bold text-xs flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>WhatsApp Concierge</span>
                        </span>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 10. LUXURY EDITORIAL WEBSITE FOOTER (LIVE WYSIWYG CANVAS)
                 ========================================================================= -->
            <section class="mt-8 rounded-3xl bg-[#063F34] text-[#FAF8F5] p-6 sm:p-10 shadow-lg border border-[#063F34] overflow-hidden relative">
                <!-- Section Header Banner -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-8 border-b border-white/10">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#C7A76A] animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#C7A76A]">Official Website Footer (WYSIWYG Live Editor)</span>
                    </div>
                    <span class="text-[10px] text-white/50">Click any text to edit inline &bull; Changes automatically update public website footer</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-10">
                    <!-- Column 1: Brand Identity & Bio -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2.5">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#FAF8F5] text-sm font-bold text-[#063F34]">C</span>
                            <div>
                                <span class="serif text-xl font-bold block text-[#FAF8F5] tracking-tight">
                                    <span id="edit-footer-brand-title" class="wysiwyg-text text-white hover:text-[#063F34]" contenteditable="true">
                                        {{ $homepageContent['footer_brand_title'] ?? 'Country Side Cottages' }}
                                    </span>
                                </span>
                                <span class="text-[9px] uppercase tracking-[.25em] text-[#C7A76A] font-bold block">
                                    <span id="edit-footer-brand-tagline" class="wysiwyg-text text-[#C7A76A] hover:text-[#063F34]" contenteditable="true">
                                        {{ $homepageContent['footer_brand_tagline'] ?? 'Luxury Cottages of Kerala' }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <p class="text-xs text-[#FAF8F5]/70 leading-relaxed max-w-sm">
                            <span id="edit-footer-brand-desc" class="wysiwyg-text text-[#FAF8F5]/90 hover:text-[#063F34]" contenteditable="true">
                                {{ $homepageContent['footer_brand_desc'] ?? 'Immersive, slow-living cottages nestled in the spice hills, misty valleys, and tranquil backwaters of Kerala.' }}
                            </span>
                        </p>
                        <div class="flex items-center gap-2 text-xs text-[#C7A76A] font-semibold">
                            <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i>
                            <span id="edit-footer-badge" class="wysiwyg-text text-[#C7A76A] hover:text-[#063F34]" contenteditable="true">
                                {{ $homepageContent['footer_badge'] ?? 'Eco-Conscious Botanical Cottages' }}
                            </span>
                        </div>
                    </div>

                    <!-- Column 2: Destinations -->
                    <div>
                        <h4 class="eyebrow text-[#C7A76A] mb-4">
                            <span id="edit-footer-dest-title" class="wysiwyg-text text-[#C7A76A] hover:text-[#063F34]" contenteditable="true">
                                {{ $homepageContent['footer_dest_title'] ?? 'Destinations' }}
                            </span>
                        </h4>
                        <ul class="space-y-2 text-xs text-[#FAF8F5]/75">
                            @forelse($branchesList->take(4) as $b)
                                <li class="flex items-center gap-1.5 text-white/80">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-[#C7A76A]"></i>
                                    <span>{{ $b->display_name ?: ($b->name . ' (' . $b->city . ')') }}</span>
                                </li>
                            @empty
                                <li>Munnar High Forest</li>
                                <li>Wayanad Valley Reserve</li>
                                <li>Kumarakom Waterside</li>
                            @endforelse
                            <li class="text-white/60">Ayurvedic Wellness Pavilion</li>
                        </ul>
                    </div>

                    <!-- Column 3: Curated Experiences -->
                    <div>
                        <h4 class="eyebrow text-[#C7A76A] mb-4">
                            <span id="edit-footer-exp-title" class="wysiwyg-text text-[#C7A76A] hover:text-[#063F34]" contenteditable="true">
                                {{ $homepageContent['footer_exp_title'] ?? 'Experiences' }}
                            </span>
                        </h4>
                        <ul class="space-y-2 text-xs text-[#FAF8F5]/75">
                            <li>
                                <span id="edit-footer-exp-link-1" class="wysiwyg-text text-white hover:text-[#063F34]" contenteditable="true">
                                    {{ $homepageContent['footer_exp_link_1'] ?? 'Plantation Kitchen & Dining' }}
                                </span>
                            </li>
                            <li>
                                <span id="edit-footer-exp-link-2" class="wysiwyg-text text-white hover:text-[#063F34]" contenteditable="true">
                                    {{ $homepageContent['footer_exp_link_2'] ?? 'Country Side Spices Farm Shop' }}
                                </span>
                            </li>
                            <li>
                                <span id="edit-footer-exp-link-3" class="wysiwyg-text text-white hover:text-[#063F34]" contenteditable="true">
                                    {{ $homepageContent['footer_exp_link_3'] ?? 'Cottages Visual Gallery' }}
                                </span>
                            </li>
                            <li>
                                <span id="edit-footer-exp-link-4" class="wysiwyg-text text-white hover:text-[#063F34]" contenteditable="true">
                                    {{ $homepageContent['footer_exp_link_4'] ?? 'Guided Nature Discoveries' }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 4: Direct Concierge & Contact -->
                    <div>
                        <h4 class="eyebrow text-[#C7A76A] mb-4">
                            <span id="edit-footer-concierge-title" class="wysiwyg-text text-[#C7A76A] hover:text-[#063F34]" contenteditable="true">
                                {{ $homepageContent['footer_concierge_title'] ?? 'Direct Concierge' }}
                            </span>
                        </h4>
                        <p class="text-xs text-[#FAF8F5]/70 mb-3">
                            <span id="edit-footer-concierge-desc" class="wysiwyg-text text-white/80 hover:text-[#063F34]" contenteditable="true">
                                {{ $homepageContent['footer_concierge_desc'] ?? 'Front desk assistance 24/7 for bespoke retreat arrangements.' }}
                            </span>
                        </p>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2 text-[#C7A76A]">
                                <i data-lucide="phone" class="w-3.5 h-3.5 shrink-0"></i>
                                <span id="edit-footer-phone" class="wysiwyg-text text-[#C7A76A] hover:text-[#063F34]" contenteditable="true">
                                    {{ $homepageContent['footer_phone'] ?? '+91 484 290 0000' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 text-[#FAF8F5]/75">
                                <i data-lucide="mail" class="w-3.5 h-3.5 shrink-0"></i>
                                <span id="edit-footer-email" class="wysiwyg-text text-white hover:text-[#063F34]" contenteditable="true">
                                    {{ $homepageContent['footer_email'] ?? 'concierge@countrysidecottages.com' }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center gap-2 rounded-xl bg-[#C7A76A] px-3.5 py-2 text-[10px] font-bold text-[#063F34] shadow-xs">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                <span id="edit-footer-chat-btn" class="wysiwyg-text text-[#063F34]" contenteditable="true">
                                    {{ $homepageContent['footer_chat_btn'] ?? 'Chat with Concierge' }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Copyright & Legal Links -->
                <div class="border-t border-white/10 mt-10 pt-6 flex flex-col md:flex-row items-center justify-between text-[11px] text-[#FAF8F5]/60 gap-4">
                    <p>
                        &copy; {{ date('Y') }} 
                        <span id="edit-footer-copyright" class="wysiwyg-text text-white/80 hover:text-[#063F34]" contenteditable="true">
                            {{ $homepageContent['footer_copyright'] ?? 'Country Side Cottages Hospitality Ltd. All rights reserved.' }}
                        </span>
                    </p>
                    <div class="flex items-center gap-6">
                        <span id="edit-footer-support-link" class="wysiwyg-text text-white/80 hover:text-[#063F34]" contenteditable="true">
                            {{ $homepageContent['footer_support_link'] ?? 'Help & Support' }}
                        </span>
                        <span class="text-[#C7A76A]">
                            <span id="edit-footer-policy-note" class="wysiwyg-text text-[#C7A76A] hover:text-[#063F34]" contenteditable="true">
                                {{ $homepageContent['footer_policy_note'] ?? '🌿 Strictly No Swimming Pool Policy' }}
                            </span>
                        </span>
                    </div>
                </div>
            </section>

            <!-- Hidden Legacy Fallbacks for Backward Compatibility -->
            <div class="hidden">
                <span id="edit-stay-eyebrow">{{ $homepageContent['stay_eyebrow'] ?? ($homepageContent['cottages_eyebrow'] ?? '') }}</span>
                <span id="edit-stay-heading-1">{{ $homepageContent['stay_heading_1'] ?? ($homepageContent['cottages_heading'] ?? '') }}</span>
                <span id="edit-dining-eyebrow">{{ $homepageContent['dining_eyebrow'] ?? '' }}</span>
                <span id="edit-dining-heading-1">{{ $homepageContent['dining_heading_1'] ?? '' }}</span>
                <span id="edit-dining-desc">{{ $homepageContent['dining_description'] ?? '' }}</span>
                <img id="edit-dining-img" src="{{ $homepageContent['dining_image'] ?? '' }}" alt="dining fallback">
                <span id="edit-spices-eyebrow">{{ $homepageContent['spices_eyebrow'] ?? '' }}</span>
                <span id="edit-spices-heading-1">{{ $homepageContent['spices_heading_1'] ?? '' }}</span>
            </div>

        </div>
    </div>
</div>

<style>
    /* PPT-Style WYSIWYG Hover and Edit States */
    .wysiwyg-text {
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 6px;
        padding: 2px 6px;
        margin: -2px -6px;
        cursor: text;
        position: relative;
        display: inline-block;
        min-width: 24px;
    }
    .wysiwyg-text:hover {
        outline: 2px dashed #C7A76A;
        background-color: rgba(199, 167, 106, 0.18);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .wysiwyg-text:focus {
        outline: 2px solid #C7A76A !important;
        background-color: #FFFFFF !important;
        color: #063F34 !important;
        box-shadow: 0 4px 14px rgba(6, 63, 52, 0.25) !important;
    }
</style>
