@extends('layouts.customer')

@section('title', 'Nearby Discoveries & Excursions | Krishna Resorts')

@section('content')
<!-- HERO SECTION -->
<div class="bg-forest text-paper py-6 sm:py-10 md:py-12 px-4 sm:px-6 relative overflow-hidden">
    <div class="mx-auto max-w-[1480px] text-center relative z-10">
        <span class="eyebrow text-brass block mb-2">Curated Excursions</span>
        <h1 class="serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight">Nearby Discoveries</h1>
        <p class="text-xs sm:text-sm text-paper/70 max-w-2xl mx-auto mt-3 leading-relaxed">
            Cascading waterfalls, mist-shrouded peaks, ancient spice trade paths, and tranquil lakes just moments from our resort gates.
        </p>
    </div>
</div>

<!-- FILTER BAR -->
<div class="bg-paper/95 backdrop-blur-md border-b border-forest/10 sticky top-16 md:top-20 z-30 shadow-xs">
    <div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-3 flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('nearby.index', array_filter(['branch_id' => $selectedBranchId])) }}" 
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ empty($selectedCategory) ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                All Points of Interest ({{ $locations->count() }})
            </a>
            <a href="{{ route('nearby.index', array_filter(['category' => 'waterfall', 'branch_id' => $selectedBranchId])) }}" 
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $selectedCategory === 'waterfall' ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                Waterfalls
            </a>
            <a href="{{ route('nearby.index', array_filter(['category' => 'viewpoint', 'branch_id' => $selectedBranchId])) }}" 
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $selectedCategory === 'viewpoint' ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                Viewpoints & Peaks
            </a>
            <a href="{{ route('nearby.index', array_filter(['category' => 'heritage', 'branch_id' => $selectedBranchId])) }}" 
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $selectedCategory === 'heritage' ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                Heritage & Culture
            </a>
            <a href="{{ route('nearby.index', array_filter(['category' => 'wildlife', 'branch_id' => $selectedBranchId])) }}" 
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $selectedCategory === 'wildlife' ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                Wildlife & Nature
            </a>
        </div>

        <div class="shrink-0">
            <form action="{{ route('nearby.index') }}" method="GET" class="flex items-center gap-2">
                @if($selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif
                <select name="branch_id" onchange="this.form.submit()" class="bg-white soft-border rounded-xl text-xs font-semibold px-3 py-1.5 text-forest focus:outline-hidden cursor-pointer shadow-xs">
                    <option value="">All Resort Regions</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ (string)$selectedBranchId === (string)$b->id ? 'selected' : '' }}>
                            Near {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
</div>

<!-- LOCATIONS CARDS GRID -->
<div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-8 sm:py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-7">
        @forelse($locations as $loc)
        <div class="bg-white rounded-[26px] soft-border overflow-hidden shadow-card lift transition duration-300 flex flex-col justify-between group">
            <div>
                <div class="img-zoom relative h-60 w-full overflow-hidden bg-mint">
                    <img src="{{ $loc->image_url ?: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80' }}" 
                         alt="{{ $loc->name }}" 
                         class="w-full h-full object-cover">

                    <div class="absolute top-3 left-3 bg-forest/90 backdrop-blur-md text-paper px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                        {{ $loc->branch ? $loc->branch->name : 'Kerala' }}
                    </div>

                    <div class="absolute top-3 right-3 bg-paper/95 backdrop-blur-md text-forest px-2.5 py-1 rounded-full text-[10px] font-bold flex items-center gap-1 shadow-xs">
                        <i data-lucide="compass" class="w-3.5 h-3.5 text-emerald"></i>
                        <span>{{ $loc->distance_km ?? 5 }} km away</span>
                    </div>
                </div>

                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="eyebrow text-emerald block">
                            {{ str_replace('wildlife', 'Wildlife Reserve', ucfirst($loc->category)) }}
                        </span>
                        @if($loc->is_taxi_available)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                <i data-lucide="car" class="w-3 h-3 text-emerald-600"></i> Taxi Available
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">
                                Taxi Not Available
                            </span>
                        @endif
                    </div>

                    <h3 class="serif text-xl font-bold text-forest">{{ $loc->name }}</h3>
                    <p class="text-xs text-forest/70 leading-relaxed">
                        {{ $loc->description ?: 'Breathtaking scenic immersion with pristine trails and panoramic valley panoramas.' }}
                    </p>
                </div>
            </div>

            <div class="px-6 pb-6 pt-4 border-t border-forest/10 flex items-center justify-between text-xs text-forest/60">
                <div class="flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald"></i>
                    <span>Approx. {{ $loc->travel_time ?? '15 mins drive' }}</span>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        @if($loc->is_taxi_available)
                            <a href="{{ route('customer.dashboard') }}#excursions" class="text-xs font-bold text-emerald hover:underline flex items-center gap-1">
                                <span>Book Cab</span>
                                <i data-lucide="car" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    @endauth
                    <button type="button" onclick="openChat()" class="text-forest font-bold hover:text-emerald flex items-center gap-1 transition">
                        <span>Ask Concierge</span>
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 bg-white rounded-[26px] soft-border">
            <i data-lucide="compass" class="w-10 h-10 text-brass mx-auto mb-3"></i>
            <h3 class="serif text-xl font-bold text-forest">No Points of Interest Found</h3>
            <p class="text-xs text-forest/60 mt-1">Please select another region or category.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
