<!-- INDIVIDUAL CLIENT 360° IN-HOUSE CONSOLE MODAL (ADM-02C) -->
<div id="modal-guest-360" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-0 md:p-4 transition-all">
    <div class="w-full h-full md:max-w-6xl md:h-auto md:max-h-[92vh] bg-white md:rounded-2xl shadow-2xl flex flex-col overflow-hidden text-brand-text">
        
        <!-- TOP APP BAR / GUEST BANNER -->
        <div class="bg-brand-deep text-white px-3 sm:px-6 py-2 sm:py-3.5 flex items-center justify-between shrink-0 shadow-md">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <button type="button" onclick="closeGuest360Modal()" class="p-1 sm:p-1.5 -ml-1 text-white/70 hover:text-white rounded-lg hover:bg-white/10 transition shrink-0" title="Close / Back to In-House Hub">
                    <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </button>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-emerald-700 text-white font-mono font-bold flex items-center justify-center text-xs sm:text-base shrink-0 shadow-inner" id="g360-room-badge">
                    --
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <h3 class="font-bold text-xs sm:text-base text-white truncate max-w-[120px] xs:max-w-[180px] sm:max-w-none" id="g360-guest-name">Loading Guest Profile...</h3>
                        <span id="g360-vip-badge" class="px-1.5 py-0.5 sm:px-2 sm:py-0.2 rounded-full text-[8px] sm:text-[9px] font-bold uppercase tracking-wider bg-amber-400 text-brand-deep shrink-0">STANDARD</span>
                        <span class="hidden sm:inline-block px-2 py-0.2 rounded-full text-[9px] font-bold uppercase bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 shrink-0">In-House</span>
                    </div>
                    <div class="text-[10px] sm:text-[11px] leading-tight text-white/70 mt-0.5 flex items-center gap-1.5 sm:gap-2.5 truncate">
                        <span id="g360-cottage-type" class="truncate">Cottage</span>
                        <span class="opacity-60">•</span>
                        <span id="g360-dates-summary" class="truncate">Dates</span>
                        <span class="hidden xs:inline opacity-60">•</span>
                        <span id="g360-code" class="hidden xs:inline font-mono text-white/50">KR-...</span>
                    </div>
                </div>
            </div>

            <!-- Top Right Financial Due & Contacts -->
            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                <!-- Financial Net Due Chip -->
                <div id="g360-net-due-pill" class="px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-white/10 border border-white/20 text-right shrink-0">
                    <div class="text-[8px] sm:text-[9px] uppercase font-bold text-white/60 tracking-wider leading-none">Folio Due</div>
                    <div class="text-xs sm:text-sm font-extrabold text-white leading-tight mt-0.5" id="g360-net-due-amount">₹0.00</div>
                </div>

                <!-- Call & WhatsApp Quick Buttons -->
                <a id="g360-call-link" href="#" class="p-1.5 sm:p-2 rounded-lg sm:rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white transition shadow-xs shrink-0" title="Call Guest Phone">
                    <i data-lucide="phone" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                </a>
                <a id="g360-whatsapp-link" href="#" target="_blank" class="hidden sm:inline-flex p-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white transition shadow-xs shrink-0" title="Chat on WhatsApp">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                </a>
                <button type="button" onclick="closeGuest360Modal()" class="hidden md:inline-flex p-2 text-white/60 hover:text-white rounded-xl hover:bg-white/10 transition shrink-0">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- TAB NAVIGATION BAR (Horizontally Scrollable) -->
        <div class="bg-gray-50 border-b border-gray-200 px-2 sm:px-6 py-1.5 sm:py-2 flex items-center gap-1 sm:gap-2 overflow-x-auto no-scrollbar shrink-0">
            <button type="button" onclick="switchGuest360Tab('folio')" id="g360-tab-btn-folio" class="g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white shadow-xs transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                <span>Overview & Folio</span>
            </button>
            <button type="button" onclick="switchGuest360Tab('dining')" id="g360-tab-btn-dining" class="g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="chef-hat" class="w-3.5 h-3.5 text-orange-600"></i>
                <span>Dining Orders</span>
                <span id="g360-badge-dining" class="hidden px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-orange-500 text-white">0</span>
            </button>
            <button type="button" onclick="switchGuest360Tab('spices')" id="g360-tab-btn-spices" class="g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="leaf" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Spice Boutique</span>
            </button>
            <button type="button" onclick="switchGuest360Tab('extensions')" id="g360-tab-btn-extensions" class="g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="calendar-plus" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Stay Extensions</span>
                <span id="g360-badge-ext" class="hidden px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-500 text-white">0</span>
            </button>
            <button type="button" onclick="switchGuest360Tab('facilities')" id="g360-tab-btn-facilities" class="g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-teal-600"></i>
                <span>Facilities</span>
                <span id="g360-badge-facilities" class="hidden px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-teal-600 text-white">0</span>
            </button>
            <button type="button" onclick="switchGuest360Tab('taxi')" id="g360-tab-btn-taxi" class="g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="car" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Taxi & Travel</span>
                <span id="g360-badge-taxi" class="hidden px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-emerald-600 text-white">0</span>
            </button>
            <button type="button" onclick="switchGuest360Tab('chat')" id="g360-tab-btn-chat" class="g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="message-square" class="w-3.5 h-3.5 text-purple-600"></i>
                <span>Chat & Help Desk</span>
                <span id="g360-badge-chat" class="hidden px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-purple-500 text-white">0</span>
            </button>
            <button type="button" onclick="switchGuest360Tab('profile')" id="g360-tab-btn-profile" class="g360-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="user-check" class="w-3.5 h-3.5 text-gray-600"></i>
                <span>Profile & Notes</span>
            </button>
        </div>

        <!-- MODAL BODY / SCROLLABLE CONTENT -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-brand-canvas space-y-5" id="g360-content-body">
            
            <!-- ================= TAB 1: OVERVIEW & FOLIO LEDGER ================= -->
            <div id="g360-tab-folio" class="g360-tab-content space-y-4">
                <!-- 4 Financial Stat Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                        <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Stay Tariff</span>
                        <div class="text-base sm:text-lg font-bold text-brand-text mt-0.5" id="g360-stat-room">₹0.00</div>
                        <span class="text-[10px] text-brand-muted" id="g360-stat-nights">0 Nights booked</span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                        <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Incidental Folio</span>
                        <div class="text-base sm:text-lg font-bold text-brand-text mt-0.5" id="g360-stat-folio-charges">₹0.00</div>
                        <span class="text-[10px] text-brand-muted" id="g360-stat-charges-count">0 items posted</span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-xs">
                        <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Total Paid</span>
                        <div class="text-base sm:text-lg font-bold text-emerald-700 mt-0.5" id="g360-stat-paid">₹0.00</div>
                        <span class="text-[10px] text-emerald-600" id="g360-stat-payment-status">Settled</span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-amber-200 bg-amber-50/40 shadow-xs" id="g360-stat-due-card">
                        <span class="text-[10px] uppercase font-bold text-amber-900 tracking-wider block">Net Balance Due</span>
                        <div class="text-base sm:text-lg font-extrabold text-amber-700 mt-0.5" id="g360-stat-net-due">₹0.00</div>
                        <span class="text-[10px] text-amber-800" id="g360-stat-settlement-label">Settlement required</span>
                    </div>
                </div>

                <!-- Folio Actions Bar -->
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-brand-muted">Itemized Folio Ledger</h4>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openAddFolioChargeModal()" class="px-3 py-1.5 bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Folio Charge
                        </button>
                        <button type="button" onclick="openRecordFolioPaymentModal()" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                            <i data-lucide="wallet" class="w-3.5 h-3.5"></i> Record Payment
                        </button>
                    </div>
                </div>

                <!-- Itemized Folio Table -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 border-b border-gray-200 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                                <tr>
                                    <th class="px-4 py-2.5">Category</th>
                                    <th class="px-4 py-2.5">Charge Description</th>
                                    <th class="px-4 py-2.5">Date / Time</th>
                                    <th class="px-4 py-2.5 text-right">Amount</th>
                                    <th class="px-4 py-2.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="g360-itemized-folio-tbody">
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-brand-muted">Loading folio ledger...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payments History Ledger -->
                <div class="space-y-2 pt-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-brand-muted">Payments & Settlement History</h4>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 border-b border-gray-200 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                                    <tr>
                                        <th class="px-4 py-2.5">Transaction Ref</th>
                                        <th class="px-4 py-2.5">Payment Method</th>
                                        <th class="px-4 py-2.5">Timestamp</th>
                                        <th class="px-4 py-2.5 text-right">Amount</th>
                                        <th class="px-4 py-2.5 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100" id="g360-payments-tbody">
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-brand-muted">No payments recorded yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 2: IN-VILLA DINING ORDERS ================= -->
            <div id="g360-tab-dining" class="g360-tab-content hidden space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <div>
                        <h4 class="text-xs font-bold text-brand-text">Room Service & Villa Dining Orders</h4>
                        <p class="text-[10px] text-brand-muted">Live kitchen delivery tracking, order items, and guest reviews</p>
                    </div>
                    <span id="g360-dining-orders-count" class="px-2.5 py-1 bg-orange-100 text-orange-900 font-bold text-xs rounded-lg">0 Orders</span>
                </div>
                <div id="g360-dining-orders-list" class="space-y-3">
                    <p class="text-xs text-brand-muted py-6 text-center">No dining orders found for this cottage.</p>
                </div>
            </div>

            <!-- ================= TAB 3: SPICE BOUTIQUE ORDERS ================= -->
            <div id="g360-tab-spices" class="g360-tab-content hidden space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <div>
                        <h4 class="text-xs font-bold text-brand-text">Krishna Spices Orders</h4>
                        <p class="text-[10px] text-brand-muted">Purchases of packets and custom loose kg weights</p>
                    </div>
                    <span id="g360-spices-orders-count" class="px-2.5 py-1 bg-emerald-100 text-emerald-900 font-bold text-xs rounded-lg">0 Orders</span>
                </div>
                <div id="g360-spices-orders-list" class="space-y-3">
                    <p class="text-xs text-brand-muted py-6 text-center">No spice boutique orders found for this guest.</p>
                </div>
            </div>

            <!-- ================= TAB 4: STAY EXTENSIONS ================= -->
            <div id="g360-tab-extensions" class="g360-tab-content hidden space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <div>
                        <h4 class="text-xs font-bold text-brand-text">Stay Extension History & Offers</h4>
                        <p class="text-[10px] text-brand-muted">In-house extension requests, manager loyalty rates, and room re-allocations</p>
                    </div>
                </div>
                <div id="g360-extensions-list" class="space-y-3">
                    <p class="text-xs text-brand-muted py-6 text-center">No stay extensions requested.</p>
                </div>
            </div>

            <!-- ================= TAB 5: LIVE CHAT & HELP DESK ================= -->
            <div id="g360-tab-chat" class="g360-tab-content hidden space-y-4">
                <!-- Dual Layout: Chat Thread + Service Ticket Dispatch -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Column 1 & 2: Chat Conversation Thread -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-xs flex flex-col h-[480px]">
                        <!-- Chat Header -->
                        <div class="p-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/80 rounded-t-2xl">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-bold text-brand-text">Live Concierge Conversation</span>
                            </div>
                            <span class="text-[10px] text-brand-muted" id="g360-chat-ticket-ref">Ticket: --</span>
                        </div>

                        <!-- Messages Stream -->
                        <div class="flex-1 overflow-y-auto p-3.5 space-y-3" id="g360-chat-messages-container">
                            <div class="text-center text-xs text-brand-muted py-8">Loading messages...</div>
                        </div>

                        <!-- In-Console Reply Box -->
                        <div class="p-3 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold uppercase text-brand-muted tracking-wider flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" id="g360-internal-note-toggle" class="rounded text-amber-600 focus:ring-amber-500">
                                    <span>Staff Internal Note (Hidden from guest)</span>
                                </label>
                                <span class="text-[10px] text-brand-muted">Direct SMS alert to guest phone</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="text" 
                                    id="g360-chat-reply-input" 
                                    placeholder="Type message to guest or internal note..." 
                                    class="flex-1 px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs text-brand-text focus:ring-1 focus:ring-brand-primary"
                                    onkeydown="if(event.key==='Enter') submitGuest360Message()"
                                >
                                <button type="button" onclick="submitGuest360Message()" id="g360-chat-send-btn" class="px-4 py-2 bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1">
                                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                    <span>Send</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Service Tickets & Amenity Requests -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-4 flex flex-col justify-between space-y-3">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                <h5 class="text-xs font-bold text-brand-text flex items-center gap-1.5">
                                    <i data-lucide="bell-ring" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>Amenity & Service Tickets</span>
                                </h5>
                                <button type="button" onclick="toggleNewServiceTicketForm()" class="text-[11px] font-bold text-brand-primary hover:underline">
                                    + Dispatch
                                </button>
                            </div>

                            <!-- New Service Ticket Form (Expandable) -->
                            <div id="g360-new-ticket-form" class="hidden p-3 bg-brand-canvas rounded-xl border border-gray-200 space-y-2 text-xs">
                                <div>
                                    <label class="font-bold text-[10px] text-brand-muted block">Service Category</label>
                                    <select id="g360-ticket-topic" class="w-full mt-0.5 p-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                                        <option value="housekeeping">🧹 Housekeeping</option>
                                        <option value="amenities">🧴 Toiletries / Towels</option>
                                        <option value="maintenance">🔧 Maintenance / AC</option>
                                        <option value="concierge">🛎️ Concierge / Excursion</option>
                                        <option value="billing">💳 Folio / Billing</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="font-bold text-[10px] text-brand-muted block">Request Subject</label>
                                    <input type="text" id="g360-ticket-subject" placeholder="e.g. Extra herbal bathrobes needed" class="w-full mt-0.5 p-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                                </div>
                                <div>
                                    <label class="font-bold text-[10px] text-brand-muted block">Priority</label>
                                    <select id="g360-ticket-priority" class="w-full mt-0.5 p-1.5 bg-white border border-gray-200 rounded-lg text-xs">
                                        <option value="normal">Normal</option>
                                        <option value="high">High Priority</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                                <button type="button" onclick="submitServiceTicket()" class="w-full py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg text-xs shadow-xs transition">
                                    Dispatch Request Now
                                </button>
                            </div>

                            <!-- Tickets List -->
                            <div class="space-y-2 overflow-y-auto max-h-[300px]" id="g360-service-tickets-list">
                                <p class="text-xs text-brand-muted py-4 text-center">No active service tickets.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB: EXPERIENCES & FACILITIES ================= -->
            <div id="g360-tab-facilities" class="g360-tab-content hidden space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                        <div>
                            <h4 class="text-xs font-bold text-brand-text uppercase tracking-wider">Booked Resort Experiences &amp; Facilities</h4>
                            <p class="text-[11px] text-brand-muted mt-0.5">Manager-controlled time-period scheduling &amp; folio charge synchronization</p>
                        </div>
                    </div>

                    <div class="space-y-3" id="g360-facilities-list">
                        <p class="text-xs text-brand-muted py-6 text-center">Loading experiences...</p>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 5B: TAXI & EXCURSION REQUESTS ================= -->
            <div id="g360-tab-taxi" class="g360-tab-content hidden space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h4 class="text-xs font-bold text-brand-text uppercase tracking-wider">In-House Cab & Excursion Bookings</h4>
                            <p class="text-[11px] text-brand-muted mt-0.5">Guest selected destinations, custom extra stops, agreed fare quotes, and Room Folio charges.</p>
                        </div>
                    </div>

                    <div class="space-y-3" id="g360-taxi-list">
                        <p class="text-xs text-brand-muted py-6 text-center">Loading taxi requests...</p>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 6: GUEST PROFILE & NOTES ================= -->
            <div id="g360-tab-profile" class="g360-tab-content hidden space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-xs space-y-4">
                    <h4 class="text-xs font-bold text-brand-text uppercase tracking-wider border-b border-gray-100 pb-2">Guest Identity & Verification</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Full Name</span>
                            <span class="font-bold text-brand-text" id="g360-prof-name">--</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Phone Number</span>
                            <span class="font-bold text-brand-text" id="g360-prof-phone">--</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Email</span>
                            <span class="font-bold text-brand-text" id="g360-prof-email">--</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Government ID Proof</span>
                            <span class="font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 inline-block mt-0.5" id="g360-prof-id">--</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">VIP Level</span>
                            <span class="font-bold text-amber-800 uppercase" id="g360-prof-vip">Standard</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Lifetime Stays / Spend</span>
                            <span class="font-bold text-brand-text" id="g360-prof-lifetime">0 Stays / ₹0</span>
                        </div>
                        <div class="sm:col-span-2 md:col-span-3">
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Registered Address</span>
                            <span class="text-brand-text" id="g360-prof-address">--</span>
                        </div>
                    </div>

                    <!-- Preferences & Special Requests -->
                    <div class="pt-3 border-t border-gray-100 space-y-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Guest Preferences / Dietary Restrictions</span>
                            <div class="mt-1 p-2.5 bg-brand-canvas rounded-xl text-xs text-brand-text border border-gray-200/70" id="g360-prof-preferences">None specified</div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider block">Special Booking Requests</span>
                            <div class="mt-1 p-2.5 bg-brand-canvas rounded-xl text-xs text-brand-text border border-gray-200/70" id="g360-prof-requests">None</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- FIXED BOTTOM ACTION TOOLBAR (Mobile & Desktop) -->
        <div class="bg-white border-t border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between gap-3 shrink-0 shadow-lg">
            <div class="flex items-center gap-2">
                <button type="button" onclick="openAddFolioChargeModal()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Charge</span>
                </button>
                <button type="button" onclick="openRecordFolioPaymentModal()" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                    <span>Settle Folio</span>
                </button>
            </div>

            <!-- Express Check-Out Button -->
            <button type="button" onclick="executeGuest360ExpressCheckOut()" id="g360-btn-checkout" class="px-4 py-2 bg-brand-deep hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                <i data-lucide="log-out" class="w-3.5 h-3.5 text-amber-400"></i>
                <span>Express Check-Out</span>
            </button>
        </div>

    </div>
