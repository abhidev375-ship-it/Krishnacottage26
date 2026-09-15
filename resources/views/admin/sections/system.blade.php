<!-- SYSTEM & ADMINISTRATION SECTION (ADM-26, ADM-27, ADM-28, ADM-29, ADM-21) -->
<section id="system" class="section space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-brand-text">System & Administration</h2>
            <p class="text-brand-muted text-xs mt-0.5">Staff roles, immutable audit trails, central resort settings, and automated notifications.</p>
        </div>
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full no-scrollbar">
            <button onclick="switchSystemTab('staff')" id="sys-tab-btn-staff" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-primary text-white transition shrink-0">Staff ({{ $staffMembers->count() }})</button>
            <button onclick="switchSystemTab('cancellation')" id="sys-tab-btn-cancellation" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0 flex items-center gap-1"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600"></i> Cancellation Rules ({{ $cancellationRules->count() }})</button>
            <button onclick="switchSystemTab('audit')" id="sys-tab-btn-audit" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">Audit Log ({{ $auditLogs->count() }})</button>
            <button onclick="switchSystemTab('settings')" id="sys-tab-btn-settings" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">Settings</button>
            <button onclick="switchSystemTab('templates')" id="sys-tab-btn-templates" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">Templates ({{ $notificationTemplates->count() }})</button>
            <button onclick="switchSystemTab('notifs')" id="sys-tab-btn-notifs" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-brand-muted hover:bg-gray-200 transition shrink-0">Delivery Logs</button>
        </div>
    </div>

    <!-- SUB-TAB 1: STAFF & PERMISSIONS (ADM-26) -->
    <div id="sys-tab-staff" class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
        <div class="p-3.5 border-b border-gray-100 flex justify-between items-center">
            <span class="text-xs text-brand-muted font-medium">Internal resort staff, managers, and permission roles</span>
            <button onclick="openModal('modal-staff')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Staff Account
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Staff Member</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Assigned Role</th>
                        <th class="px-4 py-3">Branch Access Scope</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($staffMembers as $user)
                    <tr class="hover:bg-brand-canvas/60 transition">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-primary text-white font-bold flex items-center justify-center text-xs">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-brand-text">{{ $user->name }}</div>
                                    <div class="text-[10px] text-brand-muted">Staff ID: #{{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-medium text-brand-text">{{ $user->email }}</div>
                            <div class="text-[11px] text-brand-muted">{{ $user->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-900 border border-purple-200">
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-brand-muted">
                            @if($user->branch_access_type === 'all')
                                <span class="font-semibold text-emerald-800">All {{ $branches->count() }} Branches</span>
                            @else
                                <span class="font-semibold text-brand-primary">{{ $user->branches->pluck('name')->join(', ') ?: 'None' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($user->is_active)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right space-x-1">
                            <button onclick='openEditStaffModal(@json([
                                "id" => $user->id,
                                "name" => $user->name,
                                "email" => $user->email,
                                "phone" => $user->phone ?? "",
                                "role" => $user->role,
                                "branch_access_type" => $user->branch_access_type ?? "all",
                                "branch_ids" => $user->branches->pluck("id")->toArray(),
                                "is_active" => $user->is_active ? 1 : 0,
                            ]))' class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded transition font-medium">Edit</button>
                            @if(auth()->id() !== $user->id)
                            <button onclick="deleteStaffMember({{ $user->id }}, '{{ addslashes($user->name) }}')" class="px-2 py-1 text-xs text-rose-600 hover:bg-rose-50 rounded transition font-medium">Delete</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- SUB-TAB: CANCELLATION RULES & AUTOMATED PAYBACK ENGINE (ADM-28) -->
    <div id="sys-tab-cancellation" class="hidden space-y-4">
        <!-- 1. Policy Description Editor -->
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-4 sm:p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-brand-text">Public Cancellation Policy Terms</h3>
                        <p class="text-[10px] text-brand-muted">Visible to guests on booking confirmation, stay pass, and customer portal</p>
                    </div>
                </div>
                <button type="button" onclick="saveCancellationPolicyDescription()" id="save-policy-desc-btn" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg shadow-xs transition flex items-center gap-1">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i> Save Terms
                </button>
            </div>
            <textarea id="cancellation-policy-textarea" rows="3" class="w-full p-2.5 bg-brand-canvas border border-gray-200 rounded-xl text-xs text-brand-text font-normal focus:ring-1 focus:ring-brand-primary">{{ $cancellationPolicy }}</textarea>
        </div>

        <!-- 2. Tiered Cashback Rules Table -->
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
            <div class="p-3.5 border-b border-gray-100 flex justify-between items-center bg-gray-50/60">
                <div>
                    <h4 class="text-xs font-bold text-brand-text">Dynamic Cashback Tiers</h4>
                    <p class="text-[10px] text-brand-muted">System automatically calculates exact elapsed hours and executes instant automated refunds</p>
                </div>
                <button onclick="openCancellationRuleModal()" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Cashback Tier
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Notice Window</th>
                            <th class="px-4 py-3">Cashback %</th>
                            <th class="px-4 py-3">Branch Scope</th>
                            <th class="px-4 py-3">Description / Guest Message</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($cancellationRules as $rule)
                        <tr class="hover:bg-brand-canvas/60 transition">
                            <td class="px-4 py-3.5 font-bold text-brand-text">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[11px]">
                                    &ge; {{ $rule->hours_before_checkin }} Hours Prior
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-extrabold text-sm {{ $rule->refund_percentage > 0 ? 'text-emerald-700' : 'text-gray-500' }}">
                                    {{ number_format($rule->refund_percentage, 0) }}%
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-brand-muted">
                                {{ $rule->branch ? $rule->branch->name : 'All Resort Branches (Global)' }}
                            </td>
                            <td class="px-4 py-3.5 text-brand-text max-w-xs">
                                {{ $rule->description }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($rule->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">Disabled</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1">
                                <button onclick='editCancellationRule(@json($rule))' class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded transition font-medium">Edit</button>
                                <button onclick="deleteCancellationRule({{ $rule->id }})" class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 rounded transition font-medium">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-brand-muted">No cancellation rules configured. Click 'Add Cashback Tier' to create rules.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 2: AUDIT LOG (ADM-27) -->
    <div id="sys-tab-audit" class="hidden bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
        <div class="p-3.5 border-b border-gray-100 font-bold text-sm text-brand-text flex items-center justify-between">
            <span>Administrative Traceability Timeline</span>
            <span class="text-xs text-brand-muted">Immutable ledger</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Timestamp</th>
                        <th class="px-4 py-3">User & Role</th>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Entity Type & ID</th>
                        <th class="px-4 py-3">Changes Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($auditLogs as $audit)
                    <tr class="hover:bg-brand-canvas/60 transition">
                        <td class="px-4 py-3 text-brand-muted">{{ $audit->created_at->format('d M Y, H:i:s') }}</td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-brand-text">{{ $audit->user_name }}</div>
                            <div class="text-[10px] text-brand-muted">{{ $audit->role }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-brand-text uppercase">
                                {{ str_replace('_', ' ', $audit->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px] text-brand-primary">
                            {{ $audit->entity_type }} #{{ $audit->entity_id }}
                        </td>
                        <td class="px-4 py-3 text-[11px] font-mono text-brand-muted max-w-sm truncate">
                            {{ json_encode($audit->new_values) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-brand-muted">No audit logs recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- SUB-TAB 3: SETTINGS (ADM-28) -->
    <div id="sys-tab-settings" class="hidden space-y-4">
        <div class="flex justify-between items-center bg-white p-3.5 rounded-xl border border-gray-200/70 shadow-xs">
            <span class="text-xs text-brand-muted font-medium">Global resort contact, taxation rates, cancellation policies</span>
            <button onclick="openModal('modal-settings')" class="px-3 py-1.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
                <i data-lucide="settings" class="w-3.5 h-3.5"></i> Edit Settings
            </button>
        </div>
        <!-- AUTH EXPERIENCE & LOGIN IMAGE CARD (ADM-28) -->
        @php
            $currentLoginBg = \App\Models\Setting::get('login_page_image', 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&w=1600&q=80');
        @endphp
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-4 space-y-3 shadow-xs">
            <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                <h3 class="font-bold text-sm text-brand-text uppercase tracking-wider text-[11px] text-brand-primary flex items-center gap-1.5">
                    <i data-lucide="image" class="w-3.5 h-3.5"></i> Customer Login & Register Page Hero Image
                </h3>
                <button onclick="openModal('modal-settings')" class="px-2.5 py-1 rounded-lg bg-brand-primary/10 hover:bg-brand-primary/20 text-brand-primary text-xs font-bold flex items-center gap-1 transition cursor-pointer">
                    <i data-lucide="edit-3" class="w-3 h-3"></i> Change Image
                </button>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="w-32 h-20 rounded-lg overflow-hidden border border-gray-200 shrink-0 bg-gray-100 shadow-xs">
                    <img src="{{ $currentLoginBg }}" alt="Login Screen Image" class="w-full h-full object-cover">
                </div>
                <div class="space-y-1.5 flex-1">
                    <div class="text-xs font-bold text-brand-text">Active Split-Screen Background Photography</div>
                    <div class="text-[11px] text-brand-muted font-mono truncate max-w-sm sm:max-w-xl bg-gray-50 px-2.5 py-1 rounded-md border border-gray-100">{{ $currentLoginBg }}</div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Live on /login & /register</span>
                        <span class="text-[10px] text-brand-muted">Click "Change Image" or "Edit Settings" above to upload a new file or paste any image URL</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RAZORPAY PAYMENT GATEWAY SETTINGS (ADM-28) -->
        @php
            $razorpayKeyId = \App\Models\Setting::get('razorpay_key_id', config('services.razorpay.key_id', ''));
            $razorpayKeySecret = \App\Models\Setting::get('razorpay_key_secret', config('services.razorpay.key_secret', ''));
            $razorpayWebhookSecret = \App\Models\Setting::get('razorpay_webhook_secret', config('services.razorpay.webhook_secret', ''));
            $isLiveKey = str_starts_with($razorpayKeyId, 'rzp_live_');
            $isTestKey = str_starts_with($razorpayKeyId, 'rzp_test_');
            $isConfigured = !empty($razorpayKeyId) && !empty($razorpayKeySecret);
        @endphp
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-5 space-y-4 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#063F34] flex items-center justify-center text-[#C9A86A] shadow-xs">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-brand-text flex items-center gap-2">
                            Razorpay Payment Gateway
                            @if($isLiveKey)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">● LIVE PRODUCTION</span>
                            @elseif($isTestKey)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">● TEST / SANDBOX</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">NOT CONFIGURED</span>
                            @endif
                        </h3>
                        <p class="text-[11px] text-brand-muted">Handles automated checkout for Room Reservations & Spice Orders with instant webhook reconciliation.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="testRazorpayConnectionAdmin()" id="btn-test-razorpay" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-brand-text text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer border border-gray-200">
                        <i data-lucide="activity" class="w-3.5 h-3.5 text-brand-primary"></i>
                        <span id="btn-test-text">Test API Connection</span>
                    </button>
                </div>
            </div>

            <!-- Connection Status Box (hidden until tested) -->
            <div id="razorpay-test-result" class="hidden p-3 rounded-lg text-xs font-medium border flex items-center gap-2"></div>

            <form id="form-razorpay-settings" onsubmit="handleRazorpaySettingsSubmit(event)" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-brand-text mb-1 uppercase tracking-wider text-[10px]">
                            Razorpay Key ID <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="razorpay_key_id" id="razorpay_key_id" required value="{{ $razorpayKeyId }}" placeholder="rzp_test_... or rzp_live_..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden font-mono text-xs">
                        <p class="text-[10px] text-brand-muted mt-1">Found in your Razorpay Dashboard &rarr; Account &amp; Settings &rarr; API Keys.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-brand-text mb-1 uppercase tracking-wider text-[10px]">
                            Razorpay Key Secret <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="razorpay_key_secret" id="razorpay_key_secret" required value="{{ $razorpayKeySecret }}" placeholder="Key Secret..." class="w-full px-3 py-2 pr-10 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden font-mono text-xs">
                            <button type="button" onclick="toggleSecretVisibility('razorpay_key_secret', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <p class="text-[10px] text-brand-muted mt-1">Kept encrypted on server for HMAC-SHA256 signature verification.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-brand-text mb-1 uppercase tracking-wider text-[10px]">
                            Webhook Secret (Optional)
                        </label>
                        <input type="text" name="razorpay_webhook_secret" id="razorpay_webhook_secret" value="{{ $razorpayWebhookSecret }}" placeholder="Optional webhook secret..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary focus:outline-hidden font-mono text-xs">
                        <p class="text-[10px] text-brand-muted mt-1">Secures incoming webhook notifications from Razorpay.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-brand-text mb-1 uppercase tracking-wider text-[10px]">
                            Webhook Callback URL (Copy to Razorpay Dashboard)
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" readonly value="{{ url('/webhooks/razorpay') }}" id="razorpay_webhook_url" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-brand-muted font-mono text-xs">
                            <button type="button" onclick="copyWebhookUrl()" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-brand-text font-bold text-xs border border-gray-300 shrink-0 flex items-center gap-1 transition cursor-pointer">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i> Copy
                            </button>
                        </div>
                        <p class="text-[10px] text-brand-muted mt-1">Subscribe to: <code class="bg-gray-100 px-1 py-0.5 rounded">payment.captured</code>, <code class="bg-gray-100 px-1 py-0.5 rounded">payment.failed</code>, <code class="bg-gray-100 px-1 py-0.5 rounded">order.paid</code>.</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-mint/50 border border-emerald/20 text-xs text-brand-primary flex items-start gap-2.5">
                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold block">Production Live Switch:</span>
                        <span>To move from Test to Production, simply paste your Live credentials starting with <code class="font-mono bg-white/70 px-1 py-0.5 rounded border border-emerald/20">rzp_live_...</code> and click Save. The system immediately routes customer payments to your live bank account without downtime or code deployment.</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" id="btn-save-razorpay" class="px-5 py-2.5 rounded-lg bg-brand-primary hover:bg-brand-deep text-white font-bold text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span id="btn-save-razorpay-text">Save Gateway Credentials</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- CALLMEBOT WHATSAPP OPERATIONAL GATEWAY -->
        @php
            $whatsappEnabled = (bool) \App\Models\Setting::get('whatsapp_notifications_enabled', true);
            $callmebotPhone = \App\Models\Setting::get('callmebot_phone', env('CALLMEBOT_PHONE', ''));
            $callmebotApiKey = \App\Models\Setting::get('callmebot_apikey', env('CALLMEBOT_APIKEY', ''));
            $isWaConfigured = !empty($callmebotPhone) && !empty($callmebotApiKey);
        @endphp
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-5 space-y-4 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-700 flex items-center justify-center text-white shadow-xs">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-brand-text flex items-center gap-2">
                            CallMeBot WhatsApp Operational Gateway
                            @if($isWaConfigured && $whatsappEnabled)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">● LIVE READY</span>
                            @elseif(!$whatsappEnabled)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">PAUSED / DISABLED</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">NEEDS API KEY</span>
                            @endif
                        </h3>
                        <p class="text-[11px] text-brand-muted">Automated real-time WhatsApp dispatches for Bookings, Enquiries, Kitchen Orders, Extensions & Concierge.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="testWhatsAppConnectionAdmin()" id="btn-test-whatsapp" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-brand-text text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer border border-gray-200">
                        <i data-lucide="send" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span id="btn-test-wa-text">Test WhatsApp Ping</span>
                    </button>
                </div>
            </div>

            <!-- Connection Status Box (hidden until tested) -->
            <div id="whatsapp-test-result" class="hidden p-3 rounded-lg text-xs font-medium border flex items-center gap-2"></div>

            <form id="form-whatsapp-settings" onsubmit="handleWhatsAppSettingsSubmit(event)" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-brand-text mb-1 uppercase tracking-wider text-[10px]">
                            Notification Delivery
                        </label>
                        <select name="whatsapp_notifications_enabled" id="wa_enabled" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary text-xs bg-white">
                            <option value="1" {{ $whatsappEnabled ? 'selected' : '' }}>Enabled (Instant WhatsApp Alerts Active)</option>
                            <option value="0" {{ !$whatsappEnabled ? 'selected' : '' }}>Disabled (Temporarily Pause Alerts)</option>
                        </select>
                        <p class="text-[10px] text-brand-muted mt-1">Master switch to turn on/off all WhatsApp alerts.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-brand-text mb-1 uppercase tracking-wider text-[10px]">
                            Manager WhatsApp Phone <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="callmebot_phone" id="wa_phone" required value="{{ $callmebotPhone }}" placeholder="+919447122334" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary font-mono text-xs">
                        <p class="text-[10px] text-brand-muted mt-1">Include country code (e.g. +91 94471 22334).</p>
                    </div>

                    <div>
                        <label class="block font-bold text-brand-text mb-1 uppercase tracking-wider text-[10px]">
                            CallMeBot API Key <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="callmebot_apikey" id="wa_apikey" required value="{{ $callmebotApiKey }}" placeholder="123456" class="w-full px-3 py-2 pr-10 rounded-lg border border-gray-300 focus:ring-1 focus:ring-brand-primary font-mono text-xs">
                            <button type="button" onclick="toggleSecretVisibility('wa_apikey', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <p class="text-[10px] text-brand-muted mt-1">Your free CallMeBot API authorization key.</p>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200 text-xs text-emerald-950 flex items-start gap-2.5">
                    <i data-lucide="info" class="w-4 h-4 text-emerald-700 shrink-0 mt-0.5"></i>
                    <div class="space-y-1">
                        <span class="font-bold block">How to get your free CallMeBot API Key:</span>
                        <ol class="list-decimal list-inside text-[11px] text-emerald-900 space-y-0.5">
                            <li>Add the phone number <code class="font-mono font-bold bg-white/80 px-1 py-0.5 rounded border border-emerald-300">+34 644 49 20 63</code> to your phone contacts (e.g. "CallMeBot").</li>
                            <li>Open WhatsApp and send this exact text message to CallMeBot: <code class="font-mono font-bold bg-white/80 px-1 py-0.5 rounded border border-emerald-300">I allow callmebot to send me messages</code></li>
                            <li>CallMeBot will reply immediately with your unique <code class="font-mono font-bold bg-white/80 px-1 py-0.5 rounded border border-emerald-300">apikey</code>. Paste it above and click Save.</li>
                        </ol>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" id="btn-save-whatsapp" class="px-5 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span id="btn-save-wa-text">Save WhatsApp Configuration</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($settings as $group => $items)
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-4 space-y-3 shadow-xs">
            <h3 class="font-bold text-sm text-brand-text uppercase tracking-wider text-[11px] text-brand-primary border-b border-gray-100 pb-2">
                {{ ucfirst($group) }} Configuration
            </h3>
            <div class="space-y-2.5">
                @foreach($items as $s)
                <div class="flex items-center justify-between text-xs">
                    <div>
                        <div class="font-semibold text-brand-text">{{ ucwords(str_replace('_', ' ', $s->key)) }}</div>
                        <div class="text-[10px] text-brand-muted">{{ $s->description }}</div>
                    </div>
                    <span class="font-bold text-brand-primary bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $s->value }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- SUB-TAB 4: NOTIFICATION TEMPLATES (ADM-29) -->
    <div id="sys-tab-templates" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($notificationTemplates as $tmpl)
        <div class="bg-brand-surface rounded-xl border border-gray-200/70 p-4 space-y-2.5 shadow-xs">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-sm text-brand-text">{{ $tmpl->name }}</h4>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 uppercase">{{ $tmpl->channel }}</span>
            </div>
            <div class="text-xs text-brand-muted font-mono">Code: {{ $tmpl->code }}</div>
            <div class="p-2 rounded bg-gray-50 text-xs text-brand-text border border-gray-100">
                <span class="font-bold block text-[10px] text-brand-muted mb-0.5">Subject: {{ $tmpl->subject }}</span>
                <p class="text-brand-muted leading-relaxed">{{ $tmpl->body }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- SUB-TAB 5: NOTIFICATION LOGS (ADM-21) -->
    <div id="sys-tab-notifs" class="hidden bg-brand-surface rounded-xl border border-gray-200/70 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-200/60 text-brand-muted uppercase font-semibold text-[10px]">
                    <tr>
                        <th class="px-4 py-3">Event</th>
                        <th class="px-4 py-3">Recipient</th>
                        <th class="px-4 py-3">Channel</th>
                        <th class="px-4 py-3">Delivery Status</th>
                        <th class="px-4 py-3">Sent At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($notificationLogs as $nl)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-brand-text">{{ $nl->event }}</td>
                        <td class="px-4 py-3 text-brand-muted">{{ $nl->recipient }}</td>
                        <td class="px-4 py-3 uppercase font-bold text-[10px] text-brand-primary">{{ $nl->channel }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                {{ ucfirst($nl->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-brand-muted">{{ $nl->created_at->format('d M, H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-brand-muted">No outbound logs recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
