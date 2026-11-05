<!-- DASHBOARD SECTION (ADM-01) -->
<section id="dashboard" class="section active space-y-6">
    <!-- TOP VIEW SWITCHER TOOLBAR -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-gray-200/80 shadow-xs">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-xl bg-brand-canvas flex items-center justify-center text-brand-primary">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-brand-text">Resort Operations Hub</h2>
                <p class="text-brand-muted text-[11px]">Real-time operational command · {{ $selectedBranch ? $selectedBranch->name : 'All Branches Centralised' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="#cms" onclick="navigateTo('cms')" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-xl text-xs font-semibold transition border border-gray-200">
                <i data-lucide="palette" class="w-3.5 h-3.5 text-brand-primary"></i> Live Website Studio
            </a>
            <button onclick="openNewReservationModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-deep hover:bg-black text-white text-xs font-semibold rounded-xl shadow-xs transition shrink-0 touch-tap">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-brand-accent"></i> New Booking
            </button>
        </div>
    </div>

    <!-- VIEW 1: OPERATIONS & METRICS VIEW (Active by default) -->
    <div id="operations-kpi-view" class="space-y-6">
        <!-- 6 Primary Metric Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs hover:border-brand-primary/40 transition">
                <div class="flex items-center justify-between text-brand-muted mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Arrivals</span>
                    <i data-lucide="plane-landing" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <div class="text-2xl font-bold text-brand-text">{{ $arrivalsCount }}</div>
                <div class="text-[11px] text-brand-muted mt-1">Expected today</div>
            </div>

            <div class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs hover:border-brand-primary/40 transition">
                <div class="flex items-center justify-between text-brand-muted mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Departures</span>
                    <i data-lucide="plane-takeoff" class="w-4 h-4 text-amber-600"></i>
                </div>
                <div class="text-2xl font-bold text-brand-text">{{ $departuresCount }}</div>
                <div class="text-[11px] text-brand-muted mt-1">Checking out</div>
            </div>

            <div onclick="navigateTo('in-house')" class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs hover:border-brand-primary/60 hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center justify-between text-brand-muted mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider group-hover:text-brand-primary">In-House</span>
                    <i data-lucide="users" class="w-4 h-4 text-emerald-600 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="text-2xl font-bold text-brand-text">{{ $stayingCount }}</div>
                <div class="text-[11px] text-emerald-700 font-semibold mt-1 flex items-center gap-1">
                    <span>Active stays</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </div>
            </div>

            <div class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs hover:border-brand-primary/40 transition">
                <div class="flex items-center justify-between text-brand-muted mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Available</span>
                    <i data-lucide="door-open" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <div class="text-2xl font-bold text-emerald-700">{{ $availableRoomsCount }} <span class="text-xs font-normal text-brand-muted">/ {{ $totalRoomsCount }}</span></div>
                <div class="text-[11px] text-emerald-600 font-medium mt-1">{{ $occupiedRoomsCount }} occupied</div>
            </div>

            <div class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs hover:border-brand-primary/40 transition">
                <div class="flex items-center justify-between text-brand-muted mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider">Food Orders</span>
                    <i data-lucide="chef-hat" class="w-4 h-4 text-orange-600"></i>
                </div>
                <div class="text-2xl font-bold {{ $pendingFoodOrders > 0 ? 'text-orange-600' : 'text-brand-text' }}">{{ $pendingFoodOrders }}</div>
                <div class="text-[11px] text-brand-muted mt-1">Active kitchen tickets</div>
            </div>

            <div onclick="navigateTo('spices')" class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs hover:border-emerald-600/50 hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center justify-between text-brand-muted mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider group-hover:text-emerald-700">Spices</span>
                    <i data-lucide="leaf" class="w-4 h-4 text-emerald-600 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="text-2xl font-bold text-brand-text">{{ $freshSpiceOrdersCount ?? $spiceOrders->whereIn('status', ['processing', 'paid', 'new'])->count() }}</div>
                <div class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center justify-between">
                    <span>Pack-on-order</span>
                    @if(($pendingSpiceReturnsCount ?? 0) > 0)
                        <span class="bg-rose-100 text-rose-700 px-1.5 py-0.2 rounded text-[10px] font-bold">{{ $pendingSpiceReturnsCount }} Return req</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- TODAY'S FLIGHT DECK: FAST ARRIVALS & DEPARTURES TRAY (1-Tap Operation) -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 bg-gray-50/70 border-b border-gray-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex p-1.5 rounded-lg bg-brand-deep text-brand-accent">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-sm text-brand-text">Today's Flight Deck — 1-Tap Desk Command</h3>
                        <p class="text-[11px] text-brand-muted">Instant arrivals check-in, room key allocation, and express checkout ledger</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        <i data-lucide="plane-landing" class="w-3.5 h-3.5"></i> {{ $todayArrivals->count() }} Arrivals Today
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                        <i data-lucide="plane-takeoff" class="w-3.5 h-3.5"></i> {{ $todayDepartures->count() }} Departures Today
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
                <!-- Column A: Today's Expected Arrivals -->
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-brand-text uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="log-in" class="w-3.5 h-3.5 text-emerald-600"></i> Expected Arrivals Today
                        </h4>
                        <span class="text-[11px] text-brand-muted">{{ $todayArrivals->where('status', 'confirmed')->count() }} pending check-in</span>
                    </div>

                    <div class="space-y-2.5 max-h-[360px] overflow-y-auto pr-1">
                        @forelse($todayArrivals as $arr)
                        <div class="p-3 rounded-xl border border-gray-200/80 hover:border-emerald-300 bg-white hover:bg-emerald-50/20 transition flex items-center justify-between gap-3 shadow-2xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-brand-text truncate">{{ $arr->guest ? $arr->guest->full_name : 'Guest' }}</span>
                                    @if($arr->status === 'checked_in')
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">In-House</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Confirmed</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-brand-muted flex items-center gap-2 mt-0.5 flex-wrap">
                                    <span class="font-mono text-gray-500">{{ $arr->booking_code }}</span>
                                    <span>·</span>
                                    <span>{{ $arr->roomType ? $arr->roomType->name : 'Room' }}</span>
                                    <span>·</span>
                                    <span class="font-semibold {{ $arr->room ? 'text-emerald-700' : 'text-amber-600' }}">
                                        {{ $arr->room ? 'Room ' . $arr->room->room_number : 'Room Unassigned' }}
                                    </span>
                                </div>
                                @if($arr->guest && $arr->guest->phone)
                                <div class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                                    <i data-lucide="phone" class="w-3 h-3"></i> {{ $arr->guest->phone }}
                                </div>
                                @endif
                            </div>

                            <div class="shrink-0 flex items-center gap-1.5">
                                @if($arr->status === 'checked_in')
                                    <button type="button" onclick="openGuest360Modal({{ $arr->id }})" class="px-2.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs rounded-lg border border-emerald-200 flex items-center gap-1 transition">
                                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i> View 360
                                    </button>
                                @elseif($arr->room)
                                    <button type="button" onclick="quickCheckIn({{ $arr->id }}, '{{ addslashes($arr->guest ? $arr->guest->full_name : 'Guest') }}', '{{ $arr->room->room_number }}')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow-xs flex items-center gap-1.5 transition">
                                        <i data-lucide="zap" class="w-3.5 h-3.5"></i> 1-Tap Check-In
                                    </button>
                                @else
                                    <button type="button" onclick="openAssignRoomModal({{ $arr->id }}, '{{ addslashes($arr->guest ? $arr->guest->full_name : 'Guest') }}', {{ $arr->room_type_id ?? 'null' }}, {{ $arr->branch_id }})" class="px-3 py-1.5 bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs rounded-lg shadow-xs flex items-center gap-1.5 transition">
                                        <i data-lucide="key" class="w-3.5 h-3.5"></i> Assign & Check-In
                                    </button>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="py-8 text-center text-gray-400">
                            <i data-lucide="check-circle-2" class="w-6 h-6 mx-auto mb-1 text-emerald-500 opacity-60"></i>
                            <p class="text-xs">No pending arrivals scheduled for today.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Column B: Today's Scheduled Departures -->
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-brand-text uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="log-out" class="w-3.5 h-3.5 text-amber-600"></i> Scheduled Departures Today
                        </h4>
                        <span class="text-[11px] text-brand-muted">{{ $todayDepartures->count() }} checking out</span>
                    </div>

                    <div class="space-y-2.5 max-h-[360px] overflow-y-auto pr-1">
                        @forelse($todayDepartures as $dep)
                        @php
                            $folioSummary = $dep->calculateFolioSummary();
                            $netDue = $folioSummary['net_due'] ?? 0;
                        @endphp
                        <div class="p-3 rounded-xl border border-gray-200/80 hover:border-amber-300 bg-white hover:bg-amber-50/20 transition flex items-center justify-between gap-3 shadow-2xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-brand-text truncate">{{ $dep->guest ? $dep->guest->full_name : 'Guest' }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">
                                        {{ $dep->room ? 'Room ' . $dep->room->room_number : 'Room' }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-brand-muted flex items-center gap-2 mt-0.5 flex-wrap">
                                    <span class="font-mono text-gray-500">{{ $dep->booking_code }}</span>
                                    <span>·</span>
                                    @if($netDue > 0.01)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 font-bold text-[10px]">
                                            Due: ₹{{ number_format($netDue, 2) }}
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                            Folio Settled ✓
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="shrink-0 flex items-center gap-1.5">
                                <button type="button" onclick="quickCheckOut({{ $dep->id }}, '{{ addslashes($dep->guest ? $dep->guest->full_name : 'Guest') }}', '{{ $dep->room ? $dep->room->room_number : '' }}', {{ $netDue }})" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-lg shadow-xs flex items-center gap-1.5 transition">
                                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Express Check-Out
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="py-8 text-center text-gray-400">
                            <i data-lucide="check-circle-2" class="w-6 h-6 mx-auto mb-1 text-emerald-500 opacity-60"></i>
                            <p class="text-xs">No departures scheduled for today.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Operational Split -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Attention Required (ADM-01 feed) -->
            <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs flex flex-col">
                <div class="p-3.5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-brand-text text-sm flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-amber-500"></i> Attention Required
                    </h3>
                    <span class="text-xs text-brand-muted">Action items</span>
                </div>
                <div class="p-3.5 space-y-2.5 flex-1">
                    @if($unreadMessagesCount > 0)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-amber-50/70 border border-amber-200/60 cursor-pointer hover:bg-amber-100/60 transition" onclick="navigateTo('messages')">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                                <i data-lucide="message-square" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-amber-900">{{ $unreadMessagesCount }} Unread Enquiries</div>
                                <div class="text-[11px] text-amber-700">Guest questions awaiting response</div>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-amber-600"></i>
                    </div>
                    @endif

                    @if($pendingReviewsCount > 0)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-purple-50/70 border border-purple-200/60 cursor-pointer hover:bg-purple-100/60 transition" onclick="navigateTo('reviews')">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center">
                                <i data-lucide="star" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-purple-900">{{ $pendingReviewsCount }} Reviews Pending Moderation</div>
                                <div class="text-[11px] text-purple-700">Verified stay guest feedback</div>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-purple-600"></i>
                    </div>
                    @endif

                    @if(($pendingSpiceReturnsCount ?? 0) > 0)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-rose-50/70 border border-rose-200/60 cursor-pointer hover:bg-rose-100/60 transition" onclick="navigateTo('spices'); if(typeof switchSpiceTab === 'function') switchSpiceTab('policy');">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-rose-900">{{ $pendingSpiceReturnsCount }} Spice Return / Cancellation Request{{ $pendingSpiceReturnsCount > 1 ? 's' : '' }}</div>
                                <div class="text-[11px] text-rose-700">Awaiting refund policy decision &amp; action</div>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-rose-600"></i>
                    </div>
                    @endif

                    @if($maintenanceRoomsCount > 0)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-200 cursor-pointer hover:bg-gray-100 transition" onclick="navigateTo('room-status')">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-200 text-gray-700 flex items-center justify-center">
                                <i data-lucide="wrench" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-900">{{ $maintenanceRoomsCount }} Room(s) Blocked / Maintenance</div>
                                <div class="text-[11px] text-gray-600">Inspection & timber upkeep</div>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-500"></i>
                    </div>
                    @endif

                    @if($unreadMessagesCount == 0 && $pendingReviewsCount == 0 && $lowStockProducts == 0 && $maintenanceRoomsCount == 0)
                    <div class="py-10 text-center text-brand-muted">
                        <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500 mx-auto mb-2 opacity-75"></i>
                        <p class="text-xs font-medium">All operations clear! No urgent attention required.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Stays Overview -->
            <div class="lg:col-span-2 bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs flex flex-col">
                <div class="p-3.5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-brand-text text-sm flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-4 h-4 text-brand-primary"></i> Active & Upcoming Reservations
                    </h3>
                    <button onclick="navigateTo('reservations')" class="text-xs font-semibold text-brand-primary hover:text-brand-deep hover:underline">
                        View all ({{ $reservations->count() }})
                    </button>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/75 border-b border-gray-100 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                            <tr>
                                <th class="px-3.5 py-2.5">Code / Guest</th>
                                <th class="px-3.5 py-2.5">Dates</th>
                                <th class="px-3.5 py-2.5">Branch / Room</th>
                                <th class="px-3.5 py-2.5">Status</th>
                                <th class="px-3.5 py-2.5 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reservations->take(5) as $res)
                            <tr class="hover:bg-brand-canvas/60 cursor-pointer transition" onclick="openReservationDrawer({{ $res->id }})">
                                <td class="px-3.5 py-3">
                                    <div class="font-semibold text-brand-text">{{ $res->booking_code }}</div>
                                    <div class="text-[11px] text-brand-muted">{{ $res->guest ? $res->guest->full_name : 'Guest' }}</div>
                                </td>
                                <td class="px-3.5 py-3">
                                    <div>{{ $res->check_in_date->format('d M Y') }}</div>
                                    <div class="text-[11px] text-brand-muted">&rarr; {{ $res->check_out_date->format('d M Y') }}</div>
                                </td>
                                <td class="px-3.5 py-3">
                                    <div class="font-medium text-brand-text">{{ $res->branch ? $res->branch->code : '' }} · {{ $res->roomType ? $res->roomType->name : '' }}</div>
                                    <div class="text-[11px] text-brand-muted">{{ $res->room ? 'Room ' . $res->room->room_number : 'Unassigned' }}</div>
                                </td>
                                <td class="px-3.5 py-3">
                                    @if($res->status === 'confirmed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Confirmed</span>
                                    @elseif($res->status === 'checked_in')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Checked In</span>
                                    @elseif($res->status === 'checked_out')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">Checked Out</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-50 text-gray-600 border border-gray-200">{{ ucfirst($res->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-3.5 py-3 text-right font-semibold text-brand-text">
                                    ₹{{ number_format($res->total_amount) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-brand-muted">No reservations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
