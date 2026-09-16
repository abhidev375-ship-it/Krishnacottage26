@extends('layouts.customer')

@section('title', 'Photo Gallery & Visual Journal | Krishna Cottages')

@section('content')
<!-- HERO SECTION -->
<div class="bg-forest text-paper py-6 sm:py-10 md:py-12 px-4 sm:px-6 relative overflow-hidden">
    <div class="mx-auto max-w-[1480px] text-center relative z-10">
        <span class="eyebrow text-brass block mb-2">Visual Journeys</span>
        <h1 class="serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight">The Cottages Gallery</h1>
        <p class="text-xs sm:text-sm text-paper/70 max-w-2xl mx-auto mt-3 leading-relaxed">
            Moments of stillness, mist-draped plantation mornings, heritage wooden architecture, and tranquil nature trails.
        </p>
    </div>
</div>

<!-- FILTERS SECTION (BRANCHES & CATEGORIES) -->
<div class="bg-paper/95 backdrop-blur-md border-b border-forest/10 sticky top-16 md:top-20 z-30 shadow-xs">
    <div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-3 space-y-2.5">
        <!-- ROW 1: BRANCH FILTERS (All Branches as Default) -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
            <span class="text-[10px] font-bold text-forest/50 uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1">
                <i data-lucide="map-pin" class="w-3 h-3 text-brass"></i> Location:
            </span>
            <a href="{{ route('gallery.index', array_filter(['category' => $selectedCategory])) }}" 
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition shrink-0 {{ empty($selectedBranchId) || $selectedBranchId === 'all' ? 'bg-forest text-paper shadow-xs' : 'bg-white/80 text-forest/70 hover:bg-white soft-border' }}">
                All Branches
            </a>
            @foreach($branches as $b)
            <a href="{{ route('gallery.index', array_filter(['branch_id' => $b->id, 'category' => $selectedCategory])) }}" 
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition shrink-0 {{ (string)$selectedBranchId === (string)$b->id ? 'bg-forest text-paper shadow-xs' : 'bg-white/80 text-forest/70 hover:bg-white soft-border' }}">
                {{ $b->name }}
            </a>
            @endforeach
            <a href="{{ route('gallery.index', array_filter(['branch_id' => 'resort', 'category' => $selectedCategory])) }}" 
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition shrink-0 {{ $selectedBranchId === 'resort' || $selectedBranchId === 'none' ? 'bg-forest text-paper shadow-xs' : 'bg-white/80 text-forest/70 hover:bg-white soft-border' }}">
                Cottages-Wide
            </a>
        </div>

        <!-- ROW 2: CATEGORY FILTER PILLS -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-1 border-t border-forest/5">
            <span class="text-[10px] font-bold text-forest/50 uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1">
                <i data-lucide="tag" class="w-3 h-3 text-brass"></i> Theme:
            </span>
            <a href="{{ route('gallery.index', array_filter(['branch_id' => $selectedBranchId])) }}" 
               class="px-3 py-1 rounded-full text-xs font-medium transition shrink-0 {{ empty($selectedCategory) || $selectedCategory === 'all' ? 'bg-brass text-forest font-bold' : 'text-forest/70 hover:text-forest bg-white/50 hover:bg-white soft-border' }}">
                All Themes ({{ $albums->count() }})
            </a>
            <a href="{{ route('gallery.index', array_filter(['category' => 'architecture', 'branch_id' => $selectedBranchId])) }}" 
               class="px-3 py-1 rounded-full text-xs font-medium transition shrink-0 {{ $selectedCategory === 'architecture' ? 'bg-brass text-forest font-bold' : 'text-forest/70 hover:text-forest bg-white/50 hover:bg-white soft-border' }}">
                Architecture
            </a>
            <a href="{{ route('gallery.index', array_filter(['category' => 'rooms', 'branch_id' => $selectedBranchId])) }}" 
               class="px-3 py-1 rounded-full text-xs font-medium transition shrink-0 {{ $selectedCategory === 'rooms' ? 'bg-brass text-forest font-bold' : 'text-forest/70 hover:text-forest bg-white/50 hover:bg-white soft-border' }}">
                Villas & Suites
            </a>
            <a href="{{ route('gallery.index', array_filter(['category' => 'nature', 'branch_id' => $selectedBranchId])) }}" 
               class="px-3 py-1 rounded-full text-xs font-medium transition shrink-0 {{ $selectedCategory === 'nature' ? 'bg-brass text-forest font-bold' : 'text-forest/70 hover:text-forest bg-white/50 hover:bg-white soft-border' }}">
                Plantation & Nature
            </a>
            <a href="{{ route('gallery.index', array_filter(['category' => 'dining', 'branch_id' => $selectedBranchId])) }}" 
               class="px-3 py-1 rounded-full text-xs font-medium transition shrink-0 {{ $selectedCategory === 'dining' ? 'bg-brass text-forest font-bold' : 'text-forest/70 hover:text-forest bg-white/50 hover:bg-white soft-border' }}">
                Dining Moments
            </a>
        </div>
    </div>
