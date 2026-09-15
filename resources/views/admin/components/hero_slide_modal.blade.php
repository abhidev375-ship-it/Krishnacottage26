<!-- HERO CAROUSEL SLIDE DESIGNER & IMAGE CROPPER MODAL (z-[105]) -->
<div id="modal-hero-slide" class="fixed inset-0 z-[105] hidden items-center justify-center p-3 sm:p-5 bg-black/70 backdrop-blur-sm overflow-y-auto">
    <div class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Top Bar -->
        <div class="bg-brand-deep text-white px-6 py-4 flex items-center justify-between border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-brand-primary/80 border border-brand-accent/40 flex items-center justify-center text-brand-accent shadow-xs">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 id="hero-slide-modal-title" class="text-base font-bold text-white">Add Hero Carousel Slide</h3>
                    <p class="text-white/60 text-xs">Crop photos to proper aspect ratio, configure branch labels, headlines, and ordering</p>
                </div>
            </div>
            <button type="button" onclick="closeHeroSlideModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer" title="Close modal">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Form & Content Wrapper -->
        <form id="hero-slide-form" onsubmit="submitHeroSlideForm(event)" class="p-5 sm:p-6 space-y-6">
            <input type="hidden" id="slide-edit-index" value="">
            <input type="hidden" id="slide-id" value="">
            <input type="hidden" id="slide-image-url" value="" required>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Column (lg:col-span-6): Interactive Image Cropper & Source Picker -->
                <div class="lg:col-span-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-text">Slide Photo & Visual Cropper <span class="text-red-500">*</span></label>
                        <span class="text-[11px] font-semibold text-brand-accent bg-brand-deep/5 px-2 py-0.5 rounded-full">Recommended: 4:3</span>
                    </div>

                    <!-- Image Input Options (Local file or URL) -->
                    <div class="space-y-2">
                        <!-- Local Device File Upload -->
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-brand-primary rounded-2xl p-4 text-center bg-gray-50/70 hover:bg-emerald-50/30 transition cursor-pointer group">
                            <input type="file" id="cropper-file-input" accept="image/*" onchange="handleHeroSlideFileInput(event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="flex flex-col items-center justify-center gap-1.5 pointer-events-none">
                                <div class="w-10 h-10 rounded-full bg-white shadow-xs border border-gray-200 flex items-center justify-center text-brand-primary group-hover:scale-110 transition-transform">
                                    <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                                </div>
                                <p class="text-xs font-bold text-brand-text">Upload Image from Device</p>
                                <p class="text-[10px] text-brand-muted">Click or drag image (JPG, PNG, WebP up to 10MB)</p>
                            </div>
                        </div>

                        <!-- URL Input Option -->
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <i data-lucide="link" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="url" id="cropper-url-input" placeholder="Or paste image URL (Unsplash, CDN...)" class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition">
                            </div>
                            <button type="button" onclick="loadHeroImageFromUrl()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text text-xs font-bold rounded-xl transition shrink-0 cursor-pointer">
                                Load
                            </button>
                        </div>
                    </div>

                    <!-- Interactive Cropper Workspace Container -->
                    <div id="cropper-workspace" class="hidden space-y-3 bg-gray-900 rounded-2xl p-3 shadow-inner">
                        <!-- Aspect Ratio Selector Toolbar -->
                        <div class="flex items-center justify-between gap-1 bg-black/40 p-1.5 rounded-xl text-white text-[11px]">
                            <span class="text-white/60 font-semibold px-2 text-[10px] uppercase tracking-wider">Ratio:</span>
                            <div class="flex items-center gap-1 overflow-x-auto no-scrollbar">
                                <button type="button" onclick="setCropperRatio(4/3, this)" class="cropper-ratio-btn active px-2.5 py-1 rounded-lg bg-brand-accent text-brand-deep font-bold transition">4:3 Card</button>
                                <button type="button" onclick="setCropperRatio(16/9, this)" class="cropper-ratio-btn px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white font-medium transition">16:9 Wide</button>
                                <button type="button" onclick="setCropperRatio(1/1, this)" class="cropper-ratio-btn px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white font-medium transition">1:1 Square</button>
                                <button type="button" onclick="setCropperRatio(NaN, this)" class="cropper-ratio-btn px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white font-medium transition">Free</button>
                            </div>
                        </div>

                        <!-- Canvas Image Element -->
                        <div class="relative w-full h-64 sm:h-72 overflow-hidden rounded-xl bg-black flex items-center justify-center">
                            <img id="cropper-image-target" src="" alt="Cropping Area" class="max-w-full max-h-full block" />
                        </div>

                        <!-- Cropper Transform Controls & Action -->
                        <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="rotateCropper(-90)" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs transition cursor-pointer" title="Rotate Left 90°">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                </button>
                                <button type="button" onclick="rotateCropper(90)" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs transition cursor-pointer" title="Rotate Right 90°">
                                    <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                                </button>
                                <button type="button" onclick="zoomCropper(0.1)" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs transition cursor-pointer" title="Zoom In">
                                    <i data-lucide="zoom-in" class="w-4 h-4"></i>
                                </button>
                                <button type="button" onclick="zoomCropper(-0.1)" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs transition cursor-pointer" title="Zoom Out">
                                    <i data-lucide="zoom-out" class="w-4 h-4"></i>
                                </button>
                                <button type="button" onclick="resetCropper()" class="px-2.5 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[10px] font-bold transition cursor-pointer" title="Reset view">
                                    Reset
                                </button>
                            </div>

                            <button type="button" id="btn-crop-apply" onclick="applyAndUploadCrop()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span>Crop & Apply Image</span>
                            </button>
                        </div>
                    </div>

                    <!-- Final Active Photo Preview Box -->
                    <div id="cropper-applied-preview-box" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50/50 p-3 space-y-2">
                        <div class="flex items-center justify-between text-xs text-emerald-800 font-bold">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                                <span>Image Ready for Slide</span>
                            </span>
                            <button type="button" onclick="reopenCropperWorkspace()" class="text-xs font-semibold text-brand-primary hover:underline cursor-pointer">
                                Re-adjust crop
                            </button>
                        </div>
                        <div class="relative h-44 rounded-xl overflow-hidden border border-emerald-300 shadow-xs">
                            <img id="cropper-applied-img" src="" alt="Applied crop" class="w-full h-full object-cover">
                            <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-xs text-white text-[10px] font-mono px-2 py-0.5 rounded-md">
                                Cropped & Attached
                            </div>
                        </div>
                    </div>

                    <div id="cropper-error-box" class="hidden p-3 rounded-xl bg-red-50 text-red-700 text-xs flex items-center gap-2 border border-red-200">
                        <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
                        <span id="cropper-error-text">Please upload or provide an image.</span>
                    </div>

                </div>

                <!-- Right Column (lg:col-span-6): Slide Content & Branch Details -->
                <div class="lg:col-span-6 space-y-4">
                    
                    <!-- Quick-fill from existing Resort Branch -->
                    <div class="bg-brand-canvas p-3 rounded-2xl border border-gray-200 space-y-1.5">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-brand-muted">Quick-fill from Resort Branch</label>
                        <select id="slide-branch-helper" onchange="handleBranchHelperSelect(this.value)" class="w-full text-xs font-semibold text-brand-text bg-white p-2 rounded-xl border border-gray-300 focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20">
                            <option value="">-- Choose Branch to auto-fill details (Optional) --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" 
                                    data-name="{{ $b->display_name ?: $b->name }}"
                                    data-city="{{ $b->city ?: 'Kerala' }}"
                                    data-tagline="{{ $b->tagline ?: 'Private wooden cottages, lush gardens & tranquil verandas.' }}"
                                    data-image="{{ $b->cover_image_url ?: ($b->hero_image_url ?: '') }}"
                                    data-link="{{ route('rooms.index', ['branch_id' => $b->id], false) }}">
                                    {{ $b->name }} ({{ $b->city }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Slide Title -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-text mb-1">Slide Title / Branch Name <span class="text-red-500">*</span></label>
                        <input type="text" id="slide-title" required placeholder="e.g. Krishna Valley & Garden Retreat" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition font-medium">
                    </div>

                    <!-- Location Tag & Subtitle -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-text mb-1">Pill Tag (Top Left)</label>
                            <input type="text" id="slide-tag" placeholder="e.g. Munnar · Hillside Cottages" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-text mb-1">Subtitle / Region</label>
                            <input type="text" id="slide-subtitle" placeholder="e.g. Munnar Hills" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition">
                        </div>
                    </div>

                    <!-- Slide Description -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-brand-text mb-1">Slide Description <span class="text-red-500">*</span></label>
                        <textarea id="slide-description" rows="3" required placeholder="Short, mature description of the branch or experience (e.g. Misty morning tea plantations, private wooden verandahs & pure nature)..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition leading-relaxed"></textarea>
                    </div>

                    <!-- Rating Badge & Action Link -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-text mb-1">Rating / Badge Tag</label>
                            <input type="text" id="slide-badge" placeholder="e.g. ★ 4.9 Rating or Premium Villa" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-text mb-1">Destination Link</label>
                            <input type="text" id="slide-link" placeholder="e.g. /rooms?branch_id=1" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition">
                        </div>
                    </div>

                    <!-- Sort Order & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-text mb-1">Slide Display Order</label>
                            <input type="number" id="slide-sort-order" min="1" max="99" value="1" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-brand-text mb-1">Slide Status</label>
                            <select id="slide-status" class="w-full px-3.5 py-2 text-xs rounded-xl border border-gray-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-primary/20 transition">
                                <option value="active">Active (Visible in Carousel)</option>
                                <option value="hidden">Hidden (Draft)</option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Modal Footer Controls -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                <div class="text-[11px] text-brand-muted">
                    <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1 text-brand-accent"></i>
                    <span>Changes update immediately in both the live website and visual canvas.</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeHeroSlideModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-bold text-gray-700 transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="btn-save-slide" class="px-6 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold shadow-md transition flex items-center gap-2 cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4 text-brand-accent"></i>
                        <span>Save & Publish Slide</span>
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>
