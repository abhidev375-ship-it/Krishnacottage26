<!-- NEW RESERVATION & FRONT-DESK WALK-IN COUNTER MODAL (ADM-07) -->
<div id="new-reservation-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-4">
    <div class="fixed inset-0 bg-brand-text/50 backdrop-blur-xs transition-opacity" onclick="closeNewReservationModal()"></div>

    <div class="relative w-full max-w-2xl max-h-[92vh] bg-brand-surface rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-200 z-10">
        <!-- Modal Header -->
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-emerald-50/40 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                    <i data-lucide="hotel" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-brand-text">Front-Desk & Walk-In Reservation</h3>
                    <p class="text-[11px] text-brand-muted">Instant software-wide room locking & guest registration</p>
                </div>
            </div>
            <button onclick="closeNewReservationModal()" class="p-1.5 hover:bg-gray-200 rounded-lg text-gray-500 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form Body -->
        <form id="new-reservation-form" onsubmit="submitNewReservation(event)" class="p-4 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" name="is_counter_booking" value="1">

            <!-- Step 1: Branch & Category Selection -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-bold text-brand-text block">1. Destination Branch *</label>
                    <select id="modal-branch-select" name="branch_id" onchange="onModalDatesOrBranchChanged()" required class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-primary">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-brand-text block">2. Room Category *</label>
                    <select id="modal-room-type-select" name="room_type_id" onchange="onModalDatesOrBranchChanged()" required class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-primary">
                        @foreach($roomTypes as $rt)
                            <option value="{{ $rt->id }}" data-branch="{{ $rt->branch_id }}" data-price="{{ $rt->base_price }}">
                                {{ $rt->branch ? $rt->branch->code . ' - ' : '' }}{{ $rt->name }} (₹{{ number_format($rt->base_price) }}/nt)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Step 2: Stay Dates & Physical Room Selector -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50/70 p-3 rounded-xl border border-gray-200/70">
                <div class="space-y-1">
                    <label class="font-bold text-brand-text block">Check-In Date *</label>
                    <input type="date" id="modal-checkin-date" name="check_in_date" value="{{ \Carbon\Carbon::today()->toDateString() }}" onchange="onModalDatesOrBranchChanged()" required class="w-full p-2 bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-primary">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-brand-text block">Check-Out Date *</label>
                    <input type="date" id="modal-checkout-date" name="check_out_date" value="{{ \Carbon\Carbon::today()->addDays(2)->toDateString() }}" onchange="onModalDatesOrBranchChanged()" required class="w-full p-2 bg-white border border-gray-200 rounded-lg focus:ring-1 focus:ring-brand-primary">
                </div>
            </div>

            <!-- Live Physical Unit Selector (Filtered by Date Availability) -->
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label class="font-bold text-brand-text block">3. Physical Room Allocation</label>
                    <span id="modal-room-status-indicator" class="text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Querying live units...</span>
                    </span>
                </div>
                <select id="modal-physical-room-select" name="room_id" class="w-full p-2 bg-brand-canvas border border-emerald-200/70 rounded-lg font-semibold text-brand-text focus:ring-1 focus:ring-brand-primary">
                    <option value="">⚡ Auto-Assign Next Available Free Unit</option>
                    @foreach($rooms as $rm)
                        <option value="{{ $rm->id }}" data-branch="{{ $rm->branch_id }}" data-type="{{ $rm->room_type_id }}">
                            Villa {{ $rm->room_number }} ({{ $rm->roomType ? $rm->roomType->name : 'Standard' }} - Floor {{ $rm->floor }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-brand-muted">Only rooms with zero booking overlaps for requested dates are listed.</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-bold text-brand-text block">Adults</label>
                    <input type="number" name="adults" value="2" min="1" max="10" class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-brand-text block">Children</label>
                    <input type="number" name="children" value="0" min="0" max="10" class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg">
                </div>
            </div>

            <!-- Step 3: Guest CRM & Government ID Verification -->
            <div class="space-y-2 pt-2 border-t border-gray-100">
                <span class="font-bold text-brand-text block">4. Guest Details & ID Verification</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <input type="text" name="guest_name" placeholder="Full Name *" required class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg">
                    <input type="email" name="guest_email" placeholder="Email Address *" required class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg">
                    <input type="text" name="guest_phone" placeholder="Phone (+91) *" required class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <select name="id_proof_type" class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg">
                        <option value="aadhaar">Aadhaar Card</option>
                        <option value="passport">Passport</option>
                        <option value="driving_license">Driving License</option>
                        <option value="voter_id">Voter ID</option>
                    </select>
                    <input type="text" name="id_proof_number" placeholder="ID Proof Number (e.g. 1234 5678 9012)" class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg">
                </div>
            </div>

            <!-- Step 4: Front-Desk Counter Payment & Instant Check-in -->
            <div class="p-3.5 bg-emerald-50/50 rounded-xl border border-emerald-200/60 space-y-2.5">
                <span class="font-bold text-emerald-950 block">5. Counter Payment & Instant Check-In</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-emerald-900 block mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full p-2 bg-white border border-emerald-200 rounded-lg font-semibold text-xs">
                            <option value="cash">💵 Cash on Hand</option>
                            <option value="pos_card">💳 Front-Desk POS Card Machine</option>
                            <option value="counter_upi">📱 Front-Desk UPI QR</option>
                            <option value="bank_transfer">🏦 Direct Bank NEFT/RTGS</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-bold text-emerald-900 block mb-1">Amount Collected (₹)</label>
                        <input type="number" name="paid_amount" placeholder="0.00" step="0.01" class="w-full p-2 bg-white border border-emerald-200 rounded-lg font-bold text-xs">
                    </div>
                </div>

                <label class="flex items-center gap-2 pt-1 cursor-pointer select-none">
                    <input type="checkbox" name="instant_checkin" value="1" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                    <span class="font-bold text-emerald-900 text-xs">Instant Check-In Now (Immediately marks room Occupied and activates stay folio)</span>
                </label>
            </div>

            <!-- Special Requests -->
            <div class="space-y-1">
                <label class="font-semibold text-brand-muted block">Special Requests / Front-Desk Notes</label>
                <textarea name="special_requests" rows="2" placeholder="Late checkout requested, extra linen, airport pickup..." class="w-full p-2 bg-brand-canvas border border-gray-200 rounded-lg"></textarea>
            </div>

            <!-- Footer & Submit -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                <div class="text-[11px] text-brand-muted">
                    ⚡ Locks room in central database immediately upon submission.
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeNewReservationModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text font-semibold rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" id="submit-res-btn" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Register & Lock Room</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
