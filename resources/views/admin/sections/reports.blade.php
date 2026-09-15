<!-- REPORTS & ANALYTICS SECTION (ADM-25) -->
<section id="reports" class="section space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-brand-text">Performance Reports & Analytics</h2>
            <p class="text-brand-muted text-xs mt-0.5">Occupancy trends, revenue breakdown across stays, dining, and spices.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <div class="inline-flex p-0.5 bg-gray-100 rounded-lg border border-gray-200 text-xs font-semibold">
                <button type="button" onclick="filterReportsDateRange('all')" id="btn-rep-all" class="px-2.5 py-1 rounded-md bg-white text-brand-deep shadow-2xs">All Time</button>
                <button type="button" onclick="filterReportsDateRange('month')" id="btn-rep-month" class="px-2.5 py-1 rounded-md text-brand-muted hover:text-brand-text">This Month</button>
                <button type="button" onclick="filterReportsDateRange('today')" id="btn-rep-today" class="px-2.5 py-1 rounded-md text-brand-muted hover:text-brand-text">Today</button>
            </div>
            <div class="relative inline-block text-left" id="csv-export-dropdown-container">
                <button type="button" onclick="document.getElementById('csv-export-menu').classList.toggle('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-surface border border-gray-300 hover:bg-gray-50 text-brand-text text-xs font-semibold rounded-lg shadow-xs transition">
                    <i data-lucide="download" class="w-3.5 h-3.5 text-emerald-600"></i> Export CSV Ledger <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                </button>
                <div id="csv-export-menu" class="hidden absolute right-0 mt-1.5 w-52 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5 z-30 text-xs">
                    <a href="{{ route('admin.reports.export-csv') }}?type=reservations" class="flex items-center gap-2 px-3.5 py-2 hover:bg-brand-canvas text-gray-700 hover:text-brand-deep">
                        <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-emerald-600"></i> Reservations & Stays
                    </a>
                    <a href="{{ route('admin.reports.export-csv') }}?type=dining" class="flex items-center gap-2 px-3.5 py-2 hover:bg-brand-canvas text-gray-700 hover:text-brand-deep">
                        <i data-lucide="utensils" class="w-3.5 h-3.5 text-amber-600"></i> Dining & Orders
                    </a>
                    <a href="{{ route('admin.reports.export-csv') }}?type=spices" class="flex items-center gap-2 px-3.5 py-2 hover:bg-brand-canvas text-gray-700 hover:text-brand-deep">
                        <i data-lucide="package" class="w-3.5 h-3.5 text-orange-600"></i> Spices & Shipments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 High Level Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs">
            <span class="text-xs text-brand-muted uppercase font-bold tracking-wider">Accommodation Revenue</span>
            <div class="text-2xl font-extrabold text-brand-text mt-1">₹{{ number_format($reservations->sum('total_amount')) }}</div>
            <div class="text-[11px] text-emerald-700 mt-1 font-semibold">From {{ $reservations->count() }} bookings</div>
        </div>

        <div class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs">
            <span class="text-xs text-brand-muted uppercase font-bold tracking-wider">Dining & Room Service</span>
            <div class="text-2xl font-extrabold text-brand-text mt-1">₹{{ number_format($foodOrders->sum('total_amount')) }}</div>
            <div class="text-[11px] text-emerald-700 mt-1 font-semibold">{{ $foodOrders->count() }} Orders processed</div>
        </div>

        <div class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs">
            <span class="text-xs text-brand-muted uppercase font-bold tracking-wider">Krishna Spices E-Commerce</span>
            <div class="text-2xl font-extrabold text-brand-text mt-1">₹{{ number_format($spiceOrders->sum('total_amount')) }}</div>
            <div class="text-[11px] text-emerald-700 mt-1 font-semibold">{{ $spiceOrders->count() }} Shipments fulfilled</div>
        </div>

        <div class="bg-brand-surface p-4 rounded-xl border border-gray-200/70 shadow-xs">
            <span class="text-xs text-brand-muted uppercase font-bold tracking-wider">Total Gross Revenue</span>
            <div class="text-2xl font-extrabold text-emerald-800 mt-1">
                ₹{{ number_format($reservations->sum('total_amount') + $foodOrders->sum('total_amount') + $spiceOrders->sum('total_amount')) }}
            </div>
            <div class="text-[11px] text-brand-muted mt-1 font-medium">All revenue streams</div>
        </div>
    </div>

    <!-- Branch Wise Breakdown Table -->
    <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
        <div class="p-3.5 border-b border-gray-100 font-bold text-sm text-brand-text flex items-center justify-between">
            <span>Branch-by-Branch Performance Breakdown</span>
            <span class="text-xs text-brand-muted">{{ $branches->count() }} Destinations</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Branch Name</th>
                        <th class="px-4 py-3 text-center">Room Inventory</th>
                        <th class="px-4 py-3 text-center">Active Bookings</th>
                        <th class="px-4 py-3 text-center">Dining Orders</th>
                        <th class="px-4 py-3 text-right">Accommodation Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($branches as $b)
                    <tr class="hover:bg-brand-canvas/60 transition">
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-brand-text">{{ $b->name }}</div>
                            <div class="text-[11px] text-brand-muted">{{ $b->city }}, {{ $b->state }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-center font-bold text-brand-text">
                            {{ $b->rooms->count() }} Units
                        </td>
                        <td class="px-4 py-3.5 text-center font-semibold text-brand-text">
                            {{ $reservations->where('branch_id', $b->id)->count() }}
                        </td>
                        <td class="px-4 py-3.5 text-center font-semibold text-brand-text">
                            {{ $foodOrders->where('branch_id', $b->id)->count() }}
                        </td>
                        <td class="px-4 py-3.5 text-right font-bold text-brand-text">
                            ₹{{ number_format($reservations->where('branch_id', $b->id)->sum('total_amount')) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