</div>

<!-- ALBUMS GRID -->
<div class="mx-auto max-w-[1480px] px-4 sm:px-6 py-8 sm:py-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        @forelse($albums as $album)
        <div class="bg-white rounded-[26px] soft-border overflow-hidden shadow-card lift transition duration-300 group flex flex-col cursor-pointer" onclick="openAlbumViewer({{ $album->id }})">
            <!-- Cover image container -->
            <div class="img-zoom relative h-64 w-full overflow-hidden bg-mint">
                <img src="{{ $album->cover_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80' }}" 
                     alt="{{ $album->name ?? 'Cottage Album' }}" 
                     class="w-full h-full object-cover">
                
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                <!-- Top badges -->
                <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between pointer-events-none">
                    <span class="eyebrow text-brass bg-black/50 backdrop-blur-md px-2.5 py-1 rounded-full">
                        {{ $album->branch ? $album->branch->name : 'Cottages Wide' }}
                    </span>
                    <span class="bg-black/60 backdrop-blur-md text-paper text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1.5">
                        <i data-lucide="images" class="w-3.5 h-3.5 text-brass"></i>
                        <span>{{ $album->images->count() }} Photos</span>
                    </span>
                </div>

                <!-- Bottom title overlay -->
                <div class="absolute bottom-4 left-4 right-4 text-paper">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-brass/90 block mb-0.5 capitalize">
                        {{ $album->category }}
                    </span>
                    <h3 class="serif text-xl font-bold text-paper mt-0.5 leading-snug">
                        {{ $album->name ?: 'Cottage Album' }}
                    </h3>
                </div>
            </div>

            <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                <p class="text-xs text-forest/70 leading-relaxed line-clamp-2">
                    {{ $album->description ?: 'Explore photographs from this curated collection.' }}
                </p>

                <!-- THUMBNAILS PREVIEW ROW -->
                @if($album->images->isNotEmpty())
                <div class="grid grid-cols-4 gap-2 pt-1">
                    @foreach($album->images->take(4) as $idx => $img)
                    <div class="relative h-14 rounded-xl overflow-hidden soft-border bg-forest/5 group/thumb hover:opacity-90 transition">
                        <img src="{{ $img->image_url }}" alt="{{ $img->title }}" class="w-full h-full object-cover">
                        @if($idx === 3 && $album->images->count() > 4)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-paper text-xs font-bold">
                                +{{ $album->images->count() - 4 }}
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="pt-3 border-t border-forest/10 flex items-center justify-between text-xs font-bold text-forest group-hover:text-forest/80">
                    <span class="flex items-center gap-1 text-brass">
                        <span>Open Album</span>
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </span>
                    <span class="text-[11px] text-forest/40 font-normal">Click to browse all photos</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-[26px] soft-border">
            <i data-lucide="image" class="w-12 h-12 text-brass mx-auto mb-3 opacity-60"></i>
            <h3 class="serif text-xl font-bold text-forest">No Albums Found</h3>
            <p class="text-xs text-forest/60 mt-1 max-w-md mx-auto">There are no photo albums matching your current branch or category selection. Try switching to "All Branches" or another category.</p>
            <a href="{{ route('gallery.index') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-forest text-paper text-xs font-semibold hover:bg-forest/90 transition shadow-xs">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Reset All Filters
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- INTERACTIVE ALBUM VIEWER MODAL -->
<div id="album-viewer-modal" class="hidden fixed inset-0 z-[95] bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div class="bg-paper w-full max-w-5xl rounded-[28px] shadow-2xl border border-forest/10 overflow-hidden flex flex-col max-h-[92vh] my-auto">
        <!-- MODAL HEADER -->
        <div class="bg-forest text-paper p-5 sm:p-6 relative shrink-0">
            <button onclick="closeAlbumViewer()" class="absolute top-4 sm:top-5 right-4 sm:right-5 text-paper/80 hover:text-paper bg-white/10 hover:bg-white/20 p-2 rounded-full transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="max-w-2xl pr-8">
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span id="viewer-branch" class="eyebrow text-brass bg-white/10 px-2.5 py-0.5 rounded-full"></span>
                    <span id="viewer-category" class="text-[10px] uppercase font-bold tracking-wider text-paper/70 bg-white/5 px-2 py-0.5 rounded-full"></span>
                    <span id="viewer-count" class="text-[10px] font-bold text-brass bg-brass/20 px-2 py-0.5 rounded-full"></span>
                </div>
                <h2 id="viewer-title" class="serif text-2xl sm:text-3xl font-bold text-paper"></h2>
                <p id="viewer-desc" class="text-xs sm:text-sm text-paper/70 mt-2 leading-relaxed"></p>
            </div>
        </div>

        <!-- MODAL BODY: PHOTO GRID -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar">
            <div id="viewer-photos-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Populated dynamically via JS -->
            </div>
            <div id="viewer-empty-state" class="hidden py-12 text-center text-forest/50 text-xs">
                No photographs in this album yet.
            </div>
        </div>

        <!-- MODAL FOOTER -->
        <div class="p-4 bg-white border-t border-forest/10 flex items-center justify-between text-xs text-forest/60 shrink-0">
            <span>Click any photo to open full-screen slideshow</span>
            <button onclick="closeAlbumViewer()" class="px-4 py-1.5 rounded-full bg-forest/10 hover:bg-forest/20 text-forest font-semibold transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- FULLSCREEN LIGHTBOX SLIDESHOW MODAL -->
