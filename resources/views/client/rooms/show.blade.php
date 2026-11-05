@extends('layouts.customer')

@section('title', $roomType->name . ' — Luxury Cottage in ' . ($roomType->branch?->city ?? 'Kerala') . ' | Country Side Cottages')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($roomType->short_description ?: $roomType->description), 155))
@section('og_image', $roomType->cover_image_url ?: asset('uploads/homepage/krishna_1788796146_NLXh47tR.jpg'))
@section('og_title', $roomType->name . ' — Country Side Cottages')
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($roomType->short_description ?: $roomType->description), 155))
@section('hide_mobile_nav', true)

@push('schema')
<!-- BREADCRUMBS SCHEMA -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "Home", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Stays", "item": "{{ route('rooms.index') }}" },
        { "@@type": "ListItem", "position": 3, "name": "{{ addslashes($roomType->name) }}", "item": "{{ url()->current() }}" }
    ]
}
</script>

<!-- HOTEL ROOM SCHEMA -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "HotelRoom",
    "name": "{{ addslashes($roomType->name) }}",
    "description": "{{ addslashes(strip_tags($roomType->description ?: $roomType->short_description)) }}",
    "image": "{{ $roomType->cover_image_url ?: asset('uploads/homepage/krishna_1788796146_NLXh47tR.jpg') }}",
    "bed": {
        "@@type": "BedDetails",
        "typeOfBed": "{{ $roomType->bed_type ?: 'King Bed' }}"
    },
    "occupancy": {
        "@@type": "QuantitativeValue",
        "maxValue": {{ (int)($roomType->max_guests ?: 2) }}
    },
    "offers": {
        "@@type": "Offer",
        "priceCurrency": "INR",
        "price": "{{ $roomType->base_price }}",
        "availability": "https://schema.org/InStock",
        "url": "{{ url()->current() }}"
    }
    @if(isset($reviewsCount) && $reviewsCount > 0)
    ,
    "aggregateRating": {
        "@@type": "AggregateRating",
        "ratingValue": "{{ number_format($averageRating, 1) }}",
        "reviewCount": "{{ $reviewsCount }}"
    }
    @endif
}
</script>
@endpush

