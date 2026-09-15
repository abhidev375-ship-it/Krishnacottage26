<!-- IMAGE PICKER & SWAP MODAL (UNIVERSAL MEDIA SELECTOR & CANVAS) -->
<div id="image-picker-modal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4" style="z-index: 110;">
    <div class="fixed inset-0 bg-black/75 backdrop-blur-xs transition-opacity" style="z-index: 110;" onclick="closeImagePickerModal()"></div>

    <div class="relative w-full max-w-xl max-h-[92vh] bg-brand-surface rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-gray-200 z-[111] animate-in fade-in zoom-in duration-150" style="z-index: 111;">
        <!-- Header -->
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/80 shrink-0">
            <div>
                <h3 class="text-sm font-bold text-brand-text flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 text-brand-primary"></i> Replace Photo
                </h3>
                <p class="text-[11px] text-brand-muted">Select an image from your device, enter a URL, or pick a preset</p>
            </div>
            <button onclick="closeImagePickerModal()" class="p-1.5 hover:bg-gray-200 rounded-lg text-gray-500 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Mode Navigation Tabs -->
        <div class="px-4 sm:px-5 pt-3 border-b border-gray-100 bg-gray-50/40 flex items-center gap-2 shrink-0 overflow-x-auto no-scrollbar">
            <button type="button" onclick="switchImagePickerTab('device')" id="tab-btn-device" class="px-3.5 py-2 text-xs font-bold text-brand-primary border-b-2 border-brand-primary flex items-center gap-1.5 transition shrink-0">
                <i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i> Select from Device
            </button>
            <button type="button" onclick="switchImagePickerTab('url')" id="tab-btn-url" class="px-3.5 py-2 text-xs font-medium text-gray-500 hover:text-brand-text border-b-2 border-transparent flex items-center gap-1.5 transition shrink-0">
                <i data-lucide="link-2" class="w-3.5 h-3.5"></i> Web Image URL
            </button>
            <button type="button" onclick="switchImagePickerTab('presets')" id="tab-btn-presets" class="px-3.5 py-2 text-xs font-medium text-gray-500 hover:text-brand-text border-b-2 border-transparent flex items-center gap-1.5 transition shrink-0">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Curated Presets
            </button>
        </div>

        <div class="p-4 sm:p-5 space-y-4 text-xs overflow-y-auto flex-1">
            <!-- TAB 1: UPLOAD FROM DEVICE -->
            <div id="tab-panel-device" class="space-y-3">
                <input type="file" id="image-picker-file-input" accept="image/png,image/jpeg,image/webp,image/avif,image/gif,image/svg+xml" class="hidden" onchange="handleDeviceImageSelect(this.files)">
                
                <div id="image-dropzone" onclick="document.getElementById('image-picker-file-input').click()" class="border-2 border-dashed border-gray-300 hover:border-brand-primary bg-brand-canvas/60 hover:bg-emerald-50/30 rounded-2xl p-6 text-center cursor-pointer transition flex flex-col items-center justify-center gap-2 group">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-brand-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="hard-drive-upload" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="font-bold text-brand-text text-sm">Click to select photo from device</p>
                        <p class="text-[11px] text-brand-muted mt-0.5">or drag and drop your image file here</p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-[10px] font-semibold">
                        PNG, JPG, WEBP, AVIF up to 12MB
                    </span>
                </div>

                <!-- Uploading Status Spinner -->
                <div id="upload-status-indicator" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-emerald-600"></i>
                    <span id="upload-status-text" class="font-medium">Uploading image to resort media assets...</span>
                </div>
            </div>

            <!-- TAB 2: WEB URL INPUT -->
            <div id="tab-panel-url" class="hidden space-y-2">
                <label class="font-bold text-brand-text block text-[11px]">Direct Image Web Address (URL)</label>
                <div class="flex gap-2">
                    <input type="text" id="image-picker-url-input" oninput="previewImagePickerUrl(this.value)" placeholder="https://images.unsplash.com/..." class="w-full p-2.5 bg-brand-canvas border border-gray-200 rounded-lg text-xs font-mono focus:ring-1 focus:ring-brand-primary outline-none">
                    <button type="button" onclick="previewImagePickerUrl(document.getElementById('image-picker-url-input').value)" class="px-3 bg-gray-100 hover:bg-gray-200 text-brand-text font-bold rounded-lg border border-gray-200">
                        Check
                    </button>
                </div>
                <p class="text-[10px] text-brand-muted">Paste any public HTTPS image link from Unsplash, CDN, or cloud storage.</p>
            </div>

            <!-- TAB 3: CURATED RESORT PRESETS -->
            <div id="tab-panel-presets" class="hidden space-y-1.5">
                <span class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Luxury Resort Photo Library</span>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" onclick="selectImagePreset('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=85')" class="h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary transition group relative">
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-[8px] font-bold py-0.5 text-center">Spring Water Pond</span>
                    </button>
                    <button type="button" onclick="selectImagePreset('https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1100&q=85')" class="h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary transition group relative">
                        <img src="https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-[8px] font-bold py-0.5 text-center">Valley Retreat</span>
                    </button>
                    <button type="button" onclick="selectImagePreset('https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=1100&q=85')" class="h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary transition group relative">
                        <img src="https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-[8px] font-bold py-0.5 text-center">Canopy Lodges</span>
                    </button>
                    <button type="button" onclick="selectImagePreset('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1100&q=85')" class="h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary transition group relative">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-[8px] font-bold py-0.5 text-center">Coastal View</span>
                    </button>
                    <button type="button" onclick="selectImagePreset('https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1200&q=85')" class="h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary transition group relative">
                        <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-[8px] font-bold py-0.5 text-center">Guest Suite</span>
                    </button>
                    <button type="button" onclick="selectImagePreset('https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?auto=format&fit=crop&w=900&q=85')" class="h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary transition group relative">
                        <img src="https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-[8px] font-bold py-0.5 text-center">Family</span>
                    </button>
                    <button type="button" onclick="selectImagePreset('https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=800&q=85')" class="h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary transition group relative">
                        <img src="https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-[8px] font-bold py-0.5 text-center">Dining</span>
                    </button>
                    <button type="button" onclick="selectImagePreset('https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=500&q=85')" class="h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary transition group relative">
                        <img src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=300&q=80" class="w-full h-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-black/60 text-white text-[8px] font-bold py-0.5 text-center">Spices</span>
                    </button>
                </div>
            </div>

            <!-- LIVE PREVIEW BOX (Always Visible) -->
            <div class="space-y-1.5 pt-2 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Live Preview</span>
                    <span id="image-picker-source-badge" class="text-[9px] font-semibold text-brand-primary bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">Current Image</span>
                </div>
                <div class="h-44 w-full bg-gray-100 rounded-xl overflow-hidden border border-gray-200 flex items-center justify-center relative shadow-inner">
                    <img id="image-picker-preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                    <div id="image-picker-preview-empty" class="text-center text-gray-400">
                        <i data-lucide="image" class="w-8 h-8 mx-auto mb-1 opacity-50"></i>
                        <span class="text-[11px]">No image selected</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-3.5 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-[10px] text-brand-muted">Click Apply Photo to commit to canvas</span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="closeImagePickerModal()" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-brand-text font-semibold rounded-lg transition">
                    Cancel
                </button>
                <button type="button" id="btn-apply-image-picker" onclick="applyImagePickerSelection()" class="px-4 py-1.5 bg-brand-primary hover:bg-brand-deep text-white font-bold rounded-lg shadow-xs transition flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Apply Photo</span>
                </button>
            </div>
        </div>
    </div>
</div>
