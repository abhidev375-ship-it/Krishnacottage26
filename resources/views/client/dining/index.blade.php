@extends('layouts.customer')

@section('title', 'Heritage Dining & Kitchen | Krishna Cottages')

@section('content')
<!-- HERO SECTION -->
<div class="bg-forest text-paper py-6 sm:py-10 md:py-14 px-4 sm:px-6 relative overflow-hidden">
    <div class="mx-auto max-w-[1480px] text-center relative z-10">
        <span class="eyebrow text-brass block mb-2">Heritage Kerala Gastronomy</span>
        <h1 class="serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight">The Plantation Kitchen</h1>
        <p class="text-xs sm:text-sm text-paper/70 max-w-2xl mx-auto mt-3 leading-relaxed">
            Slow-cooked recipes passed through generations, prepared in clay earthen pots with estate-harvested spices, cold-pressed coconut oil, and farm-fresh ingredients.
        </p>

        @if(Auth::check() && $isInHouse)
        <!-- CART SUMMARY CAPSULE TRIGGER (IN-HOUSE GUESTS ONLY) -->
        <button onclick="openCartDrawer()" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brass hover:brightness-105 text-forest font-bold text-xs shadow-card transition touch-tap">
            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
            <span>View Food Order Tray (<span id="cart-count-badge">0</span> items)</span>
        </button>
        @endif
    </div>
</div>

<!-- BRANCH, CATEGORY & DIETARY FILTER TABS -->
<div class="bg-paper/95 backdrop-blur-md border-b border-forest/10 sticky top-16 md:top-20 z-30 shadow-xs space-y-2 py-3">
    <div class="mx-auto max-w-[1480px] px-4 sm:px-6 flex flex-col gap-2.5">
        <!-- 1. Branch Selector Tabs (All Branches viewable) -->
        <div class="flex items-center justify-between gap-3 overflow-x-auto no-scrollbar">
            <div class="flex items-center gap-1.5 shrink-0">
                <span class="text-[10px] uppercase font-bold text-forest/50 tracking-wider mr-1">Branch:</span>
                <a href="{{ route('dining.index', array_merge(request()->except('branch_id'), [])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ empty($branchId) ? 'bg-forest text-paper shadow-xs' : 'bg-white/80 text-forest/70 hover:bg-white soft-border' }}">
                    🌐 All Branches
                </a>
                @foreach($branches as $b)
                <a href="{{ route('dining.index', array_merge(request()->query(), ['branch_id' => $b->id])) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition whitespace-nowrap {{ (string)$branchId === (string)$b->id ? 'bg-forest text-paper shadow-xs' : 'bg-white/80 text-forest/70 hover:bg-white soft-border' }}">
                    📍 {{ $b->name }}
                </a>
                @endforeach
            </div>

            <!-- Dietary Toggle -->
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('dining.index', array_merge(request()->query(), ['dietary' => request('dietary') === 'veg' ? '' : 'veg'])) }}" 
                   class="px-3 py-1.5 rounded-full text-xs font-semibold border transition flex items-center gap-1.5 {{ request('dietary') === 'veg' ? 'bg-mint border-emerald/40 text-emerald' : 'bg-white/80 soft-border text-forest/70 hover:bg-white' }}">
                    <span class="w-2 h-2 rounded-full bg-emerald"></span>
                    <span>Pure Veg Only</span>
                </a>
            </div>
        </div>

        <!-- 2. Category Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pt-1 border-t border-forest/5">
            <span class="text-[10px] uppercase font-bold text-forest/50 tracking-wider mr-1">Course:</span>
            <a href="{{ route('dining.index', array_merge(request()->except('category_id'), [])) }}" 
               class="px-3 py-1 rounded-full text-xs font-semibold transition {{ empty($categoryId) ? 'bg-emerald text-paper shadow-xs' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                All Courses
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('dining.index', array_merge(request()->query(), ['category_id' => $cat->id])) }}" 
               class="px-3 py-1 rounded-full text-xs font-semibold transition whitespace-nowrap {{ (string)$categoryId === (string)$cat->id ? 'bg-emerald text-paper shadow-xs' : 'bg-white/70 text-forest/70 hover:bg-white soft-border' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>
</div>

