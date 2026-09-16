@extends('layouts.customer')

@section('title', 'Rooms & Cottages | Krishna Cottages')

@section('content')
<!-- AIRBNB-STYLE FLOATING SEARCH & FILTER BAR -->
<div class="bg-forest text-paper pt-5 pb-8 sm:pt-7 sm:pb-12 px-4 sm:px-6 relative z-40 overflow-visible">
    <div class="mx-auto max-w-[1480px] relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-4 sm:mb-6">
            <span class="eyebrow text-brass block mb-1">Authentic Kerala Cottages</span>
            <h1 class="serif text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight">Book Your Stay</h1>
            <p class="text-xs sm:text-sm text-paper/70 mt-2">Private forest villas, heritage wooden cottages, and lakeside pavilions surrounded by nature.</p>
        </div>

        <!-- SEARCH BAR CAPSULE (AIRBNB STYLE) -->
        <form action="{{ route('rooms.index') }}" method="GET" class="bg-white text-forest rounded-2xl md:rounded-full p-2 shadow-[0_15px_45px_rgba(6,63,52,0.12)] max-w-4xl mx-auto flex flex-col md:flex-row items-stretch md:items-center divide-y md:divide-y-0 md:divide-x divide-forest/10 border border-forest/15 transition-all duration-300 hover:shadow-[0_20px_55px_rgba(6,63,52,0.18)] hover:border-forest/25 relative z-40">
            <!-- 1. DESTINATION / BRANCH (Ultra-Stylish Card Matching Guest Picker) -->
            <div class="relative px-5 py-2.5 flex-1 hover:bg-forest/[0.03] rounded-xl md:rounded-l-full transition cursor-pointer group" onclick="const s=document.getElementById('stayBranchSelect'); if(s){s.focus(); try{s.showPicker();}catch(e){}}">
                <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3 h-3 text-emerald shrink-0"></i>
                    <span>Destination</span>
                </label>
                <div class="relative flex items-center justify-between mt-0.5">
                    <select name="branch_id" id="stayBranchSelect" class="w-full bg-transparent text-sm font-semibold text-forest focus:outline-none cursor-pointer appearance-none pr-6 truncate z-10">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>
                                {{ $b->name }} ({{ $b->city }})
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-forest/40 group-hover:text-forest transition-transform duration-200 shrink-0 absolute right-0 pointer-events-none"></i>
                </div>
            </div>

            <!-- 2. CHECK-IN / CHECK-OUT DATES (Stylish Double Card with Clickable Calendar Trigger) -->
            <div class="flex-[1.4] flex items-center divide-x divide-forest/10">
                <!-- Check-in Subcard -->
                <div class="flex-1 px-4 py-2.5 hover:bg-forest/[0.03] transition-colors cursor-pointer group relative" onclick="openDateCalendar('stay-check-in-input')">
                    <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3 h-3 text-emerald shrink-0"></i>
                        <span>Check-in</span>
                    </label>
                    <div class="flex items-center justify-between mt-0.5">
                        <span id="stay-check-in-display" class="text-sm font-semibold text-forest truncate">
                            {{ !empty($checkIn) ? date('D, d M', strtotime($checkIn)) : date('D, d M', strtotime('+1 day')) }}
                        </span>
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-forest/30 group-hover:text-emerald transition shrink-0 ml-1"></i>
                    </div>
                    <input type="date" 
                           name="check_in" 
                           id="stay-check-in-input" 
                           value="{{ !empty($checkIn) ? $checkIn : date('Y-m-d', strtotime('+1 day')) }}" 
                           min="{{ date('Y-m-d') }}" 
                           onchange="updateDateDisplay('stay-check-in-input', 'stay-check-in-display'); updateMinCheckOut('stay-check-in-input', 'stay-check-out-input');" 
                           class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
                </div>

                <!-- Check-out Subcard -->
                <div class="flex-1 px-4 py-2.5 hover:bg-forest/[0.03] transition-colors cursor-pointer group relative" onclick="openDateCalendar('stay-check-out-input')">
                    <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="calendar-check-2" class="w-3 h-3 text-emerald shrink-0"></i>
                        <span>Check-out</span>
                    </label>
                    <div class="flex items-center justify-between mt-0.5">
                        <span id="stay-check-out-display" class="text-sm font-semibold text-forest truncate">
                            {{ !empty($checkOut) ? date('D, d M', strtotime($checkOut)) : date('D, d M', strtotime('+3 days')) }}
                        </span>
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-forest/30 group-hover:text-emerald transition shrink-0 ml-1"></i>
                    </div>
                    <input type="date" 
                           name="check_out" 
                           id="stay-check-out-input" 
                           value="{{ !empty($checkOut) ? $checkOut : date('Y-m-d', strtotime('+3 days')) }}" 
                           min="{{ !empty($checkIn) ? date('Y-m-d', strtotime($checkIn . ' +1 day')) : date('Y-m-d', strtotime('+2 days')) }}" 
                           onchange="updateDateDisplay('stay-check-out-input', 'stay-check-out-display')" 
                           class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
                </div>
            </div>

            <!-- 3. GUESTS COUNTER (POPOVER STEPPER) -->
            <div class="relative px-5 py-2.5 w-full md:w-56 hover:bg-forest/[0.03] transition cursor-pointer group" id="stay-guest-picker-container">
                <input type="hidden" name="adults" id="stay-adults-input" value="{{ $adults }}">
                <input type="hidden" name="children" id="stay-children-input" value="{{ $children }}">
                <div onclick="toggleStayGuestPopover()" class="w-full">
                    <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="users" class="w-3 h-3 text-emerald shrink-0"></i>
                        <span>Guests</span>
                    </label>
                    <div class="flex items-center justify-between text-sm font-semibold text-forest mt-0.5 select-none">
                        <span id="stay-guest-label" class="truncate">{{ $adults }} Adult{{ $adults > 1 ? 's' : '' }}{{ $children > 0 ? ', ' . $children . ' Kid' . ($children > 1 ? 's' : '') : ', 0 Kids' }}</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-forest/40 group-hover:text-forest transition-transform duration-200 shrink-0 ml-1" id="stay-guest-chevron"></i>
                    </div>
                </div>

                <!-- Floating Popover Card -->
                <div id="stay-guest-popover" class="hidden absolute top-full right-0 mt-3 w-72 rounded-2xl bg-[#FAF8F5] border border-forest/15 p-4 shadow-2xl z-[100] animate-in fade-in zoom-in-95 duration-150">
                    <div class="space-y-3.5" onclick="event.stopPropagation()">
                        <!-- Adults Row -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-forest">Adults</p>
                                <p class="text-[10px] text-forest/50">Ages 13 and above</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <button type="button" onclick="adjustStayGuestCount('adults', -1)" id="stay-adults-minus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer" {{ $adults <= 1 ? 'disabled' : '' }}>-</button>
                                <span id="stay-adults-val" class="w-4 text-center text-xs font-bold text-forest">{{ $adults }}</span>
                                <button type="button" onclick="adjustStayGuestCount('adults', 1)" id="stay-adults-plus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
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
                                <button type="button" onclick="adjustStayGuestCount('children', -1)" id="stay-children-minus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer" {{ $children <= 0 ? 'disabled' : '' }}>-</button>
                                <span id="stay-children-val" class="w-4 text-center text-xs font-bold text-forest">{{ $children }}</span>
                                <button type="button" onclick="adjustStayGuestCount('children', 1)" id="stay-children-plus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
                            </div>
                        </div>

                        <!-- Policy Notice -->
                        <div class="p-2 rounded-xl bg-white soft-border text-[10px] text-forest/65 flex items-start gap-1.5 leading-snug">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-emerald shrink-0 mt-0.5"></i>
                            <span>Infants &amp; toddlers under 5 stay complimentary using existing bedding.</span>
                        </div>

                        <!-- Apply Button -->
                        <button type="button" onclick="closeStayGuestPopover()" class="w-full py-1.5 rounded-xl bg-forest text-paper text-xs font-bold hover:bg-emerald transition cursor-pointer">
                            Apply Guests
                        </button>
                    </div>
                </div>
            </div>

            <!-- SEARCH SUBMIT BUTTON -->
            <div class="p-1.5 flex items-center justify-end">
                <button type="submit" class="w-full md:w-auto px-7 py-3.5 rounded-xl md:rounded-full bg-brass hover:brightness-105 text-forest font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition cursor-pointer whitespace-nowrap">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- DYNAMIC CATEGORY & AMENITY FILTER BAR -->
