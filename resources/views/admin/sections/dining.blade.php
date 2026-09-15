<!-- DINING MENU, CATEGORIES, DAILY SCHEDULE & REVIEWS SECTION (ADM-13 & ADM-14) -->
<section id="menu" class="section space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-brand-primary/10 text-brand-primary">Hearth & Kitchen Operations</span>
                @if(isset($pendingFoodReviewsCount) && $pendingFoodReviewsCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                    ★ {{ $pendingFoodReviewsCount }} Review{{ $pendingFoodReviewsCount > 1 ? 's' : '' }} Pending
                </span>
                @endif
            </div>
            <h2 class="text-2xl font-extrabold text-brand-text">Plantation Dining & Food Menu</h2>
            <p class="text-brand-muted text-xs mt-0.5">Dynamic categories, branch allocations, daily picking schedule, and guest food review moderation.</p>
        </div>

        <!-- Tab Switcher Navigation -->
        <div class="flex items-center gap-1.5 bg-gray-100 p-1.5 rounded-xl text-xs font-semibold overflow-x-auto shrink-0">
            <button onclick="switchDiningTab('orders')" id="dining-tab-btn-orders" class="px-3.5 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition flex items-center gap-1.5 shrink-0">
                <i data-lucide="chef-hat" class="w-3.5 h-3.5"></i>
                <span>Kitchen Tickets</span>
                @if($pendingFoodOrders > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-orange-500 text-white font-bold animate-pulse">{{ $pendingFoodOrders }}</span>
                @endif
            </button>
            <button onclick="switchDiningTab('items')" id="dining-tab-btn-items" class="px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition shrink-0">
                Dishes ({{ $menuItems->count() }})
            </button>
            <button onclick="switchDiningTab('categories')" id="dining-tab-btn-categories" class="px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition shrink-0">
                Categories ({{ $menuCategories->count() }})
            </button>
            <button onclick="switchDiningTab('daily')" id="dining-tab-btn-daily" class="px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition flex items-center gap-1 shrink-0">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-brand-accent"></i>
                <span>Daily Availability</span>
            </button>
            <button onclick="switchDiningTab('reviews')" id="dining-tab-btn-reviews" class="px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition flex items-center gap-1 shrink-0">
                <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500"></i>
                <span>Guest Reviews</span>
                @if(isset($pendingFoodReviewsCount) && $pendingFoodReviewsCount > 0)
                <span class="ml-1 w-4 h-4 rounded-full bg-amber-500 text-white text-[9px] flex items-center justify-center font-bold">{{ $pendingFoodReviewsCount }}</span>
                @endif
            </button>
            <button onclick="switchDiningTab('modifiers')" id="dining-tab-btn-modifiers" class="px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition shrink-0">
                Modifiers ({{ $modifierGroups->count() }})
            </button>
        </div>
    </div>

    <!-- ========================================================
         TAB 0: KITCHEN ORDER TICKETS (KOT / KANBAN) (ADM-15)
         ======================================================== -->
    <div id="dining-tab-orders" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-brand-surface p-3.5 rounded-2xl border border-gray-200/80 shadow-xs">
            <div>
                <h3 class="font-bold text-sm text-brand-text">Live Kitchen Order Tickets (KOT)</h3>
                <p class="text-[11px] text-brand-muted">Real-time dining orders across restaurants, in-villa dining, and room service.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-brand-primary bg-orange-50 border border-orange-200 px-3 py-1 rounded-xl">
                    {{ $foodOrders->where('status', '!=', 'completed')->count() }} Active Cooking Tickets
                </span>
            </div>
        </div>

        <!-- Kanban Stages Columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
            <!-- New Orders Column -->
            <div class="bg-brand-surface rounded-2xl border border-gray-200/80 p-3.5 space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-blue-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping"></span> New Orders
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                        {{ $foodOrders->where('status', 'new')->count() }}
                    </span>
                </div>
                <div class="space-y-2.5">
                    @forelse($foodOrders->where('status', 'new') as $order)
                        @include('admin.sections.food_order_card', ['order' => $order, 'nextStatus' => 'preparing', 'nextLabel' => 'Start Preparing'])
                    @empty
                        <div class="py-6 text-center text-xs text-brand-muted">No new orders</div>
                    @endforelse
                </div>
            </div>

            <!-- Preparing Column -->
            <div class="bg-brand-surface rounded-2xl border border-gray-200/80 p-3.5 space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                        <i data-lucide="flame" class="w-3.5 h-3.5 text-orange-500"></i> In Kitchen / Cooking
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-800">
                        {{ $foodOrders->where('status', 'preparing')->count() }}
                    </span>
                </div>
                <div class="space-y-2.5">
                    @forelse($foodOrders->where('status', 'preparing') as $order)
                        @include('admin.sections.food_order_card', ['order' => $order, 'nextStatus' => 'ready', 'nextLabel' => 'Mark as Ready'])
                    @empty
                        <div class="py-6 text-center text-xs text-brand-muted">No dishes currently cooking</div>
                    @endforelse
                </div>
            </div>

            <!-- Ready for Service Column -->
            <div class="bg-brand-surface rounded-2xl border border-gray-200/80 p-3.5 space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-emerald-700 flex items-center gap-1.5">
                        <i data-lucide="bell-ring" class="w-3.5 h-3.5 text-emerald-500"></i> Ready for Service
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                        {{ $foodOrders->where('status', 'ready')->count() }}
                    </span>
                </div>
                <div class="space-y-2.5">
                    @forelse($foodOrders->where('status', 'ready') as $order)
                        @include('admin.sections.food_order_card', ['order' => $order, 'nextStatus' => 'completed', 'nextLabel' => 'Complete Order'])
                    @empty
                        <div class="py-6 text-center text-xs text-brand-muted">No orders ready</div>
                    @endforelse
                </div>
            </div>

            <!-- Completed Orders Column -->
            <div class="bg-brand-surface rounded-2xl border border-gray-200/80 p-3.5 space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <span class="font-bold text-xs uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-gray-500"></i> Completed
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800">
                        {{ $foodOrders->where('status', 'completed')->count() }}
                    </span>
                </div>
                <div class="space-y-2.5">
                    @forelse($foodOrders->where('status', 'completed')->take(5) as $order)
                        @include('admin.sections.food_order_card', ['order' => $order, 'nextStatus' => null, 'nextLabel' => null])
                    @empty
                        <div class="py-6 text-center text-xs text-brand-muted">No completed tickets</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================
         TAB 1: MENU ITEMS & DISHES
         ======================================================== -->
    <div id="dining-tab-items" class="space-y-4 hidden">
        <!-- Filter Controls & Actions Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-brand-surface p-3.5 rounded-xl border border-gray-200/80">
            <!-- Category Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
                <button onclick="filterMenuItems('all', this)" class="menu-cat-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-deep text-white shrink-0 transition">
                    All Dishes ({{ $menuItems->count() }})
                </button>
                @foreach($menuCategories as $cat)
                    <button onclick="filterMenuItems('cat-{{ $cat->id }}', this)" class="menu-cat-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-brand-muted hover:bg-gray-50 hover:text-brand-text shrink-0 transition">
                        {{ $cat->name }} ({{ $cat->menuItems->count() }})
                    </button>
                @endforeach
            </div>

            <!-- Actions: Quick Add Dish & Category -->
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="openModal('modal-dining-category')" class="px-3 py-2 rounded-lg bg-white hover:bg-gray-50 border border-gray-200 text-brand-text text-xs font-bold flex items-center gap-1.5 transition touch-tap">
                    <i data-lucide="folder-plus" class="w-3.5 h-3.5 text-brand-primary"></i>
                    <span>+ New Category</span>
                </button>
                <button onclick="openModal('modal-dining-item')" class="px-3.5 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition touch-tap">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Dish</span>
                </button>
            </div>
        </div>

        <!-- Dishes Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($menuItems as $item)
            <div class="menu-item-card cat-{{ $item->menu_category_id }} bg-brand-surface rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-md transition duration-200" id="dish-card-{{ $item->id }}">
                <div>
                    <!-- Dish Image & Overlays -->
                    <div class="relative h-40 w-full overflow-hidden bg-gray-100">
                        <img src="{{ $item->image_url ?: 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=600&q=80' }}" 
                             alt="{{ $item->name }}" 
                             class="w-full h-full object-cover">
                        
                        <!-- Veg / Non-Veg Indicator -->
                        <div class="absolute top-2.5 left-2.5 bg-white/95 backdrop-blur-xs px-2 py-1 rounded-md shadow-xs flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full {{ $item->is_vegetarian ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                            <span class="text-[9px] font-bold uppercase {{ $item->is_vegetarian ? 'text-emerald-800' : 'text-red-800' }}">
                                {{ $item->is_vegetarian ? 'Veg' : 'Non-Veg' }}
                            </span>
                        </div>

                        <!-- Branch Allocation Pill -->
                        <div class="absolute bottom-2.5 left-2.5 bg-brand-deep/90 text-white backdrop-blur-xs px-2 py-0.5 rounded text-[9px] font-bold">
                            @if($item->is_all_branches || is_null($item->branch_id))
                                🌐 All Branches
                            @elseif(!empty($item->allocated_branch_ids))
                                📍 {{ count($item->allocated_branch_ids) }} Branches
                            @else
                                📍 {{ $item->branch ? $item->branch->name : 'Resort' }}
                            @endif
                        </div>

                        <!-- Today's Menu Pill -->
                        <div class="absolute top-2.5 right-2.5">
                            @if($item->is_available_today)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500 text-white shadow-xs flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    <span>Today's Menu</span>
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-700/80 text-white">
                                    Paused
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3.5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="font-bold text-sm text-brand-text truncate" title="{{ $item->name }}">{{ $item->name }}</h3>
                                <span class="text-[10px] text-brand-muted block mt-0.5">{{ $item->category ? $item->category->name : 'Uncategorized' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-sm font-extrabold text-brand-text">₹{{ number_format($item->price) }}</span>
                                <span class="text-[9px] text-brand-muted block">+{{ $item->tax_rate }}% GST</span>
                            </div>
                        </div>

                        <p class="text-[11px] text-brand-muted line-clamp-2 leading-relaxed">
                            {{ $item->short_description ?: $item->description ?: 'Fresh plantation recipe prepared daily.' }}
                        </p>

                        <!-- Rating & Dietary Tags -->
                        <div class="flex items-center justify-between pt-1 text-[10px]">
                            <div class="flex items-center gap-1 text-amber-600 font-bold">
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-500 text-amber-500"></i>
                                <span>{{ number_format($item->average_rating, 1) }}</span>
                                <span class="text-gray-400">({{ $item->reviews_count }})</span>
                            </div>
                            <span class="text-gray-500 font-medium">{{ $item->prep_time_minutes }}m prep</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Operations Bar -->
                <div class="p-3 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <!-- Quick Today Toggle -->
                        <button type="button" 
                                onclick="toggleDishDaily({{ $item->id }}, {{ $item->is_available_today ? 'false' : 'true' }})" 
                                class="px-2 py-1 rounded text-[10px] font-bold border transition {{ $item->is_available_today ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200' }}" 
                                title="Toggle availability for Today">
                            {{ $item->is_available_today ? '✓ Today' : 'Off Today' }}
                        </button>

                        <!-- Stock Select -->
                        <select onchange="updateMenuStock({{ $item->id }}, this.value)" class="text-[10px] font-semibold bg-white border border-gray-200 rounded px-1.5 py-1 focus:outline-hidden">
                            <option value="in_stock" {{ $item->availability_state === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="limited_quantity" {{ $item->availability_state === 'limited_quantity' ? 'selected' : '' }}>Limited</option>
                            <option value="out_of_stock" {{ $item->availability_state === 'out_of_stock' ? 'selected' : '' }}>Out</option>
                            <option value="hidden" {{ $item->availability_state === 'hidden' ? 'selected' : '' }}>Hidden</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-1">
                        <button type="button" onclick="openEditDishModal({{ json_encode($item) }})" class="p-1 rounded text-gray-400 hover:text-brand-primary hover:bg-white border border-transparent hover:border-gray-200 transition" title="Edit Dish">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="button" onclick="deleteMenuItem({{ $item->id }}, '{{ addslashes($item->name) }}')" class="p-1 rounded text-gray-400 hover:text-red-600 hover:bg-white border border-transparent hover:border-gray-200 transition" title="Delete Dish">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-4 py-16 text-center bg-white rounded-2xl border border-gray-200">
                <i data-lucide="utensils" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                <h4 class="font-bold text-sm text-brand-text">No Dishes Found</h4>
                <p class="text-xs text-brand-muted mt-0.5">Click "Add Dish" to add dishes to your kitchen menu.</p>
            </div>
            @endforelse
        </div>
    </div>


    <!-- ========================================================
         TAB 2: CATEGORIES MANAGEMENT
         ======================================================== -->
    <div id="dining-tab-categories" class="hidden space-y-4">
        <div class="flex items-center justify-between bg-brand-surface p-4 rounded-xl border border-gray-200/80">
            <div>
                <h3 class="font-bold text-sm text-brand-text">Menu Categories Library</h3>
                <p class="text-xs text-brand-muted">Organize your menu into Breakfast, Lunch, Dinner, Kerala Authentic, Chef Specials, etc.</p>
            </div>
            <button onclick="openModal('modal-dining-category')" class="px-3.5 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Add Category</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($menuCategories as $cat)
            <div class="bg-brand-surface rounded-2xl border border-gray-200/90 p-4 shadow-xs flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center font-bold">
                                <i data-lucide="{{ $cat->icon ?: 'utensils' }}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-brand-text">{{ $cat->name }}</h4>
                                <span class="text-[10px] text-brand-muted">Slug: /{{ $cat->slug }}</span>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $cat->isGlobal() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $cat->isGlobal() ? '🌐 All Branches' : ($cat->branch ? $cat->branch->name : 'Branch') }}
                        </span>
                    </div>

                    <p class="text-xs text-brand-muted line-clamp-2 leading-relaxed">
                        {{ $cat->description ?: 'No category description provided.' }}
                    </p>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="font-semibold text-brand-muted text-[11px]">
                        {{ $cat->menuItems->count() }} Linked Dish{{ $cat->menuItems->count() == 1 ? '' : 'es' }}
                    </span>

                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="openEditCategoryModal({{ json_encode($cat) }})" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-brand-primary transition" title="Edit Category">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        <button type="button" onclick="deleteMenuCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-red-600 transition" title="Delete Category">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 py-12 text-center text-brand-muted">No categories created yet.</div>
            @endforelse
        </div>
    </div>


    <!-- ========================================================
         TAB 3: RAPID DAILY AVAILABILITY & PICKING MATRIX
         ======================================================== -->
    <div id="dining-tab-daily" class="hidden space-y-4">
        <!-- Explanatory Banner -->
        <div class="bg-brand-deep text-white p-4 sm:p-5 rounded-2xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <i data-lucide="calendar-check" class="w-5 h-5 text-brand-accent"></i>
                    <h3 class="font-bold text-sm sm:text-base">1-Click Daily Availability & Weekly Schedule Matrix</h3>
                </div>
                <p class="text-xs text-white/70 max-w-2xl leading-relaxed">
                    Dishes will only appear on guest menus for their active days and branches. Toggle "Today's Menu" or tap individual day pills (M, T, W, T, F, S, S) to update daily picking in real time with zero page reloads.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-lg bg-white/10 font-mono">Today: {{ date('l, M d') }}</span>
            </div>
        </div>

        <!-- Rapid Matrix Table -->
        <div class="bg-brand-surface rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200 text-brand-muted text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="p-3.5">Dish Name & Category</th>
                            <th class="p-3.5">Branch Scope</th>
                            <th class="p-3.5 text-center">Today's Menu</th>
                            <th class="p-3.5 text-center">Weekly Day Schedule (Click to Toggle)</th>
                            <th class="p-3.5">Stock State</th>
                            <th class="p-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="matrix-table-body">
                        @foreach($menuItems as $item)
                        @php
                            $days = $item->available_days ?? ['mon','tue','wed','thu','fri','sat','sun'];
                            $daysLower = array_map('strtolower', (array)$days);
                            $allWeekDays = [
                                'mon' => 'M',
                                'tue' => 'T',
                                'wed' => 'W',
                                'thu' => 'T',
                                'fri' => 'F',
                                'sat' => 'S',
                                'sun' => 'S',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition" id="matrix-row-{{ $item->id }}">
                            <td class="p-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $item->is_vegetarian ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                                    <div>
                                        <span class="font-bold text-brand-text block">{{ $item->name }}</span>
                                        <span class="text-[10px] text-brand-muted">{{ $item->category ? $item->category->name : 'General' }} &middot; ₹{{ number_format($item->price) }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="p-3.5">
                                @if($item->is_all_branches || is_null($item->branch_id))
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700">🌐 All Branches</span>
                                @elseif(!empty($item->allocated_branch_ids))
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">📍 {{ count($item->allocated_branch_ids) }} Branches</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">📍 {{ $item->branch ? $item->branch->name : 'Branch' }}</span>
                                @endif
                            </td>

                            <!-- Today's Menu 1-Tap Pill -->
                            <td class="p-3.5 text-center">
                                <button type="button" 
                                        onclick="toggleDishDaily({{ $item->id }}, {{ $item->is_available_today ? 'false' : 'true' }})" 
                                        id="today-btn-{{ $item->id }}"
                                        class="px-3 py-1 rounded-full text-[11px] font-bold transition shadow-xs {{ $item->is_available_today ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-gray-200 text-gray-500 hover:bg-emerald-100 hover:text-emerald-800' }}">
                                    {{ $item->is_available_today ? '✓ Active Today' : '✕ Off Today' }}
                                </button>
                            </td>

                            <!-- Weekday Pills (M, T, W, T, F, S, S) -->
                            <td class="p-3.5 text-center">
                                <div class="inline-flex items-center gap-1 bg-gray-100 p-1 rounded-xl">
                                    @foreach($allWeekDays as $dayKey => $dayLabel)
                                    @php
                                        $isActiveDay = in_array($dayKey, $daysLower);
                                    @endphp
                                    <button type="button" 
                                            onclick="toggleDishDay({{ $item->id }}, '{{ $dayKey }}', this)" 
                                            class="w-6 h-6 rounded-lg text-[10px] font-bold transition {{ $isActiveDay ? 'bg-brand-primary text-white shadow-xs' : 'bg-transparent text-gray-400 hover:text-brand-text' }}" 
                                            title="Toggle {{ ucfirst($dayKey) }}">
                                        {{ $dayLabel }}
                                    </button>
                                    @endforeach
                                </div>
                            </td>

                            <td class="p-3.5">
                                <select onchange="updateMenuStock({{ $item->id }}, this.value)" class="text-[11px] font-semibold bg-white border border-gray-200 rounded-lg px-2 py-1 focus:outline-hidden">
                                    <option value="in_stock" {{ $item->availability_state === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                    <option value="limited_quantity" {{ $item->availability_state === 'limited_quantity' ? 'selected' : '' }}>Limited</option>
                                    <option value="out_of_stock" {{ $item->availability_state === 'out_of_stock' ? 'selected' : '' }}>Out</option>
                                    <option value="hidden" {{ $item->availability_state === 'hidden' ? 'selected' : '' }}>Hidden</option>
                                </select>
                            </td>

                            <td class="p-3.5 text-right">
                                <button onclick="openEditDishModal({{ json_encode($item) }})" class="px-2.5 py-1 rounded bg-gray-100 hover:bg-brand-primary hover:text-white text-brand-text font-bold text-[10px] transition">
                                    Edit Dish
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- ========================================================
         TAB 4: GUEST FOOD REVIEWS MODERATION
         ======================================================== -->
    <div id="dining-tab-reviews" class="hidden space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-brand-surface p-4 rounded-xl border border-gray-200/80">
            <div>
                <h3 class="font-bold text-sm text-brand-text">Verified In-Stay Dining Reviews</h3>
                <p class="text-xs text-brand-muted">Guest ratings and reviews posted after receiving their cottage meal. Approved reviews show on public dish cards.</p>
            </div>
            <div class="flex items-center gap-1.5 text-xs">
                <button onclick="filterFoodReviews('all', this)" class="food-rev-filter-btn px-3 py-1.5 rounded-lg font-bold bg-brand-deep text-white transition">
                    All ({{ $foodReviews->count() }})
                </button>
                <button onclick="filterFoodReviews('pending', this)" class="food-rev-filter-btn px-3 py-1.5 rounded-lg font-semibold bg-white border border-gray-200 text-brand-muted hover:bg-gray-50 transition">
                    Pending ({{ $pendingFoodReviewsCount }})
                </button>
                <button onclick="filterFoodReviews('approved', this)" class="food-rev-filter-btn px-3 py-1.5 rounded-lg font-semibold bg-white border border-gray-200 text-brand-muted hover:bg-gray-50 transition">
                    Approved ({{ $foodReviews->where('status', 'approved')->count() }})
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($foodReviews as $rev)
            <div class="food-review-card status-{{ $rev->status }} bg-brand-surface rounded-2xl border border-gray-200/90 p-4 shadow-xs space-y-3 flex flex-col justify-between" id="food-review-card-{{ $rev->id }}">
                <div class="space-y-2.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-brand-text">{{ $rev->customer_name }}</span>
                                <span class="text-[10px] text-brand-muted">&middot; {{ $rev->order ? '#' . $rev->order->order_number : '' }}</span>
                            </div>
                            <span class="text-[10px] text-brand-muted block">
                                {{ $rev->branch ? $rev->branch->name : 'Resort' }} &middot; {{ $rev->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <span id="food-rev-badge-{{ $rev->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $rev->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($rev->status === 'pending' ? 'bg-amber-100 text-amber-800 animate-pulse' : 'bg-rose-100 text-rose-800') }}">
                            {{ $rev->status === 'approved' ? '✓ Approved' : ($rev->status === 'pending' ? '⚡ Pending Moderation' : 'Rejected') }}
                        </span>
                    </div>

                    <!-- Stars -->
                    <div class="flex items-center gap-1 text-amber-500">
                        @for($i = 1; $i <= 5; $i++)
                        <i data-lucide="star" class="w-4 h-4 {{ $i <= $rev->rating ? 'fill-amber-400 text-amber-400' : 'text-gray-300' }}"></i>
                        @endfor
                        <span class="text-xs font-bold text-brand-text ml-1">{{ $rev->rating }}.0</span>
                        @if($rev->menuItem)
                        <span class="text-[11px] text-brand-muted ml-2">for <strong class="text-brand-text">{{ $rev->menuItem->name }}</strong></span>
                        @endif
                    </div>

                    <!-- Review Comment -->
                    <p class="text-xs text-brand-text italic bg-gray-50/80 p-2.5 rounded-xl border border-gray-100 leading-relaxed">
                        "{{ $rev->comment ?: 'Guest gave a 5-star rating without text comments.' }}"
                    </p>
                </div>

                <!-- Approval Actions -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs" id="food-rev-actions-{{ $rev->id }}">
                    @if($rev->status === 'pending')
                    <span class="text-[10px] text-amber-700 font-semibold">Awaiting resort manager approval</span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="rejectFoodReview({{ $rev->id }})" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 font-semibold text-xs transition">
                            Reject
                        </button>
                        <button type="button" onclick="approveFoodReview({{ $rev->id }})" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>Approve & Publish</span>
                        </button>
                    </div>
                    @else
                    <span class="text-[10px] text-brand-muted">
                        {{ $rev->status === 'approved' ? 'Visible to all customers on room & dining menu' : 'Hidden from public viewing' }}
                    </span>
                    @if($rev->status !== 'approved')
                    <button type="button" onclick="approveFoodReview({{ $rev->id }})" class="px-3 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-xs transition">
                        Change to Approved
                    </button>
                    @endif
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-2 py-16 text-center bg-white rounded-2xl border border-gray-200 text-brand-muted">
                <i data-lucide="star" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                <p class="text-xs">No guest meal reviews submitted yet.</p>
            </div>
            @endforelse
        </div>
    </div>


    <!-- ========================================================
         TAB 5: MODIFIERS (ADM-14)
         ======================================================== -->
    <div id="dining-tab-modifiers" class="hidden space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($modifierGroups as $mg)
            <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-4 space-y-3 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-brand-text">{{ $mg->name }}</h3>
                        <div class="text-xs text-brand-muted">{{ ucfirst($mg->selection_type) }} · Min {{ $mg->min_selections }} / Max {{ $mg->max_selections }}</div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800">
                        {{ $mg->options->count() }} Choices
                    </span>
                </div>
                
                <div class="divide-y divide-gray-100 border border-gray-100 rounded-lg overflow-hidden">
                    @foreach($mg->options as $opt)
                    <div class="p-2.5 flex items-center justify-between text-xs hover:bg-gray-50">
                        <span class="font-medium text-brand-text">{{ $opt->name }}</span>
                        <span class="font-bold {{ $opt->price_adjustment > 0 ? 'text-brand-primary' : 'text-brand-muted' }}">
                            {{ $opt->price_adjustment > 0 ? '+₹' . number_format($opt->price_adjustment) : 'Free' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- SCRIPT FOR DINING OPERATIONS -->
<script>
function switchDiningTab(tab) {
    const tabs = ['items', 'categories', 'daily', 'reviews', 'modifiers'];
    tabs.forEach(t => {
        const el = document.getElementById('dining-tab-' + t);
        const btn = document.getElementById('dining-tab-btn-' + t);
        if (el) el.classList.toggle('hidden', t !== tab);
        if (btn) {
            if (t === tab) {
                btn.className = 'px-3.5 py-1.5 rounded-lg bg-brand-primary text-white shadow-xs transition';
            } else {
                btn.className = 'px-3.5 py-1.5 rounded-lg text-brand-muted hover:text-brand-text transition';
            }
        }
    });
    if (window.lucide) lucide.createIcons();
}

function filterMenuItems(catClass, btn) {
    document.querySelectorAll('.menu-cat-btn').forEach(b => {
        b.className = 'menu-cat-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-200 text-brand-muted hover:bg-gray-50 hover:text-brand-text shrink-0 transition';
    });
    btn.className = 'menu-cat-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-deep text-white shrink-0 transition';

    document.querySelectorAll('.menu-item-card').forEach(card => {
        if (catClass === 'all' || card.classList.contains(catClass)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterFoodReviews(status, btn) {
    document.querySelectorAll('.food-rev-filter-btn').forEach(b => {
        b.className = 'food-rev-filter-btn px-3 py-1.5 rounded-lg font-semibold bg-white border border-gray-200 text-brand-muted hover:bg-gray-50 transition';
    });
    btn.className = 'food-rev-filter-btn px-3 py-1.5 rounded-lg font-bold bg-brand-deep text-white transition';

    document.querySelectorAll('.food-review-card').forEach(card => {
        if (status === 'all' || card.classList.contains('status-' + status)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

// 1-Click Today Availability Toggle
async function toggleDishDaily(itemId, activeVal) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/admin/dining/items/${itemId}/toggle-daily`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ today: activeVal })
        });
        const data = await res.json();
        if (data.success) {
            if (typeof showToast === 'function') showToast(data.message, 'success');
            setTimeout(() => location.reload(), 300);
        } else {
            alert(data.message || 'Could not update daily availability');
        }
    } catch(err) {
        console.error(err);
        alert('Server error updating daily availability');
    }
}

// 1-Click Day Schedule Toggle
async function toggleDishDay(itemId, dayKey, btn) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/admin/dining/items/${itemId}/toggle-daily`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ day: dayKey })
        });
        const data = await res.json();
        if (data.success) {
            btn.classList.toggle('bg-brand-primary');
            btn.classList.toggle('text-white');
            btn.classList.toggle('bg-transparent');
            btn.classList.toggle('text-gray-400');
            if (typeof showToast === 'function') showToast(data.message, 'info');
        }
    } catch(err) {
        console.error(err);
    }
}

// Approve Food Review
async function approveFoodReview(reviewId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
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
            const badge = document.getElementById('food-rev-badge-' + reviewId);
            if (badge) {
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800';
                badge.innerText = '✓ Approved';
            }
            const actions = document.getElementById('food-rev-actions-' + reviewId);
            if (actions) {
                actions.innerHTML = '<span class="text-[10px] text-brand-muted">Visible to all customers on room & dining menu</span>';
            }
        }
    } catch(err) {
        console.error(err);
        alert('Could not approve review');
    }
}

// Reject Food Review
async function rejectFoodReview(reviewId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/admin/food-reviews/${reviewId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        if (data.success) {
            if (typeof showToast === 'function') showToast(data.message, 'info');
            setTimeout(() => location.reload(), 300);
        }
    } catch(err) {
        console.error(err);
    }
}

// Delete Dish
async function deleteMenuItem(itemId, itemName) {
    if (!confirm(`Are you sure you want to remove "${itemName}" from the kitchen menu?`)) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/admin/dining/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        if (data.success) {
            if (typeof showToast === 'function') showToast(data.message, 'success');
            const el = document.getElementById('dish-card-' + itemId);
            if (el) el.remove();
            const row = document.getElementById('matrix-row-' + itemId);
            if (row) row.remove();
        } else {
            alert(data.message || 'Could not delete item');
        }
    } catch(err) {
        console.error(err);
        alert('Server error deleting dish');
    }
}

// Delete Category
async function deleteMenuCategory(catId, catName) {
    if (!confirm(`Delete category "${catName}"? This action cannot be undone.`)) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/admin/dining/categories/${catId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        if (data.success) {
            if (typeof showToast === 'function') showToast(data.message, 'success');
            setTimeout(() => location.reload(), 300);
        } else {
            alert(data.message || 'Cannot delete category');
        }
    } catch(err) {
        console.error(err);
        alert('Server error deleting category');
    }
}
</script>
