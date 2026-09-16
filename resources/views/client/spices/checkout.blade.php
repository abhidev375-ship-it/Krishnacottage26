@extends('layouts.customer')

@section('title', 'Checkout | Krishna Spices')

@section('content')
<div class="mx-auto max-w-[1280px] px-4 sm:px-6 py-5 sm:py-8">

    <div class="flex items-center gap-4 mb-5 sm:mb-8">
        <a href="{{ route('spices.index') }}" class="w-10 h-10 rounded-2xl bg-white soft-border flex items-center justify-center hover:bg-forest hover:text-paper transition shadow-xs">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </a>
        <div>
            <span class="eyebrow text-brass">Secure Checkout</span>
            <h1 class="serif text-2xl sm:text-3xl font-bold text-forest mt-0.5">Complete Spice Order</h1>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
        {{ session('error') }}
    </div>
    @endif

    <div id="spice-payment-error-box" class="hidden mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
        <span id="spice-payment-error-msg"></span>
    </div>

    <form action="{{ route('spices.order') }}" method="POST" id="spice-checkout-form" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        @csrf
        <input type="hidden" name="items_json" id="input-items-json">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">

        <!-- LEFT COLUMN: SHIPPING & PAYMENT (7 COLS) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- 1. CONTACT INFO -->
            <div class="bg-white rounded-[24px] soft-border p-6 shadow-card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="serif text-xl font-bold text-forest">1. Contact Details</h2>
                    <span class="text-[10px] bg-paper px-2.5 py-1 rounded-lg text-forest/60 font-semibold">Account: {{ $user->email }}</span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Full Name *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $user->name) }}" required placeholder="e.g. Radhika Sharma" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Email Address *</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $user->email) }}" required placeholder="radhika@example.com" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Phone Number (for dispatch SMS &amp; courier) *</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone', $user->phone ?? ($guest->phone ?? '')) }}" required placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- 2. DELIVERY DESTINATION & MODE -->
            <div class="bg-white rounded-[24px] soft-border p-6 shadow-card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="serif text-xl font-bold text-forest">2. Delivery Method</h2>
                    @if($activeStay)
                    <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded-full border border-emerald-200 uppercase">
                        In-House Stay Active
                    </span>
                    @endif
                </div>

                @if($activeStay)
                <!-- DELIVERY MODE SWITCHER FOR CHECKED-IN CLIENTS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <label class="p-4 rounded-2xl border-2 transition cursor-pointer flex flex-col justify-between" id="label-mode-villa">
                        <div class="flex items-center gap-2.5 mb-2">
                            <input type="radio" name="delivery_mode" value="villa" checked onchange="toggleDeliveryMode('villa')" class="text-forest focus:ring-forest">
                            <span class="text-xs font-bold text-forest">Deliver to My Villa</span>
                        </div>
                        <p class="text-[11px] text-forest/65">
                            Hand-delivered directly to Villa {{ $activeStay->room ? $activeStay->room->room_number : 'In-House' }} at {{ $activeStay->branch ? $activeStay->branch->name : 'Cottage' }}.
                        </p>
                        <div class="mt-2 text-[10px] text-emerald font-bold">✓ FREE Cottage Delivery</div>
                    </label>

                    <label class="p-4 rounded-2xl border-2 transition cursor-pointer flex flex-col justify-between" id="label-mode-courier">
                        <div class="flex items-center gap-2.5 mb-2">
                            <input type="radio" name="delivery_mode" value="courier" onchange="toggleDeliveryMode('courier')" class="text-forest focus:ring-forest">
                            <span class="text-xs font-bold text-forest">Ship to Home Address</span>
                        </div>
                        <p class="text-[11px] text-forest/65">
                            Packaged and dispatched via insured courier anywhere in India.
                        </p>
                        <div class="mt-2 text-[10px] text-forest/60 font-medium">Free above ₹999 / ₹80</div>
                    </label>
                </div>

                <div id="villa-room-input-container" class="p-4 rounded-2xl bg-mint/50 border border-emerald/15 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-emerald tracking-wider block">Assigned Delivery Unit</span>
                        <span class="font-bold text-forest text-sm">Villa {{ $activeStay->room ? $activeStay->room->room_number : 'In-House Suite' }} &middot; {{ $activeStay->branch ? $activeStay->branch->name : 'Krishna Cottages' }}</span>
                    </div>
                    <input type="hidden" name="room_number" value="{{ $activeStay->room ? $activeStay->room->room_number : 'In-House Suite' }}">
                    <span class="text-[10px] bg-white px-2 py-1 rounded-md text-forest font-mono">Room Folio Ready</span>
                </div>
                @else
                <input type="hidden" name="delivery_mode" value="courier">
                <div class="p-3 bg-paper/60 rounded-xl soft-border text-xs text-forest/70 flex items-center gap-2">
                    <i data-lucide="truck" class="w-4 h-4 text-emerald shrink-0"></i>
                    <span>Plantation orders are hand-packed and dispatched via express courier anywhere across India.</span>
                </div>
                @endif

                <!-- ADDRESS FIELDS (SHOWN FOR COURIER) -->
                <div id="courier-address-fields" class="space-y-3 {{ $activeStay ? 'hidden' : '' }}">
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Street Address Line 1 *</label>
                        <input type="text" id="field-address1" name="shipping_address_line1" value="{{ old('shipping_address_line1', $guest->address ?? '') }}" placeholder="Apartment, building, house number" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Street Address Line 2 (Optional)</label>
                        <input type="text" name="shipping_address_line2" value="{{ old('shipping_address_line2') }}" placeholder="Colony, landmark, area" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">City *</label>
                            <input type="text" id="field-city" name="shipping_city" value="{{ old('shipping_city', $guest->city ?? '') }}" placeholder="e.g. Kochi, Mumbai" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">State *</label>
                            <input type="text" id="field-state" name="shipping_state" value="{{ old('shipping_state', 'Kerala') }}" placeholder="e.g. Kerala, Maharashtra" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Pincode *</label>
                            <input type="text" id="field-pincode" name="shipping_pincode" value="{{ old('shipping_pincode', $guest->pincode ?? '') }}" placeholder="e.g. 682001" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. PAYMENT METHOD -->
            <div class="bg-white rounded-[24px] soft-border p-6 shadow-card space-y-3">
                <h2 class="serif text-xl font-bold text-forest">3. Payment Option</h2>

                <div class="space-y-2.5">
                    @if($activeStay)
                    <label id="payment-option-folio" class="flex items-center justify-between p-3.5 rounded-2xl soft-border hover:border-forest/40 cursor-pointer transition" onclick="toggleSpicePaymentRadio('charge_to_room')">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="charge_to_room" checked class="text-forest focus:ring-forest" onchange="toggleSpicePaymentRadio('charge_to_room')">
                            <div>
                                <span class="text-xs font-bold text-forest block">Charge to Room Folio</span>
                                <span class="text-[10px] text-forest/50">Settled during cottage checkout</span>
                            </div>
                        </div>
                        <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded-lg border border-emerald/20">In-Villa Guest</span>
                    </label>
                    @endif

                    <!-- UPI MAIN ACCORDION WITH PHONEPE, GPAY, PAYTM -->
                    <div class="rounded-2xl {{ !$activeStay ? 'border-2 border-forest bg-mint/20' : 'soft-border' }} overflow-hidden transition" id="box-spice-upi-option">
                        <label class="flex items-center justify-between p-3.5 cursor-pointer" onclick="toggleSpicePaymentRadio('upi')">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="upi" {{ !$activeStay ? 'checked' : '' }} class="text-forest focus:ring-forest" onchange="toggleSpicePaymentRadio('upi')">
                                <div>
                                    <div class="text-xs font-bold text-forest flex items-center gap-1.5">
                                        <span>Instant UPI (GPay / PhonePe / Paytm / QR)</span>
                                        <span class="text-[9px] bg-mint text-emerald font-bold px-2 py-0.5 rounded-md border border-emerald/20">Zero Surcharge</span>
                                    </div>
                                    <div class="text-[11px] text-forest/60 mt-0.5">Pay with PhonePe, Google Pay, Paytm, or scan QR</div>
                                </div>
                            </div>
                            <div class="hidden sm:flex items-center gap-1.5 shrink-0">
                                <span class="px-2 py-1 rounded-md bg-white soft-border text-[10px] font-bold text-forest flex items-center gap-1 shadow-xs">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.16 0 9.94 0 12s.46 3.84 1.26 5.42l4.02-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
                                    GPay
                                </span>
                                <span class="px-2 py-1 rounded-md bg-[#5F259F] text-white text-[10px] font-bold shadow-xs">
                                    PhonePe
                                </span>
                                <span class="px-2 py-1 rounded-md bg-[#002970] text-[#00BAF2] text-[10px] font-black shadow-xs">
                                    Paytm
                                </span>
                            </div>
                        </label>

                        <!-- EXPANDED UPI APPS GRID -->
                        <div id="spice-upi-apps-panel" class="px-3.5 pb-3.5 pt-1 border-t border-forest/10 space-y-2.5 {{ $activeStay ? 'hidden' : '' }}">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-forest/60">Choose your preferred UPI method:</div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <!-- Google Pay -->
                                <div onclick="selectSpiceUpiApp('gpay')" id="spice-upi-card-gpay" class="spice-upi-app-badge p-2.5 rounded-xl border-2 border-forest bg-mint/40 cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-xs">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.16 0 9.94 0 12s.46 3.84 1.26 5.42l4.02-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
                                    </div>
                                    <div class="text-[11px] font-bold text-forest">Google Pay</div>
                                    <div class="text-[9px] text-forest/50">GPay Direct</div>
                                </div>

                                <!-- PhonePe -->
                                <div onclick="selectSpiceUpiApp('phonepe')" id="spice-upi-card-phonepe" class="spice-upi-app-badge p-2.5 rounded-xl border-2 border-forest/15 hover:border-forest/40 bg-white cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs">
                                    <div class="w-8 h-8 rounded-full bg-[#5F259F] flex items-center justify-center text-white font-black text-sm shadow-xs">
                                        पे
                                    </div>
                                    <div class="text-[11px] font-bold text-forest">PhonePe</div>
                                    <div class="text-[9px] text-forest/50">Instant App</div>
                                </div>

                                <!-- Paytm -->
                                <div onclick="selectSpiceUpiApp('paytm')" id="spice-upi-card-paytm" class="spice-upi-app-badge p-2.5 rounded-xl border-2 border-forest/15 hover:border-forest/40 bg-white cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs">
                                    <div class="h-8 px-2 rounded-lg bg-[#002970] flex items-center justify-center shadow-xs">
                                        <span class="text-[10px] font-black text-[#00BAF2] tracking-tighter">Paytm</span>
                                    </div>
                                    <div class="text-[11px] font-bold text-forest">Paytm UPI</div>
                                    <div class="text-[9px] text-forest/50">Wallet / UPI</div>
                                </div>

                                <!-- QR Code / Any UPI -->
                                <div onclick="selectSpiceUpiApp('qr')" id="spice-upi-card-qr" class="spice-upi-app-badge p-2.5 rounded-xl border-2 border-forest/15 hover:border-forest/40 bg-white cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-800 shadow-xs">
                                        <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    </div>
                                    <div class="text-[11px] font-bold text-forest">Scan QR / Any</div>
                                    <div class="text-[9px] text-forest/50">BHIM / CRED / All</div>
                                </div>
                            </div>
                            <input type="hidden" name="selected_upi_app" id="selected_spice_upi_app" value="gpay">

                            <!-- DYNAMIC UPI INSTRUCTIONS & TEST MODE GUIDE -->
                            <div id="spice-upi-instruction-card" class="mt-3 p-3.5 rounded-xl bg-forest/5 border border-forest/15 space-y-2.5">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-6 h-6 rounded-full bg-forest text-paper flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 shadow-xs">
                                        <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <div id="spice-upi-guide-title" class="text-xs font-bold text-forest flex items-center gap-1.5">
                                            <span>Google Pay (GPay) Selected</span>
                                            <span class="text-[9px] bg-emerald/10 text-emerald font-bold px-1.5 py-0.5 rounded">Instant</span>
                                        </div>
                                        <div id="spice-upi-guide-desc" class="text-[11px] text-forest/75 leading-relaxed">
                                            <strong>📱 Mobile Phones:</strong> Directly launches Google Pay app via UPI Intent.
                                            <br>
                                            <strong>💻 Live Production (<code class="text-forest font-semibold">rzp_live_...</code>):</strong> Displays Dynamic QR code to scan with phone.
                                        </div>
                                    </div>
                                </div>

                                <!-- IMPORTANT TEST MODE NOTICE -->
                                <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200/90 text-amber-900 text-[11px] space-y-1.5">
                                    <div class="flex items-center gap-1.5 font-bold text-amber-950">
                                        <i data-lucide="flask-conical" class="w-4 h-4 text-amber-700 shrink-0"></i>
                                        <span>Why no QR code appears in Test Mode:</span>
                                    </div>
                                    <p class="text-[11px] leading-relaxed text-amber-900/90">
                                        Razorpay intentionally disables QR codes in <strong>Test Mode</strong> (<code class="bg-white/80 px-1 py-0.5 rounded border border-amber-300 font-mono text-[10px]">rzp_test_...</code>) because banks cannot generate simulated QR codes.
                                    </p>
                                    <div class="bg-white/90 p-2.5 rounded-md border border-amber-200 text-[11px] text-forest space-y-1">
                                        <span class="font-bold text-emerald block">🧪 How to test right now:</span>
                                        <div class="text-[10px] text-forest/80">
                                            When the Razorpay popup opens, we have automatically pre-filled <strong class="text-forest font-mono">success@razorpay</strong> for you. Simply click the green <strong>"Pay"</strong> or <strong>"Success"</strong> button to simulate a successful payment!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <label class="flex items-center justify-between p-3.5 rounded-2xl soft-border hover:border-forest/40 cursor-pointer transition" onclick="toggleSpicePaymentRadio('card')">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="card" class="text-forest focus:ring-forest" onchange="toggleSpicePaymentRadio('card')">
                            <span class="text-xs font-bold text-forest">Credit / Debit Card (Visa / MasterCard / RuPay)</span>
                        </div>
                        <span class="text-[10px] text-forest/40">Secure Gateway</span>
                    </label>

                    <label class="flex items-center justify-between p-3.5 rounded-2xl soft-border hover:border-forest/40 cursor-pointer transition" onclick="toggleSpicePaymentRadio('cod')">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="cod" class="text-forest focus:ring-forest" onchange="toggleSpicePaymentRadio('cod')">
                            <span class="text-xs font-bold text-forest">Cash on Delivery (COD)</span>
                        </div>
                        <span class="text-[10px] text-forest/50">Upon Arrival</span>
                    </label>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <button type="submit" id="submit-spice-btn" class="w-full py-4 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs flex items-center justify-center gap-2 shadow-card transition touch-tap">
                <span id="spice-btn-text">Confirm &amp; Place Spice Order</span>
                <span id="spice-btn-spinner" class="hidden animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                <i id="spice-btn-icon" data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-brass"></i>
            </button>
        </div>

        <!-- RIGHT COLUMN: ORDER SUMMARY (5 COLS) -->
        <div class="lg:col-span-5">
            <div class="sticky top-24 bg-white rounded-[26px] soft-border p-6 shadow-card space-y-5">
                <div class="flex items-center justify-between border-b border-forest/10 pb-4">
                    <h3 class="serif text-xl font-bold text-forest">Order Summary</h3>
                    <span id="summary-items-count" class="text-xs text-forest/50 font-mono">0 Items</span>
                </div>

                <div id="checkout-items-list" class="space-y-3 max-h-64 overflow-y-auto">
                    <!-- Populated via JS -->
                </div>

                <div class="pt-4 border-t border-forest/10 space-y-2 text-xs text-forest/70">
                    <div class="flex justify-between">
                        <span>Items Subtotal:</span>
                        <span id="summary-subtotal" class="font-bold text-forest">₹0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Estate Delivery / Shipping:</span>
                        <span id="summary-shipping" class="font-bold text-emerald">₹0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Applicable GST (5% Single Estate):</span>
                        <span id="summary-tax" class="font-bold text-forest">₹0</span>
                    </div>
                    <div class="pt-3 border-t border-forest/10 flex justify-between font-bold text-sm text-forest">
                        <span>Total Amount:</span>
                        <span id="summary-total">₹0</span>
                    </div>
                </div>

                <div class="bg-mint p-3 rounded-2xl border border-emerald/15 flex items-center gap-2.5 text-xs text-forest">
                    <i data-lucide="package-check" class="w-4 h-4 text-emerald shrink-0"></i>
                    <span>Vacuum sealed for harvest freshness &amp; direct plantation tracking.</span>
                </div>

                @if($globalReturnsEnabled ?? true)
                <div class="bg-paper p-3 rounded-2xl border border-forest/10 flex items-start gap-2.5 text-[11px] text-forest/70">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald shrink-0 mt-0.5"></i>
                    <span><strong>100% Pre-Dispatch Return Guarantee:</strong> All orders are packed fresh upon order. Cancel anytime before dispatch from your dashboard for a 100% immediate cashback refund.</span>
                </div>
                @else
                <div class="bg-amber-50 p-3 rounded-2xl border border-amber-200 flex items-start gap-2.5 text-[11px] text-amber-900">
                    <i data-lucide="info" class="w-4 h-4 text-amber-700 shrink-0 mt-0.5"></i>
                    <span><strong>Policy Notice:</strong> This product does not have return policies.</span>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    let currentDeliveryMode = '{{ $activeStay ? "villa" : "courier" }}';

    function toggleDeliveryMode(mode) {
        currentDeliveryMode = mode;
        const villaBox = document.getElementById('villa-room-input-container');
        const courierBox = document.getElementById('courier-address-fields');
        const folioOpt = document.getElementById('payment-option-folio');
        const lblVilla = document.getElementById('label-mode-villa');
        const lblCourier = document.getElementById('label-mode-courier');

        const addr1 = document.getElementById('field-address1');
        const city = document.getElementById('field-city');
        const pincode = document.getElementById('field-pincode');

        if (mode === 'villa') {
            if (villaBox) villaBox.classList.remove('hidden');
            if (courierBox) courierBox.classList.add('hidden');
            if (folioOpt) folioOpt.classList.remove('hidden');
            if (lblVilla) {
                lblVilla.className = 'p-4 rounded-2xl border-2 border-forest bg-mint/30 transition cursor-pointer flex flex-col justify-between';
            }
            if (lblCourier) {
                lblCourier.className = 'p-4 rounded-2xl border-2 border-forest/10 bg-white transition cursor-pointer flex flex-col justify-between';
            }
            if (addr1) addr1.required = false;
            if (city) city.required = false;
            if (pincode) pincode.required = false;
        } else {
            if (villaBox) villaBox.classList.add('hidden');
            if (courierBox) courierBox.classList.remove('hidden');
            if (folioOpt) folioOpt.classList.add('hidden');
            if (lblCourier) {
                lblCourier.className = 'p-4 rounded-2xl border-2 border-forest bg-mint/30 transition cursor-pointer flex flex-col justify-between';
            }
            if (lblVilla) {
                lblVilla.className = 'p-4 rounded-2xl border-2 border-forest/10 bg-white transition cursor-pointer flex flex-col justify-between';
            }
            if (addr1) addr1.required = true;
            if (city) city.required = true;
            if (pincode) pincode.required = true;

            // If folio was checked, switch to upi
            const activePay = document.querySelector('input[name="payment_method"]:checked');
            if (activePay && activePay.value === 'charge_to_room') {
                const upiRadio = document.querySelector('input[name="payment_method"][value="upi"]');
                if (upiRadio) upiRadio.checked = true;
            }
        }

        recalculateBill();
    }

    function recalculateBill() {
        const cart = JSON.parse(localStorage.getItem('krishna_spices_cart')) || [];
        const container = document.getElementById('checkout-items-list');
        const jsonInput = document.getElementById('input-items-json');
        const submitBtn = document.getElementById('submit-spice-btn');

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-6 text-forest/50">
                    <p class="text-xs">Your cart is empty.</p>
                    <a href="{{ route('spices.index') }}" class="inline-block mt-3 px-4 py-1.5 rounded-xl bg-forest text-paper text-xs font-bold">Return to Shop</a>
                </div>
            `;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            return;
        }

        if (jsonInput) jsonInput.value = JSON.stringify(cart);

        let subtotal = 0;
        let totalTax = 0;
        let html = '';

        cart.forEach(i => {
            const isLoose = (i.type === 'loose');
            const line = isLoose 
                ? Math.round(i.price * i.weight_kg * 100) / 100 
                : Math.round(i.price * i.qty * 100) / 100;
            
            const taxRate = i.tax_rate || 5.0;
            const lineTax = Math.round(line * (taxRate / 100) * 100) / 100;

            subtotal += line;
            totalTax += lineTax;

            html += `
                <div class="flex items-center justify-between text-xs py-2 border-b border-forest/5">
                    <div class="min-w-0 pr-2">
                        <div class="font-bold text-forest truncate">${i.name}</div>
                        <div class="text-[11px] text-forest/50">
                            ${isLoose ? `${i.weight_kg.toFixed(2)} kg loose @ ₹${i.price.toLocaleString('en-IN')}/kg` : `${i.size} &times; ${i.qty}`}
                        </div>
                    </div>
                    <span class="font-bold text-forest shrink-0">₹${line.toLocaleString('en-IN')}</span>
                </div>
            `;
        });

        container.innerHTML = html;
        document.getElementById('summary-items-count').textContent = cart.length + ' Item' + (cart.length > 1 ? 's' : '');

        // Shipping: in-villa is free; courier is free above 999, else 80
        const shipping = (currentDeliveryMode === 'villa') ? 0 : ((subtotal >= 999) ? 0 : 80);
        const grand = Math.round(subtotal + shipping + totalTax);

        document.getElementById('summary-subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN');
        document.getElementById('summary-shipping').textContent = (currentDeliveryMode === 'villa') ? 'FREE (In-Villa)' : (shipping === 0 ? 'FREE' : '₹' + shipping);
        document.getElementById('summary-tax').textContent = '₹' + totalTax.toLocaleString('en-IN');
        document.getElementById('summary-total').textContent = '₹' + grand.toLocaleString('en-IN');
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleDeliveryMode(currentDeliveryMode);

        const form = document.getElementById('spice-checkout-form');
        const submitBtn = document.getElementById('submit-spice-btn');
        const btnText = document.getElementById('spice-btn-text');
        const btnSpinner = document.getElementById('spice-btn-spinner');
        const btnIcon = document.getElementById('spice-btn-icon');
        const errorBox = document.getElementById('spice-payment-error-box');
        const errorMsg = document.getElementById('spice-payment-error-msg');

        function showError(message) {
            if (errorBox && errorMsg) {
                errorMsg.textContent = message;
                errorBox.classList.remove('hidden');
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alert(message);
            }
        }

        function clearError() {
            if (errorBox) errorBox.classList.add('hidden');
        }

        function setLoading(isLoading) {
            if (!submitBtn) return;
            submitBtn.disabled = isLoading;
            if (isLoading) {
                btnText.textContent = 'Processing Payment...';
                if (btnSpinner) btnSpinner.classList.remove('hidden');
                if (btnIcon) btnIcon.classList.add('hidden');
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            } else {
                btnText.textContent = 'Confirm & Place Spice Order';
                if (btnSpinner) btnSpinner.classList.add('hidden');
                if (btnIcon) btnIcon.classList.remove('hidden');
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }

        window.toggleSpicePaymentRadio = function(method) {
            const radio = document.querySelector(`input[name="payment_method"][value="${method}"]`);
            if (radio) radio.checked = true;

            const upiBox = document.getElementById('box-spice-upi-option');
            const upiPanel = document.getElementById('spice-upi-apps-panel');

            if (method === 'upi') {
                if (upiBox) {
                    upiBox.className = 'rounded-2xl border-2 border-forest bg-mint/20 overflow-hidden transition';
                }
                if (upiPanel) upiPanel.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Confirm & Pay via UPI';
            } else {
                if (upiBox) {
                    upiBox.className = 'rounded-2xl soft-border overflow-hidden transition';
                }
                if (upiPanel) upiPanel.classList.add('hidden');
                if (btnText) {
                    if (method === 'cod') {
                        btnText.textContent = 'Place Order (Cash on Delivery)';
                    } else if (method === 'charge_to_room') {
                        btnText.textContent = 'Charge to Room Folio';
                    } else if (method === 'card') {
                        btnText.textContent = 'Pay with Card';
                    } else {
                        btnText.textContent = 'Confirm & Place Spice Order';
                    }
                }
            }
        };

        window.selectSpiceUpiApp = function(app) {
            const hiddenAppInput = document.getElementById('selected_spice_upi_app');
            if (hiddenAppInput) hiddenAppInput.value = app;
            document.querySelectorAll('.spice-upi-app-badge').forEach(el => {
                el.className = 'spice-upi-app-badge p-2.5 rounded-xl border-2 border-forest/15 hover:border-forest/40 bg-white cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs';
            });
            const activeCard = document.getElementById(`spice-upi-card-${app}`);
            if (activeCard) {
                activeCard.className = 'spice-upi-app-badge p-2.5 rounded-xl border-2 border-forest bg-mint/40 cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs';
            }

            const guideTitle = document.getElementById('spice-upi-guide-title');
            const guideDesc = document.getElementById('spice-upi-guide-desc');
            if (guideTitle && guideDesc) {
                if (app === 'gpay') {
                    guideTitle.innerHTML = '<span>Google Pay (GPay) Selected</span><span class="text-[9px] bg-emerald/10 text-emerald font-bold px-1.5 py-0.5 rounded">Instant</span>';
                    guideDesc.innerHTML = '<strong>📱 Mobile Phones:</strong> Directly launches Google Pay app via UPI Intent.<br><strong>💻 Live Production (<code class="text-forest font-semibold">rzp_live_...</code>):</strong> Displays Dynamic QR code to scan with phone.';
                } else if (app === 'phonepe') {
                    guideTitle.innerHTML = '<span>PhonePe UPI Selected</span><span class="text-[9px] bg-[#5F259F]/10 text-[#5F259F] font-bold px-1.5 py-0.5 rounded">Instant</span>';
                    guideDesc.innerHTML = '<strong>📱 Mobile Phones:</strong> Directly launches PhonePe app via UPI Intent.<br><strong>💻 Live Production (<code class="text-forest font-semibold">rzp_live_...</code>):</strong> Displays Dynamic QR code to scan with phone.';
                } else if (app === 'paytm') {
                    guideTitle.innerHTML = '<span>Paytm UPI Selected</span><span class="text-[9px] bg-[#002970]/10 text-[#002970] font-bold px-1.5 py-0.5 rounded">Instant</span>';
                    guideDesc.innerHTML = '<strong>📱 Mobile Phones:</strong> Directly launches Paytm app via UPI Intent.<br><strong>💻 Live Production (<code class="text-forest font-semibold">rzp_live_...</code>):</strong> Displays Dynamic QR code to scan with phone.';
                } else {
                    guideTitle.innerHTML = '<span>Scan QR / Any UPI App</span><span class="text-[9px] bg-forest/10 text-forest font-bold px-1.5 py-0.5 rounded">All Apps</span>';
                    guideDesc.innerHTML = '<strong>Scan & Pay (Live Mode):</strong> Works with Google Pay, PhonePe, Paytm, BHIM, CRED, Amazon Pay.';
                }
            }
            if (window.lucide) lucide.createIcons();
            window.toggleSpicePaymentRadio('upi');
        };

        if (form) {
            form.addEventListener('submit', async (e) => {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const cart = JSON.parse(localStorage.getItem('krishna_spices_cart')) || [];
                if (cart.length === 0) {
                    e.preventDefault();
                    showError('Your shopping cart is empty. Please add spices before checking out.');
                    return;
                }

                const paymentMethod = form.querySelector('input[name="payment_method"]:checked')?.value || 'upi';

                // Offline payment methods: COD or Charge to Room Folio
                if (paymentMethod === 'cod' || paymentMethod === 'charge_to_room') {
                    localStorage.removeItem('krishna_spices_cart');
                    return;
                }

                // If payment tokens already set, allow standard submission
                if (document.getElementById('razorpay_payment_id').value) {
                    localStorage.removeItem('krishna_spices_cart');
                    return;
                }

                // Online payment via Razorpay
                e.preventDefault();
                clearError();
                setLoading(true);

                try {
                    const formData = new FormData(form);
                    const payload = {
                        customer_name: formData.get('customer_name'),
                        customer_email: formData.get('customer_email'),
                        customer_phone: formData.get('customer_phone'),
                        delivery_mode: currentDeliveryMode,
                        items_json: JSON.stringify(cart)
                    };

                    const res = await fetch('{{ route("spices.razorpay.create-order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        showError(data.message || 'Unable to initialize online payment. Please try Cash on Delivery or retry.');
                        setLoading(false);
                        return;
                    }

                    const prefillData = Object.assign({}, data.prefill || {});
                    if (paymentMethod === 'upi') {
                        prefillData.method = 'upi';
                        if (data.key_id && data.key_id.startsWith('rzp_test_')) {
                            prefillData.vpa = 'success@razorpay';
                        }
                    }

                    const options = {
                        key: data.key_id,
                        amount: data.amount,
                        currency: data.currency || 'INR',
                        name: data.name || 'Krishna Spices',
                        description: data.description || 'Harvest Spice Order',
                        order_id: data.order_id,
                        prefill: prefillData,
                        theme: data.theme || { color: '#063F34' },
                        handler: function (response) {
                            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                            document.getElementById('razorpay_signature').value = response.razorpay_signature;
                            localStorage.removeItem('krishna_spices_cart');
                            btnText.textContent = 'Finalizing Order...';
                            form.submit();
                        },
                        modal: {
                            ondismiss: function () {
                                setLoading(false);
                            }
                        }
                    };

                    const rzp = new Razorpay(options);
                    rzp.on('payment.failed', function (response) {
                        showError(response.error.description || 'Payment was unsuccessful or cancelled. Please try again.');
                        setLoading(false);
                    });
                    rzp.open();
                } catch (err) {
                    console.error('Razorpay initialization error:', err);
                    showError('A network error occurred while connecting to payment gateway. Please retry.');
                    setLoading(false);
                }
            });
        }
    });
</script>
@endpush
@endsection
