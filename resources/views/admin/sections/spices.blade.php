<!-- KRISHNA SPICES E-COMMERCE & FULFILLMENT HUB (ADM-16, ADM-17, ADM-18) -->
<section id="spices" class="section space-y-5">
    <!-- Header with 3 Hub Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/70 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-xl sm:text-2xl font-bold text-brand-text">Krishna Spices E-Commerce Hub</h2>
            </div>
            <p class="text-brand-muted text-xs mt-1">Single-estate harvest spices &bull; Pack-on-order fulfillment &bull; Courier &amp; Villa delivery &bull; Customizable Return &amp; Refund Policy.</p>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
            <!-- Global Return Available Switch -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-200 shrink-0" title="Global Return Policy Switch">
                <span class="text-xs font-bold text-brand-text flex items-center gap-1.5">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-brand-primary"></i>
                    <span>Return Available:</span>
                </span>
                <button type="button" 
                        id="global-returns-toggle-btn"
                        onclick="toggleGlobalSpiceReturns()"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ ($globalSpiceReturnsEnabled ?? true) ? 'bg-emerald-600' : 'bg-gray-300' }}"
                        role="switch" 
                        aria-checked="{{ ($globalSpiceReturnsEnabled ?? true) ? 'true' : 'false' }}">
                    <span id="global-returns-toggle-thumb" 
                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ ($globalSpiceReturnsEnabled ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
                <span id="global-returns-toggle-label" class="text-[11px] font-bold {{ ($globalSpiceReturnsEnabled ?? true) ? 'text-emerald-700' : 'text-gray-500' }}">
                    {{ ($globalSpiceReturnsEnabled ?? true) ? 'ON' : 'OFF' }}
                </span>
            </div>

            <div class="flex items-center gap-1.5 bg-gray-100 p-1.5 rounded-xl text-xs font-semibold shrink-0">
                <button onclick="switchSpiceTab('orders')" id="spice-tab-btn-orders" class="px-3.5 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5 shrink-0">
                    <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                    <span>Orders &amp; Dispatch ({{ $spiceOrders->count() }})</span>
                </button>
                <button onclick="switchSpiceTab('products')" id="spice-tab-btn-products" class="px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition shrink-0">
                    Products Catalog ({{ $spiceProducts->count() }})
                </button>
                <button onclick="switchSpiceTab('policy')" id="spice-tab-btn-policy" class="px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition flex items-center gap-1.5 shrink-0">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Return Policy &amp; Refunds</span>
                    @if(($pendingSpiceReturnsCount ?? 0) > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-rose-500 text-white font-bold">{{ $pendingSpiceReturnsCount }} Req</span>
                    @endif
                </button>
            </div>
            <button onclick="openModal('modal-spice-product')" class="px-3.5 py-2 rounded-xl bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition touch-tap shrink-0">
                <i data-lucide="plus" class="w-4 h-4 text-brand-accent"></i> Add Product
            </button>
        </div>
    </div>

    <!-- ========================================================
         TAB 1: ORDERS & DISPATCH HUB (ADM-18)
         ======================================================== -->
    <div id="spice-tab-orders" class="space-y-4">
        <!-- Status Filters Bar -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 text-xs">
            <button onclick="filterSpiceOrders('all', this)" class="spice-filter-btn px-3 py-1.5 rounded-xl font-bold bg-brand-deep text-white shadow-xs transition">
                All ({{ $spiceOrders->count() }})
            </button>
            <button onclick="filterSpiceOrders('paid', this)" class="spice-filter-btn px-3 py-1.5 rounded-xl font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                Paid / To Pack ({{ $spiceOrders->where('status', 'paid')->count() }})
            </button>
            <button onclick="filterSpiceOrders('packed', this)" class="spice-filter-btn px-3 py-1.5 rounded-xl font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                Packed ({{ $spiceOrders->where('status', 'packed')->count() }})
            </button>
            <button onclick="filterSpiceOrders('shipped', this)" class="spice-filter-btn px-3 py-1.5 rounded-xl font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                In Transit / Shipped ({{ $spiceOrders->where('status', 'shipped')->count() }})
            </button>
            <button onclick="filterSpiceOrders('delivered', this)" class="spice-filter-btn px-3 py-1.5 rounded-xl font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                Delivered ({{ $spiceOrders->where('status', 'delivered')->count() }})
            </button>
        </div>

        <!-- Orders Desktop Table & Mobile Card View -->
        <div class="bg-brand-surface rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
            <!-- Desktop / Tablet Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/90 border-b border-gray-200/70 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Order Number</th>
                            <th class="px-4 py-3">Customer &amp; Mode</th>
                            <th class="px-4 py-3">Items (Packets / Loose KG)</th>
                            <th class="px-4 py-3">Fulfillment Status</th>
                            <th class="px-4 py-3">Return / Refund</th>
                            <th class="px-4 py-3">Tracking Details</th>
                            <th class="px-4 py-3 text-right">Bill &amp; Tax</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="spice-orders-tbody">
                        @forelse($spiceOrders as $order)
                        <tr class="hover:bg-brand-canvas/60 transition spice-order-row" data-status="{{ $order->status }}" id="spice-order-row-{{ $order->id }}">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-brand-text">{{ $order->order_number }}</div>
                                <div class="text-[10px] text-brand-muted">{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-brand-text">{{ $order->customer_name }}</div>
                                <div class="text-[10px] text-brand-muted">{{ $order->customer_phone }}</div>
                                @if($order->delivery_mode === 'villa' || $order->room_number)
                                    <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        <i data-lucide="door-open" class="w-3 h-3"></i> Villa Delivery: {{ $order->room_number ?? 'In-House' }}
                                    </span>
                                @else
                                    <div class="text-[10px] text-gray-500 truncate max-w-[170px]">{{ $order->shipping_city }}, {{ $order->shipping_state }} ({{ $order->shipping_pincode }})</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 space-y-1">
                                @foreach($order->items as $item)
                                    <div class="text-xs text-brand-text flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 shrink-0"></span>
                                        <span><strong>{{ $item->product_name }}</strong>: {{ $item->weight_display ?: ($item->quantity . 'x') }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono">(₹{{ number_format($item->total_price) }})</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-4 py-3.5">
                                @if($order->status === 'paid' || $order->status === 'processing')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Paid / Fresh Pack</span>
                                @elseif($order->status === 'packed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Packed &amp; Sealed</span>
                                @elseif($order->status === 'shipped')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">In Transit</span>
                                @elseif($order->status === 'delivered')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">Delivered</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">Cancelled</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($order->refund_status === 'requested')
                                    <button type="button" onclick="openSpiceReturnModal({{ $order->id }}, '{{ $order->order_number }}', {{ $order->total_amount }})" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300 hover:bg-rose-200 transition flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600"></i> Return Req (₹{{ number_format($order->refund_amount, 2) }})
                                    </button>
                                @elseif($order->refund_status === 'approved' || $order->refund_status === 'refunded')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Refunded ₹{{ number_format($order->refund_amount, 2) }}
                                    </span>
                                @elseif($order->refund_status === 'rejected')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">
                                        Return Rejected
                                    </span>
                                @else
                                    <span class="text-[10px] text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($order->tracking_number)
                                    <div class="font-bold text-brand-text flex items-center gap-1">
                                        <i data-lucide="truck" class="w-3 h-3 text-brand-primary"></i>
                                        <span>{{ $order->shipping_courier ?: 'Courier Partner' }}</span>
                                    </div>
                                    <div class="text-[11px] text-brand-primary font-mono">{{ $order->tracking_number }}</div>
                                @else
                                    <button type="button" onclick="openDispatchModal({{ $order->id }}, '{{ $order->order_number }}')" class="text-[11px] font-semibold text-brand-primary hover:underline flex items-center gap-1">
                                        <i data-lucide="plus" class="w-3 h-3"></i> Add Tracking
                                    </button>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="font-extrabold text-brand-text text-sm">₹{{ number_format($order->total_amount, 2) }}</div>
                                <div class="text-[10px] text-gray-500">Subtotal ₹{{ number_format($order->subtotal, 2) }}</div>
                                <div class="text-[10px] text-emerald-700">Incl. ₹{{ number_format($order->tax_amount, 2) }} GST</div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <select onchange="handleSpiceOrderStatusChange({{ $order->id }}, '{{ $order->order_number }}', this.value)" class="text-[11px] font-semibold bg-white border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-brand-primary focus:outline-hidden">
                                        <option value="paid" {{ in_array($order->status, ['paid', 'processing']) ? 'selected' : '' }}>Paid / Pack</option>
                                        <option value="packed" {{ $order->status === 'packed' ? 'selected' : '' }}>Packed</option>
                                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-brand-muted">No spice commerce orders recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================
         TAB 2: PRODUCTS CATALOG (ADM-16) — PACK-ON-ORDER MODEL
         ======================================================== -->
    <div id="spice-tab-products" class="space-y-4 hidden">
        <!-- Notice banner explaining Pack-on-Order model -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-emerald-950">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <i data-lucide="leaf" class="w-4 h-4"></i>
                </span>
                <div>
                    <h4 class="font-bold text-emerald-900">Fresh Harvest Pack-on-Order Model (Zero Stock Lock)</h4>
                    <p class="text-emerald-800/80 text-[11px] mt-0.5">Spices are ground and vacuum-packed on-demand after order receipt. Products are controlled via 1-click Store Active/Inactive toggle.</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="font-mono font-bold text-emerald-900 bg-white/80 px-2.5 py-1 rounded-lg border border-emerald-200">
                    {{ $spiceProducts->where('is_active', true)->count() }} Active &bull; {{ $spiceProducts->where('is_active', false)->count() }} Paused
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($spiceProducts as $sp)
            <div class="bg-brand-surface rounded-2xl border {{ $sp->is_active ? 'border-gray-200/80' : 'border-dashed border-gray-300 opacity-80' }} shadow-xs hover:shadow-md transition duration-200 flex flex-col justify-between overflow-hidden group">
                <div>
                    <!-- Photo Header & Badges -->
                    <div class="h-44 w-full bg-mint relative overflow-hidden">
                        <img src="{{ $sp->image_url ?: 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=600&q=80' }}" 
                             alt="{{ $sp->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500 {{ $sp->is_active ? '' : 'grayscale' }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                        <!-- Top Left: Featured Star -->
                        @if($sp->is_featured)
                        <div class="absolute top-2.5 left-2.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-brand-deep flex items-center gap-1 shadow-xs">
                                <i data-lucide="sparkles" class="w-3 h-3"></i> Featured
                            </span>
                        </div>
                        @endif

                        <!-- Top Right: Active Status Badge -->
                        <div class="absolute top-2.5 right-2.5">
                            <span id="spice-avail-badge-{{ $sp->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $sp->is_active ? 'bg-emerald-500 text-white shadow-xs' : 'bg-gray-800/90 text-gray-200' }}">
                                {{ $sp->is_active ? '✓ Active in Store' : '✕ Paused / Inactive' }}
                            </span>
                        </div>

                        <!-- Bottom Bar: Selling Mode Pill -->
                        <div class="absolute bottom-2 left-2.5">
                            @if($sp->selling_mode === 'both')
                                <span class="px-2 py-0.5 rounded-md bg-purple-900/80 backdrop-blur-xs text-purple-100 text-[10px] font-bold flex items-center gap-1">
                                    <i data-lucide="scale" class="w-3 h-3 text-purple-300"></i> Packets &amp; Loose KG
                                </span>
                            @elseif($sp->selling_mode === 'loose')
                                <span class="px-2 py-0.5 rounded-md bg-amber-900/80 backdrop-blur-xs text-amber-100 text-[10px] font-bold flex items-center gap-1">
                                    <i data-lucide="scale" class="w-3 h-3 text-amber-300"></i> Loose KG Only
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-blue-900/80 backdrop-blur-xs text-blue-100 text-[10px] font-bold flex items-center gap-1">
                                    <i data-lucide="package" class="w-3 h-3 text-blue-300"></i> Packets Only
                                </span>
                            @endif
                        </div>

                        <!-- Bottom Right: Return Policy Status Pill -->
                        <div class="absolute bottom-2 right-2.5">
                            @if(!($globalSpiceReturnsEnabled ?? true))
                                <span class="px-2 py-0.5 rounded-md bg-gray-900/85 backdrop-blur-xs text-amber-300 text-[9px] font-bold flex items-center gap-1 shadow-xs" title="Returns disabled globally">
                                    <i data-lucide="shield-off" class="w-3 h-3 text-amber-400"></i> No Returns (Global Off)
                                </span>
                            @elseif($sp->is_returnable ?? true)
                                <span id="spice-return-badge-{{ $sp->id }}" class="px-2 py-0.5 rounded-md bg-blue-900/85 backdrop-blur-xs text-blue-100 text-[9px] font-bold flex items-center gap-1 shadow-xs">
                                    <i data-lucide="rotate-ccw" class="w-3 h-3 text-blue-300"></i> Return Available
                                </span>
                            @else
                                <span id="spice-return-badge-{{ $sp->id }}" class="px-2 py-0.5 rounded-md bg-rose-900/85 backdrop-blur-xs text-rose-100 text-[9px] font-bold flex items-center gap-1 shadow-xs">
                                    <i data-lucide="shield-alert" class="w-3 h-3 text-rose-300"></i> No Return Policy
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Product Content -->
                    <div class="p-4 sm:p-5 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-primary">
                                    {{ $sp->category ? $sp->category->name : 'Estate Spices' }}
                                </span>
                                <h3 class="font-bold text-base text-brand-text leading-snug">{{ $sp->name }}</h3>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 border border-amber-200 text-amber-900 shrink-0">
                                {{ $sp->tax_rate ?? 5.0 }}% GST
                            </span>
                        </div>

                        <!-- Pricing Breakdown Grid -->
                        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                            @if($sp->isPacketAvailable())
                            <div>
                                <div class="text-[10px] text-gray-500">Packet Price</div>
                                <div class="font-extrabold text-brand-text">₹{{ number_format($sp->price, 2) }}</div>
                                <div class="text-[10px] text-gray-500 font-medium">{{ $sp->package_size ?? ($sp->weight_grams . 'g') }}</div>
                            </div>
                            @else
                            <div class="text-gray-400 italic text-[11px] flex items-center">
                                No packet sales
                            </div>
                            @endif

                            @if($sp->isLooseAvailable())
                            <div class="border-l border-gray-200 pl-2">
                                <div class="text-[10px] text-gray-500">Loose Rate</div>
                                <div class="font-extrabold text-emerald-700">₹{{ number_format($sp->price_per_kg ?: ($sp->price * (1000 / ($sp->weight_grams ?: 100))), 2) }}<span class="text-[10px] font-normal text-gray-500">/kg</span></div>
                                <div class="text-[10px] text-gray-500 font-medium">Min {{ $sp->min_loose_weight_kg ?? 0.1 }} kg</div>
                            </div>
                            @else
                            <div class="border-l border-gray-200 pl-2 text-gray-400 italic text-[11px] flex items-center">
                                No loose kg sales
                            </div>
                            @endif
                        </div>

                        <p class="text-xs text-brand-muted leading-relaxed line-clamp-2">
                            {{ $sp->short_description ?: $sp->description ?: 'Single-estate harvest directly shade dried under Munnar forest canopy.' }}
                        </p>

                        <!-- Non-Returnable Warning Notice (If returns off) -->
                        <div id="spice-return-notice-{{ $sp->id }}" class="{{ (!($globalSpiceReturnsEnabled ?? true) || !($sp->is_returnable ?? true)) ? 'flex' : 'hidden' }} p-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] font-semibold items-center gap-1.5">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-amber-700 shrink-0"></i>
                            <span>This product does not have return policies</span>
                        </div>
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="p-3 bg-gray-50/90 border-t border-gray-100 flex items-center justify-between gap-2 flex-wrap">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <!-- 1-Click Active Toggle Button -->
                        <button type="button" 
                                onclick="toggleSpiceAvailability({{ $sp->id }}, this)" 
                                id="spice-avail-btn-{{ $sp->id }}"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $sp->is_active ? 'bg-amber-100 text-amber-900 hover:bg-amber-200' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                            <i data-lucide="{{ $sp->is_active ? 'pause-circle' : 'play-circle' }}" class="w-3.5 h-3.5"></i>
                            <span>{{ $sp->is_active ? 'Pause' : 'Activate' }}</span>
                        </button>

                        <!-- Per-Product Return Toggle ("pickout selected spices if needed") -->
                        <button type="button" 
                                onclick="toggleProductReturn({{ $sp->id }}, this)" 
                                id="spice-return-btn-{{ $sp->id }}"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 {{ ($sp->is_returnable ?? true) ? 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200' }}"
                                title="Toggle return policy for {{ $sp->name }}">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>{{ ($sp->is_returnable ?? true) ? 'Return: On' : 'Return: Off' }}</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <!-- Edit Button -->
                        <button type="button" 
                                onclick="openSpiceEditModal({{ json_encode($sp) }})" 
                                class="px-2.5 py-1.5 rounded-lg border border-gray-300 hover:bg-white text-gray-700 font-semibold text-xs transition flex items-center gap-1">
                            <i data-lucide="pencil" class="w-3 h-3 text-brand-primary"></i> Edit
                        </button>
                        <!-- Delete Button -->
                        <button type="button" 
                                onclick="deleteSpiceProduct({{ $sp->id }}, '{{ addslashes($sp->name) }}')" 
                                class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs transition" title="Delete product">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-brand-muted bg-white rounded-2xl border border-gray-200">
                <i data-lucide="leaf" class="w-10 h-10 mx-auto mb-2 text-gray-300"></i>
                <h4 class="font-bold text-sm text-brand-text">No spice products found</h4>
                <p class="text-xs text-gray-500 mt-1">Click "Add Product" to create your first estate listing.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================================
         TAB 3: RETURN POLICY & REFUNDS MANAGEMENT (ADM-17)
         ======================================================== -->
    <div id="spice-tab-policy" class="space-y-6 hidden">
        <!-- 0. MASTER GLOBAL RETURN FACILITY TOGGLE CARD -->
        <div id="tab3-global-card" class="p-5 sm:p-6 rounded-2xl border transition-all duration-300 shadow-xs {{ ($globalSpiceReturnsEnabled ?? true) ? 'bg-emerald-50/70 border-emerald-200' : 'bg-amber-50/90 border-amber-300' }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div id="tab3-global-icon-box" class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 {{ ($globalSpiceReturnsEnabled ?? true) ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white' }}">
                        <i data-lucide="{{ ($globalSpiceReturnsEnabled ?? true) ? 'rotate-ccw' : 'shield-alert' }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-base text-brand-text">Global Return &amp; Refund Facility</h3>
                            <span id="tab3-global-status-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ ($globalSpiceReturnsEnabled ?? true) ? 'bg-emerald-200 text-emerald-900 border border-emerald-300' : 'bg-amber-200 text-amber-900 border border-amber-400' }}">
                                {{ ($globalSpiceReturnsEnabled ?? true) ? '✓ Globally ACTIVE' : '✕ Globally TURNED OFF' }}
                            </span>
                        </div>
                        <p class="text-xs text-brand-muted mt-1 leading-relaxed max-w-2xl">
                            Master switch for the entire spices store. When <strong>TURNED OFF</strong>, no return policies, guarantee cards, or refund options are shown anywhere on the client store, checkout, or guest dashboard. The website will solely state: <span class="font-bold text-brand-text">"This product does not have return policies."</span>
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-3">
                    <button type="button" 
                            onclick="toggleGlobalSpiceReturns()" 
                            id="tab3-global-returns-toggle-btn"
                            class="px-4 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 {{ ($globalSpiceReturnsEnabled ?? true) ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                        <i data-lucide="{{ ($globalSpiceReturnsEnabled ?? true) ? 'power-off' : 'power' }}" class="w-4 h-4"></i>
                        <span id="tab3-global-returns-btn-text">{{ ($globalSpiceReturnsEnabled ?? true) ? 'Turn OFF Globally' : 'Enable Globally' }}</span>
                    </button>
                </div>
            </div>

            <div id="tab3-global-warning" class="{{ (!($globalSpiceReturnsEnabled ?? true)) ? 'flex' : 'hidden' }} mt-4 p-3 rounded-xl bg-amber-100/90 border border-amber-300/80 text-amber-950 text-xs items-center gap-2 font-medium">
                <i data-lucide="info" class="w-4 h-4 text-amber-800 shrink-0"></i>
                <span>Returns are currently turned off globally. The return facility section is completely hidden from client-facing pages and displays: "This product does not have return policies." Individual products can also be flagged returnable or non-returnable once globally enabled.</span>
            </div>
        </div>

        <!-- 1. Policy Rules & Refund Tiers Engine -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-brand-text">Spice Return &amp; Cancellation Policy Rules</h3>
                        <p class="text-[11px] text-brand-muted">Customizable refund tiers (mirrors room cancellation engine) with time windows and handling deductions.</p>
                    </div>
                </div>
                <button type="button" onclick="openSpiceRuleModal()" class="px-3.5 py-2 rounded-xl bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition touch-tap self-start sm:self-auto">
                    <i data-lucide="plus" class="w-4 h-4 text-brand-accent"></i> Add Return Rule Tier
                </button>
            </div>

            <!-- Rules Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200/70 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Rule Name</th>
                            <th class="px-4 py-3">Applies To</th>
                            <th class="px-4 py-3 text-center">Time Limit</th>
                            <th class="px-4 py-3 text-center">Refund %</th>
                            <th class="px-4 py-3 text-center">Handling Fee</th>
                            <th class="px-4 py-3">Description &amp; Terms</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($spiceReturnRules as $rule)
                        <tr class="hover:bg-brand-canvas/60 transition" id="spice-rule-row-{{ $rule->id }}">
                            <td class="px-4 py-3 font-bold text-brand-text">
                                {{ $rule->name }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $rule->applies_to === 'before_dispatch' ? 'bg-blue-100 text-blue-800' : ($rule->applies_to === 'after_delivery' ? 'bg-emerald-100 text-emerald-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ str_replace('_', ' ', $rule->applies_to) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-mono font-semibold">
                                {{ $rule->time_limit_hours }}h ({{ round($rule->time_limit_hours / 24, 1) }}d)
                            </td>
                            <td class="px-4 py-3 text-center font-extrabold text-sm text-emerald-700">
                                {{ number_format($rule->refund_percentage, 0) }}%
                            </td>
                            <td class="px-4 py-3 text-center font-mono">
                                {{ $rule->handling_fee > 0 ? '₹' . number_format($rule->handling_fee, 2) : 'None' }}
                            </td>
                            <td class="px-4 py-3 text-brand-muted text-[11px] max-w-xs leading-snug">
                                {{ $rule->description }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="toggleSpiceRuleActive({{ $rule->id }})" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase cursor-pointer {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    {{ $rule->is_active ? 'Active' : 'Paused' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openSpiceRuleModal({{ json_encode($rule) }})" class="p-1 rounded text-gray-500 hover:text-brand-primary" title="Edit Rule">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button type="button" onclick="deleteSpiceRule({{ $rule->id }})" class="p-1 rounded text-gray-400 hover:text-rose-600" title="Delete Rule">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-brand-muted">No spice return rules configured yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Customer Return & Cancellation Queue -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-brand-text">Return &amp; Refund Request Queue</h3>
                        <p class="text-[11px] text-brand-muted">Review, approve, or reject customer cancellation and return claims.</p>
                    </div>
                </div>
                <span class="font-mono text-xs font-bold text-brand-text bg-gray-100 px-3 py-1 rounded-lg">
                    {{ $spiceReturnOrders->count() }} Total Logged
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200/70 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Order Number</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Reason / Request Details</th>
                            <th class="px-4 py-3 text-right">Order Bill</th>
                            <th class="px-4 py-3 text-right">Eligible Refund</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">1-Click Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($spiceReturnOrders as $retOrder)
                        <tr class="hover:bg-brand-canvas/60 transition">
                            <td class="px-4 py-3 font-bold text-brand-text">
                                {{ $retOrder->order_number }}
                                <div class="text-[10px] text-brand-muted">{{ $retOrder->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-brand-text">{{ $retOrder->customer_name }}</div>
                                <div class="text-[10px] text-brand-muted">{{ $retOrder->customer_phone }}</div>
                            </td>
                            <td class="px-4 py-3 max-w-sm">
                                <div class="text-xs text-brand-text font-medium">{{ $retOrder->cancellation_reason ?: 'No customer note specified.' }}</div>
                                @if($retOrder->returnRule)
                                    <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">Matched Tier: {{ $retOrder->returnRule->name }} ({{ number_format($retOrder->returnRule->refund_percentage, 0) }}%)</div>
                                @endif
                                @if($retOrder->return_notes)
                                    <div class="text-[10px] text-brand-muted italic mt-0.5">Admin Note: {{ $retOrder->return_notes }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold">
                                ₹{{ number_format($retOrder->total_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-extrabold text-emerald-700 text-sm">
                                ₹{{ number_format($retOrder->refund_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($retOrder->refund_status === 'requested')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                        Pending Review
                                    </span>
                                @elseif($retOrder->refund_status === 'approved' || $retOrder->refund_status === 'refunded')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Refund Approved
                                    </span>
                                @elseif($retOrder->refund_status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        Request Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($retOrder->refund_status === 'requested')
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="quickProcessSpiceReturn({{ $retOrder->id }}, 'approve', {{ $retOrder->refund_amount }})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                                            Approve ₹{{ number_format($retOrder->refund_amount) }}
                                        </button>
                                        <button type="button" onclick="quickProcessSpiceReturn({{ $retOrder->id }}, 'reject')" class="px-2 py-1 border border-gray-300 hover:bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold transition">
                                            Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-gray-400 font-mono">Completed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-brand-muted">No return or cancellation requests in queue.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Public Policy Terms Editor (Settings) -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 space-y-4">
            <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base text-brand-text">Public Return &amp; Cancellation Policy Copy</h3>
                    <p class="text-[11px] text-brand-muted">This wording is presented directly to clients in the spice shop and customer dashboard.</p>
                </div>
            </div>

            <form onsubmit="updateSpicePolicyCopy(event)" class="space-y-3">
                @csrf
                <textarea id="spice-policy-text-input" rows="4" class="w-full text-xs p-3.5 rounded-xl border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden leading-relaxed">{{ $spiceReturnPolicyDescription }}</textarea>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] text-brand-muted">Visible on /spices store and /dashboard recent purchases.</span>
                    <button type="submit" id="btn-save-spice-policy" class="px-4 py-2 bg-brand-deep hover:bg-black text-white rounded-xl text-xs font-bold transition shadow-xs">
                        Save Policy Description
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
