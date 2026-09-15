<!-- Food Order Card Sub-component -->
<div class="bg-white p-3.5 rounded-xl border border-gray-200/90 shadow-xs space-y-2.5 hover:shadow-md transition" id="food-order-{{ $order->id }}">
    <div class="flex items-start justify-between">
        <div>
            <div class="font-bold text-sm text-brand-text">{{ $order->order_number }}</div>
            <div class="text-[11px] text-brand-muted">{{ $order->customer_name }}</div>
        </div>
        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-brand-text">
            {{ ucwords(str_replace('_', ' ', $order->order_type)) }} {{ $order->table_number ? '· ' . $order->table_number : '' }}
        </span>
    </div>

    <!-- Order Items Summary -->
    <div class="space-y-1 bg-gray-50/70 p-2 rounded-lg border border-gray-100 text-xs">
        @foreach($order->items as $item)
        <div class="flex items-center justify-between text-[11px]">
            <span class="font-medium text-brand-text">{{ $item->quantity }}x {{ $item->item_name }}</span>
            <span class="font-semibold text-brand-muted">₹{{ number_format($item->subtotal) }}</span>
        </div>
        @if($item->selected_modifiers)
            <div class="text-[10px] text-emerald-700 pl-3">
                @foreach($item->selected_modifiers as $mod)
                    {{ $mod }}
                @endforeach
            </div>
        @endif
        @endforeach
    </div>

    @if($order->special_instructions)
    <div class="text-[11px] text-amber-800 bg-amber-50/80 p-1.5 rounded border border-amber-200/50">
        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i> {{ $order->special_instructions }}
    </div>
    @endif

    <!-- Review & Moderation Block (If Reviewed by Guest) -->
    @if($order->foodReview)
    <div class="p-2.5 rounded-lg bg-amber-50/70 border border-amber-200/60 space-y-1.5 text-xs" id="card-review-block-{{ $order->foodReview->id }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1 text-amber-500">
                @for($i = 1; $i <= 5; $i++)
                <i data-lucide="star" class="w-3 h-3 {{ $i <= $order->foodReview->rating ? 'fill-amber-400 text-amber-400' : 'text-gray-300' }}"></i>
                @endfor
                <span class="text-[10px] font-bold text-amber-900 ml-1">{{ $order->foodReview->rating }}.0</span>
            </div>
            <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $order->foodReview->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}" id="card-review-badge-{{ $order->foodReview->id }}">
                {{ $order->foodReview->status === 'approved' ? '✓ Approved' : '⚡ Review Posted' }}
            </span>
        </div>
        @if($order->foodReview->comment)
        <p class="text-[11px] text-amber-900 italic leading-snug">"{{ $order->foodReview->comment }}"</p>
        @endif

        @if($order->foodReview->status === 'pending')
        <button type="button" 
                onclick="approveFoodReviewFromCard({{ $order->foodReview->id }}, this)" 
                class="w-full mt-1 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center gap-1 shadow-xs transition">
            <i data-lucide="check" class="w-3 h-3"></i>
            <span>Approve Review</span>
        </button>
        @endif
    </div>
    @endif

    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-brand-text">₹{{ number_format($order->total_amount) }}</span>
            <span class="text-[10px] text-brand-muted block">{{ $order->ordered_at->diffForHumans() }}</span>
        </div>
        @if($nextStatus)
        <button onclick="advanceFoodOrderStatus({{ $order->id }}, '{{ $nextStatus }}')" class="px-2.5 py-1 bg-brand-primary hover:bg-brand-deep text-white rounded text-xs font-semibold shadow-xs transition flex items-center gap-1">
            <span>{{ $nextLabel }}</span>
            <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </button>
        @else
        <span class="text-[10px] font-bold text-emerald-700 flex items-center gap-1">
            <i data-lucide="check" class="w-3 h-3"></i> Delivered
        </span>
        @endif
    </div>
</div>

<script>
if (typeof approveFoodReviewFromCard === 'undefined') {
    window.approveFoodReviewFromCard = async function(reviewId, btn) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        try {
            btn.disabled = true;
            btn.innerText = 'Approving...';
            const res = await fetch(`/admin/food-reviews/${reviewId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });
            const data = await res.json();
            if (data.success) {
                if (typeof showToast === 'function') showToast(data.message, 'success');
                const badge = document.getElementById('card-review-badge-' + reviewId);
                if (badge) {
                    badge.className = 'text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800';
                    badge.innerText = '✓ Approved';
                }
                btn.remove();
            } else {
                btn.disabled = false;
                btn.innerText = 'Approve Review';
                alert(data.message || 'Error approving review');
            }
        } catch(err) {
            console.error(err);
            btn.disabled = false;
            btn.innerText = 'Approve Review';
        }
    };
}
</script>
