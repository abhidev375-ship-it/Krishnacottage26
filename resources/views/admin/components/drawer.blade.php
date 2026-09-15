<!-- RESERVATION DETAIL SLIDE-OVER DRAWER (ADM-03) -->
<div id="drawer-backdrop" class="fixed inset-0 bg-brand-text/40 backdrop-blur-xs z-40 hidden transition-opacity" onclick="closeDrawer()"></div>

<div id="reservation-drawer" class="fixed inset-y-0 right-0 w-full max-w-md bg-brand-surface shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col border-l border-gray-200">
    <!-- Header -->
    <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/80">
        <div>
            <h3 class="text-base font-bold text-brand-text" id="drawer-booking-code">Reservation Details</h3>
            <p class="text-xs text-brand-muted" id="drawer-created-at">Loading details...</p>
        </div>
        <button onclick="closeDrawer()" class="p-1.5 hover:bg-gray-200 rounded-lg text-gray-500 transition">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Scrollable Body -->
    <div class="flex-1 overflow-y-auto p-5 space-y-5 text-xs">
        <!-- Guest Profile Card -->
        <div class="bg-brand-canvas/70 p-3.5 rounded-xl border border-gray-200/70 space-y-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-primary/10 text-brand-primary font-bold flex items-center justify-center text-sm" id="drawer-guest-avatar">
                    G
                </div>
                <div>
                    <div class="font-bold text-sm text-brand-text" id="drawer-guest-name">Guest Name</div>
                    <div class="text-[11px] text-brand-muted" id="drawer-guest-contact">Phone & Email</div>
                </div>
            </div>
        </div>

        <!-- Stay Timeline Card -->
        <div class="space-y-1.5">
            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider">Stay Timeline</span>
            <div class="flex items-center justify-between bg-brand-surface p-3 rounded-xl border border-gray-200 shadow-xs">
                <div class="text-center">
                    <div class="text-[10px] text-brand-muted uppercase font-semibold">Check-In</div>
                    <div class="font-bold text-xs text-brand-text mt-0.5" id="drawer-checkin">Oct 12, 2026</div>
                </div>
                <div class="px-3 text-brand-muted"><i data-lucide="arrow-right" class="w-4 h-4"></i></div>
                <div class="text-center">
                    <div class="text-[10px] text-brand-muted uppercase font-semibold">Check-Out</div>
                    <div class="font-bold text-xs text-brand-text mt-0.5" id="drawer-checkout">Oct 15, 2026</div>
                </div>
            </div>
        </div>

        <!-- Room & Branch Card -->
        <div class="space-y-1.5">
            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider">Assigned Accommodation</span>
            <div class="p-3 bg-brand-surface rounded-xl border border-gray-200 shadow-xs space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-brand-muted">Branch:</span>
                    <span class="font-bold text-brand-text" id="drawer-branch-name">Branch</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-brand-muted">Room Category:</span>
                    <span class="font-semibold text-brand-text" id="drawer-room-type">Room Type</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                    <span class="text-brand-muted">Physical Unit:</span>
                    <span class="font-bold text-emerald-800" id="drawer-room-unit">Unassigned</span>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="space-y-1.5">
            <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider">Financial Breakdown</span>
            <div class="p-3 bg-brand-surface rounded-xl border border-gray-200 shadow-xs space-y-1.5">
                <div class="flex justify-between text-brand-muted">
                    <span>Nightly Rate:</span>
                    <span id="drawer-rate">₹0</span>
                </div>
                <div class="flex justify-between text-brand-muted">
                    <span>Subtotal:</span>
                    <span id="drawer-subtotal">₹0</span>
                </div>
                <div class="flex justify-between text-brand-muted">
                    <span>Tax (GST 12%):</span>
                    <span id="drawer-tax">₹0</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-brand-text pt-2 border-t border-gray-100">
                    <span>Total Amount:</span>
                    <span id="drawer-total" class="text-brand-primary">₹0</span>
                </div>
                <div class="flex justify-between text-xs pt-1">
                    <span class="text-brand-muted">Payment State:</span>
                    <span id="drawer-payment-badge" class="font-bold">Pending</span>
                </div>
                <div id="drawer-refund-row" class="hidden flex justify-between text-xs pt-1 border-t border-gray-100 text-emerald-800 font-bold">
                    <span>Automated Refund:</span>
                    <span id="drawer-refunded-amount">₹0</span>
                </div>
            </div>
        </div>

        <!-- Special Requests & Extension Alert -->
        <div class="space-y-2">
            <div id="drawer-extension-alert" class="hidden p-3 bg-amber-50 border border-amber-200 rounded-xl space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-900">Approved Stay Extension (Unsettled)</span>
                    <span id="drawer-ext-badge" class="px-1.5 py-0.5 bg-amber-200 text-amber-900 rounded text-[9px] font-bold">Folio Pending</span>
                </div>
                <p class="text-[11px] text-amber-800" id="drawer-ext-desc">Extended stay offer pending on-hand or checkout payment.</p>
                <button type="button" onclick="openExtensionPaymentModalFromDrawer()" class="w-full py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i> Settle Extension Payment Now
                </button>
            </div>

            <div class="space-y-1">
                <span class="text-[10px] uppercase font-bold text-brand-muted tracking-wider">Special Requests / Counter Notes</span>
                <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-200 text-brand-muted text-[11px]" id="drawer-requests">
                    None specified
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="p-4 border-t border-gray-200 bg-gray-50/90 space-y-2" id="drawer-action-buttons">
        <div class="flex gap-2">
            <button onclick="triggerReservationCheckIn()" id="drawer-btn-checkin" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Check In
            </button>
            <button onclick="triggerReservationCheckOut()" id="drawer-btn-checkout" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                <i data-lucide="log-out" class="w-4 h-4"></i> Check Out
            </button>
        </div>
        <button onclick="openCancellationPreviewModal()" id="drawer-btn-cancel" class="w-full py-1.5 text-red-600 hover:bg-red-50 font-semibold rounded-lg text-xs transition flex items-center justify-center gap-1">
            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Cancel Reservation & Calculate Cashback
        </button>
    </div>