<div id="lightbox-modal" class="hidden fixed inset-0 z-[120] bg-black/95 flex flex-col justify-between p-4 sm:p-6 select-none">
    <!-- Top toolbar -->
    <div class="flex items-center justify-between text-white z-50">
        <div class="flex items-center gap-3">
            <span id="lightbox-counter" class="text-xs font-mono bg-white/15 px-3 py-1 rounded-full text-brass">1 / 1</span>
            <span id="lightbox-album-tag" class="text-xs text-white/70 hidden sm:inline"></span>
        </div>
        <button onclick="closeLightbox()" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 p-2.5 rounded-full transition">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Main display with Prev / Next Navigation -->
    <div class="relative flex-1 flex items-center justify-center my-2 overflow-hidden">
        <!-- Prev button -->
        <button id="lightbox-prev-btn" onclick="lightboxPrev(event)" class="absolute left-2 sm:left-6 z-40 text-white/80 hover:text-white bg-black/60 hover:bg-black/80 p-3 rounded-full backdrop-blur-xs transition shadow-lg">
            <i data-lucide="chevron-left" class="w-6 h-6 sm:w-8 sm:h-8"></i>
        </button>

        <img id="lightbox-img" src="" class="max-h-[75vh] max-w-[92vw] sm:max-w-[85vw] rounded-xl sm:rounded-2xl object-contain shadow-2xl transition duration-200">

        <!-- Next button -->
        <button id="lightbox-next-btn" onclick="lightboxNext(event)" class="absolute right-2 sm:right-6 z-40 text-white/80 hover:text-white bg-black/60 hover:bg-black/80 p-3 rounded-full backdrop-blur-xs transition shadow-lg">
            <i data-lucide="chevron-right" class="w-6 h-6 sm:w-8 sm:h-8"></i>
        </button>
    </div>

    <!-- Bottom Caption Bar -->
    <div class="text-center py-2 z-50">
        <p id="lightbox-caption" class="serif text-base sm:text-lg font-bold text-white max-w-2xl mx-auto"></p>
        <p class="text-[11px] text-white/40 mt-1">Use Left / Right arrow keys to navigate &middot; Esc to close</p>
    </div>
</div>

