<!-- ROOMS & ACCOMMODATIONS MODULE (ADM-05) -->
<section id="rooms" class="section space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-brand-text">Accommodations & Room Management</h2>
            <p class="text-brand-muted text-xs mt-0.5">Manage room classification, rates, amenities library, categories, and physical units.</p>
        </div>
        <!-- 5 Sub-Tabs Navigation -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full no-scrollbar">
            <button onclick="switchRoomTab('floorplan')" id="tab-btn-floorplan" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition shadow-xs shrink-0 flex items-center gap-1.5">
                <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i>
                <span>Status & Housekeeping ({{ $rooms->count() }})</span>
            </button>
            <button onclick="switchRoomTab('availability')" id="tab-btn-availability" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1.5">
                <i data-lucide="grid-3x3" class="w-3.5 h-3.5"></i>
                <span>7-Day Availability</span>
            </button>
            <button onclick="switchRoomTab('types')" id="tab-btn-types" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">
                Room Types ({{ $roomTypes->count() }})
            </button>
            <button onclick="switchRoomTab('physical')" id="tab-btn-physical" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">
                Physical Units ({{ $rooms->count() }})
            </button>
            <button onclick="switchRoomTab('categories')" id="tab-btn-categories" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">
                Categories ({{ $roomCategories->count() }})
            </button>
            <button onclick="switchRoomTab('amenities')" id="tab-btn-amenities" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">
                Amenities ({{ $amenities->count() }})
            </button>
        </div>
    </div>

    <!-- TAB 0A: VISUAL FLOORPLAN & HOUSEKEEPING BOARD (ADM-06) -->
    <div id="room-tab-floorplan" class="space-y-4">
        <!-- Legend & Filters -->
        <div class="bg-brand-surface p-3.5 rounded-2xl border border-gray-200/70 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="text-xs text-brand-muted font-medium">Floor plan visual cards with direct 1-tap Clean/Dirty and operational state toggles.</div>
            <div class="flex items-center gap-3 text-[11px] font-semibold text-brand-muted flex-wrap">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Available</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Occupied</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Reserved</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Maintenance</span>
            </div>
        </div>

        <!-- Visual Grid of Physical Rooms -->
        <div class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            @foreach($rooms as $room)
            @php
                $borderColor = match($room->operational_status) {
                    'available' => 'border-emerald-300 bg-emerald-50/30',
                    'occupied' => 'border-blue-300 bg-blue-50/30',
                    'reserved' => 'border-amber-300 bg-amber-50/30',
                    'maintenance' => 'border-red-300 bg-red-50/30',
                    default => 'border-gray-200 bg-white'
                };
            @endphp
            <div class="p-4 rounded-xl border-2 {{ $borderColor }} shadow-xs flex flex-col justify-between space-y-3 bg-brand-surface hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xl font-extrabold text-brand-text">{{ $room->room_number }}</span>
                        <div class="text-[10px] text-brand-muted uppercase font-bold tracking-wider">{{ $room->branch ? $room->branch->code : '' }} · {{ $room->floor }}</div>
                    </div>
                    <div class="w-2.5 h-2.5 rounded-full {{ $room->operational_status === 'available' ? 'bg-emerald-500' : ($room->operational_status === 'occupied' ? 'bg-blue-500' : ($room->operational_status === 'reserved' ? 'bg-amber-500' : 'bg-red-500')) }}"></div>
                </div>

                <div>
                    <div class="text-xs font-semibold text-brand-text truncate">{{ $room->roomType ? $room->roomType->name : '' }}</div>
                    @if($room->operational_status === 'occupied')
                        @php
                            $currRes = $room->reservations->where('status', 'checked_in')->first();
                        @endphp
                        @if($currRes && $currRes->guest)
                            <div class="text-[11px] text-blue-800 font-medium truncate mt-0.5">
                                Guest: {{ $currRes->guest->full_name }}
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Status Badges & Controls -->
                <div class="pt-2 border-t border-gray-100/80 flex items-center justify-between gap-1">
                    <button type="button" onclick="quickToggleHousekeeping({{ $room->id }}, '{{ $room->room_number }}', '{{ $room->housekeeping_status }}', this)" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase transition hover:opacity-85 cursor-pointer flex items-center gap-1 {{ $room->housekeeping_status === 'clean' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}" title="Click to toggle Housekeeping (Clean/Dirty)">
                        <i data-lucide="{{ $room->housekeeping_status === 'clean' ? 'sparkles' : 'spray-can' }}" class="w-3 h-3"></i>
                        <span>{{ $room->housekeeping_status }}</span>
                    </button>
                    
                    <select onchange="updateRoomOperationalStatus({{ $room->id }}, this.value)" class="text-[10px] font-semibold bg-white border border-gray-200 rounded px-1.5 py-0.5 focus:outline-none focus:ring-1 focus:ring-brand-primary">
                        <option value="available" {{ $room->operational_status === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ $room->operational_status === 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="reserved" {{ $room->operational_status === 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="maintenance" {{ $room->operational_status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="blocked" {{ $room->operational_status === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 0B: 7-DAY AVAILABILITY MATRIX (ADM-04) -->
    <div id="room-tab-availability" class="space-y-4 hidden">
        <div class="bg-brand-surface rounded-2xl border border-gray-200/70 shadow-xs overflow-hidden">
            <div class="p-3.5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-sm text-brand-text">7-Day Real-Time Room Availability Matrix</h3>
                    <p class="text-[11px] text-brand-muted">Sellable capacity across room categories from today onwards</p>
                </div>
                <span class="text-xs text-brand-muted bg-gray-100 px-2.5 py-1 rounded-lg">Zero Query Live Cache</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-200 text-brand-muted">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-brand-text uppercase text-[10px] w-64">Room Type / Branch</th>
                            <th class="px-3 py-3 text-center font-semibold text-brand-text">Units</th>
                            @for($i = 0; $i < 7; $i++)
                                @php $day = \Carbon\Carbon::today()->addDays($i); @endphp
                                <th class="px-3 py-3 text-center {{ $day->isToday() ? 'bg-emerald-50/80 text-emerald-900 font-bold' : '' }}">
                                    <div class="text-[10px] uppercase font-bold">{{ $day->format('D') }}</div>
                                    <div class="text-xs">{{ $day->format('d M') }}</div>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($roomTypes as $rt)
                        @php
                            $totalUnits = $rt->rooms->count();
                        @endphp
                        <tr class="hover:bg-brand-canvas/50 transition">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-brand-text">{{ $rt->name }}</div>
                                <div class="text-[11px] text-brand-muted">{{ $rt->branch ? $rt->branch->name : '' }} · ₹{{ number_format($rt->base_price) }}/night</div>
                            </td>
                            <td class="px-3 py-3.5 text-center font-semibold text-brand-muted">
                                {{ $totalUnits }}
                            </td>
                            @for($i = 0; $i < 7; $i++)
                                @php
                                    $checkDate = \Carbon\Carbon::today()->addDays($i)->toDateString();
                                    $cellData = $availabilityMatrix[$rt->id][$checkDate] ?? null;
                                    if ($cellData) {
                                        $bookedCount = $cellData['booked'];
                                        $available = $cellData['available'];
                                    } else {
                                        $available = $totalUnits;
                                    }
                                @endphp
                                <td class="px-3 py-3.5 text-center">
                                    @if($available == 0 && $totalUnits > 0)
                                        <span class="inline-block w-8 py-1 rounded bg-red-100 text-red-800 font-bold text-xs" title="Sold Out (0/{{ $totalUnits }})">0</span>
                                    @elseif($available <= 1 && $totalUnits > 1)
                                        <span class="inline-block w-8 py-1 rounded bg-amber-100 text-amber-800 font-bold text-xs" title="Limited ({{ $available }}/{{ $totalUnits }})">{{ $available }}</span>
                                    @else
                                        <span class="inline-block w-8 py-1 rounded bg-emerald-100 text-emerald-800 font-bold text-xs" title="Available ({{ $available }}/{{ $totalUnits }})">{{ $available }}</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-brand-muted">No room types found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 1: ROOM TYPES -->
    <div id="room-tab-types" class="space-y-4 hidden">
        <div class="flex justify-between items-center">
            <span class="text-xs text-brand-muted font-medium">Configure suite classification, branch pricing, specs, and amenities</span>
            <button onclick="openModal('modal-room-type')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Room Type
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($roomTypes as $rt)
            <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden flex flex-col">
                <div class="h-44 w-full bg-gray-100 relative overflow-hidden group">
                    @if($rt->cover_image_url)
                        <img src="{{ $rt->cover_image_url }}" alt="{{ $rt->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-200">
                            <i data-lucide="image" class="w-8 h-8"></i>
                        </div>
                    @endif
                    <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                        <span class="bg-black/60 backdrop-blur-xs text-white px-2 py-0.5 rounded text-[10px] font-semibold">
                            {{ $rt->branch ? $rt->branch->name : 'All Branches' }}
                        </span>
                        @if($rt->category)
                        <span class="bg-brand-primary/80 backdrop-blur-xs text-white px-2 py-0.5 rounded text-[10px] font-semibold flex items-center gap-1">
                            <i data-lucide="{{ $rt->category->icon ?: 'palmtree' }}" class="w-2.5 h-2.5"></i>
                            {{ $rt->category->name }}
                        </span>
                        @endif
                    </div>
                    <div class="absolute bottom-2.5 right-2.5 bg-brand-surface text-brand-text px-2.5 py-1 rounded-lg text-xs font-bold shadow-xs flex items-baseline gap-1">
                        ₹{{ number_format($rt->base_price) }} <span class="text-[10px] text-brand-muted font-normal">/ night</span>
                    </div>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-bold text-base text-brand-text">{{ $rt->name }}</h3>
                            <button onclick="openEditRoomTypeModal({{ json_encode($rt) }})" class="p-1 rounded text-brand-muted hover:text-brand-primary hover:bg-gray-100 transition" title="Edit Room Type">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <p class="text-xs text-brand-muted mt-1 leading-relaxed line-clamp-2">{{ $rt->short_description ?: $rt->description }}</p>
                    </div>

                    <!-- Dynamic Amenities Tags -->
                    <div class="flex flex-wrap gap-1.5">
                        @if($rt->amenitiesList && $rt->amenitiesList->count() > 0)
                            @foreach($rt->amenitiesList->take(4) as $amenity)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 text-brand-muted text-[10px] font-medium">
                                    <i data-lucide="{{ $amenity->icon ?: 'check' }}" class="w-2.5 h-2.5 text-emerald-600"></i> {{ $amenity->name }}
                                </span>
                            @endforeach
                            @if($rt->amenitiesList->count() > 4)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 text-brand-muted text-[10px] font-semibold">
                                    +{{ $rt->amenitiesList->count() - 4 }} more
                                </span>
                            @endif
                        @elseif(!empty($rt->amenities))
                            @foreach(array_slice($rt->amenities, 0, 4) as $amenityName)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 text-brand-muted text-[10px] font-medium">
                                    <i data-lucide="check" class="w-2.5 h-2.5 text-emerald-600"></i> {{ $amenityName }}
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-brand-muted">
                        <span>Max {{ $rt->max_guests }} Guests · {{ $rt->size_sqft ?? 450 }} sq.ft</span>
                        <span class="font-bold text-brand-primary">{{ $rt->rooms->count() }} Physical Rooms</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 p-8 text-center bg-brand-surface rounded-xl border border-gray-200/70 text-xs text-brand-muted">
                No room types configured for this branch. Click "Add Room Type" to create one.
            </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 2: PHYSICAL ROOMS TABLE -->
    <div id="room-tab-physical" class="hidden bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
        <div class="p-3.5 border-b border-gray-100 flex justify-between items-center">
            <span class="text-xs text-brand-muted font-medium">Individual physical units, keys, housekeeping status</span>
            <button onclick="openModal('modal-physical-room')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Physical Unit
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Room Unit</th>
                        <th class="px-4 py-3">Branch</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Floor / Location</th>
                        <th class="px-4 py-3">Operational State</th>
                        <th class="px-4 py-3">Housekeeping</th>
                        <th class="px-4 py-3 text-right">Quick Toggle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rooms as $room)
                    <tr class="hover:bg-brand-canvas/60 transition">
                        <td class="px-4 py-3 font-bold text-brand-text text-sm">
                            Room {{ $room->room_number }}
                        </td>
                        <td class="px-4 py-3 text-brand-muted font-medium">
                            {{ $room->branch ? $room->branch->name : '' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-brand-text">
                            {{ $room->roomType ? $room->roomType->name : '' }}
                        </td>
                        <td class="px-4 py-3 text-brand-muted">
                            {{ $room->floor }}
                        </td>
                        <td class="px-4 py-3">
                            @if($room->operational_status === 'available')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Available</span>
                            @elseif($room->operational_status === 'occupied')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Occupied</span>
                            @elseif($room->operational_status === 'reserved')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Reserved</span>
                            @elseif($room->operational_status === 'maintenance')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">Maintenance</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">{{ ucfirst($room->operational_status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($room->housekeeping_status === 'clean')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Clean</span>
                            @elseif($room->housekeeping_status === 'dirty')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800">Dirty</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-800">Inspecting</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <button onclick="toggleRoomHousekeeping({{ $room->id }}, '{{ $room->housekeeping_status === 'clean' ? 'dirty' : 'clean' }}')" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-brand-text rounded text-[10px] font-medium transition cursor-pointer">
                                Mark {{ $room->housekeeping_status === 'clean' ? 'Dirty' : 'Clean' }}
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-brand-muted text-xs">No physical room units registered.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: ROOM CATEGORIES (NEW) -->
    <div id="room-tab-categories" class="hidden space-y-4">
        <div class="flex justify-between items-center">
            <span class="text-xs text-brand-muted font-medium">Architectural styles and cottage classifications (Villas, Cottages, Treehouses, etc.)</span>
            <button onclick="openModal('modal-room-category')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Room Category
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($roomCategories as $cat)
            <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs p-4 flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-brand-canvas text-brand-primary flex items-center justify-center">
                                <i data-lucide="{{ $cat->icon ?: 'palmtree' }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-brand-text">{{ $cat->name }}</h3>
                                <span class="font-mono text-[10px] text-brand-muted">slug: {{ $cat->slug }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button onclick="openEditCategoryModal({{ json_encode($cat) }})" class="p-1.5 rounded text-brand-muted hover:text-brand-primary hover:bg-gray-100 transition cursor-pointer" title="Edit Category">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </button>
                            <button onclick="deleteCategory({{ $cat->id }})" class="p-1.5 rounded text-brand-muted hover:text-red-600 hover:bg-red-50 transition cursor-pointer" title="Delete Category">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-brand-muted leading-relaxed">{{ $cat->description ?: 'No description provided.' }}</p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-brand-muted">Order: <strong>{{ $cat->sort_order }}</strong></span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $cat->room_types_count ?? 0 }} Room Types
                    </span>
                </div>
            </div>
            @empty
            <div class="col-span-3 p-8 text-center bg-brand-surface rounded-xl border border-gray-200/70 text-xs text-brand-muted">
                No room categories created yet. Click "Add Room Category" to define your cottage styles.
            </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 4: AMENITIES LIBRARY (NEW) -->
    <div id="room-tab-amenities" class="hidden space-y-4">
        <div class="flex justify-between items-center">
            <span class="text-xs text-brand-muted font-medium">Dynamic features and comforts library with icons and client filter badges</span>
            <button onclick="openModal('modal-amenity')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Amenity
            </button>
        </div>

        @php
            $amenityGroups = $amenities->groupBy('category');
        @endphp

        @foreach($amenityGroups as $groupName => $groupAmenities)
        <div class="space-y-2.5">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-brand-text uppercase tracking-wider">{{ $groupName }}</span>
                <span class="text-[10px] bg-gray-100 text-brand-muted px-2 py-0.5 rounded-full font-semibold">{{ $groupAmenities->count() }} items</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($groupAmenities as $am)
                <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs p-3.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 truncate">
                        <div class="w-8 h-8 rounded-lg bg-brand-canvas text-brand-primary flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $am->icon ?: 'sparkles' }}" class="w-4 h-4"></i>
                        </div>
                        <div class="truncate">
                            <div class="flex items-center gap-1.5">
                                <h4 class="font-bold text-xs text-brand-text truncate">{{ $am->name }}</h4>
                                @if($am->is_featured)
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300" title="Visible on client explorer quick filter bar">Featured Pill</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-brand-muted">{{ $am->room_types_count ?? 0 }} room types &middot; icon: {{ $am->icon }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button onclick="openEditAmenityModal({{ json_encode($am) }})" class="p-1 rounded text-brand-muted hover:text-brand-primary hover:bg-gray-100 transition cursor-pointer" title="Edit Amenity">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        </button>
                        <button onclick="deleteAmenity({{ $am->id }})" class="p-1 rounded text-brand-muted hover:text-red-600 hover:bg-red-50 transition cursor-pointer" title="Delete Amenity">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</section>
