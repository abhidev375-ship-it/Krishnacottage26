<!-- GUESTS CRM SECTION (ADM-08) -->
<section id="guests" class="section space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-brand-text">Guest Directory & CRM</h2>
            <p class="text-brand-muted text-xs mt-0.5">Customer profiles, VIP tiers, historical stays, preferences, and cumulative expenditure.</p>
        </div>
        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-gray-400"></i>
            <input type="text" id="guest-search-input" onkeyup="searchGuestTable()" placeholder="Search guest name, phone, city..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-brand-canvas border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary">
        </div>
    </div>

    <!-- Guests Table -->
    <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="guests-table">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Guest Profile</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">VIP Tier</th>
                        <th class="px-4 py-3 text-center">Stays</th>
                        <th class="px-4 py-3 text-right">Total Spent</th>
                        <th class="px-4 py-3">Preferences & Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($guests as $g)
                    <tr class="guest-row hover:bg-brand-canvas/60 transition">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-primary/10 text-brand-primary font-bold flex items-center justify-center text-xs">
                                    {{ substr($g->first_name, 0, 1) }}{{ substr($g->last_name ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-brand-text">{{ $g->full_name }}</div>
                                    <div class="text-[10px] text-brand-muted">Guest #{{ str_pad($g->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-medium text-brand-text">{{ $g->phone ?? 'N/A' }}</div>
                            <div class="text-[11px] text-brand-muted">{{ $g->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-brand-text font-medium">
                            {{ $g->city ? $g->city . ', ' : '' }}{{ $g->country }}
                        </td>
                        <td class="px-4 py-3.5">
                            @if($g->vip_level === 'gold')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">Gold VIP</span>
                            @elseif($g->vip_level === 'silver')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-300">Silver VIP</span>
                            @elseif($g->vip_level === 'platinum')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-900 border border-purple-300">Platinum</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700">Standard</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center font-bold text-brand-text">
                            {{ $g->reservations->count() > 0 ? $g->reservations->count() : $g->total_stays }}
                        </td>
                        <td class="px-4 py-3.5 text-right font-bold text-brand-text">
                            ₹{{ number_format($g->total_spent) }}
                        </td>
                        <td class="px-4 py-3.5 max-w-xs truncate text-[11px] text-brand-muted">
                            {{ $g->preferences ?? 'None recorded' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-brand-muted">No guest profiles found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
