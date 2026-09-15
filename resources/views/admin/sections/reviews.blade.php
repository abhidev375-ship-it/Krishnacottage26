{{-- Admin Section: Reviews & Testimonials Hub --}}
<div id="reviews" class="section space-y-6">

    <!-- Top Section Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-amber-600">
                    <i data-lucide="star" class="w-5 h-5 fill-amber-400 text-amber-500"></i>
                </div>
                <div>
                    <h2 class="font-bold text-lg text-brand-text">Reviews & Testimonials Hub</h2>
                    <p class="text-xs text-brand-muted">Moderate verified guest reviews and curate the dynamic welcome page testimonials carousel.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="switchReviewsHubTab('testimonials')" class="inline-flex items-center gap-2 px-3.5 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold shadow-xs hover:bg-brand-deep transition cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Testimonial</span>
            </button>
            <a href="{{ route('home') }}#reviews" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-brand-canvas text-brand-text border border-gray-200 rounded-xl text-xs font-semibold hover:bg-gray-100 transition" title="Preview live carousel on homepage">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-brand-muted"></i>
                <span>View On Homepage</span>
            </a>
        </div>
    </div>

    <!-- Navigation Hub Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
        <button type="button" id="tab-btn-guest-reviews" onclick="switchReviewsHubTab('moderation')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer bg-brand-deep text-white shadow-xs">
            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
            <span>Guest Review Moderation</span>
            @php
                $totalPending = ($pendingReviewsCount ?? 0) + ($pendingFoodReviewsCount ?? 0);
            @endphp
            @if($totalPending > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500 text-white font-extrabold animate-pulse">{{ $totalPending }} Pending</span>
            @endif
        </button>

        <button type="button" id="tab-btn-welcome-testimonials" onclick="switchReviewsHubTab('testimonials')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-brand-muted hover:text-brand-text hover:bg-white transition cursor-pointer">
            <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
            <span>Welcome Carousel Testimonials</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-brand-canvas border border-gray-200 text-brand-text font-bold">{{ $testimonials->count() }}</span>
        </button>
    </div>

    <!-- ================= TAB 1: GUEST REVIEW MODERATION ================= -->
    <div id="subtab-reviews-moderation" class="space-y-4">
        <!-- Filter Controls -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-brand-muted mr-1">Filter:</span>
                <button type="button" onclick="filterReviewsList('all')" class="review-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-deep text-white shadow-2xs" data-filter="all">All Reviews</button>
                <button type="button" onclick="filterReviewsList('pending')" class="review-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-canvas text-brand-muted hover:text-brand-text" data-filter="pending">
                    Needs Review @if($totalPending > 0)<span class="ml-1 px-1.5 py-0.2 rounded-full bg-amber-500 text-white text-[10px]">{{ $totalPending }}</span>@endif
                </button>
                <button type="button" onclick="filterReviewsList('approved')" class="review-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-canvas text-brand-muted hover:text-brand-text" data-filter="approved">Approved</button>
                <button type="button" onclick="filterReviewsList('rejected')" class="review-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-canvas text-brand-muted hover:text-brand-text" data-filter="rejected">Hidden / Rejected</button>
                <button type="button" onclick="filterReviewsList('stay')" class="review-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-canvas text-brand-muted hover:text-brand-text" data-filter="stay">Stay Only</button>
                <button type="button" onclick="filterReviewsList('food')" class="review-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-canvas text-brand-muted hover:text-brand-text" data-filter="food">Dining Only</button>
            </div>

            <div class="text-xs text-brand-muted flex items-center gap-2">
                <i data-lucide="info" class="w-3.5 h-3.5 text-brand-accent"></i>
                <span>Approved reviews show on public listings. Click "Promote" to highlight in Welcome Carousel.</span>
            </div>
        </div>

        <!-- Reviews Grid / Stream -->
        <div class="grid grid-cols-1 gap-4" id="reviews-stream-container">
            {{-- 1. Stay Reviews --}}
            @forelse($reviews as $rev)
                <div class="review-card bg-white p-5 rounded-2xl border border-gray-200 shadow-xs hover:shadow-sm transition space-y-4"
                     data-type="stay"
                     data-status="{{ $rev->status }}">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center text-sm border border-emerald-200">
                                {{ strtoupper(substr($rev->guest->first_name ?? $rev->guest_name ?? 'G', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-brand-text">{{ $rev->guest->full_name ?? $rev->guest_name ?? 'Guest' }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">Verified Stay</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">Resort Stay</span>
                                </div>
                                <p class="text-[11px] text-brand-muted mt-0.5">
                                    {{ $rev->branch->name ?? 'Krishna Cottage' }}
                                    @if($rev->roomType) · {{ $rev->roomType->name }} @endif
                                    · {{ $rev->created_at ? $rev->created_at->format('d M Y, h:i A') : '' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Star Rating -->
                            <div class="flex items-center text-amber-400 text-xs font-bold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/60">
                                @for($i = 1; $i <= 5; $i++)
                                    <i data-lucide="star" class="w-3.5 h-3.5 {{ $i <= ($rev->rating ?? 5) ? 'fill-amber-400 text-amber-400' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-1.5 text-amber-700">{{ $rev->rating }}/5</span>
                            </div>

                            <!-- Status Badge -->
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                @if($rev->status === 'approved') bg-emerald-100 text-emerald-800 border border-emerald-200
                                @elseif($rev->status === 'pending') bg-amber-100 text-amber-800 border border-amber-200 animate-pulse
                                @elseif($rev->status === 'hidden') bg-gray-100 text-gray-700 border border-gray-200
                                @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                                {{ $rev->status }}
                            </span>
                        </div>
                    </div>

                    <!-- Review Content -->
                    <div class="space-y-1.5">
                        @if($rev->title)
                            <h5 class="font-bold text-sm text-brand-text">{{ $rev->title }}</h5>
                        @endif
                        <p class="text-xs text-brand-muted leading-relaxed italic">
                            “{{ $rev->comment }}”
                        </p>
                    </div>

                    <!-- Staff Reply Note if exists -->
                    @if($rev->staff_reply)
                        <div class="bg-brand-canvas p-3 rounded-xl border border-gray-200/70 text-xs space-y-1">
                            <div class="flex items-center gap-1.5 font-bold text-brand-deep">
                                <i data-lucide="corner-down-right" class="w-3.5 h-3.5 text-brand-accent"></i>
                                <span>Official Staff Response:</span>
                            </div>
                            <p class="text-brand-muted pl-5">{{ $rev->staff_reply }}</p>
                        </div>
                    @endif

                    <!-- Action Toolbar -->
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            @if($rev->status !== 'approved')
                                <button type="button" onclick="moderateStayReview({{ $rev->id }}, 'approved')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition cursor-pointer shadow-2xs">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Approve</span>
                                </button>
                            @endif

                            @if($rev->status !== 'rejected')
                                <button type="button" onclick="moderateStayReview({{ $rev->id }}, 'rejected')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition cursor-pointer">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    <span>Reject</span>
                                </button>
                            @endif

                            @if($rev->status !== 'hidden' && $rev->status === 'approved')
                                <button type="button" onclick="moderateStayReview({{ $rev->id }}, 'hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition cursor-pointer">
                                    <i data-lucide="eye-off" class="w-3.5 h-3.5"></i>
                                    <span>Hide</span>
                                </button>
                            @endif

                            <button type="button" onclick="toggleStaffReplyBox({{ $rev->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-canvas text-brand-text border border-gray-200 hover:bg-gray-100 transition cursor-pointer">
                                <i data-lucide="message-square" class="w-3.5 h-3.5 text-brand-muted"></i>
                                <span>{{ $rev->staff_reply ? 'Edit Reply' : 'Staff Reply' }}</span>
                            </button>
                        </div>

                        <!-- 1-Click Promote to Testimonial Carousel -->
                        <div>
                            @php
                                $isAlreadyPromoted = \App\Models\Testimonial::where('review_id', $rev->id)->exists();
                            @endphp
                            @if($isAlreadyPromoted)
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500 fill-amber-400"></i>
                                    <span>In Welcome Carousel</span>
                                </span>
                            @else
                                <button type="button" onclick="promoteReviewToTestimonial({{ $rev->id }}, 'stay')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500 text-white hover:bg-amber-600 transition cursor-pointer shadow-2xs">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                    <span>Promote to Welcome Testimonial</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Inline Staff Reply Form (Hidden by default) -->
                    <div id="reply-box-{{ $rev->id }}" class="hidden pt-3 border-t border-gray-100 space-y-2">
                        <textarea id="reply-input-{{ $rev->id }}" rows="2" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-1 focus:ring-brand-primary" placeholder="Type official response from resort management...">{{ $rev->staff_reply }}</textarea>
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" onclick="toggleStaffReplyBox({{ $rev->id }})" class="px-3 py-1 rounded-lg text-xs text-brand-muted hover:text-brand-text">Cancel</button>
                            <button type="button" onclick="submitStaffReply({{ $rev->id }})" class="px-3.5 py-1 bg-brand-primary text-white text-xs font-bold rounded-lg hover:bg-brand-deep transition">Save Response</button>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse

            {{-- 2. Dining Reviews --}}
            @forelse($foodReviews ?? [] as $fr)
                <div class="review-card bg-white p-5 rounded-2xl border border-gray-200 shadow-xs hover:shadow-sm transition space-y-4"
                     data-type="food"
                     data-status="{{ $fr->status }}">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-700 font-bold flex items-center justify-center text-sm border border-orange-200">
                                {{ strtoupper(substr($fr->order->guest->first_name ?? $fr->guest_name ?? 'F', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-brand-text">{{ $fr->order->guest->full_name ?? $fr->guest_name ?? 'Guest' }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200/60">Dining Review</span>
                                    @if($fr->menuItem)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">{{ $fr->menuItem->name }}</span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-brand-muted mt-0.5">
                                    Order #{{ $fr->order->order_number ?? $fr->food_order_id }} · {{ $fr->branch->name ?? 'Dining Hub' }}
                                    · {{ $fr->created_at ? $fr->created_at->format('d M Y, h:i A') : '' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex items-center text-amber-400 text-xs font-bold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/60">
                                @for($i = 1; $i <= 5; $i++)
                                    <i data-lucide="star" class="w-3.5 h-3.5 {{ $i <= ($fr->rating ?? 5) ? 'fill-amber-400 text-amber-400' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-1.5 text-amber-700">{{ $fr->rating }}/5</span>
                            </div>

                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                @if($fr->status === 'approved') bg-emerald-100 text-emerald-800 border border-emerald-200
                                @elseif($fr->status === 'pending') bg-amber-100 text-amber-800 border border-amber-200 animate-pulse
                                @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                                {{ $fr->status }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <p class="text-xs text-brand-muted leading-relaxed italic">
                            “{{ $fr->comment ?? 'Wonderful food and authentic flavor experience.' }}”
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            @if($fr->status !== 'approved')
                                <button type="button" onclick="moderateFoodReview({{ $fr->id }}, 'approve')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition cursor-pointer shadow-2xs">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Approve</span>
                                </button>
                            @endif

                            @if($fr->status !== 'rejected')
                                <button type="button" onclick="moderateFoodReview({{ $fr->id }}, 'reject')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition cursor-pointer">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    <span>Reject</span>
                                </button>
                            @endif
                        </div>

                        <button type="button" onclick="promoteReviewToTestimonial({{ $fr->id }}, 'food')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-500 text-white hover:bg-amber-600 transition cursor-pointer shadow-2xs">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            <span>Promote to Welcome Testimonial</span>
                        </button>
                    </div>
                </div>
            @empty
            @endforelse

            @if($reviews->isEmpty() && (!isset($foodReviews) || $foodReviews->isEmpty()))
                <div class="bg-white p-12 text-center rounded-2xl border border-gray-200 space-y-3">
                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 mx-auto flex items-center justify-center">
                        <i data-lucide="message-square-off" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-bold text-sm text-brand-text">No guest reviews recorded yet</h3>
                    <p class="text-xs text-brand-muted max-w-sm mx-auto">When guests checkout or submit food reviews, they will show here for your moderation and approval.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ================= TAB 2: WELCOME TESTIMONIALS CAROUSEL MANAGER ================= -->
    <div id="subtab-reviews-testimonials" class="space-y-6 hidden">

        <!-- Info & Live Preview Callout -->
        <div class="bg-gradient-to-r from-brand-deep to-brand-primary p-6 rounded-2xl text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wider bg-brand-accent/30 text-brand-accent border border-brand-accent/40">Dynamic Welcome Carousel</span>
                    <span class="text-xs text-white/70">· Auto-rotates every 6s on Homepage</span>
                </div>
                <h3 class="text-lg font-bold">Curate Your Welcome Page Stories</h3>
                <p class="text-xs text-white/80 leading-relaxed">
                    Set display orders with the up/down arrows or number input. Inactive testimonials are safely hidden without losing their data. Guests will experience these as a smooth sliding carousel.
                </p>
            </div>

            <button type="button" onclick="openAddTestimonialModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-accent text-brand-deep rounded-xl text-xs font-bold hover:bg-white transition cursor-pointer shrink-0 shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Add New Testimonial</span>
            </button>
        </div>

        <!-- Live Carousel Preview Card -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="play-circle" class="w-4 h-4 text-emerald-600"></i>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-brand-muted">Live Homepage Carousel Preview</h4>
                </div>
                <span class="text-[11px] text-brand-muted">Showing active items ({{ $testimonials->where('is_active', true)->count() }} active of {{ $testimonials->count() }} total)</span>
            </div>

            <div class="rounded-2xl bg-[#E9EFEA] p-6 border border-emerald-900/10 relative overflow-hidden shadow-inner">
                <div id="admin-carousel-preview-box" class="space-y-4">
                    @php
                        $firstActive = $testimonials->where('is_active', true)->first() ?? $testimonials->first();
                    @endphp
                    @if($firstActive)
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#0B5D4B]">Verified guest note</span>
                            <div class="flex text-amber-500 text-xs">
                                @for($i = 1; $i <= ($firstActive->rating ?? 5); $i++)
                                    ★
                                @endfor
                            </div>
                        </div>

                        <p class="font-serif text-xl sm:text-2xl text-[#063F34] leading-relaxed italic">
                            “{{ $firstActive->quote }}”
                        </p>

                        <div class="flex items-center justify-between pt-4 border-t border-[#063F34]/10">
                            <div>
                                <h5 class="font-bold text-xs text-[#063F34]">{{ $firstActive->guest_name }}</h5>
                                <p class="text-[10px] text-[#5A6B65]">{{ $firstActive->stay_title }}</p>
                            </div>
                            <span class="px-2.5 py-1 bg-white text-[#063F34] font-bold text-[10px] rounded-lg border border-gray-200 shadow-2xs">Live Order #{{ $firstActive->sort_order }}</span>
                        </div>
                    @else
                        <p class="text-xs text-brand-muted italic text-center py-4">No testimonials active. Add a testimonial below to display on the welcome page.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Testimonials List & Reordering Table -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-brand-canvas/50">
                <div class="flex items-center gap-2">
                    <i data-lucide="list-ordered" class="w-4 h-4 text-brand-primary"></i>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-brand-text">Testimonial Sequence & Pick Order</h4>
                </div>
                <div class="text-[11px] text-brand-muted">
                    <span>Use arrows or input to reorder · Lower number appears first in carousel</span>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($testimonials as $idx => $item)
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/80 transition" id="testimonial-row-{{ $item->id }}">
                        <!-- Left: Order Controls & Visibility -->
                        <div class="flex items-center gap-3 shrink-0">
                            <!-- Up / Down Order Buttons -->
                            <div class="flex flex-col gap-1 items-center">
                                <button type="button" onclick="moveTestimonialOrder({{ $item->id }}, 'up')" class="w-6 h-6 rounded bg-gray-100 hover:bg-brand-primary hover:text-white text-brand-muted flex items-center justify-center transition cursor-pointer" title="Move Up in Carousel">
                                    <i data-lucide="chevron-up" class="w-3.5 h-3.5"></i>
                                </button>
                                <input type="number" value="{{ $item->sort_order }}" onchange="updateTestimonialSortOrder({{ $item->id }}, this.value)" class="w-10 text-center text-xs font-bold py-0.5 border border-gray-300 rounded focus:ring-1 focus:ring-brand-primary focus:outline-none" title="Display sequence number" />
                                <button type="button" onclick="moveTestimonialOrder({{ $item->id }}, 'down')" class="w-6 h-6 rounded bg-gray-100 hover:bg-brand-primary hover:text-white text-brand-muted flex items-center justify-center transition cursor-pointer" title="Move Down in Carousel">
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                            <!-- Visibility Toggle Switch -->
                            <button type="button" onclick="toggleTestimonialActive({{ $item->id }})" class="p-1.5 rounded-lg border text-xs font-bold flex items-center gap-1.5 cursor-pointer transition {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-500 border-gray-200' }}" title="Toggle visibility in welcome carousel">
                                <i data-lucide="{{ $item->is_active ? 'eye' : 'eye-off' }}" class="w-3.5 h-3.5"></i>
                                <span>{{ $item->is_active ? 'Active' : 'Hidden' }}</span>
                            </button>
                        </div>

                        <!-- Center: Testimonial Details -->
                        <div class="flex-1 space-y-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h5 class="font-bold text-sm text-brand-text">{{ $item->guest_name }}</h5>
                                <span class="text-xs text-brand-muted">· {{ $item->stay_title }}</span>
                                @if($item->branch)
                                    <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">{{ $item->branch->name }}</span>
                                @else
                                    <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600">All Branches</span>
                                @endif
                                <div class="flex text-amber-400 text-xs font-bold">
                                    @for($s = 1; $s <= ($item->rating ?? 5); $s++)
                                        ★
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-brand-muted line-clamp-2 italic">“{{ $item->quote }}”</p>
                        </div>

                        <!-- Right: Edit / Delete Actions -->
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick='openEditTestimonialModal(@json($item))' class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-brand-canvas text-brand-text border border-gray-200 hover:bg-gray-100 transition cursor-pointer">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-brand-muted"></i>
                                <span>Edit</span>
                            </button>
                            <button type="button" onclick="deleteTestimonialItem({{ $item->id }}, '{{ addslashes($item->guest_name) }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition cursor-pointer">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-brand-muted text-xs">
                        No testimonials in database. Click "Add New Testimonial" above or promote an approved review!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: ADD NEW TESTIMONIAL ================= -->
<div id="modal-add-testimonial" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-brand-canvas/50">
            <div class="flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4 text-brand-primary"></i>
                <h3 class="font-bold text-sm text-brand-text">Add Welcome Page Testimonial</h3>
            </div>
            <button type="button" onclick="closeModal('modal-add-testimonial')" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-add-testimonial" onsubmit="submitAddTestimonial(event)" class="p-6 overflow-y-auto space-y-4">
            <div>
                <label class="block text-xs font-bold text-brand-text mb-1">Guest / Reviewer Name *</label>
                <input type="text" name="guest_name" required placeholder="e.g. Dr. Siddharth Verma" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Stay / Experience Title *</label>
                    <input type="text" name="stay_title" required placeholder="e.g. Heritage Forest Suite · Thekkady" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Star Rating (1-5)</label>
                    <select name="rating" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none">
                        <option value="5" selected>★★★★★ (5 Stars - Outstanding)</option>
                        <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                        <option value="3">★★★☆☆ (3 Stars - Good)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Associated Branch</label>
                    <select name="branch_id" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none">
                        <option value="">All Branches / Resort-wide</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Display Order (Pick Sequence)</label>
                    <input type="number" name="sort_order" value="{{ ($testimonials->max('sort_order') ?? 0) + 1 }}" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-text mb-1">Guest Quote / Review Note *</label>
                <textarea name="quote" rows="3" required placeholder="Type the authentic guest words here..." class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="add-is-active" name="is_active" value="1" checked class="rounded text-brand-primary focus:ring-brand-primary" />
                <label for="add-is-active" class="text-xs font-bold text-brand-text cursor-pointer">Immediately display in Welcome Page sliding carousel</label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('modal-add-testimonial')" class="px-4 py-2 rounded-xl text-xs font-semibold text-brand-muted hover:text-brand-text cursor-pointer">Cancel</button>
                <button type="submit" id="btn-save-testimonial" class="px-5 py-2 rounded-xl text-xs font-bold bg-brand-primary text-white hover:bg-brand-deep transition shadow-sm cursor-pointer">Save Testimonial</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: EDIT TESTIMONIAL ================= -->
<div id="modal-edit-testimonial" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-brand-canvas/50">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-primary"></i>
                <h3 class="font-bold text-sm text-brand-text">Edit Testimonial</h3>
            </div>
            <button type="button" onclick="closeModal('modal-edit-testimonial')" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-testimonial" onsubmit="submitEditTestimonial(event)" class="p-6 overflow-y-auto space-y-4">
            <input type="hidden" id="edit-testimonial-id" name="id" />

            <div>
                <label class="block text-xs font-bold text-brand-text mb-1">Guest / Reviewer Name *</label>
                <input type="text" id="edit-guest-name" name="guest_name" required class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Stay / Experience Title *</label>
                    <input type="text" id="edit-stay-title" name="stay_title" required class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Star Rating (1-5)</label>
                    <select id="edit-rating" name="rating" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none">
                        <option value="5">★★★★★ (5 Stars - Outstanding)</option>
                        <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                        <option value="3">★★★☆☆ (3 Stars - Good)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Associated Branch</label>
                    <select id="edit-branch-id" name="branch_id" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none">
                        <option value="">All Branches / Resort-wide</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Display Order (Pick Sequence)</label>
                    <input type="number" id="edit-sort-order" name="sort_order" class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-text mb-1">Guest Quote / Review Note *</label>
                <textarea id="edit-quote" name="quote" rows="3" required class="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:ring-1 focus:ring-brand-primary focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="edit-is-active" name="is_active" value="1" class="rounded text-brand-primary focus:ring-brand-primary" />
                <label for="edit-is-active" class="text-xs font-bold text-brand-text cursor-pointer">Visible in Welcome Page sliding carousel</label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('modal-edit-testimonial')" class="px-4 py-2 rounded-xl text-xs font-semibold text-brand-muted hover:text-brand-text cursor-pointer">Cancel</button>
                <button type="submit" id="btn-update-testimonial" class="px-5 py-2 rounded-xl text-xs font-bold bg-brand-primary text-white hover:bg-brand-deep transition shadow-sm cursor-pointer">Update Testimonial</button>
            </div>
        </form>
    </div>
</div>