</div>

<!-- SUB-MODAL 1: ADD FOLIO CHARGE MODAL -->
<div id="modal-g360-add-charge" class="hidden fixed inset-0 z-[80] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="z-index: 80;">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4 border border-gray-100 relative z-10">
        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <h4 class="font-bold text-sm text-brand-text flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-700"></i>
                <span>Post Room Folio Charge</span>
            </h4>
            <button type="button" onclick="closeModal('modal-g360-add-charge')" class="p-1 text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form onsubmit="submitAddFolioCharge(event)" class="space-y-3 text-xs">
            <div>
                <label class="font-bold text-brand-muted block">Charge Category</label>
                <select id="g360-charge-cat" class="w-full mt-1 p-2 bg-brand-canvas border border-gray-200 rounded-xl text-xs" required>
                    <option value="minibar">🍷 Minibar & Snacks</option>
                    <option value="laundry">🧺 Laundry & Dry Cleaning</option>
                    <option value="extra_bed">🛏️ Extra Bed / Rollaway</option>
                    <option value="transport">🚕 Airport Cab / Private Transfer</option>
                    <option value="spa_wellness">💆 Spa & Ayurvedic Wellness</option>
                    <option value="activity">🧗 Plantation Trek / Activity</option>
                    <option value="dining">🍽️ Special Dining Folio</option>
                    <option value="miscellaneous">📋 Miscellaneous Incidentals</option>
                </select>
            </div>
            <div>
                <label class="font-bold text-brand-muted block">Item / Charge Description</label>
                <input type="text" id="g360-charge-title" placeholder="e.g. 2x Laundry shirts & Minibar Cashews" class="w-full mt-1 p-2 bg-brand-canvas border border-gray-200 rounded-xl text-xs" required>
            </div>
            <div>
                <label class="font-bold text-brand-muted block">Amount (₹)</label>
                <input type="number" step="0.01" min="1" id="g360-charge-amount" placeholder="0.00" class="w-full mt-1 p-2 bg-brand-canvas border border-gray-200 rounded-xl text-xs font-mono font-bold" required>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-g360-add-charge')" class="px-3 py-1.5 rounded-xl border border-gray-200 text-xs font-bold text-brand-muted">Cancel</button>
                <button type="submit" id="btn-submit-folio-charge" class="px-4 py-1.5 rounded-xl bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold shadow-xs transition">Post to Folio</button>
            </div>
        </form>
    </div>