</div>

<!-- MODAL: CANCELLATION & AUTOMATED PAYBACK ENGINE (ADM-03) -->
<div id="cancellation-preview-modal" class="fixed inset-0 z-[80] hidden flex items-center justify-center p-4" style="z-index: 80;">
    <div class="fixed inset-0 bg-brand-text/50 backdrop-blur-xs transition-opacity" onclick="closeCancellationPreviewModal()"></div>
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-5 border border-red-200 z-10 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center font-bold">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-brand-text">Cancellation & Payback Calculation</h3>
                    <p class="text-[10px] text-brand-muted" id="cancel-modal-code">Ref: Loading...</p>
                </div>
            </div>
            <button onclick="closeCancellationPreviewModal()" class="p-1 hover:bg-gray-100 rounded text-gray-400">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Calculated Tier Breakdown Box -->
        <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200 space-y-2 text-xs">
            <div class="flex justify-between text-brand-muted">
                <span>Hours to Check-In:</span>
                <span id="cancel-calc-hours" class="font-bold text-brand-text">0 hrs</span>
            </div>
            <div class="flex justify-between text-brand-muted">
                <span>Total Paid by Guest:</span>
                <span id="cancel-calc-paid" class="font-bold text-brand-text">₹0</span>
            </div>
            <div class="flex justify-between text-brand-muted">
                <span>Matched Cashback Tier:</span>
                <span id="cancel-calc-pct" class="font-bold text-blue-600">0%</span>
            </div>
            <div class="flex justify-between text-sm font-extrabold text-emerald-700 pt-2 border-t border-gray-200">
                <span>Automated Refund / Payback:</span>
                <span id="cancel-calc-cashback">₹0</span>
            </div>
            <div class="flex justify-between text-[11px] text-red-600">
                <span>Cancellation Deduction (Retained):</span>
                <span id="cancel-calc-retained">₹0</span>
            </div>
            <p id="cancel-calc-rule" class="text-[10px] text-brand-muted italic pt-1 border-t border-gray-200/60">Loading rule details...</p>
        </div>

        <div>
            <label class="block text-[10px] uppercase font-bold text-brand-muted mb-1">Cancellation Reason / Notes</label>
            <input type="text" id="cancel-reason-input" value="Guest requested cancellation" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs">
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
            <button type="button" onclick="closeCancellationPreviewModal()" class="px-3 py-1.5 bg-gray-100 text-brand-text text-xs font-semibold rounded-lg">
                Abort
            </button>
            <button type="button" id="cancel-execute-btn" onclick="executeCancellationWithCashback()" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="dollar-sign" class="w-3.5 h-3.5"></i> Confirm & Auto-Refund
            </button>
        </div>
    </div>
</div>
