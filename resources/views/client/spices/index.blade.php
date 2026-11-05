@extends('layouts.customer')

@section('title', 'Country Side Spices | Single-Origin Kerala Plantation Harvest')
@section('meta_description', 'Shop fresh, unadulterated high-altitude green cardamom, black tellicherry pepper, cloves, and wild cinnamon directly harvested from Country Side Cottages shade-grown plantations.')

@push('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "Home", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Spices", "item": "{{ route('spices.index') }}" }
    ]
}
</script>
@endpush

@section('content')
<!-- HERO SECTION -->
<div class="bg-forest text-paper py-6 sm:py-10 md:py-12 px-4 sm:px-6 relative overflow-hidden">
    <div class="mx-auto max-w-[1480px] text-center relative z-10">
        <span class="eyebrow text-brass block mb-2">High-Altitude Single-Estate Spices</span>
        <h1 class="serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight">Country Side Spices Shop</h1>
        <p class="text-xs sm:text-sm text-paper/70 max-w-2xl mx-auto mt-3 leading-relaxed">
            Sun-dried, unadulterated cardamom, black peppercorns, cloves, and wild cinnamon harvested directly from our shade-grown plantation reserves.
        </p>

        <!-- CART BUTTON -->
        <button onclick="openSpiceCart()" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brass hover:brightness-105 text-forest font-bold text-xs shadow-card transition touch-tap">
            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
            <span>Spice Basket (<span id="spice-cart-badge">0</span> items)</span>
        </button>
    </div>
</div>

<!-- SUCCESS ORDER BANNER (IF REDIRECTED AFTER ORDER) -->
@if(session('order_success'))
<div class="mx-auto max-w-4xl mt-6 px-4">
    <div class="bg-mint border border-emerald/30 rounded-2xl p-5 flex items-center gap-4 text-forest shadow-card">
        <div class="w-10 h-10 rounded-xl bg-forest flex items-center justify-center shrink-0 text-brass">
            <i data-lucide="check" class="w-6 h-6"></i>
        </div>
        <div>
            <h4 class="serif font-bold text-base text-forest">Thank you, {{ session('order_success')['customer'] }}! Order Placed.</h4>
            <p class="text-xs text-forest/70 mt-0.5">
                Order <strong>#{{ session('order_success')['order_number'] }}</strong> for ₹{{ session('order_success')['total'] }} has been received and is being hand-packed at our estate.
            </p>
        </div>
    </div>
</div>
@endif