@section('content')
<div class="mx-auto max-w-[1480px] px-4 sm:px-6 pt-4 pb-24 sm:py-6">

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
                    @if($reviewsCount > 0)
                        <span>{{ number_format($averageRating, 2) }}</span>
                        <span class="text-forest/30">&middot;</span>
                        <a href="#reviews" class="underline hover:text-emerald transition">{{ $reviewsCount }} review{{ $reviewsCount > 1 ? 's' : '' }}</a>
                    @else
                        <span>New</span>
                        <span class="text-forest/30">&middot;</span>
                        <span class="text-forest/60">Verified Cottage</span>
                    @endif
                </div>
                <div class="flex items-center gap-1 text-forest/60">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald"></i>
                    <span class="underline">{{ $roomType->branch ? $roomType->branch->city : 'Kerala' }}, India</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DYNAMIC ROOM PHOTO GALLERY / HERO COLLAGE -->
    @php
        $allPhotos = $roomType->all_photos;
        $totalPhotosCount = count($allPhotos);
        $mainPhoto = $allPhotos[0] ?? null;
        $extraPhotos = array_slice($allPhotos, 1);
    @endphp

    @if($totalPhotosCount === 0)
    <!-- ZERO PHOTOS PLACEHOLDER (NO MOCK STOCK IMAGES) -->
    <div class="relative w-full max-w-7xl mx-auto rounded-[24px] sm:rounded-[32px] overflow-hidden soft-border shadow-card mb-6 sm:mb-8 h-64 sm:h-80 md:h-96 bg-mint/50 flex flex-col items-center justify-center text-center p-6">
        <div class="w-16 h-16 rounded-2xl bg-white soft-border shadow-xs flex items-center justify-center text-emerald mb-3">
            <i data-lucide="bed-double" class="w-8 h-8"></i>
        </div>
        <h3 class="serif text-xl font-bold text-forest">{{ $roomType->name }}</h3>
        <p class="text-xs text-forest/60 mt-1">{{ $roomType->branch ? $roomType->branch->name : 'Country Side Cottages' }} &middot; {{ $roomType->category ? $roomType->category->name : 'Private Suite' }}</p>
    </div>
    @elseif($totalPhotosCount === 1)
    {{-- 1 PHOTO: ELEGANT PANORAMIC BANNER --}}
    <div class="relative w-full max-w-7xl mx-auto rounded-[24px] sm:rounded-[32px] overflow-hidden soft-border shadow-card mb-6 sm:mb-8 bg-mint h-80 sm:h-96 md:h-[480px] lg:h-[540px]">
        <div class="w-full h-full cursor-pointer group relative" onclick="openPhotoLightbox(0)">
            <img src="{{ $mainPhoto }}" alt="{{ $roomType->name }}" 
                 class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-[1.02]">
            <div class="absolute inset-0 bg-black/5 group-hover:bg-black/0 transition pointer-events-none"></div>
        </div>
        <button type="button" onclick="openPhotoLightbox(0)" 
                class="absolute bottom-3 right-3 sm:bottom-5 sm:right-5 z-20 bg-paper/95 backdrop-blur-md hover:bg-white text-forest px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs sm:text-sm font-bold soft-border shadow-card flex items-center gap-2 transition cursor-pointer">
            <i data-lucide="maximize-2" class="w-4 h-4 text-emerald"></i>
            <span>View Full Photo</span>
        </button>
    </div>
    @elseif($totalPhotosCount >= 2 && $totalPhotosCount < 4)
    <!-- 2 OR 3 PHOTOS: BALANCED PANORAMIC LAYOUT -->
    <div class="relative w-full max-w-7xl mx-auto rounded-[24px] sm:rounded-[32px] overflow-hidden soft-border shadow-card mb-6 sm:mb-8 grid grid-cols-1 md:grid-cols-2 gap-2 sm:gap-2.5 h-80 sm:h-96 md:h-[480px] lg:h-[540px] bg-mint">
        <!-- Main Photo -->
        <div class="h-full overflow-hidden cursor-pointer group relative img-zoom" onclick="openPhotoLightbox(0)">
            <img src="{{ $mainPhoto }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
            <!-- Mobile Photo Badge -->
            <div class="md:hidden absolute top-3 left-3 bg-black/60 backdrop-blur-md text-white text-[11px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                <i data-lucide="image" class="w-3 h-3 text-emerald"></i>
                <span>1 / {{ $totalPhotosCount }}</span>
            </div>
        </div>
        <!-- Secondary Photos -->
        <div class="grid {{ count($extraPhotos) > 1 ? 'grid-rows-2' : 'grid-rows-1' }} gap-2 sm:gap-2.5 h-full">
            @foreach($extraPhotos as $idx => $extraImg)
            <div class="h-full overflow-hidden cursor-pointer group relative bg-mint img-zoom" onclick="openPhotoLightbox({{ $idx + 1 }})">
                <img src="{{ $extraImg }}" alt="{{ $roomType->name }} photo {{ $idx + 2 }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
            </div>
            @endforeach
        </div>
        <button type="button" onclick="openPhotoLightbox(0)" class="absolute bottom-3 right-3 sm:bottom-5 sm:right-5 bg-paper/95 backdrop-blur-md hover:bg-white text-forest px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs sm:text-sm font-bold soft-border shadow-card flex items-center gap-2 transition cursor-pointer z-20">
            <i data-lucide="grid-2x2" class="w-4 h-4 text-emerald"></i>
            <span>All photos ({{ $totalPhotosCount }})</span>
        </button>
    </div>
    @else
    <!-- 4+ PHOTOS: SIGNATURE 5-PHOTO MOSAIC -->
    <div class="relative w-full max-w-7xl mx-auto rounded-[24px] sm:rounded-[32px] overflow-hidden soft-border shadow-card mb-6 sm:mb-8 grid grid-cols-1 md:grid-cols-4 gap-2 sm:gap-2.5 h-80 sm:h-96 md:h-[480px] lg:h-[540px] xl:h-[600px] bg-mint">
        <!-- Main Showcase Photo (Left 2 cols on md+, full width on mobile) -->
        <div class="md:col-span-2 h-full overflow-hidden cursor-pointer group relative img-zoom" onclick="openPhotoLightbox(0)">
            <img src="{{ $mainPhoto }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
            <!-- Mobile Photo Count Badge -->
            <div class="md:hidden absolute top-3 left-3 bg-black/60 backdrop-blur-md text-white text-[11px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                <i data-lucide="image" class="w-3 h-3 text-emerald"></i>
                <span>1 / {{ $totalPhotosCount }}</span>
            </div>
        </div>

        <!-- 4 Grid Photos (Right 2 cols on md+, hidden on mobile so main photo gets full height) -->
        <div class="hidden md:grid col-span-2 grid-cols-2 gap-2 sm:gap-2.5 h-full">
            @for($i = 0; $i < 4; $i++)
            @if(isset($extraPhotos[$i]))
            <div class="h-full overflow-hidden cursor-pointer group relative bg-mint img-zoom" onclick="openPhotoLightbox({{ $i + 1 }})">
                <img src="{{ $extraPhotos[$i] }}" alt="{{ $roomType->name }} photo {{ $i+2 }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
            </div>
            @else
            <div class="h-full overflow-hidden cursor-pointer group relative bg-mint img-zoom" onclick="openPhotoLightbox(0)">
                <img src="{{ $mainPhoto }}" alt="{{ $roomType->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
            </div>
            @endif
            @endfor
        </div>

        <!-- "SHOW ALL PHOTOS" BUTTON -->
        <button type="button" onclick="openPhotoLightbox(0)" class="absolute bottom-3 right-3 sm:bottom-5 sm:right-5 bg-paper/95 backdrop-blur-md hover:bg-white text-forest px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs sm:text-sm font-bold soft-border shadow-card flex items-center gap-2 transition cursor-pointer z-20">
            <i data-lucide="grid-2x2" class="w-4 h-4 text-emerald"></i>
            <span>Show all photos ({{ $totalPhotosCount }})</span>
        </button>
    </div>
    @endif

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
                            {{ $roomType->max_guests ?? 2 }} guests &middot; {{ $roomType->bed_type ?? 'King Bed' }} &middot; Private en-suite bath{{ $roomType->size_sqft ? ' &middot; ' . $roomType->size_sqft . ' sq.ft' : '' }}
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
                            <p class="text-[11px] text-forest/55 mt-0.5">Sanitized according to signature slow-living cottage hospitality standards.</p>
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
                    {{ $roomType->description ?: ($roomType->short_description ?: 'Tucked into the lush slopes of our Kerala cottages, this retreat features reclaimed hardwood framing, hand-chiseled stone bathrooms, and broad private balconies bathed in morning mist.') }}
                </p>
            </div>

            <!-- SLEEPING ARRANGEMENTS -->
            <div class="border-b border-forest/10 pb-6">
                <h3 class="serif text-xl font-bold text-forest mb-4">Where you'll sleep</h3>
                <div class="p-5 rounded-2xl bg-white soft-border shadow-xs max-w-sm">
                    <i data-lucide="bed" class="w-6 h-6 text-emerald mb-2"></i>
                    <h4 class="font-bold text-xs text-forest">Bedroom Suite</h4>
                    <p class="text-xs text-forest/55 mt-1">{{ $roomType->bed_type ?? '1 King Bed' }} &middot; Premium Cotton Linens</p>
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
                        @if($reviewsCount > 0)
                            <div class="flex items-center gap-1 serif text-2xl font-bold text-forest">
                                <i data-lucide="star" class="w-5 h-5 fill-brass text-brass"></i>
                                <span>{{ number_format($averageRating, 2) }}</span>
                            </div>
                            <span class="text-forest/30 text-sm">&middot;</span>
                            <span class="serif text-xl font-bold text-forest">{{ $reviewsCount }} verified review{{ $reviewsCount > 1 ? 's' : '' }}</span>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="serif text-xl font-bold text-forest">Guest Reviews</span>
                                <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-mint text-emerald font-bold border border-emerald/20">New Suite</span>
                            </div>
                        @endif
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
                                <span>Cottage Manager Response</span>
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
                        <span class="text-xs text-forest/50 font-normal">/ night (1 room)</span>
                        @php
                            $totalPhysicalRooms = $roomType->rooms ? $roomType->rooms->count() : 1;
                        @endphp
                        @if($totalPhysicalRooms > 1)
                            <span class="text-xs text-forest/65 block font-medium mt-0.5">
                                Entire Villa ({{ $totalPhysicalRooms }} rooms): ₹{{ number_format($roomType->base_price * $totalPhysicalRooms) }} / night
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 text-xs font-bold text-forest">
                        <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                        @if($reviewsCount > 0)
                            <span>{{ number_format($averageRating, 1) }}</span>
                            <span class="text-forest/30">&middot;</span>
                            <span class="text-forest/60 underline">{{ $reviewsCount }}</span>
                        @else
                            <span class="text-emerald">New</span>
                        @endif
                    </div>
                </div>

                <!-- DATE & GUEST BOX (AIRBNB SIGNATURE STAYS STYLE) -->
                <form action="{{ route('booking.checkout', $roomType->id) }}" method="GET" class="space-y-4">
                    <div class="soft-border rounded-2xl divide-y divide-forest/10 bg-paper/50 relative">
                        <!-- Date Pickers Grid -->
                        <div class="grid grid-cols-2 divide-x divide-forest/10 rounded-t-2xl">
                            <!-- Check-in Subcard -->
                            <div class="p-3 hover:bg-forest/[0.03] transition-colors cursor-pointer group relative" onclick="openDateCalendar('detail-check-in-input')">
                                <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3 h-3 text-emerald shrink-0"></i>
                                    <span>Check-in</span>
                                </label>
                                <div class="flex items-center justify-between mt-1">
                                    <span id="detail-check-in-display" class="text-xs sm:text-sm font-semibold text-forest truncate">
                                        {{ !empty($checkIn) ? date('D, d M', strtotime($checkIn)) : date('D, d M', strtotime('+1 day')) }}
                                    </span>
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-forest/30 group-hover:text-emerald transition shrink-0 ml-1"></i>
                                </div>
                                <input type="date" 
                                       name="check_in" 
                                       id="detail-check-in-input" 
                                       value="{{ !empty($checkIn) ? $checkIn : date('Y-m-d', strtotime('+1 day')) }}" 
                                       min="{{ date('Y-m-d') }}" 
                                       onchange="updateDetailDateDisplay('detail-check-in-input', 'detail-check-in-display'); onDetailDateChanged();" 
                                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
                            </div>

                            <!-- Check-out Subcard -->
                            <div class="p-3 hover:bg-forest/[0.03] transition-colors cursor-pointer group relative" onclick="openDateCalendar('detail-check-out-input')">
                                <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                                    <i data-lucide="calendar-check-2" class="w-3 h-3 text-emerald shrink-0"></i>
                                    <span>Check-out</span>
                                </label>
                                <div class="flex items-center justify-between mt-1">
                                    <span id="detail-check-out-display" class="text-xs sm:text-sm font-semibold text-forest truncate">
                                        {{ !empty($checkOut) ? date('D, d M', strtotime($checkOut)) : date('D, d M', strtotime('+3 days')) }}
                                    </span>
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-forest/30 group-hover:text-emerald transition shrink-0 ml-1"></i>
                                </div>
                                <input type="date" 
                                       name="check_out" 
                                       id="detail-check-out-input" 
                                       value="{{ !empty($checkOut) ? $checkOut : date('Y-m-d', strtotime('+3 days')) }}" 
                                       min="{{ !empty($checkIn) ? date('Y-m-d', strtotime($checkIn . ' +1 day')) : date('Y-m-d', strtotime('+2 days')) }}" 
                                       onchange="updateDetailDateDisplay('detail-check-out-input', 'detail-check-out-display'); onDetailDateChanged();" 
                                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
                            </div>
                        </div>

                        <!-- ROOMS & GUESTS (STAYS PAGE POPOVER STEPPER) -->
                        <div class="relative p-3 hover:bg-forest/[0.03] transition cursor-pointer group rounded-b-2xl" id="detail-guest-picker-container">
                            <input type="hidden" name="rooms" id="detail-rooms-input" value="{{ $roomsCount ?? 1 }}">
                            <input type="hidden" name="adults" id="detail-adults-input" value="{{ $adults }}">
                            <input type="hidden" name="children" id="detail-children-input" value="{{ $children }}">
                            
                            <div onclick="toggleDetailGuestPopover()" class="w-full">
                                <div class="flex items-center justify-between">
                                    <label class="block text-[9px] uppercase tracking-[0.16em] font-bold text-forest/50 group-hover:text-emerald transition cursor-pointer flex items-center gap-1.5">
                                        <i data-lucide="users" class="w-3 h-3 text-emerald shrink-0"></i>
                                        <span>Rooms &amp; Guests</span>
                                    </label>
                                    <span class="text-[9px] font-semibold text-forest/40">Max {{ $roomType->max_guests }} guests</span>
                                </div>
                                <div class="flex items-center justify-between text-xs sm:text-sm font-semibold text-forest mt-1 select-none">
                                    <span id="detail-guest-label" class="truncate">{{ $roomsCount ?? 1 }} Room{{ ($roomsCount ?? 1) > 1 ? 's' : '' }} &middot; {{ $adults }} Adult{{ $adults > 1 ? 's' : '' }}{{ $children > 0 ? ', ' . $children . ' Kid' . ($children > 1 ? 's' : '') : '' }}</span>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-forest/40 group-hover:text-forest transition-transform duration-200 shrink-0 ml-1" id="detail-guest-chevron"></i>
                                </div>
                            </div>

                            <!-- Floating Popover Card -->
                            <div id="detail-guest-popover" class="hidden absolute top-full left-0 right-0 mt-2 rounded-2xl bg-[#FAF8F5] border border-forest/15 p-4 shadow-2xl z-50">
                                <div class="space-y-3" onclick="event.stopPropagation()">
                                    <!-- Rooms Row -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-bold text-forest">Rooms</p>
                                            <p class="text-[10px] text-forest/50">Number of suites</p>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <button type="button" onclick="adjustDetailGuestCount('rooms', -1)" id="detail-rooms-minus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer" {{ ($roomsCount ?? 1) <= 1 ? 'disabled' : '' }}>-</button>
                                            <span id="detail-rooms-val" class="w-4 text-center text-xs font-bold text-forest">{{ $roomsCount ?? 1 }}</span>
                                            <button type="button" onclick="adjustDetailGuestCount('rooms', 1)" id="detail-rooms-plus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
                                        </div>
                                    </div>

                                    <div class="h-px bg-forest/10"></div>

                                    <!-- Adults Row -->
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-bold text-forest">Adults</p>
                                            <p class="text-[10px] text-forest/50">Ages 13 and above</p>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <button type="button" onclick="adjustDetailGuestCount('adults', -1)" id="detail-adults-minus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer" {{ $adults <= 1 ? 'disabled' : '' }}>-</button>
                                            <span id="detail-adults-val" class="w-4 text-center text-xs font-bold text-forest">{{ $adults }}</span>
                                            <button type="button" onclick="adjustDetailGuestCount('adults', 1)" id="detail-adults-plus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
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
                                            <button type="button" onclick="adjustDetailGuestCount('children', -1)" id="detail-children-minus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 disabled:opacity-30 disabled:cursor-not-allowed transition text-xs font-bold cursor-pointer" {{ $children <= 0 ? 'disabled' : '' }}>-</button>
                                            <span id="detail-children-val" class="w-4 text-center text-xs font-bold text-forest">{{ $children }}</span>
                                            <button type="button" onclick="adjustDetailGuestCount('children', 1)" id="detail-children-plus" class="grid h-7 w-7 place-items-center rounded-full border border-forest/20 text-forest hover:bg-forest/5 transition text-xs font-bold cursor-pointer">+</button>
                                        </div>
                                    </div>

                                    <!-- Policy Notice -->
                                    <div class="p-2 rounded-xl bg-white soft-border text-[10px] text-forest/65 flex items-start gap-1.5 leading-snug">
                                        <i data-lucide="info" class="w-3.5 h-3.5 text-emerald shrink-0 mt-0.5"></i>
                                        <span>Infants under 5 stay free. Children &gt; 12 are treated as adults for bedding.</span>
                                    </div>

                                    <!-- Apply Button -->
                                    <button type="button" onclick="closeDetailGuestPopover()" class="w-full py-2 rounded-xl bg-forest text-paper text-xs font-bold hover:bg-emerald transition cursor-pointer shadow-xs">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA BUTTON -->
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs tracking-wide shadow-card transition cursor-pointer">
                        <span id="detail-reserve-btn-text">Reserve {{ ($roomsCount ?? 1) > 1 ? ($roomsCount . ' Rooms') : '1 Room' }}</span>
                    </button>

                    <p class="text-center text-[11px] text-forest/50">
                        You won't be charged yet &middot; Instant confirmation
                    </p>
                    @if(!empty($childAges))
                        @foreach($childAges as $age)
                            <input type="hidden" name="child_ages[]" value="{{ $age }}">
                        @endforeach
                    @endif

                    <!-- ITEMIZED PRICING BREAKDOWN (AIRBNB TRANSPARENT STYLE) -->
                    @php
                        $sub = $stayPricing['subtotal'] ?? ($roomType->base_price * $nights);
                        $tax = $stayPricing['tax'] ?? round($sub * 0.12, 2);
                        $tot = $stayPricing['total'] ?? ($sub + $tax);
                        $advance = $stayPricing['deposit'] ?? round($tot * 0.20, 2);
                        $avgRate = $stayPricing['average_nightly_rate'] ?? $roomType->base_price;
                        $wkndNights = $stayPricing['weekend_nights'] ?? 0;
                    @endphp
                    <div class="pt-4 border-t border-forest/10 space-y-2 text-xs text-forest/70">
                        <div class="flex justify-between">
                            <span>
                                ₹{{ number_format($avgRate) }} avg / night &times; <span id="detail-nights-count">{{ $nights }}</span> night{{ $nights > 1 ? 's' : '' }}<span id="detail-rooms-multiplier">{{ ($roomsCount ?? 1) > 1 ? ' &times; ' . $roomsCount . ' rooms' : '' }}</span>
                                @if($wkndNights > 0 && !empty($stayPricing['weekend_price']))
                                    <span class="text-[10px] text-amber-700 block">({{ $wkndNights }} weekend night{{ $wkndNights > 1 ? 's' : '' }} @ ₹{{ number_format($stayPricing['weekend_price']) }})</span>
                                @endif
                            </span>
                            <span class="font-semibold text-forest" id="detail-subtotal-val">₹{{ number_format($sub) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="underline">Hospitality & Sanitization Fee</span>
                            <span class="text-emerald font-semibold">Free</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="underline">GST Tax ({{ $stayPricing['tax_rate'] ?? 12 }}%)</span>
                            <span class="font-semibold text-forest" id="detail-tax-val">₹{{ number_format($tax) }}</span>
                        </div>
                        <div class="pt-3 border-t border-forest/10 flex justify-between font-bold text-sm text-forest">
                            <span>Total before check-in</span>
                            <span id="detail-total-val">₹{{ number_format($tot) }}</span>
                        </div>
                        <div class="bg-mint p-3 rounded-xl text-[11px] text-forest border border-emerald/15 mt-2">
                            <span class="font-bold text-emerald">Flexible Payment:</span> Pay full amount now, or pay <strong id="detail-deposit-val">₹{{ number_format($advance) }} (20%)</strong> deposit today and balance upon arrival.
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

<!-- FULLSCREEN PHOTO LIGHTBOX MODAL (SPACIOUS, SCREEN-FILLING AIRBNB / LUXURY HOTEL STYLE) -->
<div id="gallery-modal" class="hidden fixed inset-0 z-[120] bg-black/95 backdrop-blur-md flex flex-col justify-between p-3 sm:p-5 select-none" style="height: 100vh; max-height: 100vh; overflow: hidden;" onclick="if(event.target === this) closePhotoLightbox()">
    
    <!-- Top Navigation Bar -->
    <div class="w-full max-w-7xl mx-auto flex items-center justify-between pb-2.5 sm:pb-3 border-b border-white/15 z-50 shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-emerald/20 flex items-center justify-center text-emerald">
                <i data-lucide="image" class="w-4 h-4"></i>
            </div>
            <div>
                <h3 class="serif text-sm sm:text-base md:text-lg font-bold text-white leading-tight">{{ $roomType->name }}</h3>
                <span class="text-[11px] sm:text-xs text-white/60" id="lightbox-counter">Photo 1 of {{ count($allPhotos) }} &middot; {{ $roomType->branch ? $roomType->branch->name : 'Country Side Cottages' }}</span>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <span class="hidden sm:inline-block text-[11px] text-white/40 mr-1">Press <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white/80 font-mono text-[10px]">Esc</kbd> to exit</span>
            <button type="button" onclick="closePhotoLightbox()" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-full transition cursor-pointer shadow-xs" title="Close (Esc)">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <!-- Centered Photo Stage: Maximum screen presence in both mobile & desktop webview -->
    <div class="flex-1 w-full max-w-7xl mx-auto flex items-center justify-center relative min-h-0 my-auto py-2 sm:py-3 overflow-hidden">
        @if(count($allPhotos) > 1)
        <!-- Previous Arrow -->
        <button type="button" onclick="navigateLightbox(-1)" class="absolute left-2 sm:left-4 z-40 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-black/70 hover:bg-black/90 text-white border border-white/25 backdrop-blur-md flex items-center justify-center transition shadow-2xl hover:scale-105 active:scale-95 cursor-pointer" title="Previous Photo (←)">
            <i data-lucide="chevron-left" class="w-6 h-6 sm:w-7 sm:h-7"></i>
        </button>
        @endif

        <div class="relative flex items-center justify-center max-h-full max-w-full">
            <img id="lightbox-current-img" 
                 src="{{ $allPhotos[0] ?? '' }}" 
                 alt="{{ $roomType->name }}" 
                 class="max-h-[72vh] sm:max-h-[78vh] md:max-h-[82vh] max-w-[96vw] sm:max-w-[92vw] md:max-w-[88vw] w-auto h-auto object-contain rounded-2xl shadow-2xl block mx-auto transition-opacity duration-200">
        </div>

        @if(count($allPhotos) > 1)
        <!-- Next Arrow -->
        <button type="button" onclick="navigateLightbox(1)" class="absolute right-2 sm:right-4 z-40 w-11 h-11 sm:w-14 sm:h-14 rounded-full bg-black/70 hover:bg-black/90 text-white border border-white/25 backdrop-blur-md flex items-center justify-center transition shadow-2xl hover:scale-105 active:scale-95 cursor-pointer" title="Next Photo (→)">
            <i data-lucide="chevron-right" class="w-6 h-6 sm:w-7 sm:h-7"></i>
        </button>
        @endif
    </div>

    <!-- Bottom Thumbnail / Control Strip -->
    <div class="w-full max-w-7xl mx-auto flex items-center justify-center gap-2 sm:gap-3 pt-2 pb-1 shrink-0 overflow-x-auto no-scrollbar">
        @if(count($allPhotos) > 1)
            @foreach($allPhotos as $idx => $thumb)
            <button type="button" onclick="setLightboxIndex({{ $idx }})" class="lightbox-thumb w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 rounded-xl overflow-hidden border-2 transition cursor-pointer shrink-0 shadow-md {{ $idx === 0 ? 'border-emerald ring-2 ring-emerald/40' : 'border-transparent opacity-60 hover:opacity-100' }}" data-idx="{{ $idx }}">
                <img src="{{ $thumb }}" alt="Thumbnail {{ $idx + 1 }}" class="w-full h-full object-cover">
            </button>
            @endforeach
        @else
            <span class="text-xs text-white/50">Click anywhere outside the photo or press Esc to close</span>
        @endif
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
                <textarea name="comment" required rows="4" placeholder="Tell future guests about the serene atmosphere, heritage room comforts, views, and cottage hospitality..." class="w-full px-3 py-2 rounded-xl border border-forest/20 bg-white text-forest focus:outline-hidden focus:ring-1 focus:ring-forest text-xs"></textarea>
            </div>

            <p class="text-[11px] text-forest/50 leading-relaxed">
                ℹ️ To maintain authentic trust, guest reviews are verified and displayed once approved by our cottage management team.
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
<div class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-paper/95 backdrop-blur-md border-t border-forest/15 px-5 py-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] flex items-center justify-between shadow-[0_-10px_25px_rgba(0,0,0,0.08)]">
    <div>
        <div class="flex items-baseline gap-1">
            <span class="font-bold text-base text-forest">₹{{ number_format($roomType->base_price) }}</span>
            <span class="text-xs text-forest/50">/ night</span>
        </div>
        <div class="text-[10px] text-emerald font-semibold">
            <span id="detail-mobile-nights">{{ $nights }}</span> night stay &middot; Total <span id="detail-mobile-total">₹{{ number_format($tot) }}</span>
        </div>
    </div>
    <button type="button" 
            onclick="const f = document.getElementById('detail-check-in-input'); if(f && f.closest('form')) f.closest('form').submit();" 
            class="px-6 py-2.5 rounded-xl bg-forest text-paper text-xs font-bold shadow-card hover:bg-emerald transition cursor-pointer">
        Reserve
    </button>
</div>

<script>
    /* =====================================================================
       FULLSCREEN PHOTO LIGHTBOX (AIRBNB STYLE)
       ===================================================================== */
    const lightboxPhotos = @json($allPhotos);
    let currentLightboxIdx = 0;

    function openPhotoLightbox(index = 0) {
        if (!lightboxPhotos || lightboxPhotos.length === 0) return;
        currentLightboxIdx = Math.max(0, Math.min(index, lightboxPhotos.length - 1));
        updateLightboxView();
        const modal = document.getElementById('gallery-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            if (window.lucide) {
                lucide.createIcons();
            }
        }
    }

    function closePhotoLightbox() {
        const modal = document.getElementById('gallery-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function navigateLightbox(direction) {
        if (!lightboxPhotos || lightboxPhotos.length <= 1) return;
        currentLightboxIdx = (currentLightboxIdx + direction + lightboxPhotos.length) % lightboxPhotos.length;
        updateLightboxView();
    }

    function setLightboxIndex(index) {
        if (!lightboxPhotos || index < 0 || index >= lightboxPhotos.length) return;
        currentLightboxIdx = index;
        updateLightboxView();
    }

    function updateLightboxView() {
        const img = document.getElementById('lightbox-current-img');
        const counter = document.getElementById('lightbox-counter');
        if (img && lightboxPhotos[currentLightboxIdx]) {
            img.src = lightboxPhotos[currentLightboxIdx];
        }
        if (counter) {
            const branchName = "{{ $roomType->branch ? addslashes($roomType->branch->name) : 'Country Side Cottages' }}";
            counter.textContent = `Photo ${currentLightboxIdx + 1} of ${lightboxPhotos.length} \u00b7 ${branchName}`;
        }
        document.querySelectorAll('.lightbox-thumb').forEach((thumb, idx) => {
            if (idx === currentLightboxIdx) {
                thumb.classList.remove('border-transparent', 'opacity-60');
                thumb.classList.add('border-emerald', 'ring-2', 'ring-emerald/40', 'opacity-100');
            } else {
                thumb.classList.remove('border-emerald', 'ring-2', 'ring-emerald/40', 'opacity-100');
                thumb.classList.add('border-transparent', 'opacity-60');
            }
        });
    }

    // Keyboard navigation (Esc to close, Left/Right arrow keys to navigate)
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('gallery-modal');
        if (!modal || modal.classList.contains('hidden')) return;

        if (e.key === 'Escape') {
            closePhotoLightbox();
        } else if (e.key === 'ArrowLeft') {
            navigateLightbox(-1);
        } else if (e.key === 'ArrowRight') {
            navigateLightbox(1);
        }
    });

    /* =====================================================================
       DATE HELPERS (EXACT SIGNATURE FROM STAYS SEARCH CARD)
       ===================================================================== */
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

    function updateDetailDateDisplay(inputId, displayId) {
        const input = document.getElementById(inputId);
        const display = document.getElementById(displayId);
        if (input && display && input.value) {
            display.textContent = formatDisplayDate(input.value);
        }
    }

    function onDetailDateChanged() {
        const inInput = document.getElementById('detail-check-in-input');
        const outInput = document.getElementById('detail-check-out-input');
        if (!inInput || !outInput || !inInput.value) return;

        const parts = inInput.value.split('-');
        if (parts.length === 3) {
            const inDate = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            const minOut = new Date(inDate);
            minOut.setDate(minOut.getDate() + 1);

            const y = minOut.getFullYear();
            const m = String(minOut.getMonth() + 1).padStart(2, '0');
            const d = String(minOut.getDate()).padStart(2, '0');
            const minDateStr = `${y}-${m}-${d}`;
            outInput.min = minDateStr;

            if (outInput.value <= inInput.value) {
                outInput.value = minDateStr;
                updateDetailDateDisplay('detail-check-out-input', 'detail-check-out-display');
            }
        }

        if (inInput.value && outInput.value) {
            const dIn = new Date(inInput.value);
            const dOut = new Date(outInput.value);
            const diffNights = Math.max(1, Math.round((dOut - dIn) / (1000 * 60 * 60 * 24)));
            detailPricingConfig.nights = diffNights;

            const nightsCountEl = document.getElementById('detail-nights-count');
            if (nightsCountEl) nightsCountEl.textContent = diffNights;

            const mobileNightsEl = document.getElementById('detail-mobile-nights');
            if (mobileNightsEl) mobileNightsEl.textContent = diffNights;

            updateDetailGuestUI();
        }
    }

    /* =====================================================================
       ROOM DETAIL GUEST PICKER & POPOVER (STAYS PAGE STYLE)
       ===================================================================== */
    const detailPricingConfig = {
        nights: {{ (int)$nights }},
        avgRate: {{ (float)($stayPricing['average_nightly_rate'] ?? $roomType->base_price) }},
        taxRate: {{ (float)($stayPricing['tax_rate'] ?? 12.0) }},
        depositPct: {{ (float)($stayPricing['deposit_percentage'] ?? 20.0) }},
        maxAvailableRooms: {{ max(1, (int)($availableRoomsCount ?? ($roomType->rooms ? $roomType->rooms->count() : 4))) }},
        maxGuestsPerRoom: {{ (int)($roomType->max_guests ?: 4) }}
    };

    let detailGuestState = {
        rooms: {{ (int)($roomsCount ?? 1) }},
        adults: {{ (int)($adults ?? 2) }},
        children: {{ (int)($children ?? 0) }}
    };

    function adjustDetailGuestCount(type, delta) {
        const maxRooms = detailPricingConfig.maxAvailableRooms;
        const maxAdults = Math.max(10, detailGuestState.rooms * (detailPricingConfig.maxGuestsPerRoom || 4));
        
        if (type === 'rooms') {
            detailGuestState.rooms = Math.min(maxRooms, Math.max(1, (detailGuestState.rooms || 1) + delta));
        } else if (type === 'adults') {
            detailGuestState.adults = Math.min(maxAdults, Math.max(1, detailGuestState.adults + delta));
        } else if (type === 'children') {
            detailGuestState.children = Math.min(6, Math.max(0, detailGuestState.children + delta));
        }
        updateDetailGuestUI();
    }

    function updateDetailGuestUI() {
        const r = detailGuestState.rooms || 1;
        const a = detailGuestState.adults;
        const c = detailGuestState.children;
        const maxRooms = detailPricingConfig.maxAvailableRooms;
        const maxAdults = Math.max(10, r * (detailPricingConfig.maxGuestsPerRoom || 4));

        if (document.getElementById('detail-rooms-input')) document.getElementById('detail-rooms-input').value = r;
        if (document.getElementById('detail-adults-input')) document.getElementById('detail-adults-input').value = a;
        if (document.getElementById('detail-children-input')) document.getElementById('detail-children-input').value = c;
        if (document.getElementById('detail-rooms-val')) document.getElementById('detail-rooms-val').textContent = r;
        if (document.getElementById('detail-adults-val')) document.getElementById('detail-adults-val').textContent = a;
        if (document.getElementById('detail-children-val')) document.getElementById('detail-children-val').textContent = c;
        if (document.getElementById('detail-rooms-minus')) document.getElementById('detail-rooms-minus').disabled = (r <= 1);
        if (document.getElementById('detail-rooms-plus')) document.getElementById('detail-rooms-plus').disabled = (r >= maxRooms);
        if (document.getElementById('detail-adults-minus')) document.getElementById('detail-adults-minus').disabled = (a <= 1);
        if (document.getElementById('detail-adults-plus')) document.getElementById('detail-adults-plus').disabled = (a >= maxAdults);
        if (document.getElementById('detail-children-minus')) document.getElementById('detail-children-minus').disabled = (c <= 0);
        if (document.getElementById('detail-children-plus')) document.getElementById('detail-children-plus').disabled = (c >= 6);

        const label = `${r} Room${r > 1 ? 's' : ''} \u00b7 ${a} Adult${a > 1 ? 's' : ''}${c > 0 ? ', ' + c + ' Kid' + (c > 1 ? 's' : '') : ''}`;
        if (document.getElementById('detail-guest-label')) {
            document.getElementById('detail-guest-label').textContent = label;
        }

        if (document.getElementById('detail-max-guests-badge')) {
            const maxCap = r * (detailPricingConfig.maxGuestsPerRoom || 4);
            document.getElementById('detail-max-guests-badge').textContent = `Max ${maxCap} guests`;
        }

        // Live Dynamic Stay Pricing Recalculation
        const subtotal = Math.round(detailPricingConfig.avgRate * detailPricingConfig.nights * r);
        const tax = Math.round(subtotal * (detailPricingConfig.taxRate / 100));
        const total = subtotal + tax;
        const deposit = Math.round(total * (detailPricingConfig.depositPct / 100));

        if (document.getElementById('detail-subtotal-val')) {
            document.getElementById('detail-subtotal-val').textContent = '₹' + subtotal.toLocaleString('en-IN');
        }
        if (document.getElementById('detail-tax-val')) {
            document.getElementById('detail-tax-val').textContent = '₹' + tax.toLocaleString('en-IN');
        }
        if (document.getElementById('detail-total-val')) {
            document.getElementById('detail-total-val').textContent = '₹' + total.toLocaleString('en-IN');
        }
        if (document.getElementById('detail-deposit-val')) {
            document.getElementById('detail-deposit-val').textContent = '₹' + deposit.toLocaleString('en-IN') + ' (20%)';
        }
        if (document.getElementById('detail-mobile-total')) {
            document.getElementById('detail-mobile-total').textContent = '₹' + total.toLocaleString('en-IN');
        }
        if (document.getElementById('detail-rooms-multiplier')) {
            document.getElementById('detail-rooms-multiplier').textContent = r > 1 ? ` \u00d7 ${r} rooms` : '';
        }
        if (document.getElementById('detail-reserve-btn-text')) {
            document.getElementById('detail-reserve-btn-text').textContent = `Reserve ${r} Room${r > 1 ? 's' : ''}`;
        }
    }

    function toggleDetailGuestPopover() {
        const pop = document.getElementById('detail-guest-popover');
        const chev = document.getElementById('detail-guest-chevron');
        if (pop) {
            const isHidden = pop.classList.contains('hidden');
            if (isHidden) {
                pop.classList.remove('hidden');
                if (chev) chev.style.transform = 'rotate(180deg)';
            } else {
                pop.classList.add('hidden');
                if (chev) chev.style.transform = 'rotate(0deg)';
            }
        }
    }

    function closeDetailGuestPopover() {
        const pop = document.getElementById('detail-guest-popover');
        const chev = document.getElementById('detail-guest-chevron');
        if (pop) pop.classList.add('hidden');
        if (chev) chev.style.transform = 'rotate(0deg)';
    }

    document.addEventListener('click', (e) => {
        const container = document.getElementById('detail-guest-picker-container');
        const pop = document.getElementById('detail-guest-popover');
        if (container && !container.contains(e.target) && pop && !pop.classList.contains('hidden')) {
            closeDetailGuestPopover();
        }
    });
</script>
@endsection

