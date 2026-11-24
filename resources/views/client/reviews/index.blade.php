@extends('layouts.customer')

@section('title', 'Guest Reviews & Experiences — Country Side Cottages')
@section('meta_description', 'Read authentic verified guest reviews and traveler stories from Country Side Cottages in Munnar, Rajakkad, and Adimali, Kerala.')

@section('content')
<div class="min-h-screen py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-10">
        
        <!-- Page Header -->
        <div class="text-center max-w-2xl mx-auto">
            <span class="eyebrow text-brass font-bold uppercase tracking-wider block mb-2">Verified Traveler Stories</span>
            <h1 class="serif text-3xl sm:text-4xl md:text-5xl font-bold text-forest tracking-tight">
                Guest Reviews &amp; Ratings
            </h1>
            <p class="text-xs sm:text-sm text-forest/70 mt-2 leading-relaxed">
                Genuine experiences shared by guests who lived amidst the tea trails, misty peaks, and peaceful wooden cottages of Country Side Cottages.
            </p>
        </div>

        <!-- GOOGLE REVIEWS OVERALL RATING SCORECARD -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 soft-border shadow-card">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <!-- Left: Big Rating Number & Google Style Badge -->
                <div class="md:col-span-5 text-center md:text-left flex flex-col items-center md:items-start justify-center border-b md:border-b-0 md:border-r border-forest/10 pb-6 md:pb-0 md:pr-8">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-5xl sm:text-6xl font-extrabold text-forest tracking-tight">
                            {{ number_format($averageRating, 1) }}
                        </span>
                        <div class="flex flex-col items-start">
                            <div class="flex items-center text-amber-400 text-lg sm:text-xl">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>★</span>
                                @endfor
                            </div>
                            <span class="text-[11px] font-bold text-forest/60 uppercase tracking-wider mt-0.5">Overall Score</span>
                        </div>
                    </div>
                    <p class="text-xs text-forest/70 mt-2">
                        Based on <strong>{{ $totalReviews }}</strong> verified traveler reviews across all cottages.
                    </p>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200/80 mt-4">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald"></i>
                        <span>100% Genuine Verified Guest Feedback</span>
                    </div>
                </div>

                <!-- Right: Star Rating Distribution Progress Bars -->
                <div class="md:col-span-7 space-y-2">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $count = $ratingCounts[$star] ?? 0;
                            $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : ($star === 5 ? 95 : 5);
                        @endphp
                        <div class="flex items-center gap-3 text-xs">
                            <span class="w-12 font-bold text-forest text-right shrink-0">{{ $star }} stars</span>
                            <div class="flex-1 h-3 rounded-full bg-paper/80 overflow-hidden soft-border">
                                <div class="h-full bg-amber-400 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="w-10 text-[11px] text-forest/60 text-right shrink-0 font-medium">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- DYNAMIC FILTER TOOLBAR -->
        <div class="bg-white p-4 rounded-2xl soft-border shadow-xs flex flex-wrap items-center justify-between gap-3">
            <!-- Dynamic Branch Filters -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-[11px] font-bold text-forest/50 uppercase tracking-wider mr-1">Branch:</span>
                <a href="{{ route('reviews.index', array_filter(['rating' => $selectedRating, 'has_media' => $hasMediaOnly ? '1' : null])) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ empty($selectedBranchId) ? 'bg-forest text-paper shadow-xs' : 'bg-paper text-forest/70 hover:bg-forest/10' }}">
                    All Branches
                </a>
                @foreach($branches as $b)
                <a href="{{ route('reviews.index', array_filter(['branch_id' => $b->id, 'rating' => $selectedRating, 'has_media' => $hasMediaOnly ? '1' : null])) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $selectedBranchId == $b->id ? 'bg-forest text-paper shadow-xs' : 'bg-paper text-forest/70 hover:bg-forest/10' }}">
                    {{ $b->name }}
                </a>
                @endforeach
            </div>

            <!-- Dynamic Media & Rating Filters -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('reviews.index', array_filter(['branch_id' => $selectedBranchId, 'rating' => $selectedRating, 'has_media' => $hasMediaOnly ? null : '1'])) }}" 
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $hasMediaOnly ? 'bg-emerald text-paper shadow-xs' : 'bg-paper text-forest/70 hover:bg-forest/10' }}">
                    <i data-lucide="image" class="w-3.5 h-3.5"></i>
                    <span>With Photos/Videos</span>
                </a>

                <select onchange="location = this.value;" class="px-3 py-1.5 rounded-xl bg-paper text-forest/80 text-xs font-bold soft-border focus:ring-1 focus:ring-forest focus:outline-hidden">
                    <option value="{{ route('reviews.index', array_filter(['branch_id' => $selectedBranchId, 'has_media' => $hasMediaOnly ? '1' : null])) }}" {{ empty($selectedRating) ? 'selected' : '' }}>
                        All Ratings
                    </option>
                    @foreach([5, 4, 3] as $rOption)
                    <option value="{{ route('reviews.index', array_filter(['branch_id' => $selectedBranchId, 'rating' => $rOption, 'has_media' => $hasMediaOnly ? '1' : null])) }}" {{ $selectedRating == $rOption ? 'selected' : '' }}>
                        {{ $rOption }} Stars Only
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- GOOGLE REVIEWS CARDS GRID -->
        @if($reviews->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center soft-border shadow-card max-w-lg mx-auto space-y-3">
            <div class="w-14 h-14 rounded-full bg-mint text-emerald flex items-center justify-center mx-auto">
                <i data-lucide="message-square" class="w-7 h-7"></i>
            </div>
            <h3 class="serif text-xl font-bold text-forest">No Reviews Match Filters</h3>
            <p class="text-xs text-forest/60">Try clearing the branch or rating filters to see more traveler stories.</p>
            <a href="{{ route('reviews.index') }}" class="inline-block mt-2 px-5 py-2 rounded-xl bg-forest text-paper text-xs font-bold hover:bg-emerald transition">
                Reset Filters
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reviews as $rev)
            <div class="bg-white rounded-3xl p-6 soft-border shadow-card hover:shadow-md transition flex flex-col justify-between space-y-4">
                
                <!-- Review Card Top -->
                <div class="space-y-3">
                    <!-- Guest Profile Header -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <!-- Avatar circle -->
                            <div class="w-11 h-11 rounded-full bg-forest text-paper font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                {{ strtoupper(substr($rev->effective_guest_name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-forest leading-tight">{{ $rev->effective_guest_name }}</h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-bold text-emerald flex items-center gap-0.5">
                                        <i data-lucide="check-circle-2" class="w-3 h-3 text-emerald"></i> Verified Stay
                                    </span>
                                    <span class="text-[10px] text-forest/40">&middot; {{ $rev->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- 5 Star Rating -->
                        <div class="flex items-center text-amber-400 text-xs shrink-0 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200/60 font-bold">
                            @for($i = 1; $i <= 5; $i++)
                                <span>{{ $i <= $rev->rating ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                    </div>

                    <!-- Stay Location / Summary -->
                    <div class="text-[11px] text-forest/60 flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3 h-3 text-emerald shrink-0"></i>
                        <span class="font-semibold text-forest">{{ $rev->branch?->name ?? 'Country Side Cottages' }}</span>
                        @if($rev->stay_summary)
                            <span>&middot; {{ $rev->stay_summary }}</span>
                        @endif
                    </div>

                    <!-- Title & Comment -->
                    @if($rev->title)
                    <h5 class="font-bold text-sm text-forest leading-snug">{{ $rev->title }}</h5>
                    @endif
                    <p class="text-xs text-forest/80 leading-relaxed italic">
                        “{{ $rev->comment }}”
                    </p>

                    <!-- Attached Photos Thumbnails -->
                    @if(!empty($rev->photos_list))
                    <div class="pt-2">
                        <span class="text-[10px] font-bold text-forest/50 uppercase tracking-wider block mb-1.5">Guest Photos</span>
                        <div class="flex items-center gap-2 flex-wrap">
                            @foreach($rev->photos_list as $pIdx => $photo)
                            <button type="button" onclick="openReviewMediaLightbox('{{ $photo }}', 'image', '{{ addslashes($rev->effective_guest_name) }}')" class="relative w-16 h-16 rounded-xl overflow-hidden soft-border group cursor-pointer shrink-0">
                                <img src="{{ $photo }}" alt="Guest photo" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition"></div>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Attached Video Player / Thumbnail -->
                    @if(!empty($rev->videos_list))
                    <div class="pt-2">
                        <span class="text-[10px] font-bold text-forest/50 uppercase tracking-wider block mb-1.5">Guest Video Clip</span>
                        @foreach($rev->videos_list as $video)
                        <div class="rounded-2xl overflow-hidden bg-black/90 relative soft-border">
                            <video src="{{ $video }}" controls playsinline preload="metadata" class="w-full max-h-48 object-cover rounded-2xl"></video>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Resort Manager Response (if any) -->
                @if($rev->staff_reply)
                <div class="pt-3 border-t border-forest/10 mt-3">
                    <div class="p-3.5 rounded-2xl bg-paper/60 soft-border space-y-1 text-xs">
                        <div class="flex items-center gap-1.5 font-bold text-forest text-[11px]">
                            <i data-lucide="message-square" class="w-3.5 h-3.5 text-emerald"></i>
                            <span>Response from Country Side Cottages</span>
                        </div>
                        <p class="text-forest/75 text-[11px] leading-relaxed">
                            {{ $rev->staff_reply }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-6">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>

<!-- REVIEW MEDIA LIGHTBOX MODAL -->
<div id="review-media-lightbox" class="hidden fixed inset-0 z-[150] bg-black/95 backdrop-blur-md flex flex-col justify-between p-4 select-none" onclick="if(event.target === this) closeReviewMediaLightbox()">
    <div class="w-full max-w-5xl mx-auto flex items-center justify-between pb-3 border-b border-white/20">
        <div class="flex items-center gap-2 text-white">
            <i data-lucide="image" class="w-4 h-4 text-emerald"></i>
            <span id="lightbox-guest-title" class="text-xs sm:text-sm font-bold">Guest Photo</span>
        </div>
        <button type="button" onclick="closeReviewMediaLightbox()" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition cursor-pointer">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <div class="w-full max-w-4xl mx-auto flex-1 flex items-center justify-center p-2" id="lightbox-media-container">
        <!-- Rendered via JS -->
    </div>

    <div class="text-center text-[11px] text-white/50 pb-2">
        Country Side Cottages Verified Guest Media
    </div>
</div>

<script>
    function openReviewMediaLightbox(src, type, guestName) {
        const modal = document.getElementById('review-media-lightbox');
        const container = document.getElementById('lightbox-media-container');
        const title = document.getElementById('lightbox-guest-title');
        if (!modal || !container) return;

        if (title) title.textContent = `Memory by ${guestName}`;

        if (type === 'image') {
            container.innerHTML = `<img src="${src}" class="max-h-[80vh] max-w-full rounded-2xl object-contain shadow-2xl animate-fade-in" alt="Guest photo">`;
        } else {
            container.innerHTML = `<video src="${src}" controls autoplay playsinline class="max-h-[80vh] max-w-full rounded-2xl shadow-2xl"></video>`;
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewMediaLightbox() {
        const modal = document.getElementById('review-media-lightbox');
        const container = document.getElementById('lightbox-media-container');
        if (modal) modal.classList.add('hidden');
        if (container) container.innerHTML = '';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeReviewMediaLightbox();
    });
</script>
@endsection
