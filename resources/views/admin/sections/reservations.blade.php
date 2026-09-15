<!-- STAYS & FRONT DESK HUB (ADM-02 & ADM-02B) -->
<section id="reservations" class="section space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-xl sm:text-2xl font-bold text-brand-text">Stays & Front Desk Hub</h2>
            </div>
            <p class="text-brand-muted text-xs mt-0.5">Manage live checked-in fleet, master bookings ledger, stay extensions, and counter registrations.</p>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
            <!-- Stay View Switcher Buttons -->
            <div class="bg-gray-100 p-1 rounded-xl flex items-center gap-1 border border-gray-200 shrink-0">
                <button type="button" onclick="switchReservationView('inhouse')" id="btn-view-inhouse" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-brand-text shadow-xs transition flex items-center gap-1.5 shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>In-House Fleet ({{ $checkedInReservations->count() }})</span>
                </button>
                <button type="button" onclick="switchReservationView('bookings')" id="btn-view-bookings" class="px-3 py-1.5 rounded-lg text-xs font-bold text-brand-muted hover:text-brand-text transition shrink-0">
                    All Bookings ({{ $reservations->count() }})
                </button>
                <button type="button" onclick="switchReservationView('extensions')" id="btn-view-extensions" class="px-3 py-1.5 rounded-lg text-xs font-bold text-brand-muted hover:text-brand-text transition flex items-center gap-1.5 shrink-0">
                    <span>Stay Extensions</span>
                    @php
                        $pendingExtCount = $stayExtensionRequests->where('status', 'pending')->count();
                        $unsettledExtCount = $stayExtensionRequests->where('status', 'approved')->where('payment_status', 'pending')->count();
                    @endphp
                    @if($pendingExtCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-amber-500 text-white font-bold animate-pulse">{{ $pendingExtCount }} New</span>
                    @elseif($unsettledExtCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-emerald-600 text-white font-bold">{{ $unsettledExtCount }} Due</span>
                    @endif
                </button>
            </div>

            <button onclick="openNewReservationModal()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-xs transition touch-tap shrink-0">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-brand-accent"></i> Walk-In / New Booking
            </button>
        </div>
    </div>

    <!-- VIEW 0: ACTIVE IN-HOUSE FLEET (ADM-02B) -->
    <div id="view-inhouse-container" class="space-y-4">
        <!-- In-House Search & Filter Controls -->
        <div class="bg-brand-surface rounded-2xl border border-gray-200/80 p-3.5 sm:p-4 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" class="w-4 h-4 text-brand-muted absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input 
                    type="text" 
                    id="in-house-search-input" 
                    oninput="filterInHouseList()" 
                    placeholder="Search guest name, room #, phone, booking code..." 
                    class="w-full pl-9 pr-4 py-2 bg-brand-canvas border border-gray-200 rounded-xl text-xs text-brand-text placeholder-brand-muted focus:ring-1 focus:ring-brand-primary transition"
                >
            </div>

            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full no-scrollbar">
                <button type="button" onclick="setInHouseFilter('all')" id="filter-pill-all" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white transition shrink-0 shadow-xs">
                    All In-House ({{ $checkedInReservations->count() }})
                </button>
                <button type="button" onclick="setInHouseFilter('unsettled')" id="filter-pill-unsettled" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-brand-muted hover:bg-amber-50 hover:text-amber-800 transition shrink-0 flex items-center gap-1">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-amber-500"></i> Balance Due
                </button>
                <button type="button" onclick="setInHouseFilter('food')" id="filter-pill-food" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-brand-muted hover:bg-orange-50 hover:text-orange-800 transition shrink-0 flex items-center gap-1">
                    <i data-lucide="chef-hat" class="w-3.5 h-3.5 text-orange-500"></i> Active Food Orders
                </button>
                <button type="button" onclick="setInHouseFilter('extension')" id="filter-pill-extension" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-brand-muted hover:bg-blue-50 hover:text-blue-800 transition shrink-0 flex items-center gap-1">
                    <i data-lucide="calendar-plus" class="w-3.5 h-3.5 text-blue-500"></i> Stay Extension
                </button>
                <button type="button" onclick="setInHouseFilter('departing')" id="filter-pill-departing" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-brand-muted hover:bg-purple-50 hover:text-purple-800 transition shrink-0 flex items-center gap-1">
                    <i data-lucide="log-out" class="w-3.5 h-3.5 text-purple-500"></i> Departing Today
                </button>
                <button type="button" onclick="refreshInHouseList()" class="p-1.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-brand-muted hover:text-brand-text transition border border-gray-200 shrink-0" title="Refresh Live In-House List">
                    <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        <!-- In-House Dual View: Mobile Cards + Desktop Table -->
        <div id="in-house-container" class="space-y-4">
            <!-- 1. MOBILE CARD GRID (Visible on sm & md screens) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 lg:hidden" id="in-house-mobile-grid">
                @forelse($checkedInReservations as $res)
                    @php
                        $guest = $res->guest;
                        $room = $res->room;
                        $roomType = $res->roomType;
                        $branch = $res->branch;
                        $summary = $res->calculateFolioSummary();
                        $isDepartingToday = \Carbon\Carbon::parse($res->check_out_date)->isToday();
                        $daysRemaining = max(0, \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($res->check_out_date), false));
                        $hasUnsettledExt = $res->extensionRequests->where('status', 'approved')->where('payment_status', 'pending')->isNotEmpty();
                        $hasPendingExt = $res->extensionRequests->where('status', 'pending')->isNotEmpty();
                    @endphp
                    <div 
                        class="in-house-card bg-brand-surface rounded-2xl border border-gray-200/80 p-4 shadow-xs hover:shadow-md transition space-y-3 cursor-pointer"
                        onclick="openGuest360Modal({{ $res->id }})"
                        data-guest-name="{{ strtolower($guest ? $guest->full_name : '') }}"
                        data-room-number="{{ strtolower($room ? $room->room_number : '') }}"
                        data-phone="{{ strtolower($guest ? $guest->phone : '') }}"
                        data-code="{{ strtolower($res->booking_code) }}"
                        data-is-unsettled="{{ $summary['net_due'] > 0.01 ? '1' : '0' }}"
                        data-has-food="0"
                        data-has-extension="{{ ($hasUnsettledExt || $hasPendingExt) ? '1' : '0' }}"
                        data-is-departing="{{ $isDepartingToday ? '1' : '0' }}"
                    >
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-800 text-white font-mono font-bold text-xs shadow-xs">
                                    Villa #{{ $room ? $room->room_number : 'N/A' }}
                                </span>
                                <span class="text-xs font-semibold text-brand-text truncate max-w-[150px]">
                                    {{ $roomType ? $roomType->name : 'Resort Villa' }}
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> In-House
                            </span>
                        </div>

                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="font-bold text-sm text-brand-text flex items-center gap-1.5">
                                    {{ $guest ? $guest->full_name : 'Guest' }}
                                    @if($guest && $guest->vip_level && $guest->vip_level !== 'standard')
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200">
                                            {{ $guest->vip_level }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-brand-muted mt-0.5 flex items-center gap-2">
                                    <span>📞 {{ $guest ? $guest->phone : 'N/A' }}</span>
                                    <span>•</span>
                                    <span>👥 {{ $res->adults }} Adults{{ $res->children > 0 ? ', ' . $res->children . ' Kids' : '' }}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] text-brand-muted block">Stay Window</span>
                                <span class="text-xs font-bold {{ $isDepartingToday ? 'text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded' : 'text-brand-text' }}">
                                    {{ \Carbon\Carbon::parse($res->check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($res->check_out_date)->format('d M') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-2.5 bg-brand-canvas rounded-xl border border-gray-200/60">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Net Folio Balance</span>
                                @if($summary['net_due'] <= 0.01)
                                    <span class="text-xs font-extrabold text-emerald-700 flex items-center gap-1">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> ₹0 Settled
                                    </span>
                                @else
                                    <span class="text-xs font-extrabold text-amber-700 flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> ₹{{ number_format($summary['net_due'], 2) }} Due
                                    </span>
                                @endif
                            </div>
                            <div class="text-right text-[11px] text-brand-muted">
                                <div>Total: ₹{{ number_format($summary['grand_total'], 0) }}</div>
                                <div>Paid: ₹{{ number_format($summary['paid_total'], 0) }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div class="flex items-center gap-1.5">
                                @if($hasPendingExt || $hasUnsettledExt)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 flex items-center gap-1" title="Stay Extension Active">
                                        <i data-lucide="clock" class="w-3 h-3"></i> Ext
                                    </span>
                                @endif
                                @if($isDepartingToday)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 flex items-center gap-1">
                                        <i data-lucide="log-out" class="w-3 h-3"></i> Checkout Today
                                    </span>
                                @endif
                                @if($res->is_counter_booking)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700" title="Front-desk walk-in">
                                        Walk-in
                                    </span>
                                @endif
                            </div>
                            <button type="button" class="px-3 py-1.5 bg-brand-primary hover:bg-brand-deep text-white font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1">
                                <span>Open 360° Console</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-brand-muted bg-white rounded-2xl border border-gray-200">
                        <i data-lucide="users" class="w-8 h-8 mx-auto text-gray-300 mb-2"></i>
                        <p class="text-sm font-semibold">No checked-in guests currently staying.</p>
                        <p class="text-xs mt-1">Walk-in arrivals or online check-ins will populate this hub automatically.</p>
                    </div>
                @endforelse
            </div>

            <!-- 2. DESKTOP OPERATIONAL TABLE (Visible on lg & larger screens) -->
            <div class="hidden lg:block bg-brand-surface rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="in-house-desktop-table">
                        <thead class="bg-gray-50/90 border-b border-gray-200/70 text-brand-muted uppercase font-bold text-[10px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3.5">Cottage / Unit</th>
                                <th class="px-4 py-3.5">Guest Profile</th>
                                <th class="px-4 py-3.5">Stay Window</th>
                                <th class="px-4 py-3.5">Folio Balance</th>
                                <th class="px-4 py-3.5">Active Badges</th>
                                <th class="px-4 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($checkedInReservations as $res)
                                @php
                                    $guest = $res->guest;
                                    $room = $res->room;
                                    $roomType = $res->roomType;
                                    $branch = $res->branch;
                                    $summary = $res->calculateFolioSummary();
                                    $isDepartingToday = \Carbon\Carbon::parse($res->check_out_date)->isToday();
                                    $daysRemaining = max(0, \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($res->check_out_date), false));
                                    $hasUnsettledExt = $res->extensionRequests->where('status', 'approved')->where('payment_status', 'pending')->isNotEmpty();
                                    $hasPendingExt = $res->extensionRequests->where('status', 'pending')->isNotEmpty();
                                @endphp
                                <tr 
                                    class="in-house-row hover:bg-brand-canvas/70 transition cursor-pointer"
                                    onclick="openGuest360Modal({{ $res->id }})"
                                    data-guest-name="{{ strtolower($guest ? $guest->full_name : '') }}"
                                    data-room-number="{{ strtolower($room ? $room->room_number : '') }}"
                                    data-phone="{{ strtolower($guest ? $guest->phone : '') }}"
                                    data-code="{{ strtolower($res->booking_code) }}"
                                    data-is-unsettled="{{ $summary['net_due'] > 0.01 ? '1' : '0' }}"
                                    data-has-food="0"
                                    data-has-extension="{{ ($hasUnsettledExt || $hasPendingExt) ? '1' : '0' }}"
                                    data-is-departing="{{ $isDepartingToday ? '1' : '0' }}"
                                >
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-800 text-white font-mono font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                                {{ $room ? $room->room_number : 'N/A' }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-brand-text">{{ $roomType ? $roomType->name : 'Cottage' }}</div>
                                                <div class="text-[10px] text-brand-muted">{{ $branch ? $branch->name : 'Resort' }} · Floor {{ $room ? $room->floor : '1' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-brand-text flex items-center gap-1.5">
                                            {{ $guest ? $guest->full_name : 'Guest' }}
                                            @if($guest && $guest->vip_level && $guest->vip_level !== 'standard')
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-amber-100 text-amber-900 border border-amber-200">
                                                    {{ $guest->vip_level }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-brand-muted mt-0.5">
                                            {{ $guest ? $guest->phone : 'N/A' }} · {{ $res->adults }}A{{ $res->children > 0 ? ', ' . $res->children . 'C' : '' }}
                                        </div>
                                        <div class="text-[10px] text-brand-muted font-mono">{{ $res->booking_code }}</div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-brand-text">
                                            {{ \Carbon\Carbon::parse($res->check_in_date)->format('d M') }} &rarr; {{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}
                                        </div>
                                        <div class="text-[11px] mt-0.5">
                                            @if($isDepartingToday)
                                                <span class="font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md">Checking Out Today</span>
                                            @else
                                                <span class="text-brand-muted">{{ $daysRemaining }} night(s) remaining</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($summary['net_due'] <= 0.01)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> ₹0 Settled
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-200 inline-flex items-center gap-1">
                                                <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> ₹{{ number_format($summary['net_due'], 2) }} Due
                                            </span>
                                        @endif
                                        <div class="text-[10px] text-brand-muted mt-0.5">
                                            Paid: ₹{{ number_format($summary['paid_total'], 0) }} / Total: ₹{{ number_format($summary['grand_total'], 0) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if($hasPendingExt || $hasUnsettledExt)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200" title="Stay Extension Active">
                                                    ⏳ Extension
                                                </span>
                                            @endif
                                            @if($res->is_counter_booking)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                    Counter Walk-in
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <button 
                                            type="button" 
                                            onclick="event.stopPropagation(); openGuest360Modal({{ $res->id }})"
                                            class="px-3.5 py-1.5 rounded-xl bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs transition inline-flex items-center gap-1.5"
                                        >
                                            <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                                            <span>360° Console</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-brand-muted">
                                        <i data-lucide="users" class="w-8 h-8 mx-auto text-gray-300 mb-2"></i>
                                        <p class="text-sm font-semibold">No checked-in guests currently staying.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 1: REGULAR RESERVATIONS LIST -->
    <div id="view-bookings-container" class="space-y-4 hidden">
        <!-- Filter & Search Toolbar -->
        <div class="bg-brand-surface p-3.5 rounded-xl border border-gray-200/70 shadow-xs flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button onclick="filterReservations('all', this)" class="res-filter-btn px-3 py-1 rounded-lg text-xs font-semibold bg-brand-primary text-white transition">All ({{ $reservations->count() }})</button>
                <button onclick="filterReservations('confirmed', this)" class="res-filter-btn px-3 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition">Confirmed ({{ $reservations->where('status', 'confirmed')->count() }})</button>
                <button onclick="filterReservations('checked_in', this)" class="res-filter-btn px-3 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition">Checked In ({{ $reservations->where('status', 'checked_in')->count() }})</button>
                <button onclick="filterReservations('checked_out', this)" class="res-filter-btn px-3 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition">Checked Out ({{ $reservations->where('status', 'checked_out')->count() }})</button>
                <button onclick="filterReservations('cancelled', this)" class="res-filter-btn px-3 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition">Cancelled ({{ $reservations->where('status', 'cancelled')->count() }})</button>
            </div>
            <div class="relative w-full sm:w-64">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-gray-400"></i>
                <input type="text" id="res-search-input" onkeyup="searchReservationTable()" placeholder="Search guest, code, room..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-brand-canvas border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary">
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="reservations-table">
                    <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Booking Code</th>
                            <th class="px-4 py-3">Guest Details</th>
                            <th class="px-4 py-3">Branch & Room</th>
                            <th class="px-4 py-3">Stay Dates</th>
                            <th class="px-4 py-3">Booking Status</th>
                            <th class="px-4 py-3">Payment</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reservations as $res)
                        <tr class="res-row hover:bg-brand-canvas/60 transition cursor-pointer" data-status="{{ $res->status }}" onclick="openReservationDrawer({{ $res->id }})">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-brand-text">{{ $res->booking_code }}</div>
                                <div class="text-[10px] text-brand-muted flex items-center gap-1">
                                    <span>{{ $res->created_at->format('d M Y') }}</span>
                                    @if($res->is_counter_booking)
                                        <span class="px-1.5 py-0.2 rounded bg-purple-100 text-purple-800 text-[9px] font-bold">Front-Desk</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-brand-text">{{ $res->guest ? $res->guest->full_name : 'Guest' }}</div>
                                <div class="text-[11px] text-brand-muted">{{ $res->guest ? $res->guest->phone : 'N/A' }}</div>
                                @if($res->id_proof_type)
                                    <div class="text-[9px] text-emerald-800 font-mono">ID: {{ ucfirst($res->id_proof_type) }} ({{ $res->id_proof_number }})</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-brand-text">{{ $res->branch ? $res->branch->name : 'Resort' }}</div>
                                <div class="text-[11px] text-brand-muted">{{ $res->roomType ? $res->roomType->name : '' }}</div>
                                <div class="mt-0.5">
                                    @if($res->room)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Villa {{ $res->room->room_number }} ({{ $res->room->floor }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Unassigned
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-brand-text">{{ $res->check_in_date->format('d M Y') }} &rarr; {{ $res->check_out_date->format('d M Y') }}</div>
                                <div class="text-[11px] text-brand-muted">{{ $res->check_in_date->diffInDays($res->check_out_date) }} Nights · {{ $res->adults }} Adults, {{ $res->children }} Children</div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($res->status === 'confirmed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Confirmed</span>
                                @elseif($res->status === 'checked_in')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Checked In</span>
                                @elseif($res->status === 'checked_out')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">Checked Out</span>
                                @elseif($res->status === 'cancelled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">Cancelled</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-50 text-gray-700 border border-gray-200">{{ ucfirst($res->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($res->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span>
                                @elseif($res->payment_status === 'partial')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Partial (₹{{ number_format($res->paid_amount) }})</span>
                                @elseif($res->payment_status === 'refunded')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">Refunded (₹{{ number_format($res->refunded_amount) }})</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-brand-text">
                                ₹{{ number_format($res->total_amount) }}
                            </td>
                            <td class="px-4 py-3.5 text-center" onclick="event.stopPropagation()">
                                <button onclick="openReservationDrawer({{ $res->id }})" class="p-1.5 text-brand-muted hover:text-brand-primary hover:bg-gray-100 rounded-lg transition" title="Inspect details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-brand-muted text-sm">
                                <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                No reservations match the selected criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- VIEW 2: STAY EXTENSION REQUESTS QUEUE -->
    <div id="view-extensions-container" class="hidden space-y-4">
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
            <div class="p-3.5 border-b border-gray-100 flex justify-between items-center bg-gray-50/60">
                <div>
                    <h3 class="text-xs font-bold text-brand-text">In-House Stay Extension Queue</h3>
                    <p class="text-[10px] text-brand-muted">Guest requests to extend holiday dates with smart room re-allocations and manager loyalty offers</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Ext. ID & Date</th>
                            <th class="px-4 py-3">Guest & Stay Ref</th>
                            <th class="px-4 py-3">Extension Window</th>
                            <th class="px-4 py-3">Allocated Unit(s)</th>
                            <th class="px-4 py-3">Standard vs Offer Rate</th>
                            <th class="px-4 py-3">Approval Status</th>
                            <th class="px-4 py-3">Payment Folio Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stayExtensionRequests as $ext)
                        <tr class="hover:bg-brand-canvas/60 transition">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-brand-text">EXT #{{ $ext->id }}</div>
                                <div class="text-[10px] text-brand-muted">{{ $ext->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-brand-text">{{ $ext->guest ? $ext->guest->full_name : ($ext->user ? $ext->user->name : 'Guest') }}</div>
                                <div class="text-[10px] font-mono text-brand-primary">Code: {{ $ext->reservation ? $ext->reservation->booking_code : 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-brand-text">{{ $ext->current_checkout_date->format('d M') }} &rarr; <span class="text-emerald-700 font-bold">{{ $ext->requested_checkout_date->format('d M Y') }}</span></div>
                                <div class="text-[10px] text-brand-muted">+{{ $ext->extra_nights }} Extra Night(s)</div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($ext->allocation_type === 'same_room')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <i data-lucide="check" class="w-3 h-3"></i> Same Villa
                                    </span>
                                @elseif($ext->allocation_type === 'single_room')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                        Alternate Suite Upgrade
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                        Multi-Room Pair
                                    </span>
                                @endif
                                <div class="text-[10px] text-brand-muted mt-0.5">
                                    Units: {{ !empty($ext->allocated_room_ids) ? implode(', ', $ext->allocated_room_ids) : 'Current Unit' }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-brand-muted line-through text-[11px]">₹{{ number_format($ext->standard_amount) }}</div>
                                @if($ext->offered_amount)
                                    <div class="font-extrabold text-sm text-emerald-700">₹{{ number_format($ext->offered_amount) }}</div>
                                    @if($ext->manager_discount_percentage > 0)
                                        <span class="text-[9px] bg-emerald-100 text-emerald-800 px-1.5 py-0.2 rounded font-bold">Save {{ number_format($ext->manager_discount_percentage, 0) }}%</span>
                                    @endif
                                @else
                                    <div class="text-[10px] text-amber-700 font-semibold italic">Awaiting Offer</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($ext->status === 'approved')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Approved & Locked</span>
                                @elseif($ext->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Completed</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Pending Review</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($ext->payment_status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Paid ({{ ucfirst($ext->payment_method ?? 'Settled') }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        Folio Pending (On-Hand / Checkout)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1">
                                @if($ext->status === 'pending')
                                    <button onclick='openExtensionOfferModal(@json($ext))' class="px-2.5 py-1 bg-brand-primary hover:bg-brand-deep text-white font-bold rounded-lg text-xs shadow-xs transition">
                                        Make Offer & Approve
                                    </button>
                                @endif

                                @if($ext->payment_status === 'pending')
                                    <button onclick='openExtensionPaymentModal(@json($ext))' class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg text-xs shadow-xs transition flex items-center gap-1 inline-flex">
                                        <i data-lucide="receipt" class="w-3 h-3"></i> Record Payment
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-brand-muted">No stay extension requests submitted yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
