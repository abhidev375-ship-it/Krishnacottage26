@extends('layouts.customer')

@section('title', 'Authentic Facilities & Experiences | Krishna Cottages')

@section('content')
<!-- HERO SECTION -->
<div class="bg-forest text-paper py-6 sm:py-10 md:py-14 px-4 sm:px-6 relative overflow-hidden">
    <div class="mx-auto max-w-[1480px] text-center relative z-10">
        <span class="eyebrow text-brass block mb-2">Authentic Kerala Living</span>
        <h1 class="serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight">Facilities & In-House Experiences</h1>
        <p class="text-xs sm:text-sm text-paper/70 max-w-2xl mx-auto mt-3 leading-relaxed">
            Slow, mindful spaces crafted for nature communion, botanical healing, and serene plantation life across Krishna destinations.
        </p>

        <!-- AUTHENTIC LIVING PROMISE (strictly no pool) -->
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-brass/30 px-4 py-1.5 rounded-full text-xs text-brass font-semibold mt-5">
            <i data-lucide="leaf" class="w-3.5 h-3.5"></i>
            <span>Ecological Harmony &middot; Natural Stream Trails, strictly No Artificial Swimming Pools</span>
        </div>
    </div>
</div>

<!-- BRANCH FILTER BAR -->
<div class="bg-paper/95 backdrop-blur-md border-b border-forest/10 sticky top-16 md:top-20 z-30 shadow-xs">
    <div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-3 flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('facilities.index') }}" 
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ empty($selectedBranchId) ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                All Branches ({{ $facilities->count() }})
            </a>
            @foreach($branches as $b)
                <a href="{{ route('facilities.index', ['branch_id' => $b->id]) }}" 
                   class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ (string)$selectedBranchId === (string)$b->id ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                    {{ $b->name }}
                </a>
            @endforeach
        </div>

        @if($selectedBranchId)
            <a href="{{ route('facilities.index') }}" class="text-xs text-forest/60 hover:text-emerald font-semibold flex items-center gap-1 shrink-0">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset Filter
            </a>
        @endif
    </div>
</div>

<!-- FACILITIES GRID -->
<div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-8 sm:py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        @forelse($facilities as $fac)
        <div class="bg-white rounded-[26px] soft-border overflow-hidden shadow-card lift transition duration-300 flex flex-col justify-between group">
            <div>
                <div class="img-zoom relative h-60 w-full overflow-hidden bg-mint">
                    <img src="{{ $fac->image_url ?: 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80' }}" 
                         alt="{{ $fac->name }}" 
                         class="w-full h-full object-cover">
                    
                    <!-- BRANCH BADGE: Specific Branch Name or All Branches -->
                    <div class="absolute top-3.5 left-3.5 flex items-center gap-1.5">
                        <span class="bg-forest/90 backdrop-blur-md text-paper text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider flex items-center gap-1 shadow-md">
                            <i data-lucide="{{ $fac->branch ? 'map-pin' : 'globe' }}" class="w-3 h-3 text-brass"></i>
                            {{ $fac->branch ? $fac->branch->name : 'All Branches' }}
                        </span>
                    </div>

                    <!-- RATE / BOOKABILITY BADGE -->
                    <div class="absolute top-3.5 right-3.5">
                        @if($fac->is_bookable)
                            @if($fac->rate > 0)
                                <span class="bg-emerald-800 text-white font-bold text-[11px] px-3 py-1 rounded-full shadow-md">
                                    ₹{{ number_format($fac->rate, 0) }} / Guest
                                </span>
                            @else
                                <span class="bg-emerald-700 text-white font-bold text-[11px] px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                                    <i data-lucide="sparkles" class="w-3 h-3 text-yellow-300"></i> Complimentary
                                </span>
                            @endif
                        @else
                            <span class="bg-paper text-forest soft-border text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-xs">
                                General Access
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-3">
                    <h3 class="serif text-xl font-bold text-forest group-hover:text-emerald transition">{{ $fac->name }}</h3>
                    <p class="text-xs text-forest/70 leading-relaxed">
                        {{ $fac->description ?: $fac->short_description ?: 'Experience restorative quietude and authentic nature immersion crafted by local artisans and wellness masters.' }}
                    </p>
                </div>
            </div>

            <div class="px-6 pb-6 pt-4 border-t border-forest/10 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-forest/60">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald"></i>
                    <span>{{ $fac->operating_hours ?? 'Open Daily &middot; 7:00 AM - 7:00 PM' }}</span>
                </div>
                
                @if($fac->is_bookable)
                    @auth
                        <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-1 bg-forest hover:bg-emerald text-paper px-3 py-1.5 rounded-xl font-bold transition shadow-xs">
                            <i data-lucide="calendar-plus" class="w-3.5 h-3.5 text-brass"></i>
                            <span>Book In-House</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-forest font-bold hover:text-emerald flex items-center gap-1 transition">
                            <span>Book In Stay</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-brass"></i>
                        </a>
                    @endauth
                @else
                    <button type="button" onclick="openChat()" class="text-forest font-bold hover:text-emerald flex items-center gap-1 transition">
                        <span>Enquire</span>
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-[26px] soft-border">
            <i data-lucide="sparkles" class="w-10 h-10 text-brass mx-auto mb-3"></i>
            <h3 class="serif text-xl font-bold text-forest">No Facilities Found</h3>
            <p class="text-xs text-forest/60 mt-1">Please select another branch or clear your filter.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
