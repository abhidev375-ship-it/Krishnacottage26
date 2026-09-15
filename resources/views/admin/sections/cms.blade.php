<!-- WEBSITE CMS & VISUAL STUDIO HUB (ADM-22, ADM-23, ADM-24) -->
<section id="cms" class="section space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-xl sm:text-2xl font-bold text-brand-text">Website Studio & Content CMS</h2>
            </div>
            <p class="text-brand-muted text-xs mt-0.5">Interactive live homepage designer, SEO pages, navigation hierarchies, and public section controls.</p>
        </div>
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 max-w-full no-scrollbar">
            <button onclick="switchCmsTab('visual')" id="cms-tab-btn-visual" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition shadow-xs shrink-0 flex items-center gap-1.5">
                <i data-lucide="palette" class="w-3.5 h-3.5 text-brand-accent"></i>
                <span>Live Visual Studio</span>
            </button>
            <button onclick="switchCmsTab('pages')" id="cms-tab-btn-pages" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:text-brand-text transition shrink-0">
                Pages ({{ $pages->count() }})
            </button>
            <button onclick="switchCmsTab('nav')" id="cms-tab-btn-nav" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:text-brand-text transition shrink-0">
                Navigation Structure
            </button>
            <button onclick="switchCmsTab('home')" id="cms-tab-btn-home" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:text-brand-text transition shrink-0">
                Homepage Sections
            </button>
        </div>
    </div>

    <!-- SUB-TAB 0: LIVE VISUAL HOMEPAGE CANVAS (ADM-24B) -->
    <div id="cms-tab-visual" class="space-y-4">
        @include('admin.sections.visual_editor')
    </div>

    <!-- SUB-TAB 1: PAGES (ADM-22) -->
    <div id="cms-tab-pages" class="bg-brand-surface rounded-2xl border border-gray-200/70 shadow-xs overflow-hidden space-y-3 p-4 hidden">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-brand-text">Custom Published Pages</h3>
                <p class="text-[11px] text-brand-muted">SEO content pages, stories, and policy articles</p>
            </div>
            <button onclick="openNewPageModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-deep hover:bg-black text-white text-xs font-semibold rounded-lg shadow-xs transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add New Page
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200/80">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Page Title</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Branch Context</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pages as $page)
                    <tr class="hover:bg-brand-canvas/60 transition">
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-brand-text">{{ $page->title }}</div>
                            @if($page->meta_description)
                            <div class="text-[11px] text-brand-muted line-clamp-1 max-w-sm">{{ $page->meta_description }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-mono text-brand-primary text-[11px]">/{{ $page->slug }}</td>
                        <td class="px-4 py-3.5 text-brand-muted">{{ $page->branch ? $page->branch->name : 'Resort-wide (All Branches)' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            <button type="button" onclick="togglePageStatus({{ $page->id }})" class="px-2 py-0.5 rounded-full text-[10px] font-bold transition {{ $page->is_published ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                {{ $page->is_published ? 'Published' : 'Draft' }}
                            </button>
                        </td>
                        <td class="px-4 py-3.5 text-right space-x-2">
                            <button type="button" onclick="openEditPageModal({{ json_encode($page) }})" class="font-semibold text-brand-primary hover:text-brand-deep hover:underline">
                                Edit
                            </button>
                            <button type="button" onclick="deleteCmsPage({{ $page->id }}, '{{ addslashes($page->title) }}')" class="font-semibold text-red-600 hover:text-red-800 hover:underline">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-brand-muted">
                            <i data-lucide="file-text" class="w-6 h-6 mx-auto mb-1 opacity-50"></i>
                            <p class="text-xs">No custom pages configured yet. Click "Add New Page" to create one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SUB-TAB 2: NAVIGATION (ADM-23) -->
    <div id="cms-tab-nav" class="hidden bg-brand-surface rounded-xl border border-gray-200/70 p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-brand-text">Main Header Navigation Menu Tree</h3>
                <p class="text-[11px] text-brand-muted">Hierarchical links shown on desktop and mobile site headers</p>
            </div>
            <button onclick="openNewNavModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-deep hover:bg-black text-white text-xs font-semibold rounded-lg shadow-xs transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Navigation Item
            </button>
        </div>

        <div class="space-y-2 max-w-2xl">
            @forelse($navigationItems as $nav)
            <div class="p-3 rounded-xl bg-gray-50/80 border border-gray-200 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="grip-vertical" class="w-4 h-4 text-gray-400"></i>
                        <span class="font-bold text-brand-text text-sm">{{ $nav->label }}</span>
                        <span class="font-mono text-brand-muted text-[11px] bg-white px-2 py-0.5 rounded border border-gray-200">{{ $nav->url }}</span>
                        @if($nav->branch)
                        <span class="text-[10px] text-brand-primary bg-brand-canvas px-1.5 py-0.5 rounded">{{ $nav->branch->code }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold {{ $nav->is_active ? 'text-emerald-700' : 'text-gray-400' }}">
                            {{ $nav->is_active ? 'Active' : 'Disabled' }}
                        </span>
                        <button type="button" onclick="deleteCmsNav({{ $nav->id }}, '{{ addslashes($nav->label) }}')" class="text-red-500 hover:text-red-700 p-1" title="Delete link">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                @if($nav->children && $nav->children->count() > 0)
                <div class="pl-6 pt-2 border-t border-gray-200/60 space-y-1.5">
                    @foreach($nav->children as $child)
                    <div class="flex items-center justify-between py-1 px-2 rounded bg-white border border-gray-150 text-[11px]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="corner-down-right" class="w-3 h-3 text-gray-400"></i>
                            <span class="font-semibold text-brand-text">{{ $child->label }}</span>
                            <span class="font-mono text-gray-400 text-[10px]">{{ $child->url }}</span>
                        </div>
                        <button type="button" onclick="deleteCmsNav({{ $child->id }}, '{{ addslashes($child->label) }}')" class="text-red-400 hover:text-red-600 p-0.5">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="py-8 text-center text-brand-muted">
                <i data-lucide="menu" class="w-6 h-6 mx-auto mb-1 opacity-50"></i>
                <p class="text-xs">No navigation items configured. Click "Add Navigation Item" to add links.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- SUB-TAB 3: HOMEPAGE CURATION (ADM-24) -->
    <div id="cms-tab-home" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-4 space-y-3 shadow-xs">
            <div>
                <h3 class="font-bold text-sm text-brand-text">Featured Modules on Homepage</h3>
                <p class="text-[11px] text-brand-muted">Instant live toggle — changes take effect immediately on public site</p>
            </div>
            <div class="space-y-2.5 text-xs">
                <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white hover:border-brand-primary/40 cursor-pointer transition">
                    <div>
                        <span class="font-bold text-brand-text block">Branch Destinations Showcase</span>
                        <span class="text-[11px] text-brand-muted">Interactive resort location cards & tabs</span>
                    </div>
                    <input type="checkbox" onchange="toggleHomepageModule('branches', this.checked)" {{ ($homepageModules['branches'] ?? true) ? 'checked' : '' }} class="rounded text-brand-primary w-4 h-4 cursor-pointer">
                </label>
                <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white hover:border-brand-primary/40 cursor-pointer transition">
                    <div>
                        <span class="font-bold text-brand-text block">Stay & Accommodation Categories</span>
                        <span class="text-[11px] text-brand-muted">Villa, cottage, and room type cards</span>
                    </div>
                    <input type="checkbox" onchange="toggleHomepageModule('categories', this.checked)" {{ ($homepageModules['categories'] ?? true) ? 'checked' : '' }} class="rounded text-brand-primary w-4 h-4 cursor-pointer">
                </label>
                <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white hover:border-brand-primary/40 cursor-pointer transition">
                    <div>
                        <span class="font-bold text-brand-text block">Resort Facilities & Wildlife Experiences</span>
                        <span class="text-[11px] text-brand-muted">Amenities, nature walks, and activities</span>
                    </div>
                    <input type="checkbox" onchange="toggleHomepageModule('facilities', this.checked)" {{ ($homepageModules['facilities'] ?? true) ? 'checked' : '' }} class="rounded text-brand-primary w-4 h-4 cursor-pointer">
                </label>
                <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white hover:border-brand-primary/40 cursor-pointer transition">
                    <div>
                        <span class="font-bold text-brand-text block">Estate Dining & Culinary Heritage</span>
                        <span class="text-[11px] text-brand-muted">Chef specialties, restaurants, and organic farm dishes</span>
                    </div>
                    <input type="checkbox" onchange="toggleHomepageModule('dining', this.checked)" {{ ($homepageModules['dining'] ?? true) ? 'checked' : '' }} class="rounded text-brand-primary w-4 h-4 cursor-pointer">
                </label>
                <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white hover:border-brand-primary/40 cursor-pointer transition">
                    <div>
                        <span class="font-bold text-brand-text block">Krishna Spices E-Commerce Spotlight</span>
                        <span class="text-[11px] text-brand-muted">Cardamom, pepper, cinnamon showcase & buy online</span>
                    </div>
                    <input type="checkbox" onchange="toggleHomepageModule('spices', this.checked)" {{ ($homepageModules['spices'] ?? true) ? 'checked' : '' }} class="rounded text-brand-primary w-4 h-4 cursor-pointer">
                </label>
                <label class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white hover:border-brand-primary/40 cursor-pointer transition">
                    <div>
                        <span class="font-bold text-brand-text block">Verified Guest Reviews Carousel</span>
                        <span class="text-[11px] text-brand-muted">Real guest stay feedback and star ratings</span>
                    </div>
                    <input type="checkbox" onchange="toggleHomepageModule('reviews', this.checked)" {{ ($homepageModules['reviews'] ?? true) ? 'checked' : '' }} class="rounded text-brand-primary w-4 h-4 cursor-pointer">
                </label>
            </div>
        </div>

        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-4 space-y-3 shadow-xs">
            <h3 class="font-bold text-sm text-brand-text">Brand Identity & Editorial Rules</h3>
            <div class="text-xs text-brand-muted space-y-2.5">
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 space-y-1">
                    <span class="font-bold text-brand-text block">Primary Brand Tagline</span>
                    <p class="italic text-gray-700">"Come away somewhere better."</p>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 space-y-1">
                    <span class="font-bold text-brand-text block">Active Destination Scope</span>
                    <p class="text-gray-700">{{ $branches->count() }} Destination Branches · Centralised Operational Fleet</p>
                </div>
                <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200 space-y-1">
                    <span class="font-bold text-emerald-900 block">Editorial Guideline</span>
                    <p class="text-emerald-800 text-[11px]">Krishna Resorts features eco-conscious experiences and wildlife encounters near national reserves and tea estates. Real-time availability is synchronized across all public engines.</p>
                </div>
            </div>
        </div>
    </div>
</section>
