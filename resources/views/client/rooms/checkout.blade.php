@extends('layouts.customer')

@section('title', 'Confirm and Book | ' . $roomType->name)

@section('content')
<div class="mx-auto max-w-[1280px] px-4 sm:px-6 py-5 sm:py-8">

    <!-- CHECKOUT HEADER WITH BACK BUTTON -->
    <div class="flex items-center gap-4 mb-5 sm:mb-8">
        <a href="{{ route('rooms.show', $roomType->slug) }}" class="w-10 h-10 rounded-2xl bg-white soft-border flex items-center justify-center hover:bg-forest hover:text-paper transition shadow-xs">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </a>
        <div>
            <span class="eyebrow text-brass">Reservation Step 2 of 2</span>
            <h1 class="serif text-2xl sm:text-3xl font-bold text-forest mt-0.5">Confirm and Pay</h1>
        </div>
    </div>

    <!-- 2-COLUMN AIRBNB CHECKOUT GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

        <!-- LEFT COLUMN: TRIP REVIEW, GUEST FORM, PAYMENT METHODS (7 COLS) -->
        <div class="lg:col-span-7 space-y-8">

            @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <div id="payment-error-box" class="hidden p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                <span id="payment-error-msg"></span>
            </div>

            <form action="{{ route('booking.store') }}" method="POST" id="checkout-form" class="space-y-8">
                @csrf
                <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                <input type="hidden" name="check_in_date" value="{{ $checkIn }}">
                <input type="hidden" name="check_out_date" value="{{ $checkOut }}">
                <input type="hidden" name="adults" value="{{ $adults }}">
                <input type="hidden" name="children" value="{{ $children }}">
                <input type="hidden" name="rooms_count" value="{{ $roomsCount ?? 1 }}">
                @if(!empty($childAges))
                    @foreach($childAges as $age)
                        <input type="hidden" name="child_ages[]" value="{{ $age }}">
                    @endforeach
                @endif
                
                <!-- Razorpay Transaction Tokens -->
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                <!-- 1. YOUR TRIP REVIEW -->
                <div class="bg-white rounded-[24px] soft-border p-6 shadow-card space-y-4">
                    <h2 class="serif text-xl font-bold text-forest">Your Stay Summary</h2>
                    <div class="flex items-center justify-between border-t border-forest/10 pt-3">
                        <div>
                            <div class="text-xs font-bold text-forest">Dates</div>
                            <div class="text-xs text-forest/60 mt-0.5">
                                {{ date('D, M d, Y', strtotime($checkIn)) }} &ndash; {{ date('D, M d, Y', strtotime($checkOut)) }} ({{ $nights }} night{{ $nights > 1 ? 's' : '' }})
                            </div>
                        </div>
                        <a href="{{ route('rooms.show', $roomType->slug) }}" class="text-xs font-semibold text-emerald hover:underline">Edit</a>
                    </div>
                    <div class="flex items-center justify-between border-t border-forest/5 pt-3">
                        <div>
                            <div class="text-xs font-bold text-forest">Rooms &amp; Guests</div>
                            <div class="text-xs text-forest/60 mt-0.5">
                                {{ $roomsCount ?? 1 }} room{{ ($roomsCount ?? 1) > 1 ? 's' : '' }} &middot; {{ $adults }} adult{{ $adults > 1 ? 's' : '' }}{{ $children > 0 ? ', ' . $children . ' children' : '' }}
                                @if(!empty($party['reclassified_adults']) && $party['reclassified_adults'] > 0)
                                    <span class="text-[10px] text-amber-700 block font-medium">({{ $party['reclassified_adults'] }} child &gt; 12 accommodated as adult)</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('rooms.show', $roomType->slug) }}" class="text-xs font-semibold text-emerald hover:underline">Edit</a>
                    </div>
                </div>

                <!-- 2. CHOOSE HOW TO PAY -->
                <div class="bg-white rounded-[24px] soft-border p-6 shadow-card space-y-4">
                    <h2 class="serif text-xl font-bold text-forest">Choose how to pay</h2>

                    <div class="space-y-3">
                        <label id="choice-card-full" class="flex items-start justify-between p-4 rounded-2xl border-2 border-forest bg-mint/50 cursor-pointer transition shadow-xs" onclick="updatePaymentChoice('full')">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="payment_choice" value="full" {{ old('payment_choice', 'full') === 'full' ? 'checked' : '' }} class="mt-1 text-forest focus:ring-forest" onchange="updatePaymentChoice('full')">
                                <div>
                                    <div class="text-xs font-bold text-forest">Pay in full</div>
                                    <div class="text-[11px] text-forest/60 mt-0.5">Pay the total ₹{{ number_format($total) }} now and enjoy a seamless check-in.</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-forest">₹{{ number_format($total) }}</span>
                        </label>

                        <label id="choice-card-deposit_20" class="flex items-start justify-between p-4 rounded-2xl soft-border hover:border-forest/40 bg-white cursor-pointer transition" onclick="updatePaymentChoice('deposit_20')">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="payment_choice" value="deposit_20" {{ old('payment_choice') === 'deposit_20' ? 'checked' : '' }} class="mt-1 text-forest focus:ring-forest" onchange="updatePaymentChoice('deposit_20')">
                                <div>
                                    <div class="text-xs font-bold text-forest flex items-center gap-1.5">
                                        <span>Pay 20% deposit now</span>
                                        <span class="text-[9px] bg-emerald/10 text-emerald font-bold px-2 py-0.5 rounded-md border border-emerald/20">Reserve Now</span>
                                    </div>
                                    <div class="text-[11px] text-forest/60 mt-0.5">Pay ₹{{ number_format($deposit) }} today, and the rest (₹{{ number_format($total - $deposit) }}) upon arrival at the cottages.</div>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-forest">₹{{ number_format($deposit) }}</span>
                        </label>
                    </div>
                </div>

                <!-- 3. GUEST INFORMATION -->
                <div class="bg-white rounded-[24px] soft-border p-6 shadow-card space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="serif text-xl font-bold text-forest">Guest Information</h2>
                        @if(isset($user) || Auth::check())
                            @php $u = $user ?? Auth::user(); @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald/10 border border-emerald/20 text-emerald text-[10px] font-bold">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                <span>Booking as {{ $u->name }}</span>
                            </span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                            $currentUser = $user ?? Auth::user();
                            $nameParts = explode(' ', $currentUser->name ?? '', 2);
                            $prefillFirst = $nameParts[0] ?? '';
                            $prefillLast = $nameParts[1] ?? '';
                            $prefillEmail = $currentUser->email ?? '';
                            $prefillPhone = $currentUser->phone ?? '';
                        @endphp
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">First Name *</label>
                            <input type="text" name="first_name" required value="{{ old('first_name', $prefillFirst) }}" placeholder="e.g. Aditya" class="w-full px-3.5 py-2.5 rounded-xl soft-border text-xs font-semibold text-forest focus:ring-1 focus:ring-forest focus:outline-hidden bg-paper/30">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Last Name *</label>
                            <input type="text" name="last_name" required value="{{ old('last_name', $prefillLast) }}" placeholder="e.g. Nair" class="w-full px-3.5 py-2.5 rounded-xl soft-border text-xs font-semibold text-forest focus:ring-1 focus:ring-forest focus:outline-hidden bg-paper/30">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Email Address (for Stay Pass) *</label>
                            <input type="email" name="email" required value="{{ old('email', $prefillEmail) }}" placeholder="aditya@example.com" class="w-full px-3.5 py-2.5 rounded-xl soft-border text-xs font-semibold text-forest focus:ring-1 focus:ring-forest focus:outline-hidden bg-paper/30">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Phone Number (WhatsApp) *</label>
                            <input type="tel" name="phone" required value="{{ old('phone', $prefillPhone) }}" placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 rounded-xl soft-border text-xs font-semibold text-forest focus:ring-1 focus:ring-forest focus:outline-hidden bg-paper/30">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">City of Residence</label>
                            <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Bengaluru, Mumbai" class="w-full px-3.5 py-2.5 rounded-xl soft-border text-xs font-semibold text-forest focus:ring-1 focus:ring-forest focus:outline-hidden bg-paper/30">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Country</label>
                            <input type="text" name="country" value="{{ old('country', 'India') }}" class="w-full px-3.5 py-2.5 rounded-xl soft-border text-xs font-semibold text-forest focus:ring-1 focus:ring-forest focus:outline-hidden bg-paper/30">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/60 mb-1">Special Requests & Dietary Preferences (Optional)</label>
                        <textarea name="special_requests" rows="2" placeholder="e.g. Anniversary setup, vegan ayurvedic breakfast, late check-in..." class="w-full px-3.5 py-2.5 rounded-xl soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden bg-paper/30"></textarea>
                    </div>
                </div>

                <!-- 4. PAYMENT METHOD -->
                <div class="bg-white rounded-[24px] soft-border p-6 shadow-card space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="serif text-xl font-bold text-forest">Pay with</h2>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-emerald bg-mint px-2.5 py-1 rounded-full border border-emerald/20">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> 100% Secure via Razorpay
                        </span>
                    </div>

                    <div class="space-y-3">
                        <!-- UPI MAIN ACCORDION WITH PHONEPE, GPAY, PAYTM -->
                        <div class="rounded-2xl border-2 border-forest bg-mint/20 overflow-hidden transition" id="box-upi-option">
                            <label class="flex items-center justify-between p-4 cursor-pointer" onclick="togglePaymentRadio('upi')">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="upi" checked class="text-forest focus:ring-forest" onchange="togglePaymentRadio('upi')">
                                    <div>
                                        <div class="text-xs font-bold text-forest flex items-center gap-1.5">
                                            <span>Instant UPI</span>
                                            <span class="text-[9px] bg-mint text-emerald font-bold px-2 py-0.5 rounded-md border border-emerald/20">Zero Surcharge &middot; Instant</span>
                                        </div>
                                        <div class="text-[11px] text-forest/60 mt-0.5">Pay with PhonePe, Google Pay, Paytm, or scan instant QR</div>
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
                            <div id="upi-apps-panel" class="px-4 pb-4 pt-1 border-t border-forest/10 space-y-2.5">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-forest/60">Choose your preferred UPI method:</div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <!-- Google Pay -->
                                    <div onclick="selectUpiApp('gpay')" id="upi-card-gpay" class="upi-app-badge p-3 rounded-xl border-2 border-forest bg-mint/40 cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs">
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-xs">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.26 21.36 7.33 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.16 0 9.94 0 12s.46 3.84 1.26 5.42l4.02-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
                                        </div>
                                        <div class="text-[11px] font-bold text-forest">Google Pay</div>
                                        <div class="text-[9px] text-forest/50">GPay Direct</div>
                                    </div>

                                    <!-- PhonePe -->
                                    <div onclick="selectUpiApp('phonepe')" id="upi-card-phonepe" class="upi-app-badge p-3 rounded-xl border-2 border-forest/15 hover:border-forest/40 bg-white cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs">
                                        <div class="w-8 h-8 rounded-full bg-[#5F259F] flex items-center justify-center text-white font-black text-sm shadow-xs">
                                            पे
                                        </div>
                                        <div class="text-[11px] font-bold text-forest">PhonePe</div>
                                        <div class="text-[9px] text-forest/50">Instant App</div>
                                    </div>

                                    <!-- Paytm -->
                                    <div onclick="selectUpiApp('paytm')" id="upi-card-paytm" class="upi-app-badge p-3 rounded-xl border-2 border-forest/15 hover:border-forest/40 bg-white cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs">
                                        <div class="h-8 px-2 rounded-lg bg-[#002970] flex items-center justify-center shadow-xs">
                                            <span class="text-[10px] font-black text-[#00BAF2] tracking-tighter">Paytm</span>
                                        </div>
                                        <div class="text-[11px] font-bold text-forest">Paytm UPI</div>
                                        <div class="text-[9px] text-forest/50">Wallet / UPI</div>
                                    </div>

                                    <!-- QR Code / Any UPI -->
                                    <div onclick="selectUpiApp('qr')" id="upi-card-qr" class="upi-app-badge p-3 rounded-xl border-2 border-forest/15 hover:border-forest/40 bg-white cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-800 shadow-xs">
                                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                                        </div>
                                        <div class="text-[11px] font-bold text-forest">Scan QR / Any</div>
                                        <div class="text-[9px] text-forest/50">BHIM / CRED / All</div>
                                    </div>
                                </div>
                                <input type="hidden" name="selected_upi_app" id="selected_upi_app" value="gpay">

                                <!-- PRODUCTION-GRADE UPI INSTRUCTIONS -->
                                <div id="upi-instruction-card" class="mt-3 p-3.5 rounded-xl bg-forest/5 border border-forest/15">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-6 h-6 rounded-full bg-forest text-paper flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 shadow-xs">
                                            <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <div id="upi-guide-title" class="text-xs font-bold text-forest flex items-center gap-1.5">
                                                <span>Google Pay (GPay) Selected</span>
                                                <span class="text-[9px] bg-emerald/10 text-emerald font-bold px-1.5 py-0.5 rounded">Instant</span>
                                            </div>
                                            <div id="upi-guide-desc" class="text-[11px] text-forest/75 leading-relaxed">
                                                Directly open Google Pay on mobile or scan dynamic QR code with any UPI app on desktop.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CREDIT OR DEBIT CARD -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl soft-border hover:border-forest/40 transition cursor-pointer" id="box-card-option" onclick="togglePaymentRadio('card')">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="card" class="text-forest focus:ring-forest" onchange="togglePaymentRadio('card')">
                                <span class="text-xs font-bold text-forest">Credit or Debit Card</span>
                            </div>
                            <div class="flex items-center gap-1 text-[10px] text-forest/40 font-bold">
                                <span>VISA</span> &middot; <span>Mastercard</span> &middot; <span>RuPay</span>
                            </div>
                        </label>

                        <!-- NET BANKING -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl soft-border hover:border-forest/40 transition cursor-pointer" id="box-netbanking-option" onclick="togglePaymentRadio('netbanking')">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="netbanking" class="text-forest focus:ring-forest" onchange="togglePaymentRadio('netbanking')">
                                <span class="text-xs font-bold text-forest">Net Banking</span>
                            </div>
                            <span class="text-[10px] text-forest/50">All Major Banks</span>
                        </label>
                    </div>
                </div>

                <!-- 5. POLICIES & TERMS -->
                <div class="space-y-3 text-xs text-forest/60">
                    <h3 class="font-bold text-forest">Cancellation Policy</h3>
                    <p class="leading-relaxed">
                        Free cancellation until 48 hours before check-in. If cancelled less than 48 hours before arrival, 1 night's charge applies.
                    </p>
                    <p class="text-[11px] leading-relaxed">
                        By selecting the button below, I agree to the <button type="button" onclick="openGroundRulesModal()" class="underline hover:text-forest font-semibold cursor-pointer">Cottage Ground Rules</button>, <button type="button" onclick="openTermsModal()" class="underline hover:text-forest font-semibold cursor-pointer">Terms of Service</button>, and the strictly preserved peaceful nature atmosphere.
                    </p>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" id="submit-booking-btn" class="w-full py-4 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs tracking-wide shadow-card transition flex items-center justify-center gap-2">
                    <span id="booking-btn-text">Confirm & Reserve Room</span>
                    <span id="booking-btn-spinner" class="hidden animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                </button>
            </form>
        </div>

        <!-- RIGHT COLUMN: STICKY BOOKING SUMMARY (5 COLS) -->
        <div class="lg:col-span-5">
            <div class="sticky top-24 bg-white rounded-[26px] soft-border p-6 shadow-card space-y-5">
                <!-- SUITE SUMMARY CARD -->
                <div class="flex gap-4 border-b border-forest/10 pb-5">
                    <img src="{{ $roomType->cover_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=400&q=80' }}" 
                         alt="{{ $roomType->name }}" 
                         class="w-24 h-24 rounded-2xl object-cover shrink-0 soft-border">
                    <div class="flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-emerald tracking-wider">
                                {{ $roomType->branch ? $roomType->branch->name : 'Kerala' }}
                            </span>
                            <h3 class="serif text-base font-bold text-forest">{{ $roomType->name }}</h3>
                            <div class="text-[11px] text-forest/50 mt-0.5">{{ $roomType->bed_type ?? 'King Bed' }} &middot; {{ $roomType->size_sqft ?? 480 }} sq.ft</div>
                        </div>
                        <div class="flex items-center gap-1 text-[11px] font-bold text-forest">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-brass text-brass"></i>
                            <span>4.94 (38 reviews)</span>
                        </div>
                    </div>
                </div>

                <!-- PRICE DETAILS BREAKDOWN -->
                <div class="space-y-2.5 text-xs text-forest/70">
                    <h4 class="eyebrow text-forest/50 mb-2">Price Details</h4>
                    <div class="flex justify-between">
                        <span>
                            ₹{{ number_format($rate) }} avg / night &times; {{ $nights }} night{{ $nights > 1 ? 's' : '' }}{{ ($roomsCount ?? 1) > 1 ? ' &times; ' . $roomsCount . ' rooms' : '' }}
                            @if(!empty($pricing['weekend_nights']) && $pricing['weekend_nights'] > 0)
                                <span class="text-[10px] text-amber-700 block">({{ $pricing['weekend_nights'] }} weekend night{{ $pricing['weekend_nights'] > 1 ? 's' : '' }} @ ₹{{ number_format($pricing['weekend_price']) }})</span>
                            @endif
                        </span>
                        <span class="font-semibold text-forest">₹{{ number_format($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Hospitality & Sanitization</span>
                        <span class="text-emerald font-semibold">Included</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Goods & Services Tax (GST 12%)</span>
                        <span class="font-semibold text-forest">₹{{ number_format($tax) }}</span>
                    </div>

                    <div class="pt-3 border-t border-forest/10 flex justify-between font-bold text-sm text-forest">
                        <span>Total (INR)</span>
                        <span>₹{{ number_format($total) }}</span>
                    </div>

                    <!-- DYNAMIC REAL-TIME DEPOSIT / PAYMENT BREAKDOWN -->
                    <div id="summary-deposit-section" class="pt-3 border-t border-forest/10 space-y-2 {{ old('payment_choice') === 'deposit_20' ? '' : 'hidden' }}">
                        <div class="flex justify-between items-center text-xs font-bold text-emerald bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                                <span>Due Now (20% Deposit)</span>
                            </span>
                            <span>₹{{ number_format($deposit) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] font-semibold text-amber-900 bg-amber-50 px-3 py-2 rounded-xl border border-amber-200">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-700 shrink-0"></i>
                                <span>Remaining Balance Due at Check-in</span>
                            </span>
                            <span>₹{{ number_format($total - $deposit) }}</span>
                        </div>
                    </div>
                    <div id="summary-full-section" class="pt-3 border-t border-forest/10 flex justify-between items-center text-xs font-bold text-forest bg-forest/5 px-3 py-2 rounded-xl {{ old('payment_choice') === 'deposit_20' ? 'hidden' : '' }}">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-forest shrink-0"></i>
                            <span>Due Now (Full Payment)</span>
                        </span>
                        <span>₹{{ number_format($total) }}</span>
                    </div>
                </div>

                <div class="bg-mint p-3 rounded-2xl border border-emerald/15 flex items-center gap-2.5 text-xs text-forest">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald shrink-0"></i>
                    <span>Protected by Country Side Cottages Hospitality Guarantee &middot; 24/7 Concierge Support</span>
                </div>
            </div>
        </div>
</div>

<!-- COTTAGE GROUND RULES & TERMS MODAL -->
<div id="ground-rules-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-forest/80 backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 soft-border shadow-card max-h-[85vh] overflow-y-auto space-y-4">
        <div class="flex items-center justify-between border-b border-forest/10 pb-4">
            <div>
                <span class="eyebrow text-brass block">Country Side Cottages Protocols</span>
                <h3 class="serif text-xl sm:text-2xl font-bold text-forest mt-0.5" id="policy-modal-title">Cottage Ground Rules</h3>
            </div>
            <button type="button" onclick="closeGroundRulesModal()" class="w-8 h-8 rounded-full bg-forest/5 hover:bg-forest/10 flex items-center justify-center text-forest cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="text-xs text-forest/75 leading-relaxed space-y-3.5" id="policy-modal-body">
            <div class="space-y-1">
                <h4 class="font-bold text-forest flex items-center gap-1.5 text-xs">
                    <i data-lucide="volume-x" class="w-3.5 h-3.5 text-brass"></i> Nature Sanctuary & Quiet Hours
                </h4>
                <p>To preserve our valley silence, quiet hours are observed between 10:00 PM and 7:00 AM. Loud outdoor speakers and boisterous gatherings are strictly prohibited.</p>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-forest flex items-center gap-1.5 text-xs">
                    <i data-lucide="ban" class="w-3.5 h-3.5 text-brass"></i> Strictly No Swimming Pool Policy
                </h4>
                <p>Country Side Cottages operates under eco-botanical heritage principles with no artificial swimming pools. We encourage cold mountain stream walks, herbal spice plantation trails, and natural veranda unwinding.</p>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-forest flex items-center gap-1.5 text-xs">
                    <i data-lucide="id-card" class="w-3.5 h-3.5 text-brass"></i> Government ID Requirement
                </h4>
                <p>Valid government-issued photo identification (Aadhaar Card, Passport, or Voter ID for Indian Nationals; Passport & Visa for Foreign Nationals) is mandatory for all adult guests at check-in.</p>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-forest flex items-center gap-1.5 text-xs">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-brass"></i> Smoke-Free Verandas & Cottages
                </h4>
                <p>All cottage interiors and wooden balcony decks are strictly non-smoking to preserve the heritage timber and pristine mountain air.</p>
            </div>
        </div>
        <div class="pt-4 border-t border-forest/10 flex justify-end">
            <button type="button" onclick="closeGroundRulesModal()" class="px-5 py-2.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-xs cursor-pointer">
                I Understand & Agree
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function openGroundRulesModal() {
    const modal = document.getElementById('ground-rules-modal');
    const title = document.getElementById('policy-modal-title');
    if (title) title.textContent = 'Cottage Ground Rules';
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function openTermsModal() {
    const modal = document.getElementById('ground-rules-modal');
    const title = document.getElementById('policy-modal-title');
    if (title) title.textContent = 'Terms of Service & Stay Agreement';
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeGroundRulesModal() {
    const modal = document.getElementById('ground-rules-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('checkout-form');
    const submitBtn = document.getElementById('submit-booking-btn');
    const btnText = document.getElementById('booking-btn-text');
    const btnSpinner = document.getElementById('booking-btn-spinner');
    const errorBox = document.getElementById('payment-error-box');
    const errorMsg = document.getElementById('payment-error-msg');

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
        if (errorBox) {
            errorBox.classList.add('hidden');
        }
    }

    const totalAmount = {{ (float) $total }};
    const depositAmount = {{ (float) $deposit }};
    const totalFormatted = '₹{{ number_format($total) }}';
    const depositFormatted = '₹{{ number_format($deposit) }}';

    function getSelectedChoice() {
        const checked = document.querySelector('input[name="payment_choice"]:checked');
        return checked ? checked.value : 'full';
    }

    function getSelectedMethod() {
        const checked = document.querySelector('input[name="payment_method"]:checked');
        return checked ? checked.value : 'upi';
    }

    function updateSubmitButton() {
        if (!submitBtn) return;
        if (submitBtn.disabled && btnSpinner && !btnSpinner.classList.contains('hidden')) return;

        const choice = getSelectedChoice();
        const method = getSelectedMethod();
        const isDeposit = (choice === 'deposit_20');
        const activeAmount = isDeposit ? depositFormatted : totalFormatted;
        const labelPrefix = isDeposit ? '20% Deposit (' + activeAmount + ')' : 'in Full (' + activeAmount + ')';

        if (method === 'upi') {
            btnText.textContent = `Confirm & Pay ${labelPrefix} via UPI`;
        } else if (method === 'card') {
            btnText.textContent = `Confirm & Pay ${labelPrefix} with Card`;
        } else if (method === 'netbanking') {
            btnText.textContent = `Confirm & Pay ${labelPrefix} via Net Banking`;
        } else {
            btnText.textContent = `Confirm & Pay ${labelPrefix}`;
        }
    }

    function setLoading(isLoading) {
        if (!submitBtn) return;
        submitBtn.disabled = isLoading;
        if (isLoading) {
            btnText.textContent = 'Processing Payment...';
            btnSpinner.classList.remove('hidden');
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        } else {
            btnSpinner.classList.add('hidden');
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            updateSubmitButton();
        }
    }

    window.updatePaymentChoice = function(choice) {
        const radio = document.querySelector(`input[name="payment_choice"][value="${choice}"]`);
        if (radio) radio.checked = true;

        const cardFull = document.getElementById('choice-card-full');
        const cardDeposit = document.getElementById('choice-card-deposit_20');
        const summaryDeposit = document.getElementById('summary-deposit-section');
        const summaryFull = document.getElementById('summary-full-section');

        if (choice === 'deposit_20') {
            if (cardDeposit) {
                cardDeposit.className = 'flex items-start justify-between p-4 rounded-2xl border-2 border-forest bg-mint/50 cursor-pointer transition shadow-xs';
            }
            if (cardFull) {
                cardFull.className = 'flex items-start justify-between p-4 rounded-2xl soft-border hover:border-forest/40 bg-white cursor-pointer transition';
            }
            if (summaryDeposit) summaryDeposit.classList.remove('hidden');
            if (summaryFull) summaryFull.classList.add('hidden');
        } else {
            if (cardFull) {
                cardFull.className = 'flex items-start justify-between p-4 rounded-2xl border-2 border-forest bg-mint/50 cursor-pointer transition shadow-xs';
            }
            if (cardDeposit) {
                cardDeposit.className = 'flex items-start justify-between p-4 rounded-2xl soft-border hover:border-forest/40 bg-white cursor-pointer transition';
            }
            if (summaryDeposit) summaryDeposit.classList.add('hidden');
            if (summaryFull) summaryFull.classList.remove('hidden');
        }

        updateSubmitButton();
    };

    window.togglePaymentRadio = function(method) {
        const radio = document.querySelector(`input[name="payment_method"][value="${method}"]`);
        if (radio) radio.checked = true;

        const upiBox = document.getElementById('box-upi-option');
        const cardBox = document.getElementById('box-card-option');
        const netbankingBox = document.getElementById('box-netbanking-option');
        const upiPanel = document.getElementById('upi-apps-panel');

        // Reset styling
        if (upiBox) upiBox.className = 'rounded-2xl soft-border overflow-hidden transition';
        if (cardBox) cardBox.className = 'flex items-center justify-between p-3.5 rounded-2xl soft-border hover:border-forest/40 transition cursor-pointer';
        if (netbankingBox) netbankingBox.className = 'flex items-center justify-between p-3.5 rounded-2xl soft-border hover:border-forest/40 transition cursor-pointer';

        if (method === 'upi') {
            if (upiBox) upiBox.className = 'rounded-2xl border-2 border-forest bg-mint/20 overflow-hidden transition';
            if (upiPanel) upiPanel.classList.remove('hidden');
        } else if (method === 'card') {
            if (cardBox) cardBox.className = 'flex items-center justify-between p-3.5 rounded-2xl border-2 border-forest bg-mint/20 transition cursor-pointer shadow-xs';
            if (upiPanel) upiPanel.classList.add('hidden');
        } else if (method === 'netbanking') {
            if (netbankingBox) netbankingBox.className = 'flex items-center justify-between p-3.5 rounded-2xl border-2 border-forest bg-mint/20 transition cursor-pointer shadow-xs';
            if (upiPanel) upiPanel.classList.add('hidden');
        }

        updateSubmitButton();
    };

    window.selectUpiApp = function(app) {
        const hiddenAppInput = document.getElementById('selected_upi_app');
        if (hiddenAppInput) hiddenAppInput.value = app;
        document.querySelectorAll('.upi-app-badge').forEach(el => {
            el.className = 'upi-app-badge p-3 rounded-xl border-2 border-forest/15 hover:border-forest/40 bg-white cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs';
        });
        const activeCard = document.getElementById(`upi-card-${app}`);
        if (activeCard) {
            activeCard.className = 'upi-app-badge p-3 rounded-xl border-2 border-forest bg-mint/40 cursor-pointer transition flex flex-col items-center text-center gap-1 shadow-xs';
        }

        const guideTitle = document.getElementById('upi-guide-title');
        const guideDesc = document.getElementById('upi-guide-desc');
        if (guideTitle && guideDesc) {
            if (app === 'gpay') {
                guideTitle.innerHTML = '<span>Google Pay (GPay) Selected</span><span class="text-[9px] bg-emerald/10 text-emerald font-bold px-1.5 py-0.5 rounded">Instant</span>';
                guideDesc.innerHTML = 'Directly open Google Pay on mobile or scan the dynamic QR code with any UPI app on desktop.';
            } else if (app === 'phonepe') {
                guideTitle.innerHTML = '<span>PhonePe UPI Selected</span><span class="text-[9px] bg-[#5F259F]/10 text-[#5F259F] font-bold px-1.5 py-0.5 rounded">Instant</span>';
                guideDesc.innerHTML = 'Directly open PhonePe on mobile or scan the dynamic QR code with any UPI app on desktop.';
            } else if (app === 'paytm') {
                guideTitle.innerHTML = '<span>Paytm UPI Selected</span><span class="text-[9px] bg-[#002970]/10 text-[#002970] font-bold px-1.5 py-0.5 rounded">Instant</span>';
                guideDesc.innerHTML = 'Directly open Paytm on mobile or scan the dynamic QR code with any UPI app on desktop.';
            } else {
                guideTitle.innerHTML = '<span>Scan QR / Any UPI App</span><span class="text-[9px] bg-forest/10 text-forest font-bold px-1.5 py-0.5 rounded">All Apps</span>';
                guideDesc.innerHTML = 'Scan the on-screen QR code using any UPI app (Google Pay, PhonePe, Paytm, BHIM, CRED).';
            }
        }
        if (window.lucide) lucide.createIcons();
        window.togglePaymentRadio('upi');
    };

    // Initialize initial state
    updatePaymentChoice('{{ old("payment_choice", "full") }}');
    togglePaymentRadio('{{ old("payment_method", "upi") }}');

    if (form) {
        form.addEventListener('submit', async (e) => {
            // Check standard HTML5 validation first
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const paymentMethodInput = form.querySelector('input[name="payment_method"]:checked');
            const paymentMethod = paymentMethodInput ? paymentMethodInput.value : 'upi';

            // If transaction token already present, proceed to standard submit
            if (document.getElementById('razorpay_payment_id').value) {
                return;
            }

            e.preventDefault();
            clearError();
            setLoading(true);

            try {
                const formData = new FormData(form);
                const payload = {
                    room_type_id: formData.get('room_type_id'),
                    check_in_date: formData.get('check_in_date'),
                    check_out_date: formData.get('check_out_date'),
                    adults: formData.get('adults'),
                    children: formData.get('children'),
                    rooms_count: formData.get('rooms_count') || 1,
                    child_ages: formData.getAll('child_ages[]'),
                    payment_choice: formData.get('payment_choice'),
                    first_name: formData.get('first_name'),
                    last_name: formData.get('last_name'),
                    email: formData.get('email'),
                    phone: formData.get('phone'),
                    city: formData.get('city'),
                    country: formData.get('country'),
                    special_requests: formData.get('special_requests'),
                };

                const res = await fetch('{{ route("booking.razorpay.create-order") }}', {
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
                    showError(data.message || 'Unable to initialize online payment. Please try again or select another payment method.');
                    setLoading(false);
                    return;
                }

                const prefillData = Object.assign({}, data.prefill || {});
                if (paymentMethod === 'upi') {
                    prefillData.method = 'upi';
                }

                const options = {
                    key: data.key_id,
                    amount: data.amount,
                    currency: data.currency || 'INR',
                    name: data.name || 'Country Side Cottages',
                    description: data.description || 'Room Reservation',
                    order_id: data.order_id,
                    prefill: prefillData,
                    theme: data.theme || { color: '#063F34' },
                    handler: function (response) {
                        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                        document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                        document.getElementById('razorpay_signature').value = response.razorpay_signature;
                        btnText.textContent = 'Securing Your Booking...';
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