</div>

<!-- SUB-MODAL 2: RECORD FOLIO PAYMENT MODAL -->
<div id="modal-g360-record-payment" class="hidden fixed inset-0 z-[80] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="z-index: 80;">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4 border border-gray-100 relative z-10">
        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <h4 class="font-bold text-sm text-brand-text flex items-center gap-2">
                <i data-lucide="wallet" class="w-4 h-4 text-emerald-700"></i>
                <span>Record In-House Payment</span>
            </h4>
            <button type="button" onclick="closeModal('modal-g360-record-payment')" class="p-1 text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form onsubmit="submitRecordFolioPayment(event)" class="space-y-3 text-xs">
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs space-y-1">
                <span class="text-[10px] font-bold uppercase text-amber-800 tracking-wider">Outstanding Balance</span>
                <div class="text-base font-extrabold text-amber-900" id="g360-modal-pay-balance">₹0.00 Due</div>
            </div>

            <div>
                <label class="font-bold text-brand-muted block">Payment Method</label>
                <select id="g360-pay-method" class="w-full mt-1 p-2 bg-brand-canvas border border-gray-200 rounded-xl text-xs" required>
                    <option value="cash">💵 On-Hand Cash</option>
                    <option value="pos_card">💳 Front-Desk POS Card Terminal</option>
                    <option value="counter_upi">📱 Front-Desk UPI QR</option>
                    <option value="bank_transfer">🏦 Direct Bank NEFT / IMPS</option>
                </select>
            </div>

            <div>
                <label class="font-bold text-brand-muted block">Amount Paid (₹)</label>
                <input type="number" step="0.01" min="1" id="g360-pay-amount" placeholder="0.00" class="w-full mt-1 p-2 bg-brand-canvas border border-gray-200 rounded-xl text-xs font-mono font-bold" required>
            </div>

            <div>
                <label class="font-bold text-brand-muted block">Reference / Receipt Notes (Optional)</label>
                <input type="text" id="g360-pay-notes" placeholder="e.g. Paid at Villa desk by Guest" class="w-full mt-1 p-2 bg-brand-canvas border border-gray-200 rounded-xl text-xs">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-g360-record-payment')" class="px-3 py-1.5 rounded-xl border border-gray-200 text-xs font-bold text-brand-muted">Cancel</button>
                <button type="submit" id="btn-submit-folio-payment" class="px-4 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs transition">Confirm Payment</button>
            </div>
        </form>
    </div>
</div>