@push('scripts')
<script>
    // Client Album Data Registry
    const clientAlbums = {!! json_encode($albums->map(function($a) {
        return [
            'id' => $a->id,
            'name' => $a->name ?: 'Cottage Album',
            'branch' => $a->branch ? $a->branch->name : 'Cottages-Wide',
            'category' => ucfirst($a->category),
            'description' => $a->description ?: 'Visual moments captured at Krishna Cottages.',
            'cover_url' => $a->cover_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
            'images' => $a->images->values()->map(function($img) {
                return [
                    'id' => $img->id,
                    'url' => $img->image_url,
                    'title' => $img->title ?: 'Photograph'
                ];
            })
        ];
    })->values()) !!};

    let currentOpenAlbum = null;
    let currentLightboxList = [];
    let currentLightboxIndex = 0;

    // Open Album Viewer
    function openAlbumViewer(albumId) {
        const album = clientAlbums.find(a => a.id === albumId);
        if (!album) return;

        currentOpenAlbum = album;
        document.getElementById('viewer-branch').textContent = album.branch;
        document.getElementById('viewer-category').textContent = album.category;
        document.getElementById('viewer-count').textContent = `${album.images.length} Photos`;
        document.getElementById('viewer-title').textContent = album.name;
        document.getElementById('viewer-desc').textContent = album.description;

        const grid = document.getElementById('viewer-photos-grid');
        grid.innerHTML = '';

        if (album.images.length === 0) {
            document.getElementById('viewer-empty-state').classList.remove('hidden');
        } else {
            document.getElementById('viewer-empty-state').classList.add('hidden');
            album.images.forEach((img, idx) => {
                const item = document.createElement('div');
                item.className = 'group relative rounded-2xl overflow-hidden soft-border bg-forest/5 shadow-xs cursor-pointer h-52 transition hover:shadow-md';
                item.onclick = () => openLightboxForAlbum(albumId, idx);
                item.innerHTML = `
                    <img src="${img.url}" alt="${img.title}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-3.5 text-paper">
                        <span class="text-xs font-bold leading-tight line-clamp-1">${img.title}</span>
                        <span class="text-[10px] text-brass mt-0.5 flex items-center gap-1"><i data-lucide="zoom-in" class="w-3 h-3"></i> View Fullscreen</span>
                    </div>
                `;
                grid.appendChild(item);
            });
        }

        document.getElementById('album-viewer-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (window.lucide) lucide.createIcons();
    }

    function closeAlbumViewer() {
        document.getElementById('album-viewer-modal').classList.add('hidden');
        if (document.getElementById('lightbox-modal').classList.contains('hidden')) {
            document.body.style.overflow = '';
        }
    }

    // Open Lightbox for Album Photos
    function openLightboxForAlbum(albumId, photoIndex) {
        const album = clientAlbums.find(a => a.id === albumId);
        if (!album || !album.images.length) return;

        currentLightboxList = album.images;
        currentLightboxIndex = photoIndex;
        document.getElementById('lightbox-album-tag').textContent = `${album.name} (${album.branch})`;
        renderLightboxCurrent();
        document.getElementById('lightbox-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (window.lucide) lucide.createIcons();
    }

    // Single photo direct open (fallback for direct links)
    function openLightbox(url, caption) {
        currentLightboxList = [{ url: url, title: caption || 'Cottage Photograph' }];
        currentLightboxIndex = 0;
        document.getElementById('lightbox-album-tag').textContent = '';
        renderLightboxCurrent();
        document.getElementById('lightbox-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (window.lucide) lucide.createIcons();
    }

    function renderLightboxCurrent() {
        if (!currentLightboxList.length) return;
        const photo = currentLightboxList[currentLightboxIndex];
        const imgEl = document.getElementById('lightbox-img');
        imgEl.src = photo.url;
        imgEl.alt = photo.title || 'Cottage Photograph';
        document.getElementById('lightbox-caption').textContent = photo.title || '';
        document.getElementById('lightbox-counter').textContent = `${currentLightboxIndex + 1} / ${currentLightboxList.length}`;

        // Toggle nav buttons visibility if single image
        const prevBtn = document.getElementById('lightbox-prev-btn');
        const nextBtn = document.getElementById('lightbox-next-btn');
        if (currentLightboxList.length <= 1) {
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.remove('hidden');
        }
    }

    function lightboxPrev(e) {
        if (e) e.stopPropagation();
        if (currentLightboxList.length <= 1) return;
        currentLightboxIndex = (currentLightboxIndex - 1 + currentLightboxList.length) % currentLightboxList.length;
        renderLightboxCurrent();
    }

    function lightboxNext(e) {
        if (e) e.stopPropagation();
        if (currentLightboxList.length <= 1) return;
        currentLightboxIndex = (currentLightboxIndex + 1) % currentLightboxList.length;
        renderLightboxCurrent();
    }

    function closeLightbox() {
        document.getElementById('lightbox-modal').classList.add('hidden');
        if (document.getElementById('album-viewer-modal').classList.contains('hidden')) {
            document.body.style.overflow = '';
        }
    }

    // Backdrop click handlers
    document.getElementById('album-viewer-modal')?.addEventListener('click', (e) => {
        if (e.target.id === 'album-viewer-modal') closeAlbumViewer();
    });

    document.getElementById('lightbox-modal')?.addEventListener('click', (e) => {
        if (e.target.id === 'lightbox-modal') closeLightbox();
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        const lb = document.getElementById('lightbox-modal');
        const av = document.getElementById('album-viewer-modal');

        if (!lb.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                lightboxPrev();
            } else if (e.key === 'ArrowRight') {
                lightboxNext();
            }
        } else if (!av.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeAlbumViewer();
            }
        }
    });
</script>
@endpush
@endsection
