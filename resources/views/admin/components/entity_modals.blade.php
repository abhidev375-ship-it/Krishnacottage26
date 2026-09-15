<!-- ================= ENTITY CRUD ACTION MODALS ================= -->

<!-- 1. ADD / EDIT BRANCH MODAL (ADM-09) -->
<div id="modal-branch" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add New Resort Branch</h3>
            </div>
            <button onclick="closeModal('modal-branch')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-branch" onsubmit="handleEntitySubmit(event, '{{ route('admin.branches.store') }}', 'modal-branch')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Krishna · Munnar Mist" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Code *</label>
                    <input type="text" name="code" required placeholder="e.g. BRD" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs uppercase font-mono">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Display Name / Title</label>
                <input type="text" name="display_name" placeholder="e.g. Krishna Valley & Garden Retreat" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Physical Location / Street Address *</label>
                <input type="text" name="address" required placeholder="e.g. Top Station Road, Mattupetty Post" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">City / Region *</label>
                    <input type="text" id="add-branch-city" name="city" required placeholder="Munnar" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">State *</label>
                    <input type="text" id="add-branch-state" name="state" required value="Kerala" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Postal Code</label>
                    <input type="text" name="pincode" placeholder="685612" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Latitude (Optional)</label>
                    <input type="number" step="any" id="add-branch-latitude" name="latitude" placeholder="e.g. 10.0889" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Longitude (Optional)</label>
                    <input type="number" step="any" id="add-branch-longitude" name="longitude" placeholder="e.g. 77.0595" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
            </div>

            <!-- Auto-detect GPS coordinates helper -->
            <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50 border border-emerald-200/60 text-[11px]">
                <div class="flex items-center gap-1.5 text-gray-700 font-medium">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Live GPS &amp; Weather auto-syncs from City</span>
                </div>
                <button type="button" onclick="autoDetectBranchCoords('add')" class="px-2.5 py-1 rounded-md bg-[#063F34] text-white text-[10px] font-bold hover:bg-[#0B5D4B] transition cursor-pointer">
                    Auto-Fill GPS
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1 flex items-center justify-between">
                        <span class="flex items-center gap-1">
                            <i data-lucide="phone" class="w-3 h-3 text-emerald-600"></i>
                            <span>Mobile / Phone Number</span>
                        </span>
                        <span class="text-[9px] text-brand-muted font-normal">Customer Call Button</span>
                    </label>
                    <input type="tel" name="phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="branch@krishnaresorts.com" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Operational Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="active">Active & Operational</option>
                        <option value="maintenance">Under Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tagline & Ambience</label>
                    <input type="text" name="tagline" placeholder="Quiet views & garden stays amidst rolling mist" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Hero Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="branch-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="image" id="branch-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="hero_image_url" id="branch-image-url" oninput="updateFieldThumbnailPreview('branch-image-url', this.value)" placeholder="Enter image URL or choose file from device..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'branch-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('branch-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cover Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="branch-cover-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="image" id="branch-cover-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="cover_image_url" id="branch-cover-image-url" oninput="updateFieldThumbnailPreview('branch-cover-image-url', this.value)" placeholder="Enter image URL or choose file from device..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'branch-cover-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('branch-cover-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-branch')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save Branch</button>
            </div>
        </form>
    </div>
</div>

<!-- 1B. EDIT RESORT BRANCH MODAL -->
<div id="modal-edit-branch" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm" id="edit-branch-modal-title">Edit Resort Branch</h3>
            </div>
            <button onclick="closeModal('modal-edit-branch')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-branch" onsubmit="submitEditBranchForm(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-branch-id" name="branch_id">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Name *</label>
                    <input type="text" id="edit-branch-name" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Code</label>
                    <input type="text" id="edit-branch-code" disabled class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-100 text-gray-500 font-mono text-xs cursor-not-allowed uppercase">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Display Name / Title</label>
                <input type="text" id="edit-branch-display-name" name="display_name" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">City / Region *</label>
                    <input type="text" id="edit-branch-city" name="city" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">State *</label>
                    <input type="text" id="edit-branch-state" name="state" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Latitude (Optional)</label>
                    <input type="number" step="any" id="edit-branch-latitude" name="latitude" placeholder="e.g. 10.0889" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Longitude (Optional)</label>
                    <input type="number" step="any" id="edit-branch-longitude" name="longitude" placeholder="e.g. 77.0595" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
            </div>

            <!-- Auto-detect GPS coordinates helper -->
            <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50 border border-emerald-200/60 text-[11px]">
                <div class="flex items-center gap-1.5 text-gray-700 font-medium">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Live GPS &amp; Weather auto-syncs from City</span>
                </div>
                <button type="button" onclick="autoDetectBranchCoords('edit')" class="px-2.5 py-1 rounded-md bg-[#063F34] text-white text-[10px] font-bold hover:bg-[#0B5D4B] transition cursor-pointer">
                    Auto-Fill GPS
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1 flex items-center justify-between">
                        <span class="flex items-center gap-1">
                            <i data-lucide="phone" class="w-3 h-3 text-emerald-600"></i>
                            <span>Mobile / Phone Number</span>
                        </span>
                        <span class="text-[9px] text-brand-muted font-normal">Customer Call Button</span>
                    </label>
                    <input type="tel" id="edit-branch-phone" name="phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="edit-branch-email" name="email" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Operational Status</label>
                    <select id="edit-branch-status" name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="active">Active & Operational</option>
                        <option value="maintenance">Under Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tagline & Ambience</label>
                    <input type="text" id="edit-branch-tagline" name="tagline" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Hero Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="edit-branch-hero-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="image" id="edit-branch-hero-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" id="edit-branch-hero-url" name="hero_image_url" oninput="updateFieldThumbnailPreview('edit-branch-hero-url', this.value)" placeholder="Enter image URL or choose file from device..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'edit-branch-hero-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('edit-branch-hero-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cover Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="edit-branch-cover-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="image" id="edit-branch-cover-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" id="edit-branch-cover-url" name="cover_image_url" oninput="updateFieldThumbnailPreview('edit-branch-cover-url', this.value)" placeholder="Enter image URL or choose file from device..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'edit-branch-cover-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('edit-branch-cover-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-branch')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Update Branch</button>
            </div>
        </form>
    </div>
</div>