<!-- CATEGORY & SEARCH BAR -->
<div class="bg-paper/95 backdrop-blur-md border-b border-forest/10 sticky top-16 md:top-20 z-30 shadow-xs">
    <div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-3 flex items-center justify-between gap-4 overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('spices.index') }}" 
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ empty($categoryId) ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                All Spices ({{ $products->count() }})
            </a>
            @foreach($categories as $c)
            <a href="{{ route('spices.index', ['category_id' => $c->id]) }}" 
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition whitespace-nowrap {{ (string)$categoryId === (string)$c->id ? 'bg-forest text-paper' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                {{ $c->name }}
            </a>
            @endforeach
        </div>

        <form action="{{ route('spices.index') }}" method="GET" class="shrink-0 hidden md:block">
            <div class="relative">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search cardamom, pepper..." class="w-56 pl-8 pr-3 py-1.5 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                <i data-lucide="search" class="w-3.5 h-3.5 text-forest/40 absolute left-2.5 top-2.5"></i>
            </div>
        </form>
    </div>
</div>

<!-- PRODUCTS CATALOG GRID -->
<div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-8 sm:py-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 lg:gap-6">
        @forelse($products as $prod)
        @php
            $mode = $prod->selling_mode ?: 'both';
            $kgPrice = (float)($prod->price_per_kg ?: round($prod->price * (1000 / ($prod->weight_grams ?: 100)), 2));
            $taxRate = (float)($prod->tax_rate ?: 5.00);
            $packSize = $prod->package_size ?: ($prod->weight_grams . 'g Pack');
        @endphp
        <div class="bg-white rounded-[24px] soft-border overflow-hidden shadow-card lift transition duration-300 flex flex-col justify-between group">
            <div>
                <!-- Image & Badges -->
                <div class="img-zoom relative h-48 sm:h-52 w-full overflow-hidden bg-mint flex items-center justify-center">
                    @if($prod->image_url)
                        <img src="{{ $prod->image_url }}" 
                             alt="{{ $prod->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-mint/80 to-paper text-forest/40 p-4 text-center">
                            <i data-lucide="leaf" class="w-8 h-8 text-emerald/50 mb-1.5"></i>
                            <span class="text-xs font-semibold text-forest/70 line-clamp-1">{{ $prod->name }}</span>
                            <span class="text-[10px] text-forest/40">{{ $prod->category ? $prod->category->name : 'Single Estate Spice' }}</span>
                        </div>
                    @endif
                    
                    <!-- Selling Mode Pill -->
                    <div class="absolute top-3 left-3 bg-forest/90 backdrop-blur-md text-paper px-2.5 py-0.5 rounded-md text-[10px] font-bold shadow-xs">
                        @if($mode === 'loose')
                            Loose KG Only
                        @elseif($mode === 'packet')
                            {{ $packSize }}
                        @else
                            Packets &amp; Loose KG
                        @endif
                    </div>

                    @if($prod->is_featured)
                    <div class="absolute top-3 right-3 bg-brass text-forest px-2.5 py-0.5 rounded-full text-[10px] font-bold shadow-xs">
                        Single Estate
                    </div>
                    @endif
                </div>

                <!-- Product Title & Info -->
                <div class="p-5 pb-3 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="eyebrow text-emerald block">
                            {{ $prod->category ? $prod->category->name : 'Estate Spices' }}
                        </span>
                        <span class="text-[10px] text-forest/50 font-semibold bg-paper px-2 py-0.5 rounded-md">
                            +{{ $taxRate }}% GST
                        </span>
                    </div>
                    <h3 class="serif text-base font-bold text-forest group-hover:text-emerald transition">
                        {{ $prod->name }}
                    </h3>
                    <p class="text-xs text-forest/70 line-clamp-2 leading-relaxed">
                        {{ $prod->short_description ?: $prod->description ?: 'Harvested at peak botanical maturity and naturally shade dried for maximum essential oil potency.' }}
                    </p>
                </div>

                <!-- SELLING MODE SWITCHER TABS (Only for 'both') -->
                @if($mode === 'both')
                <div class="px-5 pb-2">
                    <div class="flex items-center p-1 rounded-xl bg-paper/80 soft-border text-xs">
                        <button type="button" 
                                onclick="switchCardMode({{ $prod->id }}, 'packet')" 
                                id="tab-packet-{{ $prod->id }}" 
                                class="flex-1 py-1 rounded-lg font-bold bg-white text-forest shadow-xs text-center transition touch-tap">
                            Packets
                        </button>
                        <button type="button" 
                                onclick="switchCardMode({{ $prod->id }}, 'loose')" 
                                id="tab-loose-{{ $prod->id }}" 
                                class="flex-1 py-1 rounded-lg font-semibold text-forest/60 hover:text-forest text-center transition touch-tap">
                            Custom Loose (KG)
                        </button>
                    </div>
                </div>
                @endif

                <!-- PACKET ORDERING SECTION -->
                <div id="panel-packet-{{ $prod->id }}" class="px-5 space-y-3 {{ $mode === 'loose' ? 'hidden' : '' }}">
                    <div class="flex items-baseline justify-between pt-1">
                        <div>
                            <span class="text-lg font-bold text-forest">₹{{ number_format($prod->price) }}</span>
                            <span class="text-[11px] text-forest/60 ml-1">/ {{ $packSize }}</span>
                        </div>
                        @if($prod->compare_at_price && $prod->compare_at_price > $prod->price)
                            <span class="text-xs text-forest/40 line-through">₹{{ number_format($prod->compare_at_price) }}</span>
                        @endif
                    </div>

                    <!-- Quantity Stepper & Add to Cart -->
                    <div class="flex items-center gap-2">
                        <div class="flex items-center bg-paper soft-border rounded-xl p-1 shrink-0">
                            <button type="button" onclick="adjustCardQty({{ $prod->id }}, -1)" class="w-7 h-7 rounded-lg bg-white flex items-center justify-center text-xs font-bold text-forest hover:bg-forest hover:text-paper transition touch-tap">-</button>
                            <input type="text" id="card-qty-{{ $prod->id }}" value="1" readonly class="w-8 text-center text-xs font-bold text-forest bg-transparent border-0 focus:outline-hidden">
                            <button type="button" onclick="adjustCardQty({{ $prod->id }}, 1)" class="w-7 h-7 rounded-lg bg-white flex items-center justify-center text-xs font-bold text-forest hover:bg-forest hover:text-paper transition touch-tap">+</button>
                        </div>
                        <button type="button" 
                                onclick="addPacketToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->price }}, '{{ addslashes($packSize) }}', {{ $taxRate }})"
                                class="flex-1 py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-1.5 shadow-card transition touch-tap">
                            <i data-lucide="shopping-bag" class="w-3.5 h-3.5 text-brass"></i>
                            <span>Add Packets</span>
                        </button>
                    </div>
                </div>

                <!-- LOOSE KG ORDERING SECTION -->
                <div id="panel-loose-{{ $prod->id }}" class="px-5 space-y-3 {{ $mode !== 'loose' ? 'hidden' : '' }}">
                    <div class="flex items-baseline justify-between pt-1">
                        <div>
                            <span class="text-lg font-bold text-forest">₹{{ number_format($kgPrice) }}</span>
                            <span class="text-[11px] text-forest/60 ml-1">/ Kilogram</span>
                        </div>
                        <span class="text-[10px] bg-mint text-emerald font-bold px-2 py-0.5 rounded-md">Flexible KG</span>
                    </div>

                    <!-- Quick weight presets -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @foreach([0.25, 0.50, 1.00, 2.50] as $w)
                        <button type="button" 
                                onclick="setCardLooseWeight({{ $prod->id }}, {{ $w }}, {{ $kgPrice }})" 
                                class="px-2 py-1 rounded-lg bg-paper hover:bg-forest hover:text-paper soft-border text-[11px] font-semibold text-forest transition touch-tap">
                            {{ number_format($w, 2) }} kg
                        </button>
                        @endforeach
                    </div>

                    <!-- Decimal weight input & live calculated total -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="number" 
                                       step="0.05" 
                                       min="0.05" 
                                       id="card-loose-kg-{{ $prod->id }}" 
                                       value="1.00" 
                                       oninput="calcCardLooseTotal({{ $prod->id }}, {{ $kgPrice }})"
                                       class="w-full pl-3 pr-8 py-1.5 rounded-xl bg-paper/40 soft-border text-xs font-bold text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                                <span class="absolute right-2.5 top-1.5 text-[10px] font-bold text-forest/50">KG</span>
                            </div>
                            <button type="button" 
                                    onclick="addLooseToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $kgPrice }}, {{ $taxRate }})"
                                    class="py-2 px-3 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-1.5 shadow-card transition touch-tap">
                                <i data-lucide="plus" class="w-3.5 h-3.5 text-brass"></i>
                                <span>Add Loose</span>
                            </button>
                        </div>
                        <div class="text-[11px] text-forest/70 flex items-center justify-between bg-mint/50 p-2 rounded-xl border border-emerald/15">
                            <span>Est: <strong id="card-loose-calc-{{ $prod->id }}">₹{{ number_format($kgPrice) }}</strong></span>
                            <span class="text-[10px] text-forest/50 font-mono">+{{ $taxRate }}% GST</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card bottom spacing -->
            <div class="p-5 pt-3"></div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-[24px] soft-border">
            <i data-lucide="leaf" class="w-10 h-10 text-brass mx-auto mb-3"></i>
            <h3 class="serif text-xl font-bold text-forest">No Spices Found</h3>
            <p class="text-xs text-forest/60 mt-1">Please try another filter or search term.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- SPICE CART SLIDE-OVER DRAWER -->
<div id="spice-cart-drawer" class="hidden fixed inset-0 z-[90] bg-forest/40 backdrop-blur-sm flex justify-end">
    <div class="w-full sm:w-[480px] bg-paper h-full flex flex-col justify-between shadow-2xl overflow-y-auto">
        <!-- Header -->
        <div class="bg-forest text-paper p-5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10 text-brass"><i data-lucide="shopping-bag" class="w-4 h-4"></i></span>
                <div>
                    <h3 class="serif text-lg font-bold">Your Spice Basket</h3>
                    <p class="text-[9px] uppercase tracking-widest text-paper/60">Country Side Farm Harvest</p>
                </div>
            </div>
            <button onclick="closeSpiceCart()" class="text-paper/70 hover:text-paper p-1.5 rounded-lg hover:bg-white/10 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Cart Items List -->
        <div class="p-6 flex-1 space-y-4 overflow-y-auto">
            <div id="spice-cart-items" class="space-y-3">
                <!-- Populated via JS -->
            </div>
        </div>

        <!-- Footer / Subtotal / Checkout -->
        <div class="p-6 bg-white border-t border-forest/10 space-y-3">
            <div class="space-y-1.5 text-xs text-forest/70">
                <div class="flex justify-between">
                    <span>Items Subtotal:</span>
                    <span id="spice-cart-subtotal" class="font-bold text-forest">₹0</span>
                </div>
                <div class="flex justify-between">
                    <span>Applicable GST (5%):</span>
                    <span id="spice-cart-tax" class="font-bold text-forest">₹0</span>
                </div>
                <div class="flex justify-between">
                    <span>Estimated Shipping:</span>
                    <span id="spice-cart-shipping" class="font-bold text-emerald">Calculated at Checkout</span>
                </div>
                <div class="pt-2 border-t border-forest/10 flex justify-between font-bold text-sm text-forest">
                    <span>Estimated Total:</span>
                    <span id="spice-cart-total">₹0</span>
                </div>
            </div>

            <p class="text-[10px] text-forest/50">Orders packaged directly from single-estate reserves. Free delivery for in-villa guests or orders above ₹999 across India.</p>

            <a href="{{ route('spices.checkout') }}" id="spice-checkout-link" class="w-full py-3.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-2 shadow-card transition touch-tap">
                <span>Proceed to Shipping &amp; Checkout</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Cart storage schema:
    // [{ id, name, type: 'packet'|'loose', price, qty, weight_kg, size, tax_rate }]
    let spiceCart = JSON.parse(localStorage.getItem('countryside_spices_cart') || localStorage.getItem('village_spices_cart') || localStorage.getItem('krishna_spices_cart')) || [];

    function saveSpiceCart() {
        localStorage.setItem('countryside_spices_cart', JSON.stringify(spiceCart));
        localStorage.setItem('village_spices_cart', JSON.stringify(spiceCart));
        updateSpiceCartUI();
    }

    // Switch between Packets and Custom Loose KG on card
    function switchCardMode(prodId, mode) {
        const tabPacket = document.getElementById(`tab-packet-${prodId}`);
        const tabLoose = document.getElementById(`tab-loose-${prodId}`);
        const pnlPacket = document.getElementById(`panel-packet-${prodId}`);
        const pnlLoose = document.getElementById(`panel-loose-${prodId}`);

        if (mode === 'packet') {
            tabPacket.className = 'flex-1 py-1 rounded-lg font-bold bg-white text-forest shadow-xs text-center transition touch-tap';
            tabLoose.className = 'flex-1 py-1 rounded-lg font-semibold text-forest/60 hover:text-forest text-center transition touch-tap';
            pnlPacket.classList.remove('hidden');
            pnlLoose.classList.add('hidden');
        } else {
            tabLoose.className = 'flex-1 py-1 rounded-lg font-bold bg-white text-forest shadow-xs text-center transition touch-tap';
            tabPacket.className = 'flex-1 py-1 rounded-lg font-semibold text-forest/60 hover:text-forest text-center transition touch-tap';
            pnlLoose.classList.remove('hidden');
            pnlPacket.classList.add('hidden');
        }
    }

    // Packet Stepper
    function adjustCardQty(prodId, delta) {
        const inp = document.getElementById(`card-qty-${prodId}`);
        if (!inp) return;
        let val = parseInt(inp.value, 10) || 1;
        val = Math.max(1, val + delta);
        inp.value = val;
    }

    // Add Packet to Basket
    function addPacketToCart(id, name, price, size, taxRate) {
        const qtyInp = document.getElementById(`card-qty-${id}`);
        const qty = qtyInp ? Math.max(1, parseInt(qtyInp.value, 10) || 1) : 1;

        const existing = spiceCart.find(i => i.id === id && i.type === 'packet');
        if (existing) {
            existing.qty += qty;
        } else {
            spiceCart.push({
                id: id,
                name: name,
                type: 'packet',
                price: parseFloat(price),
                qty: qty,
                weight_kg: null,
                size: size || 'Pack',
                tax_rate: parseFloat(taxRate) || 5.00
            });
        }
        saveSpiceCart();
        if (typeof window.availabilityToast === 'function') {
            availabilityToast(`${qty}x ${name} added to Basket`);
        }
        openSpiceCart();
    }

    // Loose Presets & Calculator
    function setCardLooseWeight(prodId, weight, kgPrice) {
        const inp = document.getElementById(`card-loose-kg-${prodId}`);
        if (inp) {
            inp.value = parseFloat(weight).toFixed(2);
            calcCardLooseTotal(prodId, kgPrice);
        }
    }

    function calcCardLooseTotal(prodId, kgPrice) {
        const inp = document.getElementById(`card-loose-kg-${prodId}`);
        const out = document.getElementById(`card-loose-calc-${prodId}`);
        if (!inp || !out) return;
        const w = Math.max(0.05, parseFloat(inp.value) || 0.05);
        const est = Math.round(w * kgPrice);
        out.textContent = '₹' + est.toLocaleString('en-IN');
    }

    // Add Loose KG to Basket
    function addLooseToCart(id, name, kgPrice, taxRate) {
        const inp = document.getElementById(`card-loose-kg-${id}`);
        const weight = inp ? Math.max(0.05, parseFloat(inp.value) || 1.0) : 1.0;
        const roundedWeight = Math.round(weight * 100) / 100;

        const existing = spiceCart.find(i => i.id === id && i.type === 'loose');
        if (existing) {
            existing.weight_kg = Math.round((existing.weight_kg + roundedWeight) * 100) / 100;
            existing.size = `${existing.weight_kg.toFixed(2)} kg loose`;
        } else {
            spiceCart.push({
                id: id,
                name: name,
                type: 'loose',
                price: parseFloat(kgPrice),
                qty: 1,
                weight_kg: roundedWeight,
                size: `${roundedWeight.toFixed(2)} kg loose`,
                tax_rate: parseFloat(taxRate) || 5.00
            });
        }
        saveSpiceCart();
        if (typeof window.availabilityToast === 'function') {
            availabilityToast(`${roundedWeight.toFixed(2)} kg ${name} added to Basket`);
        }
        openSpiceCart();
    }

    // Adjust Cart Item
    function updateSpiceItem(index, delta) {
        const item = spiceCart[index];
        if (!item) return;

        if (item.type === 'loose') {
            item.weight_kg = Math.max(0.05, Math.round((item.weight_kg + (delta * 0.25)) * 100) / 100);
            item.size = `${item.weight_kg.toFixed(2)} kg loose`;
            if (item.weight_kg <= 0.05 && delta < 0) {
                // remove item if decreasing beyond min
                spiceCart.splice(index, 1);
            }
        } else {
            item.qty += delta;
            if (item.qty <= 0) {
                spiceCart.splice(index, 1);
            }
        }
        saveSpiceCart();
    }

    function removeSpiceItem(index) {
        spiceCart.splice(index, 1);
        saveSpiceCart();
    }

    // Update Drawer UI
    function updateSpiceCartUI() {
        const badge = document.getElementById('spice-cart-badge');
        const count = spiceCart.reduce((s, i) => s + (i.type === 'loose' ? 1 : i.qty), 0);
        if (badge) badge.textContent = count;

        const container = document.getElementById('spice-cart-items');
        if (!container) return;

        if (spiceCart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 text-forest/50">
                    <i data-lucide="leaf" class="w-8 h-8 mx-auto mb-2 text-forest/30"></i>
                    <p class="text-xs font-semibold">Your spice basket is empty</p>
                    <p class="text-[11px] text-forest/40 mt-1">Select sealed packets or custom loose harvest to order.</p>
                </div>
            `;
            document.getElementById('spice-cart-subtotal').textContent = '₹0';
            document.getElementById('spice-cart-tax').textContent = '₹0';
            document.getElementById('spice-cart-total').textContent = '₹0';
            const chk = document.getElementById('spice-checkout-link');
            if (chk) chk.classList.add('pointer-events-none', 'opacity-50');
            if (window.lucide) lucide.createIcons();
            return;
        }

        const chk = document.getElementById('spice-checkout-link');
        if (chk) chk.classList.remove('pointer-events-none', 'opacity-50');

        let subtotal = 0;
        let totalTax = 0;
        let html = '';

        spiceCart.forEach((i, idx) => {
            const line = (i.type === 'loose')
                ? Math.round(i.price * i.weight_kg * 100) / 100
                : Math.round(i.price * i.qty * 100) / 100;

            const taxRate = i.tax_rate || 5.0;
            const lineTax = Math.round(line * (taxRate / 100) * 100) / 100;

            subtotal += line;
            totalTax += lineTax;

            const isLoose = (i.type === 'loose');

            html += `
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-white soft-border shadow-xs">
                    <div class="flex-1 pr-2 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h5 class="text-xs font-bold text-forest truncate">${i.name}</h5>
                            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase ${isLoose ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'}">
                                ${isLoose ? 'Loose' : 'Packet'}
                            </span>
                        </div>
                        <div class="text-[11px] text-forest/60 mt-0.5">
                            ${isLoose 
                                ? `${i.weight_kg.toFixed(2)} kg &times; ₹${i.price.toLocaleString('en-IN')}/kg = <strong>₹${line.toLocaleString('en-IN')}</strong>` 
                                : `${i.size} &middot; ₹${i.price.toLocaleString('en-IN')} &times; ${i.qty} = <strong>₹${line.toLocaleString('en-IN')}</strong>`}
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <div class="flex items-center gap-1 bg-paper soft-border rounded-xl p-1">
                            <button type="button" onclick="updateSpiceItem(${idx}, -1)" class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-xs font-bold text-forest hover:bg-forest hover:text-paper transition touch-tap">-</button>
                            <span class="text-xs font-bold px-1.5 text-forest">${isLoose ? i.weight_kg.toFixed(2) + 'k' : i.qty}</span>
                            <button type="button" onclick="updateSpiceItem(${idx}, 1)" class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-xs font-bold text-forest hover:bg-forest hover:text-paper transition touch-tap">+</button>
                        </div>
                        <button type="button" onclick="removeSpiceItem(${idx})" class="text-forest/30 hover:text-rose-600 p-1 transition" title="Remove">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        const grandTotal = Math.round(subtotal + totalTax);

        document.getElementById('spice-cart-subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN');
        document.getElementById('spice-cart-tax').textContent = '₹' + totalTax.toLocaleString('en-IN');
        document.getElementById('spice-cart-total').textContent = '₹' + grandTotal.toLocaleString('en-IN');

        if (window.lucide) lucide.createIcons();
    }

    function openSpiceCart() {
        const drawer = document.getElementById('spice-cart-drawer');
        if (drawer) {
            drawer.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            updateSpiceCartUI();
        }
    }

    function closeSpiceCart() {
        const drawer = document.getElementById('spice-cart-drawer');
        if (drawer) {
            drawer.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    document.getElementById('spice-cart-drawer')?.addEventListener('click', (e) => {
        if (e.target.id === 'spice-cart-drawer') closeSpiceCart();
    });

    document.addEventListener('DOMContentLoaded', updateSpiceCartUI);
</script>
@endpush
@endsection
