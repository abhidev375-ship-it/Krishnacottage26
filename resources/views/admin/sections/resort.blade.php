<!-- RESORT CONTENT & LOCATIONS SECTION (ADM-09, ADM-10, ADM-11, ADM-12) -->
<section id="branches" class="section space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-brand-text">Resort Branches & Content</h2>
            <p class="text-brand-muted text-xs mt-0.5">Dynamic branch destinations, facilities & experiences, photo albums, and nearby points of interest.</p>
        </div>
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 max-w-full no-scrollbar">
            <button onclick="switchResortTab('branches')" id="resort-tab-btn-branches" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition shrink-0">Branches ({{ $branches->count() }})</button>
            <button onclick="switchResortTab('facilities')" id="resort-tab-btn-facilities" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">Facilities ({{ $facilities->count() }})</button>
            <button onclick="switchResortTab('gallery')" id="resort-tab-btn-gallery" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">Gallery Albums ({{ $galleryAlbums->count() }})</button>
            <button onclick="switchResortTab('nearby')" id="resort-tab-btn-nearby" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">Nearby ({{ $nearbyLocations->count() }})</button>
            <button onclick="switchResortTab('taxi')" id="resort-tab-btn-taxi" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1"><i data-lucide="car" class="w-3.5 h-3.5 text-emerald-600"></i> Taxi Requests ({{ $taxiRequests->count() }})</button>
        </div>
    </div>

    <!-- SUB-TAB 1: DYNAMIC BRANCHES (ADM-09) -->
    <div id="resort-tab-branches" class="space-y-4">
        <div class="flex justify-between items-center">
            <span class="text-xs text-brand-muted font-medium">Manage active resort branches & destinations</span>
            <button onclick="openModal('modal-branch')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Branch
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($branches as $b)
            <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-md transition group">
                <div class="h-44 w-full bg-gray-100 relative overflow-hidden">
                    @if($b->cover_image_url || $b->hero_image_url)
                        <img src="{{ $b->cover_image_url ?: $b->hero_image_url }}" alt="{{ $b->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                            <i data-lucide="image" class="w-8 h-8"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/30"></div>
                    <div class="absolute top-2.5 left-2.5 bg-black/60 text-white px-2 py-0.5 rounded text-[10px] font-bold font-mono">
                        {{ $b->code }}
                    </div>
                    <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded text-[10px] font-bold {{ $b->status === 'active' ? 'bg-emerald-500 text-white' : ($b->status === 'maintenance' ? 'bg-amber-500 text-white' : 'bg-gray-500 text-white') }}">
                        {{ ucfirst($b->status) }}
                    </div>
                    <div class="absolute bottom-2.5 left-2.5 right-2.5 text-white">
                        <span class="text-[10px] uppercase font-bold text-white/70 block">{{ $b->city }}, {{ $b->state }}</span>
                        <h4 class="font-bold text-sm text-white truncate">{{ $b->name }}</h4>
                    </div>
                </div>
                <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="text-xs text-brand-primary font-semibold">{{ $b->display_name ?: $b->name }}</div>
                        <p class="text-xs text-brand-muted mt-1 leading-relaxed line-clamp-2">{{ $b->tagline ?: ($b->city . ' resort destination') }}</p>
                    </div>

                    <div class="pt-2 border-t border-gray-100 text-xs text-brand-muted space-y-1.5">
                        @if($b->phone)
                        @php $cleanBPhone = preg_replace('/[^0-9+]/', '', $b->phone); @endphp
                        <div class="flex items-center justify-between">
                            <a href="tel:{{ $cleanBPhone }}" class="flex items-center gap-1.5 text-emerald-700 hover:text-emerald-900 hover:underline font-semibold" title="Call Branch Mobile">
                                <i data-lucide="phone-call" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                <span>{{ $b->phone }}</span>
                            </a>
                            <span class="text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-medium">Customer Dial</span>
                        </div>
                        @else
                        <div class="flex items-center gap-1.5 text-amber-600 italic text-[11px]">
                            <i data-lucide="phone-off" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                            <span>No phone added (call button disabled)</span>
                        </div>
                        @endif
                        @if($b->email)
                        <div class="flex items-center gap-1.5"><i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i> {{ $b->email }}</div>
                        @endif
                        <div class="flex items-center gap-1.5 pt-1 text-[11px] font-medium text-brand-primary">
                            <i data-lucide="bed-double" class="w-3.5 h-3.5"></i>
                            <span>{{ $b->roomTypes->count() }} Room Suites &middot; {{ $b->rooms->count() }} Physical Rooms</span>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="openEditBranchModal({{ json_encode($b) }})" class="px-2.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-brand-text text-xs font-semibold flex items-center gap-1 transition">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                            </button>
                            @if($b->phone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $b->phone) }}" class="px-2.5 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold flex items-center gap-1 transition" title="Call Branch Front Desk: {{ $b->phone }}">
                                <i data-lucide="phone" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Call</span>
                            </a>
                            @endif
                        </div>
                        <button type="button" onclick="toggleBranchStatus({{ $b->id }})" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $b->status === 'active' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                            {{ $b->status === 'active' ? 'Disable' : 'Activate' }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- SUB-TAB 2: FACILITIES & EXPERIENCES (ADM-10 - strictly no pool) -->
    <div id="resort-tab-facilities" class="hidden space-y-4">
        <!-- Sub-view Navigation & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-brand-surface p-3 rounded-xl border border-gray-200/70 shadow-xs">
            <div class="flex items-center gap-2">
                <button type="button" onclick="switchFacilitySubView('directory')" id="btn-fac-subview-directory" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white shadow-xs transition">
                    Facilities Directory ({{ $facilities->count() }})
                </button>
                <button type="button" onclick="switchFacilitySubView('bookings')" id="btn-fac-subview-bookings" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition flex items-center gap-1.5">
                    <span>Bookings Tracker</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ isset($facilityBookings) && $facilityBookings->where('status', 'pending')->count() > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-700' }}">
                        {{ isset($facilityBookings) ? $facilityBookings->count() : 0 }}
                    </span>
                    @if(isset($facilityBookings) && $facilityBookings->where('status', 'pending')->count() > 0)
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    @endif
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="openModal('modal-facility')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Facility
                </button>
            </div>
        </div>

        <!-- SUBVIEW A: FACILITIES DIRECTORY -->
        <div id="fac-subview-directory" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($facilities as $f)
                <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden flex flex-col justify-between">
                    <div class="h-44 w-full bg-gray-100 relative overflow-hidden">
                        @if($f->image_url)
                            <img src="{{ $f->image_url }}" alt="{{ $f->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                <i data-lucide="sparkles" class="w-8 h-8 opacity-40"></i>
                            </div>
                        @endif

                        <!-- Branch Badge: Specific Branch Name or All Branches -->
                        <span class="absolute top-2.5 left-2.5 bg-black/75 backdrop-blur-xs text-white px-2.5 py-0.5 rounded-full text-[10px] font-bold shadow-xs">
                            <i data-lucide="{{ $f->branch ? 'map-pin' : 'globe' }}" class="w-3 h-3 inline mr-0.5 text-brand-accent"></i>
                            {{ $f->branch ? $f->branch->name : 'All Branches' }}
                        </span>

                        <!-- Rate / Bookability Pill -->
                        <div class="absolute top-2.5 right-2.5 flex flex-col items-end gap-1">
                            @if($f->is_bookable)
                                <span class="bg-emerald-700 text-white px-2.5 py-0.5 rounded-full text-[10px] font-bold shadow-xs">
                                    {{ $f->rate > 0 ? '₹' . number_format($f->rate, 0) : 'Complimentary' }}
                                </span>
                            @else
                                <span class="bg-gray-800/80 text-gray-200 px-2.5 py-0.5 rounded-full text-[10px] font-medium">
                                    Showcase Only
                                </span>
                            @endif

                            @if($f->has_scheduling)
                                <span class="bg-blue-700 text-white px-2 py-0.5 rounded-full text-[9px] font-bold shadow-xs">
                                    Slot Allocation
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-1">
                                <h3 class="font-bold text-base text-brand-text">{{ $f->name }}</h3>
                                @if($f->category)
                                    <span class="text-[9px] uppercase font-bold text-brand-muted bg-gray-100 px-2 py-0.5 rounded">
                                        {{ $f->category }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-brand-muted mt-1 leading-relaxed line-clamp-2">
                                {{ $f->short_description ?: $f->description }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-brand-muted">
                            <span><i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1 text-emerald-600"></i> {{ $f->operating_hours ?? 'Open Daily' }}</span>
                            <span class="font-semibold text-brand-primary">
                                {{ $f->bookings ? $f->bookings->count() : 0 }} Booked
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- SUBVIEW B: FACILITY BOOKINGS TRACKER -->
        <div id="fac-subview-bookings" class="hidden space-y-4">
            <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-gray-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-sm text-brand-text">In-House Guest Experience &amp; Facility Bookings</h3>
                        <p class="text-xs text-brand-muted mt-0.5">Review booking requests, allocate designated time periods, and sync folio charges.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <select id="filter-fac-booking-status" onchange="filterFacilityBookings()" class="px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs text-brand-text focus:ring-1 focus:ring-brand-primary">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending Allocation</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="table-facility-bookings">
                        <thead class="bg-gray-50 border-b border-gray-200 text-brand-muted uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Booking ID</th>
                                <th class="py-3 px-4">Guest &amp; Room</th>
                                <th class="py-3 px-4">Experience / Facility</th>
                                <th class="py-3 px-4">Date &amp; Guests</th>
                                <th class="py-3 px-4">Rate &amp; Total</th>
                                <th class="py-3 px-4">Allocated Time Slot</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(isset($facilityBookings) && $facilityBookings->isNotEmpty())
                                @foreach($facilityBookings as $fb)
                                @php
                                    $fbStatusBadge = match($fb->status) {
                                        'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'completed' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'cancelled' => 'bg-gray-100 text-gray-600 border-gray-300',
                                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition fac-booking-row" data-status="{{ $fb->status }}">
                                    <td class="py-3 px-4 font-mono font-bold text-brand-primary">
                                        #FB-{{ $fb->id }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-brand-text">{{ $fb->guest ? $fb->guest->full_name : 'Guest' }}</div>
                                        <div class="text-[11px] text-brand-muted flex items-center gap-1 mt-0.5">
                                            <i data-lucide="bed-double" class="w-3 h-3 text-emerald-600"></i>
                                            <span>Room {{ $fb->reservation && $fb->reservation->room ? $fb->reservation->room->room_number : 'In-House' }}</span>
                                            <span class="opacity-40">&bull;</span>
                                            <span>{{ $fb->branch ? $fb->branch->name : 'Resort' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-brand-text">{{ $fb->facility ? $fb->facility->name : 'Facility' }}</div>
                                        @if($fb->notes)
                                            <div class="text-[10px] text-brand-muted italic mt-0.5">"{{ Str::limit($fb->notes, 35) }}"</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-brand-text">{{ \Carbon\Carbon::parse($fb->booking_date)->format('d M Y') }}</div>
                                        <div class="text-[11px] text-brand-muted">{{ $fb->guests_count }} {{ Str::plural('Guest', $fb->guests_count) }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-brand-text">{{ $fb->total_amount > 0 ? '₹' . number_format($fb->total_amount, 2) : 'Complimentary' }}</div>
                                        @if($fb->folio_charge_id)
                                            <span class="text-[9px] uppercase font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Folio Billed</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($fb->allocated_time_slot)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <i data-lucide="clock" class="w-3 h-3 text-emerald-600"></i>
                                                {{ $fb->allocated_time_slot }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                <i data-lucide="alert-circle" class="w-3 h-3 text-amber-600"></i>
                                                Needs Allocation
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $fbStatusBadge }}">
                                            {{ ucfirst($fb->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <button type="button" 
                                                onclick="openAllocateSlotModal({{ $fb->id }}, '{{ addslashes($fb->guest ? $fb->guest->full_name : 'Guest') }}', '{{ addslashes($fb->facility ? $fb->facility->name : 'Facility') }}', '{{ $fb->allocated_time_slot ?? '' }}', '{{ $fb->status }}', {{ (float)$fb->total_amount }}, {{ $fb->folio_charge_id ? 'true' : 'false' }})" 
                                                class="px-2.5 py-1 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs transition inline-flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-3 h-3"></i>
                                            <span>Allocate Slot</span>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-brand-muted text-xs">
                                        No facility bookings found. In-house guests can book experiences directly from their customer dashboard.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 3: GALLERY ALBUMS (ADM-11) -->
    <div id="resort-tab-gallery" class="hidden space-y-4">
        <!-- TOP TOOLBAR: BRANCH FILTERS & CREATE BUTTON -->
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-3 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5">
                <span class="text-xs font-bold text-brand-muted uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> Branch:
                </span>
                <button type="button" onclick="filterAdminGalleryByBranch('all')" data-branch-id="all" class="gallery-branch-tab-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white shadow-xs transition shrink-0">
                    All Branches
                </button>
                @foreach($branches as $b)
                    <button type="button" onclick="filterAdminGalleryByBranch('{{ $b->id }}')" data-branch-id="{{ $b->id }}" class="gallery-branch-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-brand-text bg-white border border-gray-200 hover:bg-gray-50 transition shrink-0">
                        {{ $b->name }}
                    </button>
                @endforeach
                <button type="button" onclick="filterAdminGalleryByBranch('none')" data-branch-id="none" class="gallery-branch-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-brand-text bg-white border border-gray-200 hover:bg-gray-50 transition shrink-0">
                    Resort-Wide
                </button>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="openModal('modal-gallery-album')" class="px-3.5 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Create Album
                </button>
            </div>
        </div>

        <!-- ALBUM CARDS GRID -->
        <div id="admin-gallery-albums-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($galleryAlbums as $album)
            <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden flex flex-col admin-gallery-album-card group hover:shadow-md transition" id="admin-gallery-album-{{ $album->id }}" data-branch-id="{{ $album->branch_id ?? 'none' }}">
                <div class="h-48 w-full bg-gray-100 relative overflow-hidden cursor-pointer" onclick="openAlbumPhotoManager({{ $album->id }})">
                    @if($album->cover_image_url)
                        <img src="{{ $album->cover_image_url }}" alt="{{ $album->name ?? 'Album Cover' }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-brand-muted">
                            <i data-lucide="image" class="w-8 h-8 opacity-40 mb-1"></i>
                            <span class="text-[11px]">No cover image</span>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-70"></div>
                    
                    <!-- Top Badges -->
                    <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/90 text-brand-text backdrop-blur-xs shadow-xs">
                            {{ $album->branch ? $album->branch->name : 'Resort-Wide' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-black/60 text-white backdrop-blur-xs capitalize">
                            {{ $album->category }}
                        </span>
                    </div>

                    <div class="absolute top-2.5 right-2.5">
                        @if($album->is_published)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white shadow-xs">Published</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-500 text-white">Draft</span>
                        @endif
                    </div>

                    <!-- Photo Count -->
                    <div class="absolute bottom-2.5 right-2.5 bg-black/75 text-white px-2.5 py-0.5 rounded-full text-xs font-bold flex items-center gap-1 backdrop-blur-xs">
                        <i data-lucide="images" class="w-3 h-3 text-amber-400"></i>
                        <span id="album-card-count-{{ $album->id }}">{{ $album->images->count() }}</span> Photos
                    </div>
                </div>

                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <h3 class="font-bold text-sm text-brand-text line-clamp-1" title="{{ $album->name }}">
                            {{ $album->name ?: 'Untitled Album' }}
                        </h3>
                        <p class="text-xs text-brand-muted line-clamp-2 mt-1 leading-relaxed">
                            {{ $album->description ?: 'No description provided.' }}
                        </p>
                    </div>

                    <!-- Card Actions -->
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="openAlbumPhotoManager({{ $album->id }})" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center gap-1.5 transition">
                            <i data-lucide="folder-open" class="w-3.5 h-3.5"></i> Manage Photos
                        </button>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick='openEditAlbumModal(@json($album))' class="p-1.5 rounded-lg text-gray-500 hover:text-brand-primary hover:bg-gray-100 transition" title="Edit Album Details">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </button>
                            <button type="button" onclick="deleteGalleryAlbum({{ $album->id }}, '{{ addslashes($album->name ?? 'Album') }}')" class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete Album">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center bg-brand-surface rounded-xl border border-gray-200/70 p-6">
                <i data-lucide="image" class="w-10 h-10 text-brand-muted/50 mx-auto mb-2"></i>
                <h4 class="text-sm font-bold text-brand-text">No Gallery Albums Yet</h4>
                <p class="text-xs text-brand-muted mt-1 max-w-sm mx-auto">Create photography albums to showcase your suites, heritage architecture, and lush nature walks to guests.</p>
                <button onclick="openModal('modal-gallery-album')" class="mt-4 px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold inline-flex items-center gap-1.5 shadow-xs transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Create First Album
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- SUB-TAB 4: NEARBY LOCATIONS (ADM-12) -->
    <div id="resort-tab-nearby" class="hidden bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
        <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <span class="text-xs text-brand-text font-bold">Curated Local Discoveries & Sightseeing Spots</span>
                <p class="text-[11px] text-brand-muted">Configure destinations, toggle guest visibility, and manage resort cab travel availability.</p>
            </div>
            <button onclick="openModal('modal-nearby')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Location
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Location & Landmark</th>
                        <th class="px-4 py-3">Branch</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Distance & Time</th>
                        <th class="px-4 py-3 text-center">Guest Visibility</th>
                        <th class="px-4 py-3 text-center">Cab Service</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($nearbyLocations as $loc)
                    <tr class="hover:bg-brand-canvas/60 transition">
                        <td class="px-4 py-3.5 font-bold text-brand-text">
                            <div class="flex items-center gap-3">
                                @if($loc->image_url)
                                    <img src="{{ $loc->image_url }}" alt="{{ $loc->name }}" class="w-9 h-9 rounded-lg object-cover border border-gray-200 shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-mint text-emerald-700 flex items-center justify-center shrink-0">
                                        <i data-lucide="compass" class="w-4 h-4"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-brand-text">{{ $loc->name }}</div>
                                    <div class="text-[11px] text-brand-muted max-w-xs truncate">{{ $loc->description ?: 'Scenic destination' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-brand-muted font-medium">{{ $loc->branch ? $loc->branch->name : 'All Branches' }}</td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                {{ str_replace('wildlife', 'Wildlife Reserve', $loc->category) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-brand-text">
                            <div>{{ $loc->distance_km }} km</div>
                            <div class="text-[10px] text-brand-muted font-normal">{{ $loc->travel_time ?: 'Approx drive' }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <button type="button" onclick="toggleNearbyAvailability({{ $loc->id }})" 
                                    id="btn-nearby-avail-{{ $loc->id }}"
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold transition {{ $loc->is_available ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}" 
                                    title="Click to toggle guest visibility">
                                {{ $loc->is_available ? '✓ Open & Visible' : '✗ Temporarily Closed' }}
                            </button>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <button type="button" onclick="toggleNearbyTaxi({{ $loc->id }})" 
                                    id="btn-nearby-taxi-{{ $loc->id }}"
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold transition {{ $loc->is_taxi_available ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}" 
                                    title="Click to toggle cab booking availability">
                                {{ $loc->is_taxi_available ? '🚕 Cab Available' : 'No Cab' }}
                            </button>
                        </td>
                        <td class="px-4 py-3.5 text-right space-x-1">
                            <button type="button" onclick="openEditNearbyModal({{ json_encode($loc) }})" class="p-1.5 text-brand-muted hover:text-brand-primary rounded-md hover:bg-gray-100 transition" title="Edit Discovery">
                                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                            </button>
                            <button type="button" onclick="deleteNearby({{ $loc->id }}, '{{ addslashes($loc->name) }}')" class="p-1.5 text-rose-500 hover:text-rose-700 rounded-md hover:bg-rose-50 transition" title="Archive Discovery">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-brand-muted">No nearby locations added yet. Click "Add Location" to create one.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SUB-TAB 5: TAXI & EXCURSION REQUESTS TRACKER -->
    <div id="resort-tab-taxi" class="hidden bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
        <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <span class="text-xs text-brand-text font-bold">In-House Guest Cab & Excursion Requests</span>
                <p class="text-[11px] text-brand-muted">Review requested destinations, custom stops, quote fares, assign drivers, and post charges to room folios.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                {{ $taxiRequests->count() }} Total Requests
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Reference & Date</th>
                        <th class="px-4 py-3">Guest & Room</th>
                        <th class="px-4 py-3">Branch</th>
                        <th class="px-4 py-3">Requested Destinations & Route</th>
                        <th class="px-4 py-3">Passengers</th>
                        <th class="px-4 py-3">Quoted Fare</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($taxiRequests as $req)
                    <tr class="hover:bg-brand-canvas/60 transition">
                        <td class="px-4 py-3.5 font-mono">
                            <div class="font-bold text-brand-primary">#{{ $req->booking_reference }}</div>
                            <div class="text-[10px] text-brand-muted">{{ date('d M Y', strtotime($req->pickup_date)) }} &middot; {{ $req->pickup_time ?: 'Morning' }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-brand-text">{{ $req->guest ? $req->guest->full_name : 'Guest' }}</div>
                            <div class="text-[10px] text-brand-muted">
                                @if($req->reservation && $req->reservation->room)
                                    Villa {{ $req->reservation->room->room_number }}
                                @else
                                    In-House Stay
                                @endif
                                &middot; {{ $req->guest ? $req->guest->phone : '' }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-brand-muted font-medium">{{ $req->branch ? $req->branch->name : 'Resort' }}</td>
                        <td class="px-4 py-3.5 max-w-xs">
                            <div class="flex flex-wrap gap-1 mb-1">
                                @foreach($req->selected_locations as $dest)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-brand-canvas text-brand-primary border border-brand-primary/20">
                                        {{ $dest->name }}
                                    </span>
                                @endforeach
                            </div>
                            @if($req->extra_locations_notes)
                                <div class="text-[11px] text-amber-900 bg-amber-50/80 p-1.5 rounded border border-amber-200/50 mt-1">
                                    <strong>Extra Stops:</strong> {{ $req->extra_locations_notes }}
                                </div>
                            @endif
                            @if($req->driver_details)
                                <div class="text-[10px] text-emerald-800 mt-1 flex items-center gap-1">
                                    <i data-lucide="user-check" class="w-3 h-3"></i> {{ $req->driver_details }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-brand-text">{{ $req->passengers_count }} Pax</td>
                        <td class="px-4 py-3.5 font-bold">
                            @if($req->estimated_fare)
                                <span class="text-brand-text font-mono">₹{{ number_format($req->estimated_fare) }}</span>
                                @if($req->folio_charge_id)
                                    <span class="block text-[9px] text-emerald-600 font-bold uppercase">Billed to Folio</span>
                                @endif
                            @else
                                <span class="text-amber-600 italic text-[11px]">Pending Quote</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                                $badgeClass = match($req->status) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'contacted' => 'bg-blue-100 text-blue-800',
                                    'confirmed' => 'bg-emerald-100 text-emerald-800',
                                    'completed' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $badgeClass }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right space-x-1 whitespace-nowrap">
                            @if($req->guest && $req->guest->phone)
                                <a href="tel:{{ $req->guest->phone }}" class="inline-flex p-1.5 text-brand-muted hover:text-brand-primary rounded-md hover:bg-gray-100 transition" title="Call Guest">
                                    <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                            <button type="button" onclick="openQuoteTaxiModal({{ json_encode($req) }})" class="px-2.5 py-1 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-[11px] font-bold shadow-xs transition">
                                Quote / Dispatch
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-brand-muted">No cab or excursion requests recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
