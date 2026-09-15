<!-- GUEST COMMUNICATIONS & DIRECT MESSAGING PLATFORM (ADM-19 & ADM-20) -->
<section id="messages" class="section space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3" id="comm-section-header">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl sm:text-2xl font-bold text-brand-text">Direct Guest Messaging Platform</h2>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span> Live DM
                </span>
            </div>
            <p class="hidden sm:block text-brand-muted text-xs mt-0.5">Exclusive staff-locking protocol, real-time message exchange, interactive recommendation cards, and upcoming stay alerts.</p>
        </div>
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 max-w-full no-scrollbar">
            <button onclick="openModal('modal-create-template')" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-surface border border-gray-200 text-brand-text hover:bg-gray-50 flex items-center gap-1.5 shadow-xs transition shrink-0">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-brand-primary"></i> + Quick Action
            </button>
            <button onclick="switchCommTab('messages')" id="comm-tab-btn-messages" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition shadow-xs shrink-0">
                Live Chats ({{ $enquiries->count() }})
            </button>
            <button onclick="switchCommTab('reviews')" id="comm-tab-btn-reviews" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">
                Guest Reviews ({{ $reviews->count() }})
            </button>
        </div>
    </div>

    <!-- TAB 1: DIRECT MESSAGING PLATFORM (ADM-19) -->
    <div id="comm-tab-messages" class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-4 h-[calc(100dvh-130px)] sm:h-[680px]">
        
        <!-- Thread List (4 Cols) -->
        <div id="comm-thread-list-col" class="lg:col-span-4 bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs flex flex-col overflow-hidden h-full">
            <!-- Filter / Search Header -->
            <div class="p-3 border-b border-gray-100 bg-gray-50 space-y-2 shrink-0">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-xs uppercase text-brand-muted tracking-wider">Conversations</span>
                    <span class="text-xs font-bold text-brand-primary">{{ $enquiries->count() }} active</span>
                </div>
                <div class="relative">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                    <input type="text" id="thread-search-input" onkeyup="filterEnquiryThreads()" placeholder="Filter by guest or ticket..." class="w-full text-xs pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary">
                </div>
            </div>

            <!-- Thread Items Scroll Area -->
            <div class="flex-1 overflow-y-auto divide-y divide-gray-100" id="enquiry-threads-list">
                @forelse($enquiries as $enq)
                @php
                    $isLockedByMe = $enq->isLockedBy(auth()->id());
                    $isLockedByOther = $enq->isLocked() && !$isLockedByMe && !auth()->user()->isSuperAdmin();
                @endphp
                <div class="p-3.5 hover:bg-brand-canvas/70 cursor-pointer transition relative {{ $loop->first ? 'bg-brand-canvas border-l-3 border-brand-primary' : '' }}" onclick="selectEnquiryThread({{ $enq->id }})" id="enq-thread-item-{{ $enq->id }}" data-search="{{ strtolower($enq->customer_name . ' ' . $enq->ticket_number . ' ' . ($enq->subject ?? '') . ' ' . ($enq->topic ?? '')) }}">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 truncate">
                            @if($enq->has_unread_messages)
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 animate-pulse" title="Unread Message"></span>
                            @endif
                            <span class="font-bold text-xs text-brand-text truncate">{{ $enq->customer_name }}</span>
                        </div>
                        <span class="text-[10px] text-brand-muted shrink-0">{{ $enq->last_message_at ? $enq->last_message_at->format('H:i') : $enq->created_at->format('d M') }}</span>
                    </div>

                    <div class="text-xs text-brand-primary font-medium mt-1 flex items-center gap-1.5 truncate">
                        <span class="font-mono text-[10px] text-gray-500">{{ $enq->ticket_number }}</span>
                        <span>&middot;</span>
                        <span class="truncate">{{ $enq->subject ?? 'Direct Chat' }}</span>
                    </div>

                    <!-- Topic & Source Pills -->
                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                        @if($enq->topic && $enq->topic !== 'general')
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200 uppercase">
                                {{ str_replace('_', ' ', $enq->topic) }}
                            </span>
                        @endif
                        <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $enq->branch ? $enq->branch->code : 'Resort Central' }}
                        </span>
                    </div>

                    <div class="text-[11px] text-brand-muted truncate mt-1">
                        {{ $enq->messages->last() ? $enq->messages->last()->message : 'No messages yet' }}
                    </div>

                    <!-- Lock & Status Row -->
                    <div class="mt-2.5 flex items-center justify-between gap-1.5 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            @if($enq->isLocked())
                                @if($isLockedByMe)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                        <i data-lucide="lock" class="w-3 h-3"></i> Locked to You
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 flex items-center gap-1" title="Locked by {{ $enq->lockedBy ? $enq->lockedBy->name : 'Staff' }}">
                                        <i data-lucide="lock" class="w-3 h-3 text-amber-700"></i> {{ $enq->lockedBy ? Str::limit($enq->lockedBy->name, 12) : 'Staff' }}
                                    </span>
                                @endif
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 flex items-center gap-1">
                                    <i data-lucide="unlock" class="w-3 h-3 text-gray-400"></i> Open
                                </span>
                            @endif

                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                {{ ucfirst($enq->status) }}
                            </span>
                        </div>
                        <span class="text-[10px] font-semibold text-brand-muted">{{ $enq->messages->count() }} msgs</span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-xs text-brand-muted">No active direct message threads.</div>
                @endforelse
            </div>
        </div>

        <!-- Chat View (8 Cols) - Modeled after Client Concierge Chat System -->
        @php $activeEnq = $enquiries->first(); @endphp
        <div class="hidden lg:flex lg:col-span-8 bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs flex-col overflow-hidden relative h-full" id="comm-chat-view-col">
            @if($activeEnq)
            @php
                $activeLockedByMe = $activeEnq->isLockedBy(auth()->id());
                $activeLockedByOther = $activeEnq->isLocked() && !$activeLockedByMe && !auth()->user()->isSuperAdmin();
            @endphp

            <!-- Client-Style Resort Concierge Header -->
            <div class="bg-[#063F34] text-white p-3.5 sm:p-4 shrink-0 shadow-xs space-y-2.5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button type="button" onclick="backToEnquiryThreads()" class="lg:hidden p-1.5 -ml-1 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition flex items-center justify-center cursor-pointer" title="Back to conversations">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </button>
                        <div class="relative shrink-0">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-[#C7A76A]">
                                <i data-lucide="message-circle" class="w-5 h-5"></i>
                            </span>
                            <span class="absolute -top-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-400 ring-2 ring-[#063F34] animate-pulse"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-sm sm:text-base text-white tracking-tight" id="chat-header-guest-name">{{ $activeEnq->customer_name }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-[#C7A76A]/20 text-[#C7A76A] border border-[#C7A76A]/30" id="chat-header-ticket">{{ $activeEnq->ticket_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold uppercase tracking-wider">Live Concierge</span>
                            </div>
                            <div class="text-[11px] text-white/70 mt-0.5 flex items-center gap-2 flex-wrap" id="chat-header-contact">
                                <a href="mailto:{{ $activeEnq->customer_email }}" class="hover:text-[#C7A76A] transition underline underline-offset-2">{{ $activeEnq->customer_email }}</a>
                                @if($activeEnq->customer_phone)
                                    <span>&middot;</span>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $activeEnq->customer_phone) }}" class="text-white font-medium hover:text-[#C7A76A] transition font-mono">{{ $activeEnq->customer_phone }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white/10 text-emerald-200 border border-white/15" id="chat-header-branch">
                            {{ $activeEnq->branch ? $activeEnq->branch->name : 'Resort Central' }}
                        </span>
                    </div>
                </div>

                <!-- Lock Status Bar -->
                <div class="pt-2 border-t border-white/10 flex items-center justify-between gap-3 text-xs" id="chat-lock-bar">
                    <div class="flex items-center gap-2" id="chat-lock-status-indicator">
                        @if($activeEnq->isLocked())
                            @if($activeLockedByMe)
                                <span class="inline-flex items-center gap-1 text-emerald-300 font-bold">
                                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                                    <span>Locked Exclusively to You</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-amber-300 font-bold">
                                    <i data-lucide="lock" class="w-4 h-4 text-amber-400"></i>
                                    <span>Locked Exclusively by {{ $activeEnq->lockedBy ? $activeEnq->lockedBy->name : 'Staff' }}</span>
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1 text-white/60 font-medium">
                                <i data-lucide="unlock" class="w-4 h-4 text-white/40"></i>
                                <span>Unassigned &middot; Any manager can claim</span>
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2" id="chat-lock-action-buttons">
                        @if(!$activeEnq->isLocked())
                            <button onclick="claimChat(currentEnquiryId)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                                <i data-lucide="lock" class="w-3.5 h-3.5"></i> Accept & Lock Chat
                            </button>
                        @elseif($activeLockedByMe)
                            <button onclick="relieveChat(currentEnquiryId)" class="px-3 py-1 bg-white/20 hover:bg-white/30 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition cursor-pointer">
                                <i data-lucide="unlock" class="w-3.5 h-3.5"></i> Relieve / Release Lock
                            </button>
                        @elseif(auth()->user()->isSuperAdmin())
                            <button onclick="forceUnlockChat(currentEnquiryId)" class="px-3 py-1 bg-red-600 hover:bg-red-500 text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition cursor-pointer">
                                <i data-lucide="key" class="w-3.5 h-3.5"></i> Force Unlock (Admin)
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upcoming / In-House Stay Alert Strip (Dynamic) -->
            @php
                $initialStay = $activeEnq ? $activeEnq->reservation : null;
                $initialInHouse = $initialStay && ($initialStay->status === 'checked_in' || ($initialStay->status === 'confirmed' && \Carbon\Carbon::parse($initialStay->check_in_date)->isToday()));
            @endphp
            <div id="chat-upcoming-stay-strip" class="{{ $initialStay ? '' : 'hidden' }} {{ $initialInHouse ? 'bg-emerald-500/15 border-b border-emerald-500/30 text-emerald-950' : 'bg-amber-500/15 border-b border-amber-500/30 text-amber-900' }} px-4 py-2 flex items-center justify-between text-xs shrink-0">
                <div class="flex items-center gap-2">
                    <i data-lucide="{{ $initialInHouse ? 'key' : 'calendar-clock' }}" class="w-4 h-4 {{ $initialInHouse ? 'text-emerald-700' : 'text-amber-700' }} shrink-0"></i>
                    <span id="chat-upcoming-stay-text">
                        @if($initialStay)
                            @if($initialInHouse)
                                <strong>Active In-House Guest:</strong> Checked in at <strong>{{ $initialStay->branch ? $initialStay->branch->name : 'Resort' }}</strong> &middot; Room: <strong class="text-emerald-900">{{ $initialStay->room ? $initialStay->room->room_number : 'Assigned' }}</strong> &middot; Booking <span class="font-mono font-bold">{{ $initialStay->booking_code }}</span> ({{ \Carbon\Carbon::parse($initialStay->check_in_date)->format('d M') }} &rarr; {{ \Carbon\Carbon::parse($initialStay->check_out_date)->format('d M Y') }}).
                            @else
                                <strong>Upcoming Stay Alert:</strong> Guest holds booking <span class="font-mono font-bold">{{ $initialStay->booking_code }}</span> at <strong>{{ $initialStay->branch ? $initialStay->branch->name : 'Resort' }}</strong> ({{ \Carbon\Carbon::parse($initialStay->check_in_date)->format('d M') }} &rarr; {{ \Carbon\Carbon::parse($initialStay->check_out_date)->format('d M Y') }}). SMS alert sent to Branch Manager.
                            @endif
                        @else
                            Upcoming Stay Detected: Guest has an active booking.
                        @endif
                    </span>
                </div>
            </div>

            <!-- Messages Stream Container (Warm Paper Style matching Client UI) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3.5 bg-[#F8F7F2]" id="enquiry-chat-messages">
                @if($activeLockedByOther)
                    <!-- Locked Protection Overlay -->
                    <div class="h-full flex flex-col items-center justify-center text-center p-8 space-y-3">
                        <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
                            <i data-lucide="lock" class="w-6 h-6"></i>
                        </div>
                        <h4 class="font-bold text-brand-text text-sm">Conversation Locked by {{ $activeEnq->lockedBy ? $activeEnq->lockedBy->name : 'Staff' }}</h4>
                        <p class="text-xs text-brand-muted max-w-sm">Under Krishna Resorts direct messaging protocols, only the committed staff member can read or respond to this conversation until they release the lock.</p>
                    </div>
                @else
                    @foreach($activeEnq->messages as $msg)
                    @php 
                        $isCustomer = $msg->sender_type === 'customer'; 
                        $payload = $msg->card_payload;
                        $cardsList = (!empty($payload['cards']) && is_array($payload['cards'])) ? $payload['cards'] : (!empty($payload) ? [$payload] : []);
                    @endphp
                    <div class="flex flex-col {{ $isCustomer ? 'items-start' : 'items-end' }} my-1">
                        @if(!$isCustomer)
                        <div class="flex items-center gap-1.5 mb-1 px-1">
                            <span class="text-[10px] font-bold text-brand-primary flex items-center gap-1">
                                <i data-lucide="user-check" class="w-3 h-3"></i> {{ $msg->user ? $msg->user->name : 'Staff Member' }}
                            </span>
                            <span class="text-[9px] text-brand-muted">&middot; {{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        @else
                        <div class="flex items-center gap-1.5 mb-1 px-1">
                            <span class="text-[10px] font-bold text-brand-text">{{ $activeEnq->customer_name }}</span>
                            <span class="text-[9px] text-brand-muted">&middot; {{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        @endif

                        <div class="max-w-[85%] sm:max-w-[78%] p-3.5 rounded-2xl text-xs leading-relaxed {{ $msg->is_internal_note ? 'bg-amber-50 border border-amber-200 text-amber-900 shadow-xs' : ($isCustomer ? 'bg-white border border-forest/10 text-brand-text rounded-tl-xs shadow-xs' : 'bg-[#0B5D4B] text-white rounded-tr-xs shadow-card') }}">
                            @if($msg->is_internal_note)
                                <div class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                    <i data-lucide="lock" class="w-3 h-3"></i> Internal Staff Note
                                </div>
                            @endif

                            @if($msg->target_room)
                                <div class="mb-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isCustomer ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-white/20 text-brass border border-white/30' }}">
                                        <i data-lucide="door-open" class="w-3 h-3"></i> Target: {{ $msg->target_room }}
                                    </span>
                                </div>
                            @endif

                            @if($msg->message)
                                <p class="leading-relaxed whitespace-pre-line">{{ $msg->message }}</p>
                            @endif

                            <!-- Interactive Card / Multi-Card Grid -->
                            @if(!empty($cardsList))
                            <div class="mt-2.5 {{ count($cardsList) > 1 ? 'grid grid-cols-1 sm:grid-cols-2 gap-2.5' : 'w-full' }}">
                                @foreach($cardsList as $cp)
                                <div class="bg-white rounded-2xl overflow-hidden border border-forest/15 shadow-xs text-brand-text flex flex-col justify-between">
                                    @if(!empty($cp['image_url']))
                                    <div class="h-28 w-full relative overflow-hidden bg-black/5">
                                        <img src="{{ $cp['image_url'] }}" alt="{{ $cp['title'] ?? '' }}" class="w-full h-full object-cover">
                                        @if(!empty($cp['badge']))
                                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#063F34]/90 text-white uppercase">{{ $cp['badge'] }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    <div class="p-3 flex-1 flex flex-col justify-between space-y-1.5">
                                        <div>
                                            <div class="flex items-start justify-between gap-1.5">
                                                <h4 class="font-bold text-xs leading-snug">{{ $cp['title'] ?? '' }}</h4>
                                                @if(!empty($cp['price']))
                                                <span class="font-bold text-xs text-brand-primary shrink-0">{{ $cp['price'] }}</span>
                                                @endif
                                            </div>
                                            @if(!empty($cp['subtitle']))
                                            <p class="text-[10px] text-brand-muted mt-0.5 line-clamp-2">{{ $cp['subtitle'] }}</p>
                                            @endif
                                        </div>
                                        @if(!empty($cp['link_url']))
                                        <a href="{{ $cp['link_url'] }}" target="_blank" class="mt-2 w-full py-1.5 px-3 rounded-xl bg-[#0B5D4B] hover:bg-[#063F34] text-white text-[10px] font-bold flex items-center justify-center gap-1.5 shadow-xs transition">
                                            <span>{{ $cp['action_text'] ?? 'View Details' }}</span>
                                            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-[#C7A76A]"></i>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <span class="text-[9px] text-brand-muted mt-0.5 px-1">{{ $msg->created_at->format('d M, H:i') }}</span>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Quick Category Suggestion Pills (Client-Style Direct Access) -->
            <div class="px-3 py-2 bg-white/90 border-t border-gray-100 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0">
                <span class="text-[9px] uppercase font-bold text-brand-muted tracking-wider shrink-0 mr-1">Quick:</span>
                <button type="button" onclick="openPickerWithTab('actions')" class="text-[10px] whitespace-nowrap bg-[#F8F7F2] hover:bg-emerald-50 hover:border-emerald-200 text-brand-text px-2.5 py-1 rounded-full border border-gray-200 font-semibold transition flex items-center gap-1 cursor-pointer">
                    ⚡ Quick Actions
                </button>
                <button type="button" onclick="openPickerWithTab('rooms')" class="text-[10px] whitespace-nowrap bg-[#F8F7F2] hover:bg-emerald-50 hover:border-emerald-200 text-brand-text px-2.5 py-1 rounded-full border border-gray-200 font-semibold transition flex items-center gap-1 cursor-pointer">
                    🏨 Rooms & Cottages
                </button>
                <button type="button" onclick="openPickerWithTab('branches')" class="text-[10px] whitespace-nowrap bg-[#F8F7F2] hover:bg-emerald-50 hover:border-emerald-200 text-brand-text px-2.5 py-1 rounded-full border border-gray-200 font-semibold transition flex items-center gap-1 cursor-pointer">
                    📍 Branches
                </button>
                <button type="button" onclick="openPickerWithTab('spices')" class="text-[10px] whitespace-nowrap bg-[#F8F7F2] hover:bg-emerald-50 hover:border-emerald-200 text-brand-text px-2.5 py-1 rounded-full border border-gray-200 font-semibold transition flex items-center gap-1 cursor-pointer">
                    🌿 Farm Spices
                </button>
                <button type="button" onclick="openPickerWithTab('dining')" class="text-[10px] whitespace-nowrap bg-[#F8F7F2] hover:bg-emerald-50 hover:border-emerald-200 text-brand-text px-2.5 py-1 rounded-full border border-gray-200 font-semibold transition flex items-center gap-1 cursor-pointer">
                    🍲 Dining Specials
                </button>
                <button type="button" onclick="openPickerWithTab('nearby')" class="text-[10px] whitespace-nowrap bg-[#F8F7F2] hover:bg-emerald-50 hover:border-emerald-200 text-brand-text px-2.5 py-1 rounded-full border border-gray-200 font-semibold transition flex items-center gap-1 cursor-pointer">
                    🗺️ Excursions
                </button>
            </div>

            <!-- Attached Multiple Cards Tray (Clean horizontal scrollable chip bar) -->
            <div id="attached-cards-tray" class="hidden px-3 py-2 bg-emerald-50/95 border-t border-emerald-200 shrink-0">
                <div class="flex items-center justify-between pb-1.5">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[9px] uppercase font-bold text-white bg-[#0B5D4B] px-2 py-0.5 rounded-full shadow-2xs" id="attached-cards-count">0 Cards</span>
                        <span class="text-[10px] sm:text-[11px] font-semibold text-emerald-950">Attached to send:</span>
                    </div>
                    <button type="button" onclick="clearCardAttachments()" class="text-[10px] sm:text-[11px] text-red-600 hover:text-red-800 font-bold cursor-pointer transition">
                        Clear all
                    </button>
                </div>
                <!-- Horizontal Scrollable Chips Container -->
                <div id="attached-cards-chips" class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar"></div>
            </div>

            <!-- Sleek Quick Action & Multi-Card Selector Bottom Sheet / Popover -->
            <div id="card-action-picker-popover" class="hidden absolute inset-x-0 bottom-0 z-30 max-h-[85%] sm:max-h-[420px] bg-white rounded-t-2xl shadow-2xl border-t-2 border-emerald-600 flex flex-col transition-all">
                <!-- Header with Counter and Done Button -->
                <div class="p-3 border-b border-gray-100 flex items-center justify-between shrink-0 bg-gray-50/90">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs text-brand-text">Attach Cards & Quick Actions</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800" id="picker-attached-badge">0 Attached</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openModal('modal-create-template')" class="hidden sm:inline-flex text-[10px] text-brand-primary font-bold hover:underline items-center gap-1 cursor-pointer">
                            <i data-lucide="plus" class="w-3 h-3"></i> Create Quick Action
                        </button>
                        <button type="button" onclick="toggleCardActionPicker()" class="px-3.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-lg shadow-xs cursor-pointer">
                            Done
                        </button>
                    </div>
                </div>

                <!-- Search & Category Tabs (Shrink-0) -->
                <div class="p-2.5 pb-2 space-y-2 shrink-0 bg-white border-b border-gray-100">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="picker-search-input" onkeyup="filterCardPickerItems()" placeholder="Search rooms, branches, spices, dining..." class="w-full text-xs pl-8 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary">
                    </div>

                    <!-- Category Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-0.5">
                        <button type="button" onclick="selectPickerTab('actions')" id="picker-tab-btn-actions" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-brand-primary text-white transition shrink-0">⚡ Quick Actions</button>
                        <button type="button" onclick="selectPickerTab('rooms')" id="picker-tab-btn-rooms" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">🏨 Rooms</button>
                        <button type="button" onclick="selectPickerTab('branches')" id="picker-tab-btn-branches" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">📍 Branches</button>
                        <button type="button" onclick="selectPickerTab('spices')" id="picker-tab-btn-spices" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">🌿 Spices</button>
                        <button type="button" onclick="selectPickerTab('dining')" id="picker-tab-btn-dining" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">🍲 Dining</button>
                        <button type="button" onclick="selectPickerTab('nearby')" id="picker-tab-btn-nearby" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">🗺️ Excursions</button>
                    </div>
                </div>

                <!-- Dynamic Item Grid -->
                <div id="picker-items-grid" class="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 overflow-y-auto flex-1 max-h-[280px]"></div>
            </div>

            <!-- Modern Input Composer (Pinned at bottom with safe mobile margins) -->
            <div class="p-2 sm:p-3 bg-white border-t border-gray-200 shrink-0 sticky bottom-0 z-20" id="chat-reply-container">
                <!-- Top Micro Bar for Internal Note & Status -->
                <div class="flex items-center justify-between px-1 pb-1.5 text-[11px]">
                    <label class="inline-flex items-center gap-1.5 text-brand-muted cursor-pointer hover:text-brand-text">
                        <input type="checkbox" id="reply-is-internal" class="rounded text-amber-600 focus:ring-0 cursor-pointer w-3.5 h-3.5">
                        <span class="font-semibold text-[10px] text-gray-600 flex items-center gap-1">
                            <svg class="w-3 h-3 text-amber-600 inline shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Internal Staff Note Only
                        </span>
                    <div class="flex items-center gap-2">
                        <select id="reply-target-room" class="hidden text-[10px] py-0.5 px-2 bg-gray-50 border border-gray-200 rounded text-gray-700 font-medium focus:outline-none focus:ring-1 focus:ring-brand-primary">
                            <option value="">Target: All Rooms</option>
                        </select>
                        <span class="text-[9px] text-brand-muted hidden xs:inline">Enter to send &middot; Shift+Enter for newline</span>
                    </div>
                </div>

                <!-- Main Composer Row -->
                <div class="flex items-end gap-1.5 sm:gap-2">
                    <!-- Plus Button for Quick Actions & Cards -->
                    <button type="button" onclick="toggleCardActionPicker()" id="chat-plus-btn" title="Add Quick Actions & Interactive Cards" class="h-10 w-10 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-800 flex items-center justify-center font-bold text-lg shadow-xs transition hover:scale-105 shrink-0 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </button>

                    <!-- Text Composer -->
                    <div class="flex-1 min-w-0">
                        <textarea id="reply-message-input" rows="1" onkeydown="handleReplyInputKey(event)" placeholder="Type reply or click + for cards..." class="w-full text-xs py-2.5 px-3 rounded-xl border border-gray-200 bg-[#F8F7F2] text-brand-text placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-brand-primary focus:bg-white resize-none transition max-h-24 leading-relaxed block" style="min-height: 40px;" {{ $activeLockedByOther ? 'disabled' : '' }}></textarea>
                    </div>

                    <!-- Send Button (Guaranteed to fit and show on all mobile screens) -->
                    <button type="button" onclick="sendEnquiryReply()" id="chat-send-btn" class="h-10 px-3.5 sm:px-4 rounded-xl bg-[#0B5D4B] hover:bg-[#063F34] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-card transition shrink-0 cursor-pointer disabled:opacity-50" {{ $activeLockedByOther ? 'disabled' : '' }}>
                        <span>Send</span>
                        <svg class="w-3.5 h-3.5 text-[#C7A76A]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
            @else
            <div class="flex-1 flex flex-col items-center justify-center text-brand-muted text-xs p-8 space-y-2">
                <i data-lucide="message-square-off" class="w-8 h-8 text-gray-300"></i>
                <p>No conversation thread selected.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: REVIEWS MODERATION (ADM-20) -->
    <div id="comm-tab-reviews" class="hidden space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($reviews as $rev)
            <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-4 space-y-3 shadow-xs" id="review-card-{{ $rev->id }}">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="font-bold text-brand-text">{{ $rev->guest ? $rev->guest->full_name : 'Guest' }}</div>
                        <div class="text-xs text-brand-muted">{{ $rev->stay_summary }} · {{ $rev->branch ? $rev->branch->name : '' }}</div>
                    </div>
                    @if($rev->verified_stay)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                            <i data-lucide="shield-check" class="w-3 h-3"></i> Verified Stay
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-1 text-amber-500">
                    @for($s = 1; $s <= 5; $s++)
                        <i data-lucide="star" class="w-4 h-4 {{ $s <= $rev->rating ? 'fill-current' : 'text-gray-300' }}"></i>
                    @endfor
                </div>

                <div>
                    <h4 class="font-semibold text-sm text-brand-text">"{{ $rev->title }}"</h4>
                    <p class="text-xs text-brand-muted mt-1 leading-relaxed">{{ $rev->comment }}</p>
                </div>

                @if($rev->staff_reply)
                <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-100 text-xs">
                    <span class="font-bold text-brand-primary block text-[10px] uppercase">Staff Response:</span>
                    <p class="text-brand-muted mt-0.5">{{ $rev->staff_reply }}</p>
                </div>
                @endif

                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase text-brand-muted">Status: <strong class="text-brand-text">{{ $rev->status }}</strong></span>
                    <div class="space-x-1">
                        <button onclick="moderateReview({{ $rev->id }}, 'approved')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold transition">Approve</button>
                        <button onclick="moderateReview({{ $rev->id }}, 'hidden')" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-semibold transition">Hide</button>
                        <button onclick="moderateReview({{ $rev->id }}, 'rejected')" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold transition">Reject</button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-2 py-12 text-center text-brand-muted">No reviews awaiting moderation.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- CREATE CUSTOM QUICK ACTION MODAL -->
<div id="modal-create-template" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-base text-brand-text">Create Quick Action Template</h3>
                <p class="text-xs text-brand-muted">Add predefined responses or instant rich card recommendations for the team.</p>
            </div>
            <button onclick="closeModal('modal-create-template')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form onsubmit="submitCreateTemplate(event)" class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-brand-text mb-1">Template Title</label>
                <input type="text" id="tpl-title" required placeholder="e.g. Munnar Heritage Villa Recommendation" class="w-full text-xs p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Category</label>
                    <select id="tpl-category" class="w-full text-xs p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary">
                        <option value="General">General FAQ</option>
                        <option value="Branches">Branch Details</option>
                        <option value="Rooms">Rooms & Cottages</option>
                        <option value="Spices">Spices Shop</option>
                        <option value="Dining">Plantation Dining</option>
                        <option value="Excursions">Excursions & Nearby</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-brand-text mb-1">Action Type</label>
                    <select id="tpl-type" onchange="toggleTemplateEntityPicker(this.value)" class="w-full text-xs p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary">
                        <option value="text">Standard Text</option>
                        <option value="branch">Branch Card</option>
                        <option value="room">Room Card</option>
                        <option value="spice">Spice Card</option>
                        <option value="dining">Dining Card</option>
                        <option value="nearby">Nearby Excursion Card</option>
                    </select>
                </div>
            </div>

            <div id="tpl-entity-selector-container" class="hidden">
                <label class="block text-xs font-bold text-brand-text mb-1">Target Item</label>
                <select id="tpl-entity-id" class="w-full text-xs p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary">
                    <option value="">-- Choose Item --</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-text mb-1">Message Text</label>
                <textarea id="tpl-message" rows="3" placeholder="Enter message to send or card accompaniment text..." class="w-full text-xs p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-brand-primary"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-create-template')" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold rounded-lg shadow-xs">Save Quick Action</button>
            </div>
        </form>
    </div>
</div>
