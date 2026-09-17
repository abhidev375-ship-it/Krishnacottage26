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
@endphp

<!-- =========================================================================
     HERO & MENU CAROUSEL SLIDES CRUD MANAGER (ADM-12B)
     ========================================================================= -->
<section id="slides" class="section space-y-6 hidden">
    
    <!-- Top Control Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-brand-deep flex items-center justify-center text-brand-accent shadow-xs">
                    <i data-lucide="presentation" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-brand-text">Hero & Menu Carousel Slides</h2>
                    <p class="text-xs text-brand-muted mt-0.5">Manage full-bleed photography, dynamic headlines, destination tags, badges, and room booking links for the homepage hero carousel.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="/" target="_blank" class="px-3 py-2 rounded-xl text-xs font-semibold text-brand-primary bg-brand-canvas hover:bg-gray-200 border border-gray-200 transition flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>View Live Site</span>
            </a>
            <button type="button" onclick="openHeroSlideModal()" class="px-4 py-2 rounded-xl bg-brand-deep hover:bg-black text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <i data-lucide="plus-circle" class="w-4 h-4 text-brand-accent"></i>
                <span>Add New Slide</span>
            </button>
        </div>
    </div>

    <!-- Live Card Carousel Preview Deck -->
    <div class="bg-white rounded-3xl border border-gray-200/90 shadow-sm p-4 sm:p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h3 class="font-bold text-sm text-brand-text">Interactive Live Carousel Preview</h3>
                <span class="text-brand-muted text-xs">— Matches public desktop & mobile presentation</span>
            </div>
            <div class="flex items-center gap-2">
                <span id="slides-crud-counter-badge" class="px-2.5 py-1 rounded-full text-xs font-bold bg-brand-deep text-brand-accent">
                    {{ count($heroSlidesList) }} Active Slides
                </span>
            </div>
        </div>

        <!-- Framed Resort Card Preview Canvas -->
        <div class="max-w-4xl mx-auto">
            <div id="slides-crud-carousel-preview" class="relative w-full min-h-[380px] sm:min-h-[460px] rounded-3xl overflow-hidden bg-brand-deep shadow-xl group select-none flex flex-col justify-between">
                
                <!-- Track -->
                <div id="slides-crud-preview-track" class="relative w-full h-full flex-1">
                    @foreach($heroSlidesList as $sIdx => $s)
                        <div class="slides-crud-slide absolute inset-0 transition-all duration-700 ease-in-out {{ $sIdx === 0 ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 pointer-events-none z-0' }}" data-slide-index="{{ $sIdx }}">
                            <img src="{{ $s['image'] }}" alt="{{ $s['title'] }}" class="w-full h-full object-cover brightness-[0.78]" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-black/30"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/20 to-black/50"></div>

                            <!-- Top Floating Tag -->
                            <div class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/50 backdrop-blur-md text-white text-[10px] sm:text-xs font-semibold uppercase tracking-wider border border-white/20">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-brand-accent"></i>
                                    <span>{{ $s['tag'] ?? ($s['subtitle'] ?? 'Krishna Cottages') }}</span>
                                </span>
                                @if(!empty($s['badge']))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-brand-accent/25 backdrop-blur-md text-brand-accent text-[10px] font-bold border border-brand-accent/30">
                                        {{ $s['badge'] }}
                                    </span>
                                @endif
                            </div>

                            <!-- Top Right Counter & Quick Edit -->
                            <div class="absolute top-4 right-4 sm:top-6 sm:right-6 z-20 flex items-center gap-2">
                                <div class="text-white/80 bg-black/50 backdrop-blur-md px-3 py-1 rounded-full text-xs font-mono border border-white/15">
                                    <span class="text-white font-bold">0{{ $sIdx + 1 }}</span> / 0{{ count($heroSlidesList) }}
                                </div>
                                <button type="button" onclick="openHeroSlideModal({{ $sIdx }})" class="p-1.5 rounded-full bg-white text-brand-deep hover:bg-brand-accent hover:text-white shadow-md transition cursor-pointer" title="Edit this slide">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                            <!-- Centered Content -->
                            <div class="absolute inset-0 z-20 flex flex-col items-center justify-center text-center px-4 sm:px-8 max-w-2xl mx-auto text-white">
                                <p class="text-brand-accent text-[10px] sm:text-xs tracking-[.25em] mb-2 font-bold uppercase">
                                    {{ $s['subtitle'] ?? ($s['tag'] ?? 'HERITAGE SANCTUARY') }}
                                </p>
                                <h3 class="serif text-2xl sm:text-4xl font-bold tracking-tight text-white drop-shadow-md">
                                    {{ $s['title'] }}
                                </h3>
                                <p class="mt-2 text-xs sm:text-sm text-white/90 leading-relaxed line-clamp-2">
                                    {{ $s['description'] }}
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
                    @endforeach
                </div>

                <!-- Navigation Arrows -->
                <button type="button" onclick="cycleSlidesCrudSlide(-1)" class="absolute left-3 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-black/40 hover:bg-white text-white hover:text-brand-deep backdrop-blur-md border border-white/20 flex items-center justify-center shadow-lg transition cursor-pointer">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <button type="button" onclick="cycleSlidesCrudSlide(1)" class="absolute right-3 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-black/40 hover:bg-white text-white hover:text-brand-deep backdrop-blur-md border border-white/20 flex items-center justify-center shadow-lg transition cursor-pointer">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>

                <!-- Indicator Dots -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 flex items-center gap-2 bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/15" id="slides-crud-preview-dots">
                    @foreach($heroSlidesList as $dIdx => $s)
                        <button type="button" onclick="goToSlidesCrudSlide({{ $dIdx }})" class="slides-crud-dot h-2 rounded-full transition-all duration-300 cursor-pointer {{ $dIdx === 0 ? 'w-6 bg-brand-accent' : 'w-2 bg-white/40' }}"></button>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <!-- Complete Slides CRUD Cards Grid -->
    <div class="bg-white rounded-3xl border border-gray-200/90 shadow-sm p-4 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-base text-brand-text flex items-center gap-2">
                    <i data-lucide="list-ordered" class="w-4 h-4 text-brand-primary"></i>
                    <span>All Configured Hero Carousel Slides</span>
                </h3>
                <p class="text-xs text-brand-muted mt-0.5">Use the controls below to reorder slides, adjust text and images, toggle active visibility, or delete slides.</p>
            </div>
            <button type="button" onclick="openHeroSlideModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold shadow-xs transition cursor-pointer shrink-0">
                <i data-lucide="plus" class="w-4 h-4 text-brand-accent"></i>
                <span>Add Slide</span>
            </button>
        </div>

        <!-- Dynamic Slide Cards Container (Mirrored in JS) -->
        <div id="slides-crud-manager-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($heroSlidesList as $sIdx => $s)
                <div class="slide-crud-card rounded-2xl border border-gray-200 bg-[#FAF7F0]/70 p-4 flex flex-col justify-between space-y-3.5 hover:border-brand-primary/40 hover:shadow-lg transition duration-200 group" data-slide-index="{{ $sIdx }}">
                    <div class="space-y-3">
                        <!-- Thumbnail Box with Badge & Tags -->
                        <div class="relative h-44 rounded-xl overflow-hidden bg-black/10 border border-gray-200 shadow-2xs">
                            <img src="{{ $s['image'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $s['title'] }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-black/30"></div>
                            
                            <!-- Slide Order Badge -->
                            <span class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-lg bg-brand-deep text-white text-[11px] font-bold shadow-xs flex items-center gap-1">
                                <span>#{{ $s['sort_order'] ?? ($sIdx + 1) }}</span>
                            </span>

                            <!-- Status Toggle Button (1-Click) -->
                            <button type="button" onclick="toggleHeroSlideStatus({{ $sIdx }})" class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-lg text-[10px] font-bold transition shadow-xs cursor-pointer flex items-center gap-1 {{ ($s['status'] ?? 'active') === 'active' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-600 hover:bg-gray-700 text-white' }}" title="Click to toggle Active/Hidden">
                                <span class="w-1.5 h-1.5 rounded-full {{ ($s['status'] ?? 'active') === 'active' ? 'bg-emerald-300' : 'bg-gray-300' }}"></span>
                                <span>{{ ucfirst($s['status'] ?? 'active') }}</span>
                            </button>

                            <!-- Destination Tag (Bottom Left) -->
                            <span class="absolute bottom-2.5 left-2.5 px-3 py-1 rounded-full bg-white/95 text-brand-deep text-[10px] font-bold shadow-xs flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3 text-brand-accent"></i>
                                <span>{{ $s['tag'] ?? 'Kerala Retreat' }}</span>
                            </span>

                            @if(!empty($s['badge']))
                                <span class="absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded-full bg-brand-accent text-brand-deep text-[10px] font-bold shadow-xs">
                                    {{ $s['badge'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Content Details -->
                        <div class="space-y-1">
                            <h4 class="font-bold text-sm text-brand-deep line-clamp-1">{{ $s['title'] }}</h4>
                            @if(!empty($s['subtitle']))
                                <p class="text-[11px] font-semibold text-brand-primary line-clamp-1">{{ $s['subtitle'] }}</p>
                            @endif
                            <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed pt-1">{{ $s['description'] }}</p>
                        </div>

                        <!-- Target Link Info -->
                        <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between text-[11px] text-gray-500 font-mono">
                            <span class="truncate max-w-[200px]" title="{{ $s['link'] ?? '/rooms' }}">
                                <i data-lucide="link" class="w-3 h-3 inline mr-1 text-gray-400"></i>
                                {{ $s['link'] ?? '/rooms' }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Controls Footer -->
                    <div class="pt-3 border-t border-gray-200/80 flex items-center justify-between gap-2">
                        <!-- Move Earlier / Later -->
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="moveHeroSlide({{ $sIdx }}, -1)" class="p-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 transition cursor-pointer disabled:opacity-30 disabled:pointer-events-none shadow-2xs" title="Move Slide Earlier" {{ $sIdx === 0 ? 'disabled' : '' }}>
                                <i data-lucide="arrow-up" class="w-3.5 h-3.5"></i>
                            </button>
                            <button type="button" onclick="moveHeroSlide({{ $sIdx }}, 1)" class="p-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 transition cursor-pointer disabled:opacity-30 disabled:pointer-events-none shadow-2xs" title="Move Slide Later" {{ $sIdx === count($heroSlidesList) - 1 ? 'disabled' : '' }}>
                                <i data-lucide="arrow-down" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <!-- Edit with Cropper & Delete -->
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="openHeroSlideModal({{ $sIdx }})" class="px-3 py-1.5 rounded-xl border border-brand-primary/30 bg-emerald-50 hover:bg-emerald-100 text-brand-primary text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                <span>Edit & Crop</span>
                            </button>
                            <button type="button" onclick="deleteHeroSlide({{ $sIdx }})" class="p-2 rounded-xl border border-red-200 bg-red-50/70 hover:bg-red-100 text-red-700 transition cursor-pointer shadow-2xs" title="Delete Slide">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</section>