<div class="border-b border-forest/10 bg-paper/95 backdrop-blur-md sticky top-14 md:top-20 z-30 shadow-xs space-y-2 py-3 px-4 sm:px-6">
    <div class="mx-auto max-w-[1480px]">
        <!-- 1. CATEGORY PILLS -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
            <span class="text-[9px] uppercase font-bold text-forest/40 tracking-wider shrink-0 mr-1">Style:</span>
            <a href="{{ route('rooms.index', array_merge(request()->query(), ['category' => '', 'category_id' => ''])) }}" 
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 {{ empty($categorySlug) && empty($categoryId) ? 'bg-forest text-paper shadow-xs' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i> All Stays
            </a>
            @foreach($categories as $cat)
            @php $isCatActive = ($categorySlug === $cat->slug) || ((string)$categoryId === (string)$cat->id); @endphp
            <a href="{{ route('rooms.index', array_merge(request()->query(), ['category' => $cat->slug, 'category_id' => $cat->id])) }}" 
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 {{ $isCatActive ? 'bg-forest text-paper shadow-xs' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                <i data-lucide="{{ $cat->icon ?: 'palmtree' }}" class="w-3.5 h-3.5"></i> {{ $cat->name }}
            </a>
            @endforeach
        </div>

        <!-- 2. DYNAMIC FEATURED AMENITIES PILLS -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-1 border-t border-forest/5">
            <span class="text-[9px] uppercase font-bold text-forest/40 tracking-wider shrink-0 mr-1">Feature:</span>
            <a href="{{ route('rooms.index', array_merge(request()->query(), ['amenity' => ''])) }}" 
               class="px-3 py-1 rounded-full text-[11px] font-medium whitespace-nowrap transition {{ empty($amenityFilter) ? 'bg-forest/15 text-forest font-bold' : 'text-forest/60 hover:text-forest' }}">
                Any Feature
            </a>
            @foreach($featuredAmenities as $fam)
            @php $isAmenityActive = ($amenityFilter === $fam->name || $amenityFilter === $fam->slug); @endphp
            <a href="{{ route('rooms.index', array_merge(request()->query(), ['amenity' => $isAmenityActive ? '' : $fam->name])) }}" 
               class="px-3 py-1 rounded-full text-[11px] font-medium whitespace-nowrap transition flex items-center gap-1.5 {{ $isAmenityActive ? 'bg-forest text-paper font-bold shadow-xs' : 'bg-white/60 text-forest/70 hover:bg-white soft-border' }}">
                <i data-lucide="{{ $fam->icon ?: 'sparkles' }}" class="w-3 h-3 text-emerald"></i> {{ $fam->name }}
            </a>
            @endforeach
        </div>
    </div>
</div>

<!-- ROOMS LISTINGS CONTAINER (AIRBNB GRID) -->
<div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="serif text-2xl font-bold text-forest">Available Rooms & Cottages</h2>
            <p class="text-xs text-forest/60 mt-0.5">{{ $roomTypes->count() }} stays found for {{ $nights }} night{{ $nights > 1 ? 's' : '' }} &middot; Transparent pricing, no hidden resort fees</p>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-emerald bg-mint px-3 py-1.5 rounded-xl border border-emerald/20">
            <i data-lucide="shield-check" class="w-4 h-4 text-emerald"></i>
            <span>Krishna Hospitality Guarantee</span>
        </div>
    </div>

    @if($roomTypes->isEmpty())
    <div class="bg-white rounded-[24px] p-12 text-center soft-border shadow-card max-w-lg mx-auto">
        <div class="w-16 h-16 rounded-full bg-mint flex items-center justify-center text-emerald mx-auto mb-4">
            <i data-lucide="bed-double" class="w-8 h-8"></i>
        </div>
        <h3 class="serif text-xl font-bold text-forest">No Rooms Match Your Criteria</h3>
        <p class="text-xs text-forest/60 mt-2">Try relaxing your date filters, guest count, or destination selection to view more available suites.</p>
        <a href="{{ route('rooms.index') }}" class="inline-block mt-5 px-5 py-2.5 rounded-xl bg-forest text-paper font-bold text-xs hover:bg-forest/90 transition">Reset All Filters</a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        @foreach($roomTypes as $room)
        @php
            $estTotal = round($room->base_price * $nights * 1.12);
        @endphp
        <div class="group bg-white rounded-[24px] soft-border overflow-hidden shadow-card lift transition-all duration-300 flex flex-col justify-between">
            <!-- PHOTO SECTION (AIRBNB STYLE) -->
            <div class="img-zoom relative h-64 w-full overflow-hidden bg-mint">
                <img src="{{ $room->cover_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80' }}" 
                     alt="{{ $room->name }}" 
                     class="w-full h-full object-cover">
                
                <!-- BADGES -->
                <div class="absolute top-3 left-3 flex items-center gap-1.5">
                    <span class="bg-forest/90 backdrop-blur-md text-paper text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                        {{ $room->branch ? $room->branch->city : 'Kerala' }}
                    </span>
                    <span class="bg-paper/95 backdrop-blur-md text-forest text-[10px] font-bold px-2.5 py-1 rounded-full shadow-xs">
                        {{ $room->size_sqft ?? 480 }} sq.ft
                    </span>
                </div>

                <div class="absolute top-3 right-3">
                    <button type="button" class="w-8 h-8 rounded-full bg-white/80 backdrop-blur-md flex items-center justify-center text-forest/70 hover:text-rose-600 shadow-xs transition">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Category badge bottom -->
                @if($room->category)
                <div class="absolute bottom-3 left-3 bg-forest/80 backdrop-blur-md text-paper px-2.5 py-1 rounded-lg text-[10px] font-medium flex items-center gap-1.5">
                    <i data-lucide="{{ $room->category->icon ?: 'palmtree' }}" class="w-3 h-3 text-brass"></i>
                    <span>{{ $room->category->name }}</span>
                </div>
                @else
                <div class="absolute bottom-3 left-3 bg-forest/80 backdrop-blur-md text-paper px-2.5 py-1 rounded-lg text-[10px] font-medium flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-3 h-3 text-brass"></i>
                    <span>Verified Cottage</span>
                </div>
                @endif
            </div>

            <!-- DETAILS SECTION -->
            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <!-- Title & Branch -->
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="serif text-xl font-bold text-forest group-hover:text-emerald transition">
                            <a href="{{ route('rooms.show', $room->slug) }}">{{ $room->name }}</a>
                        </h3>
                        <div class="flex items-center gap-1 text-xs font-bold text-forest shrink-0">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                            <span>4.94</span>
                        </div>
                    </div>

                    <p class="text-xs text-forest/55 mt-1">{{ $room->branch ? $room->branch->name : 'Krishna Resort' }} &middot; {{ $room->bed_type ?? 'King Bed' }}</p>

                    <p class="text-xs text-forest/70 line-clamp-2 mt-2 leading-relaxed">
                        {{ $room->short_description ?: 'Crafted with authentic reclaimed teakwood, stone verandas, and sweeping plantation vistas.' }}
                    </p>

                    <!-- AMENITIES TAGS -->
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @if($room->amenitiesList && $room->amenitiesList->count() > 0)
                            @foreach($room->amenitiesList->take(3) as $amenity)
                            <span class="text-[10px] bg-mint text-forest font-semibold px-2 py-0.5 rounded-lg border border-emerald/15 flex items-center gap-1">
                                <i data-lucide="{{ $amenity->icon ?: 'check' }}" class="w-2.5 h-2.5 text-emerald"></i>
                                {{ $amenity->name }}
                            </span>
                            @endforeach
                        @elseif(!empty($room->amenities))
                            @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                            <span class="text-[10px] bg-mint text-forest font-semibold px-2 py-0.5 rounded-lg border border-emerald/15">
                                {{ $amenity }}
                            </span>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- PRICING & CALL TO ACTION (AIRBNB TRANSPARENT STYLE) -->
                <div class="pt-4 mt-4 border-t border-forest/10 flex items-end justify-between">
                    <div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-lg font-bold text-forest">₹{{ number_format($room->base_price) }}</span>
                            <span class="text-xs text-forest/50 font-normal">/ night</span>
                        </div>
                        <div class="text-[11px] text-forest/55 underline decoration-dotted mt-0.5">
                            ₹{{ number_format($estTotal) }} total incl. 12% GST
                        </div>
                    </div>

                    <a href="{{ route('rooms.show', ['slug' => $room->slug, 'check_in' => $checkIn, 'check_out' => $checkOut, 'adults' => $adults, 'children' => $children]) }}" 
                       class="px-4 py-2 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center gap-1.5 shadow-card transition">
                        <span>Reserve</span>
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
    let stayGuestState = {
        adults: {{ (int)$adults }},
        children: {{ (int)$children }}
    };

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
       GUEST PICKER & POPOVER
       ========================================================================== */
    function adjustStayGuestCount(guestType, delta) {
        if (guestType === 'adults') {
            stayGuestState.adults = Math.min(10, Math.max(1, stayGuestState.adults + delta));
        } else if (guestType === 'children') {
            stayGuestState.children = Math.min(6, Math.max(0, stayGuestState.children + delta));
        }
        updateStayGuestUI();
    }

    function updateStayGuestUI() {
        document.getElementById('stay-adults-input').value = stayGuestState.adults;
        document.getElementById('stay-children-input').value = stayGuestState.children;
        document.getElementById('stay-adults-val').textContent = stayGuestState.adults;
        document.getElementById('stay-children-val').textContent = stayGuestState.children;
        document.getElementById('stay-adults-minus').disabled = (stayGuestState.adults <= 1);
        document.getElementById('stay-children-minus').disabled = (stayGuestState.children <= 0);

        const label = `${stayGuestState.adults} Adult${stayGuestState.adults > 1 ? 's' : ''}${stayGuestState.children > 0 ? ', ' + stayGuestState.children + ' Kid' + (stayGuestState.children > 1 ? 's' : '') : ', 0 Kids'}`;
        document.getElementById('stay-guest-label').textContent = label;
    }

    function positionStayGuestPopover() {
        const popover = document.getElementById('stay-guest-popover');
        const container = document.getElementById('stay-guest-picker-container');
        if (!popover || !container) return;

        const rect = container.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        const popoverHeight = 310;

        if (spaceBelow < popoverHeight && rect.top > 250) {
            popover.classList.remove('top-full', 'mt-3');
            popover.classList.add('bottom-full', 'mb-3');
        } else {
            popover.classList.remove('bottom-full', 'mb-3');
            popover.classList.add('top-full', 'mt-3');
        }
    }

    function toggleStayGuestPopover() {
        const popover = document.getElementById('stay-guest-popover');
        const chevron = document.getElementById('stay-guest-chevron');
        if (popover) {
            const isHidden = popover.classList.contains('hidden');
            if (isHidden) {
                positionStayGuestPopover();
                popover.classList.remove('hidden');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            } else {
                popover.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        }
    }

    function closeStayGuestPopover() {
        const popover = document.getElementById('stay-guest-popover');
        const chevron = document.getElementById('stay-guest-chevron');
        if (popover) popover.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }

    document.addEventListener('click', (e) => {
        const container = document.getElementById('stay-guest-picker-container');
        const popover = document.getElementById('stay-guest-popover');
        if (container && !container.contains(e.target) && popover && !popover.classList.contains('hidden')) {
            closeStayGuestPopover();
        }
    });
</script>
@endsection
