@extends('layouts.customer')

@section('title', $roomType->name . ' | Krishna Resorts')

@section('content')
<div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-4 sm:py-6">

    <!-- BREADCRUMBS & TITLE HEADER (AIRBNB STYLE) -->
    <div class="mb-5">
        <div class="flex items-center gap-2 text-xs text-forest/50 mb-2">
            <a href="{{ route('rooms.index') }}" class="hover:underline">Rooms</a>
            <span>/</span>
            <span class="text-forest font-semibold">{{ $roomType->branch ? $roomType->branch->name : 'Kerala' }}</span>
            <span>/</span>
            <span>{{ $roomType->name }}</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="serif text-2xl sm:text-3xl md:text-4xl font-bold text-forest">{{ $roomType->name }}</h1>
                @if($roomType->category)
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-mint text-emerald border border-emerald/20 flex items-center gap-1 shadow-xs">
                    <i data-lucide="{{ $roomType->category->icon ?: 'palmtree' }}" class="w-3.5 h-3.5"></i>
                    {{ $roomType->category->name }}
                </span>
                @endif
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold text-forest">
                <div class="flex items-center gap-1">
                    <i data-lucide="star" class="w-4 h-4 fill-brass text-brass"></i>
                    <span>{{ number_format($averageRating, 2) }}</span>
                    <span class="text-forest/30">&middot;</span>
                    <a href="#reviews" class="underline hover:text-emerald transition">{{ $reviewsCount }} reviews</a>
                </div>
                <div class="flex items-center gap-1 text-forest/60">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald"></i>
                    <span class="underline">{{ $roomType->branch ? $roomType->branch->city : 'Kerala' }}, India</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 5-PHOTO HERO COLLAGE (AIRBNB SIGNATURE FORMAT) -->
    @php
        $mainPhoto = $roomType->cover_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';
        $photos = !empty($roomType->gallery_images) ? $roomType->gallery_images : [
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1507652313519-d4e9174996dd?auto=format&fit=crop&w=800&q=80'
        ];
    @endphp
    <div class="relative rounded-[26px] overflow-hidden soft-border shadow-card mb-6 sm:mb-8 grid grid-cols-1 md:grid-cols-4 gap-2 h-60 sm:h-80 md:h-[420px] bg-mint">
        <!-- MAIN SHOWCASE PHOTO (LEFT 2 COLS) -->
        <div class="md:col-span-2 h-full overflow-hidden cursor-pointer group relative img-zoom" onclick="document.getElementById('gallery-modal').classList.remove('hidden')">
            <img src="{{ $mainPhoto }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
        </div>

        <!-- 4 GRID PHOTOS (RIGHT 2 COLS) -->
        <div class="hidden md:grid col-span-2 grid-cols-2 gap-2 h-full">
            @for($i = 0; $i < 4; $i++)
            <div class="h-full overflow-hidden cursor-pointer group relative bg-mint img-zoom" onclick="document.getElementById('gallery-modal').classList.remove('hidden')">
                <img src="{{ $photos[$i] ?? $mainPhoto }}" alt="{{ $roomType->name }} photo {{ $i+1 }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
            </div>
            @endfor
        </div>

        <!-- "SHOW ALL PHOTOS" BUTTON -->
        <button onclick="document.getElementById('gallery-modal').classList.remove('hidden')" class="absolute bottom-4 right-4 bg-paper/95 backdrop-blur-md hover:bg-white text-forest px-3.5 py-2 rounded-xl text-xs font-bold soft-border shadow-card flex items-center gap-1.5 transition">
            <i data-lucide="grid-2x2" class="w-4 h-4 text-emerald"></i>
            <span>Show all photos</span>
        </button>
    </div>

    <!-- MAIN TWO-COLUMN CONTENT & STICKY RESERVATION WIDGET -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

        <!-- LEFT COLUMN: ROOM DETAILS, AMENITIES, HOST, CALENDAR, REVIEWS -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-8">
            <!-- ROOM HIGHLIGHTS & SPECS -->
            <div class="border-b border-forest/10 pb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="serif text-xl sm:text-2xl font-bold text-forest">
                            {{ $roomType->category ? $roomType->category->name : 'Private Cottage' }} in {{ $roomType->branch ? $roomType->branch->name : 'Kerala' }}
                        </h2>
                        <p class="text-xs text-forest/60 mt-1">
                            {{ $roomType->max_guests ?? 2 }} guests &middot; {{ $roomType->bed_type ?? 'King Bed' }} &middot; 1 private herbal bath &middot; {{ $roomType->size_sqft ?? 480 }} sq.ft
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-forest text-brass flex items-center justify-center font-bold text-lg shadow-card">
                        KR
                    </div>
                </div>

                <!-- AIRBNB TOP COMMITMENTS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-forest/5">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-mint flex items-center justify-center text-emerald shrink-0">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-forest">Dedicated View Deck</h4>
                            <p class="text-[11px] text-forest/55 mt-0.5">Private veranda overlooking dense plantation hills.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-mint flex items-center justify-center text-emerald shrink-0">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-forest">Hygiene Certified</h4>
                            <p class="text-[11px] text-forest/55 mt-0.5">Sanitized according to Ayurvedic slow-living hospitality standards.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-mint flex items-center justify-center text-emerald shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-forest">Free Cancellation</h4>
                            <p class="text-[11px] text-forest/55 mt-0.5">Cancel up to 48 hours before check-in for a full refund.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROOM STORY & DESCRIPTION -->
            <div class="border-b border-forest/10 pb-6 text-forest/80 text-xs sm:text-sm leading-relaxed space-y-3">
                <h3 class="serif text-xl font-bold text-forest">About this slow-living space</h3>
                <p>
                    {{ $roomType->description ?: ($roomType->short_description ?: 'Tucked into the lush slopes of our Kerala resort, this retreat features reclaimed hardwood framing, hand-chiseled stone bathrooms, and broad private balconies bathed in morning mist.') }}
                </p>
            </div>

            <!-- SLEEPING ARRANGEMENTS -->
            <div class="border-b border-forest/10 pb-6">
                <h3 class="serif text-xl font-bold text-forest mb-4">Where you'll sleep</h3>
                <div class="p-5 rounded-2xl bg-white soft-border shadow-xs max-w-sm">
                    <i data-lucide="bed" class="w-6 h-6 text-emerald mb-2"></i>
                    <h4 class="font-bold text-xs text-forest">Bedroom Suite</h4>
                    <p class="text-xs text-forest/55 mt-1">{{ $roomType->bed_type ?? '1 King Bed' }} &middot; Luxury Cotton Linens</p>
                </div>
            </div>

            <!-- AMENITIES (DYNAMIC AIRBNB GRID) -->
            <div class="border-b border-forest/10 pb-6">
                <h3 class="serif text-xl font-bold text-forest mb-4">What this place offers</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    @if($roomType->amenitiesList && $roomType->amenitiesList->count() > 0)
                        @foreach($roomType->amenitiesList as $am)
                        <div class="flex items-center gap-3 text-forest/80">
                            <i data-lucide="{{ $am->icon ?: 'sparkles' }}" class="w-4 h-4 text-emerald shrink-0"></i>
                            <div>
                                <span class="font-semibold">{{ $am->name }}</span>
                                @if($am->description)
                                    <p class="text-[10px] text-forest/50">{{ $am->description }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @elseif(!empty($roomType->amenities))
                        @foreach($roomType->amenities as $amName)
                        <div class="flex items-center gap-3 text-forest/80">
                            <i data-lucide="check" class="w-4 h-4 text-emerald shrink-0"></i>
                            <span>{{ $amName }}</span>
                        </div>
                        @endforeach
                    @else
                        <div class="flex items-center gap-3 text-forest/80">
                            <i data-lucide="trees" class="w-4 h-4 text-emerald"></i>
                            <span>Forest Canopy Views</span>
                        </div>
                        <div class="flex items-center gap-3 text-forest/80">
                            <i data-lucide="wifi" class="w-4 h-4 text-emerald"></i>
                            <span>High-Speed Wi-Fi</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- REVIEWS SECTION (AIRBNB STYLE) -->
            <div id="reviews" class="pt-4 border-t border-forest/10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1 serif text-2xl font-bold text-forest">
                            <i data-lucide="star" class="w-5 h-5 fill-brass text-brass"></i>
                            <span>{{ number_format($averageRating, 2) }}</span>
                        </div>
                        <span class="text-forest/30 text-sm">&middot;</span>
                        <span class="serif text-xl font-bold text-forest">{{ $reviewsCount }} verified reviews</span>
                    </div>

                    <div>
                        @if($canWriteReview && $eligibleReservation)
                            <button onclick="document.getElementById('modal-write-review').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-card flex items-center gap-2 transition cursor-pointer">
                                <i data-lucide="pen-tool" class="w-3.5 h-3.5 text-brass"></i>
                                <span>Write a Review</span>
                            </button>
                        @elseif(Auth::check())
                            <span class="text-[11px] text-forest/60 italic bg-mint/50 px-3 py-1.5 rounded-lg border border-emerald/10">
                                🌿 Reviews unlock after check-in or stay completion.
                            </span>
                        @else
                            <a href="{{ route('login') }}" class="text-xs text-forest font-semibold underline hover:text-emerald">
                                Sign in to write a verified review
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($reviews as $rev)
                    @php
                        $guestName = $rev->guest ? $rev->guest->full_name : 'Verified In-House Guest';
                    @endphp
                    <div class="bg-white rounded-2xl p-4.5 soft-border shadow-xs space-y-2.5 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-mint text-emerald font-bold text-xs flex items-center justify-center">
                                        {{ substr($guestName, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-forest">{{ $guestName }}</h4>
                                        <span class="text-[10px] text-forest/45">{{ $rev->created_at ? $rev->created_at->format('M Y') : 'Recent' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-mint text-emerald border border-emerald/20 flex items-center gap-1">
                                        <i data-lucide="shield-check" class="w-2.5 h-2.5"></i> Verified Stay
                                    </span>
                                    <div class="flex items-center text-brass text-[10px]">
                                        @for($s = 0; $s < ($rev->rating ?? 5); $s++)
                                            <i data-lucide="star" class="w-3 h-3 fill-brass"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            @if($rev->title)
                                <h5 class="text-xs font-bold text-forest">{{ $rev->title }}</h5>
                            @endif

                            <p class="text-xs text-forest/75 leading-relaxed">
                                "{{ $rev->comment }}"
                            </p>
                        </div>

                        <!-- Resort Manager Response if available -->
                        @if($rev->staff_reply)
                        <div class="mt-2 p-3 rounded-xl bg-paper/70 border-l-2 border-emerald space-y-1 text-xs">
                            <div class="flex items-center gap-1.5 text-emerald font-bold text-[10px]">
                                <i data-lucide="message-square" class="w-3 h-3"></i>
                                <span>Resort Manager Response</span>
                            </div>
                            <p class="text-forest/70 italic text-[11px] leading-relaxed">{{ $rev->staff_reply }}</p>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="col-span-2 p-8 bg-white rounded-2xl soft-border text-center text-xs text-forest/50">
                        No reviews published yet for this cottage. Be among the first to experience this slow-living haven.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: STICKY FLOATING RESERVATION WIDGET (AIRBNB SIGNATURE) -->
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="sticky top-20 md:top-24 bg-white rounded-[26px] soft-border shadow-card p-5 sm:p-6 space-y-5">
                <!-- PRICE HEADER -->
                <div class="flex items-baseline justify-between border-b border-forest/10 pb-4">
                    <div>
                        <span class="serif text-2xl font-bold text-forest">₹{{ number_format($roomType->base_price) }}</span>
                        <span class="text-xs text-forest/50 font-normal">/ night</span>
                    </div>
                    <div class="flex items-center gap-1 text-xs font-bold text-forest">
                        <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                        <span>{{ number_format($averageRating, 1) }}</span>
                        <span class="text-forest/30">&middot;</span>
                        <span class="text-forest/60 underline">{{ $reviewsCount }}</span>
                    </div>
                </div>

                <!-- DATE & GUEST BOX (AIRBNB STYLE) -->
                <form action="{{ route('booking.checkout', $roomType->id) }}" method="GET" class="space-y-4">
                    <div class="soft-border rounded-2xl overflow-hidden divide-y divide-forest/10 bg-paper/50">
                        <div class="grid grid-cols-2 divide-x divide-forest/10">
                            <div class="p-2.5">
                                <label class="block text-[9px] uppercase font-bold text-forest/50">Check-in</label>
                                <input type="date" name="check_in" value="{{ $checkIn }}" min="{{ date('Y-m-d') }}" class="w-full bg-transparent text-xs font-semibold focus:outline-hidden text-forest">
                            </div>
                            <div class="p-2.5">
                                <label class="block text-[9px] uppercase font-bold text-forest/50">Check-out</label>
                                <input type="date" name="check_out" value="{{ $checkOut }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full bg-transparent text-xs font-semibold focus:outline-hidden text-forest">
                            </div>
                        </div>
                        <div class="p-2.5 space-y-2">
                            <label class="block text-[9px] uppercase font-bold text-forest/50">Guests & Occupancy</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[8px] uppercase font-semibold text-forest/40">Adults (13+)</label>
                                    <select name="adults" class="w-full bg-transparent text-xs font-bold text-forest focus:outline-hidden cursor-pointer">
                                        @for($i = 1; $i <= max(2, ($roomType->max_adults ?: 4)); $i++)
                                            <option value="{{ $i }}" {{ (int)$adults === $i ? 'selected' : '' }}>{{ $i }} Adult{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[8px] uppercase font-semibold text-forest/40">Children (0-12)</label>
                                    <select name="children" class="w-full bg-transparent text-xs font-bold text-forest focus:outline-hidden cursor-pointer">
                                        @for($c = 0; $c <= max(1, ($roomType->max_children ?: 2)); $c++)
                                            <option value="{{ $c }}" {{ (int)$children === $c ? 'selected' : '' }}>{{ $c }} {{ $c === 1 ? 'Child' : 'Children' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="text-[10px] text-forest/55 pt-1 border-t border-forest/5 flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3 text-emerald shrink-0"></i>
                                <span>Max {{ $roomType->max_guests }} guests (Up to {{ $roomType->max_adults }} adults)</span>
                            </div>
                        </div>
                    </div>

                    <!-- CTA BUTTON -->
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs tracking-wide shadow-card transition">
                        Reserve Room
                    </button>

                    <p class="text-center text-[11px] text-forest/50">
                        You won't be charged yet &middot; Instant confirmation
                    </p>

                    <!-- ITEMIZED PRICING BREAKDOWN (AIRBNB TRANSPARENT STYLE) -->
                    @php
                        $sub = $roomType->base_price * $nights;
                        $tax = round($sub * 0.12, 2);
                        $tot = $sub + $tax;
                        $advance = round($tot * 0.20, 2);
                    @endphp
                    <div class="pt-4 border-t border-forest/10 space-y-2 text-xs text-forest/70">
                        <div class="flex justify-between">
                            <span class="underline">₹{{ number_format($roomType->base_price) }} &times; {{ $nights }} night{{ $nights > 1 ? 's' : '' }}</span>
                            <span class="font-semibold text-forest">₹{{ number_format($sub) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="underline">Hospitality & Sanitization Fee</span>
                            <span class="text-emerald font-semibold">Free</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="underline">GST Tax (12%)</span>
                            <span class="font-semibold text-forest">₹{{ number_format($tax) }}</span>
                        </div>
                        <div class="pt-3 border-t border-forest/10 flex justify-between font-bold text-sm text-forest">
                            <span>Total before check-in</span>
                            <span>₹{{ number_format($tot) }}</span>
                        </div>
                        <div class="bg-mint p-3 rounded-xl text-[11px] text-forest border border-emerald/15 mt-2">
                            <span class="font-bold text-emerald">Flexible Payment:</span> Pay full amount now, or pay <strong>₹{{ number_format($advance) }} (20%)</strong> deposit today and balance upon arrival.
                        </div>
                    </div>
                </form>
            </div>

            @php
                $showBranchPhone = $roomType->branch?->phone ?? \App\Models\Setting::get('resort_phone', '+91 94471 22334');
                $cleanShowBranchPhone = preg_replace('/[^0-9+]/', '', $showBranchPhone);
            @endphp
            @if($showBranchPhone)
            <div class="mt-4 p-4 rounded-2xl bg-paper/70 soft-border text-center space-y-1.5 shadow-xs">
                <span class="text-[10px] uppercase font-bold text-forest/50 tracking-wider block">Questions about this cottage?</span>
                <a href="tel:{{ $cleanShowBranchPhone }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white soft-border text-forest text-xs font-bold hover:bg-forest hover:text-paper transition shadow-xs">
                    <i data-lucide="phone-call" class="w-3.5 h-3.5 text-emerald"></i>
                    <span>Call {{ $roomType->branch ? $roomType->branch->name : 'Desk' }}: {{ $showBranchPhone }}</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- FULLSCREEN PHOTO GALLERY MODAL -->
<div id="gallery-modal" class="hidden fixed inset-0 z-[100] bg-black/95 overflow-y-auto p-4 sm:p-8">
    <div class="max-w-5xl mx-auto relative text-white">
        <button onclick="document.getElementById('gallery-modal').classList.add('hidden')" class="fixed top-6 right-6 text-white bg-white/10 hover:bg-white/20 p-2.5 rounded-full z-50">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <h3 class="serif text-2xl font-bold mb-6 text-white">{{ $roomType->name }} &mdash; Photo Gallery</h3>
        <div class="space-y-6">
            <img src="{{ $mainPhoto }}" class="w-full rounded-2xl object-cover">
            @foreach($photos as $img)
                <img src="{{ $img }}" class="w-full rounded-2xl object-cover">
            @endforeach
        </div>
    </div>
</div>

<!-- WRITE GUEST REVIEW MODAL (AIRBNB STYLE VERIFIED REVIEWS) -->
@if($canWriteReview && $eligibleReservation)
<div id="modal-write-review" class="hidden fixed inset-0 z-[110] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-paper rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-forest/15 animate-fadeIn text-forest">
        <div class="bg-forest text-paper px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="star" class="w-4 h-4 text-brass fill-brass"></i>
                <h3 class="font-bold text-sm">Write Verified Stay Review</h3>
            </div>
            <button onclick="document.getElementById('modal-write-review').classList.add('hidden')" class="text-paper/60 hover:text-paper p-1 cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-submit-review" onsubmit="handleReviewSubmit(event)" class="p-5 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" name="reservation_id" value="{{ $eligibleReservation->id }}">

            <!-- Stay verification badge -->
            <div class="p-3 bg-mint/60 rounded-xl border border-emerald/20 flex items-center justify-between">
                <div>
                    <span class="font-bold text-emerald block text-xs">Verified Booking #{{ $eligibleReservation->booking_code }}</span>
                    <span class="text-[11px] text-forest/60">{{ $roomType->name }} &middot; {{ $roomType->branch ? $roomType->branch->name : 'Kerala' }}</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                    {{ ucfirst($eligibleReservation->status) }}
                </span>
            </div>

            <!-- Star Rating Selection -->
            <div>
                <label class="block text-xs font-bold text-forest mb-2">Overall Rating *</label>
                <div class="flex items-center gap-3">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="flex flex-col items-center gap-1 cursor-pointer group">
                        <input type="radio" name="rating" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center border border-forest/20 group-hover:border-forest peer-checked:bg-forest peer-checked:text-brass transition">
                            <i data-lucide="star" class="w-5 h-5 group-hover:scale-110 transition"></i>
                        </div>
                        <span class="text-[10px] font-semibold text-forest/70 peer-checked:text-forest peer-checked:font-bold">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</span>
                    </label>
                    @endfor
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-forest mb-1">Review Headline / Title</label>
                <input type="text" name="title" placeholder="e.g. Unforgettable misty morning in the plantation suite" class="w-full px-3 py-2 rounded-xl border border-forest/20 bg-white text-forest focus:outline-hidden focus:ring-1 focus:ring-forest text-xs">
            </div>

            <div>
                <label class="block text-xs font-bold text-forest mb-1">Your Detailed Experience *</label>
                <textarea name="comment" required rows="4" placeholder="Tell future guests about the serene atmosphere, heritage room comforts, views, and resort hospitality..." class="w-full px-3 py-2 rounded-xl border border-forest/20 bg-white text-forest focus:outline-hidden focus:ring-1 focus:ring-forest text-xs"></textarea>
            </div>

            <p class="text-[11px] text-forest/50 leading-relaxed">
                ℹ️ To maintain authentic trust, guest reviews are verified and displayed once approved by our resort management team.
            </p>

            <div class="pt-3 border-t border-forest/10 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-write-review').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-forest/20 text-forest/70 hover:bg-white font-semibold text-xs cursor-pointer">Cancel</button>
                <button type="submit" id="submit-review-btn" class="px-5 py-2 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-card transition cursor-pointer">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
async function handleReviewSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-review-btn');
    btn.disabled = true;
    btn.textContent = 'Submitting...';

    const form = e.target;
    const formData = new FormData(form);

    try {
        const res = await fetch('{{ route('reviews.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            alert(data.message);
            document.getElementById('modal-write-review').classList.add('hidden');
            window.location.reload();
        } else {
            alert(data.message || 'Error submitting review');
            btn.disabled = false;
            btn.textContent = 'Submit Review';
        }
    } catch(err) {
        alert('Network error submitting review');
        btn.disabled = false;
        btn.textContent = 'Submit Review';
    }
}
</script>
@endif

<!-- MOBILE STICKY BOTTOM BAR (< 768px AIRBNB STYLE) -->
<div class="md:hidden fixed bottom-16 left-3 right-3 z-40 bg-paper/95 backdrop-blur-md soft-border rounded-2xl px-4 py-2.5 flex items-center justify-between shadow-card">
    <div>
        <div class="flex items-baseline gap-1">
            <span class="font-bold text-base text-forest">₹{{ number_format($roomType->base_price) }}</span>
            <span class="text-xs text-forest/50">/ night</span>
        </div>
        <div class="text-[10px] text-emerald font-semibold">{{ $nights }} night stay &middot; Total ₹{{ number_format($tot) }}</div>
    </div>
    <a href="{{ route('booking.checkout', ['id' => $roomType->id, 'check_in' => $checkIn, 'check_out' => $checkOut, 'adults' => $adults, 'children' => $children]) }}" 
       class="px-5 py-2.5 rounded-xl bg-forest text-paper text-xs font-bold shadow-card hover:bg-emerald transition">
        Reserve
    </a>
</div>
@endsection
