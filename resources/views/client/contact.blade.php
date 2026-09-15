@extends('layouts.customer')

@php
    $heroEyebrow = $contactContent['hero_eyebrow'] ?? '24/7 Front Desk & Concierge';
    $heroTitle = $contactContent['hero_title'] ?? 'Connect With Us';
    $heroSubtitle = $contactContent['hero_subtitle'] ?? 'Whether planning a bespoke anniversary retreat, private plantation dinner, or requiring travel assistance across Kerala.';
    $cleanCentralPhone = preg_replace('/[^0-9+]/', '', $centralPhone);
    $faqs = $contactContent['faqs'] ?? [];
@endphp

@section('title', 'Concierge & Contact | Krishna Resorts')

@section('content')
<!-- HERO SECTION -->
<div class="bg-forest text-paper py-8 sm:py-12 md:py-16 px-4 sm:px-6 relative overflow-hidden">
    <div class="absolute -right-20 -top-20 w-96 h-96 rounded-full bg-emerald-800/20 blur-3xl pointer-events-none"></div>
    <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-brass/10 blur-3xl pointer-events-none"></div>

    <div class="mx-auto max-w-[1480px] text-center relative z-10">
        <span class="eyebrow text-brass block mb-2">{{ $heroEyebrow }}</span>
        <h1 class="serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight">{{ $heroTitle }}</h1>
        <p class="text-xs sm:text-sm text-paper/75 max-w-2xl mx-auto mt-3 leading-relaxed">
            {{ $heroSubtitle }}
        </p>

        <!-- Quick contact pills -->
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3 text-xs">
            <a href="tel:{{ $cleanCentralPhone }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 text-paper border border-white/15 transition backdrop-blur-xs font-mono">
                <i data-lucide="phone-call" class="w-3.5 h-3.5 text-brass"></i>
                <span>{{ $centralPhone }}</span>
            </a>
            <a href="mailto:{{ $centralEmail }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 text-paper border border-white/15 transition backdrop-blur-xs">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-brass"></i>
                <span>{{ $centralEmail }}</span>
            </a>
            <button type="button" onclick="openChat()" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brass hover:bg-brass/90 text-forest font-bold transition shadow-xs cursor-pointer">
                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                <span>Live Concierge Chat</span>
            </button>
        </div>
    </div>
</div>

