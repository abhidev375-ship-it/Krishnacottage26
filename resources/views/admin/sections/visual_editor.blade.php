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

    if (!isset($branchesWeather)) {
        try {
            $weatherService = app(\App\Services\WeatherService::class);
            $branchesWeather = $weatherService->getAllBranchesWeather($branches);
        } catch (\Throwable $e) {
            $branchesWeather = [];
        }
    }
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
            <button type="button" onclick="resetHomepageVisualContent()" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 border border-gray-200 transition flex items-center gap-1" title="Revert to original brand text and images">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
            </button>
            <a href="/" target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-primary bg-brand-canvas hover:bg-gray-200 border border-gray-200 transition flex items-center gap-1">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Preview Site
            </a>
            <button type="button" onclick="saveHomepageVisualContent()" id="btn-save-homepage" class="px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-brand-primary hover:bg-brand-deep shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    <!-- Live Client Homepage Canvas Wrapper -->
    <div class="rounded-3xl border border-gray-300 shadow-xl overflow-hidden bg-[#F7F5EF] text-[#14231E]">
        <!-- Canvas Top Info Banner -->
        <div class="bg-brand-deep text-white px-5 py-2.5 flex items-center justify-between text-xs border-b border-brand-primary/40">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-accent animate-pulse"></span>
                <span class="font-bold tracking-wide">Interactive Homepage Canvas</span>
                <span class="text-white/60 text-[11px]">— Click any text to type directly · Hover photos to swap</span>
            </div>
            <span class="text-brand-accent text-[11px] font-semibold uppercase tracking-wider">PPT-Style Live Mode</span>
        </div>

        <!-- CANVAS MAIN CONTENT (Replicating welcome.blade.php with inline edit controls) -->
        <div class="p-6 md:p-10 space-y-12 select-text" id="homepage-canvas">

            <!-- 1. HERO EDITORIAL STAGE (3 Columns: Typography & Story + Centered Climate + Hero Carousel) -->
            <section class="relative">
                <div class="grid gap-6 lg:gap-6 lg:grid-cols-12 items-start">
                    <!-- Left Column (lg:col-span-5): Editorial Story & Identity -->
                    <div class="{{ !empty($branchesWeather) ? 'lg:col-span-5' : 'lg:col-span-7' }} space-y-4">
                        <!-- Brand Title (Aligned Flush with Top of Carousel) -->
                        <div class="leading-none pt-0.5">
                            <h2 class="font-serif text-3xl sm:text-4xl lg:text-[38px] font-bold text-[#063F34] tracking-tight leading-none">
                                Krishna Cottage
                            </h2>
                        </div>

                        <div class="flex items-center gap-2 text-forest/70 pt-0.5">
                            <span class="h-2 w-2 rounded-full bg-[#C7A76A]"></span>
                            <span id="edit-hero-eyebrow" class="wysiwyg-text text-[10.5px] font-bold uppercase tracking-widest text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['hero_eyebrow'] }}</span>
                        </div>
                        
                        <h1 class="text-2xl sm:text-3xl lg:text-[2.6rem] font-serif font-normal leading-[1.08] text-[#063F34] tracking-tight">
                            <span id="edit-hero-heading-1" class="wysiwyg-text inline-block" contenteditable="true">{{ $homepageContent['hero_heading_1'] }}</span><br>
                            <span id="edit-hero-heading-2" class="wysiwyg-text inline-block italic text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['hero_heading_2'] }}</span>
                        </h1>

                        <p id="edit-hero-desc" class="wysiwyg-text text-xs sm:text-sm text-[#5A6B65] leading-relaxed max-w-xl font-normal" contenteditable="true">
                            {{ $homepageContent['hero_description'] }}
                        </p>

                        <!-- 3 Genuine Heritage Amenities -->
                        <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold text-[#063F34]/80 pt-1">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1 shadow-2xs">
                                <i data-lucide="home" class="w-3 h-3 text-[#0B5D4B]"></i>
                                <span>Wooden Verandahs</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1 shadow-2xs">
                                <i data-lucide="utensils" class="w-3 h-3 text-[#0B5D4B]"></i>
                                <span>Clay-Pot Cuisine</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1 shadow-2xs">
                                <i data-lucide="sparkles" class="w-3 h-3 text-[#0B5D4B]"></i>
                                <span>Estate Spices</span>
                            </span>
                        </div>

                        <!-- Action Buttons Replica -->
                        <div class="pt-1 flex flex-wrap gap-2.5">
                            <span class="inline-flex items-center gap-2 rounded-xl bg-[#063F34] px-4 py-2.5 text-xs font-bold text-[#F7F5EF] shadow-sm">
                                Explore Cottages <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-[#C7A76A]"></i>
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs font-bold text-[#063F34]">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5 text-[#0B5D4B]"></i> Chat with Concierge
                            </span>
                        </div>
                    </div>

                    <!-- Middle Column: Centered Vertical Climate Column (Direct to Body, Centric, No Background Cards) -->
                    @if(!empty($branchesWeather))
                    <div class="hidden lg:flex lg:col-span-2 flex-col items-center text-center w-full pt-1">
                        <div class="flex items-center justify-center gap-1.5 mb-3 text-[#063F34]/60">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#0B5D4B] animate-pulse"></span>
                            <span class="text-[9.5px] font-bold uppercase tracking-[0.24em]">Climate</span>
                        </div>

                        <div class="w-full space-y-3">
                            @foreach($branchesWeather as $bId => $w)
                                <div class="text-center w-full py-1">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="text-xs font-bold text-[#063F34] tracking-tight">{{ $w['city'] }}</span>
                                        <div class="flex items-center gap-1">
                                            <i data-lucide="{{ $w['icon'] }}" class="w-3.5 h-3.5 {{ $w['icon_color'] ?? 'text-amber-500' }}"></i>
                                            <span class="font-serif text-sm font-bold text-[#063F34]">{{ $w['temperature'] }}°</span>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-[#5A6B65] font-medium mt-0.5">
                                        {{ $w['short_label'] }} <span class="text-black/20">·</span> <span class="font-mono text-[9px] text-[#5A6B65]/70">{{ $w['wind'] }}</span>
                                    </p>
                                    @if(!empty($w['forecast']))
                                        <div class="flex items-center justify-center gap-1 text-[9px] text-[#5A6B65] mt-1 font-mono">
                                            @foreach(array_slice($w['forecast'], 0, 3) as $fc)
                                                <span class="{{ $loop->first ? 'text-[#063F34] font-semibold' : '' }}">{{ $fc['day'] }}:{{ $fc['temp_max'] }}°</span>
                                                @if(!$loop->last)<span class="text-black/20">·</span>@endif
                                            @endforeach
                                        </div>
                                    @endif
                                    @if(!$loop->last)
                                        <div class="w-10 h-px bg-black/10 mx-auto mt-2.5"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3 flex items-center justify-center gap-1.5 text-[8.5px] text-[#5A6B65]/60 tracking-wider font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0B5D4B]/70"></span>
                            <span>Live Open-Meteo telemetry</span>
                        </div>
                    </div>
                    @endif

                    <!-- Right Column (lg:col-span-5): Animated Branch Carousel Live Stage & Quick Controls -->
                    <div class="lg:col-span-5 space-y-3">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#063F34] flex items-center gap-1.5">
                                <i data-lucide="layers" class="w-3.5 h-3.5 text-[#0B5D4B]"></i>
                                <span>Hero Carousel Stage</span>
                            </span>
                            <button type="button" onclick="openHeroSlideModal()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#063F34] hover:bg-[#0B5D4B] text-white text-[11px] font-bold shadow-xs transition cursor-pointer">
                                <i data-lucide="plus" class="w-3.5 h-3.5 text-[#C7A76A]"></i> Add Slide
                            </button>
                        </div>

                        <!-- Live Visual Carousel Container -->
                        <div id="admin-hero-carousel-preview" class="relative h-[440px] rounded-[30px] overflow-hidden bg-[#E9EFEA] border border-gray-200/90 shadow-lg group select-none">
                            <div id="admin-slides-container" class="relative w-full h-full">
                                @foreach($heroSlidesList as $sIdx => $s)
                                    <div class="admin-preview-slide absolute inset-0 transition-all duration-500 ease-out {{ $sIdx === 0 ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-95 pointer-events-none z-0' }}" data-admin-slide-idx="{{ $sIdx }}">
                                        <img src="{{ $s['image'] }}" class="w-full h-full object-cover" alt="{{ $s['title'] }}" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

                                        <!-- Top Left Pill Tag -->
                                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-xs rounded-full px-3.5 py-1.5 text-[10px] font-bold uppercase text-[#063F34] border border-white/60 shadow-2xs flex items-center gap-1.5">
                                            <i data-lucide="map-pin" class="w-3 h-3 text-[#0B5D4B]"></i>
                                            <span>{{ $s['tag'] ?? 'Krishna Cottages' }}</span>
                                        </div>

                                        <!-- Top Right Counter & Edit Button -->
                                        <div class="absolute top-4 right-4 z-20 flex items-center gap-1.5">
                                            <span class="bg-black/60 text-white rounded-full px-2.5 py-1 text-[10px] font-bold tracking-wider backdrop-blur-xs">
                                                0{{ $sIdx + 1 }} / 0{{ count($heroSlidesList) }}
                                            </span>
                                            <button type="button" onclick="openHeroSlideModal({{ $sIdx }})" class="p-1.5 rounded-full bg-white text-[#063F34] hover:bg-[#C7A76A] hover:text-white shadow-md transition cursor-pointer" title="Edit this slide with cropper">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>

                                        <!-- Bottom Floating Glass Card -->
                                        <div class="absolute inset-x-4 bottom-4 p-4 bg-[#063F34]/95 backdrop-blur-md text-white rounded-2xl border border-white/15 shadow-xl">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="font-serif font-bold text-sm text-white truncate">{{ $s['title'] }}</h4>
                                                        @if(!empty($s['badge']))
                                                            <span class="shrink-0 rounded-full bg-[#C7A76A]/20 px-2 py-0.5 text-[9px] font-bold text-[#C7A76A] border border-[#C7A76A]/30">{{ $s['badge'] }}</span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 text-[10px] text-white/75 leading-relaxed line-clamp-2">{{ $s['description'] }}</p>
                                                </div>
                                                <div class="shrink-0 grid h-8 w-8 place-items-center rounded-xl bg-[#C7A76A] text-[#063F34] shadow-xs">
                                                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Carousel Preview Navigation Arrows -->
                            <button type="button" onclick="cycleAdminPreviewSlide(-1)" class="absolute left-3 top-1/2 -translate-y-1/2 z-30 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-[#063F34] flex items-center justify-center shadow-md transition opacity-0 group-hover:opacity-100 cursor-pointer">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            </button>
                            <button type="button" onclick="cycleAdminPreviewSlide(1)" class="absolute right-3 top-1/2 -translate-y-1/2 z-30 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-[#063F34] flex items-center justify-center shadow-md transition opacity-0 group-hover:opacity-100 cursor-pointer">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <!-- Quick Carousel Slide Pills -->
                        <div class="flex items-center justify-between text-[11px] text-gray-500 px-1">
                            <span>Showing slide <strong id="admin-slide-current-idx">1</strong> of <strong id="admin-slide-total-count">{{ count($heroSlidesList) }}</strong></span>
                            <div class="flex items-center gap-1.5" id="admin-preview-dots">
                                @foreach($heroSlidesList as $dIdx => $s)
                                    <button type="button" onclick="goToAdminPreviewSlide({{ $dIdx }})" class="admin-dot h-1.5 rounded-full transition-all duration-300 {{ $dIdx === 0 ? 'w-5 bg-[#063F34]' : 'w-1.5 bg-gray-300' }}"></button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Hidden elements for backward compatibility -->
                        <div class="hidden">
                            <img id="edit-hero-img-main" src="{{ $homepageContent['hero_image_main'] ?? '' }}" alt="Main photo">
                            <img id="edit-hero-img-sec" src="{{ $homepageContent['hero_image_secondary'] ?? '' }}" alt="Secondary photo">
                            <span id="edit-hero-card-title">{{ $homepageContent['hero_card_title'] ?? '' }}</span>
                            <span id="edit-hero-card-desc">{{ $homepageContent['hero_card_description'] ?? '' }}</span>
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

                                        <!-- Status Badge -->
                                        <span class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[10px] font-bold {{ ($s['status'] ?? 'active') === 'active' ? 'bg-emerald-600 text-white' : 'bg-gray-500 text-white' }}">
                                            {{ ucfirst($s['status'] ?? 'active') }}
                                        </span>

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

                <!-- Centered Luxury Search Dock Replica -->
                <div class="mt-8 max-w-4xl mx-auto">
                    <div class="bg-white rounded-full p-2 border border-gray-200 shadow-md flex items-center divide-x divide-gray-200">
                        <div class="px-5 py-2 flex-[1.4]">
                            <span class="block text-[8px] uppercase font-bold text-gray-400">Destination</span>
                            <span class="text-xs font-semibold text-[#063F34]">All Branches</span>
                        </div>
                        <div class="px-5 py-2 flex-[1.4]">
                            <span class="block text-[8px] uppercase font-bold text-gray-400">Dates</span>
                            <span class="text-xs font-semibold text-[#063F34]">Flexible Check-in &middot; Check-out</span>
                        </div>
                        <div class="px-5 py-2 w-48">
                            <span class="block text-[8px] uppercase font-bold text-gray-400">Guests</span>
                            <span class="text-xs font-semibold text-[#063F34]">2 Adults, 0 Kids</span>
                        </div>
                        <div class="p-1">
                            <span class="px-5 py-2.5 rounded-full bg-[#C7A76A] text-[#063F34] font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs whitespace-nowrap">
                                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                <span>Search Stays</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 4 Balanced Feature Pillar Cards Replica -->
                <div class="mt-6 max-w-5xl mx-auto">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                        <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs">
                            <div class="h-9 w-9 rounded-xl bg-[#063F34]/5 flex items-center justify-center text-[#0B5D4B]">
                                <i data-lucide="bed-double" class="h-4.5 w-4.5"></i>
                            </div>
                            <p class="mt-3 text-xs font-bold text-[#063F34]">Cottages &amp; Suites</p>
                            <p class="mt-1 text-[10px] text-[#5A6B65] leading-snug">Timber verandas, nature views &amp; private living</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs">
                            <div class="h-9 w-9 rounded-xl bg-[#063F34]/5 flex items-center justify-center text-[#0B5D4B]">
                                <i data-lucide="utensils" class="h-4.5 w-4.5"></i>
                            </div>
                            <p class="mt-3 text-xs font-bold text-[#063F34]">Plantation Dining</p>
                            <p class="mt-1 text-[10px] text-[#5A6B65] leading-snug">Clay-pot Kerala cuisine &amp; fresh garden harvests</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs">
                            <div class="h-9 w-9 rounded-xl bg-[#063F34]/5 flex items-center justify-center text-[#0B5D4B]">
                                <i data-lucide="shopping-bag" class="h-4.5 w-4.5"></i>
                            </div>
                            <p class="mt-3 text-xs font-bold text-[#063F34]">Krishna Spices</p>
                            <p class="mt-1 text-[10px] text-[#5A6B65] leading-snug">Direct estate pepper, cloves &amp; cardamom</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-2xs">
                            <div class="h-9 w-9 rounded-xl bg-[#063F34]/5 flex items-center justify-center text-[#0B5D4B]">
                                <i data-lucide="images" class="h-4.5 w-4.5"></i>
                            </div>
                            <p class="mt-3 text-xs font-bold text-[#063F34]">Resort Gallery</p>
                            <p class="mt-1 text-[10px] text-[#5A6B65] leading-snug">Moments captured across our 3 Kerala retreats</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. BRANCH RAIL SECTION (Screenshot 2) -->
            <section class="pt-6 border-t border-gray-200">
                <div class="flex items-end justify-between mb-4">
                    <div>
                        <span id="edit-branch-eyebrow" class="wysiwyg-text text-[11px] font-bold uppercase tracking-widest text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['branch_section_eyebrow'] }}</span>
                        <h2 id="edit-branch-heading" class="wysiwyg-text text-3xl font-serif text-[#063F34] mt-1" contenteditable="true">{{ $homepageContent['branch_section_heading'] }}</h2>
                    </div>
                    <span class="text-xs text-brand-muted">Select branch dynamically for each card</span>
                </div>

                @php
                    $editorCols = match(true) {
                        $branches->count() === 1 => 'grid-cols-1 max-w-md',
                        $branches->count() === 2 => 'grid-cols-1 md:grid-cols-2',
                        $branches->count() === 3 => 'grid-cols-1 md:grid-cols-3',
                        default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
                    };
                @endphp
                <div class="grid gap-4 {{ $editorCols }}">
                    @foreach($branches as $idx => $b)
                    @php
                        $bCard = collect($homepageContent['branches'] ?? [])->firstWhere('branch_id', $b->id)
                                 ?? collect($homepageContent['branches'] ?? [])->get($idx)
                                 ?? [
                                     'branch_id' => $b->id,
                                     'title' => $b->display_name ?: $b->name,
                                     'tagline' => $b->tagline ?: ($b->city . ' · ' . $b->state),
                                     'image' => $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1100&q=85')
                                 ];
                        $cardBranchId = $bCard['branch_id'] ?? $b->id;
                        $cardTitle = $bCard['title'] ?? ($b->display_name ?: $b->name);
                        $cardTagline = $bCard['tagline'] ?? ($b->tagline ?: ($b->city . ' · ' . $b->state));
                        $cardImage = $bCard['image'] ?? ($b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1100&q=85'));
                    @endphp
                    <div class="bg-white rounded-[22px] border border-gray-200/90 shadow-sm overflow-hidden flex flex-col justify-between" id="branch-card-container-{{ $idx }}">
                        <!-- Branch Selector Dropdown -->
                        <div class="p-2.5 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase text-brand-muted">Card {{ $idx + 1 }} Destination:</span>
                            <select onchange="onSelectBranchForCard({{ $idx }}, this.value)" class="text-xs font-bold bg-white border border-gray-300 rounded px-2 py-1 text-brand-deep">
                                @foreach($branches as $branchOption)
                                    <option value="{{ $branchOption->id }}" {{ $cardBranchId == $branchOption->id ? 'selected' : '' }}>
                                        {{ $branchOption->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Thumbnail Image with Edit Overlay -->
                        <div class="relative h-44 group overflow-hidden">
                            <img id="edit-branch-img-{{ $idx }}" src="{{ $cardImage }}" alt="{{ $cardTitle }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" onclick="openImagePicker('edit-branch-img-{{ $idx }}')" class="px-3 py-1 bg-white text-brand-deep rounded text-xs font-bold shadow hover:bg-gray-100 flex items-center gap-1">
                                    <i data-lucide="camera" class="w-3.5 h-3.5"></i> Edit Photo
                                </button>
                            </div>
                            <span class="absolute top-2.5 left-2.5 bg-white/85 text-brand-deep px-2 py-0.5 rounded-full text-[10px] font-bold">0{{ $idx + 1 }}</span>
                        </div>

                        <!-- Title and Tagline -->
                        <div class="p-4 space-y-1">
                            <h3 id="edit-branch-title-{{ $idx }}" class="wysiwyg-text font-bold text-sm text-[#063F34]" contenteditable="true">{{ $cardTitle }}</h3>
                            <p id="edit-branch-tagline-{{ $idx }}" class="wysiwyg-text text-xs text-[#5A6B65]" contenteditable="true">{{ $cardTagline }}</p>
                            @if($b && $b->phone)
                            <div class="pt-1 flex items-center gap-1 text-[11px] font-semibold text-[#0B5D4B]">
                                <i data-lucide="phone-call" class="w-3 h-3"></i>
                                <span>{{ $b->phone }} (Dynamic Customer Dial)</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- 3. STAY OVERVIEW SECTION (Screenshot 3) -->
            <section class="pt-6 border-t border-gray-200">
                <div class="grid gap-4 lg:grid-cols-12 items-stretch">
                    <!-- Left Box -->
                    <div class="lg:col-span-7 bg-[#E9EFEA] rounded-[24px] p-6 border border-gray-200 flex flex-col justify-between space-y-4">
                        <div>
                            <span id="edit-stay-eyebrow" class="wysiwyg-text text-[11px] font-bold uppercase tracking-widest text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['stay_eyebrow'] }}</span>
                            <h2 class="text-3xl md:text-4xl font-serif text-[#063F34] mt-1 leading-tight">
                                <span id="edit-stay-heading-1" class="wysiwyg-text inline-block" contenteditable="true">{{ $homepageContent['stay_heading_1'] }}</span><br>
                                <span id="edit-stay-heading-2" class="wysiwyg-text inline-block italic text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['stay_heading_2'] }}</span>
                            </h2>
                            <p id="edit-stay-desc" class="wysiwyg-text text-xs text-[#5A6B65] mt-3 leading-relaxed max-w-lg" contenteditable="true">
                                {{ $homepageContent['stay_description'] }}
                            </p>
                        </div>

                        <div class="p-3.5 bg-white/80 rounded-2xl border border-forest/10">
                            <span class="text-[10px] uppercase font-bold text-[#5A6B65] block mb-2">Stay Basics</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px]">
                                <div class="p-2 rounded bg-[#F7F5EF]"><strong class="block text-brand-text">Room choices</strong><span class="text-[9px] text-brand-muted">By branch</span></div>
                                <div class="p-2 rounded bg-[#F7F5EF]"><strong class="block text-brand-text">Guest needs</strong><span class="text-[9px] text-brand-muted">Dates & stay</span></div>
                                <div class="p-2 rounded bg-[#F7F5EF]"><strong class="block text-brand-text">Easy booking</strong><span class="text-[9px] text-brand-muted">Availability</span></div>
                                <div class="p-2 rounded bg-[#F7F5EF]"><strong class="block text-brand-text">Confirmation</strong><span class="text-[9px] text-brand-muted">Email & SMS</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Box (Screenshot 3) -->
                    <div class="lg:col-span-5 space-y-3 flex flex-col justify-between">
                        <!-- Photo with Edit Overlay -->
                        <div class="relative h-56 rounded-[22px] overflow-hidden group shadow-sm">
                            <img id="edit-stay-img" src="{{ $homepageContent['stay_image'] }}" alt="Guest Room" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" onclick="openImagePicker('edit-stay-img')" class="px-3 py-1.5 bg-white text-brand-deep rounded-lg text-xs font-bold shadow hover:bg-gray-100 flex items-center gap-1.5">
                                    <i data-lucide="camera" class="w-4 h-4"></i> Edit Room Photo
                                </button>
                            </div>
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-3 text-white">
                                <h4 id="edit-stay-img-title" class="wysiwyg-text font-bold text-xs" contenteditable="true">{{ $homepageContent['stay_image_title'] }}</h4>
                                <p id="edit-stay-img-subtitle" class="wysiwyg-text text-[10px] text-white/75" contenteditable="true">{{ $homepageContent['stay_image_subtitle'] }}</p>
                            </div>
                        </div>

                        <!-- Stay Journey Steps -->
                        <div class="p-4 bg-white rounded-[22px] border border-gray-200 space-y-2 text-xs">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#0B5D4B] block">Stay Journey</span>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-[#E9EFEA] text-[#0B5D4B] font-bold text-[10px] flex items-center justify-center shrink-0">01</span>
                                <div><strong class="text-brand-text">Choose a branch</strong> — <span class="text-brand-muted text-[11px]">See branch-specific suites</span></div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-[#E9EFEA] text-[#0B5D4B] font-bold text-[10px] flex items-center justify-center shrink-0">02</span>
                                <div><strong class="text-brand-text">Check your dates</strong> — <span class="text-brand-muted text-[11px]">Instant inventory calendar</span></div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-[#E9EFEA] text-[#0B5D4B] font-bold text-[10px] flex items-center justify-center shrink-0">03</span>
                                <div><strong class="text-brand-text">Reserve with confidence</strong> — <span class="text-brand-muted text-[11px]">Direct confirmation</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. EXPERIENCES & ACTIVITIES SECTION (Screenshot 4) -->
            <section class="pt-6 border-t border-gray-200">
                <div class="mb-4">
                    <span id="edit-exp-eyebrow" class="wysiwyg-text text-[11px] font-bold uppercase tracking-widest text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['experience_eyebrow'] }}</span>
                    <h2 class="text-3xl font-serif text-[#063F34] mt-1">
                        <span id="edit-exp-heading-1" class="wysiwyg-text inline-block" contenteditable="true">{{ $homepageContent['experience_heading_1'] }}</span>
                        <span id="edit-exp-heading-2" class="wysiwyg-text inline-block italic text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['experience_heading_2'] }}</span>
                    </h2>
                </div>

                <div class="grid gap-3 md:grid-cols-4">
                    <!-- Large Left Photo Card (Screenshot 4) -->
                    <div class="md:row-span-2 relative min-h-[300px] rounded-[20px] overflow-hidden group shadow-sm bg-[#063F34] text-white">
                        <img id="edit-exp-img-main" src="{{ $homepageContent['experience_image_main'] }}" alt="Nature" class="w-full h-full object-cover opacity-80">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button type="button" onclick="openImagePicker('edit-exp-img-main')" class="px-3 py-1.5 bg-white text-brand-deep rounded text-xs font-bold shadow hover:bg-gray-100 flex items-center gap-1">
                                <i data-lucide="camera" class="w-3.5 h-3.5"></i> Edit Nature Photo
                            </button>
                        </div>
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                            <h4 id="edit-exp-card-1-title" class="wysiwyg-text font-bold text-sm" contenteditable="true">{{ $homepageContent['experience_card_1_title'] ?? 'Nature & open spaces' }}</h4>
                            <p id="edit-exp-card-1-desc" class="wysiwyg-text text-[10px] text-white/70" contenteditable="true">{{ $homepageContent['experience_card_1_desc'] ?? 'Green surroundings · quiet moments' }}</p>
                        </div>
                    </div>

                    <!-- Card 2: Local Exploration (Middle 1) -->
                    <div class="relative h-36 rounded-[20px] overflow-hidden group shadow-sm bg-[#063F34] text-white">
                        <img id="edit-exp-img-local" src="{{ $homepageContent['experience_image_local'] ?? '' }}" alt="Local exploration" class="w-full h-full object-cover opacity-70 {{ empty($homepageContent['experience_image_local']) ? 'hidden' : '' }}">
                        <div id="edit-exp-img-local-noimg" class="absolute inset-0 bg-white p-4 flex flex-col justify-between text-[#063F34] {{ !empty($homepageContent['experience_image_local']) ? 'hidden' : '' }}">
                            <div class="w-8 h-8 rounded-lg bg-[#E9EFEA] text-[#0B5D4B] flex items-center justify-center"><i data-lucide="compass" class="w-4 h-4"></i></div>
                            <div>
                                <h4 class="font-bold text-sm text-[#063F34]">{{ $homepageContent['experience_card_2_title'] ?? 'Local exploration' }}</h4>
                                <p class="text-[11px] text-brand-muted mt-1">{{ $homepageContent['experience_card_2_desc'] ?? 'Nearby places, easy routes and discoveries.' }}</p>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 z-20">
                            <button type="button" onclick="openImagePicker('edit-exp-img-local')" class="px-2.5 py-1 bg-white text-brand-deep rounded text-[10px] font-bold shadow hover:bg-gray-100 flex items-center gap-1">
                                <i data-lucide="camera" class="w-3 h-3"></i> Set Photo
                            </button>
                            <button type="button" onclick="clearExpCardImage('edit-exp-img-local')" class="px-2 py-1 bg-rose-600 text-white rounded text-[10px] font-bold shadow hover:bg-rose-700 flex items-center gap-1" title="Clear Image">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                        <div id="edit-exp-img-local-overlay" class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent p-3 z-10 {{ empty($homepageContent['experience_image_local']) ? 'hidden' : '' }}">
                            <h4 id="edit-exp-card-2-title" class="wysiwyg-text font-bold text-xs" contenteditable="true">{{ $homepageContent['experience_card_2_title'] ?? 'Local exploration' }}</h4>
                            <p id="edit-exp-card-2-desc" class="wysiwyg-text text-[9px] text-white/70" contenteditable="true">{{ $homepageContent['experience_card_2_desc'] ?? 'Nearby places, easy routes and discoveries.' }}</p>
                        </div>
                    </div>

                    <!-- Card 3: Experiences & Activities (Middle 2) -->
                    <div class="relative h-36 rounded-[20px] overflow-hidden group shadow-sm bg-[#063F34] text-white">
                        <img id="edit-exp-img-activities" src="{{ $homepageContent['experience_image_activities'] ?? '' }}" alt="Experiences & activities" class="w-full h-full object-cover opacity-70 {{ empty($homepageContent['experience_image_activities']) ? 'hidden' : '' }}">
                        <div id="edit-exp-img-activities-noimg" class="absolute inset-0 bg-white p-4 flex flex-col justify-between text-[#063F34] {{ !empty($homepageContent['experience_image_activities']) ? 'hidden' : '' }}">
                            <div class="w-8 h-8 rounded-lg bg-[#E9EFEA] text-[#0B5D4B] flex items-center justify-center"><i data-lucide="sparkles" class="w-4 h-4"></i></div>
                            <div>
                                <h4 class="font-bold text-sm text-[#063F34]">{{ $homepageContent['experience_card_3_title'] ?? 'Experiences & activities' }}</h4>
                                <p class="text-[11px] text-brand-muted mt-1">{{ $homepageContent['experience_card_3_desc'] ?? 'Slow days and memorable moments.' }}</p>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 z-20">
                            <button type="button" onclick="openImagePicker('edit-exp-img-activities')" class="px-2.5 py-1 bg-white text-brand-deep rounded text-[10px] font-bold shadow hover:bg-gray-100 flex items-center gap-1">
                                <i data-lucide="camera" class="w-3 h-3"></i> Set Photo
                            </button>
                            <button type="button" onclick="clearExpCardImage('edit-exp-img-activities')" class="px-2 py-1 bg-rose-600 text-white rounded text-[10px] font-bold shadow hover:bg-rose-700 flex items-center gap-1" title="Clear Image">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                        <div id="edit-exp-img-activities-overlay" class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent p-3 z-10 {{ empty($homepageContent['experience_image_activities']) ? 'hidden' : '' }}">
                            <h4 id="edit-exp-card-3-title" class="wysiwyg-text font-bold text-xs" contenteditable="true">{{ $homepageContent['experience_card_3_title'] ?? 'Experiences & activities' }}</h4>
                            <p id="edit-exp-card-3-desc" class="wysiwyg-text text-[9px] text-white/70" contenteditable="true">{{ $homepageContent['experience_card_3_desc'] ?? 'Slow days and memorable moments.' }}</p>
                        </div>
                    </div>

                    <!-- Family Moments Photo Card (Right Top) -->
                    <div class="relative h-36 rounded-[20px] overflow-hidden group shadow-sm bg-[#063F34] text-white">
                        <img id="edit-exp-img-family" src="{{ $homepageContent['experience_image_family'] }}" alt="Family moments" class="w-full h-full object-cover opacity-70">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button type="button" onclick="openImagePicker('edit-exp-img-family')" class="px-2 py-1 bg-white text-brand-deep rounded text-[10px] font-bold shadow hover:bg-gray-100 flex items-center gap-1">
                                <i data-lucide="camera" class="w-3 h-3"></i> Edit Photo
                            </button>
                        </div>
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-3">
                            <h4 id="edit-exp-card-4-title" class="wysiwyg-text font-bold text-xs" contenteditable="true">{{ $homepageContent['experience_card_4_title'] ?? 'Family moments' }}</h4>
                            <p id="edit-exp-card-4-desc" class="wysiwyg-text text-[9px] text-white/70" contenteditable="true">{{ $homepageContent['experience_card_4_desc'] ?? 'Easy days, shared time.' }}</p>
                        </div>
                    </div>

                    <!-- Card 5: Events & Celebrations (Middle 3) -->
                    <div class="relative h-36 rounded-[20px] overflow-hidden group shadow-sm bg-[#063F34] text-white">
                        <img id="edit-exp-img-events" src="{{ $homepageContent['experience_image_events'] ?? '' }}" alt="Events & celebrations" class="w-full h-full object-cover opacity-70 {{ empty($homepageContent['experience_image_events']) ? 'hidden' : '' }}">
                        <div id="edit-exp-img-events-noimg" class="absolute inset-0 bg-white p-4 flex flex-col justify-between text-[#063F34] {{ !empty($homepageContent['experience_image_events']) ? 'hidden' : '' }}">
                            <div class="w-8 h-8 rounded-lg bg-[#E9EFEA] text-[#0B5D4B] flex items-center justify-center"><i data-lucide="calendar" class="w-4 h-4"></i></div>
                            <div>
                                <h4 class="font-bold text-sm text-[#063F34]">{{ $homepageContent['experience_card_5_title'] ?? 'Events & celebrations' }}</h4>
                                <p class="text-[11px] text-brand-muted mt-1">{{ $homepageContent['experience_card_5_desc'] ?? 'Gatherings and special occasions.' }}</p>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 z-20">
                            <button type="button" onclick="openImagePicker('edit-exp-img-events')" class="px-2.5 py-1 bg-white text-brand-deep rounded text-[10px] font-bold shadow hover:bg-gray-100 flex items-center gap-1">
                                <i data-lucide="camera" class="w-3 h-3"></i> Set Photo
                            </button>
                            <button type="button" onclick="clearExpCardImage('edit-exp-img-events')" class="px-2 py-1 bg-rose-600 text-white rounded text-[10px] font-bold shadow hover:bg-rose-700 flex items-center gap-1" title="Clear Image">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                        <div id="edit-exp-img-events-overlay" class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent p-3 z-10 {{ empty($homepageContent['experience_image_events']) ? 'hidden' : '' }}">
                            <h4 id="edit-exp-card-5-title" class="wysiwyg-text font-bold text-xs" contenteditable="true">{{ $homepageContent['experience_card_5_title'] ?? 'Events & celebrations' }}</h4>
                            <p id="edit-exp-card-5-desc" class="wysiwyg-text text-[9px] text-white/70" contenteditable="true">{{ $homepageContent['experience_card_5_desc'] ?? 'Gatherings and special occasions.' }}</p>
                        </div>
                    </div>

                    <!-- Card 6: Dining Experiences (Middle 4) -->
                    <div class="relative h-36 rounded-[20px] overflow-hidden group shadow-sm bg-[#063F34] text-white">
                        <img id="edit-exp-img-dining" src="{{ $homepageContent['experience_image_dining'] ?? '' }}" alt="Dining experiences" class="w-full h-full object-cover opacity-70 {{ empty($homepageContent['experience_image_dining']) ? 'hidden' : '' }}">
                        <div id="edit-exp-img-dining-noimg" class="absolute inset-0 bg-white p-4 flex flex-col justify-between text-[#063F34] {{ !empty($homepageContent['experience_image_dining']) ? 'hidden' : '' }}">
                            <div class="w-8 h-8 rounded-lg bg-[#E9EFEA] text-[#0B5D4B] flex items-center justify-center"><i data-lucide="utensils" class="w-4 h-4"></i></div>
                            <div>
                                <h4 class="font-bold text-sm text-[#063F34]">{{ $homepageContent['experience_card_6_title'] ?? 'Dining experiences' }}</h4>
                                <p class="text-[11px] text-brand-muted mt-1">{{ $homepageContent['experience_card_6_desc'] ?? 'Restaurant dining and direct food orders.' }}</p>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 z-20">
                            <button type="button" onclick="openImagePicker('edit-exp-img-dining')" class="px-2.5 py-1 bg-white text-brand-deep rounded text-[10px] font-bold shadow hover:bg-gray-100 flex items-center gap-1">
                                <i data-lucide="camera" class="w-3 h-3"></i> Set Photo
                            </button>
                            <button type="button" onclick="clearExpCardImage('edit-exp-img-dining')" class="px-2 py-1 bg-rose-600 text-white rounded text-[10px] font-bold shadow hover:bg-rose-700 flex items-center gap-1" title="Clear Image">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                        <div id="edit-exp-img-dining-overlay" class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent p-3 z-10 {{ empty($homepageContent['experience_image_dining']) ? 'hidden' : '' }}">
                            <h4 id="edit-exp-card-6-title" class="wysiwyg-text font-bold text-xs" contenteditable="true">{{ $homepageContent['experience_card_6_title'] ?? 'Dining experiences' }}</h4>
                            <p id="edit-exp-card-6-desc" class="wysiwyg-text text-[9px] text-white/70" contenteditable="true">{{ $homepageContent['experience_card_6_desc'] ?? 'Restaurant dining and direct food orders.' }}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-[20px] border border-gray-200 flex flex-col justify-between h-36">
                        <div class="w-8 h-8 rounded-lg bg-[#E9EFEA] text-[#0B5D4B] flex items-center justify-center"><i data-lucide="message-square" class="w-4 h-4"></i></div>
                        <div>
                            <h4 id="edit-exp-card-7-title" class="wysiwyg-text font-bold text-sm text-[#063F34]" contenteditable="true">{{ $homepageContent['experience_card_7_title'] ?? 'Concierge & enquiry' }}</h4>
                            <p id="edit-exp-card-7-desc" class="wysiwyg-text text-[11px] text-brand-muted mt-1" contenteditable="true">{{ $homepageContent['experience_card_7_desc'] ?? 'Direct support while planning your stay.' }}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-[#0B5D4B] text-white rounded-[20px] flex flex-col justify-between h-36">
                        <div class="w-8 h-8 rounded-lg bg-white/15 text-[#C7A76A] flex items-center justify-center"><i data-lucide="map-pin" class="w-4 h-4"></i></div>
                        <div>
                            <h4 id="edit-exp-card-8-title" class="wysiwyg-text font-bold text-sm text-white" contenteditable="true">{{ $homepageContent['experience_card_8_title'] ?? 'Around your branch' }}</h4>
                            <p id="edit-exp-card-8-desc" class="wysiwyg-text text-[11px] text-white/70 mt-1" contenteditable="true">{{ $homepageContent['experience_card_8_desc'] ?? 'Discover places nearby, with branch-specific suggestions.' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. GALLERY SECTION (Screenshot 5) -->
            <section class="pt-6 border-t border-gray-200">
                <div class="flex items-end justify-between mb-4">
                    <div>
                        <span id="edit-gallery-eyebrow" class="wysiwyg-text text-[11px] font-bold uppercase tracking-widest text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['gallery_eyebrow'] }}</span>
                        <h2 id="edit-gallery-heading" class="wysiwyg-text text-3xl font-serif text-[#063F34] mt-1" contenteditable="true">{{ $homepageContent['gallery_heading'] }}</h2>
                    </div>
                    <span class="text-xs text-brand-muted">Click photo to swap featured images</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($homepageContent['gallery_images'] as $gIdx => $gItem)
                    <div class="relative h-60 rounded-[20px] overflow-hidden group shadow-sm border border-gray-200">
                        <img id="edit-gallery-img-{{ $gIdx }}" src="{{ $gItem['url'] }}" alt="Gallery preview" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button type="button" onclick="openImagePicker('edit-gallery-img-{{ $gIdx }}')" class="px-3 py-1.5 bg-white text-brand-deep rounded text-xs font-bold shadow hover:bg-gray-100 flex items-center gap-1.5">
                                <i data-lucide="camera" class="w-4 h-4"></i> Edit Image {{ $gIdx + 1 }}
                            </button>
                        </div>
                        <span id="edit-gallery-tag-{{ $gIdx }}" class="wysiwyg-text absolute bottom-3 left-3 rounded-full bg-white/90 px-3 py-1 text-[10px] font-bold text-[#063F34]" contenteditable="true">
                            {{ $gItem['tag'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- 6. DINING & KRISHNA SPICES (Screenshot 5 / requirement 6) -->
            <section class="pt-6 border-t border-gray-200">
                <div class="grid gap-4 lg:grid-cols-2">
                    <!-- Dining Card -->
                    <div class="p-6 bg-[#063F34] text-white rounded-[24px] shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span id="edit-dining-eyebrow" class="wysiwyg-text text-[10px] uppercase tracking-wider text-white/50 font-bold" contenteditable="true">{{ $homepageContent['dining_eyebrow'] }}</span>
                                <h3 class="text-2xl font-serif leading-tight mt-1">
                                    <span id="edit-dining-heading-1" class="wysiwyg-text inline-block" contenteditable="true">{{ $homepageContent['dining_heading_1'] }}</span><br>
                                    <span id="edit-dining-heading-2" class="wysiwyg-text inline-block italic text-[#C7A76A]" contenteditable="true">{{ $homepageContent['dining_heading_2'] }}</span>
                                </h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-[#C7A76A]"><i data-lucide="utensils" class="w-5 h-5"></i></div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 items-center">
                            <div class="relative h-44 rounded-xl overflow-hidden group">
                                <img id="edit-dining-img" src="{{ $homepageContent['dining_image'] }}" alt="Featured dish" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" onclick="openImagePicker('edit-dining-img')" class="px-2 py-1 bg-white text-brand-deep rounded text-[10px] font-bold shadow hover:bg-gray-100 flex items-center gap-1">
                                        <i data-lucide="camera" class="w-3 h-3"></i> Edit Dish
                                    </button>
                                </div>
                            </div>
                            <div class="p-3 bg-white/5 rounded-xl border border-white/10 space-y-1 text-xs">
                                <span class="text-[9px] uppercase tracking-widest text-[#C7A76A] font-bold">Featured</span>
                                <h4 id="edit-dining-title" class="wysiwyg-text font-serif text-lg text-white" contenteditable="true">{{ $homepageContent['dining_title'] }}</h4>
                                <p id="edit-dining-desc" class="wysiwyg-text text-[10px] text-white/70 leading-relaxed" contenteditable="true">{{ $homepageContent['dining_description'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Spices Card with Pinning Dropdowns -->
                    <div class="p-6 bg-white rounded-[24px] border border-gray-200 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span id="edit-spices-eyebrow" class="wysiwyg-text text-[10px] uppercase tracking-wider text-[#0B5D4B] font-bold" contenteditable="true">{{ $homepageContent['spices_eyebrow'] }}</span>
                                <h3 class="text-2xl font-serif leading-tight mt-1 text-[#063F34]">
                                    <span id="edit-spices-heading-1" class="wysiwyg-text inline-block" contenteditable="true">{{ $homepageContent['spices_heading_1'] }}</span><br>
                                    <span id="edit-spices-heading-2" class="wysiwyg-text inline-block italic text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['spices_heading_2'] }}</span>
                                </h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-[#E9EFEA] flex items-center justify-center text-[#0B5D4B]"><i data-lucide="shopping-bag" class="w-5 h-5"></i></div>
                        </div>

                        <!-- 2 Pinned Spices Product Selectors (Requirement 6) -->
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Pinned Spice Slot 1 -->
                            <div class="p-3 rounded-2xl border border-gray-200 bg-[#F7F5EF] space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold uppercase text-brand-muted">Pin Product 1:</span>
                                    <select id="select-pinned-spice-1" onchange="onSelectPinnedSpice(1, this.value)" class="text-[10px] font-bold bg-white border border-gray-300 rounded px-1.5 py-0.5 max-w-[110px] truncate">
                                        @foreach($spiceProducts as $sp)
                                            <option value="{{ $sp->id }}" data-name="{{ $sp->name }}" data-price="{{ $sp->price }}" data-img="{{ $sp->image_url }}" {{ ($homepageContent['spices_pinned_product_1_id'] ?? 1) == $sp->id ? 'selected' : '' }}>
                                                {{ $sp->name }} (₹{{ number_format($sp->price) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @php
                                    $pinnedSp1 = $spiceProducts->firstWhere('id', $homepageContent['spices_pinned_product_1_id'] ?? 1) ?? $spiceProducts->first();
                                @endphp
                                <div class="relative h-28 rounded-xl overflow-hidden group">
                                    <img id="pinned-spice-img-1" src="{{ $pinnedSp1 ? $pinnedSp1->image_url : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=500&q=85' }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button type="button" onclick="openImagePicker('pinned-spice-img-1')" class="px-2 py-1 bg-white text-brand-deep rounded text-[9px] font-bold shadow">
                                            Swap Photo
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <h5 id="pinned-spice-name-1" class="font-bold text-xs text-brand-text truncate">{{ $pinnedSp1 ? $pinnedSp1->name : ($spiceProducts->first() ? $spiceProducts->first()->name : 'Estate Product') }}</h5>
                                    <p id="pinned-spice-price-1" class="text-[11px] font-bold text-[#0B5D4B]">₹{{ $pinnedSp1 ? number_format($pinnedSp1->price) : ($spiceProducts->first() ? number_format($spiceProducts->first()->price) : '0') }}</p>
                                </div>
                            </div>

                            <!-- Pinned Spice Slot 2 -->
                            <div class="p-3 rounded-2xl border border-gray-200 bg-[#F7F5EF] space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold uppercase text-brand-muted">Pin Product 2:</span>
                                    <select id="select-pinned-spice-2" onchange="onSelectPinnedSpice(2, this.value)" class="text-[10px] font-bold bg-white border border-gray-300 rounded px-1.5 py-0.5 max-w-[110px] truncate">
                                        @foreach($spiceProducts as $sp)
                                            <option value="{{ $sp->id }}" data-name="{{ $sp->name }}" data-price="{{ $sp->price }}" data-img="{{ $sp->image_url }}" {{ ($homepageContent['spices_pinned_product_2_id'] ?? 2) == $sp->id ? 'selected' : '' }}>
                                                {{ $sp->name }} (₹{{ number_format($sp->price) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @php
                                    $pinnedSp2 = $spiceProducts->firstWhere('id', $homepageContent['spices_pinned_product_2_id'] ?? 2) ?? $spiceProducts->skip(1)->first() ?? $spiceProducts->first();
                                @endphp
                                <div class="relative h-28 rounded-xl overflow-hidden group">
                                    <img id="pinned-spice-img-2" src="{{ $pinnedSp2 ? $pinnedSp2->image_url : ($spiceProducts->first() ? $spiceProducts->first()->image_url : '') }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button type="button" onclick="openImagePicker('pinned-spice-img-2')" class="px-2 py-1 bg-white text-brand-deep rounded text-[9px] font-bold shadow">
                                            Swap Photo
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <h5 id="pinned-spice-name-2" class="font-bold text-xs text-brand-text truncate">{{ $pinnedSp2 ? $pinnedSp2->name : ($spiceProducts->skip(1)->first() ? $spiceProducts->skip(1)->first()->name : 'Estate Blend') }}</h5>
                                    <p id="pinned-spice-price-2" class="text-[11px] font-bold text-[#0B5D4B]">₹{{ $pinnedSp2 ? number_format($pinnedSp2->price) : ($spiceProducts->skip(1)->first() ? number_format($spiceProducts->skip(1)->first()->price) : '0') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 7. OUR LOCATIONS (BRANCH RETREATS) & VERIFIED REVIEW SECTION (DITTO REPLICA OF WELCOME) -->
            <section class="pt-6 border-t border-gray-200">
                <div class="grid gap-4 lg:grid-cols-12 items-stretch">
                    <!-- Left: Our Locations (Branch Retreats with Google Maps) (7 cols) -->
                    <div class="p-6 bg-white rounded-[24px] border border-gray-200 shadow-sm lg:col-span-7 flex flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span id="edit-locations-eyebrow" class="wysiwyg-text text-[10px] uppercase tracking-wider text-[#0B5D4B] font-bold" contenteditable="true">{{ $homepageContent['locations_eyebrow'] ?? 'Kerala Sanctuaries' }}</span>
                                <h3 class="text-3xl font-serif leading-tight mt-1 text-[#063F34]">
                                    <span id="edit-locations-heading-1" class="wysiwyg-text inline-block" contenteditable="true">{{ $homepageContent['locations_heading_1'] ?? 'Our Locations' }}</span><br>
                                    <span id="edit-locations-heading-2" class="wysiwyg-text inline-block italic text-[#0B5D4B]" contenteditable="true">{{ $homepageContent['locations_heading_2'] ?? 'Visit our retreats.' }}</span>
                                </h3>
                            </div>
                            <span class="rounded-full bg-[#E9EFEA] px-3 py-1.5 text-[9px] font-bold text-[#0B5D4B] flex items-center gap-1 shadow-2xs">
                                <i data-lucide="map-pin" class="w-3 h-3 text-[#0B5D4B]"></i>
                                <span>{{ $branches->count() }} Destinations</span>
                            </span>
                        </div>

                        <!-- 3 Live Branch Location Cards (Replicating welcome.blade.php) -->
                        <div class="mt-2 grid gap-3 sm:grid-cols-3">
                            @foreach($branches as $b)
                            @php
                                $mapsUrl = $b->google_maps_url ?: ('https://www.google.com/maps/search/?api=1&query=' . urlencode($b->name . ' ' . $b->city . ' Kerala India'));
                                $branchImg = $b->cover_image_url ?: ($b->hero_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80');
                            @endphp
                            <div class="group relative overflow-hidden rounded-[20px] bg-[#063F34] text-[#F7F5EF] h-[210px] sm:h-[225px] flex flex-col justify-between p-3.5 border border-gray-200 shadow-sm transition duration-300">
                                <!-- Branch Background Photo -->
                                <img src="{{ $branchImg }}" alt="{{ $b->name }}" class="absolute inset-0 h-full w-full object-cover brightness-90 transition-transform duration-500 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                                <!-- Top Badge: City & Maps External Icon -->
                                <div class="relative z-10 flex items-center justify-between">
                                    <span class="bg-[#063F34]/85 backdrop-blur-md px-2.5 py-1 rounded-full text-[9px] font-bold text-[#F7F5EF] uppercase tracking-wider flex items-center gap-1 shadow-xs">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-[#C7A76A]"></i>
                                        <span>{{ $b->city }}</span>
                                    </span>
                                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-7 w-7 place-items-center rounded-full bg-white/20 backdrop-blur-md text-white hover:bg-[#C7A76A] hover:text-[#063F34] transition shadow-xs" title="Open Google Maps">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>

                                <!-- Bottom Content: Branch Name & Google Maps Link -->
                                <div class="relative z-10 space-y-1">
                                    <h4 class="font-serif text-base font-bold text-white group-hover:text-[#C7A76A] transition leading-snug">
                                        {{ $b->name }}
                                    </h4>
                                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1 text-[10px] text-[#F7F5EF]/80 font-medium hover:text-[#C7A76A]">
                                        <span class="underline">Google Maps</span>
                                        <i data-lucide="arrow-up-right" class="w-3 h-3 text-[#C7A76A]"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Hidden fields for backward compatibility -->
                        <div class="hidden">
                            <span id="edit-nearby-eyebrow">{{ $homepageContent['nearby_eyebrow'] ?? 'Around you' }}</span>
                            <span id="edit-nearby-heading-1">{{ $homepageContent['nearby_heading_1'] ?? 'Discover the' }}</span>
                            <span id="edit-nearby-heading-2">{{ $homepageContent['nearby_heading_2'] ?? 'nearby.' }}</span>
                            <span id="edit-nearby-item-1-name">{{ $homepageContent['nearby_item_1_name'] ?? 'River viewpoint' }}</span>
                            <span id="edit-nearby-item-1-dist">{{ $homepageContent['nearby_item_1_dist'] ?? '2.1 km' }}</span>
                            <span id="edit-nearby-item-1-tag">{{ $homepageContent['nearby_item_1_tag'] ?? 'Nature · easy access' }}</span>
                            <span id="edit-nearby-item-2-name">{{ $homepageContent['nearby_item_2_name'] ?? 'Temple trail' }}</span>
                            <span id="edit-nearby-item-2-dist">{{ $homepageContent['nearby_item_2_dist'] ?? '4.3 km' }}</span>
                            <span id="edit-nearby-item-2-tag">{{ $homepageContent['nearby_item_2_tag'] ?? 'Culture · half day' }}</span>
                            <span id="edit-nearby-item-3-name">{{ $homepageContent['nearby_item_3_name'] ?? 'Waterfall' }}</span>
                            <span id="edit-nearby-item-3-dist">{{ $homepageContent['nearby_item_3_dist'] ?? '8.2 km' }}</span>
                            <span id="edit-nearby-item-3-tag">{{ $homepageContent['nearby_item_3_tag'] ?? 'Nature · explore' }}</span>
                        </div>
                    </div>

                    <!-- Right: Dynamic Carousel Preview (5 cols) (Managed in Reviews & Testimonials Hub) -->
                    <div class="p-6 bg-[#E9EFEA] rounded-[24px] border border-gray-200 shadow-sm lg:col-span-5 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-[#0B5D4B] font-bold">Dynamic Testimonials Carousel</span>
                                <span class="text-[#C7A76A] tracking-wider text-xs font-bold">★★★★★</span>
                            </div>

                            @php
                                $previewTestimonial = $testimonials->where('is_active', true)->first() ?? $testimonials->first();
                            @endphp

                            <div class="mt-4 space-y-3">
                                <p class="font-serif text-2xl text-[#063F34] leading-snug">
                                    “{{ $previewTestimonial->quote ?? 'Everything felt easy — from the room to dinner to knowing what was nearby.' }}”
                                </p>
                                <div class="flex items-center gap-1.5 text-[11px] text-[#5A6B65]">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i>
                                    <span>Sliding Carousel · {{ $testimonials->where('is_active', true)->count() }} Active Stories</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-[#063F34]/10">
                            <div>
                                <h5 class="font-bold text-xs text-[#063F34]">{{ $previewTestimonial->guest_name ?? 'Ananya & Ravi' }}</h5>
                                <p class="text-[10px] text-[#5A6B65]">{{ $previewTestimonial->stay_title ?? 'Garden Residence · completed stay' }}</p>
                            </div>
                            <a href="#reviews" onclick="navigateTo('reviews'); switchReviewsHubTab('testimonials');" class="px-3.5 py-1.5 bg-brand-deep hover:bg-brand-primary text-white font-bold text-[11px] rounded-xl shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="sliders" class="w-3.5 h-3.5 text-brand-accent"></i>
                                <span>Curate & Order</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8. FINAL CALL TO ACTION BANNER -->
            <section class="p-8 bg-[#063F34] text-white rounded-[26px] shadow-md flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 max-w-xl">
                    <span id="edit-cta-eyebrow" class="wysiwyg-text text-[10px] font-bold uppercase tracking-widest text-[#C7A76A]" contenteditable="true">{{ $homepageContent['cta_eyebrow'] }}</span>
                    <h3 class="text-3xl font-serif leading-tight">
                        <span id="edit-cta-heading-1" class="wysiwyg-text inline-block" contenteditable="true">{{ $homepageContent['cta_heading_1'] }}</span><br>
                        <span id="edit-cta-heading-2" class="wysiwyg-text inline-block italic text-[#C7A76A]" contenteditable="true">{{ $homepageContent['cta_heading_2'] }}</span>
                    </h3>
                    <p id="edit-cta-desc" class="wysiwyg-text text-xs text-white/70 leading-relaxed" contenteditable="true">{{ $homepageContent['cta_description'] }}</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="px-5 py-3 bg-[#F7F5EF] text-[#063F34] font-bold text-xs rounded-xl shadow-xs">Book your stay</span>
                    <span class="px-4 py-3 bg-white/10 text-white font-bold text-xs rounded-xl border border-white/15">Chat directly</span>
                </div>
            </section>

            <!-- 9. LIVE EDITABLE LUXURY FOOTER (PPT-STYLE CANVAS - SHARED UNIVERSALLY) -->
            <section class="mt-8 pt-4">
                <div class="flex items-center justify-between mb-3 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#C7A76A]"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#063F34]">Section 9 · Luxury Footer & Concierge (Universally Shared on All Pages)</span>
                    </div>
                    <span class="text-[11px] text-gray-500 italic">Editing here updates the footer on welcome, stay, dining, spices, and all client pages</span>
                </div>

                <div class="bg-[#052C24] text-[#F7F5EF] rounded-[28px] p-8 md:p-10 border border-white/10 shadow-xl space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Brand Identity & Bio -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-2.5">
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#F7F5EF] text-sm font-bold text-[#063F34]">K</span>
                                <div>
                                    <span id="edit-footer-brand-title" class="wysiwyg-text font-serif text-xl font-bold block text-[#F7F5EF] tracking-tight" contenteditable="true">{{ $homepageContent['footer_brand_title'] ?? 'Krishna Resorts' }}</span>
                                    <span id="edit-footer-brand-tagline" class="wysiwyg-text text-[9px] uppercase tracking-[.25em] text-[#C7A76A] font-bold block" contenteditable="true">{{ $homepageContent['footer_brand_tagline'] ?? 'Luxury Cottages of Kerala' }}</span>
                                </div>
                            </div>
                            <p id="edit-footer-brand-desc" class="wysiwyg-text text-xs text-white/70 leading-relaxed max-w-sm" contenteditable="true">
                                {{ $homepageContent['footer_brand_desc'] ?? 'Immersive, slow-living cottages nestled in the spice hills, misty valleys, and tranquil backwaters of Kerala.' }}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-[#C7A76A] font-semibold">
                                <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i>
                                <span id="edit-footer-badge" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_badge'] ?? 'Eco-Conscious Botanical Cottages' }}</span>
                            </div>
                        </div>

                        <!-- Destinations Column -->
                        <div>
                            <h4 id="edit-footer-dest-title" class="wysiwyg-text text-[10px] font-bold uppercase tracking-wider text-[#C7A76A] mb-4" contenteditable="true">{{ $homepageContent['footer_dest_title'] ?? 'Destinations' }}</h4>
                            <ul class="space-y-2 text-xs text-white/75">
                                <li class="flex items-center gap-1.5 text-white/60">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-[#C7A76A]/70"></i>
                                    <span>Munnar High Forest (Dynamic)</span>
                                </li>
                                <li class="flex items-center gap-1.5 text-white/60">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-[#C7A76A]/70"></i>
                                    <span>Wayanad Valley Reserve (Dynamic)</span>
                                </li>
                                <li class="flex items-center gap-1.5 text-white/60">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-[#C7A76A]/70"></i>
                                    <span>Kumarakom Waterside (Dynamic)</span>
                                </li>
                                <li class="flex items-center gap-1.5 text-white/60">
                                    <i data-lucide="sparkles" class="w-3 h-3 text-[#C7A76A]/70"></i>
                                    <span>Ayurvedic Wellness Pavilion</span>
                                </li>
                            </ul>
                            <span class="text-[10px] text-white/40 block mt-2 italic">* Branch names auto-sync from Branch Manager</span>
                        </div>

                        <!-- Curated Experiences Column -->
                        <div>
                            <h4 id="edit-footer-exp-title" class="wysiwyg-text text-[10px] font-bold uppercase tracking-wider text-[#C7A76A] mb-4" contenteditable="true">{{ $homepageContent['footer_exp_title'] ?? 'Experiences' }}</h4>
                            <ul class="space-y-2 text-xs text-white/75">
                                <li><span id="edit-footer-exp-1" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_exp_link_1'] ?? 'Plantation Kitchen & Dining' }}</span></li>
                                <li><span id="edit-footer-exp-2" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_exp_link_2'] ?? 'Krishna Spices Farm Shop' }}</span></li>
                                <li><span id="edit-footer-exp-3" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_exp_link_3'] ?? 'Resort Visual Gallery' }}</span></li>
                                <li><span id="edit-footer-exp-4" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_exp_link_4'] ?? 'Guided Nature Discoveries' }}</span></li>
                            </ul>
                        </div>

                        <!-- Direct Concierge Column -->
                        <div>
                            <h4 id="edit-footer-concierge-title" class="wysiwyg-text text-[10px] font-bold uppercase tracking-wider text-[#C7A76A] mb-4" contenteditable="true">{{ $homepageContent['footer_concierge_title'] ?? 'Direct Concierge' }}</h4>
                            <p id="edit-footer-concierge-desc" class="wysiwyg-text text-xs text-white/70 mb-3" contenteditable="true">{{ $homepageContent['footer_concierge_desc'] ?? 'Front desk assistance 24/7 for bespoke retreat arrangements.' }}</p>
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center gap-2 text-[#C7A76A]">
                                    <i data-lucide="phone" class="w-3.5 h-3.5 shrink-0"></i>
                                    <span id="edit-footer-phone" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_phone'] ?? '+91 484 290 0000' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-white/75">
                                    <i data-lucide="mail" class="w-3.5 h-3.5 shrink-0"></i>
                                    <span id="edit-footer-email" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_email'] ?? 'concierge@krishnaresorts.com' }}</span>
                                </div>
                            </div>
                            <div class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#C7A76A] px-3.5 py-2 text-[10px] font-bold text-[#063F34]">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                <span id="edit-footer-chat-btn" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_chat_btn'] ?? 'Chat with Concierge' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Copyright & Legal Links -->
                    <div class="border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between text-[11px] text-white/50 gap-4">
                        <p>&copy; {{ date('Y') }} <span id="edit-footer-copyright" class="wysiwyg-text" contenteditable="true">{{ $homepageContent['footer_copyright'] ?? 'Krishna Resorts Hospitality Ltd. All rights reserved.' }}</span></p>
                        <div class="flex items-center gap-6">
                            <span id="edit-footer-support" class="wysiwyg-text hover:text-white" contenteditable="true">{{ $homepageContent['footer_support_link'] ?? 'Help & Support' }}</span>
                            <span id="edit-footer-policy" class="wysiwyg-text text-[#C7A76A]/80" contenteditable="true">{{ $homepageContent['footer_policy_note'] ?? '🌿 Strictly No Swimming Pool Policy' }}</span>
                        </div>
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
