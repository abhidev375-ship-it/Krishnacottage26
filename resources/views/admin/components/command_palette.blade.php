<!-- COMMAND PALETTE MODAL (ADM-30) -->
<div id="cmd-palette" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-10 sm:pt-24 p-3 sm:p-4">
    <div class="fixed inset-0 bg-brand-text/50 backdrop-blur-xs transition-opacity" onclick="closeCommandPalette()"></div>

    <div class="relative w-full max-w-2xl bg-brand-surface rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-200 z-10 max-h-[85vh]">
        <!-- Search Input Bar -->
        <div class="flex items-center px-4 py-3.5 border-b border-gray-200 shrink-0">
            <i data-lucide="search" class="w-5 h-5 text-gray-400 shrink-0"></i>
            <input type="text" id="cmd-search-input" onkeyup="handleCommandPaletteSearch(this.value)" placeholder="Search bookings, guests, rooms, dishes, spices... (Type 2+ chars)" class="w-full border-0 bg-transparent px-3 text-sm text-brand-text placeholder-gray-400 focus:outline-none focus:ring-0">
            <kbd class="hidden sm:inline-block border border-gray-200 rounded px-1.5 py-0.5 text-[10px] text-gray-400 bg-gray-50">ESC</kbd>
        </div>

        <!-- Search Results / Quick Suggestions -->
        <div class="max-h-[65vh] sm:max-h-96 overflow-y-auto p-2 space-y-1 text-xs" id="cmd-results-container">
            <div class="p-3 text-[11px] font-bold text-brand-muted uppercase tracking-wider">Quick Navigation</div>
            
            <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-100 cursor-pointer transition text-brand-text" onclick="navigateTo('dashboard'); closeCommandPalette()">
                <div class="w-7 h-7 rounded bg-emerald-50 text-emerald-700 flex items-center justify-center"><i data-lucide="layout-dashboard" class="w-4 h-4"></i></div>
                <div>
                    <div class="font-bold">Dashboard</div>
                    <div class="text-[10px] text-brand-muted">Operational summaries and arrivals</div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-100 cursor-pointer transition text-brand-text" onclick="navigateTo('reservations'); closeCommandPalette()">
                <div class="w-7 h-7 rounded bg-blue-50 text-blue-700 flex items-center justify-center"><i data-lucide="calendar-check" class="w-4 h-4"></i></div>
                <div>
                    <div class="font-bold">Reservations</div>
                    <div class="text-[10px] text-brand-muted">Search, assign rooms, check-in guests</div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-100 cursor-pointer transition text-brand-text" onclick="navigateTo('room-status'); closeCommandPalette()">
                <div class="w-7 h-7 rounded bg-amber-50 text-amber-700 flex items-center justify-center"><i data-lucide="clipboard-check" class="w-4 h-4"></i></div>
                <div>
                    <div class="font-bold">Room Status Board</div>
                    <div class="text-[10px] text-brand-muted">Live housekeeping clean/dirty states</div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-100 cursor-pointer transition text-brand-text" onclick="navigateTo('menu'); closeCommandPalette()">
                <div class="w-7 h-7 rounded bg-orange-50 text-orange-700 flex items-center justify-center"><i data-lucide="utensils" class="w-4 h-4"></i></div>
                <div>
                    <div class="font-bold">Dining Menu</div>
                    <div class="text-[10px] text-brand-muted">Toggle dish stock and modifiers</div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-100 cursor-pointer transition text-brand-text" onclick="navigateTo('inventory'); closeCommandPalette()">
                <div class="w-7 h-7 rounded bg-purple-50 text-purple-700 flex items-center justify-center"><i data-lucide="package" class="w-4 h-4"></i></div>
                <div>
                    <div class="font-bold">Spice Inventory</div>
                    <div class="text-[10px] text-brand-muted">Audit and adjust spice stock</div>
                </div>
            </div>
        </div>
    </div>
</div>