<div class="mx-auto max-w-[1380px] px-4 sm:px-6 py-8 sm:py-12">

    <!-- GLOBAL ERROR ALERT -->
    @if(isset($errors) && $errors->any())
    <div class="mb-8 p-5 rounded-2xl bg-red-50 border border-red-200 text-red-900 text-xs sm:text-sm flex items-start gap-3 shadow-xs">
        <div class="w-7 h-7 rounded-lg bg-red-600 text-white flex items-center justify-center shrink-0 mt-0.5">
            <i data-lucide="alert-circle" class="w-4 h-4"></i>
        </div>
        <div class="space-y-1">
            <span class="font-bold block">Please correct the following errors:</span>
            <ul class="list-disc list-inside text-xs text-red-800 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- SUCCESS BANNER (FROM STANDARD POST) -->
    @if(session('success'))
    <div id="static-success-box" class="mb-8 p-6 rounded-3xl bg-emerald-50 border border-emerald-200 text-forest text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-card">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-forest flex items-center justify-center shrink-0 text-brass">
                <i data-lucide="check-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm sm:text-base text-forest">Inquiry Received</h4>
                <p class="text-xs text-forest/75 mt-0.5">{{ session('success') }}</p>
                @if(session('ticket_number'))
                <span class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1 rounded-md bg-forest/10 font-mono text-xs font-bold text-forest border border-forest/20">
                    <i data-lucide="ticket" class="w-3.5 h-3.5 text-emerald-700"></i>
                    Ticket #{{ session('ticket_number') }}
                </span>
                @endif
            </div>
        </div>
        <button type="button" onclick="openChat()" class="px-4 py-2.5 rounded-xl bg-forest hover:bg-forest/90 text-paper font-bold text-xs flex items-center justify-center gap-2 transition shrink-0 cursor-pointer shadow-xs">
            <i data-lucide="message-square" class="w-4 h-4 text-brass"></i>
            <span>Continue in Live Chat</span>
        </button>
    </div>
    @endif

    <!-- DYNAMIC AJAX CONFIRMATION MODAL/CARD (HIDDEN BY DEFAULT) -->
    <div id="ajax-success-card" class="hidden mb-8 p-6 sm:p-8 rounded-3xl bg-[#063F34] text-paper soft-border shadow-card animate-fadeIn">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-brass">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="eyebrow text-brass">Inquiry Dispatched</span>
                    <h3 class="serif text-xl sm:text-2xl font-bold text-paper mt-0.5" id="ajax-success-title">Your message has been received</h3>
                    <p class="text-xs sm:text-sm text-paper/75 mt-1 max-w-xl" id="ajax-success-message">
                        Our guest relations team has logged your inquiry. Staff will review and reply promptly.
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/10 font-mono text-xs font-bold text-brass border border-white/15">
                            <i data-lucide="ticket" class="w-3.5 h-3.5"></i>
                            <span id="ajax-ticket-number">ENQ-XXXXX</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 text-[11px] font-semibold border border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Direct Concierge Logged</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                <button type="button" onclick="openChat()" class="px-5 py-3 rounded-xl bg-brass hover:bg-brass/90 text-forest font-bold text-xs flex items-center justify-center gap-2 transition cursor-pointer shadow-card">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Open Live Chat With Staff</span>
                </button>
                <button type="button" onclick="resetContactForm()" class="px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-paper font-semibold text-xs transition cursor-pointer">
                    Submit Another Inquiry
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

        <!-- LEFT: DYNAMIC INQUIRY FORM (7 COLS) -->
        <div class="lg:col-span-7 bg-white rounded-[28px] p-6 sm:p-8 soft-border shadow-card flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-4 mb-2">
                    <div>
                        <span class="eyebrow text-brass">Direct Concierge</span>
                        <h2 class="serif text-2xl sm:text-3xl font-bold text-forest mt-0.5">Send an Inquiry</h2>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[10px] font-bold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span> Desk Active
                    </span>
                </div>
                <p class="text-xs text-forest/60 mb-6">Our guest relations manager typically replies within 15 minutes during operating hours.</p>

                <form id="contact-form" action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Name & Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/70 mb-1">Your Full Name *</label>
                            <input type="text" name="name" id="field-name" required value="{{ old('name', auth()->user()?->name) }}" placeholder="e.g. Anand Menon" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden @error('name') border-red-500 bg-red-50/50 @enderror">
                            @error('name')
                                <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/70 mb-1">Email Address *</label>
                            <input type="email" name="email" id="field-email" required value="{{ old('email', auth()->user()?->email) }}" placeholder="anand@example.com" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden @error('email') border-red-500 bg-red-50/50 @enderror">
                            @error('email')
                                <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone & Topic -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/70 mb-1">Phone Number (Optional)</label>
                            <input type="tel" name="phone" id="field-phone" value="{{ old('phone', auth()->user()?->phone) }}" placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden @error('phone') border-red-500 bg-red-50/50 @enderror">
                            @error('phone')
                                <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/70 mb-1">Inquiry Topic *</label>
                            <select name="topic" id="field-topic" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                                <option value="general" {{ old('topic') === 'general' ? 'selected' : '' }}>General Concierge & Assistance</option>
                                <option value="booking_related" {{ old('topic') === 'booking_related' ? 'selected' : '' }}>Villa Booking & Retreat Reservations</option>
                                <option value="dining" {{ old('topic') === 'dining' ? 'selected' : '' }}>Plantation Dining & Special Meals</option>
                                <option value="spices" {{ old('topic') === 'spices' ? 'selected' : '' }}>Krishna Spices Farm Shop</option>
                                <option value="events" {{ old('topic') === 'events' ? 'selected' : '' }}>Weddings, Retreats & Private Celebrations</option>
                            </select>
                        </div>
                    </div>

                    <!-- Destination Branch Dropdown -->
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/70 mb-1">Destination Branch</label>
                        <select name="branch_id" id="field-branch" onchange="syncBranchSelection(this.value)" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                            <option value="">General Resort Concierge (All Branches)</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ (string)old('branch_id', request('branch_id')) === (string)$b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->city }})
                                </option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-forest/50 mt-1 block">Pick a specific sanctuary or send to Central Concierge.</span>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/70 mb-1">Subject / Inquiry Headline *</label>
                        <input type="text" name="subject" id="field-subject" required value="{{ old('subject') }}" placeholder="e.g. Special Anniversary Stay / Dietary Requirements" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden @error('subject') border-red-500 bg-red-50/50 @enderror">
                        @error('subject')
                            <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Message Details -->
                    <div>
                        <label class="block text-[10px] uppercase font-bold tracking-wider text-forest/70 mb-1">Message Details *</label>
                        <textarea name="message" id="field-message" rows="5" required placeholder="How can our concierge team assist you with your upcoming stay, dining, or bespoke requests?" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/30 soft-border text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden leading-relaxed @error('message') border-red-500 bg-red-50/50 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submit-btn" class="w-full py-3.5 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-card transition touch-tap flex items-center justify-center gap-2 cursor-pointer">
                        <span id="btn-text">Submit Inquiry to Concierge</span>
                        <i data-lucide="send" id="btn-icon" class="w-4 h-4 text-brass"></i>
                        <svg id="btn-spinner" class="hidden animate-spin h-4 w-4 text-paper" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Privacy & Peace Promise -->
            <div class="mt-6 pt-4 border-t border-forest/10 flex items-center justify-between text-[11px] text-forest/50">
                <span class="flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                    Your details are securely held under strict privacy protocols.
                </span>
                <span class="hidden sm:inline text-forest/40">Krishna Resorts Hospitality</span>
            </div>
        </div>

        <!-- RIGHT: INTERACTIVE BRANCH DIRECTORY & CONTACT CARDS (5 COLS) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- 24/7 CENTRAL CONCIERGE CARD -->
            <div class="bg-forest text-paper rounded-[28px] p-6 shadow-card space-y-4 relative overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-brass">
                            <i data-lucide="phone-call" class="w-5 h-5"></i>
                        </span>
                        <div>
                            <h3 class="serif text-xl font-bold">Direct Assistance</h3>
                            <span class="text-xs text-paper/60">Central Reservation & Front Desk</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-brass/20 text-brass border border-brass/30 text-[10px] font-bold uppercase tracking-wider">
                        24 / 7
                    </span>
                </div>
                
                <div class="pt-2 border-t border-white/10 space-y-3 text-xs">
                    <div class="flex items-center gap-3">
                        <i data-lucide="phone" class="w-4 h-4 text-brass shrink-0"></i>
                        <a href="tel:{{ $cleanCentralPhone }}" class="hover:text-brass transition font-mono">{{ $centralPhone }}</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <i data-lucide="mail" class="w-4 h-4 text-brass shrink-0"></i>
                        <a href="mailto:{{ $centralEmail }}" class="hover:text-brass transition">{{ $centralEmail }}</a>
                    </div>
                    <div class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-4 h-4 text-brass shrink-0 mt-0.5"></i>
                        <span class="text-paper/80 leading-relaxed">{{ $centralAddress }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i data-lucide="clock" class="w-4 h-4 text-brass shrink-0"></i>
                        <span class="text-paper/80">{{ $frontdeskHours }}</span>
                    </div>
                </div>

                <!-- 1-Click Live Chat launcher -->
                <button type="button" onclick="openChat()" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-brass hover:bg-brass/90 text-forest font-bold text-xs flex items-center justify-center gap-2 transition shadow-xs cursor-pointer">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                    <span>Launch Live Concierge Chat</span>
                </button>
            </div>

            <!-- INTERACTIVE SANCTUARIES & BRANCH DIRECTORY -->
            <div class="bg-white rounded-[28px] p-6 soft-border shadow-card space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="eyebrow text-brass">Resort Sanctuaries</span>
                        <h3 class="serif text-xl font-bold text-forest mt-0.5">Branch Directory</h3>
                    </div>
                    <span class="text-xs font-bold text-forest/40">{{ $branches->count() }} Properties</span>
                </div>

                <div class="space-y-3 divide-y divide-forest/10" id="branches-list">
                    @foreach($branches as $b)
                    @php
                        $cleanBranchPhone = $b->phone ? preg_replace('/[^0-9+]/', '', $b->phone) : null;
                        $encodedAddress = urlencode(($b->name . ', ' . ($b->address ?? '') . ', ' . $b->city . ', Kerala, India'));
                        $mapUrl = "https://www.google.com/maps/search/?api=1&query={$encodedAddress}";
                    @endphp
                    <div class="pt-3.5 first:pt-0 transition rounded-xl p-2.5 -mx-2.5 cursor-pointer hover:bg-paper/40" id="branch-card-{{ $b->id }}" onclick="selectBranchCard({{ $b->id }})">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-bold text-xs text-forest flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-700"></i>
                                    <span>{{ $b->display_name ?: $b->name }}</span>
                                </h4>
                                <p class="text-[11px] text-forest/60 mt-0.5 leading-relaxed">
                                    {{ $b->address ?? 'Tea Garden Estate' }}, {{ $b->city }}, {{ $b->state }}
                                </p>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-forest/5 text-forest/70 font-semibold shrink-0">
                                {{ $b->code }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs mt-2.5 gap-2 flex-wrap pt-2 border-t border-forest/5">
                            @if($b->phone)
                            <a href="tel:{{ $cleanBranchPhone }}" onclick="event.stopPropagation();" class="text-emerald-800 hover:text-emerald-900 flex items-center gap-1 text-[11px] font-semibold" title="Call {{ $b->name }}">
                                <i data-lucide="phone" class="w-3 h-3 text-emerald-600"></i>
                                <span>{{ $b->phone }}</span>
                            </a>
                            @else
                            <span class="text-[11px] text-forest/40 italic">Phone on request</span>
                            @endif

                            <div class="flex items-center gap-2" onclick="event.stopPropagation();">
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="px-2 py-1 rounded-lg bg-paper/60 hover:bg-paper text-forest text-[10px] font-semibold flex items-center gap-1 soft-border transition" title="Open in Google Maps">
                                    <i data-lucide="navigation" class="w-3 h-3 text-brass"></i>
                                    <span>Directions</span>
                                </a>
                                <button type="button" onclick="selectBranchCard({{ $b->id }});" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold transition">
                                    Select
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- CONCIERGE FAQS ACCORDION -->
            @if(!empty($faqs))
            <div class="bg-white rounded-[28px] p-6 soft-border shadow-card space-y-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-4 h-4 text-brass"></i>
                    <h3 class="serif text-lg font-bold text-forest">Common Inquiries</h3>
                </div>

                <div class="space-y-2 text-xs">
                    @foreach($faqs as $idx => $faq)
                    <div class="rounded-xl bg-paper/30 soft-border overflow-hidden">
                        <button type="button" onclick="toggleFaq({{ $idx }})" class="w-full p-3 text-left font-bold text-forest flex items-center justify-between gap-2 hover:text-emerald transition cursor-pointer">
                            <span>{{ $faq['question'] }}</span>
                            <i data-lucide="chevron-down" id="faq-chevron-{{ $idx }}" class="w-3.5 h-3.5 shrink-0 transition-transform"></i>
                        </button>
                        <div id="faq-answer-{{ $idx }}" class="hidden p-3 pt-0 text-forest/75 leading-relaxed text-[11px] border-t border-forest/5">
                            {{ $faq['answer'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Synchronize selection between Dropdown and Branch Directory Cards
    function syncBranchSelection(branchId) {
        document.querySelectorAll('[id^="branch-card-"]').forEach(card => {
            card.classList.remove('bg-emerald-50/80', 'ring-2', 'ring-emerald-600');
        });

        if (branchId) {
            const selectedCard = document.getElementById(`branch-card-${branchId}`);
            if (selectedCard) {
                selectedCard.classList.add('bg-emerald-50/80', 'ring-2', 'ring-emerald-600');
            }
        }
    }

    function selectBranchCard(branchId) {
        const selectEl = document.getElementById('field-branch');
        if (selectEl) {
            selectEl.value = branchId;
            syncBranchSelection(branchId);
            selectEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Toggle FAQ Accordion
    function toggleFaq(idx) {
        const ans = document.getElementById(`faq-answer-${idx}`);
        const chevron = document.getElementById(`faq-chevron-${idx}`);
        if (ans) {
            const isHidden = ans.classList.contains('hidden');
            ans.classList.toggle('hidden');
            if (chevron) {
                chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }
    }

    // Reset Contact Form for another submission
    function resetContactForm() {
        const form = document.getElementById('contact-form');
        if (form) form.reset();
        const successCard = document.getElementById('ajax-success-card');
        if (successCard) successCard.classList.add('hidden');
        syncBranchSelection('');
        window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
    }

    // Handle AJAX Form Submission with Instant Ticket Feedback
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize branch card selection if URL or old input had branch_id
        const initialBranch = document.getElementById('field-branch')?.value;
        if (initialBranch) {
            syncBranchSelection(initialBranch);
        }

        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', async function(e) {
                // If form has files or explicitly desires normal submit, let it proceed
                e.preventDefault();

                const submitBtn = document.getElementById('submit-btn');
                const btnText = document.getElementById('btn-text');
                const btnIcon = document.getElementById('btn-icon');
                const btnSpinner = document.getElementById('btn-spinner');

                if (submitBtn) submitBtn.disabled = true;
                if (btnText) btnText.textContent = 'Submitting inquiry...';
                if (btnIcon) btnIcon.classList.add('hidden');
                if (btnSpinner) btnSpinner.classList.remove('hidden');

                const formData = new FormData(contactForm);

                try {
                    const response = await fetch(contactForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Display confirmation card
                        const successCard = document.getElementById('ajax-success-card');
                        const ticketEl = document.getElementById('ajax-ticket-number');
                        const msgEl = document.getElementById('ajax-success-message');
                        const staticBox = document.getElementById('static-success-box');

                        if (staticBox) staticBox.classList.add('hidden');

                        if (ticketEl && data.enquiry) {
                            ticketEl.textContent = `Ticket #${data.enquiry.ticket_number}`;
                        }
                        if (msgEl && data.message) {
                            msgEl.textContent = data.message;
                        }

                        if (successCard) {
                            successCard.classList.remove('hidden');
                            successCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }

                        // Store guest details in localStorage for the Concierge Chat widget
                        const enteredName = formData.get('name');
                        const enteredPhone = formData.get('phone');
                        if (enteredName) localStorage.setItem('krishna_guest_name', enteredName);
                        if (enteredPhone) localStorage.setItem('krishna_guest_phone', enteredPhone);

                        // Reset form fields
                        contactForm.reset();
                        syncBranchSelection('');
                    } else if (response.status === 422) {
                        // Validation error
                        const errors = data.errors || {};
                        let errorMsg = 'Please check the entered fields:\n';
                        for (const key in errors) {
                            errorMsg += `• ${errors[key].join(', ')}\n`;
                        }
                        alert(errorMsg);
                    } else {
                        alert(data.message || 'Unable to submit inquiry. Please try again or call our front desk.');
                    }
                } catch (err) {
                    console.error('Contact form submission error:', err);
                    // Fallback to standard form submission if fetch fails
                    contactForm.submit();
                    return;
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                    if (btnText) btnText.textContent = 'Submit Inquiry to Concierge';
                    if (btnIcon) btnIcon.classList.remove('hidden');
                    if (btnSpinner) btnSpinner.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush
@endsection
