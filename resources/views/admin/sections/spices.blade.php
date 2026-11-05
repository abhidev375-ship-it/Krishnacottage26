<!-- COUNTRY SIDE SPICES E-COMMERCE & FULFILLMENT HUB (ADM-16, ADM-17, ADM-18) -->
<section id="spices" class="section space-y-5">
    <!-- Header with 3 Hub Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/70 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-xl sm:text-2xl font-bold text-brand-text">Country Side Spices E-Commerce Hub</h2>
            </div>
            <p class="text-brand-muted text-xs mt-1">Single-estate harvest spices &bull; Pack-on-order fulfillment &bull; Courier &amp; Villa delivery &bull; Live inventory control.</p>
        </div>
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
            <div class="flex items-center gap-1.5 bg-gray-100 p-1.5 rounded-xl text-xs font-semibold shrink-0">
                <button onclick="switchSpiceTab('orders')" id="spice-tab-btn-orders" class="px-3.5 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5 shrink-0">
                    <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                    <span>Orders &amp; Dispatch ({{ $spiceOrders->count() }})</span>
                </button>
                <button onclick="switchSpiceTab('products')" id="spice-tab-btn-products" class="px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition shrink-0">
                    Products Catalog ({{ $spiceProducts->count() }})
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
                    <div class="h-44 w-full bg-mint relative overflow-hidden flex items-center justify-center">
                        @if($sp->image_url)
                        <img src="{{ $sp->image_url }}" 
                             alt="{{ $sp->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500 {{ $sp->is_active ? '' : 'grayscale' }}">
                        @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-400 p-2 text-center">
                            <i data-lucide="leaf" class="w-8 h-8 text-gray-300 mb-1"></i>
                            <span class="text-[11px] font-medium text-gray-500">{{ $sp->name }}</span>
                        </div>
                        @endif
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

                        <!-- Bottom Right: Live Stock Status Pill -->
                        <div class="absolute bottom-2 right-2.5">
                            <span id="spice-stock-pill-{{ $sp->id }}" class="px-2 py-0.5 rounded-md backdrop-blur-xs text-[9px] font-bold flex items-center gap-1 shadow-xs {{ ($sp->stock_quantity ?? 0) > ($sp->low_stock_threshold ?? 5) ? 'bg-emerald-900/85 text-emerald-100' : (($sp->stock_quantity ?? 0) > 0 ? 'bg-amber-900/85 text-amber-100' : 'bg-rose-900/85 text-rose-100') }}">
                                <i data-lucide="package" class="w-3 h-3"></i>
                                <span id="spice-stock-pill-text-{{ $sp->id }}">{{ ($sp->stock_quantity ?? 0) > 0 ? ($sp->stock_quantity . ' in stock') : 'Out of stock' }}</span>
                            </span>
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

                        <!-- Inline Restock Steppers (Phase 5 High-Density UX) -->
                        <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg p-0.5 shadow-2xs" title="1-Click Quick Restock">
                            <span class="text-[10px] font-bold text-gray-500 px-1.5 flex items-center gap-1">
                                <i data-lucide="package" class="w-3 h-3 text-brand-primary"></i>
                                <span id="spice-stock-qty-{{ $sp->id }}">{{ $sp->stock_quantity ?? 0 }}</span>
                            </span>
                            <button type="button" onclick="quickAdjustSpiceStock({{ $sp->id }}, -1)" class="w-5 h-5 rounded flex items-center justify-center bg-gray-100 hover:bg-rose-100 text-gray-700 hover:text-rose-700 text-[10px] font-bold transition" title="Deduct 1 unit">-1</button>
                            <button type="button" onclick="quickAdjustSpiceStock({{ $sp->id }}, 10)" class="px-1.5 h-5 rounded flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[10px] font-bold transition" title="Add 10 units">+10</button>
                            <button type="button" onclick="quickAdjustSpiceStock({{ $sp->id }}, 50)" class="px-1.5 h-5 rounded flex items-center justify-center bg-emerald-100 hover:bg-emerald-200 text-emerald-900 text-[10px] font-bold transition" title="Add 50 units">+50</button>
                        </div>
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
</section>
