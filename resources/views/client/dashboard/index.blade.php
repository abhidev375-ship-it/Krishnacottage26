@extends('layouts.customer')

@section('title', 'Guest Dashboard & Stay Pass | Krishna Cottages')

@section('content')
<!-- TOP BANNER / GUEST IDENTITY BAR (COMPACT) -->
<div class="bg-forest text-paper py-3.5 sm:py-4 md:py-5 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Subtle luxury decorative ambient gradients -->
    <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
    <div class="absolute left-1/4 -top-24 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

    <div class="mx-auto max-w-[1440px] relative z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <span class="eyebrow text-brass uppercase tracking-widest text-[9px] sm:text-[10px] font-bold block mb-0.5">Private Guest Portal</span>
                <h1 class="serif text-xl sm:text-2xl md:text-3xl font-bold tracking-tight leading-tight">
                    Namaste, {{ $user->name }}
                </h1>
            </div>

            @php
                $dashBranch = $selectedStay?->branch ?? $allBranches?->first();
                $dashPhone = $dashBranch?->phone ?? \App\Models\Setting::get('resort_phone', '+91 94471 22334');
                $dashCleanPhone = preg_replace('/[^0-9+]/', '', $dashPhone);
            @endphp

            <!-- Quick Action Toolbar -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 shrink-0">
                @if($dashPhone)
                <a href="tel:{{ $dashCleanPhone }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-emerald-600/30 hover:bg-emerald-600/50 border border-emerald-500/30 text-paper font-bold text-xs flex items-center gap-1.5 transition touch-tap shadow-xs" title="Call {{ $dashBranch?->name ?? 'Front Desk' }} ({{ $dashPhone }})">
                    <i data-lucide="phone-call" class="w-3.5 h-3.5 text-brass"></i>
                    <span class="hidden sm:inline">Call Desk:</span>
                    <span>{{ $dashPhone }}</span>
                </a>
                @endif
                <button onclick="openDashboardChat()" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-paper font-bold text-xs flex items-center gap-1.5 transition touch-tap shadow-xs">
                    <i data-lucide="message-circle" class="w-3.5 h-3.5 text-brass"></i>
                    <span>Concierge Chat</span>
                </button>
                <a href="{{ route('rooms.index') }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-brass hover:brightness-105 text-forest font-bold text-xs flex items-center gap-1.5 shadow-card transition touch-tap">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Book Another Stay</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-1.5 sm:p-2 rounded-xl bg-white/5 hover:bg-rose-900/40 border border-white/10 text-paper/70 hover:text-rose-200 text-xs font-semibold transition" title="Sign Out">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- MULTI-STAY & MULTI-BRANCH SWITCHER TABS (If any bookings exist) -->
        @if($allReservations->isNotEmpty())
        <div class="mt-3.5 pt-3 border-t border-white/10">
            <div class="flex items-center justify-between gap-4 mb-2">
                <span class="text-[9px] sm:text-[10px] uppercase font-bold text-paper/70 tracking-wider flex items-center gap-1.5">
                    <i data-lucide="layers" class="w-3 h-3 text-brass"></i> Your Stays & Reservations ({{ $allReservations->count() }})
                </span>
                <span class="text-[9px] text-paper/40 hidden sm:inline">Tap any stay to switch view</span>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-0.5">
                @foreach($allReservations as $res)
                @php
                    $isCurrentSelected = $selectedStay && $selectedStay->id === $res->id;
                    $statusColor = match($res->status) {
                        'checked_in' => 'bg-emerald-400 text-emerald-950',
                        'confirmed' => 'bg-amber-400 text-amber-950',
                        'checked_out' => 'bg-paper/30 text-paper',
                        default => 'bg-white/20 text-paper/70',
                    };
                    $statusLabel = match($res->status) {
                        'checked_in' => 'In-House',
                        'confirmed' => 'Upcoming',
                        'checked_out' => 'Past Stay',
                        default => ucfirst($res->status),
                    };
                @endphp
                <a href="{{ route('customer.dashboard', ['stay_id' => $res->id]) }}" 
                   class="shrink-0 px-3 py-1.5 rounded-xl transition flex items-center gap-2 text-xs {{ $isCurrentSelected ? 'bg-white text-forest shadow-xs font-bold' : 'bg-white/10 hover:bg-white/15 text-paper/85 border border-white/10' }}">
                    <i data-lucide="{{ $res->status === 'checked_in' ? 'key' : 'calendar' }}" class="w-3 h-3 {{ $isCurrentSelected ? 'text-forest' : 'text-brass' }}"></i>
                    <span class="truncate max-w-[150px] sm:max-w-[200px]">{{ $res->branch ? $res->branch->name : 'Resort' }}</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[8px] sm:text-[9px] font-bold uppercase tracking-wider {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="font-mono text-[9px] opacity-60">#{{ substr($res->booking_code, -5) }}</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- MAIN DASHBOARD CONTENT AREA -->
<div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 py-6 md:py-10 space-y-8 md:space-y-12">

    @if($selectedStay)
    <!-- ========================================================
         SECTION 1: SELECTED STAY HERO PASS (AIRBNB LUXURY CARD)
         ======================================================== -->
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-forest/50">Current Active Property Context</span>
                <h2 class="serif text-2xl font-bold text-forest mt-0.5">
                    {{ $selectedStay->branch ? $selectedStay->branch->name : 'Resort Branch' }}
                </h2>
            </div>

            <!-- Lifecycle Badge -->
            <div class="flex items-center gap-2">
                @if($isInHouse)
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>Currently In-House (Checked-In)</span>
                </span>
                @elseif($isUpcoming)
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-700"></i>
                    <span>Confirmed Upcoming Stay</span>
                </span>
                @elseif($isCompleted)
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Completed Stay</span>
                </span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-[28px] soft-border shadow-card overflow-hidden grid grid-cols-1 lg:grid-cols-12">
            <!-- Cottage Photo & Branch Pill -->
            <div class="lg:col-span-5 h-64 lg:h-auto min-h-[260px] relative overflow-hidden bg-mint group">
                <img src="{{ $selectedStay->roomType && $selectedStay->roomType->cover_image_url ? $selectedStay->roomType->cover_image_url : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80' }}" 
                     alt="{{ $selectedStay->roomType->name ?? 'Cottage' }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-forest/80 via-transparent to-transparent"></div>

                <div class="absolute top-4 left-4 bg-forest/90 backdrop-blur-md text-paper text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-xs">
                    {{ $selectedStay->branch ? $selectedStay->branch->name : 'Kerala' }}
                </div>

                <div class="absolute bottom-4 left-4 right-4 text-paper">
                    <div class="font-mono text-xs text-brass font-bold">Code: {{ $selectedStay->booking_code }}</div>
                    <div class="serif text-xl font-bold">{{ $selectedStay->roomType ? $selectedStay->roomType->name : 'Signature Cottage' }}</div>
                    <div class="text-[11px] text-paper/90 mt-1 flex items-start gap-1.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-brass shrink-0 mt-0.5"></i>
                        <span>{{ $selectedStay->branch ? $selectedStay->branch->full_address : ($selectedStay->branch->city ?? 'Kerala, India') }}</span>
                    </div>
                </div>
            </div>

            <!-- Details, Room Assignment & Controls -->
            <div class="lg:col-span-7 p-6 sm:p-8 flex flex-col justify-between space-y-6">
                <div class="space-y-5">
                    <!-- Dates & Guests Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-3 rounded-2xl bg-paper/60 soft-border">
                            <span class="text-forest/40 block text-[9px] uppercase font-bold tracking-wider">Check-in</span>
                            <span class="font-bold text-forest block mt-0.5">{{ date('D, M d', strtotime($selectedStay->check_in_date)) }}</span>
                            <span class="text-[10px] text-emerald font-semibold">From 2:00 PM</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-paper/60 soft-border">
                            <span class="text-forest/40 block text-[9px] uppercase font-bold tracking-wider">Check-out</span>
                            <span class="font-bold text-forest block mt-0.5" id="hero-checkout-label">{{ date('D, M d', strtotime($selectedStay->check_out_date)) }}</span>
                            <span class="text-[10px] text-forest/50">Until 11:00 AM</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-paper/60 soft-border">
                            <span class="text-forest/40 block text-[9px] uppercase font-bold tracking-wider">Length of Stay</span>
                            @php
                                $d1 = \Carbon\Carbon::parse($selectedStay->check_in_date);
                                $d2 = \Carbon\Carbon::parse($selectedStay->check_out_date);
                                $stayNights = max(1, $d1->diffInDays($d2));
                            @endphp
                            <span class="font-bold text-forest block mt-0.5" id="hero-nights-label">{{ $stayNights }} Night{{ $stayNights > 1 ? 's' : '' }}</span>
                            <span class="text-[10px] text-forest/50">₹{{ number_format($selectedStay->nightly_rate) }}/night</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-paper/60 soft-border">
                            <span class="text-forest/40 block text-[9px] uppercase font-bold tracking-wider">Party Size</span>
                            <span class="font-bold text-forest block mt-0.5">{{ $selectedStay->adults }} Adult{{ $selectedStay->adults > 1 ? 's' : '' }}</span>
                            <span class="text-[10px] text-forest/50">{{ $selectedStay->children > 0 ? $selectedStay->children . ' Child' : 'No children' }}</span>
                        </div>
                    </div>

                    <!-- PHYSICAL ROOM NUMBER (STRICTLY GATED BY LIFECYCLE) -->
                    <div class="p-4 rounded-2xl {{ $isInHouse ? 'bg-mint/80 border border-emerald/20 text-forest' : 'bg-paper/40 soft-border text-forest/70' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $isInHouse ? 'bg-forest text-paper' : 'bg-white soft-border text-forest/40' }} flex items-center justify-center shrink-0 shadow-xs">
                                    <i data-lucide="{{ $isInHouse ? 'door-open' : 'lock' }}" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="text-[9px] uppercase font-bold tracking-wider text-forest/50 block">Physical Room Assignment</span>
                                    @if($isInHouse)
                                        <div class="font-bold text-forest text-base flex items-center gap-2">
                                            @if(!empty($assignedRooms))
                                                <span>{{ count($assignedRooms) > 1 ? 'Assigned Units: ' : 'Assigned Unit: ' }}</span>
                                                <span class="font-mono text-emerald font-extrabold bg-white px-2 py-0.5 rounded-lg border border-emerald/20">
                                                    {{ implode(', ', array_map(fn($r) => 'Villa ' . $r, $assignedRooms)) }}
                                                </span>
                                            @else
                                                <span class="font-mono text-emerald font-extrabold">Villa {{ $selectedStay->room->room_number ?? '101' }}</span>
                                            @endif
                                            <span class="text-[10px] bg-emerald-600 text-paper px-2 py-0.5 rounded-full font-bold uppercase">Active Key</span>
                                        </div>
                                        <p class="text-[10px] text-forest/60 mt-0.5">High-speed Wi-Fi: <strong class="text-forest">KrishnaGuest_{{ $selectedStay->branch->code ?? 'RESORT' }}</strong> (Password: <span class="font-mono">kerala{{ date('Y') }}</span>)</p>
                                    @elseif($isUpcoming)
                                        <div class="font-semibold text-forest text-xs">
                                            Room assigned upon physical check-in at front desk
                                        </div>
                                        <p class="text-[10px] text-forest/50 mt-0.5">Your reservation for {{ $selectedStay->roomType->name ?? 'Cottage' }} is 100% guaranteed.</p>
                                    @else
                                        <div class="font-semibold text-forest text-xs">
                                            Past Stay &middot; Unit: Villa {{ $selectedStay->room->room_number ?? '101' }}
                                        </div>
                                        <p class="text-[10px] text-forest/50 mt-0.5">Checked out on {{ date('M d, Y', strtotime($selectedStay->check_out_date)) }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($isInHouse)
                            <button onclick="openDashboardChat()" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-forest hover:bg-emerald text-paper text-xs font-bold transition">
                                <i data-lucide="bell" class="w-3.5 h-3.5 text-brass"></i>
                                <span>Room Service</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- PASS ACTIONS BAR -->
                <div class="pt-4 border-t border-forest/10 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('booking.confirmation', $selectedStay->booking_code) }}" 
                           class="px-4 py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-card transition flex items-center gap-1.5 touch-tap">
                            <i data-lucide="qr-code" class="w-3.5 h-3.5 text-brass"></i>
                            <span>View Stay Pass</span>
                        </a>
                        <a href="{{ route('rooms.show', $selectedStay->roomType->slug ?? 'cottage') }}" 
                           class="px-3.5 py-2.5 rounded-xl bg-paper hover:bg-white soft-border text-forest font-bold text-xs transition">
                            <span>Room Info</span>
                        </a>
                        @if(in_array($selectedStay->status, ['confirmed', 'checked_in']))
                        <button type="button" onclick="openClientCancelModal({{ $selectedStay->id }}, '{{ $selectedStay->booking_code }}')" 
                                class="px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 font-bold text-xs transition flex items-center gap-1.5 touch-tap">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5 text-rose-600"></i>
                            <span>Cancel Stay &amp; Cashback</span>
                        </button>
                        @elseif($selectedStay->status === 'cancelled')
                        <span class="px-3 py-1.5 rounded-xl bg-rose-100 text-rose-800 text-xs font-bold border border-rose-200 flex items-center gap-1.5">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            <span>Cancelled @if($selectedStay->refunded_amount > 0)(₹{{ number_format($selectedStay->refunded_amount) }} Cashback)@endif</span>
                        </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if($selectedStay->branch)
                        <a href="{{ $selectedStay->branch->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="px-3 py-2 rounded-xl bg-paper hover:bg-white soft-border text-forest text-xs font-semibold transition flex items-center gap-1.5" title="Get Driving Directions on Google Maps">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald"></i>
                            <span>View Map</span>
                        </a>
                        @endif
                        @php
                            $stayBranch = $selectedStay->branch;
                            $stayPhone = $stayBranch?->phone ?? \App\Models\Setting::get('resort_phone', '+91 94471 22334');
                            $cleanStayPhone = preg_replace('/[^0-9+]/', '', $stayPhone);
                        @endphp
                        @if($stayPhone)
                        <a href="tel:{{ $cleanStayPhone }}" class="px-3 py-2 rounded-xl bg-paper hover:bg-white soft-border text-forest text-xs font-semibold transition flex items-center gap-1.5" title="Call {{ $stayBranch ? $stayBranch->name : 'Front Desk' }}: {{ $stayPhone }}">
                            <i data-lucide="phone-call" class="w-3.5 h-3.5 text-emerald"></i>
                            <span>Call Desk: {{ $stayPhone }}</span>
                        </a>
                        @endif
                        <button onclick="openDashboardChat()" class="px-3.5 py-2 rounded-xl bg-mint hover:bg-emerald/20 text-emerald font-bold text-xs border border-emerald/20 transition flex items-center gap-1.5">
                            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                            <span>Concierge Desk</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- ========================================================
         SECTION 2: STAY EXTENSION REQUEST (LIFECYCLE GATED)
         ======================================================== -->
    <div class="bg-white rounded-[32px] p-6 sm:p-10 md:p-12 soft-border shadow-card">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-forest/10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-mint text-emerald flex items-center justify-center shrink-0">
                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="serif text-xl font-bold text-forest">Extend Your Stay in Paradise</h3>
                    <p class="text-xs text-forest/60 mt-0.5">Want to prolong your holiday? Check live availability or view your custom manager loyalty offer.</p>
                </div>
            </div>

            @if($isInHouse)
            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-3 py-1 rounded-full border border-emerald-200 uppercase tracking-wider self-start sm:self-auto">
                ⚡ Direct Manager Priority
            </span>
            @endif
        </div>

        @if($isInHouse)
            @php
                $activeExt = $selectedStay->latestExtensionRequest;
            @endphp

            @if($activeExt && in_array($activeExt->status, ['pending', 'approved']))
                <!-- EXTENSION STATUS DISPLAY -->
                @if($activeExt->status === 'pending')
                <div class="p-6 sm:p-8 rounded-3xl bg-amber-50/70 border border-amber-200/80 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-xs">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider text-amber-800/70">Request #EXT-{{ $activeExt->id }}</span>
                                <h4 class="serif text-lg font-bold text-amber-950">Extension Under Manager Loyalty Review</h4>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-200 text-amber-900 border border-amber-300 self-start sm:self-auto">
                            ⏳ Review In Progress
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="p-3 rounded-2xl bg-white/80 soft-border">
                            <span class="text-forest/50 block text-[9px] uppercase font-bold">Extended Check-out</span>
                            <span class="font-bold text-forest text-sm mt-0.5 block">{{ date('D, M d, Y', strtotime($activeExt->requested_checkout_date)) }}</span>
                            <span class="text-[10px] text-emerald font-semibold">+{{ $activeExt->extra_nights }} Night{{ $activeExt->extra_nights > 1 ? 's' : '' }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-white/80 soft-border">
                            <span class="text-forest/50 block text-[9px] uppercase font-bold">Allocation Type</span>
                            <span class="font-bold text-forest text-sm mt-0.5 block">{{ ucwords(str_replace('_', ' ', $activeExt->allocation_type)) }}</span>
                            <span class="text-[10px] text-forest/60">Units: {{ implode(', ', array_map(fn($id) => 'Villa ' . $id, $activeExt->allocated_room_ids ?? [])) }}</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-white/80 soft-border">
                            <span class="text-forest/50 block text-[9px] uppercase font-bold">Standard Estimate</span>
                            <span class="font-bold text-forest text-sm mt-0.5 block">₹{{ number_format($activeExt->standard_amount, 2) }}</span>
                            <span class="text-[10px] text-amber-700 font-semibold">Discount being prepared</span>
                        </div>
                    </div>

                    <p class="text-xs text-amber-900/80 leading-relaxed">
                        Namaste! The property manager has been notified of your request. A special loyalty discount will be posted to your folio shortly. If you have any urgent requests, please reach out via <button onclick="openDashboardChat()" class="underline font-bold text-forest hover:text-emerald">Concierge Chat</button>.
                    </p>
                </div>
                @elseif($activeExt->status === 'approved')
                <div class="p-6 sm:p-8 rounded-3xl bg-emerald-50/80 border border-emerald-200/90 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-700 text-paper flex items-center justify-center shadow-md">
                                <i data-lucide="sparkles" class="w-6 h-6 text-brass"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-800">Exclusive Resort Courtesy</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-emerald-200 text-emerald-900">
                                        {{ $activeExt->manager_discount_percentage > 0 ? $activeExt->manager_discount_percentage . '% OFF APPLIED' : 'APPROVED' }}
                                    </span>
                                </div>
                                <h4 class="serif text-xl font-bold text-forest">Stay Extension Approved! Checkout Extended</h4>
                            </div>
                        </div>

                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-600 text-paper flex items-center gap-1.5 self-start sm:self-auto shadow-xs">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>Dates Locked In</span>
                        </span>
                    </div>

                    <!-- Details banner -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-3.5 rounded-2xl bg-white/90 soft-border">
                            <span class="text-forest/50 block text-[9px] uppercase font-bold">New Check-out Date</span>
                            <span class="font-bold text-forest text-sm mt-0.5 block">{{ date('D, M d, Y', strtotime($activeExt->requested_checkout_date)) }}</span>
                            <span class="text-[10px] text-emerald font-semibold">+{{ $activeExt->extra_nights }} Night{{ $activeExt->extra_nights > 1 ? 's' : '' }}</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/90 soft-border">
                            <span class="text-forest/50 block text-[9px] uppercase font-bold">Standard Value</span>
                            <span class="font-semibold text-forest/60 text-sm mt-0.5 block line-through">₹{{ number_format($activeExt->standard_amount, 2) }}</span>
                            <span class="text-[10px] text-forest/50">Base tariff + GST</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/90 soft-border">
                            <span class="text-forest/50 block text-[9px] uppercase font-bold">Manager Loyalty Price</span>
                            <span class="font-extrabold text-emerald text-base mt-0.5 block">₹{{ number_format($activeExt->offered_amount, 2) }}</span>
                            <span class="text-[10px] text-emerald font-bold">Saved ₹{{ number_format(max(0, $activeExt->standard_amount - $activeExt->offered_amount), 2) }}</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/90 soft-border">
                            <span class="text-forest/50 block text-[9px] uppercase font-bold">Settlement Status</span>
                            @if($activeExt->payment_status === 'paid')
                            <span class="font-bold text-emerald text-sm mt-0.5 block flex items-center gap-1">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Paid &amp; Settled
                            </span>
                            <span class="text-[10px] text-emerald font-semibold">{{ ucfirst($activeExt->payment_method ?? 'Payment') }}</span>
                            @else
                            <span class="font-bold text-amber-700 text-sm mt-0.5 block flex items-center gap-1">
                                <i data-lucide="clock" class="w-4 h-4"></i> Pending Settlement
                            </span>
                            <span class="text-[10px] text-amber-800">On-hand or online</span>
                            @endif
                        </div>
                    </div>

                    @if($activeExt->manager_notes)
                    <div class="p-3.5 rounded-2xl bg-emerald-100/60 border border-emerald-300/60 text-xs text-emerald-950 flex items-start gap-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-emerald-700 shrink-0 mt-0.5"></i>
                        <div>
                            <span class="font-bold block">Resort Manager Note:</span>
                            <p class="text-emerald-900 mt-0.5">{{ $activeExt->manager_notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- In-House Flexible Settlement Bar -->
                    @if($activeExt->payment_status === 'pending')
                    <div class="pt-4 border-t border-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="text-xs text-emerald-900 leading-relaxed max-w-xl">
                            <p><strong>Flexible In-House Settlement:</strong> You can pay directly to resort staff (Cash, POS Card, or UPI) at any time before or during checkout, or pay securely online right now.</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="openOnHandPaymentNotice({{ $activeExt->offered_amount }})" class="px-4 py-2.5 rounded-xl bg-white hover:bg-paper soft-border text-forest text-xs font-bold transition flex items-center gap-1.5 touch-tap">
                                <i data-lucide="wallet" class="w-3.5 h-3.5 text-forest/70"></i>
                                <span>Pay On-Hand / At Checkout</span>
                            </button>
                            <button type="button" onclick="payExtensionOnline({{ $activeExt->id }}, {{ $activeExt->offered_amount }})" id="ext-online-pay-btn-{{ $activeExt->id }}" class="px-4 py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper text-xs font-bold transition shadow-xs flex items-center gap-1.5 touch-tap">
                                <i data-lucide="credit-card" class="w-3.5 h-3.5 text-brass"></i>
                                <span>Pay Online (₹{{ number_format($activeExt->offered_amount) }})</span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            @else
                <!-- NEW EXTENSION AVAILABILITY & SMART RE-ALLOCATION WIZARD -->
                <div class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        <div class="lg:col-span-7 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Current Departure Date</label>
                                    <input type="text" disabled value="{{ date('D, M d, Y', strtotime($selectedStay->check_out_date)) }}" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 soft-border text-xs text-forest font-semibold cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Requested Extended Check-out *</label>
                                    @php
                                        $minDate = \Carbon\Carbon::parse($selectedStay->check_out_date)->addDay()->format('Y-m-d');
                                        $defaultNewDate = \Carbon\Carbon::parse($selectedStay->check_out_date)->addDay()->format('Y-m-d');
                                    @endphp
                                    <input type="date" id="ext-new-checkout" min="{{ $minDate }}" value="{{ $defaultNewDate }}" onchange="checkExtensionAvailability({{ $selectedStay->id }})" class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest font-bold focus:ring-1 focus:ring-forest focus:outline-hidden">
                                </div>
                            </div>

                            <!-- Quick +1, +2, +3 Nights Tap Pills -->
                            <div>
                                <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/50 mb-1.5">Quick Add Nights:</label>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="quickSelectExtensionNights({{ $selectedStay->id }}, 1)" class="px-3 py-1.5 rounded-xl bg-paper hover:bg-forest hover:text-paper soft-border text-xs font-semibold text-forest transition touch-tap">
                                        +1 Night
                                    </button>
                                    <button type="button" onclick="quickSelectExtensionNights({{ $selectedStay->id }}, 2)" class="px-3 py-1.5 rounded-xl bg-paper hover:bg-forest hover:text-paper soft-border text-xs font-semibold text-forest transition touch-tap">
                                        +2 Nights
                                    </button>
                                    <button type="button" onclick="quickSelectExtensionNights({{ $selectedStay->id }}, 3)" class="px-3 py-1.5 rounded-xl bg-paper hover:bg-forest hover:text-paper soft-border text-xs font-semibold text-forest transition touch-tap">
                                        +3 Nights
                                    </button>
                                </div>
                            </div>

                            <!-- Dynamic Live Availability Engine Container -->
                            <div id="ext-availability-container" class="space-y-3 pt-2">
                                <div id="ext-checking-spinner" class="hidden py-4 text-center text-xs text-forest/60">
                                    <div class="w-5 h-5 border-2 border-forest/30 border-t-forest rounded-full animate-spin mx-auto mb-1"></div>
                                    <span>Scanning cottage &amp; villa availability...</span>
                                </div>

                                <!-- Result Box: Same Room or Smart Recommendations -->
                                <div id="ext-availability-results" class="space-y-3"></div>
                            </div>

                            <div>
                                <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Special Notes for Branch Manager (Optional)</label>
                                <input type="text" id="ext-notes" placeholder="e.g. Flight in the late evening, celebrate anniversary..." class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                            </div>
                        </div>

                        <!-- Summary & Submit Box -->
                        <div class="lg:col-span-5 bg-paper/50 rounded-2xl p-5 soft-border space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-forest/10">
                                <span class="text-xs font-bold text-forest">Estimated Additional Charges</span>
                                <span class="text-[10px] text-forest/50" id="ext-rate-label">Rate: ₹{{ number_format($selectedStay->nightly_rate) }}/nt</span>
                            </div>

                            <div class="space-y-2 text-xs text-forest/70">
                                <div class="flex justify-between">
                                    <span>Extended Nights:</span>
                                    <span id="ext-est-nights" class="font-bold text-forest">1 Night</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Selected Allocation:</span>
                                    <span id="ext-selected-allocation-label" class="font-bold text-forest truncate max-w-[180px]">Checking...</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Subtotal:</span>
                                    <span id="ext-est-subtotal" class="font-bold text-forest">₹{{ number_format($selectedStay->nightly_rate) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Applicable GST (12%):</span>
                                    <span id="ext-est-tax" class="font-bold text-forest">₹{{ number_format(round($selectedStay->nightly_rate * 0.12, 2)) }}</span>
                                </div>
                                <div class="flex justify-between font-bold text-sm text-forest pt-2 border-t border-forest/10">
                                    <span>Estimated Standard Total:</span>
                                    <span id="ext-est-total" class="text-emerald font-extrabold text-base">₹{{ number_format(round($selectedStay->nightly_rate * 1.12, 2)) }}</span>
                                </div>
                            </div>

                            <button onclick="submitStayExtensionRequest({{ $selectedStay->id }})" id="ext-submit-btn" class="w-full py-3.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-2 shadow-card transition touch-tap">
                                <i data-lucide="send" class="w-3.5 h-3.5 text-brass"></i>
                                <span>Request Extension &amp; Loyalty Discount</span>
                            </button>
                            <p class="text-[10px] text-center text-forest/50 leading-relaxed">
                                Once submitted, manager reviews and applies a discounted rate. You may settle on-hand or at checkout anytime.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @elseif($isUpcoming)
        <!-- UPCOMING STAY NOTICE -->
        <div class="p-6 rounded-2xl bg-paper/40 soft-border text-center max-w-xl mx-auto space-y-2">
            <i data-lucide="calendar-clock" class="w-7 h-7 text-amber-700 mx-auto"></i>
            <h4 class="serif text-base font-bold text-forest">Stay Extension Available Upon Check-In</h4>
            <p class="text-xs text-forest/60">
                In-stay checkout extensions can be requested anytime after your physical arrival. If you need pre-arrival booking modifications or early check-in, please reach out to our concierge desk.
            </p>
            <button onclick="openDashboardChat()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white soft-border text-forest text-xs font-bold hover:bg-forest hover:text-paper transition mt-2">
                <i data-lucide="message-circle" class="w-3.5 h-3.5 text-emerald"></i>
                <span>Ask Concierge About Dates</span>
            </button>
        </div>
        @else
        <!-- COMPLETED STAY -->
        <div class="p-6 rounded-2xl bg-paper/40 soft-border text-center max-w-xl mx-auto space-y-2">
            <i data-lucide="check-circle" class="w-7 h-7 text-forest/40 mx-auto"></i>
            <h4 class="serif text-base font-bold text-forest">This Holiday Has Concluded</h4>
            <p class="text-xs text-forest/60">
                You checked out on {{ date('M d, Y', strtotime($selectedStay->check_out_date)) }}. We would be thrilled to welcome you back!
            </p>
            <a href="{{ route('rooms.index', ['branch_id' => $selectedStay->branch_id]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-forest text-paper text-xs font-bold hover:bg-emerald transition mt-2">
                <span>Book This Cottage Again</span>
            </a>
        </div>
        @endif
    </div>

    @if($selectedStay)
    <!-- ========================================================
         SECTION 2.5: BOOK RESORT FACILITIES & IN-HOUSE EXPERIENCES
         ======================================================== -->
    <div class="bg-white rounded-[32px] p-6 sm:p-10 md:p-12 soft-border shadow-card space-y-8 relative overflow-hidden" id="experiences-section">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-forest/10">
            <div class="flex items-start gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-forest/5 text-emerald flex items-center justify-center shrink-0 shadow-xs border border-forest/10">
                    <i data-lucide="sparkles" class="w-6 h-6 text-brass"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] uppercase font-bold text-forest/50 tracking-wider">Staying Guest Privilege</span>
                        @if(isset($isInHouse) && $isInHouse)
                        <span class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full border border-emerald-300 uppercase tracking-wider">
                            ✓ In-House Booking Active
                        </span>
                        @else
                        <span class="text-[9px] bg-amber-100 text-amber-800 font-semibold px-2.5 py-0.5 rounded-full border border-amber-200">
                            Unlocks Upon Cottage Check-in
                        </span>
                        @endif
                    </div>
                    <h3 class="serif text-2xl sm:text-3xl font-bold text-forest">Resort Facilities &amp; Curated Experiences</h3>
                    <p class="text-xs sm:text-sm text-forest/65 mt-1 max-w-xl leading-relaxed">
                        Mindful activities, Ayurvedic wellness sessions, and plantation trails. Request your preferred experience and our resort team will allocate your scheduled time slot.
                    </p>
                </div>
            </div>

            @if(isset($isInHouse) && $isInHouse)
                <div class="text-xs text-forest/60 bg-paper px-3.5 py-2 rounded-xl border border-forest/10 flex items-center gap-2 shrink-0">
                    <i data-lucide="clock" class="w-4 h-4 text-emerald"></i>
                    <span>Manager schedules timing after request</span>
                </div>
            @endif
        </div>

        <!-- ACTIVE BOOKED FACILITIES TRAY (If any booked) -->
        @if(isset($myFacilityBookings) && $myFacilityBookings->isNotEmpty())
        <div class="p-5 rounded-2xl bg-paper/60 border border-forest/10 space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="serif text-base font-bold text-forest flex items-center gap-2">
                    <i data-lucide="calendar-check" class="w-4 h-4 text-emerald"></i>
                    <span>Your Booked Experiences &amp; Facilities</span>
                </h4>
                <span class="text-[11px] font-semibold text-forest/50">{{ $myFacilityBookings->count() }} Bookings</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($myFacilityBookings as $bk)
                @php
                    $bStatusColor = match($bk->status) {
                        'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'completed' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'cancelled' => 'bg-gray-100 text-gray-600 border-gray-300',
                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                    };
                    $bStatusLabel = match($bk->status) {
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default => 'Pending Staff Allocation',
                    };
                @endphp
                <div class="bg-white rounded-xl p-4 soft-border shadow-xs flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h5 class="font-bold text-sm text-forest">{{ $bk->facility ? $bk->facility->name : 'Resort Experience' }}</h5>
                                <div class="text-[11px] text-forest/60 mt-0.5 flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3 text-brass"></i>
                                    <span>{{ \Carbon\Carbon::parse($bk->booking_date)->format('D, d M Y') }} &middot; {{ $bk->guests_count }} {{ Str::plural('Guest', $bk->guests_count) }}</span>
                                </div>
                            </div>
                            <span class="text-[9px] uppercase font-bold px-2 py-0.5 rounded-full border {{ $bStatusColor }}">
                                {{ $bStatusLabel }}
                            </span>
                        </div>

                        <!-- ALLOCATED TIME SLOT BADGE -->
                        <div class="mt-3 pt-2.5 border-t border-forest/10">
                            @if($bk->allocated_time_slot)
                                <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-200">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>Time Slot: {{ $bk->allocated_time_slot }}</span>
                                </div>
                            @elseif($bk->facility && $bk->facility->has_scheduling)
                                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-amber-800 bg-amber-50 px-2.5 py-1.5 rounded-lg border border-amber-200">
                                    <i data-lucide="loader" class="w-3.5 h-3.5 text-amber-600 animate-spin"></i>
                                    <span>Slot: Staff will allocate your timing shortly</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 text-[11px] font-medium text-forest/70 bg-paper px-2.5 py-1.5 rounded-lg">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald"></i>
                                    <span>Open Access during operating hours</span>
                                </div>
                            @endif
                        </div>

                        @if($bk->notes)
                            <p class="text-[11px] text-forest/50 italic mt-1.5">"{{ $bk->notes }}"</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between text-xs pt-2 border-t border-forest/10">
                        <span class="font-bold text-forest">
                            {{ $bk->total_amount > 0 ? '₹' . number_format($bk->total_amount, 0) : 'Complimentary' }}
                        </span>

                        @if($bk->status === 'pending')
                            <button type="button" onclick="cancelFacilityBooking({{ $bk->id }})" class="text-rose-600 hover:text-rose-800 text-[11px] font-semibold hover:underline">
                                Cancel
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- BOOKABLE EXPERIENCES CARDS -->
        <div>
            @if(isset($bookableFacilities) && $bookableFacilities->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($bookableFacilities as $fac)
                    <div class="bg-paper/40 rounded-2xl border border-forest/10 overflow-hidden flex flex-col justify-between hover:shadow-card transition duration-300 group">
                        <div>
                            <div class="h-44 w-full bg-mint relative overflow-hidden">
                                <img src="{{ $fac->image_url ?: 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80' }}" 
                                     alt="{{ $fac->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                
                                <span class="absolute top-2.5 left-2.5 bg-forest/90 text-paper text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                    {{ $fac->branch ? $fac->branch->name : 'All Branches' }}
                                </span>

                                <span class="absolute top-2.5 right-2.5 bg-white/95 text-forest text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-xs">
                                    {{ $fac->rate > 0 ? '₹' . number_format($fac->rate, 0) : 'Complimentary' }}
                                </span>
                            </div>

                            <div class="p-5 space-y-2">
                                <h4 class="serif text-lg font-bold text-forest">{{ $fac->name }}</h4>
                                <p class="text-xs text-forest/70 leading-relaxed line-clamp-2">
                                    {{ $fac->description ?: $fac->short_description ?: 'Restorative wellness and estate activities crafted for mindful relaxation.' }}
                                </p>
                                <div class="text-[11px] text-forest/50 flex items-center gap-1 pt-1">
                                    <i data-lucide="clock" class="w-3 h-3 text-emerald"></i>
                                    <span>{{ $fac->operating_hours ?? 'Open Daily' }}</span>
                                    @if($fac->has_scheduling)
                                        <span class="ml-auto text-emerald-700 font-semibold">&bull; Staff Scheduled</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-5 pt-0">
                            @if(isset($isInHouse) && $isInHouse)
                                <button type="button" 
                                        onclick="openFacilityBookingModal({{ $fac->id }}, '{{ addslashes($fac->name) }}', {{ (float)$fac->rate }}, {{ $fac->has_scheduling ? 1 : 0 }})" 
                                        class="w-full py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-xs">
                                    <i data-lucide="calendar-plus" class="w-4 h-4 text-brass"></i>
                                    <span>Book Experience</span>
                                </button>
                            @else
                                <button type="button" disabled class="w-full py-2.5 rounded-xl bg-forest/10 text-forest/40 font-bold text-xs flex items-center justify-center gap-1.5 cursor-not-allowed">
                                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                    <span>Available at Check-in</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-paper/30 rounded-2xl border border-dashed border-forest/20">
                    <i data-lucide="sparkles" class="w-8 h-8 text-brass mx-auto mb-2 opacity-70"></i>
                    <p class="text-xs text-forest/60 font-semibold">No bookable facilities listed for this branch currently.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ========================================================
         SECTION 3: 1-TAP IN-VILLA ORDERING (SLOW MARQUEE + DRAWER)
         ======================================================== -->
    <div class="bg-white rounded-[32px] p-6 sm:p-10 md:p-12 soft-border shadow-card space-y-8 relative overflow-hidden" id="ordering-section">
        <!-- Subtle decorative glow -->
        <div class="absolute -right-20 -top-20 w-72 h-72 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none"></div>

        <!-- Section Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-forest/10 relative z-10">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-mint text-emerald flex items-center justify-center shrink-0 shadow-xs">
                    <i data-lucide="utensils" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] uppercase font-bold text-forest/50 tracking-wider">Hearth to Cottage Service</span>
                        @if(isset($isInHouse) && $isInHouse)
                        <span class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full border border-emerald-300 uppercase tracking-wider">
                            ⚡ In-House Folio Active
                        </span>
                        @else
                        <span class="text-[9px] bg-amber-100 text-amber-800 font-semibold px-2.5 py-0.5 rounded-full border border-amber-200">
                            In-Villa Kitchen & Spice Ordering Unlocks at Check-in
                        </span>
                        @endif
                    </div>
                    <h3 class="serif text-2xl sm:text-3xl font-bold text-forest">1-Tap In-Villa Ordering</h3>
                    <p class="text-xs sm:text-sm text-forest/65 mt-1 max-w-xl leading-relaxed">
                        Savor freshly cooked Kerala plantation delicacies and cold-pressed estate spices. Choose any dish or farm harvest to order directly to your villa.
                    </p>
                </div>
            </div>

            <!-- Controls: Category Tabs & < > Arrow Nav Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <!-- Tab Switcher (Dining vs Spices) -->
                <div class="flex items-center p-1.5 rounded-2xl bg-paper soft-border text-xs">
                    <button type="button" onclick="switchQuickOrderTab('dining')" id="btn-tab-dining" class="px-4 py-2 rounded-xl font-bold bg-white text-forest shadow-xs transition touch-tap flex items-center gap-1.5">
                        <i data-lucide="utensils" class="w-3.5 h-3.5 text-emerald"></i>
                        <span>Dining & Kitchen</span>
                    </button>
                    <button type="button" onclick="switchQuickOrderTab('spices')" id="btn-tab-spices" class="px-4 py-2 rounded-xl font-semibold text-forest/60 hover:text-forest transition touch-tap flex items-center gap-1.5">
                        <i data-lucide="leaf" class="w-3.5 h-3.5 text-forest/50"></i>
                        <span>Estate Spices</span>
                    </button>
                </div>

                <!-- Manual < > Arrow Navigation Buttons -->
                <div class="flex items-center gap-1.5 bg-paper soft-border p-1 rounded-xl">
                    <button type="button" onclick="scrollActiveMarquee(-340)" class="w-8 h-8 rounded-lg bg-white hover:bg-forest hover:text-paper soft-border text-forest flex items-center justify-center transition touch-tap shadow-xs" title="Scroll Left" aria-label="Previous items">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="scrollActiveMarquee(340)" class="w-8 h-8 rounded-lg bg-white hover:bg-forest hover:text-paper soft-border text-forest flex items-center justify-center transition touch-tap shadow-xs" title="Scroll Right" aria-label="Next items">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- View Full Menu / Store Link -->
                <a href="{{ route('dining.index') }}" id="link-view-all" class="px-3.5 py-2 rounded-xl bg-forest/5 hover:bg-forest hover:text-paper text-forest text-xs font-bold transition flex items-center gap-1">
                    <span>Full Menu</span>
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
                </a>
            </div>
        </div>

        <!-- DINING MARQUEE PANEL -->
        <div id="quick-order-dining-panel" class="space-y-4">
            <div class="flex items-center justify-between text-xs px-1 text-forest/60">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald animate-pulse"></span>
                    <span class="font-medium">Continuous slow marquee &mdash; hover mouse to pause, tap &lt; &gt; or drag to explore</span>
                </span>
                <span class="text-[11px] font-semibold text-forest/50 hidden sm:inline">5% Restaurant GST &bull; Freshly Prepared</span>
            </div>

            <!-- Slow Marquee Container with edge fades -->
            <div class="relative overflow-hidden rounded-2xl group/marquee">
                <!-- Left gradient mask -->
                <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-10 sm:w-16 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
                <!-- Right gradient mask -->
                <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-10 sm:w-16 bg-gradient-to-l from-white via-white/80 to-transparent z-10"></div>

                <!-- Track (overflow-x-auto, continuous smooth scroll) -->
                <div id="dining-marquee-track" 
                     class="marquee-track flex items-stretch gap-5 overflow-x-auto no-scrollbar py-3 px-2 sm:px-4 cursor-grab active:cursor-grabbing select-none"
                     style="scroll-behavior: auto;">
                    @forelse($diningItems as $item)
                    <div class="w-[280px] sm:w-[320px] shrink-0 rounded-[22px] soft-border bg-[#FAF8F5] hover:bg-white hover:shadow-xl transition-all duration-300 p-4 flex flex-col justify-between group">
                        <div>
                            <!-- Photo & Badges -->
                            <div class="h-44 w-full rounded-2xl overflow-hidden relative mb-3 bg-mint group-hover:scale-[1.02] transition duration-500">
                                <img src="{{ $item->image_url ?: 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=600&q=80' }}" 
                                     alt="{{ $item->name }}" 
                                     class="w-full h-full object-cover">
                                <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                                    <span class="bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-bold text-forest uppercase tracking-wider flex items-center gap-1 shadow-xs">
                                        <span class="w-2 h-2 rounded-full {{ $item->is_vegetarian ? 'bg-emerald' : 'bg-rose-600' }}"></span>
                                        <span>{{ $item->is_vegetarian ? 'Veg' : 'Non-Veg' }}</span>
                                    </span>
                                </div>
                                <div class="absolute bottom-2.5 right-2.5 bg-forest/90 backdrop-blur-md text-brass font-bold text-xs px-2.5 py-1 rounded-lg font-mono shadow-xs">
                                    ₹{{ number_format($item->price) }}
                                </div>
                            </div>

                            <!-- Dish Info -->
                            <div class="space-y-1">
                                <h4 class="font-bold text-forest text-sm group-hover:text-emerald transition line-clamp-1">{{ $item->name }}</h4>
                                <p class="text-[11px] text-forest/65 line-clamp-2 leading-relaxed">
                                    {{ $item->short_description ?: 'Traditional Kerala specialty prepared in earthen pots with fragrant plantation spices.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Button: Place Order -->
                        <div class="pt-3 mt-2 border-t border-forest/10 flex items-center justify-between gap-2">
                            <span class="text-[10px] text-forest/50 font-medium">In-Villa Service</span>
                            <button type="button" 
                                    onclick="openOrderDrawer('dining', {{ $item->id }}, '{{ addslashes($item->name) }}', {{ (float) $item->price }}, '{{ addslashes($item->image_url ?: '') }}')" 
                                    class="px-4 py-2 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center gap-1.5 shadow-xs transition touch-tap">
                                <i data-lucide="plus" class="w-3.5 h-3.5 text-brass"></i>
                                <span>Place Order</span>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-forest/50 py-10 text-center w-full">Dining items loading...</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- SPICES MARQUEE PANEL -->
        <div id="quick-order-spices-panel" class="hidden space-y-4">
            <div class="flex items-center justify-between text-xs px-1 text-forest/60">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald animate-pulse"></span>
                    <span class="font-medium">Continuous slow marquee &mdash; hover mouse to pause, tap &lt; &gt; or drag to explore</span>
                </span>
                <span class="text-[11px] font-semibold text-forest/50 hidden sm:inline">100% Plantation Fresh &bull; Vacuum Packed</span>
            </div>

            <!-- Slow Marquee Container with edge fades -->
            <div class="relative overflow-hidden rounded-2xl group/marquee">
                <!-- Left gradient mask -->
                <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-10 sm:w-16 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
                <!-- Right gradient mask -->
                <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-10 sm:w-16 bg-gradient-to-l from-white via-white/80 to-transparent z-10"></div>

                <!-- Track -->
                <div id="spices-marquee-track" 
                     class="marquee-track flex items-stretch gap-5 overflow-x-auto no-scrollbar py-3 px-2 sm:px-4 cursor-grab active:cursor-grabbing select-none"
                     style="scroll-behavior: auto;">
                    @forelse($spiceProducts as $sp)
                    <div class="w-[280px] sm:w-[320px] shrink-0 rounded-[22px] soft-border bg-[#FAF8F5] hover:bg-white hover:shadow-xl transition-all duration-300 p-4 flex flex-col justify-between group">
                        <div>
                            <!-- Photo & Badges -->
                            <div class="h-44 w-full rounded-2xl overflow-hidden relative mb-3 bg-mint group-hover:scale-[1.02] transition duration-500">
                                <img src="{{ $sp->image_url ?: 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=600&q=80' }}" 
                                     alt="{{ $sp->name }}" 
                                     class="w-full h-full object-cover">
                                <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                                    <span class="bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-bold text-emerald uppercase tracking-wider flex items-center gap-1 shadow-xs">
                                        <i data-lucide="leaf" class="w-3 h-3 text-emerald"></i>
                                        <span>Estate Harvest</span>
                                    </span>
                                </div>
                                <div class="absolute bottom-2.5 right-2.5 bg-forest/90 backdrop-blur-md text-brass font-bold text-xs px-2.5 py-1 rounded-lg font-mono shadow-xs">
                                    ₹{{ number_format($sp->selling_mode === 'loose' ? $sp->price_per_kg : $sp->price) }}{{ $sp->selling_mode === 'loose' ? '/kg' : '' }}
                                </div>
                            </div>

                            <!-- Spice Info -->
                            <div class="space-y-1">
                                <h4 class="font-bold text-forest text-sm group-hover:text-emerald transition line-clamp-1">{{ $sp->name }}</h4>
                                <p class="text-[11px] text-forest/65 line-clamp-2 leading-relaxed">
                                    {{ $sp->short_description ?: ($sp->weight_grams . 'g vacuum-sealed fresh organic harvest from our hillside plantations.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Button: Place Order -->
                        <div class="pt-3 mt-2 border-t border-forest/10 flex items-center justify-between gap-2">
                            <span class="text-[10px] text-forest/50 font-mono">
                                @if($sp->selling_mode === 'loose')
                                    Loose KG
                                @else
                                    {{ $sp->package_size ?? ($sp->weight_grams . 'g pack') }}
                                @endif
                            </span>
                            <button type="button" 
                                    onclick="openOrderDrawer('spices', {{ $sp->id }}, '{{ addslashes($sp->name) }}', {{ (float) ($sp->selling_mode === 'loose' ? $sp->price_per_kg : $sp->price) }}, '{{ addslashes($sp->image_url ?: '') }}')" 
                                    class="px-4 py-2 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center gap-1.5 shadow-xs transition touch-tap">
                                <i data-lucide="plus" class="w-3.5 h-3.5 text-brass"></i>
                                <span>Place Order</span>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-forest/50 py-10 text-center w-full">Spice products loading...</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


    <!-- ========================================================
         SECTION 4: NEAREST LOCATIONS & CAB EXCURSIONS FOR BOOKED BRANCH
         ======================================================== -->
    <div class="space-y-6" id="excursions">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-forest/50">Curated Local Experiences &amp; Travel</span>
                <h3 class="serif text-2xl font-bold text-forest mt-0.5">
                    Attractions Near {{ $selectedStay->branch ? $selectedStay->branch->name : 'Resort' }}
                </h3>
            </div>
            <div class="flex items-center gap-3">
                @if($isInHouse)
                <button type="button" onclick="openBookTaxiModal()" class="px-4 py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-card transition flex items-center gap-2 touch-tap">
                    <i data-lucide="car" class="w-4 h-4 text-brass"></i>
                    <span>Plan Excursion / Book Cab</span>
                </button>
                @endif
                <a href="{{ route('nearby.index', ['branch_id' => $selectedStay->branch_id]) }}" class="text-xs text-emerald font-semibold hover:underline flex items-center gap-1">
                    <span>View All</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        @if(isset($myTaxiRequests) && $myTaxiRequests->isNotEmpty())
        <!-- ACTIVE EXCURSIONS TRAY -->
        <div class="bg-paper/70 rounded-2xl p-4 sm:p-5 soft-border space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="compass" class="w-4 h-4 text-emerald"></i>
                    <h4 class="font-bold text-xs text-forest uppercase tracking-wider">Your Cab &amp; Excursion Bookings ({{ $myTaxiRequests->count() }})</h4>
                </div>
                @if($dashPhone)
                <a href="tel:{{ $dashCleanPhone }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white soft-border text-emerald hover:text-forest text-[11px] font-bold shadow-xs transition" title="Call Excursion Desk">
                    <i data-lucide="phone-call" class="w-3 h-3 text-emerald"></i>
                    <span>Call Desk: {{ $dashPhone }}</span>
                </a>
                @else
                <span class="text-[10px] text-forest/50">Managed by Concierge Desk</span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($myTaxiRequests as $taxi)
                <div class="bg-white rounded-xl p-4 soft-border shadow-xs space-y-2.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs text-forest">#{{ $taxi->booking_reference }}</span>
                            @php
                                $badgeCls = match($taxi->status) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'contacted' => 'bg-blue-100 text-blue-800',
                                    'confirmed' => 'bg-emerald-100 text-emerald-800',
                                    'completed' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $badgeCls }}">{{ $taxi->status }}</span>
                        </div>
                        <span class="text-[11px] font-mono text-forest/60">{{ date('d M Y', strtotime($taxi->pickup_date)) }} &middot; {{ $taxi->pickup_time ?: 'Morning' }}</span>
                    </div>

                    <div class="text-xs">
                        <span class="text-[10px] uppercase font-bold text-forest/50 block mb-1">Destinations Selected:</span>
                        <div class="flex flex-wrap gap-1">
                            @forelse($taxi->selected_locations as $dest)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-mint/50 text-forest soft-border">
                                    {{ $dest->name }}
                                </span>
                            @empty
                                <span class="text-xs text-forest/60 italic">Custom Sightseeing Route</span>
                            @endforelse
                        </div>
                        @if($taxi->extra_locations_notes)
                            <div class="text-[11px] text-amber-900 bg-amber-50/70 p-2 rounded-lg border border-amber-200/50 mt-2">
                                <strong>Custom Stops / Requests:</strong> {{ $taxi->extra_locations_notes }}
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-forest/10 text-xs">
                        <div>
                            <span class="text-[10px] text-forest/50">Fare:</span>
                            @if($taxi->estimated_fare)
                                <span class="font-mono font-bold text-forest ml-1">₹{{ number_format($taxi->estimated_fare) }}</span>
                                @if($taxi->folio_charge_id)
                                    <span class="text-[9px] font-bold text-emerald uppercase ml-1 bg-emerald/10 px-1.5 py-0.5 rounded">Billed to Folio</span>
                                @endif
                            @else
                                <span class="text-amber-700 italic text-[11px] ml-1">Staff will call/message with fare quote</span>
                            @endif
                        </div>
                        @if(in_array($taxi->status, ['pending', 'contacted']))
                        <button type="button" onclick="cancelCustomerTaxi({{ $taxi->id }})" class="text-[11px] font-semibold text-rose-600 hover:text-rose-800 transition">
                            Cancel
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($nearbyLocations as $spot)
            <div class="bg-white rounded-[24px] soft-border overflow-hidden shadow-card lift transition duration-300 flex flex-col justify-between group">
                <div>
                    <div class="h-44 w-full relative overflow-hidden bg-mint">
                        <img src="{{ $spot->image_url ?: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80' }}" 
                             alt="{{ $spot->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-bold text-forest uppercase">
                            {{ str_replace('wildlife', 'Wildlife Reserve', ucfirst($spot->category)) }}
                        </div>
                        <div class="absolute bottom-3 right-3 bg-forest/90 text-paper px-2.5 py-1 rounded-lg text-[10px] font-bold flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i>
                            <span>{{ $spot->distance_km ?? '3.5' }} km away</span>
                        </div>
                    </div>

                    <div class="p-5 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="serif text-lg font-bold text-forest group-hover:text-emerald transition">{{ $spot->name }}</h4>
                            @if($spot->is_taxi_available)
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 shrink-0">
                                    <i data-lucide="car" class="w-2.5 h-2.5 text-emerald-600"></i> Taxi Available
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[9px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200 shrink-0">
                                    No Cab
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-forest/70 line-clamp-2 leading-relaxed">
                            {{ $spot->description ?: 'Breathtaking scenic immersion with pristine trails and panoramic valley panoramas.' }}
                        </p>
                        <div class="text-[11px] text-forest/50 flex items-center gap-2 pt-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-brass"></i>
                            <span>Approx. {{ $spot->travel_time ?? '15 mins drive' }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 pt-0">
                    @if($isInHouse)
                        <button onclick="openBookTaxiModal({{ $spot->id }})" 
                                class="w-full py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-2 transition touch-tap">
                            <i data-lucide="car" class="w-3.5 h-3.5 text-brass"></i>
                            <span>Plan Cab Excursion to Here</span>
                        </button>
                    @else
                        <button onclick="requestConciergeTour('{{ addslashes($spot->name) }}')" 
                                class="w-full py-2.5 rounded-xl bg-paper hover:bg-forest hover:text-paper soft-border text-forest font-bold text-xs flex items-center justify-center gap-2 transition touch-tap">
                            <i data-lucide="compass" class="w-3.5 h-3.5 text-emerald"></i>
                            <span>Inquire with Concierge</span>
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-xs text-forest/50 col-span-3 text-center py-6">Attractions for this branch are being updated.</p>
            @endforelse
        </div>
    </div>


    <!-- ========================================================
         SECTION 5: AIRBNB-STYLE VERIFIED REVIEW & TESTIMONIAL
         (LIFECYCLE GATED - Checked-in or Completed Stays)
         ======================================================== -->
    <div class="bg-white rounded-[32px] p-6 sm:p-10 md:p-12 soft-border shadow-card">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-forest/10">
            <div class="w-11 h-11 rounded-2xl bg-mint text-emerald flex items-center justify-center shrink-0">
                <i data-lucide="star" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="serif text-xl font-bold text-forest">Verified Guest Review & Testimonial</h3>
                <p class="text-xs text-forest/60 mt-0.5">Your verified feedback helps fellow travelers choose their ideal Kerala retreat.</p>
            </div>
        </div>

        @if($hasReview)
        <!-- ALREADY REVIEWED -->
        <div class="p-6 rounded-2xl bg-paper/40 soft-border space-y-3 max-w-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    @for($i = 1; $i <= 5; $i++)
                    <i data-lucide="star" class="w-4 h-4 {{ $i <= $userReview->rating ? 'text-amber-500 fill-amber-500' : 'text-forest/20' }}"></i>
                    @endfor
                    <span class="text-xs font-bold text-forest ml-2">{{ $userReview->rating }}.0 / 5.0</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full {{ $userReview->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                    {{ $userReview->status === 'approved' ? 'Verified & Published' : 'Pending Manager Review' }}
                </span>
            </div>
            <h4 class="font-bold text-forest text-sm">{{ $userReview->title ?: 'Wonderful Experience' }}</h4>
            <p class="text-xs text-forest/70 leading-relaxed italic">"{{ $userReview->comment }}"</p>
            <span class="text-[10px] text-forest/40 block">Submitted on {{ $userReview->created_at->format('M d, Y') }}</span>
        </div>
        @elseif($isInHouse || $isCompleted)
        <!-- AIRBNB-STYLE SUBMISSION FORM -->
        <form onsubmit="submitVerifiedReview(event, {{ $selectedStay->id }})" class="space-y-5 max-w-2xl">
            @csrf
            <div>
                <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-2">Overall Holiday Rating *</label>
                <div class="flex items-center gap-2" id="star-rating-container">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" onclick="setStarRating({{ $i }})" class="p-1 text-forest/20 hover:text-amber-500 transition star-btn" data-star="{{ $i }}">
                        <i data-lucide="star" class="w-6 h-6"></i>
                    </button>
                    @endfor
                    <input type="hidden" id="rev-rating" value="5" required>
                    <span id="star-rating-label" class="text-xs font-bold text-emerald ml-3">5.0 - Exceptional</span>
                </div>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Headline / Title</label>
                <input type="text" id="rev-title" placeholder="e.g. Unforgettable family holiday amidst tea plantations" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Your Detailed Experience & Feedback *</label>
                <textarea id="rev-comment" required rows="4" placeholder="Tell us about the cottage comfort, clay-pot dining, tranquility, and staff hospitality..." class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden"></textarea>
            </div>

            <button type="submit" id="rev-submit-btn" class="px-6 py-3 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center gap-2 shadow-card transition touch-tap">
                <i data-lucide="check" class="w-3.5 h-3.5 text-brass"></i>
                <span>Submit Verified Stay Review</span>
            </button>
            <p class="text-[10px] text-forest/50 leading-relaxed">
                Verified reviews are moderated by our resort manager and appear on the room page.
            </p>
        </form>
        @else
        <!-- NOT ELIGIBLE YET -->
        <div class="p-6 rounded-2xl bg-paper/40 soft-border text-center max-w-xl mx-auto space-y-1">
            <h4 class="serif text-base font-bold text-forest">Review Unlocks After Check-in</h4>
            <p class="text-xs text-forest/60">
                To maintain authentic verified traveler ratings, guest reviews can be submitted after your arrival at the resort.
            </p>
        </div>
        @endif
    </div>


    <!-- ========================================================
         SECTION 6: RECENT ORDERS & FOLIO TRACKING
         ======================================================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Kitchen Orders -->
        <div class="bg-white rounded-[32px] p-6 sm:p-8 soft-border shadow-card space-y-5">
            <div class="flex items-center justify-between border-b border-forest/10 pb-3">
                <div class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-mint text-emerald"><i data-lucide="chef-hat" class="w-4 h-4"></i></span>
                    <h3 class="serif text-lg font-bold text-forest">Recent Dining Orders</h3>
                </div>
                <span class="text-xs text-forest/40 font-mono">{{ $foodOrders->count() }} Orders</span>
            </div>

            <div class="space-y-3.5" id="dashboard-food-orders-list">
                @forelse($foodOrders as $fo)
                <div class="p-4 rounded-2xl bg-paper/40 soft-border space-y-2.5 text-xs" id="customer-food-order-{{ $fo->id }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-bold text-forest text-sm">#{{ $fo->order_number }}</div>
                            <div class="text-[11px] text-forest/50 mt-0.5">
                                {{ $fo->table_number ?? 'In-Villa' }} &middot; {{ $fo->created_at ? $fo->created_at->diffForHumans() : 'Recent' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-forest text-sm block">₹{{ number_format($fo->total_amount) }}</span>
                            <!-- Live Status Tracker Badge -->
                            @if($fo->status === 'new')
                            <span class="inline-flex items-center gap-1 text-[10px] bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-md uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                Order Received
                            </span>
                            @elseif($fo->status === 'preparing')
                            <span class="inline-flex items-center gap-1 text-[10px] bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-md uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                Chef Cooking
                            </span>
                            @elseif($fo->status === 'ready')
                            <span class="inline-flex items-center gap-1 text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-md uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                Out for Delivery
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-md uppercase">
                                ✓ Delivered
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div class="text-[11px] text-forest/75 bg-white/70 p-2 rounded-xl border border-forest/5 flex flex-wrap gap-x-2 gap-y-0.5">
                        @foreach($fo->items as $it)
                        <span class="font-medium">&bull; {{ $it->quantity }}x {{ $it->item_name }}</span>
                        @endforeach
                    </div>

                    <!-- DYNAMIC INLINE REVIEW SYSTEM (When order is completed) -->
                    @if($fo->status === 'completed')
                        @if($fo->foodReview)
                        <!-- Already Reviewed -->
                        <div class="pt-2 border-t border-forest/10 flex items-center justify-between text-xs bg-white/50 p-2.5 rounded-xl">
                            <div class="flex items-center gap-1 text-amber-500">
                                @for($s = 1; $s <= 5; $s++)
                                <i data-lucide="star" class="w-3.5 h-3.5 {{ $s <= $fo->foodReview->rating ? 'fill-amber-400 text-amber-400' : 'text-forest/20' }}"></i>
                                @endfor
                                <span class="ml-1 font-bold text-forest text-[11px]">{{ $fo->foodReview->rating }}.0</span>
                                @if($fo->foodReview->comment)
                                <span class="text-forest/60 italic text-[11px] ml-1.5">"{{ Str::limit($fo->foodReview->comment, 30) }}"</span>
                                @endif
                            </div>
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full {{ $fo->foodReview->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $fo->foodReview->status === 'approved' ? '✓ Published' : '⚡ Review Submitted' }}
                            </span>
                        </div>
                        @else
                        <!-- In-Order Review Form -->
                        <div class="pt-2 border-t border-forest/10 space-y-2 bg-paper/80 p-3 rounded-xl soft-border" id="order-review-box-{{ $fo->id }}">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-forest flex items-center gap-1">
                                    <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500"></i>
                                    <span>Rate your meal experience:</span>
                                </span>
                                <div class="flex items-center gap-1" id="order-star-btns-{{ $fo->id }}">
                                    @for($s = 1; $s <= 5; $s++)
                                    <button type="button" onclick="setOrderRating({{ $fo->id }}, {{ $s }})" class="order-star-btn text-amber-400 hover:scale-110 transition p-0.5" data-star="{{ $s }}">
                                        <i data-lucide="star" class="w-4 h-4 fill-amber-400"></i>
                                    </button>
                                    @endfor
                                    <input type="hidden" id="order-rating-input-{{ $fo->id }}" value="5">
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" id="order-comment-input-{{ $fo->id }}" placeholder="Compliments to chef or feedback (optional)..." class="flex-1 px-3 py-1.5 rounded-lg bg-white soft-border text-xs text-forest focus:outline-hidden">
                                <button type="button" onclick="submitFoodOrderReview({{ $fo->id }})" class="px-3.5 py-1.5 rounded-lg bg-forest hover:bg-emerald text-paper font-bold text-xs shrink-0 transition touch-tap">
                                    Submit
                                </button>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
                @empty
                <p class="text-xs text-forest/50 text-center py-6">No kitchen orders placed during this visit.</p>
                @endforelse
            </div>
        </div>

        <!-- Spice Purchases -->
        <div class="bg-white rounded-[32px] p-6 sm:p-8 soft-border shadow-card space-y-5">
            <div class="flex items-center justify-between border-b border-forest/10 pb-3">
                <div class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-mint text-emerald"><i data-lucide="leaf" class="w-4 h-4"></i></span>
                    <h3 class="serif text-lg font-bold text-forest">Recent Spice Purchases</h3>
                </div>
                <span class="text-xs text-forest/40 font-mono">{{ $spiceOrders->count() }} Orders</span>
            </div>

            <div class="space-y-4">
                @forelse($spiceOrders as $spOrder)
                @php
                    $st = $spOrder->status;
                    $stepIdx = match($st) {
                        'processing', 'paid' => 1,
                        'packed' => 2,
                        'shipped' => 3,
                        'delivered' => 4,
                        default => 1
                    };
                @endphp
                <div class="p-4 sm:p-5 rounded-2xl bg-paper/40 soft-border space-y-3.5 text-xs">
                    <!-- Top Row: Order Number, Date & Total -->
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-forest text-sm">#{{ $spOrder->order_number }}</span>
                                @if($spOrder->delivery_mode === 'villa')
                                    <span class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-md uppercase">
                                        Villa Delivery ({{ $spOrder->room_number ?? 'In-House' }})
                                    </span>
                                @else
                                    <span class="text-[9px] bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-md uppercase">
                                        Courier ({{ $spOrder->shipping_city ?: 'India' }})
                                    </span>
                                @endif

                                @if($spOrder->refund_status === 'requested')
                                    <span class="text-[9px] bg-amber-100 text-amber-900 border border-amber-300 font-bold px-2 py-0.5 rounded-md uppercase flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3 text-amber-700"></i> Return Requested (₹{{ number_format($spOrder->refund_amount, 2) }})
                                    </span>
                                @elseif($spOrder->refund_status === 'approved')
                                    <span class="text-[9px] bg-blue-100 text-blue-900 border border-blue-300 font-bold px-2 py-0.5 rounded-md uppercase flex items-center gap-1">
                                        <i data-lucide="check-circle-2" class="w-3 h-3 text-blue-700"></i> Refund Approved (₹{{ number_format($spOrder->refund_amount, 2) }})
                                    </span>
                                @elseif($spOrder->refund_status === 'refunded')
                                    <span class="text-[9px] bg-purple-100 text-purple-900 border border-purple-300 font-bold px-2 py-0.5 rounded-md uppercase flex items-center gap-1">
                                        <i data-lucide="check-check" class="w-3 h-3 text-purple-700"></i> Refund Settled (₹{{ number_format($spOrder->refund_amount, 2) }})
                                    </span>
                                @elseif($spOrder->refund_status === 'rejected')
                                    <span class="text-[9px] bg-rose-100 text-rose-900 border border-rose-300 font-bold px-2 py-0.5 rounded-md uppercase flex items-center gap-1">
                                        <i data-lucide="x-circle" class="w-3 h-3 text-rose-700"></i> Return Declined
                                    </span>
                                @endif
                            </div>
                            <div class="text-[11px] text-forest/50 mt-1">
                                Ordered {{ $spOrder->created_at ? $spOrder->created_at->format('M d, Y · h:i A') : 'Recently' }}
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="font-bold text-forest text-sm block">₹{{ number_format($spOrder->total_amount) }}</span>
                            <span class="text-[10px] text-forest/50 font-mono">
                                {{ $spOrder->payment_status === 'paid' ? '✓ Paid' : '⚡ Billed / Pending' }}
                            </span>
                        </div>
                    </div>

                    <!-- Live Status Progression Stepper -->
                    <div class="p-3 bg-white rounded-xl border border-forest/5 space-y-2">
                        <div class="flex items-center justify-between text-[10px] font-bold text-forest/60">
                            <span class="{{ $stepIdx >= 1 ? 'text-emerald font-extrabold' : '' }}">1. Confirmed</span>
                            <span class="{{ $stepIdx >= 2 ? 'text-emerald font-extrabold' : '' }}">2. Packed</span>
                            <span class="{{ $stepIdx >= 3 ? 'text-emerald font-extrabold' : '' }}">3. Dispatched</span>
                            <span class="{{ $stepIdx >= 4 ? 'text-emerald font-extrabold' : '' }}">4. Delivered</span>
                        </div>
                        <div class="w-full bg-paper rounded-full h-1.5 overflow-hidden flex">
                            <div class="bg-emerald h-full transition-all duration-500 {{ $stepIdx === 1 ? 'w-1/4' : ($stepIdx === 2 ? 'w-2/4' : ($stepIdx === 3 ? 'w-3/4' : 'w-full')) }}"></div>
                        </div>
                    </div>

                    <!-- Tracking Courier Info (if dispatched) -->
                    @if($spOrder->tracking_number)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-mint/70 border border-emerald/20 text-[11px]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="truck" class="w-3.5 h-3.5 text-emerald"></i>
                            <span>{{ $spOrder->courier_partner ?: 'Blue Dart Express' }}:</span>
                            <strong class="font-mono text-forest">{{ $spOrder->tracking_number }}</strong>
                        </div>
                        <span class="text-[9px] bg-emerald text-paper font-bold px-2 py-0.5 rounded-md uppercase">In Transit</span>
                    </div>
                    @endif

                    <!-- Items List (Packets & Loose KG) -->
                    <div class="space-y-1 bg-white/70 p-2.5 rounded-xl border border-forest/5">
                        @foreach($spOrder->items as $it)
                        <div class="flex items-center justify-between text-[11px] text-forest/80">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald"></span>
                                <span class="font-medium text-forest">{{ $it->product_name }}</span>
                                <span class="text-forest/50 font-mono text-[10px]">
                                    @if($it->pricing_type === 'loose' || $it->weight_kg)
                                        ({{ $it->weight_display ?: number_format($it->weight_kg, 2) . ' kg loose' }})
                                    @else
                                        ({{ $it->weight_display ?: ($it->quantity . ' pack') }})
                                    @endif
                                </span>
                            </div>
                            <span class="font-semibold text-forest">₹{{ number_format($it->total_price) }}</span>
                        </div>
                        @endforeach
                    </div>

                    <!-- Bill Breakdown (Subtotal + GST + Shipping) -->
                    <div class="flex items-center justify-between text-[10px] text-forest/50 pt-1 border-t border-forest/5">
                        <span>Items: ₹{{ number_format($spOrder->subtotal) }}</span>
                        <span>GST (5%): ₹{{ number_format($spOrder->tax_amount ?: round($spOrder->subtotal * 0.05)) }}</span>
                        <span>Shipping: {{ $spOrder->shipping_charge > 0 ? '₹' . number_format($spOrder->shipping_charge) : 'FREE' }}</span>
                    </div>

                    <!-- Return Policy Guarantee & Customer Return Action Bar -->
                    <div class="pt-2 border-t border-forest/10 flex items-center justify-between gap-2 flex-wrap">
                        @if($spOrder->isEligibleForReturn())
                            <div class="text-[11px] text-forest/60">
                                @if(in_array($spOrder->status, ['pending', 'processing', 'paid', 'packed']))
                                    <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald"></i>
                                        100% Pre-Dispatch Cancellation Guarantee
                                    </span>
                                @else
                                    <span class="text-forest/70 font-semibold flex items-center gap-1">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald"></i>
                                        7-Day Freshness Return Guarantee
                                    </span>
                                @endif
                            </div>
                            <button type="button" 
                                    onclick="openClientSpiceReturnModal({{ $spOrder->id }}, '{{ $spOrder->order_number }}')" 
                                    class="px-3 py-1.5 rounded-xl bg-paper hover:bg-rose-50 border border-rose-200 text-rose-700 hover:text-rose-800 text-[11px] font-bold transition flex items-center gap-1.5 touch-tap">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>{{ in_array($spOrder->status, ['pending', 'processing', 'paid', 'packed']) ? 'Cancel Order & Refund' : 'Request Return & Refund' }}</span>
                            </button>
                        @elseif($spOrder->refund_status === 'requested')
                            <div class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 p-2.5 rounded-xl w-full flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-700 shrink-0"></i>
                                    <span>Return in review: <strong>{{ $spOrder->cancellation_reason ?: 'Customer requested return' }}</strong></span>
                                </div>
                                <span class="font-bold text-amber-900 shrink-0">₹{{ number_format($spOrder->refund_amount, 2) }}</span>
                            </div>
                        @elseif($spOrder->refund_status === 'approved')
                            <div class="text-[11px] text-blue-900 bg-blue-50 border border-blue-200 p-2.5 rounded-xl w-full flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-blue-700 shrink-0"></i>
                                    <span>Refund approved by estate manager. Cashback dispatch in progress.</span>
                                </div>
                                <span class="font-bold text-blue-900 shrink-0">₹{{ number_format($spOrder->refund_amount, 2) }}</span>
                            </div>
                        @elseif($spOrder->refund_status === 'refunded')
                            <div class="text-[11px] text-purple-900 bg-purple-50 border border-purple-200 p-2.5 rounded-xl w-full flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-check" class="w-3.5 h-3.5 text-purple-700 shrink-0"></i>
                                    <span>Refund completed on {{ $spOrder->returned_at ? $spOrder->returned_at->format('d M Y') : 'recently' }}.</span>
                                </div>
                                <span class="font-bold text-purple-900 shrink-0">₹{{ number_format($spOrder->refund_amount, 2) }} Refunded</span>
                            </div>
                        @elseif(!in_array($spOrder->status, ['cancelled']))
                            <div class="text-[11px] text-amber-900 bg-amber-50 border border-amber-200 p-2 rounded-xl w-full flex items-center gap-1.5 font-medium">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-amber-700 shrink-0"></i>
                                <span>This product does not have return policies.</span>
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-forest/50 text-center py-6">No spice shop orders recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    @else
    <!-- ========================================================
         BROWSE-ONLY STATE (GUEST HAS NO BOOKINGS)
         ======================================================== -->
    <div class="space-y-10">
        <!-- Welcome Card -->
        <div class="bg-white rounded-[28px] p-8 sm:p-12 soft-border shadow-card text-center max-w-2xl mx-auto space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-mint text-emerald flex items-center justify-center mx-auto">
                <i data-lucide="sparkles" class="w-8 h-8"></i>
            </div>
            <h3 class="serif text-2xl sm:text-3xl font-bold text-forest">Your Journey Awaits</h3>
            <p class="text-xs sm:text-sm text-forest/60 max-w-lg mx-auto leading-relaxed">
                You do not have any active or upcoming reservations linked to your account. Explore our hillside cottages, organic spice plantations, and Ayurvedic dining below to plan your tranquil escape.
            </p>
            <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('rooms.index') }}" class="px-6 py-3 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-card transition">
                    Browse All Cottages
                </a>
                <button onclick="openDashboardChat()" class="px-6 py-3 rounded-xl bg-paper hover:bg-white soft-border text-forest font-bold text-xs transition">
                    Ask Concierge for Guidance
                </button>
            </div>
        </div>

        <!-- Branches Carousel/Grid -->
        <div class="space-y-4">
            <h3 class="serif text-2xl font-bold text-forest text-center">Discover Our Plantation Branches</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($allBranches as $branch)
                <div class="bg-white rounded-[24px] soft-border overflow-hidden shadow-card lift transition flex flex-col justify-between">
                    <div>
                        <div class="h-44 w-full relative overflow-hidden bg-mint">
                            <img src="{{ $branch->hero_image_url ?: 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=600&q=80' }}" 
                                 alt="{{ $branch->name }}" 
                                 class="w-full h-full object-cover">
                            <div class="absolute top-3 left-3 bg-forest/90 text-paper text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase">
                                {{ $branch->city }}
                            </div>
                        </div>
                        <div class="p-5 space-y-2">
                            <h4 class="serif text-xl font-bold text-forest">{{ $branch->name }}</h4>
                            <p class="text-xs text-forest/70 line-clamp-2">{{ $branch->tagline ?: 'Authentic heritage retreat enveloped by cloud-kissed spice plantations.' }}</p>
                        </div>
                    </div>
                    <div class="p-5 pt-0 flex items-center gap-2">
                        <a href="{{ route('rooms.index', ['branch_id' => $branch->id]) }}" class="flex-1 py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-1.5 transition">
                            <span>Explore Cottages</span>
                            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
                        </a>
                        @if($branch->phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $branch->phone) }}" class="p-2.5 rounded-xl bg-paper hover:bg-white soft-border text-forest text-xs font-semibold transition flex items-center justify-center" title="Call {{ $branch->name }} Front Desk: {{ $branch->phone }}">
                            <i data-lucide="phone" class="w-4 h-4 text-emerald"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @if($spiceOrders->isNotEmpty())
        <!-- Spice Purchases for Non-Checked In Customer -->
        <div class="bg-white rounded-[32px] p-6 sm:p-8 soft-border shadow-card space-y-5">
            <div class="flex items-center justify-between border-b border-forest/10 pb-3">
                <div class="flex items-center gap-2">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-mint text-emerald"><i data-lucide="leaf" class="w-4 h-4"></i></span>
                    <h3 class="serif text-lg font-bold text-forest">Your Spice Orders</h3>
                </div>
                <span class="text-xs text-forest/40 font-mono">{{ $spiceOrders->count() }} Orders</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($spiceOrders as $spOrder)
                @php
                    $st = $spOrder->status;
                    $stepIdx = match($st) {
                        'processing', 'paid' => 1,
                        'packed' => 2,
                        'shipped' => 3,
                        'delivered' => 4,
                        default => 1
                    };
                @endphp
                <div class="p-4 rounded-2xl bg-paper/40 soft-border space-y-3.5 text-xs">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-forest text-sm">#{{ $spOrder->order_number }}</span>
                                <span class="text-[9px] bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded-md uppercase">
                                    Courier ({{ $spOrder->shipping_city ?: 'India' }})
                                </span>

                                @if($spOrder->refund_status === 'requested')
                                    <span class="text-[9px] bg-amber-100 text-amber-900 border border-amber-300 font-bold px-2 py-0.5 rounded-md uppercase flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3 text-amber-700"></i> Return Requested (₹{{ number_format($spOrder->refund_amount, 2) }})
                                    </span>
                                @elseif($spOrder->refund_status === 'approved')
                                    <span class="text-[9px] bg-blue-100 text-blue-900 border border-blue-300 font-bold px-2 py-0.5 rounded-md uppercase flex items-center gap-1">
                                        <i data-lucide="check-circle-2" class="w-3 h-3 text-blue-700"></i> Refund Approved (₹{{ number_format($spOrder->refund_amount, 2) }})
                                    </span>
                                @elseif($spOrder->refund_status === 'refunded')
                                    <span class="text-[9px] bg-purple-100 text-purple-900 border border-purple-300 font-bold px-2 py-0.5 rounded-md uppercase flex items-center gap-1">
                                        <i data-lucide="check-check" class="w-3 h-3 text-purple-700"></i> Refund Settled (₹{{ number_format($spOrder->refund_amount, 2) }})
                                    </span>
                                @elseif($spOrder->refund_status === 'rejected')
                                    <span class="text-[9px] bg-rose-100 text-rose-900 border border-rose-300 font-bold px-2 py-0.5 rounded-md uppercase flex items-center gap-1">
                                        <i data-lucide="x-circle" class="w-3 h-3 text-rose-700"></i> Return Declined
                                    </span>
                                @endif
                            </div>
                            <div class="text-[11px] text-forest/50 mt-1">
                                Ordered {{ $spOrder->created_at ? $spOrder->created_at->format('M d, Y · h:i A') : 'Recently' }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-bold text-forest text-sm block">₹{{ number_format($spOrder->total_amount) }}</span>
                            <span class="text-[10px] text-forest/50 font-mono">
                                {{ $spOrder->payment_status === 'paid' ? '✓ Paid' : '⚡ Billed / Pending' }}
                            </span>
                        </div>
                    </div>

                    <!-- Live Progress Stepper -->
                    <div class="p-3 bg-white rounded-xl border border-forest/5 space-y-2">
                        <div class="flex items-center justify-between text-[10px] font-bold text-forest/60">
                            <span class="{{ $stepIdx >= 1 ? 'text-emerald font-extrabold' : '' }}">1. Confirmed</span>
                            <span class="{{ $stepIdx >= 2 ? 'text-emerald font-extrabold' : '' }}">2. Packed</span>
                            <span class="{{ $stepIdx >= 3 ? 'text-emerald font-extrabold' : '' }}">3. Dispatched</span>
                            <span class="{{ $stepIdx >= 4 ? 'text-emerald font-extrabold' : '' }}">4. Delivered</span>
                        </div>
                        <div class="w-full bg-paper rounded-full h-1.5 overflow-hidden flex">
                            <div class="bg-emerald h-full transition-all duration-500 {{ $stepIdx === 1 ? 'w-1/4' : ($stepIdx === 2 ? 'w-2/4' : ($stepIdx === 3 ? 'w-3/4' : 'w-full')) }}"></div>
                        </div>
                    </div>

                    @if($spOrder->tracking_number)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-mint/70 border border-emerald/20 text-[11px]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="truck" class="w-3.5 h-3.5 text-emerald"></i>
                            <span>{{ $spOrder->courier_partner ?: 'Blue Dart' }}:</span>
                            <strong class="font-mono text-forest">{{ $spOrder->tracking_number }}</strong>
                        </div>
                        <span class="text-[9px] bg-emerald text-paper font-bold px-2 py-0.5 rounded-md uppercase">In Transit</span>
                    </div>
                    @endif

                    <div class="space-y-1 bg-white/70 p-2.5 rounded-xl border border-forest/5">
                        @foreach($spOrder->items as $it)
                        <div class="flex items-center justify-between text-[11px] text-forest/80">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald"></span>
                                <span class="font-medium text-forest">{{ $it->product_name }}</span>
                                <span class="text-forest/50 font-mono text-[10px]">
                                    @if($it->pricing_type === 'loose' || $it->weight_kg)
                                        ({{ $it->weight_display ?: number_format($it->weight_kg, 2) . ' kg loose' }})
                                    @else
                                        ({{ $it->weight_display ?: ($it->quantity . ' pack') }})
                                    @endif
                                </span>
                            </div>
                            <span class="font-semibold text-forest">₹{{ number_format($it->total_price) }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between text-[10px] text-forest/50 pt-1 border-t border-forest/5">
                        <span>Items: ₹{{ number_format($spOrder->subtotal) }}</span>
                        <span>GST (5%): ₹{{ number_format($spOrder->tax_amount ?: round($spOrder->subtotal * 0.05)) }}</span>
                        <span>Shipping: {{ $spOrder->shipping_charge > 0 ? '₹' . number_format($spOrder->shipping_charge) : 'FREE' }}</span>
                    </div>

                    <!-- Return Policy Guarantee & Customer Return Action Bar -->
                    <div class="pt-2 border-t border-forest/10 flex items-center justify-between gap-2 flex-wrap">
                        @if($spOrder->isEligibleForReturn())
                            <div class="text-[11px] text-forest/60">
                                @if(in_array($spOrder->status, ['pending', 'processing', 'paid', 'packed']))
                                    <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald"></i>
                                        100% Pre-Dispatch Cancellation Guarantee
                                    </span>
                                @else
                                    <span class="text-forest/70 font-semibold flex items-center gap-1">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald"></i>
                                        7-Day Freshness Return Guarantee
                                    </span>
                                @endif
                            </div>
                            <button type="button" 
                                    onclick="openClientSpiceReturnModal({{ $spOrder->id }}, '{{ $spOrder->order_number }}')" 
                                    class="px-3 py-1.5 rounded-xl bg-paper hover:bg-rose-50 border border-rose-200 text-rose-700 hover:text-rose-800 text-[11px] font-bold transition flex items-center gap-1.5 touch-tap">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>{{ in_array($spOrder->status, ['pending', 'processing', 'paid', 'packed']) ? 'Cancel Order & Refund' : 'Request Return & Refund' }}</span>
                            </button>
                        @elseif($spOrder->refund_status === 'requested')
                            <div class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 p-2.5 rounded-xl w-full flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-700 shrink-0"></i>
                                    <span>Return in review: <strong>{{ $spOrder->cancellation_reason ?: 'Customer requested return' }}</strong></span>
                                </div>
                                <span class="font-bold text-amber-900 shrink-0">₹{{ number_format($spOrder->refund_amount, 2) }}</span>
                            </div>
                        @elseif($spOrder->refund_status === 'approved')
                            <div class="text-[11px] text-blue-900 bg-blue-50 border border-blue-200 p-2.5 rounded-xl w-full flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-blue-700 shrink-0"></i>
                                    <span>Refund approved by estate manager. Cashback dispatch in progress.</span>
                                </div>
                                <span class="font-bold text-blue-900 shrink-0">₹{{ number_format($spOrder->refund_amount, 2) }}</span>
                            </div>
                        @elseif($spOrder->refund_status === 'refunded')
                            <div class="text-[11px] text-purple-900 bg-purple-50 border border-purple-200 p-2.5 rounded-xl w-full flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-check" class="w-3.5 h-3.5 text-purple-700 shrink-0"></i>
                                    <span>Refund completed on {{ $spOrder->returned_at ? $spOrder->returned_at->format('d M Y') : 'recently' }}.</span>
                                </div>
                                <span class="font-bold text-purple-900 shrink-0">₹{{ number_format($spOrder->refund_amount, 2) }} Refunded</span>
                            </div>
                        @elseif(!in_array($spOrder->status, ['cancelled']))
                            <div class="text-[11px] text-amber-900 bg-amber-50 border border-amber-200 p-2 rounded-xl w-full flex items-center gap-1.5 font-medium">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-amber-700 shrink-0"></i>
                                <span>This product does not have return policies.</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

</div>

<!-- ========================================================
     FACILITY BOOKING MODAL
     ======================================================== -->
<div id="modal-book-facility" class="hidden fixed inset-0 z-[130] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden border border-forest/10 animate-fadeIn">
        <div class="bg-forest text-paper px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4 text-brass"></i>
                <h3 class="serif text-base font-bold" id="fac-modal-title">Book Resort Experience</h3>
            </div>
            <button onclick="closeFacilityBookingModal()" class="text-paper/70 hover:text-paper p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-book-facility" onsubmit="submitFacilityBooking(event)" class="p-5 space-y-4 text-xs">
            @csrf
            <input type="hidden" id="fac-booking-facility-id" name="facility_id">
            <input type="hidden" id="fac-booking-reservation-id" name="reservation_id" value="{{ $selectedStay ? $selectedStay->id : '' }}">

            <div class="p-3 bg-paper rounded-xl border border-forest/10 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-forest/50">Experience Rate</span>
                    <span class="font-bold text-forest text-sm" id="fac-modal-rate-display">Complimentary</span>
                </div>
                <p class="text-[11px] text-forest/60 leading-tight" id="fac-modal-scheduling-note">
                    Our concierge team will allocate your scheduled time slot after booking.
                </p>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-forest mb-1">Booking Date *</label>
                <input type="date" 
                       name="booking_date" 
                       id="fac-booking-date" 
                       required 
                       min="{{ $selectedStay ? $selectedStay->check_in_date : date('Y-m-d') }}" 
                       max="{{ $selectedStay ? $selectedStay->check_out_date : '' }}" 
                       value="{{ $selectedStay ? (date('Y-m-d') >= $selectedStay->check_in_date && date('Y-m-d') <= $selectedStay->check_out_date ? date('Y-m-d') : $selectedStay->check_in_date) : date('Y-m-d') }}"
                       class="w-full px-3 py-2 rounded-xl border border-forest/20 text-forest text-xs focus:ring-1 focus:ring-forest focus:outline-hidden">
                <p class="text-[10px] text-forest/50 mt-1">Must be within your stay duration ({{ $selectedStay ? \Carbon\Carbon::parse($selectedStay->check_in_date)->format('d M') . ' to ' . \Carbon\Carbon::parse($selectedStay->check_out_date)->format('d M Y') : '' }}).</p>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-forest mb-1">Number of Guests *</label>
                <div class="flex items-center gap-3">
                    <input type="number" 
                           name="guests_count" 
                           id="fac-booking-guests" 
                           min="1" 
                           max="10" 
                           value="1" 
                           oninput="updateFacilityBookingTotal()" 
                           required 
                           class="w-24 px-3 py-2 rounded-xl border border-forest/20 text-forest text-xs focus:ring-1 focus:ring-forest focus:outline-hidden">
                    <span class="text-xs text-forest/70 font-semibold" id="fac-total-calc-display">Total: Complimentary</span>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-forest mb-1">Preferences &amp; Special Requests (Optional)</label>
                <textarea name="notes" 
                          id="fac-booking-notes" 
                          rows="2" 
                          placeholder="e.g. Prefer morning hours, celebration anniversary setup, ayurvedic oil preferences..." 
                          class="w-full px-3 py-2 rounded-xl border border-forest/20 text-forest text-xs focus:ring-1 focus:ring-forest focus:outline-hidden"></textarea>
            </div>

            <div class="pt-3 border-t border-forest/10 flex justify-end gap-2">
                <button type="button" onclick="closeFacilityBookingModal()" class="px-4 py-2 rounded-xl border border-forest/20 text-forest/70 hover:bg-forest/5 font-semibold text-xs">
                    Cancel
                </button>
                <button type="submit" id="btn-submit-fac-booking" class="px-5 py-2 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-xs transition">
                    Confirm Experience Booking
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================
     TAXI / CAB EXCURSION BOOKING MODAL
     ======================================================== -->
<div id="modal-book-taxi" class="hidden fixed inset-0 z-[130] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden border border-forest/10 animate-fadeIn flex flex-col max-h-[90vh]">
        <div class="bg-forest text-paper px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="car" class="w-4 h-4 text-brass"></i>
                <h3 class="serif text-base font-bold">Plan Excursion / Book Resort Cab</h3>
            </div>
            <button onclick="closeBookTaxiModal()" class="text-paper/70 hover:text-paper p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-book-taxi" onsubmit="submitTaxiBooking(event)" class="p-5 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="taxi-booking-reservation-id" name="reservation_id" value="{{ $selectedStay ? $selectedStay->id : '' }}">

            <div class="p-3 bg-paper rounded-xl border border-forest/10 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-forest/50">Resort Excursion Desk</span>
                    @if($dashPhone)
                    <a href="tel:{{ $dashCleanPhone }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald hover:underline" title="Call Excursion Desk">
                        <i data-lucide="phone-call" class="w-3 h-3"></i>
                        <span>Direct Call: {{ $dashPhone }}</span>
                    </a>
                    @else
                    <span class="text-[11px] font-bold text-emerald">Personalized Driver &amp; Fare</span>
                    @endif
                </div>
                <p class="text-[11px] text-forest/60 leading-tight">
                    Select nearby destinations or propose your own itinerary. Our front desk will contact you via phone or room intercom to confirm vehicle availability, transparent fare quote, and start time.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-forest mb-1">Pickup Date *</label>
                    <input type="date" 
                           name="pickup_date" 
                           id="taxi-pickup-date" 
                           required 
                           min="{{ $selectedStay ? $selectedStay->check_in_date : date('Y-m-d') }}" 
                           max="{{ $selectedStay ? $selectedStay->check_out_date : '' }}" 
                           value="{{ $selectedStay ? (date('Y-m-d') >= $selectedStay->check_in_date && date('Y-m-d') <= $selectedStay->check_out_date ? date('Y-m-d') : $selectedStay->check_in_date) : date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border border-forest/20 text-forest text-xs focus:ring-1 focus:ring-forest focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-forest mb-1">Pickup Time *</label>
                    <input type="text" 
                           name="pickup_time" 
                           id="taxi-pickup-time" 
                           required 
                           value="09:00 AM" 
                           placeholder="e.g. 09:00 AM or 02:30 PM"
                           class="w-full px-3 py-2 rounded-xl border border-forest/20 text-forest text-xs focus:ring-1 focus:ring-forest focus:outline-hidden">
                    <div class="flex items-center gap-1.5 mt-1.5 overflow-x-auto no-scrollbar">
                        <button type="button" onclick="setTaxiTime('07:00 AM')" class="px-2 py-0.5 rounded bg-forest/5 hover:bg-forest/10 text-[10px] text-forest/70 shrink-0">7:00 AM</button>
                        <button type="button" onclick="setTaxiTime('09:00 AM')" class="px-2 py-0.5 rounded bg-forest/5 hover:bg-forest/10 text-[10px] text-forest/70 shrink-0">9:00 AM</button>
                        <button type="button" onclick="setTaxiTime('02:00 PM')" class="px-2 py-0.5 rounded bg-forest/5 hover:bg-forest/10 text-[10px] text-forest/70 shrink-0">2:00 PM</button>
                        <button type="button" onclick="setTaxiTime('04:30 PM')" class="px-2 py-0.5 rounded bg-forest/5 hover:bg-forest/10 text-[10px] text-forest/70 shrink-0">4:30 PM</button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-forest mb-1">Number of Passengers *</label>
                <input type="number" 
                       name="passengers_count" 
                       id="taxi-passengers" 
                       min="1" 
                       max="20" 
                       value="{{ $selectedStay ? ($selectedStay->adults_count + $selectedStay->children_count) : 2 }}" 
                       required 
                       class="w-full px-3 py-2 rounded-xl border border-forest/20 text-forest text-xs focus:ring-1 focus:ring-forest focus:outline-hidden">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-forest mb-1.5">
                    Select Places to Visit (Multi-Select)
                </label>
                <div class="space-y-1.5 max-h-44 overflow-y-auto p-2 bg-paper/50 rounded-xl border border-forest/10">
                    @forelse($nearbyLocations->where('is_taxi_available', true) as $loc)
                    <label class="flex items-center justify-between p-2 rounded-lg bg-white border border-forest/5 hover:border-forest/20 cursor-pointer transition">
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" 
                                   name="selected_location_ids[]" 
                                   value="{{ $loc->id }}" 
                                   id="taxi-loc-check-{{ $loc->id }}" 
                                   class="taxi-location-checkbox rounded text-forest focus:ring-forest">
                            <div>
                                <span class="font-bold text-forest text-xs block">{{ $loc->name }}</span>
                                <span class="text-[10px] text-forest/50">{{ $loc->distance_km ?? '3' }} km &middot; {{ $loc->travel_time ?? '15 mins' }}</span>
                            </div>
                        </div>
                        <span class="text-[9px] uppercase font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full">Taxi Available</span>
                    </label>
                    @empty
                    <p class="text-[11px] text-forest/50 p-2 italic">No pre-listed destinations with taxi service. Feel free to specify custom destinations below.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-forest mb-1">Extra Custom Stops or Notes</label>
                <textarea name="extra_locations_notes" 
                          id="taxi-extra-notes" 
                          rows="2" 
                          placeholder="e.g. Stop at local tea factory, spice stall, scenic photography viewpoint, preference for Innova / SUV..." 
                          class="w-full px-3 py-2 rounded-xl border border-forest/20 text-forest text-xs focus:ring-1 focus:ring-forest focus:outline-hidden"></textarea>
            </div>

            <div class="pt-3 border-t border-forest/10 flex justify-end gap-2 shrink-0">
                <button type="button" onclick="closeBookTaxiModal()" class="px-4 py-2 rounded-xl border border-forest/20 text-forest/70 hover:bg-forest/5 font-semibold text-xs">
                    Cancel
                </button>
                <button type="submit" id="btn-submit-taxi-booking" class="px-5 py-2 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-xs transition">
                    Request Cab Quotation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================
     IN-HOUSE FOOD / SPICE ORDER SLIDE-OVER DRAWER (MATCHING DINING PAGE)
     ======================================================== -->
<div id="cart-drawer" class="hidden fixed inset-0 z-[120] bg-forest/40 backdrop-blur-sm flex justify-end">
    <div class="w-full sm:w-[480px] bg-paper h-full flex flex-col justify-between shadow-2xl overflow-y-auto">
        <!-- HEADER -->
        <div class="bg-forest text-paper p-5 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-brass">
                    <i data-lucide="utensils" class="w-5 h-5" id="drawer-header-icon"></i>
                </span>
                <div>
                    <h3 class="serif text-lg font-bold" id="drawer-header-title">Your Order Tray</h3>
                    <p class="text-[9px] uppercase tracking-widest text-paper/60" id="drawer-header-subtitle">In-Villa Service &amp; Checkout</p>
                </div>
            </div>
            <button onclick="closeCartDrawer()" class="text-paper/70 hover:text-paper p-2 rounded-xl hover:bg-white/10 transition" aria-label="Close tray">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- BODY: ITEMS LIST + GUEST ORDER FORM -->
        <div class="p-6 flex-1 space-y-6 overflow-y-auto">
            <!-- Tray items container -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-forest/50">Selected Items</span>
                    <button type="button" onclick="clearDrawerCart()" class="text-[11px] text-rose-600 font-semibold hover:underline">Clear Tray</button>
                </div>
                <div id="cart-items-container" class="space-y-3">
                    <!-- Dynamic Cart Items injected via JS -->
                </div>
            </div>

            <!-- ORDER & GUEST DETAILS FORM -->
            <div class="pt-4 border-t border-forest/10 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="serif text-base font-bold text-forest">Order &amp; Delivery Details</h4>
                    @if(isset($isInHouse) && $isInHouse)
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald bg-mint px-2.5 py-0.5 rounded-full">
                        In-House Folio Active
                    </span>
                    @endif
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Your Full Name *</label>
                    <input type="text" id="order-guest-name" value="{{ $user->name }}" placeholder="Your Name" class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Phone Number *</label>
                        <input type="tel" id="order-guest-phone" value="{{ $user->phone }}" placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Order Type *</label>
                        <select id="order-type" class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                            <option value="room_service" {{ isset($isInHouse) && $isInHouse ? 'selected' : '' }}>Room Service (In-Villa)</option>
                            <option value="dine_in">Restaurant Dining Table</option>
                            <option value="takeaway">Estate Picnic Takeaway</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Villa Unit / Table Number</label>
                    <input type="text" id="order-table-num" 
                           value="{{ isset($isInHouse) && $isInHouse && isset($selectedStay->room) ? 'Villa ' . $selectedStay->room->room_number : '' }}" 
                           placeholder="e.g. Villa 104 or Table 6" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Special Chef / Delivery Instructions</label>
                    <input type="text" id="order-instructions" placeholder="e.g. Less spicy, extra coconut chutney, evening delivery..." class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                </div>

                <!-- PAYMENT TAB / SELECTOR (AS REQUESTED) -->
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-2">Payment Method *</label>
                    <div class="grid grid-cols-1 gap-2.5" id="payment-method-group">
                        @if(isset($isInHouse) && $isInHouse)
                        <label class="payment-option p-3.5 rounded-xl bg-white soft-border flex items-center justify-between cursor-pointer border-2 border-emerald hover:border-emerald transition shadow-xs">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_choice" value="charge_to_room" checked class="accent-emerald w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-forest">Charge to Villa Folio</span>
                                    <span class="block text-[10px] text-forest/50">Settled conveniently upon checkout</span>
                                </div>
                            </div>
                            <span class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded uppercase">Recommended</span>
                        </label>
                        @endif

                        <label class="payment-option p-3.5 rounded-xl bg-white soft-border flex items-center justify-between cursor-pointer hover:border-forest/40 transition">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_choice" value="upi" {{ !isset($isInHouse) || !$isInHouse ? 'checked' : '' }} class="accent-emerald w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-forest">Instant UPI Payment</span>
                                    <span class="block text-[10px] text-forest/50">GPay, PhonePe, Paytm, QR</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-forest/60">UPI</span>
                        </label>

                        <label class="payment-option p-3.5 rounded-xl bg-white soft-border flex items-center justify-between cursor-pointer hover:border-forest/40 transition">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_choice" value="cod" class="accent-emerald w-4 h-4">
                                <div>
                                    <span class="block text-xs font-bold text-forest">Pay on Delivery</span>
                                    <span class="block text-[10px] text-forest/50">Cash or card upon handover</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-forest/60">Cash</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER: TOTAL BREAKDOWN & SUBMIT BUTTON -->
        <div class="p-6 bg-white border-t border-forest/10 space-y-3 shrink-0">
            <div class="space-y-1.5 text-xs text-forest/70">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal" class="font-bold text-forest">₹0</span>
                </div>
                <div class="flex justify-between" id="cart-tax-row">
                    <span id="cart-tax-label">GST (5% Restaurant):</span>
                    <span id="cart-tax" class="font-bold text-forest">₹0</span>
                </div>
                <div class="flex justify-between font-bold text-sm text-forest pt-2 border-t border-forest/10">
                    <span>Total Amount:</span>
                    <span id="cart-total" class="text-emerald text-base font-extrabold">₹0</span>
                </div>
            </div>

            <button onclick="submitOrderDrawer()" id="submit-order-btn" class="w-full py-3.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-2 shadow-card transition touch-tap">
                <span>Transmit Order to Kitchen &amp; Confirm Payment</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
            </button>
            <p class="text-[10px] text-center text-forest/50">Kitchen prepares dishes live &bull; Transmitted directly to resort team</p>
        </div>
    </div>
<!-- MODAL: CLIENT CANCELLATION & AUTOMATED CASHBACK PREVIEW -->
<div id="modal-client-cancel" class="fixed inset-0 z-50 bg-forest/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-5 border border-forest/10 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-forest/10">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="serif text-lg font-bold text-forest">Cancel Reservation</h3>
                    <p class="text-[11px] text-forest/60">Booking Code: <span id="client-cancel-code" class="font-mono font-bold text-forest"></span></p>
                </div>
            </div>
            <button onclick="closeClientCancelModal()" class="w-8 h-8 rounded-full bg-paper hover:bg-forest hover:text-paper text-forest flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Cancellation Calculation Card (AJAX loaded) -->
        <div id="client-cancel-loading" class="py-8 text-center text-xs text-forest/60 space-y-2">
            <div class="w-6 h-6 border-2 border-forest/30 border-t-forest rounded-full animate-spin mx-auto"></div>
            <p>Evaluating active cancellation policy &amp; calculating cashback...</p>
        </div>

        <div id="client-cancel-content" class="space-y-4 hidden text-xs">
            <!-- Alert banner -->
            <div id="client-cancel-badge" class="p-3.5 rounded-2xl bg-paper soft-border flex items-center justify-between">
                <div>
                    <span class="text-[9px] uppercase font-bold tracking-wider text-forest/50 block">Time to Check-in (2:00 PM)</span>
                    <span id="client-cancel-hours" class="font-bold text-forest text-sm">-- Hours</span>
                </div>
                <div class="text-right">
                    <span class="text-[9px] uppercase font-bold tracking-wider text-forest/50 block">Eligible Cashback</span>
                    <span id="client-cancel-pct" class="font-extrabold text-emerald text-base">--%</span>
                </div>
            </div>

            <!-- Breakdown -->
            <div class="p-4 rounded-2xl bg-paper/60 space-y-2">
                <div class="flex justify-between text-forest/70">
                    <span>Total Amount Paid:</span>
                    <span id="client-cancel-paid" class="font-bold text-forest">₹0</span>
                </div>
                <div class="flex justify-between text-forest/70">
                    <span>Active Cancellation Rule:</span>
                    <span id="client-cancel-rule" class="font-semibold text-forest text-right max-w-[240px]">--</span>
                </div>
                <div class="flex justify-between text-forest/70">
                    <span>Cancellation Fee Retained:</span>
                    <span id="client-cancel-retained" class="font-semibold text-rose-700">₹0</span>
                </div>
                <div class="pt-2 border-t border-forest/10 flex justify-between font-bold text-sm">
                    <span class="text-forest">Automated Cashback Refund:</span>
                    <span id="client-cancel-cashback" class="text-emerald text-base font-extrabold">₹0</span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-200 text-[11px] leading-relaxed flex items-start gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 shrink-0 text-emerald-600 mt-0.5"></i>
                <span><strong>Automated Payback Guarantee:</strong> Your cashback of <span id="client-cancel-guarantee" class="font-bold text-emerald-800">₹0</span> will be recorded with an automated payback ledger code (REF-XXXXX) and dispatched to your original payment method. You will receive an SMS confirmation immediately.</span>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Reason for Cancellation</label>
                <input type="text" id="client-cancel-reason" placeholder="e.g. Flight rescheduling, personal emergency..." class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeClientCancelModal()" class="px-4 py-2.5 rounded-xl bg-paper hover:bg-white soft-border text-forest text-xs font-bold transition">
                    Keep Reservation
                </button>
                <button type="button" id="client-cancel-submit-btn" onclick="executeClientCancellation()" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                    <span>Confirm &amp; Initiate Payback</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL: CLIENT SPICE ORDER CANCEL / RETURN REQUEST
     ======================================================== -->
<div id="modal-client-spice-return" class="fixed inset-0 z-50 bg-forest/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-5 border border-forest/10 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-forest/10">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="serif text-lg font-bold text-forest" id="spice-return-modal-title">Return Spice Order</h3>
                    <p class="text-[11px] text-forest/60">Order: <span id="client-spice-return-order-num" class="font-mono font-bold text-forest">#SP-</span></p>
                </div>
            </div>
            <button onclick="closeClientSpiceReturnModal()" class="w-8 h-8 rounded-full bg-paper hover:bg-forest hover:text-paper text-forest flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Policy Description Snippet -->
        <div class="p-3 bg-mint/40 rounded-2xl border border-emerald/15 text-[11px] text-forest/75 flex items-start gap-2">
            <i data-lucide="shield-check" class="w-4 h-4 text-emerald shrink-0 mt-0.5"></i>
            <span>{{ $spiceReturnPolicyDescription ?? '100% full refund before dispatch. 7-day freshness return guarantee on unopened packages.' }}</span>
        </div>

        <!-- Loading State -->
        <div id="client-spice-return-loading" class="py-8 text-center text-xs text-forest/60 space-y-2">
            <div class="w-6 h-6 border-2 border-forest/30 border-t-forest rounded-full animate-spin mx-auto"></div>
            <p>Evaluating plantation return policy &amp; calculating refund breakdown...</p>
        </div>

        <!-- Return Calculation Breakdown (AJAX Loaded) -->
        <div id="client-spice-return-content" class="space-y-4 hidden text-xs">
            <!-- Stage Banner -->
            <div id="client-spice-return-badge" class="p-3.5 rounded-2xl bg-paper soft-border flex items-center justify-between">
                <div>
                    <span class="text-[9px] uppercase font-bold tracking-wider text-forest/50 block">Dispatch Stage</span>
                    <span id="client-spice-return-stage" class="font-bold text-forest text-sm">--</span>
                </div>
                <div class="text-right">
                    <span class="text-[9px] uppercase font-bold tracking-wider text-forest/50 block">Eligible Cashback</span>
                    <span id="client-spice-return-pct" class="font-extrabold text-emerald text-base">--%</span>
                </div>
            </div>

            <!-- Breakdown Card -->
            <div class="p-4 rounded-2xl bg-paper/60 space-y-2">
                <div class="flex justify-between text-forest/70">
                    <span>Order Total:</span>
                    <span id="client-spice-return-order-total" class="font-bold text-forest">₹0</span>
                </div>
                <div class="flex justify-between text-forest/70">
                    <span>Applicable Policy Tier:</span>
                    <span id="client-spice-return-rule-name" class="font-semibold text-forest text-right max-w-[240px]">--</span>
                </div>
                <div class="flex justify-between text-forest/70">
                    <span>Handling / Repackaging Fee:</span>
                    <span id="client-spice-return-fee" class="font-semibold text-rose-700">₹0</span>
                </div>
                <div class="pt-2 border-t border-forest/10 flex justify-between font-bold text-sm">
                    <span class="text-forest">Net Refund / Cashback:</span>
                    <span id="client-spice-return-cashback" class="text-emerald text-base font-extrabold">₹0</span>
                </div>
            </div>

            <!-- Reason Input -->
            <div>
                <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Reason for Return / Cancellation *</label>
                <textarea id="client-spice-return-reason" rows="2" placeholder="Please share why you wish to return or cancel (e.g. ordered wrong grind, delayed trip, damaged packaging)..." class="w-full px-3.5 py-2.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden resize-none"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="closeClientSpiceReturnModal()" class="px-4 py-2.5 rounded-xl bg-paper hover:bg-white soft-border text-forest text-xs font-bold transition">
                    Keep Order
                </button>
                <button type="button" id="client-spice-return-submit-btn" onclick="executeClientSpiceReturn()" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span id="client-spice-return-btn-label">Submit Return Request</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ON-HAND / AT-CHECKOUT PAYMENT NOTICE -->
<div id="modal-onhand-payment-info" class="fixed inset-0 z-50 bg-forest/70 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-5 border border-forest/10 text-center">
        <div class="w-12 h-12 rounded-2xl bg-mint text-emerald flex items-center justify-center mx-auto shadow-xs">
            <i data-lucide="wallet" class="w-6 h-6"></i>
        </div>
        <div>
            <h3 class="serif text-lg font-bold text-forest">Flexible In-House Settlement</h3>
            <p class="text-xs text-forest/70 mt-1">Amount to settle: <span id="onhand-amount-label" class="font-extrabold text-emerald text-sm">₹0</span></p>
        </div>
        <div class="p-4 rounded-2xl bg-paper text-xs text-forest/80 text-left space-y-2 leading-relaxed">
            <p>✓ <strong>No upfront rush:</strong> Your stay checkout date is already extended and confirmed.</p>
            <p>✓ <strong>Multiple payment modes:</strong> You can pay in <strong>Cash</strong>, swipe your debit/credit card on the <strong>POS Terminal</strong>, or scan the <strong>Cottage UPI QR</strong>.</p>
            <p>✓ <strong>Whenever convenient:</strong> Hand over payment to the front desk reception or duty manager at your convenience, or clear it during your final departure check-out.</p>
        </div>
        <button type="button" onclick="document.getElementById('modal-onhand-payment-info').classList.add('hidden'); document.getElementById('modal-onhand-payment-info').classList.remove('flex');" class="w-full py-3 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-card transition touch-tap">
            Understood, Thank You
        </button>
    </div>
</div>

@push('scripts')
<script>
    // -------------------------------------------------------------
    // 1. Unified Concierge Chat Sync
    // -------------------------------------------------------------
    function openDashboardChat() {
        if (typeof window.openChat === 'function') {
            window.openChat();
        } else {
            const modal = document.getElementById('conciergeModal');
            if (modal) modal.classList.remove('hidden');
        }
    }

    function requestConciergeTour(spotName) {
        openDashboardChat();
        setTimeout(() => {
            const input = document.getElementById('chatMessageInput');
            if (input) {
                input.value = `Namaste Concierge, I would like to arrange transportation and a local guide for "${spotName}" from our cottage.`;
                input.focus();
            }
        }, 300);
    }

    // -------------------------------------------------------------
    // 2. Client Cancellation & Automated Payback Logic
    // -------------------------------------------------------------
    window._pendingCancelResId = null;

    function openClientCancelModal(resId, bookingCode) {
        window._pendingCancelResId = resId;
        const modal = document.getElementById('modal-client-cancel');
        const codeEl = document.getElementById('client-cancel-code');
        const loadingEl = document.getElementById('client-cancel-loading');
        const contentEl = document.getElementById('client-cancel-content');
        const reasonInput = document.getElementById('client-cancel-reason');

        if (codeEl) codeEl.textContent = '#' + bookingCode;
        if (reasonInput) reasonInput.value = '';
        if (loadingEl) loadingEl.classList.remove('hidden');
        if (contentEl) contentEl.classList.add('hidden');

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (window.lucide) lucide.createIcons();
        }

        // Fetch dynamic cancellation preview
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(`/dashboard/reservations/${resId}/cancel-preview`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (loadingEl) loadingEl.classList.add('hidden');
                if (contentEl) contentEl.classList.remove('hidden');

                const hours = data.hours_remaining;
                const hoursText = hours > 0 ? `${hours} hrs before check-in` : `Check-in passed (${Math.abs(hours)} hrs ago)`;
                document.getElementById('client-cancel-hours').textContent = hoursText;
                document.getElementById('client-cancel-pct').textContent = `${data.refund_percentage}%`;
                document.getElementById('client-cancel-paid').textContent = `₹${Number(data.paid_amount).toLocaleString('en-IN')}`;
                document.getElementById('client-cancel-rule').textContent = data.rule_description;
                document.getElementById('client-cancel-retained').textContent = `₹${Number(data.retained_amount).toLocaleString('en-IN')}`;
                document.getElementById('client-cancel-cashback').textContent = `₹${Number(data.cashback_amount).toLocaleString('en-IN')}`;
                document.getElementById('client-cancel-guarantee').textContent = `₹${Number(data.cashback_amount).toLocaleString('en-IN')}`;

                if (window.lucide) lucide.createIcons();
            } else {
                alert(data.message || 'Unable to compute cancellation cashback.');
                closeClientCancelModal();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error loading cancellation details.');
            closeClientCancelModal();
        });
    }

    function closeClientCancelModal() {
        const modal = document.getElementById('modal-client-cancel');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        window._pendingCancelResId = null;
    }

    async function executeClientCancellation() {
        if (!window._pendingCancelResId) return;
        const resId = window._pendingCancelResId;
        const reason = document.getElementById('client-cancel-reason')?.value?.trim() || 'Cancelled via Guest Portal';
        const btn = document.getElementById('client-cancel-submit-btn');

        if (!confirm('Are you sure you wish to cancel this reservation? The cancellation and automated cashback calculation cannot be reversed.')) {
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<span>Initiating Payback...</span>`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/dashboard/reservations/${resId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            });

            const data = await res.json();
            if (data.success) {
                const refundMsg = data.cashback_amount > 0 
                    ? `\nAutomated Cashback: ₹${Number(data.cashback_amount).toLocaleString('en-IN')} (${data.refund_percentage}%)\nPayback Transaction: ${data.refund_transaction_id || 'REF-PROCESSED'}\nSMS notification dispatched.`
                    : '\nNo refund applicable per cancellation policy terms.';
                alert(`Reservation Cancelled Successfully!${refundMsg}`);
                closeClientCancelModal();
                window.location.reload();
            } else {
                alert(data.message || 'Cancellation failed. Please contact front desk.');
            }
        } catch (err) {
            console.error(err);
            alert('Server error processing cancellation.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="x-circle" class="w-3.5 h-3.5"></i><span>Confirm &amp; Initiate Payback</span>`;
            if (window.lucide) lucide.createIcons();
        }
    }

    // -------------------------------------------------------------
    // 2.5. Client Spice Return & Cancellation Policy Logic
    // -------------------------------------------------------------
    let _pendingSpiceReturnOrderId = null;
    let _pendingSpiceRequestType = null;

    function openClientSpiceReturnModal(orderId, orderNum) {
        _pendingSpiceReturnOrderId = orderId;
        const modal = document.getElementById('modal-client-spice-return');
        const orderNumEl = document.getElementById('client-spice-return-order-num');
        const titleEl = document.getElementById('spice-return-modal-title');
        const loadingEl = document.getElementById('client-spice-return-loading');
        const contentEl = document.getElementById('client-spice-return-content');
        const reasonInput = document.getElementById('client-spice-return-reason');
        const btnLabel = document.getElementById('client-spice-return-btn-label');

        if (orderNumEl) orderNumEl.textContent = '#' + orderNum;
        if (reasonInput) reasonInput.value = '';
        if (loadingEl) loadingEl.classList.remove('hidden');
        if (contentEl) contentEl.classList.add('hidden');

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (window.lucide) lucide.createIcons();
        }

        fetch(`/dashboard/spices/orders/${orderId}/return-preview`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (loadingEl) loadingEl.classList.add('hidden');
                if (contentEl) contentEl.classList.remove('hidden');

                const calc = data.calculation;
                _pendingSpiceRequestType = calc.request_type;

                if (titleEl) {
                    titleEl.textContent = (calc.request_type === 'cancellation') ? 'Cancel Spice Order' : 'Return Spice Order';
                }
                if (btnLabel) {
                    btnLabel.textContent = (calc.request_type === 'cancellation') ? 'Confirm Cancellation & Refund' : 'Submit Return Request';
                }

                const stageEl = document.getElementById('client-spice-return-stage');
                if (stageEl) {
                    stageEl.textContent = (calc.request_type === 'cancellation') ? 'Pre-Dispatch (Estate Packing)' : 'Post-Delivery Return';
                }

                document.getElementById('client-spice-return-pct').textContent = `${calc.refund_percentage}%`;
                document.getElementById('client-spice-return-order-total').textContent = `₹${Number(data.order.total_amount).toLocaleString('en-IN')}`;
                document.getElementById('client-spice-return-rule-name').textContent = calc.rule_name;
                document.getElementById('client-spice-return-fee').textContent = calc.handling_fee > 0 ? `₹${Number(calc.handling_fee).toLocaleString('en-IN')}` : 'Nil (₹0)';
                document.getElementById('client-spice-return-cashback').textContent = `₹${Number(calc.cashback).toLocaleString('en-IN')}`;

                if (window.lucide) lucide.createIcons();
            } else {
                alert(data.message || 'Unable to calculate return eligibility.');
                closeClientSpiceReturnModal();
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error loading spice return details.');
            closeClientSpiceReturnModal();
        });
    }

    function closeClientSpiceReturnModal() {
        const modal = document.getElementById('modal-client-spice-return');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        _pendingSpiceReturnOrderId = null;
        _pendingSpiceRequestType = null;
    }

    async function executeClientSpiceReturn() {
        if (!_pendingSpiceReturnOrderId) return;
        const reasonInput = document.getElementById('client-spice-return-reason');
        const reason = reasonInput ? reasonInput.value.trim() : '';

        if (!reason) {
            alert('Please provide a reason for the return or cancellation.');
            if (reasonInput) reasonInput.focus();
            return;
        }

        const btn = document.getElementById('client-spice-return-submit-btn');
        const originalText = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Submitting...';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const res = await fetch(`/dashboard/spices/orders/${_pendingSpiceReturnOrderId}/request-return`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    cancellation_reason: reason,
                    request_type: _pendingSpiceRequestType
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Spice return request recorded successfully.');
                closeClientSpiceReturnModal();
                window.location.reload();
            } else {
                alert(data.message || 'Unable to process return request.');
            }
        } catch(err) {
            console.error(err);
            alert('A network or server error occurred.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    }

    // -------------------------------------------------------------
    // Stay Extension Dynamic Availability & Smart Re-Allocation Engine
    // -------------------------------------------------------------
    @if($selectedStay && $isInHouse)
    const currentStayCheckoutStr = '{{ $selectedStay->check_out_date }}';
    const currentStayNightlyRate = {{ (float) $selectedStay->nightly_rate }};
    let currentExtensionSelection = {
        type: 'same_room',
        room_ids: [{{ $selectedStay->room_id ?? 1 }}],
        amount: currentStayNightlyRate * 1.12,
        label: 'Same Cottage (Villa {{ $selectedStay->room->room_number ?? "101" }})'
    };

    function quickSelectExtensionNights(resId, nights) {
        const d = new Date(currentStayCheckoutStr);
        d.setDate(d.getDate() + nights);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const dateInput = document.getElementById('ext-new-checkout');
        if (dateInput) {
            dateInput.value = `${yyyy}-${mm}-${dd}`;
            checkExtensionAvailability(resId);
        }
    }

    async function checkExtensionAvailability(resId) {
        const dateInput = document.getElementById('ext-new-checkout');
        if (!dateInput || !dateInput.value) return;
        const newDate = dateInput.value;

        const spinner = document.getElementById('ext-checking-spinner');
        const resultsBox = document.getElementById('ext-availability-results');
        if (spinner) spinner.classList.remove('hidden');
        if (resultsBox) resultsBox.innerHTML = '';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/dashboard/reservations/${resId}/check-extension`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ new_checkout_date: newDate })
            });

            const data = await res.json();
            if (spinner) spinner.classList.add('hidden');

            if (!data.success) {
                if (resultsBox) resultsBox.innerHTML = `<div class="p-3 rounded-xl bg-rose-50 text-rose-800 text-xs border border-rose-200">${data.message || 'Unable to verify availability.'}</div>`;
                return;
            }

            const extraNights = data.extra_nights;
            document.getElementById('ext-est-nights').textContent = `${extraNights} Night${extraNights > 1 ? 's' : ''}`;

            if (data.same_room_available && data.current_room_info) {
                // Same room available
                const info = data.current_room_info;
                currentExtensionSelection = {
                    type: 'same_room',
                    room_ids: [info.room_id],
                    amount: info.total_amount,
                    label: `Same Cottage (Villa ${info.room_number})`
                };

                updateExtensionSummary(info.subtotal, info.tax, info.total_amount, currentExtensionSelection.label);

                resultsBox.innerHTML = `
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-950 space-y-2">
                        <div class="flex items-center gap-2 font-bold text-emerald-900 text-sm">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                            <span>Villa ${info.room_number} is Free for All Extended Dates!</span>
                        </div>
                        <p class="text-[11px] text-emerald-800 leading-relaxed">
                            No shifting required. You can stay in your current cottage (${info.room_type_name}, Floor ${info.floor}) uninterrupted until <strong>${newDate}</strong>.
                        </p>
                    </div>
                `;
            } else {
                // Current room occupied -> Render Smart Room Recommendations
                let html = `
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 space-y-3">
                        <div class="flex items-center gap-2 font-bold text-amber-900">
                            <i data-lucide="info" class="w-4 h-4 text-amber-700 shrink-0"></i>
                            <span>Current Cottage Reserved for Selected Dates</span>
                        </div>
                        <p class="text-[11px] text-amber-800 leading-relaxed">
                            Villa {{ $selectedStay->room->room_number ?? "101" }} has a subsequent guest checking in. Our <strong>Smart Re-Allocation Engine</strong> has found the following available options matching your party (${data.total_guests} Guests):
                        </p>
                        <div class="space-y-2 pt-1">
                `;

                const singles = data.smart_recommendations?.single_rooms || [];
                const multis = data.smart_recommendations?.multi_room_combinations || [];

                let firstSet = false;

                if (singles.length > 0) {
                    html += `<div class="font-bold text-[10px] uppercase tracking-wider text-forest/70 mt-2">Single Room Upgrades / Alternates:</div>`;
                    singles.forEach((s, idx) => {
                        const isChecked = !firstSet;
                        if (isChecked) {
                            firstSet = true;
                            currentExtensionSelection = {
                                type: 'single_room',
                                room_ids: s.room_ids,
                                amount: s.total_amount,
                                label: s.display_label
                            };
                            const sub = s.rate_per_night * extraNights;
                            const tax = Math.round(sub * 0.12 * 100) / 100;
                            updateExtensionSummary(sub, tax, s.total_amount, s.display_label);
                        }

                        html += `
                            <label class="flex items-center justify-between p-3 rounded-xl bg-white soft-border cursor-pointer hover:border-emerald-500 transition">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="alloc_choice" value="single_${idx}" ${isChecked ? 'checked' : ''} 
                                           onchange='selectAllocationOption("single_room", ${JSON.stringify(s.room_ids)}, ${s.total_amount}, "${s.display_label}", ${s.rate_per_night * extraNights})'
                                           class="text-forest focus:ring-forest">
                                    <div>
                                        <span class="font-bold text-forest block">${s.display_label}</span>
                                        <span class="text-[10px] text-forest/50">Capacity: ${s.capacity} Guests &bull; ₹${Number(s.rate_per_night).toLocaleString('en-IN')}/nt</span>
                                    </div>
                                </div>
                                <span class="font-bold text-forest text-xs">₹${Number(s.total_amount).toLocaleString('en-IN')}</span>
                            </label>
                        `;
                    });
                }

                if (multis.length > 0) {
                    html += `<div class="font-bold text-[10px] uppercase tracking-wider text-forest/70 mt-3">Multi-Room Combinations:</div>`;
                    multis.forEach((m, idx) => {
                        const isChecked = !firstSet;
                        if (isChecked) {
                            firstSet = true;
                            currentExtensionSelection = {
                                type: 'multi_room',
                                room_ids: m.room_ids,
                                amount: m.total_amount,
                                label: m.display_label
                            };
                            const sub = m.rate_per_night * extraNights;
                            const tax = Math.round(sub * 0.12 * 100) / 100;
                            updateExtensionSummary(sub, tax, m.total_amount, m.display_label);
                        }

                        html += `
                            <label class="flex items-center justify-between p-3 rounded-xl bg-white soft-border cursor-pointer hover:border-emerald-500 transition">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="alloc_choice" value="multi_${idx}" ${isChecked ? 'checked' : ''} 
                                           onchange='selectAllocationOption("multi_room", ${JSON.stringify(m.room_ids)}, ${m.total_amount}, "${m.display_label}", ${m.rate_per_night * extraNights})'
                                           class="text-forest focus:ring-forest">
                                    <div>
                                        <span class="font-bold text-forest block">${m.display_label}</span>
                                        <span class="text-[10px] text-forest/50">${m.room_type} &bull; Cap: ${m.capacity} Guests</span>
                                    </div>
                                </div>
                                <span class="font-bold text-forest text-xs">₹${Number(m.total_amount).toLocaleString('en-IN')}</span>
                            </label>
                        `;
                    });
                }

                html += `</div></div>`;
                resultsBox.innerHTML = html;
            }

            if (window.lucide) lucide.createIcons();
        } catch (err) {
            console.error(err);
            if (spinner) spinner.classList.add('hidden');
            if (resultsBox) resultsBox.innerHTML = `<div class="p-3 rounded-xl bg-rose-50 text-rose-800 text-xs">Failed to check availability.</div>`;
        }
    }

    function selectAllocationOption(type, roomIds, amount, label, subtotal) {
        currentExtensionSelection = {
            type: type,
            room_ids: roomIds,
            amount: amount,
            label: label
        };
        const tax = Math.round(subtotal * 0.12 * 100) / 100;
        updateExtensionSummary(subtotal, tax, amount, label);
    }

    function updateExtensionSummary(subtotal, tax, total, label) {
        const subEl = document.getElementById('ext-est-subtotal');
        const taxEl = document.getElementById('ext-est-tax');
        const totEl = document.getElementById('ext-est-total');
        const lblEl = document.getElementById('ext-selected-allocation-label');

        if (subEl) subEl.textContent = `₹${Number(subtotal).toLocaleString('en-IN')}`;
        if (taxEl) taxEl.textContent = `₹${Number(tax).toLocaleString('en-IN')}`;
        if (totEl) totEl.textContent = `₹${Number(total).toLocaleString('en-IN')}`;
        if (lblEl) lblEl.textContent = label;
    }

    async function submitStayExtensionRequest(resId) {
        const dateInput = document.getElementById('ext-new-checkout');
        const notesInput = document.getElementById('ext-notes');
        const btn = document.getElementById('ext-submit-btn');

        if (!dateInput || !dateInput.value) {
            alert('Please select a valid new checkout date.');
            return;
        }

        if (!currentExtensionSelection || !currentExtensionSelection.room_ids || currentExtensionSelection.room_ids.length === 0) {
            alert('Please check availability and select a room option first.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<span>Transmitting to Manager...</span>`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/dashboard/reservations/${resId}/request-extension`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    new_checkout_date: dateInput.value,
                    allocation_type: currentExtensionSelection.type,
                    allocated_room_ids: currentExtensionSelection.room_ids,
                    standard_amount: currentExtensionSelection.amount,
                    guest_notes: notesInput?.value?.trim() || null
                })
            });

            const data = await res.json();
            if (data.success) {
                alert(data.message || 'Holiday extension request transmitted! The manager will post an offer shortly.');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to submit request.');
            }
        } catch (err) {
            console.error(err);
            alert('Network error submitting extension request.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="send" class="w-3.5 h-3.5 text-brass"></i><span>Request Extension &amp; Loyalty Discount</span>`;
            if (window.lucide) lucide.createIcons();
        }
    }

    async function payExtensionOnline(extId, amount) {
        const btn = document.getElementById(`ext-online-pay-btn-${extId}`);
        if (!confirm(`Proceed with online card/UPI payment of ₹${Number(amount).toLocaleString('en-IN')} for your stay extension?`)) {
            return;
        }

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<span>Processing Payment...</span>`;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/dashboard/stay-extensions/${extId}/pay-online`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ payment_method: 'online' })
            });

            const data = await res.json();
            if (data.success) {
                alert(`Payment Confirmed! Receipt: ${data.transaction_id}\nYour stay extension is now completely settled.`);
                window.location.reload();
            } else {
                alert(data.message || 'Payment could not be completed.');
            }
        } catch (err) {
            console.error(err);
            alert('Payment gateway error. Please try again or settle on-hand with resort staff.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="credit-card" class="w-3.5 h-3.5 text-brass"></i><span>Pay Online</span>`;
                if (window.lucide) lucide.createIcons();
            }
        }
    }

    function openOnHandPaymentNotice(amount) {
        const modal = document.getElementById('modal-onhand-payment-info');
        const lbl = document.getElementById('onhand-amount-label');
        if (lbl) lbl.textContent = `₹${Number(amount).toLocaleString('en-IN')}`;
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (window.lucide) lucide.createIcons();
        }
    }
    @endif

    // -------------------------------------------------------------
    // 3. CONTINUOUS SLOW MARQUEE ENGINE (HOVER PAUSE + ARROW BUTTONS)
    // -------------------------------------------------------------
    let currentMarqueeTab = 'dining';
    let isMarqueePaused = false;
    let marqueeInterval = null;

    function startMarquee() {
        if (marqueeInterval) clearInterval(marqueeInterval);
        marqueeInterval = setInterval(() => {
            if (isMarqueePaused) return;

            const trackId = currentMarqueeTab === 'dining' ? 'dining-marquee-track' : 'spices-marquee-track';
            const track = document.getElementById(trackId);
            if (!track) return;

            track.scrollLeft += 1;
            // Infinite seamless wrap
            if (track.scrollLeft >= (track.scrollWidth - track.clientWidth - 2)) {
                track.scrollLeft = 0;
            }
        }, 35); // 35ms tick = smooth, calm slow marquee
    }

    function scrollActiveMarquee(delta) {
        const trackId = currentMarqueeTab === 'dining' ? 'dining-marquee-track' : 'spices-marquee-track';
        const track = document.getElementById(trackId);
        if (track) {
            track.scrollBy({ left: delta, behavior: 'smooth' });
        }
    }

    function switchQuickOrderTab(tab) {
        currentMarqueeTab = tab;
        const btnDining = document.getElementById('btn-tab-dining');
        const btnSpices = document.getElementById('btn-tab-spices');
        const pnlDining = document.getElementById('quick-order-dining-panel');
        const pnlSpices = document.getElementById('quick-order-spices-panel');
        const linkAll = document.getElementById('link-view-all');

        if (tab === 'dining') {
            btnDining.className = 'px-4 py-2 rounded-xl font-bold bg-white text-forest shadow-xs transition touch-tap flex items-center gap-1.5';
            btnSpices.className = 'px-4 py-2 rounded-xl font-semibold text-forest/60 hover:text-forest transition touch-tap flex items-center gap-1.5';
            pnlDining.classList.remove('hidden');
            pnlSpices.classList.add('hidden');
            if (linkAll) {
                linkAll.href = '{{ route("dining.index") }}';
                linkAll.querySelector('span').textContent = 'Full Menu';
            }
        } else {
            btnSpices.className = 'px-4 py-2 rounded-xl font-bold bg-white text-forest shadow-xs transition touch-tap flex items-center gap-1.5';
            btnDining.className = 'px-4 py-2 rounded-xl font-semibold text-forest/60 hover:text-forest transition touch-tap flex items-center gap-1.5';
            pnlSpices.classList.remove('hidden');
            pnlDining.classList.add('hidden');
            if (linkAll) {
                linkAll.href = '{{ route("spices.index") }}';
                linkAll.querySelector('span').textContent = 'Spice Shop';
            }
        }
        if (window.lucide) lucide.createIcons();
    }

    // Attach hover & touch listeners to pause marquee
    function initMarqueeHoverListeners() {
        ['dining-marquee-track', 'spices-marquee-track', 'ordering-section'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('mouseenter', () => { isMarqueePaused = true; });
            el.addEventListener('mouseleave', () => { isMarqueePaused = false; });
            el.addEventListener('touchstart', () => { isMarqueePaused = true; }, { passive: true });
            el.addEventListener('touchend', () => { isMarqueePaused = false; }, { passive: true });
        });
    }

    // -------------------------------------------------------------
    // 4. ORDER & PAYMENT SLIDE-OVER DRAWER (MATCHING DINING PAGE)
    // -------------------------------------------------------------
    let drawerCart = JSON.parse(localStorage.getItem('krishna_dashboard_tray')) || [];

    function saveDrawerCart() {
        localStorage.setItem('krishna_dashboard_tray', JSON.stringify(drawerCart));
        updateDrawerUI();
    }

    function openOrderDrawer(type, id, name, price, image) {
        const existing = drawerCart.find(i => i.id === id && i.type === type);
        if (existing) {
            existing.qty += 1;
        } else {
            drawerCart.push({ type, id, name, price, image: image || '', qty: 1 });
        }
        saveDrawerCart();

        // Update header icon/title
        const iconEl = document.getElementById('drawer-header-icon');
        const titleEl = document.getElementById('drawer-header-title');
        const subEl = document.getElementById('drawer-header-subtitle');
        if (iconEl && titleEl && subEl) {
            if (type === 'spices') {
                iconEl.setAttribute('data-lucide', 'leaf');
                titleEl.textContent = 'Estate Spices Tray';
                subEl.textContent = 'Vacuum-Packed Plantation Harvest';
            } else {
                iconEl.setAttribute('data-lucide', 'utensils');
                titleEl.textContent = 'In-Villa Dining Tray';
                subEl.textContent = 'Fresh from Krishna Kitchen';
            }
        }

        openCartDrawer();
        if (typeof window.availabilityToast === 'function') {
            availabilityToast(`${name} added to tray`);
        }
    }

    function openCartDrawer() {
        const drawer = document.getElementById('cart-drawer');
        if (drawer) {
            drawer.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            updateDrawerUI();
        }
    }

    function closeCartDrawer() {
        const drawer = document.getElementById('cart-drawer');
        if (drawer) {
            drawer.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    document.getElementById('cart-drawer')?.addEventListener('click', (e) => {
        if (e.target.id === 'cart-drawer') closeCartDrawer();
    });

    function updateDrawerQty(id, type, delta) {
        const item = drawerCart.find(i => i.id === id && i.type === type);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) {
            drawerCart = drawerCart.filter(i => !(i.id === id && i.type === type));
        }
        saveDrawerCart();
    }

    function removeDrawerItem(id, type) {
        drawerCart = drawerCart.filter(i => !(i.id === id && i.type === type));
        saveDrawerCart();
    }

    function clearDrawerCart() {
        drawerCart = [];
        saveDrawerCart();
    }

    function updateDrawerUI() {
        const container = document.getElementById('cart-items-container');
        const btn = document.getElementById('submit-order-btn');
        if (!container) return;

        if (drawerCart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-10 text-forest/50">
                    <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto mb-2 text-forest/30"></i>
                    <p class="text-xs font-semibold">Your order tray is empty</p>
                    <p class="text-[11px] text-forest/40 mt-0.5">Select dishes or spices from the marquee to order.</p>
                </div>
            `;
            document.getElementById('cart-subtotal').textContent = '₹0';
            document.getElementById('cart-tax').textContent = '₹0';
            document.getElementById('cart-total').textContent = '₹0';
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            if (window.lucide) lucide.createIcons();
            return;
        }

        if (btn) {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        let subtotal = 0;
        let html = '';

        drawerCart.forEach(i => {
            const lineTotal = i.price * i.qty;
            subtotal += lineTotal;
            const badgeIcon = i.type === 'spices' ? 'leaf' : 'utensils';
            html += `
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-white soft-border shadow-xs">
                    <div class="flex items-center gap-3 min-w-0 flex-1 pr-2">
                        <div class="w-10 h-10 rounded-xl bg-mint overflow-hidden shrink-0 flex items-center justify-center text-forest">
                            ${i.image ? `<img src="${i.image}" class="w-full h-full object-cover">` : `<i data-lucide="${badgeIcon}" class="w-4 h-4 text-emerald"></i>`}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h5 class="text-xs font-bold text-forest truncate">${i.name}</h5>
                            <div class="text-[11px] text-forest/60">₹${i.price.toLocaleString('en-IN')} &times; ${i.qty} = <strong>₹${lineTotal.toLocaleString('en-IN')}</strong></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5 bg-paper soft-border rounded-xl p-1">
                            <button type="button" onclick="updateDrawerQty(${i.id}, '${i.type}', -1)" class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-xs font-bold text-forest hover:bg-forest hover:text-paper transition">-</button>
                            <span class="text-xs font-bold px-1.5 text-forest">${i.qty}</span>
                            <button type="button" onclick="updateDrawerQty(${i.id}, '${i.type}', 1)" class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-xs font-bold text-forest hover:bg-forest hover:text-paper transition">+</button>
                        </div>
                        <button type="button" onclick="removeDrawerItem(${i.id}, '${i.type}')" class="text-forest/30 hover:text-rose-600 p-1 transition" title="Remove">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        const tax = Math.round(subtotal * 0.05);
        const grandTotal = subtotal + tax;

        document.getElementById('cart-subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN');
        document.getElementById('cart-tax').textContent = '₹' + tax.toLocaleString('en-IN');
        document.getElementById('cart-total').textContent = '₹' + grandTotal.toLocaleString('en-IN');
        if (window.lucide) lucide.createIcons();
    }

    async function submitOrderDrawer() {
        if (drawerCart.length === 0) return;

        const name = document.getElementById('order-guest-name').value.trim();
        const phone = document.getElementById('order-guest-phone').value.trim();
        const orderType = document.getElementById('order-type').value;
        const tableNum = document.getElementById('order-table-num').value.trim();
        const instructions = document.getElementById('order-instructions').value.trim();

        // Check selected payment choice
        const paymentRadio = document.querySelector('input[name="payment_choice"]:checked');
        const paymentChoice = paymentRadio ? paymentRadio.value : 'charge_to_room';

        if (!name || !phone) {
            alert('Please provide your name and contact phone number.');
            return;
        }

        const btn = document.getElementById('submit-order-btn');
        btn.disabled = true;
        btn.innerHTML = `<span>Transmitting Order to Kitchen...</span>`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const diningItems = drawerCart.filter(i => i.type === 'dining');
            const spiceItems = drawerCart.filter(i => i.type === 'spices');

            let successMessages = [];

            // 1. Submit Dining Items
            if (diningItems.length > 0) {
                const resDining = await fetch('{{ route("customer.quick-order-dining") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        @if(isset($selectedStay) && $selectedStay) reservation_id: {{ $selectedStay->id }}, @endif
                        customer_name: name,
                        customer_phone: phone,
                        table_number: tableNum,
                        order_type: orderType,
                        special_instructions: instructions,
                        payment_choice: paymentChoice,
                        items: diningItems.map(i => ({ id: i.id, quantity: i.qty }))
                    })
                });

                const dataD = await resDining.json();
                if (resDining.ok && dataD.success) {
                    successMessages.push(dataD.message || 'Kitchen order confirmed!');
                } else {
                    throw new Error(dataD.message || 'Failed to submit dining order.');
                }
            }

            // 2. Submit Spice Items
            if (spiceItems.length > 0) {
                const resSpices = await fetch('{{ route("customer.quick-order-spices") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        @if(isset($selectedStay) && $selectedStay) reservation_id: {{ $selectedStay->id }}, @endif
                        customer_name: name,
                        customer_phone: phone,
                        target_room: tableNum,
                        delivery_mode: 'villa_delivery',
                        items: spiceItems.map(i => ({ id: i.id, quantity: i.qty }))
                    })
                });

                const dataS = await resSpices.json();
                if (resSpices.ok && dataS.success) {
                    successMessages.push(dataS.message || 'Spice order confirmed!');
                } else {
                    throw new Error(dataS.message || 'Failed to submit spice order.');
                }
            }

            // Successfully submitted!
            clearDrawerCart();
            closeCartDrawer();
            alert(successMessages.join("\n\n") || 'Your order has been received by our resort team!');
            window.location.reload();
        } catch (err) {
            alert(err.message || 'An error occurred while transmitting your order. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<span>Transmit Order to Kitchen &amp; Confirm Payment</span><i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>`;
            if (window.lucide) lucide.createIcons();
        }
    }

    // -------------------------------------------------------------
    // 5. Airbnb-Style Star Rating & Review Submission
    // -------------------------------------------------------------
    function setStarRating(starNum) {
        document.getElementById('rev-rating').value = starNum;
        const labels = {
            1: '1.0 - Needs Improvement',
            2: '2.0 - Below Expectations',
            3: '3.0 - Average Experience',
            4: '4.0 - Very Good',
            5: '5.0 - Exceptional'
        };
        document.getElementById('star-rating-label').textContent = labels[starNum] || `${starNum}.0`;

        document.querySelectorAll('.star-btn').forEach(btn => {
            const val = parseInt(btn.dataset.star);
            const icon = btn.querySelector('svg');
            if (val <= starNum) {
                btn.classList.add('text-amber-500');
                btn.classList.remove('text-forest/20');
                if (icon) icon.classList.add('fill-amber-500');
            } else {
                btn.classList.remove('text-amber-500');
                btn.classList.add('text-forest/20');
                if (icon) icon.classList.remove('fill-amber-500');
            }
        });
    }

    async function submitVerifiedReview(e, reservationId) {
        e.preventDefault();
        const rating = document.getElementById('rev-rating').value;
        const title = document.getElementById('rev-title').value.trim();
        const comment = document.getElementById('rev-comment').value.trim();
        const btn = document.getElementById('rev-submit-btn');

        if (!comment) {
            alert('Please share your review feedback.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<span>Submitting Review...</span>`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const res = await fetch('{{ route("reviews.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({
                    reservation_id: reservationId,
                    rating: parseInt(rating),
                    title: title,
                    comment: comment
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Thank you! Your verified stay review has been submitted.');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to submit review.');
            }
        } catch (err) {
            alert('Error submitting review. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="check" class="w-3.5 h-3.5 text-brass"></i><span>Submit Verified Stay Review</span>`;
            if (window.lucide) lucide.createIcons();
        }
    }

    // -------------------------------------------------------------
    // Food Order Rating & Review Functions
    // -------------------------------------------------------------
    function setOrderRating(orderId, starVal) {
        const input = document.getElementById('order-rating-input-' + orderId);
        if (input) input.value = starVal;

        const container = document.getElementById('order-star-btns-' + orderId);
        if (container) {
            container.querySelectorAll('.order-star-btn').forEach(btn => {
                const s = parseInt(btn.dataset.star);
                const svg = btn.querySelector('svg');
                if (s <= starVal) {
                    btn.classList.add('text-amber-400');
                    btn.classList.remove('text-gray-300');
                    if (svg) svg.classList.add('fill-amber-400');
                } else {
                    btn.classList.remove('text-amber-400');
                    btn.classList.add('text-gray-300');
                    if (svg) svg.classList.remove('fill-amber-400');
                }
            });
        }
    }

    async function submitFoodOrderReview(orderId) {
        const rating = document.getElementById('order-rating-input-' + orderId)?.value || 5;
        const comment = document.getElementById('order-comment-input-' + orderId)?.value || '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const res = await fetch(`/dashboard/food-orders/${orderId}/review`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    rating: parseInt(rating),
                    comment: comment,
                })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message || 'Thank you for your feedback! Review submitted for approval.');
                const box = document.getElementById('order-review-box-' + orderId);
                if (box) {
                    box.innerHTML = `
                        <div class="flex items-center justify-between text-xs bg-white/60 p-2 rounded-xl">
                            <span class="font-bold text-amber-700">★ ${rating}.0/5 &middot; Review Posted</span>
                            <span class="text-[9px] font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full uppercase">Pending Approval</span>
                        </div>
                    `;
                }
            } else {
                alert(data.message || 'Could not submit review');
            }
        } catch(err) {
            console.error(err);
            alert('Server error submitting review');
        }
    }

    // -------------------------------------------------------------
    // Facility & Experience Booking
    // -------------------------------------------------------------
    let currentFacilityRate = 0;

    function openFacilityBookingModal(facilityId, name, rate, hasScheduling) {
        document.getElementById('fac-booking-facility-id').value = facilityId;
        document.getElementById('fac-modal-title').textContent = 'Book ' + name;
        currentFacilityRate = parseFloat(rate) || 0;

        const rateDisplay = document.getElementById('fac-modal-rate-display');
        if (currentFacilityRate > 0) {
            rateDisplay.textContent = '₹' + currentFacilityRate.toLocaleString() + ' / Guest';
        } else {
            rateDisplay.textContent = 'Complimentary';
        }

        const noteEl = document.getElementById('fac-modal-scheduling-note');
        if (hasScheduling) {
            noteEl.textContent = 'Our concierge team will allocate your scheduled time slot after booking.';
        } else {
            noteEl.textContent = 'Open access experience during operating hours.';
        }

        document.getElementById('fac-booking-guests').value = 1;
        updateFacilityBookingTotal();

        document.getElementById('modal-book-facility').classList.remove('hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeFacilityBookingModal() {
        document.getElementById('modal-book-facility').classList.add('hidden');
    }

    function updateFacilityBookingTotal() {
        const guests = parseInt(document.getElementById('fac-booking-guests').value) || 1;
        const total = currentFacilityRate * guests;
        const display = document.getElementById('fac-total-calc-display');
        if (currentFacilityRate > 0) {
            display.textContent = `Total: ₹${total.toLocaleString()}`;
        } else {
            display.textContent = 'Total: Complimentary';
        }
    }

    async function submitFacilityBooking(event) {
        event.preventDefault();
        const btn = document.getElementById('btn-submit-fac-booking');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Booking...';

        const form = document.getElementById('form-book-facility');
        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const res = await fetch('{{ route("customer.facilities.book") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Experience booked successfully! Our concierge will schedule your time slot.');
                closeFacilityBookingModal();
                window.location.reload();
            } else {
                alert(data.message || 'Failed to book facility.');
            }
        } catch(err) {
            console.error(err);
            alert('A network or server error occurred while booking.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    async function cancelFacilityBooking(bookingId) {
        if (!confirm('Are you sure you want to cancel this experience booking?')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const res = await fetch(`/dashboard/facilities/${bookingId}/cancel`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Booking cancelled successfully.');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to cancel booking.');
            }
        } catch(err) {
            console.error(err);
            alert('A server error occurred while cancelling.');
        }
    }

    // -------------------------------------------------------------
    // In-House Excursion & Taxi Booking Actions
    // -------------------------------------------------------------
    function openBookTaxiModal(spotId = null) {
        const modal = document.getElementById('modal-book-taxi');
        if (!modal) return;

        // Reset or select checkboxes
        const checkboxes = document.querySelectorAll('.taxi-location-checkbox');
        checkboxes.forEach(cb => {
            if (spotId && cb.value == spotId) {
                cb.checked = true;
            }
        });

        modal.classList.remove('hidden');
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function closeBookTaxiModal() {
        const modal = document.getElementById('modal-book-taxi');
        if (modal) modal.classList.add('hidden');
    }

    function setTaxiTime(timeStr) {
        const input = document.getElementById('taxi-pickup-time');
        if (input) input.value = timeStr;
    }

    async function submitTaxiBooking(event) {
        event.preventDefault();
        const btn = document.getElementById('btn-submit-taxi-booking');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Submitting...';

        const form = document.getElementById('form-book-taxi');
        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const res = await fetch('{{ route("customer.taxi.book") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Cab excursion request submitted! Our concierge desk will contact you with vehicle details and fare.');
                closeBookTaxiModal();
                window.location.reload();
            } else {
                alert(data.message || 'Failed to submit taxi request.');
            }
        } catch(err) {
            console.error(err);
            alert('A network or server error occurred.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    async function cancelCustomerTaxi(taxiId) {
        if (!confirm('Are you sure you want to cancel this cab excursion request?')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const res = await fetch(`/dashboard/taxi/${taxiId}/cancel`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await res.json();
            if (res.ok && data.success) {
                alert(data.message || 'Cab excursion cancelled.');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to cancel cab request.');
            }
        } catch(err) {
            console.error(err);
            alert('A server error occurred while cancelling.');
        }
    }

    // -------------------------------------------------------------
    // Initialization
    // -------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('star-rating-container')) {
            setStarRating(5);
        }
        initMarqueeHoverListeners();
        startMarquee();
        updateDrawerUI();

        @if($selectedStay && $isInHouse && (!isset($activeExt) || !$activeExt || !in_array($activeExt->status, ['pending', 'approved'])))
        if (document.getElementById('ext-new-checkout')) {
            checkExtensionAvailability({{ $selectedStay->id }});
        }
        @endif
    });
</script>
@endpush
@endsection
