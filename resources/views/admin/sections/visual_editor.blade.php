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
                'image' => $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1400&q=85'),
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
                 1. HERO SECTION: CLASSIC HOTEL CARD CAROUSEL & DYNAMIC DESTINATIONS
                 ========================================================================= -->
            <section class="relative w-full pt-2 pb-6">
                
                <!-- Section Header Toolbar -->
                <div class="flex items-center justify-between mb-3 px-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#063F34] flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-[#0B5D4B]"></i>
                        <span>Section 1: Hero Card Carousel Canvas</span>
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openHeroSlideModal()" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-[#063F34] hover:bg-[#0B5D4B] text-white text-[11px] font-bold shadow-xs transition cursor-pointer">
                            <i data-lucide="plus" class="w-3.5 h-3.5 text-[#C7A76A]"></i> Add Slide
                        </button>
                    </div>
                </div>

                <!-- Framed Hero Card Carousel Canvas Replica -->
                <div id="admin-hero-carousel-preview" class="relative w-full min-h-[420px] xs:min-h-[460px] sm:min-h-[500px] rounded-3xl lg:rounded-[36px] overflow-hidden shadow-2xl bg-[#063F34] group select-none flex flex-col justify-between">
                    
                    <!-- Slides Container -->
                    <div id="admin-slides-container" class="relative w-full h-full flex-1">
                        @foreach($heroSlidesList as $sIdx => $s)
                            <div class="admin-preview-slide absolute inset-0 transition-all duration-500 ease-out {{ $sIdx === 0 ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 pointer-events-none z-0' }}" data-admin-slide-idx="{{ $sIdx }}">
                                <img src="{{ $s['image'] }}" class="w-full h-full object-cover brightness-[0.78]" alt="{{ $s['title'] }}" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-black/30"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/20 to-black/50"></div>

                                <!-- Top Floating Destination Tag -->
                                <div class="absolute top-4 left-4 sm:top-6 sm:left-7 z-20 flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/50 backdrop-blur-md text-white text-[10px] sm:text-xs font-semibold tracking-wider uppercase border border-white/20 shadow-xs">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-[#C7A76A]"></i>
                                        <span>{{ $s['tag'] ?? ($s['subtitle'] ?? 'Krishna Cottages') }}</span>
                                    </span>
                                    @if(!empty($s['badge']))
                                        <span class="hidden xs:inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#C7A76A]/25 backdrop-blur-md text-[#C7A76A] text-[10px] font-bold border border-[#C7A76A]/30">
                                            {{ $s['badge'] }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Top Right Counter & Edit Button -->
                                <div class="absolute top-4 right-4 sm:top-6 sm:right-7 z-20 flex items-center gap-2">
                                    <div class="text-white/80 bg-black/50 backdrop-blur-md px-2.5 sm:px-3 py-1 rounded-full text-[11px] sm:text-xs font-mono border border-white/15">
                                        <span class="text-white font-bold">0{{ $sIdx + 1 }}</span> / 0{{ count($heroSlidesList) }}
                                    </div>
                                    <button type="button" onclick="openHeroSlideModal({{ $sIdx }})" class="p-2 rounded-full bg-white text-[#063F34] hover:bg-[#C7A76A] hover:text-white shadow-md transition cursor-pointer" title="Edit this slide with cropper">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>

                                <!-- Centered Hero Typography -->
                                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-8 max-w-4xl mx-auto text-white">
                                    <!-- Eyebrow -->
                                    <p class="text-[#C7A76A] text-[10px] sm:text-xs tracking-[.25em] mb-2 font-bold uppercase">
                                        <span id="edit-hero-eyebrow" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['hero_eyebrow'] ?? 'PEACEFUL NATURE SANCTUARY' }}</span>
                                    </p>

                                    <!-- Headline -->
                                    <h1 class="serif text-2xl xs:text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight leading-[1.1] text-white drop-shadow-md max-w-3xl">
                                        <span id="edit-hero-heading-1" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['hero_heading_1'] ?? $s['title'] }}</span>
                                    </h1>

                                    <!-- Description -->
                                    <p class="mt-2.5 sm:mt-4 text-xs sm:text-sm lg:text-base text-white/90 max-w-2xl leading-relaxed drop-shadow-xs font-normal line-clamp-2 sm:line-clamp-3">
                                        <span id="edit-hero-desc" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['hero_description'] ?? $s['description'] }}</span>
                                    </p>

                                    <!-- Action Buttons -->
                                    <div class="mt-5 sm:mt-7 flex flex-wrap items-center justify-center gap-2.5 sm:gap-3.5">
                                        <span class="inline-flex items-center gap-2 px-5 sm:px-6 py-2.5 rounded-full bg-[#063F34] text-white font-bold text-xs sm:text-sm shadow-xl border border-emerald-400/30">
                                            <span>Explore Cottages</span>
                                            <i data-lucide="arrow-up-right" class="w-4 h-4 text-[#C7A76A]"></i>
                                        </span>
                                        <span class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-full bg-white/20 backdrop-blur-md text-white font-semibold text-xs sm:text-sm border border-white/30">
                                            <i data-lucide="message-circle" class="w-4 h-4 text-[#C7A76A]"></i>
                                            <span>Chat with Concierge</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Navigation Arrows -->
                    <button type="button" onclick="cycleAdminPreviewSlide(-1)" class="absolute left-2 sm:left-5 top-1/2 -translate-y-1/2 z-30 grid h-9 w-9 sm:h-12 sm:w-12 place-items-center rounded-full bg-black/30 hover:bg-white text-white hover:text-[#063F34] backdrop-blur-md border border-white/30 transition-all shadow-md cursor-pointer">
                        <i data-lucide="chevron-left" class="w-4 h-4 sm:w-6 sm:h-6"></i>
                    </button>
                    <button type="button" onclick="cycleAdminPreviewSlide(1)" class="absolute right-2 sm:right-5 top-1/2 -translate-y-1/2 z-30 grid h-9 w-9 sm:h-12 sm:w-12 place-items-center rounded-full bg-black/30 hover:bg-white text-white hover:text-[#063F34] backdrop-blur-md border border-white/30 transition-all shadow-md cursor-pointer">
                        <i data-lucide="chevron-right" class="w-4 h-4 sm:w-6 sm:h-6"></i>
                    </button>

                    <!-- Indicator Dots -->
                    <div class="absolute bottom-4 sm:bottom-5 left-1/2 -translate-x-1/2 z-30 flex items-center gap-2 bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/15" id="admin-preview-dots">
                        @foreach($heroSlidesList as $dIdx => $s)
                            <button type="button" onclick="goToAdminPreviewSlide({{ $dIdx }})" class="admin-dot h-2 rounded-full transition-all duration-300 cursor-pointer {{ $dIdx === 0 ? 'w-6 bg-[#C7A76A]' : 'w-2 bg-white/40' }}"></button>
                        @endforeach
                    </div>

                </div>

                <!-- Hotel Booking Engine Search Bar Replica -->
                <div class="relative mt-4 sm:-mt-8 lg:-mt-10 z-40 max-w-5xl mx-auto">
                    <div class="w-full bg-white text-[#063F34] rounded-2xl lg:rounded-full p-3 lg:p-4 shadow-[0_16px_40px_rgba(6,63,52,0.12)] border border-[#063F34]/15">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                            <!-- Field 1: Destination -->
                            <div class="lg:col-span-4 px-3.5 py-2 bg-[#FAF7F0]/60 rounded-xl lg:rounded-l-full border border-[#063F34]/10">
                                <span class="block text-[9px] uppercase tracking-wider font-bold text-[#063F34]/50 flex items-center gap-1.5 mb-0.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#0B5D4B]"></i> Destination
                                </span>
                                <span class="text-xs sm:text-sm font-bold text-[#063F34]">All Branches (Cottages Central)</span>
                            </div>

                            <!-- Field 2: Check-in / Check-out -->
                            <div class="lg:col-span-4 grid grid-cols-2 gap-2">
                                <div class="px-3 py-2 bg-[#FAF7F0]/60 rounded-xl border border-[#063F34]/10">
                                    <span class="block text-[9px] uppercase tracking-wider font-bold text-[#063F34]/50 mb-0.5">Check-In</span>
                                    <span class="text-xs font-bold text-[#063F34]">Tomorrow</span>
                                </div>
                                <div class="px-3 py-2 bg-[#FAF7F0]/60 rounded-xl border border-[#063F34]/10">
                                    <span class="block text-[9px] uppercase tracking-wider font-bold text-[#063F34]/50 mb-0.5">Check-Out</span>
                                    <span class="text-xs font-bold text-[#063F34]">+2 Days</span>
                                </div>
                            </div>

                            <!-- Field 3: Guests & Button -->
                            <div class="lg:col-span-4 grid grid-cols-2 gap-2">
                                <div class="px-3 py-2 bg-[#FAF7F0]/60 rounded-xl border border-[#063F34]/10">
                                    <span class="block text-[9px] uppercase tracking-wider font-bold text-[#063F34]/50 mb-0.5">Guests</span>
                                    <span class="text-xs font-bold text-[#063F34]">2 Guests</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-full h-full min-h-[44px] py-2.5 px-3 rounded-xl lg:rounded-r-full bg-[#C7A76A] text-[#063F34] font-bold text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md">
                                        <i data-lucide="search" class="w-4 h-4 shrink-0"></i>
                                        <span>Check Stays</span>
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
                 2. WELCOME & PHILOSOPHY: CLEAN STORY + 4 VALUE BADGES
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80">
                <div class="max-w-3xl mx-auto text-center space-y-3">
                    <span id="edit-welcome-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                        {{ $homepageContent['welcome_eyebrow'] ?? 'WELCOME TO KRISHNA COTTAGES' }}
                    </span>
                    <h2 class="serif text-3xl sm:text-4xl font-bold text-[#063F34] tracking-tight">
                        <span id="edit-welcome-heading" class="wysiwyg-text" contenteditable="true">
                            {{ $homepageContent['welcome_heading'] ?? 'A Sanctuary of Slow Living & Serenity' }}
                        </span>
                    </h2>
                    <div class="w-16 h-0.5 bg-[#C7A76A] mx-auto my-3"></div>
                    <p class="text-sm text-[#063F34]/80 leading-relaxed font-normal">
                        <span id="edit-welcome-desc" class="wysiwyg-text" contenteditable="true">
                            {{ $homepageContent['welcome_description'] ?? 'Tucked into the misty slopes of Rajakkad, Idukki, and nearby Munnar, Krishna Cottages provides handcrafted private wooden residences surrounded by aromatic tea plantations, cardamoms, and cool mountain air.' }}
                        </span>
                    </p>
                </div>

                <!-- 4 Value Highlight Cards -->
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-5 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2 text-center sm:text-left">
                        <div class="w-10 h-10 rounded-xl bg-[#063F34]/5 flex items-center justify-center text-[#0B5D4B] mx-auto sm:mx-0">
                            <i data-lucide="home" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-serif font-bold text-sm text-[#063F34]">Wooden Cottages</h4>
                        <p class="text-xs text-[#5A6B65] leading-relaxed">Handcrafted teak verandas, private sit-outs, and panoramic valley views.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2 text-center sm:text-left">
                        <div class="w-10 h-10 rounded-xl bg-[#063F34]/5 flex items-center justify-center text-[#0B5D4B] mx-auto sm:mx-0">
                            <i data-lucide="utensils" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-serif font-bold text-sm text-[#063F34]">Clay-Pot Dining</h4>
                        <p class="text-xs text-[#5A6B65] leading-relaxed">Authentic Kerala culinary recipes prepared in earthen pots with organic garden harvests.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2 text-center sm:text-left">
                        <div class="w-10 h-10 rounded-xl bg-[#063F34]/5 flex items-center justify-center text-[#0B5D4B] mx-auto sm:mx-0">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-serif font-bold text-sm text-[#063F34]">Estate Spices</h4>
                        <p class="text-xs text-[#5A6B65] leading-relaxed">Cardamom, black pepper, and clove trails. Hand-harvested farm produce.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2 text-center sm:text-left">
                        <div class="w-10 h-10 rounded-xl bg-[#063F34]/5 flex items-center justify-center text-[#0B5D4B] mx-auto sm:mx-0">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-serif font-bold text-sm text-[#063F34]">Pure Hospitality</h4>
                        <p class="text-xs text-[#5A6B65] leading-relaxed">Genuine Kerala homestay warmth with refined hotel comfort and 24/7 care.</p>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 3. SIGNATURE COTTAGES & ACCOMMODATIONS CAROUSEL
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-3">
                    <div>
                        <span id="edit-stay-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                            {{ $homepageContent['stay_eyebrow'] ?? 'CURATED LIVING SPACES' }}
                        </span>
                        <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                            <span id="edit-stay-heading-1" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['stay_heading_1'] ?? 'Signature Cottages & Villas' }}
                            </span>
                        </h2>
                    </div>
                    <span class="text-xs text-brand-muted">Dynamic Cottages from Database &middot; Live Pricing</span>
                </div>

                <!-- Cottages Grid Preview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($featuredRoomsList->take(3) as $r)
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs hover:shadow-md transition">
                            <div class="relative h-44 overflow-hidden">
                                <img src="{{ $r->cover_image_url ?: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=85' }}" class="w-full h-full object-cover" alt="{{ $r->name }}">
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
                 4. RESORT EXPERIENCES & SIGNATURE AMENITIES
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80">
                <div class="text-center max-w-2xl mx-auto mb-6">
                    <span id="edit-exp-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                        {{ $homepageContent['experience_eyebrow'] ?? 'UNRUSHED NATURE DISCOVERIES' }}
                    </span>
                    <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                        <span id="edit-exp-heading-1" class="wysiwyg-text" contenteditable="true">
                            {{ $homepageContent['experience_heading_1'] ?? 'Estate Experiences & Daily Adventures' }}
                        </span>
                    </h2>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2">
                        <i data-lucide="compass" class="w-6 h-6 text-[#0B5D4B]"></i>
                        <h4 class="font-bold text-xs text-[#063F34]">Plantation Walks</h4>
                        <p class="text-[11px] text-[#5A6B65]">Guided morning walks through cardamom and pepper groves.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2">
                        <i data-lucide="feather" class="w-6 h-6 text-[#0B5D4B]"></i>
                        <h4 class="font-bold text-xs text-[#063F34]">Birdwatching Trails</h4>
                        <p class="text-[11px] text-[#5A6B65]">Spot Malabar grey hornbills, emerald doves, and native flora.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2">
                        <i data-lucide="flame" class="w-6 h-6 text-[#0B5D4B]"></i>
                        <h4 class="font-bold text-xs text-[#063F34]">Campfire &amp; BBQ</h4>
                        <p class="text-[11px] text-[#5A6B65]">Evening warmth under starry skies with spiced grilled delights.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2">
                        <i data-lucide="flower2" class="w-6 h-6 text-[#0B5D4B]"></i>
                        <h4 class="font-bold text-xs text-[#063F34]">Ayurvedic Wellness</h4>
                        <p class="text-[11px] text-[#5A6B65]">Holistic therapies, herbal warm oils, and traditional rejuvenation.</p>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 5. PLANTATION DINING & CULINARY HERITAGE
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-4">
                        <span id="edit-dining-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                            {{ $homepageContent['dining_eyebrow'] ?? 'PLANTATION KITCHEN & DINING' }}
                        </span>
                        <h2 class="serif text-3xl font-bold text-[#063F34]">
                            <span id="edit-dining-heading-1" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['dining_heading_1'] ?? 'Earthen Clay-Pot Kerala Heritage Flavours' }}
                            </span>
                        </h2>
                        <p class="text-xs sm:text-sm text-[#063F34]/80 leading-relaxed">
                            <span id="edit-dining-desc" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['dining_description'] ?? 'Every meal at Krishna Cottages is prepared with freshly plucked plantation herbs, slow-cooked in traditional clay pots over open firewood.' }}
                            </span>
                        </p>
                        <div class="flex items-center gap-3 pt-2">
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#063F34] text-white font-bold text-xs shadow-md">
                                <span>Browse Estate Menu</span>
                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-[#C7A76A]"></i>
                            </span>
                        </div>
                    </div>
                    <div class="lg:col-span-6">
                        <div class="relative rounded-3xl overflow-hidden shadow-lg border border-gray-200 h-64 sm:h-72">
                            <img id="edit-dining-img" src="{{ $homepageContent['dining_image'] ?? 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=800&q=85' }}" alt="Dining" class="w-full h-full object-cover">
                            <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-xs text-white text-xs px-3 py-1 rounded-full font-bold">
                                Clay-Pot Prepared &middot; Fresh Estate Produce
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 6. PURE ESTATE SPICES SHOWCASE
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-3">
                    <div>
                        <span id="edit-spices-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                            {{ $homepageContent['spices_eyebrow'] ?? 'FARM-FRESH DIRECT FROM ESTATE' }}
                        </span>
                        <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                            <span id="edit-spices-heading-1" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['spices_heading_1'] ?? 'Krishna Pure Spices & Organics' }}
                            </span>
                        </h2>
                    </div>
                    <span class="text-xs text-brand-muted">Fresh Pack-on-Order &middot; Worldwide Delivery</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2">
                        <div class="h-32 rounded-xl overflow-hidden bg-black/5">
                            <img src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=600&q=80" alt="Cardamom" class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-bold text-xs text-[#063F34]">Green Cardamom (8mm Bold)</h4>
                        <p class="text-[11px] text-[#5A6B65]">Sun-dried organic green pods directly harvested from Idukki slopes.</p>
                        <div class="pt-1 flex items-center justify-between text-xs font-bold text-[#0B5D4B]">
                            <span>₹480 / 250g</span>
                            <span>Order &rarr;</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2">
                        <div class="h-32 rounded-xl overflow-hidden bg-black/5">
                            <img src="https://images.unsplash.com/photo-1509358271058-acd22cc93898?auto=format&fit=crop&w=600&q=80" alt="Pepper" class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-bold text-xs text-[#063F34]">Malabar Black Pepper</h4>
                        <p class="text-[11px] text-[#5A6B65]">High-piperine whole black peppercorns from shade-grown vines.</p>
                        <div class="pt-1 flex items-center justify-between text-xs font-bold text-[#0B5D4B]">
                            <span>₹290 / 250g</span>
                            <span>Order &rarr;</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs space-y-2">
                        <div class="h-32 rounded-xl overflow-hidden bg-black/5">
                            <img src="https://images.unsplash.com/photo-1599940824399-b87987ceb72a?auto=format&fit=crop&w=600&q=80" alt="Clove" class="w-full h-full object-cover">
                        </div>
                        <h4 class="font-bold text-xs text-[#063F34]">Aromatic Kerala Cloves</h4>
                        <p class="text-[11px] text-[#5A6B65]">Intensely fragrant handpicked dried flower buds with full oils.</p>
                        <div class="pt-1 flex items-center justify-between text-xs font-bold text-[#0B5D4B]">
                            <span>₹340 / 200g</span>
                            <span>Order &rarr;</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================================
                 7. VERIFIED GUEST TESTIMONIALS CAROUSEL
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80">
                <div class="text-center max-w-2xl mx-auto mb-6">
                    <span id="edit-review-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                        {{ $homepageContent['review_eyebrow'] ?? 'GUEST STORIES' }}
                    </span>
                    <h2 class="serif text-2xl sm:text-3xl font-bold text-[#063F34] mt-1">
                        What Our Guests Say
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
                 8. VISUAL GALLERY (MOMENTS AT KRISHNA)
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80">
                <div class="flex items-end justify-between mb-4">
                    <div>
                        <span id="edit-gallery-eyebrow" class="wysiwyg-text text-xs font-bold uppercase tracking-wider text-[#0B5D4B]" contenteditable="true">
                            {{ $homepageContent['gallery_eyebrow'] ?? 'MOMENTS IN NATURE' }}
                        </span>
                        <h2 class="serif text-2xl font-bold text-[#063F34] mt-1">
                            <span id="edit-gallery-heading" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['gallery_heading'] ?? 'Visual Gallery of the Cottages' }}
                            </span>
                        </h2>
                    </div>
                    <span class="text-xs text-brand-muted">6 Curated Retreat Photographs</span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    @php
                        $galleryDefaults = [
                            ['url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80', 'cap' => 'Wooden Cottages'],
                            ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=600&q=80', 'cap' => 'Tea Trails'],
                            ['url' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=600&q=80', 'cap' => 'Clay-Pot Table'],
                            ['url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=600&q=80', 'cap' => 'Verandah Living'],
                            ['url' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=600&q=80', 'cap' => 'Harvest Spices'],
                            ['url' => 'https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?auto=format&fit=crop&w=600&q=80', 'cap' => 'Sunset Hills'],
                        ];
                    @endphp
                    @foreach($galleryDefaults as $gIdx => $g)
                        <div class="relative h-28 rounded-xl overflow-hidden group shadow-2xs border border-gray-200">
                            <img src="{{ $g['url'] }}" alt="{{ $g['cap'] }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-2 text-center text-white text-[10px] font-bold">
                                {{ $g['cap'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- =========================================================================
                 9. PRE-FOOTER CONCIERGE & DIRECT CONTACT
                 ========================================================================= -->
            <section class="pt-8 border-t border-gray-200/80 bg-[#063F34] text-white rounded-3xl p-6 sm:p-10 shadow-xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7 space-y-3">
                        <span class="text-[#C7A76A] text-xs font-bold uppercase tracking-wider">ALWAYS HERE FOR YOU</span>
                        <h2 class="serif text-2xl sm:text-3xl font-bold text-white">
                            <span id="edit-footer-concierge-title" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['footer_concierge_title'] ?? 'Direct Concierge & Instant Enquiries' }}
                            </span>
                        </h2>
                        <p class="text-xs text-white/80 leading-relaxed max-w-xl">
                            <span id="edit-footer-concierge-desc" class="wysiwyg-text" contenteditable="true">
                                {{ $homepageContent['footer_concierge_desc'] ?? 'Planning your retreat or require special arrangements? Connect directly with our front desk team 24/7.' }}
                            </span>
                        </p>
                        <div class="flex flex-wrap items-center gap-4 text-xs pt-2">
                            <div class="flex items-center gap-2 text-[#C7A76A]">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                                <span id="edit-footer-phone" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_phone'] ?? '+91 94470 00000' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-white/80">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                                <span id="edit-footer-email" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_email'] ?? 'concierge@krishnacottages.com' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 flex flex-col sm:flex-row items-center justify-end gap-3">
                        <span class="px-5 py-3 rounded-full bg-[#C7A76A] text-[#063F34] font-bold text-xs flex items-center gap-2 shadow-lg">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span>WhatsApp Concierge</span>
                        </span>
                        <a href="/stay" class="px-5 py-3 rounded-full bg-white/20 hover:bg-white/30 text-white font-semibold text-xs border border-white/20 transition">
                            <span>Check Availability</span>
                        </a>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<style>
    /* WYSIWYG Hover and Edit States */
    .wysiwyg-text {
        transition: all 0.15s ease-in-out;
        border-radius: 4px;
        padding: 1px 3px;
        margin: -1px -3px;
    }
    .wysiwyg-text:hover {
        outline: 2px dashed #0B5D4B;
        background-color: rgba(11, 93, 75, 0.08);
        cursor: text;
    }
    .wysiwyg-text:focus {
        outline: 2px solid #C7A76A !important;
        background-color: #FFFFFF !important;
        color: #063F34 !important;
        box-shadow: 0 0 0 3px rgba(199, 167, 106, 0.3);
    }
</style>