<!-- 2. ADD ROOM TYPE MODAL (ADM-05) -->
<!-- 2. ADD ROOM TYPE MODAL (ADM-05) -->
<div id="modal-room-type" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="bed-double" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add New Room Type / Suite</h3>
            </div>
            <button onclick="closeModal('modal-room-type')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-room-type" onsubmit="handleEntitySubmit(event, '{{ route('admin.room-types.store') }}', 'modal-room-type')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch *</label>
                    <select name="branch_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Room Category *</label>
                    <select name="room_category_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="">Select Category...</option>
                        @foreach($roomCategories as $rc)
                            <option value="{{ $rc->id }}">{{ $rc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Suite Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Teakwood Forest Villa" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Base Nightly Price (INR) *</label>
                    <input type="number" name="base_price" required min="100" step="50" placeholder="6500" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Weekend Price (INR)</label>
                    <input type="number" name="weekend_price" min="100" step="50" placeholder="7500" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Max Total Guests *</label>
                    <input type="number" name="max_guests" value="3" min="1" max="15" placeholder="3" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-bold text-brand-primary">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Max Adults *</label>
                    <input type="number" name="max_adults" value="2" min="1" max="10" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Max Children</label>
                    <input type="number" name="max_children" value="1" min="0" max="6" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Size (Sq.Ft)</label>
                    <input type="number" name="size_sqft" value="480" min="50" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="p-2.5 rounded-xl bg-emerald/5 border border-emerald/15 text-[11px] text-brand-primary flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 shrink-0 text-emerald mt-0.5"></i>
                <div class="leading-tight">
                    <span class="font-bold">Occupancy & Bedding Rule:</span>
                    <span>Adults cannot exceed <strong class="underline">Max Adults</strong>. Children can occupy unused adult bed spaces up to <strong class="underline">Max Total Guests</strong>.</span>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Bed Configuration</label>
                <input type="text" name="bed_type" value="1 King Bed" placeholder="e.g. 1 King Bed or 2 Twin Beds" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Short Tagline / Overview</label>
                <input type="text" name="short_description" placeholder="Private teakwood cottage overlooking dense forest..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Full Description</label>
                <textarea name="description" rows="2" placeholder="Detailed suite description, architecture, and slow-living atmosphere..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>

            <!-- Dynamic Amenities Multi-Select -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1.5">Amenities Library Selection</label>
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl max-h-40 overflow-y-auto space-y-3">
                    @foreach($amenities->groupBy('category') as $grpName => $grpItems)
                        <div>
                            <span class="text-[10px] uppercase font-bold text-gray-500 tracking-wider block mb-1">{{ $grpName }}</span>
                            <div class="grid grid-cols-2 gap-1.5 text-xs">
                                @foreach($grpItems as $am)
                                    <label class="flex items-center gap-1.5 hover:bg-white p-1 rounded transition cursor-pointer">
                                        <input type="checkbox" name="amenity_ids[]" value="{{ $am->id }}" class="rounded text-brand-primary focus:ring-brand-primary">
                                        <span class="truncate">{{ $am->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cover Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="roomtype-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="image" id="roomtype-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="cover_image_url" id="roomtype-image-url" oninput="updateFieldThumbnailPreview('roomtype-image-url', this.value)" placeholder="Enter URL or upload cover photo..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'roomtype-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('roomtype-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[11px] font-semibold text-gray-700">Room Gallery Photos (Multiple)</label>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-bold text-brand-primary bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded cursor-pointer flex items-center gap-1 transition">
                            <i data-lucide="upload" class="w-3 h-3"></i> Upload Files
                            <input type="file" multiple accept="image/*" class="hidden" onchange="uploadMultipleFilesForGallery(this, 'rt-gallery-chips-new', 'gallery_images[]')">
                        </label>
                    </div>
                </div>
                <div class="flex gap-1.5 mb-1.5">
                    <input type="text" id="rt-gallery-url-input-new" placeholder="Or paste image URL..." class="flex-1 px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs">
                    <button type="button" onclick="addManualUrlToGallery('rt-gallery-url-input-new', 'rt-gallery-chips-new', 'gallery_images[]')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold">Add URL</button>
                </div>
                <div id="rt-gallery-chips-new" class="flex flex-wrap gap-2 p-2 bg-gray-50 border border-gray-200 rounded-xl min-h-[52px]">
                    <span class="text-[10px] text-gray-400 self-center px-1">Attached gallery photos will appear here</span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-room-type')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs cursor-pointer">Save Room Type</button>
            </div>
        </form>
    </div>
</div>

<!-- 2B. EDIT ROOM TYPE MODAL (ADM-05) -->
<div id="modal-edit-room-type" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Edit Room Type / Suite</h3>
            </div>
            <button onclick="closeModal('modal-edit-room-type')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-room-type" onsubmit="handleEditRoomTypeSubmit(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-room-type-id" name="id">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch *</label>
                    <select name="branch_id" id="edit-rt-branch-id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Room Category *</label>
                    <select name="room_category_id" id="edit-rt-category-id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="">Select Category...</option>
                        @foreach($roomCategories as $rc)
                            <option value="{{ $rc->id }}">{{ $rc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Suite Name *</label>
                    <input type="text" name="name" id="edit-rt-name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Base Nightly Price (INR) *</label>
                    <input type="number" name="base_price" id="edit-rt-base-price" required min="100" step="50" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Weekend Price (INR)</label>
                    <input type="number" name="weekend_price" id="edit-rt-weekend-price" min="100" step="50" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Max Total Guests *</label>
                    <input type="number" name="max_guests" id="edit-rt-max-guests" min="1" max="15" placeholder="3" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-bold text-brand-primary">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Max Adults *</label>
                    <input type="number" name="max_adults" id="edit-rt-max-adults" min="1" max="10" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Max Children</label>
                    <input type="number" name="max_children" id="edit-rt-max-children" min="0" max="6" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Size (Sq.Ft)</label>
                    <input type="number" name="size_sqft" id="edit-rt-size-sqft" min="50" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="p-2.5 rounded-xl bg-emerald/5 border border-emerald/15 text-[11px] text-brand-primary flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 shrink-0 text-emerald mt-0.5"></i>
                <div class="leading-tight">
                    <span class="font-bold">Occupancy & Bedding Rule:</span>
                    <span>Adults cannot exceed <strong class="underline">Max Adults</strong>. Children can occupy unused adult bed spaces up to <strong class="underline">Max Total Guests</strong>.</span>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Bed Configuration</label>
                <input type="text" name="bed_type" id="edit-rt-bed-type" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Short Tagline / Overview</label>
                <input type="text" name="short_description" id="edit-rt-short-desc" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Full Description</label>
                <textarea name="description" id="edit-rt-description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>

            <!-- Dynamic Amenities Multi-Select -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1.5">Amenities Library Selection</label>
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl max-h-40 overflow-y-auto space-y-3">
                    @foreach($amenities->groupBy('category') as $grpName => $grpItems)
                        <div>
                            <span class="text-[10px] uppercase font-bold text-gray-500 tracking-wider block mb-1">{{ $grpName }}</span>
                            <div class="grid grid-cols-2 gap-1.5 text-xs">
                                @foreach($grpItems as $am)
                                    <label class="flex items-center gap-1.5 hover:bg-white p-1 rounded transition cursor-pointer">
                                        <input type="checkbox" name="amenity_ids[]" value="{{ $am->id }}" class="edit-rt-amenity-checkbox rounded text-brand-primary focus:ring-brand-primary">
                                        <span class="truncate">{{ $am->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cover Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="edit-roomtype-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="image" id="edit-roomtype-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="cover_image_url" id="edit-roomtype-image-url" oninput="updateFieldThumbnailPreview('edit-roomtype-image-url', this.value)" placeholder="Enter URL or choose file..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'edit-roomtype-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('edit-roomtype-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[11px] font-semibold text-gray-700">Room Gallery Photos (Multiple)</label>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-bold text-brand-primary bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded cursor-pointer flex items-center gap-1 transition">
                            <i data-lucide="upload" class="w-3 h-3"></i> Upload Files
                            <input type="file" multiple accept="image/*" class="hidden" onchange="uploadMultipleFilesForGallery(this, 'edit-rt-gallery-chips', 'gallery_images[]')">
                        </label>
                    </div>
                </div>
                <div class="flex gap-1.5 mb-1.5">
                    <input type="text" id="edit-rt-gallery-url-input" placeholder="Or paste image URL..." class="flex-1 px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs">
                    <button type="button" onclick="addManualUrlToGallery('edit-rt-gallery-url-input', 'edit-rt-gallery-chips', 'gallery_images[]')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold">Add URL</button>
                </div>
                <div id="edit-rt-gallery-chips" class="flex flex-wrap gap-2 p-2 bg-gray-50 border border-gray-200 rounded-xl min-h-[52px]">
                    <span class="text-[10px] text-gray-400 self-center px-1">Attached gallery photos will appear here</span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-room-type')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs cursor-pointer">Update Room Type</button>
            </div>
        </form>
    </div>
</div>

<!-- 2C. ADD ROOM CATEGORY MODAL (ADM-05) -->
<div id="modal-room-category" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="palmtree" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add Room Category</h3>
            </div>
            <button onclick="closeModal('modal-room-category')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-room-category" onsubmit="handleEntitySubmit(event, '{{ route('admin.room-categories.store') }}', 'modal-room-category')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. Waterfront Pavilions" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Lucide Icon Identifier</label>
                <input type="text" name="icon" value="palmtree" placeholder="palmtree, castle, trees, droplet, home..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Architectural overview of this cottage category..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>
            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-room-category')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs cursor-pointer">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- 2D. EDIT ROOM CATEGORY MODAL (ADM-05) -->
<div id="modal-edit-room-category" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Edit Room Category</h3>
            </div>
            <button onclick="closeModal('modal-edit-room-category')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-room-category" onsubmit="handleEditCategorySubmit(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-cat-id" name="id">
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category Name *</label>
                <input type="text" name="name" id="edit-cat-name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Lucide Icon Identifier</label>
                <input type="text" name="icon" id="edit-cat-icon" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" id="edit-cat-desc" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>
            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-room-category')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs cursor-pointer">Update Category</button>
            </div>
        </form>
    </div>
</div>

<!-- 2E. ADD AMENITY MODAL (ADM-05) -->
<div id="modal-amenity" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add New Amenity to Library</h3>
            </div>
            <button onclick="closeModal('modal-amenity')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-amenity" onsubmit="handleEntitySubmit(event, '{{ route('admin.amenities.store') }}', 'modal-amenity')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Amenity Name *</label>
                <input type="text" name="name" required placeholder="e.g. Open-Air Rain Shower" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category Group *</label>
                    <select name="category" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="Views & Outdoors">Views & Outdoors</option>
                        <option value="Wellness & Bath">Wellness & Bath</option>
                        <option value="Room Comfort">Room Comfort</option>
                        <option value="Tech & Connectivity">Tech & Connectivity</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Lucide Icon</label>
                    <input type="text" name="icon" value="sparkles" placeholder="bath, wifi, sun, trees..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <input type="text" name="description" placeholder="Brief feature description..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>
            <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                <input type="checkbox" name="is_featured" id="amenity-is-featured" value="1" class="rounded text-brand-primary focus:ring-brand-primary">
                <label for="amenity-is-featured" class="text-xs text-gray-700 font-medium">Show as Quick Filter Pill on client explorer (/stay)</label>
            </div>
            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-amenity')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs cursor-pointer">Save Amenity</button>
            </div>
        </form>
    </div>
</div>

<!-- 2F. EDIT AMENITY MODAL (ADM-05) -->
<div id="modal-edit-amenity" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Edit Amenity</h3>
            </div>
            <button onclick="closeModal('modal-edit-amenity')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-amenity" onsubmit="handleEditAmenitySubmit(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-am-id" name="id">
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Amenity Name *</label>
                <input type="text" name="name" id="edit-am-name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category Group *</label>
                    <select name="category" id="edit-am-category" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="Views & Outdoors">Views & Outdoors</option>
                        <option value="Wellness & Bath">Wellness & Bath</option>
                        <option value="Room Comfort">Room Comfort</option>
                        <option value="Tech & Connectivity">Tech & Connectivity</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Lucide Icon</label>
                    <input type="text" name="icon" id="edit-am-icon" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <input type="text" name="description" id="edit-am-desc" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>
            <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                <input type="checkbox" name="is_featured" id="edit-am-is-featured" value="1" class="rounded text-brand-primary focus:ring-brand-primary">
                <label for="edit-am-is-featured" class="text-xs text-gray-700 font-medium">Show as Quick Filter Pill on client explorer (/stay)</label>
            </div>
            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-amenity')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs cursor-pointer">Update Amenity</button>
            </div>
        </form>
    </div>
</div>


<!-- 3. ADD PHYSICAL ROOM UNIT MODAL (ADM-05) -->
<div id="modal-physical-room" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="door-closed" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add Physical Room Unit</h3>
            </div>
            <button onclick="closeModal('modal-physical-room')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-physical-room" onsubmit="handleEntitySubmit(event, '{{ route('admin.rooms.store') }}', 'modal-physical-room')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch *</label>
                <select name="branch_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Room Category / Type *</label>
                <select name="room_type_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                    @foreach($roomTypes as $rt)
                        <option value="{{ $rt->id }}">{{ $rt->name }} ({{ $rt->branch ? $rt->branch->code : '' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Unit Number *</label>
                    <input type="text" name="room_number" required placeholder="e.g. 104" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono font-bold">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Floor / Wing</label>
                    <input type="text" name="floor" value="Ground Floor" placeholder="e.g. Ground Floor" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Max Guests Capacity Override</label>
                <input type="number" name="max_guests" min="1" max="20" placeholder="Defaults to Room Type capacity" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                <span class="text-[10px] text-gray-400">Optional. If left blank, automatically inherits the capacity rule from the selected Suite/Room Type.</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Operational Status</label>
                    <select name="operational_status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Housekeeping Status</label>
                    <select name="housekeeping_status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="clean">Clean & Inspected</option>
                        <option value="dirty">Dirty / Needs Cleaning</option>
                        <option value="inspecting">Under Inspection</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-physical-room')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save Unit</button>
            </div>
        </form>
    </div>
</div>


<!-- 4A. ADD DINING MENU DISH MODAL (ADM-13) -->
<div id="modal-dining-item" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="utensils" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add New Menu Dish</h3>
            </div>
            <button onclick="closeModal('modal-dining-item')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-dining-item" onsubmit="handleEntitySubmit(event, '{{ route('admin.dining.items.store') }}', 'modal-dining-item')" class="p-4 sm:p-5 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Dish Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Claypot Fish Curry" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category *</label>
                    <select name="menu_category_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        @foreach($menuCategories as $mc)
                            <option value="{{ $mc->id }}">{{ $mc->name }} ({{ $mc->isGlobal() ? 'Global' : ($mc->branch ? $mc->branch->name : 'Branch') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Branch Allocation Options (User requirement) -->
            <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-200 space-y-2.5">
                <label class="block text-[11px] font-bold text-gray-800">Branch Allocation *</label>
                <div class="flex items-center gap-4 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-gray-700">
                        <input type="radio" name="branch_allocation" value="all" checked onchange="toggleBranchAllocationSelector('new', 'all')">
                        <span>🌐 All Branches (Global Menu)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-gray-700">
                        <input type="radio" name="branch_allocation" value="specific" onchange="toggleBranchAllocationSelector('new', 'specific')">
                        <span>📍 Select Specific Branch(es)</span>
                    </label>
                </div>

                <div id="branch-select-container-new" class="hidden pt-2 border-t border-gray-200/80">
                    <span class="text-[10px] text-gray-500 block mb-1.5 font-medium">Select which resort branches offer this dish:</span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($branches as $b)
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-gray-200 text-[11px] font-medium text-gray-700 cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="allocated_branch_ids[]" value="{{ $b->id }}" class="rounded text-brand-primary">
                            <span>{{ $b->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Price (INR) *</label>
                    <input type="number" name="price" required min="10" step="10" placeholder="480" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tax / GST (%)</label>
                    <input type="number" name="tax_rate" value="5.00" min="0" step="0.5" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Prep Time (Mins)</label>
                    <input type="number" name="prep_time_minutes" value="25" min="5" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <!-- Dietary & Tags -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Dietary Classification</label>
                    <select name="is_vegetarian" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="1">Pure Vegetarian (Green Dot)</option>
                        <option value="0">Non-Vegetarian (Brown Dot)</option>
                    </select>
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-gray-700">
                        <input type="checkbox" name="is_featured" value="1">
                        <span>Chef's Special Badge</span>
                    </label>
                </div>
            </div>

            <!-- Daily Picking & Weekly Availability -->
            <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-[11px] font-bold text-gray-800">Weekly Availability Schedule</label>
                    <label class="flex items-center gap-1.5 cursor-pointer text-[11px] font-bold text-emerald-800">
                        <input type="checkbox" name="is_available_today" value="1" checked>
                        <span>Available on Today's Menu</span>
                    </label>
                </div>
                <div class="flex flex-wrap gap-1.5 pt-1">
                    @foreach(['mon'=>'Mon', 'tue'=>'Tue', 'wed'=>'Wed', 'thu'=>'Thu', 'fri'=>'Fri', 'sat'=>'Sat', 'sun'=>'Sun'] as $k => $lbl)
                    <label class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-[10px] font-semibold text-gray-700 cursor-pointer hover:bg-gray-100">
                        <input type="checkbox" name="available_days[]" value="{{ $k }}" checked class="rounded text-brand-primary">
                        <span>{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Simmered with kokum and fresh coconut milk in earthenware." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Dish Photo</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="dish-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="utensils" id="dish-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="image_url" id="dish-image-url" oninput="updateFieldThumbnailPreview('dish-image-url', this.value)" placeholder="Enter URL or upload dish photo..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'dish-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('dish-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-dining-item')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save Dish</button>
            </div>
        </form>
    </div>
</div>


<!-- 4B. EDIT DINING MENU DISH MODAL -->
<div id="modal-dining-item-edit" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Edit Menu Dish</h3>
            </div>
            <button onclick="closeModal('modal-dining-item-edit')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-dining-item-edit" onsubmit="handleEditDishSubmit(event)" class="p-4 sm:p-5 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-dish-id" name="dish_id">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Dish Name *</label>
                    <input type="text" id="edit-dish-name" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category *</label>
                    <select id="edit-dish-category" name="menu_category_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        @foreach($menuCategories as $mc)
                            <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Branch Allocation -->
            <div class="bg-gray-50/80 p-3 rounded-xl border border-gray-200 space-y-2.5">
                <label class="block text-[11px] font-bold text-gray-800">Branch Allocation *</label>
                <div class="flex items-center gap-4 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-gray-700">
                        <input type="radio" name="branch_allocation" id="edit-alloc-all" value="all" onchange="toggleBranchAllocationSelector('edit', 'all')">
                        <span>🌐 All Branches (Global)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-gray-700">
                        <input type="radio" name="branch_allocation" id="edit-alloc-specific" value="specific" onchange="toggleBranchAllocationSelector('edit', 'specific')">
                        <span>📍 Specific Branch(es)</span>
                    </label>
                </div>

                <div id="branch-select-container-edit" class="hidden pt-2 border-t border-gray-200/80">
                    <span class="text-[10px] text-gray-500 block mb-1.5 font-medium">Select branches:</span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="edit-branch-checkboxes">
                        @foreach($branches as $b)
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-gray-200 text-[11px] font-medium text-gray-700 cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="allocated_branch_ids[]" value="{{ $b->id }}" id="edit-branch-chk-{{ $b->id }}" class="rounded text-brand-primary">
                            <span>{{ $b->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Price (INR) *</label>
                    <input type="number" id="edit-dish-price" name="price" required min="10" step="10" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tax / GST (%)</label>
                    <input type="number" id="edit-dish-tax" name="tax_rate" value="5.00" min="0" step="0.5" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Prep Time (Mins)</label>
                    <input type="number" id="edit-dish-prep" name="prep_time_minutes" min="5" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Dietary Classification</label>
                    <select id="edit-dish-veg" name="is_vegetarian" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="1">Pure Vegetarian (Green Dot)</option>
                        <option value="0">Non-Vegetarian (Brown Dot)</option>
                    </select>
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-gray-700">
                        <input type="checkbox" id="edit-dish-featured" name="is_featured" value="1">
                        <span>Chef's Special Badge</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <textarea id="edit-dish-desc" name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Dish Photo</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="edit-dish-image-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="utensils" id="edit-dish-image-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" id="edit-dish-image" name="image_url" oninput="updateFieldThumbnailPreview('edit-dish-image', this.value)" placeholder="Enter URL or upload dish photo..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'edit-dish-image')">
                    </label>
                    <button type="button" onclick="openImagePicker('edit-dish-image')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-dining-item-edit')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Update Dish</button>
            </div>
        </form>
    </div>
</div>


<!-- 4C. ADD / EDIT MENU CATEGORY MODAL -->
<div id="modal-dining-category" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="folder-plus" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm" id="category-modal-title">Add Menu Category</h3>
            </div>
            <button onclick="closeModal('modal-dining-category')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-dining-category" onsubmit="handleCategoryFormSubmit(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="cat-edit-id" name="cat_id" value="">
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category Name *</label>
                <input type="text" id="cat-input-name" name="name" required placeholder="e.g. Kerala Authentic Foods, Breakfast, Chef's Grill" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Scope *</label>
                <select id="cat-input-branch" name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                    <option value="">🌐 All Branches (Global Category)</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">📍 {{ $b->name }}</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-gray-500 mt-1 block">Global categories are visible across every resort branch.</span>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Icon (Lucide Name)</label>
                <input type="text" id="cat-input-icon" name="icon" placeholder="utensils, flame, coffee, fish, leaf" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <textarea id="cat-input-desc" name="description" rows="2" placeholder="Brief note about recipes in this collection..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-dining-category')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs" id="cat-submit-btn">Save Category</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleBranchAllocationSelector(mode, type) {
    const container = document.getElementById('branch-select-container-' + mode);
    if (container) {
        if (type === 'specific') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }
}

function openEditDishModal(item) {
    document.getElementById('edit-dish-id').value = item.id;
    document.getElementById('edit-dish-name').value = item.name;
    document.getElementById('edit-dish-category').value = item.menu_category_id;
    document.getElementById('edit-dish-price').value = item.price;
    document.getElementById('edit-dish-tax').value = item.tax_rate ?? 5;
    document.getElementById('edit-dish-prep').value = item.prep_time_minutes ?? 25;
    document.getElementById('edit-dish-veg').value = item.is_vegetarian ? '1' : '0';
    document.getElementById('edit-dish-featured').checked = !!item.is_featured;
    document.getElementById('edit-dish-desc').value = item.description ?? item.short_description ?? '';
    document.getElementById('edit-dish-image').value = item.image_url ?? '';
    updateFieldThumbnailPreview('edit-dish-image', item.image_url ?? '');

    // Branch allocation
    if (item.is_all_branches || !item.branch_id) {
        document.getElementById('edit-alloc-all').checked = true;
        toggleBranchAllocationSelector('edit', 'all');
    } else {
        document.getElementById('edit-alloc-specific').checked = true;
        toggleBranchAllocationSelector('edit', 'specific');
        const alloc = item.allocated_branch_ids || (item.branch_id ? [item.branch_id] : []);
        document.querySelectorAll('#edit-branch-checkboxes input[type="checkbox"]').forEach(chk => {
            chk.checked = alloc.includes(parseInt(chk.value));
        });
    }

    openModal('modal-dining-item-edit');
}

async function handleEditDishSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const itemId = document.getElementById('edit-dish-id').value;
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    try {
        const res = await fetch(`/admin/dining/items/${itemId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });
        const data = await res.json();
        if (data.success) {
            closeModal('modal-dining-item-edit');
            if (typeof showToast === 'function') showToast(data.message, 'success');
            setTimeout(() => location.reload(), 300);
        } else {
            alert(data.message || 'Error updating dish');
        }
    } catch(err) {
        console.error(err);
        alert('Server error');
    }
}

function openEditCategoryModal(cat) {
    document.getElementById('cat-edit-id').value = cat.id;
    document.getElementById('cat-input-name').value = cat.name;
    document.getElementById('cat-input-branch').value = cat.branch_id || '';
    document.getElementById('cat-input-icon').value = cat.icon || '';
    document.getElementById('cat-input-desc').value = cat.description || '';
    document.getElementById('category-modal-title').innerText = 'Edit Menu Category';
    document.getElementById('cat-submit-btn').innerText = 'Update Category';
    openModal('modal-dining-category');
}

async function handleCategoryFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const catId = document.getElementById('cat-edit-id').value;
    const url = catId ? `/admin/dining/categories/${catId}` : '{{ route('admin.dining.categories.store') }}';
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });
        const data = await res.json();
        if (data.success) {
            closeModal('modal-dining-category');
            if (typeof showToast === 'function') showToast(data.message, 'success');
            setTimeout(() => location.reload(), 300);
        } else {
            alert(data.message || 'Error saving category');
        }
    } catch(err) {
        console.error(err);
        alert('Server error');
    }
}
</script>


<!-- 5. ADD SPICE PRODUCT MODAL (ADM-16) -->
<div id="modal-spice-product" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="leaf" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add New Spice Product</h3>
            </div>
            <button onclick="closeModal('modal-spice-product')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-spice-product" onsubmit="handleEntitySubmit(event, '{{ route('admin.spices.products.store') }}', 'modal-spice-product')" class="p-4 sm:p-5 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <!-- Basic Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Product Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Malabar Black Peppercorns" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category *</label>
                    <select name="spice_category_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        @foreach($spiceCategories as $sc)
                            <option value="{{ $sc->id }}">{{ $sc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Selling Mode Selector -->
            <div class="p-3 bg-brand-surface rounded-xl border border-gray-200/80 space-y-2">
                <label class="block text-[11px] font-bold text-brand-text">Packaging & Selling Mode *</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer">
                        <input type="radio" name="selling_mode" value="both" checked onchange="toggleSpiceFormModes(this.value, 'add')" class="text-brand-primary">
                        <span class="text-[11px] font-semibold text-brand-text">Packets & Loose</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer">
                        <input type="radio" name="selling_mode" value="packet" onchange="toggleSpiceFormModes(this.value, 'add')" class="text-brand-primary">
                        <span class="text-[11px] font-semibold text-brand-text">Packets Only</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer">
                        <input type="radio" name="selling_mode" value="loose" onchange="toggleSpiceFormModes(this.value, 'add')" class="text-brand-primary">
                        <span class="text-[11px] font-semibold text-brand-text">Loose KG Only</span>
                    </label>
                </div>
            </div>

            <!-- Dynamic Pricing Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Packet Details Section -->
                <div id="spice-add-packet-fields" class="p-3 bg-blue-50/50 rounded-xl border border-blue-200/60 space-y-2.5">
                    <div class="font-bold text-blue-950 text-[11px] flex items-center gap-1">
                        <i data-lucide="package" class="w-3.5 h-3.5 text-blue-700"></i> Pre-Packaged Unit Details
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Packet Size / Label</label>
                        <input type="text" name="package_size" value="100g Jar" placeholder="e.g. 250g Pouch, 1kg Bag" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Unit Weight (Grams)</label>
                            <input type="number" name="weight_grams" value="100" min="10" step="10" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Price / Packet (₹)</label>
                            <input type="number" name="price" value="280" min="0" step="5" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono font-bold">
                        </div>
                    </div>
                </div>

                <!-- Loose KG Details Section -->
                <div id="spice-add-loose-fields" class="p-3 bg-amber-50/50 rounded-xl border border-amber-200/60 space-y-2.5">
                    <div class="font-bold text-amber-950 text-[11px] flex items-center gap-1">
                        <i data-lucide="scale" class="w-3.5 h-3.5 text-amber-700"></i> Loose Weight (KG) Details
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Price Per KG (₹) *</label>
                        <input type="number" name="price_per_kg" value="2800" min="0" step="10" placeholder="e.g. 2800" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Minimum Order Weight (KG)</label>
                        <input type="number" name="min_loose_weight_kg" value="0.100" min="0.01" step="0.05" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono">
                    </div>
                </div>
            </div>

            <!-- Tax Rate & SKU -->
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">SKU Code *</label>
                    <input type="text" name="sku" required placeholder="PEP-ORG-100" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">GST Tax Rate (%) *</label>
                    <input type="number" name="tax_rate" required value="5.00" min="0" max="28" step="0.5" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Compare Price (₹)</label>
                    <input type="number" name="compare_at_price" placeholder="320" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs font-mono">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Product Description</label>
                <textarea name="short_description" rows="2" placeholder="Naturally sun-dried berries with high piperine content." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Product Main Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="spice-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="leaf" id="spice-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="image_url" id="spice-image-url" oninput="updateFieldThumbnailPreview('spice-image-url', this.value)" placeholder="Enter URL or upload product image..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'spice-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('spice-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[11px] font-semibold text-gray-700">Product Gallery Photos (Multiple)</label>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-bold text-brand-primary bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded cursor-pointer flex items-center gap-1 transition">
                            <i data-lucide="upload" class="w-3 h-3"></i> Upload Files
                            <input type="file" multiple accept="image/*" class="hidden" onchange="uploadMultipleFilesForGallery(this, 'spice-gallery-chips-new', 'gallery[]')">
                        </label>
                    </div>
                </div>
                <div class="flex gap-1.5 mb-1.5">
                    <input type="text" id="spice-gallery-url-input-new" placeholder="Or paste image URL..." class="flex-1 px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs">
                    <button type="button" onclick="addManualUrlToGallery('spice-gallery-url-input-new', 'spice-gallery-chips-new', 'gallery[]')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold">Add URL</button>
                </div>
                <div id="spice-gallery-chips-new" class="flex flex-wrap gap-2 p-2 bg-gray-50 border border-gray-200 rounded-xl min-h-[52px]">
                    <span class="text-[10px] text-gray-400 self-center px-1">Attached gallery photos will appear here</span>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-1 flex-wrap">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" checked class="rounded text-brand-primary focus:ring-brand-primary">
                    <span class="text-xs font-bold text-emerald-800">Immediately Available on Store</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_returnable" value="1" checked class="rounded text-brand-primary focus:ring-brand-primary">
                    <span class="text-xs font-bold text-blue-800">Return Policy Available</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-brand-primary focus:ring-brand-primary">
                    <span class="text-xs font-semibold text-gray-700">Featured Highlight</span>
                </label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-spice-product')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- 5B. EDIT SPICE PRODUCT MODAL -->
<div id="modal-spice-product-edit" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm" id="spice-edit-title">Edit Spice Product</h3>
            </div>
            <button onclick="closeModal('modal-spice-product-edit')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-spice-product-edit" onsubmit="handleSpiceEditSubmit(event)" class="p-4 sm:p-5 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="spice-edit-id">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Product Name *</label>
                    <input type="text" id="spice-edit-name" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category *</label>
                    <select id="spice-edit-category" name="spice_category_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                        @foreach($spiceCategories as $sc)
                            <option value="{{ $sc->id }}">{{ $sc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Selling Mode Selector -->
            <div class="p-3 bg-brand-surface rounded-xl border border-gray-200/80 space-y-2">
                <label class="block text-[11px] font-bold text-brand-text">Packaging & Selling Mode *</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer">
                        <input type="radio" name="selling_mode" id="spice-edit-mode-both" value="both" onchange="toggleSpiceFormModes(this.value, 'edit')" class="text-brand-primary">
                        <span class="text-[11px] font-semibold text-brand-text">Packets & Loose</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer">
                        <input type="radio" name="selling_mode" id="spice-edit-mode-packet" value="packet" onchange="toggleSpiceFormModes(this.value, 'edit')" class="text-brand-primary">
                        <span class="text-[11px] font-semibold text-brand-text">Packets Only</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer">
                        <input type="radio" name="selling_mode" id="spice-edit-mode-loose" value="loose" onchange="toggleSpiceFormModes(this.value, 'edit')" class="text-brand-primary">
                        <span class="text-[11px] font-semibold text-brand-text">Loose KG Only</span>
                    </label>
                </div>
            </div>

            <!-- Dynamic Pricing Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Packet Details -->
                <div id="spice-edit-packet-fields" class="p-3 bg-blue-50/50 rounded-xl border border-blue-200/60 space-y-2.5">
                    <div class="font-bold text-blue-950 text-[11px] flex items-center gap-1">
                        <i data-lucide="package" class="w-3.5 h-3.5 text-blue-700"></i> Pre-Packaged Unit Details
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Packet Size / Label</label>
                        <input type="text" id="spice-edit-package-size" name="package_size" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Unit Weight (Grams)</label>
                            <input type="number" id="spice-edit-weight-grams" name="weight_grams" min="10" step="10" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Price / Packet (₹)</label>
                            <input type="number" id="spice-edit-price" name="price" min="0" step="5" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono font-bold">
                        </div>
                    </div>
                </div>

                <!-- Loose KG Details -->
                <div id="spice-edit-loose-fields" class="p-3 bg-amber-50/50 rounded-xl border border-amber-200/60 space-y-2.5">
                    <div class="font-bold text-amber-950 text-[11px] flex items-center gap-1">
                        <i data-lucide="scale" class="w-3.5 h-3.5 text-amber-700"></i> Loose Weight (KG) Details
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Price Per KG (₹) *</label>
                        <input type="number" id="spice-edit-price-kg" name="price_per_kg" min="0" step="10" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-700 mb-0.5">Minimum Order Weight (KG)</label>
                        <input type="number" id="spice-edit-min-loose" name="min_loose_weight_kg" min="0.01" step="0.05" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">SKU Code *</label>
                    <input type="text" id="spice-edit-sku" name="sku" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">GST Tax Rate (%) *</label>
                    <input type="number" id="spice-edit-tax-rate" name="tax_rate" required min="0" max="28" step="0.5" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Compare Price (₹)</label>
                    <input type="number" id="spice-edit-compare-price" name="compare_at_price" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs font-mono">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Product Description</label>
                <textarea id="spice-edit-description" name="short_description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Product Main Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="spice-edit-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="leaf" id="spice-edit-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" id="spice-edit-image-url" name="image_url" oninput="updateFieldThumbnailPreview('spice-edit-image-url', this.value)" placeholder="Enter URL or upload product image..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'spice-edit-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('spice-edit-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[11px] font-semibold text-gray-700">Product Gallery Photos (Multiple)</label>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[10px] font-bold text-brand-primary bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded cursor-pointer flex items-center gap-1 transition">
                            <i data-lucide="upload" class="w-3 h-3"></i> Upload Files
                            <input type="file" multiple accept="image/*" class="hidden" onchange="uploadMultipleFilesForGallery(this, 'spice-gallery-chips-edit', 'gallery[]')">
                        </label>
                    </div>
                </div>
                <div class="flex gap-1.5 mb-1.5">
                    <input type="text" id="spice-gallery-url-input-edit" placeholder="Or paste image URL..." class="flex-1 px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs">
                    <button type="button" onclick="addManualUrlToGallery('spice-gallery-url-input-edit', 'spice-gallery-chips-edit', 'gallery[]')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold">Add URL</button>
                </div>
                <div id="spice-gallery-chips-edit" class="flex flex-wrap gap-2 p-2 bg-gray-50 border border-gray-200 rounded-xl min-h-[52px]">
                    <span class="text-[10px] text-gray-400 self-center px-1">Attached gallery photos will appear here</span>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-1 flex-wrap">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="spice-edit-is-available" name="is_available" value="1" class="rounded text-brand-primary">
                    <span class="text-xs font-bold text-emerald-800">Available on Client Store</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="spice-edit-is-returnable" name="is_returnable" value="1" class="rounded text-brand-primary">
                    <span class="text-xs font-bold text-blue-800">Return Policy Available</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="spice-edit-is-featured" name="is_featured" value="1" class="rounded text-brand-primary">
                    <span class="text-xs font-semibold text-gray-700">Featured Highlight</span>
                </label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-spice-product-edit')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Update Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleSpiceFormModes(mode, prefix) {
        const packetBox = document.getElementById(`spice-${prefix}-packet-fields`);
        const looseBox = document.getElementById(`spice-${prefix}-loose-fields`);
        if (!packetBox || !looseBox) return;

        if (mode === 'both') {
            packetBox.classList.remove('hidden', 'opacity-40');
            looseBox.classList.remove('hidden', 'opacity-40');
        } else if (mode === 'packet') {
            packetBox.classList.remove('hidden', 'opacity-40');
            looseBox.classList.add('hidden');
        } else if (mode === 'loose') {
            packetBox.classList.add('hidden');
            looseBox.classList.remove('hidden', 'opacity-40');
        }
    }

    function openSpiceEditModal(sp) {
        document.getElementById('spice-edit-id').value = sp.id;
        document.getElementById('spice-edit-title').textContent = `Edit Spice: ${sp.name}`;
        document.getElementById('spice-edit-name').value = sp.name || '';
        document.getElementById('spice-edit-category').value = sp.spice_category_id || '';
        document.getElementById('spice-edit-sku').value = sp.sku || '';
        document.getElementById('spice-edit-package-size').value = sp.package_size || '';
        document.getElementById('spice-edit-weight-grams').value = sp.weight_grams || 100;
        document.getElementById('spice-edit-price').value = sp.price || '';
        document.getElementById('spice-edit-price-kg').value = sp.price_per_kg || '';
        document.getElementById('spice-edit-min-loose').value = sp.min_loose_weight_kg || '0.100';
        document.getElementById('spice-edit-tax-rate').value = sp.tax_rate || '5.00';
        document.getElementById('spice-edit-compare-price').value = sp.compare_at_price || '';
        document.getElementById('spice-edit-description').value = sp.short_description || sp.description || '';
        document.getElementById('spice-edit-image-url').value = sp.image_url || '';
        updateFieldThumbnailPreview('spice-edit-image-url', sp.image_url || '');

        const chipsContainer = document.getElementById('spice-gallery-chips-edit');
        if (chipsContainer) {
            chipsContainer.innerHTML = '';
            const images = Array.isArray(sp.gallery) ? sp.gallery : [];
            if (images.length > 0) {
                images.forEach(imgUrl => addGalleryChipToContainer(chipsContainer, 'gallery[]', imgUrl));
            } else {
                chipsContainer.innerHTML = '<span class="text-[10px] text-gray-400 self-center px-1">No additional gallery photos</span>';
            }
        }
        document.getElementById('spice-edit-is-available').checked = !!sp.is_available;
        document.getElementById('spice-edit-is-returnable').checked = (sp.is_returnable !== undefined && sp.is_returnable !== null) ? !!sp.is_returnable : true;
        document.getElementById('spice-edit-is-featured').checked = !!sp.is_featured;

        const mode = sp.selling_mode || 'both';
        const radio = document.getElementById(`spice-edit-mode-${mode}`);
        if (radio) radio.checked = true;
        toggleSpiceFormModes(mode, 'edit');

        openModal('modal-spice-product-edit');
        if (window.lucide) lucide.createIcons();
    }

    async function handleSpiceEditSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('spice-edit-id').value;
        const form = document.getElementById('form-spice-product-edit');
        const formData = new FormData(form);

        try {
            const res = await fetch(`/admin/spices/products/${id}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                closeModal('modal-spice-product-edit');
                showToast(data.message);
                setTimeout(() => window.location.reload(), 600);
            } else {
                alert(data.message || 'Error updating spice product');
            }
        } catch (err) {
            console.error('Update error:', err);
            alert('Failed to connect to server.');
        }
    }
</script>



<!-- 6. ADD FACILITY MODAL (ADM-10) -->
<div id="modal-facility" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add Resort Facility / Experience</h3>
            </div>
            <button onclick="closeModal('modal-facility')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-facility" onsubmit="handleEntitySubmit(event, '{{ route('admin.facilities.store') }}', 'modal-facility')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Scope</label>
                    <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-semibold">
                        <option value="">All Branches (Resort Wide)</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category / Tag (Optional)</label>
                    <input type="text" name="category" placeholder="e.g. Wellness, Nature, Plantation..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Facility / Experience Name *</label>
                <input type="text" name="name" required placeholder="e.g. Herbal Steam Pavilion, Ayurvedic Abhyanga, Plantation Safari..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-bold">
            </div>

            <!-- CUSTOMIZABLE TOGGLES: BOOKABLE & SCHEDULING -->
            <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-bold text-gray-800 text-xs block">Bookable by In-House Guests</span>
                        <span class="text-[10px] text-gray-500">Allows staying guests to book this experience from their dashboard</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_bookable" value="1" onchange="document.getElementById('fac-rate-container').classList.toggle('hidden', !this.checked)" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-primary"></div>
                    </label>
                </div>

                <!-- Rate Container (revealed when bookable is checked) -->
                <div id="fac-rate-container" class="hidden pt-2 border-t border-gray-200">
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Experience Rate per Guest (₹)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="rate" step="0.01" min="0" value="0.00" placeholder="0.00" class="w-36 px-3 py-1.5 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-semibold">
                        <span class="text-[11px] text-gray-500 font-medium">Leave 0.00 for complimentary in-house experiences</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                    <div>
                        <span class="font-bold text-gray-800 text-xs block">Enable Slot / Time-Period Scheduling</span>
                        <span class="text-[10px] text-gray-500">Manager can allocate specific time periods (e.g. 09:00 AM - 10:30 AM)</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="has_scheduling" value="1" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Operating Hours / General Timings</label>
                <input type="text" name="operating_hours" value="Open Daily &middot; 7:00 AM - 7:00 PM" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Describe the therapeutic benefits, setting, or experience highlights." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Facility Photo</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="facility-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="sparkles" id="facility-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="image_url" id="facility-image-url" oninput="updateFieldThumbnailPreview('facility-image-url', this.value)" placeholder="Enter URL or upload facility photo..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'facility-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('facility-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-facility')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save Facility</button>
            </div>
        </form>
    </div>
</div>

<!-- ALLOCATE FACILITY TIME SLOT MODAL (MANAGER ALLOCATION) -->
<div id="modal-allocate-slot" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Allocate Time Slot &amp; Confirm Booking</h3>
            </div>
            <button onclick="closeModal('modal-allocate-slot')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-allocate-slot" onsubmit="submitSlotAllocation(event)" class="p-4 sm:p-5 space-y-4 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="alloc-booking-id" name="booking_id">

            <!-- Summary Card -->
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-brand-muted text-[11px] font-semibold">Guest</span>
                    <span class="font-bold text-brand-text" id="alloc-guest-name">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-brand-muted text-[11px] font-semibold">Experience</span>
                    <span class="font-bold text-brand-text" id="alloc-facility-name">-</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-gray-200">
                    <span class="text-brand-muted text-[11px] font-semibold">Total Amount</span>
                    <span class="font-bold text-emerald-800 text-xs" id="alloc-total-amount">Complimentary</span>
                </div>
            </div>

            <!-- Quick Slot Preset Chips -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1.5">Quick Time Slot Presets</label>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="setSlotPreset('06:30 AM - 08:00 AM')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-brand-primary hover:text-white text-brand-text font-semibold text-[11px] transition">06:30 AM - 08:00 AM</button>
                    <button type="button" onclick="setSlotPreset('08:30 AM - 10:00 AM')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-brand-primary hover:text-white text-brand-text font-semibold text-[11px] transition">08:30 AM - 10:00 AM</button>
                    <button type="button" onclick="setSlotPreset('10:30 AM - 12:00 PM')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-brand-primary hover:text-white text-brand-text font-semibold text-[11px] transition">10:30 AM - 12:00 PM</button>
                    <button type="button" onclick="setSlotPreset('03:30 PM - 05:00 PM')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-brand-primary hover:text-white text-brand-text font-semibold text-[11px] transition">03:30 PM - 05:00 PM</button>
                    <button type="button" onclick="setSlotPreset('05:30 PM - 07:00 PM')" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-brand-primary hover:text-white text-brand-text font-semibold text-[11px] transition">05:30 PM - 07:00 PM</button>
                </div>
            </div>

            <!-- Custom Allocated Time Slot Field -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Allocated Time Period *</label>
                <input type="text" id="alloc-time-slot" name="allocated_time_slot" required placeholder="e.g. 09:00 AM - 10:30 AM" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                <p class="text-[10px] text-brand-muted mt-1">This timing will instantly reflect on the guest's customer dashboard.</p>
            </div>

            <!-- Booking Status -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Booking Status *</label>
                <select id="alloc-status" name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-semibold">
                    <option value="confirmed">Confirmed</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Folio Charge Checkbox (if paid experience) -->
            <div id="alloc-folio-charge-container" class="hidden p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" id="alloc-charge-folio" name="charge_room_folio" value="1" class="mt-0.5 rounded text-brand-primary focus:ring-brand-primary">
                    <div>
                        <span class="font-bold text-emerald-900 text-xs block">Post Charge to Guest Room Folio</span>
                        <span class="text-[11px] text-emerald-700 block">Adds this activity charge to the guest's in-house room ledger for checkout settlement.</span>
                    </div>
                </label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-allocate-slot')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="btn-save-allocation" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save &amp; Confirm Allocation</button>
            </div>
        </form>
    </div>
</div>


<!-- 7. ADD NEARBY ATTRACTION MODAL (ADM-12) -->
<div id="modal-nearby" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="compass" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add Nearby Point of Interest</h3>
            </div>
            <button onclick="closeModal('modal-nearby')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-nearby" onsubmit="handleEntitySubmit(event, '{{ route('admin.nearby.store') }}', 'modal-nearby')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Region *</label>
                    <select name="branch_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category *</label>
                    <select name="category" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="waterfall">Waterfall</option>
                        <option value="viewpoint">Viewpoint & Peak</option>
                        <option value="heritage">Heritage Site</option>
                        <option value="wildlife">Wildlife Reserve</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Attraction Name *</label>
                <input type="text" name="name" required placeholder="e.g. Attukad Waterfalls" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Distance from Resort (km) *</label>
                    <input type="number" name="distance_km" required step="0.5" value="4.5" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Travel Time</label>
                    <input type="text" name="travel_time" value="15 mins drive" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Cascading waterfalls nestled between steep green tea hills." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Attraction Photo</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="nearby-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="compass" id="nearby-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="image_url" id="nearby-image-url" oninput="updateFieldThumbnailPreview('nearby-image-url', this.value)" placeholder="Enter URL or upload attraction photo..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'nearby-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('nearby-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <!-- Availability & Taxi Toggles -->
            <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" checked class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary">
                    <span class="text-[11px] font-semibold text-gray-700">Open for Guest Visits</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_taxi_available" value="1" checked class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary">
                    <span class="text-[11px] font-semibold text-gray-700">Resort Cab Available</span>
                </label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-nearby')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save Location</button>
            </div>
        </form>
    </div>
</div>

<!-- 7B. EDIT NEARBY ATTRACTION MODAL -->
<div id="modal-edit-nearby" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Edit Discovery & Cab Options</h3>
            </div>
            <button onclick="closeModal('modal-edit-nearby')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-nearby" onsubmit="handleEditNearbySubmit(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-nearby-id" name="id">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Region *</label>
                    <select name="branch_id" id="edit-nearby-branch-id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category *</label>
                    <select name="category" id="edit-nearby-category" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="waterfall">Waterfall</option>
                        <option value="viewpoint">Viewpoint & Peak</option>
                        <option value="heritage">Heritage Site</option>
                        <option value="wildlife">Wildlife Reserve</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Attraction Name *</label>
                <input type="text" name="name" id="edit-nearby-name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Distance from Resort (km) *</label>
                    <input type="number" name="distance_km" id="edit-nearby-distance" required step="0.5" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Travel Time</label>
                    <input type="text" name="travel_time" id="edit-nearby-travel-time" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" id="edit-nearby-description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Attraction Photo</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="edit-nearby-image-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="compass" id="edit-nearby-image-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="image_url" id="edit-nearby-image-url" oninput="updateFieldThumbnailPreview('edit-nearby-image-url', this.value)" placeholder="Enter URL or upload attraction photo..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'edit-nearby-image-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('edit-nearby-image-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_available" id="edit-nearby-is-available" value="1" class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary">
                    <span class="text-[11px] font-semibold text-gray-700">Open for Visits</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_taxi_available" id="edit-nearby-is-taxi" value="1" class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary">
                    <span class="text-[11px] font-semibold text-gray-700">Resort Cab Available</span>
                </label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-nearby')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Update Location</button>
            </div>
        </form>
    </div>
</div>

<!-- 7C. QUOTE TAXI FARE & DISPATCH MODAL -->
<div id="modal-quote-taxi" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="car" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Quote Cab Fare & Dispatch</h3>
            </div>
            <button onclick="closeModal('modal-quote-taxi')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-quote-taxi" onsubmit="handleQuoteTaxiSubmit(event)" class="p-5 space-y-4 text-xs">
            @csrf
            <input type="hidden" id="quote-taxi-id" name="id">

            <div class="bg-brand-canvas p-3 rounded-xl border border-gray-200/80 space-y-1">
                <div class="flex justify-between font-mono text-[11px] text-brand-primary font-bold">
                    <span id="quote-taxi-ref">TAX-XXXXX</span>
                    <span id="quote-taxi-guest">Guest Name</span>
                </div>
                <div class="text-[11px] text-brand-muted" id="quote-taxi-details">Stops & Route</div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Agreed Fare (₹) *</label>
                    <input type="number" step="1" min="0" required name="estimated_fare" id="quote-taxi-fare" placeholder="e.g. 1800" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono font-bold">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Confirmed Pickup Time</label>
                    <input type="text" name="pickup_time" id="quote-taxi-time" placeholder="e.g. 09:00 AM" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Assigned Driver & Vehicle</label>
                <input type="text" name="driver_details" id="quote-taxi-driver" placeholder="e.g. Driver Ramesh (+91 94471 22334) - Innova Crysta" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Status Progression</label>
                <select name="status" id="quote-taxi-status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-semibold">
                    <option value="contacted">Contacted & Quoted</option>
                    <option value="confirmed">Confirmed & Dispatched</option>
                    <option value="completed">Completed Trip</option>
                </select>
            </div>

            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200/60">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="post_to_folio" value="1" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                    <span class="text-[11px] font-bold text-emerald-900">Post ₹ Amount to Guest's Room Folio</span>
                </label>
                <p class="text-[10px] text-emerald-700 mt-1 pl-6">Posts charge under transport category so it can be settled upon counter checkout.</p>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-quote-taxi')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save & Confirm</button>
            </div>
        </form>
    </div>
</div>


<!-- 8. ADD GALLERY ALBUM MODAL (ADM-11) -->
<div id="modal-gallery-album" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="image" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Create New Photo Album</h3>
            </div>
            <button onclick="closeModal('modal-gallery-album')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-gallery-album" onsubmit="handleEntitySubmit(event, '{{ route('admin.gallery.albums.store') }}', 'modal-gallery-album')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Album Title (Optional)</label>
                    <input type="text" name="name" placeholder="e.g. Monsoon Mist Moments" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Resort Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                        <option value="">Resort-Wide / All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category *</label>
                    <select name="category" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                        <option value="nature">Plantation & Nature</option>
                        <option value="architecture">Architecture & Heritage</option>
                        <option value="rooms">Villas & Suites</option>
                        <option value="dining">Dining & Culinary</option>
                        <option value="experiences">Experiences & Trails</option>
                        <option value="events">Events & Gatherings</option>
                        <option value="spices">Estate Spices</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Featured on Homepage</label>
                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" class="rounded text-brand-primary">
                            <span class="text-xs text-gray-700 font-medium">Highlight as Featured</span>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Story / Description (Optional)</label>
                <textarea name="description" rows="2" placeholder="Brief visual journal narrative or atmosphere description..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cover Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="album-cover-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="image" id="album-cover-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="cover_image_url" id="album-cover-url" oninput="updateFieldThumbnailPreview('album-cover-url', this.value)" placeholder="Enter URL or upload cover..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'album-cover-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('album-cover-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-[11px] font-semibold text-gray-700">Initial Album Photos (Multiple)</label>
                    <label class="text-[10px] font-bold text-brand-primary bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded cursor-pointer flex items-center gap-1 transition">
                        <i data-lucide="upload" class="w-3 h-3"></i> Upload Files
                        <input type="file" multiple accept="image/*" class="hidden" onchange="uploadMultipleFilesForGallery(this, 'album-new-photos-chips', 'images[]')">
                    </label>
                </div>
                <div class="flex gap-1.5 mb-1.5">
                    <input type="text" id="album-new-photo-url" placeholder="Or paste photo URL..." class="flex-1 px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs">
                    <button type="button" onclick="addManualUrlToGallery('album-new-photo-url', 'album-new-photos-chips', 'images[]')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold">Add URL</button>
                </div>
                <div id="album-new-photos-chips" class="flex flex-wrap gap-2 p-2 bg-gray-50 border border-gray-200 rounded-xl min-h-[52px]">
                    <span class="text-[10px] text-gray-400 self-center px-1">Attached album photos will appear here</span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-gallery-album')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Create Album</button>
            </div>
        </form>
    </div>
</div>

<!-- 8B. EDIT GALLERY ALBUM MODAL -->
<div id="modal-edit-gallery-album" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Edit Gallery Album Details</h3>
            </div>
            <button onclick="closeModal('modal-edit-gallery-album')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-gallery-album" onsubmit="handleEntitySubmit(event, '', 'modal-edit-gallery-album')" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-album-id" name="id">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Album Title (Optional)</label>
                    <input type="text" id="edit-album-title" name="name" placeholder="e.g. Monsoon Valleys" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Resort Branch</label>
                    <select id="edit-album-branch" name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                        <option value="">Resort-Wide / All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Category *</label>
                <select id="edit-album-category" name="category" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <option value="nature">Plantation & Nature</option>
                    <option value="architecture">Architecture & Heritage</option>
                    <option value="rooms">Villas & Suites</option>
                    <option value="dining">Dining & Culinary</option>
                    <option value="experiences">Experiences & Trails</option>
                    <option value="events">Events & Gatherings</option>
                    <option value="spices">Estate Spices</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Story / Description (Optional)</label>
                <textarea id="edit-album-desc" name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs"></textarea>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cover Image</label>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0 overflow-hidden relative">
                        <img id="edit-album-cover-url-preview" src="" class="w-full h-full object-cover hidden">
                        <i data-lucide="image" id="edit-album-cover-url-placeholder" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input type="text" name="cover_image_url" id="edit-album-cover-url" oninput="updateFieldThumbnailPreview('edit-album-cover-url', this.value)" placeholder="Enter URL or choose file..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <label class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-brand-text rounded-lg cursor-pointer text-xs font-semibold flex items-center gap-1 shrink-0" title="Upload from Device">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Upload</span>
                        <input type="file" accept="image/*" class="hidden" onchange="uploadFileDirectlyToInput(this, 'edit-album-cover-url')">
                    </label>
                    <button type="button" onclick="openImagePicker('edit-album-cover-url')" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-brand-primary border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-1 shrink-0" title="Select or Pick Presets">
                        <i data-lucide="library" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Library</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-gallery-album')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- 8C. MANAGE ALBUM PHOTOS MODAL -->
<div id="modal-manage-album-photos" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <!-- Header -->
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="images" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm" id="manage-album-photos-title">Manage Album Photos</h3>
                <span id="manage-album-photos-count" class="ml-2 text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">0 Photos</span>
            </div>
            <button onclick="closeModal('modal-manage-album-photos')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 text-xs">
            <!-- Upload Toolbar -->
            <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-brand-text text-[11px] flex items-center gap-1.5">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-brand-primary"></i> Add Photos to This Album
                    </span>
                    <label class="px-3 py-1.5 bg-brand-primary hover:bg-brand-deep text-white rounded-lg cursor-pointer text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span>Upload Files from Device</span>
                        <input type="file" multiple accept="image/*" class="hidden" onchange="uploadPhotosToCurrentAlbum(this)">
                    </label>
                </div>
                <div class="flex gap-2">
                    <input type="text" id="album-photo-new-url" placeholder="Or paste public image web address (URL)..." class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-xs">
                    <button type="button" onclick="addUrlPhotoToCurrentAlbum()" class="px-3.5 py-2 bg-gray-200 hover:bg-gray-300 text-brand-text font-bold rounded-lg transition">
                        Add Photo
                    </button>
                </div>
            </div>

            <!-- Existing Photos Responsive Grid -->
            <div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-2">Album Photos Collection</span>
                <div id="album-photos-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <!-- Dynamic photo items rendered via JS -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-3.5 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <span class="text-[11px] text-gray-500">Changes to photos are saved instantly.</span>
            <button type="button" onclick="closeModal('modal-manage-album-photos')" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold rounded-lg transition text-xs">
                Done
            </button>
        </div>
    </div>
</div>


<!-- 9. EDIT SYSTEM SETTINGS MODAL (ADM-28) -->
<div id="modal-settings" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="settings" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Resort System Settings</h3>
            </div>
            <button onclick="closeModal('modal-settings')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-settings" onsubmit="handleEntitySubmit(event, '{{ route('admin.settings.update') }}', 'modal-settings')" enctype="multipart/form-data" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf

            <!-- LOGIN & REGISTER PAGE BACKGROUND IMAGE SETTING -->
            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200/80 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="image" class="w-4 h-4 text-brand-primary"></i>
                        <span class="font-bold text-xs text-brand-text">Login & Register Hero Image</span>
                    </div>
                    <span class="text-[10px] text-brand-muted uppercase font-bold tracking-wider">Split View Cover</span>
                </div>

                @php
                    $currentLoginBg = \App\Models\Setting::get('login_page_image', 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&w=1600&q=80');
                @endphp

                <!-- Image Preview -->
                <div class="relative h-28 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 group">
                    <img id="settings-login-bg-preview" src="{{ $currentLoginBg }}" alt="Login Page Hero Preview" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <span class="text-[11px] text-white font-semibold bg-black/60 px-2.5 py-1 rounded-full">Current Active Hero</span>
                    </div>
                </div>

                <!-- Upload from computer OR paste URL -->
                <div class="space-y-2 pt-1">
                    <div>
                        <label class="block text-[10.5px] font-semibold text-gray-700 mb-1">Option A: Upload New Image File</label>
                        <input type="file" name="login_page_image_file" id="settings-login-bg-file" accept="image/*" onchange="previewLoginSettingsImageFile(this)" class="w-full text-xs text-gray-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-primary file:text-white hover:file:bg-brand-deep cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-[10.5px] font-semibold text-gray-700 mb-1">Option B: Image URL (Online / CDN)</label>
                        <input type="url" name="login_page_image" id="settings-login-bg-url" value="{{ $currentLoginBg }}" oninput="previewLoginSettingsImageUrl(this.value)" placeholder="https://images.unsplash.com/..." class="w-full px-3 py-1.5 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                    </div>

                    <!-- Presets for fast 1-click selection -->
                    <div class="pt-1 flex items-center gap-1.5 flex-wrap">
                        <span class="text-[10px] text-gray-400 font-semibold">Presets:</span>
                        <button type="button" onclick="setLoginPreset('https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&w=1600&q=80')" class="px-2 py-0.5 rounded bg-white hover:bg-gray-100 border border-gray-200 text-[10px] text-gray-600 font-medium transition cursor-pointer">Wooden Cottage</button>
                        <button type="button" onclick="setLoginPreset('https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?auto=format&fit=crop&w=1600&q=80')" class="px-2 py-0.5 rounded bg-white hover:bg-gray-100 border border-gray-200 text-[10px] text-gray-600 font-medium transition cursor-pointer">Backwaters Sunset</button>
                        <button type="button" onclick="setLoginPreset('https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?auto=format&fit=crop&w=1600&q=80')" class="px-2 py-0.5 rounded bg-white hover:bg-gray-100 border border-gray-200 text-[10px] text-gray-600 font-medium transition cursor-pointer">Munnar Hills</button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Central Reservation Telephone</label>
                <input type="text" name="resort_contact_phone" value="{{ \App\Models\Setting::get('resort_contact_phone', '+91 484 290 0000') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Concierge Email</label>
                <input type="email" name="resort_contact_email" value="{{ \App\Models\Setting::get('resort_contact_email', 'concierge@krishnaresorts.com') }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Default Room GST Tax (%)</label>
                    <input type="number" name="tax_gst_rate" value="{{ \App\Models\Setting::get('tax_gst_rate', 12) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Free Cancellation Window (Hours)</label>
                    <input type="number" name="cancellation_window_hours" value="{{ \App\Models\Setting::get('cancellation_window_hours', 48) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Advance Booking Deposit Option (%)</label>
                <input type="number" name="deposit_advance_percentage" value="{{ \App\Models\Setting::get('deposit_advance_percentage', 20) }}" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-settings')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs cursor-pointer">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewLoginSettingsImageFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('settings-login-bg-preview');
                if (img) img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function previewLoginSettingsImageUrl(url) {
        if (url && url.trim().length > 10) {
            const img = document.getElementById('settings-login-bg-preview');
            if (img) img.src = url.trim();
        }
    }
    function setLoginPreset(url) {
        const input = document.getElementById('settings-login-bg-url');
        const img = document.getElementById('settings-login-bg-preview');
        if (input) input.value = url;
        if (img) img.src = url;
    }
</script>


<!-- 10. ADD STAFF ACCOUNT MODAL (ADM-26) -->
<div id="modal-staff" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add New Staff / Administrator</h3>
            </div>
            <button onclick="closeModal('modal-staff')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-staff" onsubmit="submitStaffCreateForm(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Maya Varma" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="maya@krishnaresorts.com" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Phone Number (Optional)</label>
                    <input type="tel" name="phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Assigned Role *</label>
                    <select name="role" id="add-staff-role" required onchange="handleStaffRoleChange('add')" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="branch_manager">Branch Manager</option>
                        <option value="reservation_staff">Reservation Staff</option>
                        <option value="support_staff">Support Staff</option>
                        <option value="central_manager">Central Manager</option>
                        <option value="super_admin">Super Admin (Universal Access)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Login Password *</label>
                <input type="password" name="password" required minlength="6" placeholder="At least 6 characters" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
            </div>

            <!-- Branch Access Scope Selection -->
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="block text-[11px] font-bold text-brand-text">Branch Access Scope *</label>
                    <span class="text-[10px] text-brand-muted">Control branch operational visibility</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer transition">
                        <input type="radio" id="add-scope-all" name="branch_access_type" value="all" checked onchange="toggleBranchAccessScope('add', 'all')" class="text-brand-primary focus:ring-brand-primary">
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-brand-text block">All Branches</span>
                            <span class="text-[10px] text-brand-muted block">Universal access</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer transition">
                        <input type="radio" id="add-scope-assigned" name="branch_access_type" value="assigned" onchange="toggleBranchAccessScope('add', 'assigned')" class="text-brand-primary focus:ring-brand-primary">
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-brand-text block">Customized Branches</span>
                            <span class="text-[10px] text-brand-muted block">Select specific locations</span>
                        </div>
                    </label>
                </div>

                <!-- Customized Branches Multi-Checkboxes -->
                <div id="add-staff-custom-branches" class="hidden space-y-2 pt-2 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-gray-700">Choose permitted branches:</span>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllStaffBranches('add')" class="text-[10px] text-brand-primary hover:underline font-bold">Select All</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="clearAllStaffBranches('add')" class="text-[10px] text-gray-500 hover:underline">Clear</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-1">
                        @foreach($branches as $b)
                        <label class="flex items-start gap-2.5 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary/50 hover:bg-emerald-50/30 cursor-pointer transition">
                            <input type="checkbox" name="branch_ids[]" value="{{ $b->id }}" class="add-staff-branch-cb mt-0.5 rounded text-brand-primary focus:ring-brand-primary">
                            <div class="min-w-0">
                                <div class="font-bold text-brand-text text-[11px] flex items-center gap-1.5">
                                    <span class="truncate">{{ $b->name }}</span>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-mono">{{ $b->code }}</span>
                                </div>
                                <div class="text-[10px] text-brand-muted truncate">{{ $b->city ?? $b->state ?? 'Resort Branch' }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <p id="add-staff-branch-error" class="hidden text-[10px] text-rose-600 font-semibold mt-1">Please select at least one branch for customized access.</p>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-staff')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="btn-create-staff" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Create Staff Account</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 10B. EDIT STAFF ACCOUNT MODAL (ADM-26) -->
<div id="modal-edit-staff" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="user-cog" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm" id="edit-staff-modal-title">Edit Staff Account</h3>
            </div>
            <button onclick="closeModal('modal-edit-staff')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-staff" onsubmit="submitStaffEditForm(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-staff-id" name="id">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Full Name *</label>
                    <input type="text" id="edit-staff-name" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Email Address *</label>
                    <input type="email" id="edit-staff-email" name="email" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" id="edit-staff-phone" name="phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Assigned Role *</label>
                    <select id="edit-staff-role" name="role" required onchange="handleStaffRoleChange('edit')" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="branch_manager">Branch Manager</option>
                        <option value="reservation_staff">Reservation Staff</option>
                        <option value="support_staff">Support Staff</option>
                        <option value="central_manager">Central Manager</option>
                        <option value="super_admin">Super Admin (Universal Access)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">New Password (Optional)</label>
                    <input type="password" id="edit-staff-password" name="password" minlength="6" placeholder="Leave blank to keep current" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Account Status</label>
                    <select id="edit-staff-is-active" name="is_active" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden text-xs">
                        <option value="1">Active Account</option>
                        <option value="0">Disabled / Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Branch Access Scope Selection -->
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="block text-[11px] font-bold text-brand-text">Branch Access Scope *</label>
                    <span class="text-[10px] text-brand-muted">Control branch operational visibility</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer transition">
                        <input type="radio" id="edit-scope-all" name="branch_access_type" value="all" onchange="toggleBranchAccessScope('edit', 'all')" class="text-brand-primary focus:ring-brand-primary">
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-brand-text block">All Branches</span>
                            <span class="text-[10px] text-brand-muted block">Universal access</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary cursor-pointer transition">
                        <input type="radio" id="edit-scope-assigned" name="branch_access_type" value="assigned" onchange="toggleBranchAccessScope('edit', 'assigned')" class="text-brand-primary focus:ring-brand-primary">
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-brand-text block">Customized Branches</span>
                            <span class="text-[10px] text-brand-muted block">Select specific locations</span>
                        </div>
                    </label>
                </div>

                <!-- Customized Branches Multi-Checkboxes -->
                <div id="edit-staff-custom-branches" class="hidden space-y-2 pt-2 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold text-gray-700">Choose permitted branches:</span>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllStaffBranches('edit')" class="text-[10px] text-brand-primary hover:underline font-bold">Select All</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="clearAllStaffBranches('edit')" class="text-[10px] text-gray-500 hover:underline">Clear</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-1">
                        @foreach($branches as $b)
                        <label class="flex items-start gap-2.5 p-2 rounded-lg border border-gray-200 bg-white hover:border-brand-primary/50 hover:bg-emerald-50/30 cursor-pointer transition">
                            <input type="checkbox" name="branch_ids[]" value="{{ $b->id }}" class="edit-staff-branch-cb mt-0.5 rounded text-brand-primary focus:ring-brand-primary">
                            <div class="min-w-0">
                                <div class="font-bold text-brand-text text-[11px] flex items-center gap-1.5">
                                    <span class="truncate">{{ $b->name }}</span>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-mono">{{ $b->code }}</span>
                                </div>
                                <div class="text-[10px] text-brand-muted truncate">{{ $b->city ?? $b->state ?? 'Resort Branch' }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <p id="edit-staff-branch-error" class="hidden text-[10px] text-rose-600 font-semibold mt-1">Please select at least one branch for customized access.</p>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-staff')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="btn-update-staff" class="px-4 py-2 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 17. ADD / EDIT CANCELLATION RULE TIER MODAL -->
<div id="modal-cancellation-rule" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                <h3 class="font-bold text-sm" id="cancellation-modal-title">Add Cancellation Cashback Tier</h3>
            </div>
            <button onclick="closeModal('modal-cancellation-rule')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-cancellation-rule" onsubmit="submitCancellationRuleForm(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="cancellation-rule-id" name="id" value="">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Hours Prior to Check-in (14:00) *</label>
                    <input type="number" id="rule-hours-input" name="hours_before_checkin" required min="0" placeholder="e.g. 72" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary font-mono text-xs">
                    <span class="text-[10px] text-gray-400 block mt-0.5">e.g. 72, 48, 24, 0</span>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cashback Percentage (%) *</label>
                    <input type="number" id="rule-pct-input" name="refund_percentage" required min="0" max="100" step="0.5" placeholder="e.g. 100" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary font-bold text-xs text-emerald-700">
                    <span class="text-[10px] text-gray-400 block mt-0.5">e.g. 100, 75, 50, 0</span>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Applicable Branch Scope</label>
                <select id="rule-branch-select" name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                    <option value="">All Resort Branches (Global Resort Policy)</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->city }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Policy Description / Message to Guest *</label>
                <input type="text" id="rule-desc-input" name="description" required placeholder="e.g. 100% Full Cashback if cancelled 72+ hours prior to arrival." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3 items-center pt-1">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Display Sort Order</label>
                    <input type="number" id="rule-sort-input" name="sort_order" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                </div>
                <div class="pt-4">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="rule-active-input" name="is_active" value="1" checked class="w-4 h-4 text-emerald-600 rounded">
                        <span class="font-bold text-gray-700 text-xs">Rule Tier Active</span>
                    </label>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-cancellation-rule')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="submit-rule-btn" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs">Save Cashback Tier</button>
            </div>
        </form>
    </div>
</div>

<!-- 18. MANAGER EXTENSION DISCOUNT OFFER MODAL -->
<div id="modal-extension-offer" class="hidden fixed inset-0 z-[80] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="z-index: 80;">
    <div class="bg-white rounded-2xl w-full max-w-lg flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="tag" class="w-4 h-4 text-amber-400"></i>
                <h3 class="font-bold text-sm">Review Stay Extension & Special Offer</h3>
            </div>
            <button onclick="closeModal('modal-extension-offer')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-extension-offer" onsubmit="submitExtensionOfferForm(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="ext-offer-id" name="id" value="">

            <div class="bg-paper/70 p-3.5 rounded-xl border border-gray-200/80 space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-brand-muted">Guest:</span>
                    <span id="ext-offer-guest" class="font-bold text-brand-text">Guest Name</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-brand-muted">Stay Extension Dates:</span>
                    <span id="ext-offer-dates" class="font-bold text-brand-text">Dates</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-brand-muted">Allocated Unit(s):</span>
                    <span id="ext-offer-rooms" class="font-bold text-emerald-800">Room(s)</span>
                </div>
                <div class="flex justify-between pt-1 border-t border-gray-200 text-brand-muted">
                    <span>Standard Published Total:</span>
                    <span id="ext-offer-standard" class="font-bold text-brand-text line-through">₹0</span>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-gray-700">Manager Discounted Offer Price (₹) *</label>
                <input type="number" id="ext-offer-amount-input" name="offered_amount" required step="10" placeholder="e.g. 8500" class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 font-extrabold text-base text-emerald-800 focus:ring-1 focus:ring-brand-primary">
                <span class="text-[10px] text-emerald-700 block">Offer rate will be added to the guest's folio with pending payment status.</span>
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-gray-700">Manager Notes / Offer Perks</label>
                <input type="text" id="ext-offer-notes" name="manager_notes" placeholder="e.g. Special loyalty extension rate + complimentary plantation breakfast." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-extension-offer')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="submit-ext-offer-btn" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Approve Extension & Send Offer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 19. ON-HAND / CHECKOUT EXTENSION PAYMENT COLLECTION MODAL -->
<div id="modal-extension-payment" class="hidden fixed inset-0 z-[80] bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="z-index: 80;">
    <div class="bg-white rounded-2xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4 text-emerald-400"></i>
                <h3 class="font-bold text-sm">Record On-Hand / Checkout Payment</h3>
            </div>
            <button onclick="closeModal('modal-extension-payment')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-extension-payment" onsubmit="submitExtensionPaymentForm(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="ext-pay-id" name="id" value="">

            <div class="bg-emerald-50/70 p-3 rounded-xl border border-emerald-200/80 space-y-1">
                <div class="flex justify-between font-bold text-xs text-brand-text">
                    <span>Approved Extension Amount:</span>
                    <span id="ext-pay-amount-label" class="text-emerald-800 font-extrabold text-sm">₹0</span>
                </div>
                <div class="text-[10px] text-brand-muted" id="ext-pay-guest-label">Guest: Loading...</div>
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-gray-700">Payment Collection Method *</label>
                <select id="ext-pay-method-select" name="payment_method" required class="w-full px-3 py-2 rounded-lg border border-gray-300 font-semibold text-xs">
                    <option value="cash">💵 Cash on Hand (Cottage / Front Desk)</option>
                    <option value="pos_card">💳 Front-Desk POS Card Terminal</option>
                    <option value="counter_upi">📱 Front-Desk UPI QR</option>
                    <option value="bank_transfer">🏦 Direct Bank NEFT/RTGS</option>
                    <option value="online">🌐 Online Guest Portal</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-gray-700">Amount Collected (₹) *</label>
                <input type="number" id="ext-pay-amount-input" name="paid_amount" required step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 font-bold text-xs text-emerald-800 font-mono">
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-gray-700">Internal Reference / Notes</label>
                <input type="text" id="ext-pay-notes-input" name="notes" placeholder="e.g. Settled in cash with reception staff Ramesh." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-extension-payment')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="submit-ext-pay-btn" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs">Record & Mark Paid</button>
            </div>
        </form>
    </div>
</div>

<!-- 20. FAST ASSIGN ROOM & CHECK-IN MODAL (Today's Flight Deck) -->
<div id="modal-assign-room" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="key" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Assign Physical Room</h3>
            </div>
            <button onclick="closeModal('modal-assign-room')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-assign-room" onsubmit="submitAssignRoomForm(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="assign-room-res-id" name="reservation_id" value="">
            <input type="hidden" id="assign-and-checkin-flag" name="auto_checkin" value="0">

            <div class="bg-brand-canvas p-3 rounded-xl border border-brand-primary/20 space-y-1">
                <div class="flex justify-between font-bold text-xs text-brand-text">
                    <span id="assign-room-guest-label">Guest: Loading...</span>
                    <span id="assign-room-code-label" class="font-mono text-brand-primary">#...</span>
                </div>
                <p class="text-[11px] text-brand-muted" id="assign-room-dates-label">Dates: Checking availability...</p>
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-gray-700">Select Clean & Available Room *</label>
                <select id="assign-room-select" name="room_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 font-semibold text-xs">
                    <option value="">-- Choose Available Room --</option>
                    @foreach($rooms->where('operational_status', 'available') as $r)
                    <option value="{{ $r->id }}" data-branch="{{ $r->branch_id }}" data-type="{{ $r->room_type_id }}">
                        Room {{ $r->room_number }} ({{ $r->roomType ? $r->roomType->name : 'Room' }}) · Floor {{ $r->floor }} · {{ $r->housekeeping_status }}
                    </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-brand-muted">Only rooms matching branch & confirmed dates are shown.</p>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-assign-room')" class="px-3.5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="button" onclick="assignRoomAndCheckIn(false)" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-brand-text font-bold text-xs">Assign Only</button>
                <button type="button" onclick="assignRoomAndCheckIn(true)" class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                    <i data-lucide="zap" class="w-3.5 h-3.5"></i> Assign & Check-In
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 21. ADD CMS CUSTOM PAGE MODAL -->
<div id="modal-cms-page" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="file-plus" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Create New CMS Content Page</h3>
            </div>
            <button onclick="closeModal('modal-cms-page')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-cms-page" onsubmit="submitCmsPage(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Page Title *</label>
                <input type="text" id="cms-page-title" name="title" required placeholder="e.g. Guided Plantation Trekking" oninput="autoGenerateCmsSlug(this.value)" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">URL Slug *</label>
                    <div class="flex items-center">
                        <span class="px-2.5 py-2 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-500 font-mono text-[11px]">/</span>
                        <input type="text" id="cms-page-slug" name="slug" required placeholder="guided-trekking" class="w-full px-3 py-2 rounded-r-lg border border-gray-300 font-mono text-xs">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Scope</label>
                    <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                        <option value="">Resort-wide (All Branches)</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Meta Description (SEO)</label>
                <input type="text" name="meta_description" placeholder="Brief 1-2 sentence description for search engines" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Page Content (Markdown / HTML) *</label>
                <textarea name="content" rows="6" required placeholder="Write page content here..." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="cms-page-published" name="is_published" value="1" checked class="rounded text-brand-primary w-4 h-4">
                <label for="cms-page-published" class="text-xs font-semibold text-gray-700 cursor-pointer">Publish immediately on site</label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-cms-page')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="submit-cms-page-btn" class="px-5 py-2 rounded-lg bg-brand-deep hover:bg-black text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Save & Publish Page
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 22. EDIT CMS CUSTOM PAGE MODAL -->
<div id="modal-edit-cms-page" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Edit CMS Page</h3>
            </div>
            <button onclick="closeModal('modal-edit-cms-page')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-edit-cms-page" onsubmit="submitEditCmsPage(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="edit-cms-page-id" name="page_id" value="">

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Page Title *</label>
                <input type="text" id="edit-cms-page-title" name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">URL Slug *</label>
                    <div class="flex items-center">
                        <span class="px-2.5 py-2 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-500 font-mono text-[11px]">/</span>
                        <input type="text" id="edit-cms-page-slug" name="slug" required class="w-full px-3 py-2 rounded-r-lg border border-gray-300 font-mono text-xs">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Scope</label>
                    <select id="edit-cms-page-branch" name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                        <option value="">Resort-wide (All Branches)</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Meta Description (SEO)</label>
                <input type="text" id="edit-cms-page-meta" name="meta_description" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Page Content *</label>
                <textarea id="edit-cms-page-content" name="content" rows="6" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="edit-cms-page-published" name="is_published" value="1" class="rounded text-brand-primary w-4 h-4">
                <label for="edit-cms-page-published" class="text-xs font-semibold text-gray-700 cursor-pointer">Published on public site</label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-edit-cms-page')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="submit-edit-cms-page-btn" class="px-5 py-2 rounded-lg bg-brand-deep hover:bg-black text-white font-bold text-xs shadow-xs">
                    Update Page
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 23. ADD NAVIGATION ITEM MODAL -->
<div id="modal-cms-nav" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="compass" class="w-4 h-4 text-brand-accent"></i>
                <h3 class="font-bold text-sm">Add Header Navigation Item</h3>
            </div>
            <button onclick="closeModal('modal-cms-nav')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-cms-nav" onsubmit="submitCmsNav(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Navigation Label *</label>
                <input type="text" name="label" required placeholder="e.g. Plantation Tour" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Target URL / Section Anchor *</label>
                <input type="text" name="url" required placeholder="e.g. /plantation or #experiences" class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Parent Item (Optional)</label>
                    <select name="parent_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                        <option value="">Top-Level Link</option>
                        @foreach($navigationItems as $pNav)
                        <option value="{{ $pNav->id }}">{{ $pNav->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Branch Scope</label>
                    <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="cms-nav-active" name="is_active" value="1" checked class="rounded text-brand-primary w-4 h-4">
                <label for="cms-nav-active" class="text-xs font-semibold text-gray-700 cursor-pointer">Active link (visible in header)</label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-cms-nav')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="submit-cms-nav-btn" class="px-5 py-2 rounded-lg bg-brand-deep hover:bg-black text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Save Navigation Link
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================
     SPICE RETURN POLICY RULES MODAL (ADM-17)
     ======================================================== -->
<div id="modal-spice-return-rule" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="rotate-ccw" class="w-4 h-4 text-emerald-400"></i>
                <h3 class="font-bold text-sm" id="spice-rule-modal-title">Add Spice Return Policy Tier</h3>
            </div>
            <button onclick="closeModal('modal-spice-return-rule')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-spice-return-rule" onsubmit="submitSpiceRuleForm(event)" class="p-4 sm:p-5 space-y-3.5 text-xs overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="spice-rule-id" name="id" value="">

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Rule Tier Name *</label>
                <input type="text" id="spice-rule-name-input" name="name" required placeholder="e.g. Pre-Dispatch Full Cancellation" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Application Stage *</label>
                    <select id="spice-rule-applies-select" name="applies_to" required class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs">
                        <option value="before_dispatch">Before Dispatch (Cancellation)</option>
                        <option value="after_delivery">After Delivery (Return)</option>
                        <option value="general">General / Damaged Parcel</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Time Limit (Hours) *</label>
                    <input type="number" id="spice-rule-hours-input" name="time_limit_hours" required min="0" placeholder="e.g. 24 or 168" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary font-mono text-xs">
                    <span class="text-[10px] text-gray-400 block mt-0.5">24h (1d), 168h (7 days)</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cashback Refund (%) *</label>
                    <input type="number" id="spice-rule-pct-input" name="refund_percentage" required min="0" max="100" step="0.5" placeholder="e.g. 100 or 90" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary font-bold text-xs text-emerald-700">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Handling / Restocking Fee (₹)</label>
                    <input type="number" id="spice-rule-fee-input" name="handling_fee" min="0" step="1" value="0.00" class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-xs">
                    <span class="text-[10px] text-gray-400 block mt-0.5">Deducted from cashback</span>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Policy Description / Customer Terms *</label>
                <textarea id="spice-rule-desc-input" name="description" rows="2" required placeholder="Explain condition (e.g. 100% full refund before spice milling and packing)." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3 items-center pt-1">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Sort Order</label>
                    <input type="number" id="spice-rule-sort-input" name="sort_order" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                </div>
                <div class="pt-4">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="spice-rule-active-input" name="is_active" value="1" checked class="w-4 h-4 text-emerald-600 rounded">
                        <span class="font-bold text-gray-700 text-xs">Rule Tier Active</span>
                    </label>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-spice-return-rule')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                <button type="submit" id="submit-spice-rule-btn" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs">Save Policy Tier</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================
     SPICE RETURN CLAIM PROCESSING MODAL
     ======================================================== -->
<div id="modal-spice-process-return" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg flex flex-col shadow-2xl overflow-hidden border border-gray-200 animate-fadeIn">
        <div class="bg-brand-deep text-white px-5 py-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                <h3 class="font-bold text-sm">Process Spice Return / Refund</h3>
            </div>
            <button onclick="closeModal('modal-spice-process-return')" class="text-white/60 hover:text-white p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-4 sm:p-5 space-y-4 text-xs overflow-y-auto flex-1">
            <!-- Order Summary Pill -->
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-brand-text text-sm" id="return-proc-order-num">#KSP-0000</span>
                    <span class="font-mono font-bold text-brand-text text-sm" id="return-proc-bill">₹0.00</span>
                </div>
                <div class="text-[11px] text-gray-500" id="return-proc-date">Order Placed recently</div>
            </div>

            <!-- Customer Reason -->
            <div class="p-3 bg-rose-50/70 border border-rose-200 rounded-xl space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-800 block">Customer Stated Reason</span>
                <p class="text-rose-950 font-medium text-xs leading-relaxed" id="return-proc-reason">No reason provided</p>
            </div>

            <!-- Dynamic Policy Calculation Card -->
            <div class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-xl space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Automated Policy Rule Calculation</span>
                <div class="flex items-center justify-between text-xs text-emerald-950">
                    <span>Matched Policy Tier:</span>
                    <strong id="return-proc-rule-name">Pre-Dispatch Full Cancellation</strong>
                </div>
                <div class="flex items-center justify-between text-xs text-emerald-950">
                    <span>Refund Percentage:</span>
                    <strong id="return-proc-pct">100%</strong>
                </div>
                <div class="flex items-center justify-between text-xs text-emerald-950">
                    <span>Handling Fee Deduction:</span>
                    <strong id="return-proc-fee">₹0.00</strong>
                </div>
                <div class="pt-2 border-t border-emerald-200/60 flex items-center justify-between font-bold text-sm text-emerald-900">
                    <span>Net Cashback:</span>
                    <span class="font-mono text-base" id="return-proc-cashback">₹0.00</span>
                </div>
            </div>

            <!-- Manager Refund Amount Override -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Approved Refund Amount (₹) *</label>
                <input type="number" id="return-proc-amount-input" step="0.5" class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono font-bold text-sm text-emerald-800 focus:ring-1 focus:ring-brand-primary">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Manager Notes (Optional)</label>
                <textarea id="return-proc-notes-input" rows="2" placeholder="e.g. Approved under harvest guarantee policy." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs"></textarea>
            </div>

            <input type="hidden" id="return-proc-order-id" value="">

            <div class="pt-3 border-t border-gray-200 flex items-center justify-between gap-2">
                <button type="button" onclick="submitSpiceReturnDecision('reject')" class="px-4 py-2 rounded-lg border border-rose-300 text-rose-700 hover:bg-rose-50 font-semibold text-xs">
                    Reject Request
                </button>
                <div class="flex gap-2">
                    <button type="button" onclick="closeModal('modal-spice-process-return')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold text-xs">Cancel</button>
                    <button type="button" onclick="submitSpiceReturnDecision('approve')" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs">
                        Approve &amp; Refund
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