<!-- COMPACT LUXURY MENU ITEMS GRID -->
<div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-8 sm:py-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 lg:gap-6">
        @forelse($items as $item)
        <div class="bg-white rounded-[22px] soft-border overflow-hidden shadow-card lift transition duration-300 flex flex-col justify-between group">
            <div>
                <!-- Compact Image Container -->
                <div class="relative h-40 sm:h-44 w-full overflow-hidden bg-mint">
                    <img src="{{ $item->image_url ?: 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=600&q=80' }}" 
                         alt="{{ $item->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                    <!-- VEG/NON-VEG BADGE -->
                    <div class="absolute top-2.5 left-2.5 bg-white/95 backdrop-blur-xs px-2 py-0.5 rounded-md shadow-xs flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full {{ $item->is_vegetarian ? 'bg-emerald' : 'bg-rose-600' }}"></span>
                        <span class="text-[9px] font-bold uppercase {{ $item->is_vegetarian ? 'text-emerald' : 'text-rose-700' }}">
                            {{ $item->is_vegetarian ? 'Veg' : 'Non-Veg' }}
                        </span>
                    </div>

                    <!-- Branch Allocation Badge -->
                    <div class="absolute bottom-2.5 left-2.5 bg-forest/90 text-paper backdrop-blur-xs px-2 py-0.5 rounded text-[9px] font-bold">
                        @if($item->is_all_branches || is_null($item->branch_id))
                            🌐 All Branches
                        @elseif($item->branch)
                            📍 {{ $item->branch->name }}
                        @else
                            📍 Plantation
                        @endif
                    </div>

                    @if($item->is_featured)
                    <div class="absolute top-2.5 right-2.5 bg-brass text-forest px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider shadow-xs">
                        Chef's Special
                    </div>
                    @endif
                </div>

                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="serif text-base font-bold text-forest group-hover:text-emerald transition leading-snug line-clamp-1" title="{{ $item->name }}">
                            {{ $item->name }}
                        </h3>
                        <span class="serif font-bold text-base text-forest shrink-0">₹{{ number_format($item->price) }}</span>
                    </div>

                    <p class="text-xs text-forest/70 leading-relaxed line-clamp-2">
                        {{ $item->short_description ?: $item->description ?: 'Authentic preparation crafted daily with hand-ground spices.' }}
                    </p>

                    <!-- Rating & Dietary Tags -->
                    <div class="flex items-center justify-between pt-1 text-[10px]">
                        @if($item->reviews_count > 0)
                        <div class="flex items-center gap-1 text-amber-600 font-bold">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                            <span>{{ number_format($item->average_rating, 1) }}</span>
                            <span class="text-forest/40">({{ $item->reviews_count }})</span>
                        </div>
                        @else
                        <span class="text-forest/40">{{ $item->category ? $item->category->name : 'Kitchen' }}</span>
                        @endif
                        <span class="text-forest/50 font-medium">{{ $item->prep_time_minutes }}m prep</span>
                    </div>
                </div>
            </div>

            <!-- ACTION FOOTER: Gated strictly for in-house orders -->
            <div class="p-4 pt-0">
                @if(Auth::check() && $isInHouse)
                <button onclick="addToDiningCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})" 
                        class="w-full py-2 rounded-xl bg-paper hover:bg-forest hover:text-paper soft-border text-forest font-bold text-xs flex items-center justify-center gap-1.5 transition touch-tap">
                    <i data-lucide="plus" class="w-3.5 h-3.5 text-emerald"></i>
                    <span>Order to Villa {{ $inHouseStay->room ? $inHouseStay->room->room_number : '' }}</span>
                </button>
                @elseif(Auth::check())
                <div class="w-full py-2 rounded-xl bg-paper/50 text-forest/60 text-[10px] font-semibold text-center border border-forest/10">
                    <span>In-Villa Ordering Unlocks at Check-in</span>
                </div>
                @else
                <div class="w-full py-2 rounded-xl bg-paper/50 text-forest/50 text-[10px] font-medium text-center border border-forest/10">
                    <span>Curated for In-House Plantation Dining</span>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center py-16 bg-white rounded-[24px] soft-border">
            <i data-lucide="utensils" class="w-10 h-10 text-brass mx-auto mb-3"></i>
            <h3 class="serif text-xl font-bold text-forest">No Dishes Found</h3>
            <p class="text-xs text-forest/60 mt-1">Please adjust your branch or category filter.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- IN-HOUSE FOOD ORDER SLIDE-OVER / BOTTOM SHEET -->
<div id="cart-drawer" class="hidden fixed inset-0 z-[90] bg-forest/40 backdrop-blur-sm flex justify-end">
    <div class="w-full sm:w-[460px] bg-paper h-full flex flex-col justify-between shadow-2xl overflow-y-auto">
        <!-- HEADER -->
        <div class="bg-forest text-paper p-5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/10 text-brass"><i data-lucide="utensils" class="w-4 h-4"></i></span>
                <div>
                    <h3 class="serif text-lg font-bold">Your Dining Tray</h3>
                    <p class="text-[9px] uppercase tracking-widest text-paper/60">Plantation In-House Orders</p>
                </div>
            </div>
            <button onclick="closeCartDrawer()" class="text-paper/70 hover:text-paper p-1.5 rounded-lg hover:bg-white/10 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- ITEMS LIST -->
        <div class="p-6 flex-1 space-y-4">
            <div id="cart-items-container" class="space-y-3">
                <!-- Dynamically populated via JS -->
            </div>

            <!-- GUEST IN-HOUSE DETAILS FORM -->
            <div id="order-form-section" class="pt-4 border-t border-forest/10 space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="serif text-base font-bold text-forest">Order Details (In-House Guest)</h4>
                    @guest
                    <a href="{{ route('login', ['redirect' => route('dining.index')]) }}" class="text-[11px] text-emerald font-bold hover:underline">Sign in to order</a>
                    @endguest
                </div>

                @guest
                <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-900 text-xs flex items-center justify-between gap-2">
                    <span>Signing in connects this order directly to your room folio.</span>
                    <a href="{{ route('login', ['redirect' => route('dining.index')]) }}" class="shrink-0 px-2.5 py-1 rounded-lg bg-forest text-paper font-bold text-[10px]">Sign In</a>
                </div>
                @endguest
                
                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Your Full Name *</label>
                    <input type="text" id="order-guest-name" value="{{ Auth::check() ? Auth::user()->name : '' }}" placeholder="Aditya Nair" class="w-full px-3.5 py-2 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Phone Number *</label>
                        <input type="tel" id="order-guest-phone" value="{{ Auth::check() ? Auth::user()->phone : '' }}" placeholder="+91 98765 43210" class="w-full px-3.5 py-2 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Order Type *</label>
                        <select id="order-type" class="w-full px-3.5 py-2 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                            <option value="room_service">Room Service (In-Villa)</option>
                            <option value="dine_in">Restaurant Table</option>
                            <option value="takeaway">Estate Picnic Takeaway</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Villa Unit / Table Number</label>
                    <input type="text" id="order-table-num" placeholder="e.g. Villa 104 or Table 6" class="w-full px-3.5 py-2 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Special Chef Instructions</label>
                    <input type="text" id="order-instructions" placeholder="e.g. Less spicy, extra coconut chutney..." class="w-full px-3.5 py-2 rounded-xl bg-white soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                </div>
            </div>
        </div>

        <!-- FOOTER & CHECKOUT BUTTON -->
        <div class="p-6 bg-white border-t border-forest/10 space-y-3">
            <div class="space-y-1 text-xs text-forest/70">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal" class="font-bold text-forest">₹0</span>
                </div>
                <div class="flex justify-between">
                    <span>GST (5% Restaurant):</span>
                    <span id="cart-tax" class="font-bold text-forest">₹0</span>
                </div>
                <div class="flex justify-between font-bold text-sm text-forest pt-2 border-t border-forest/10">
                    <span>Total Amount:</span>
                    <span id="cart-total">₹0</span>
                </div>
            </div>

            <button onclick="submitDiningOrder()" id="submit-order-btn" class="w-full py-3.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-2 shadow-card transition touch-tap">
                <span>Transmit Order to Kitchen</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
            </button>
            <p class="text-[10px] text-center text-forest/50">Orders are prepared fresh & billed to your stay folio upon delivery.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let diningCart = JSON.parse(localStorage.getItem('krishna_dining_cart')) || [];

    function saveCart() {
        localStorage.setItem('krishna_dining_cart', JSON.stringify(diningCart));
        updateCartUI();
    }

    function addToDiningCart(id, name, price) {
        const existing = diningCart.find(i => i.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            diningCart.push({ id, name, price, qty: 1 });
        }
        saveCart();
        availabilityToast(`${name} added to Order Tray`);
        openCartDrawer();
    }

    function updateCartQty(id, delta) {
        const item = diningCart.find(i => i.id === id);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) {
            diningCart = diningCart.filter(i => i.id !== id);
        }
        saveCart();
    }

    function updateCartUI() {
        const countBadge = document.getElementById('cart-count-badge');
        const totalCount = diningCart.reduce((sum, i) => sum + i.qty, 0);
        if (countBadge) countBadge.textContent = totalCount;

        const container = document.getElementById('cart-items-container');
        if (!container) return;

        if (diningCart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-10 text-forest/50">
                    <i data-lucide="utensils" class="w-8 h-8 mx-auto mb-2 text-forest/30"></i>
                    <p class="text-xs">Your food tray is empty</p>
                </div>
            `;
            document.getElementById('cart-subtotal').textContent = '₹0';
            document.getElementById('cart-tax').textContent = '₹0';
            document.getElementById('cart-total').textContent = '₹0';
            document.getElementById('submit-order-btn').disabled = true;
            document.getElementById('submit-order-btn').classList.add('opacity-50', 'cursor-not-allowed');
            if (window.lucide) lucide.createIcons();
            return;
        }

        document.getElementById('submit-order-btn').disabled = false;
        document.getElementById('submit-order-btn').classList.remove('opacity-50', 'cursor-not-allowed');

        let subtotal = 0;
        let html = '';

        diningCart.forEach(i => {
            const lineTotal = i.price * i.qty;
            subtotal += lineTotal;
            html += `
                <div class="flex items-center justify-between p-3 rounded-2xl bg-white soft-border shadow-xs">
                    <div class="flex-1 pr-2">
                        <h5 class="text-xs font-bold text-forest">${i.name}</h5>
                        <div class="text-[11px] text-forest/60">₹${i.price} &times; ${i.qty} = <strong>₹${lineTotal}</strong></div>
                    </div>
                    <div class="flex items-center gap-1.5 bg-paper soft-border rounded-xl p-1">
                        <button onclick="updateCartQty(${i.id}, -1)" class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-xs font-bold text-forest hover:bg-forest hover:text-paper transition">-</button>
                        <span class="text-xs font-bold px-1 text-forest">${i.qty}</span>
                        <button onclick="updateCartQty(${i.id}, 1)" class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-xs font-bold text-forest hover:bg-forest hover:text-paper transition">+</button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        const tax = Math.round(subtotal * 0.05);
        const grandTotal = subtotal + tax;

        document.getElementById('cart-subtotal').textContent = '₹' + subtotal.toLocaleString();
        document.getElementById('cart-tax').textContent = '₹' + tax.toLocaleString();
        document.getElementById('cart-total').textContent = '₹' + grandTotal.toLocaleString();
        if (window.lucide) lucide.createIcons();
    }

    function openCartDrawer() {
        document.getElementById('cart-drawer').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        updateCartUI();
    }

    function closeCartDrawer() {
        document.getElementById('cart-drawer').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('cart-drawer')?.addEventListener('click', (e) => {
        if (e.target.id === 'cart-drawer') closeCartDrawer();
    });

    async function submitDiningOrder() {
        if (diningCart.length === 0) return;

        const name = document.getElementById('order-guest-name').value.trim();
        const phone = document.getElementById('order-guest-phone').value.trim();
        const orderType = document.getElementById('order-type').value;
        const tableNum = document.getElementById('order-table-num').value.trim();
        const instructions = document.getElementById('order-instructions').value.trim();

        if (!name || !phone) {
            alert('Please enter your name and contact phone number.');
            return;
        }

        const btn = document.getElementById('submit-order-btn');
        btn.disabled = true;
        btn.innerHTML = `<span>Transmitting to Kitchen...</span>`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const res = await fetch('{{ route("dining.order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    customer_name: name,
                    customer_phone: phone,
                    order_type: orderType,
                    table_number: tableNum,
                    instructions: instructions,
                    items: diningCart.map(i => ({ id: i.id, quantity: i.qty }))
                })
            });

            const data = await res.json();
            if (data.require_login) {
                alert(data.message || 'Please sign in to place your dining order. Your tray has been preserved.');
                window.location.href = data.login_url || '{{ route("login") }}';
                return;
            }

            if (res.ok && data.success) {
                diningCart = [];
                saveCart();
                closeCartDrawer();
                availabilityToast(data.message || 'Order received by the kitchen!');
            } else {
                alert(data.message || 'Failed to submit dining order. Please check inputs.');
            }
        } catch (err) {
            alert('An unexpected error occurred while placing your order. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<span>Transmit Order to Kitchen</span><i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>`;
            if (window.lucide) lucide.createIcons();
        }
    }

    document.addEventListener('DOMContentLoaded', updateCartUI);
</script>
@endpush
@endsection
