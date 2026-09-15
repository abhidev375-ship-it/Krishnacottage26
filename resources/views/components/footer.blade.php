@php
    $content = $homepageContent ?? app(\App\Http\Controllers\Admin\AdminController::class)->getHomepageContent();
    $fBrandTitle = $content['footer_brand_title'] ?? 'Krishna Resorts';
    $fBrandTagline = $content['footer_brand_tagline'] ?? 'Luxury Cottages of Kerala';
    $fBrandDesc = $content['footer_brand_desc'] ?? 'Immersive, slow-living cottages nestled in the spice hills, misty valleys, and tranquil backwaters of Kerala.';
    $fBadge = $content['footer_badge'] ?? 'Eco-Conscious Botanical Cottages';
    $fDestTitle = $content['footer_dest_title'] ?? 'Destinations';
    $fExpTitle = $content['footer_exp_title'] ?? 'Experiences';
    $fExpLink1 = $content['footer_exp_link_1'] ?? 'Plantation Kitchen & Dining';
    $fExpLink2 = $content['footer_exp_link_2'] ?? 'Krishna Spices Farm Shop';
    $fExpLink3 = $content['footer_exp_link_3'] ?? 'Resort Visual Gallery';
    $fExpLink4 = $content['footer_exp_link_4'] ?? 'Guided Nature Discoveries';
    $fConciergeTitle = $content['footer_concierge_title'] ?? 'Direct Concierge';
    $fConciergeDesc = $content['footer_concierge_desc'] ?? 'Front desk assistance 24/7 for bespoke retreat arrangements.';
    $fPhone = $content['footer_phone'] ?? '+91 484 290 0000';
    $fEmail = $content['footer_email'] ?? 'concierge@krishnaresorts.com';
    $fChatBtn = $content['footer_chat_btn'] ?? 'Chat with Concierge';
    $fCopyright = $content['footer_copyright'] ?? 'Krishna Resorts Hospitality Ltd. All rights reserved.';
    $fSupportLink = $content['footer_support_link'] ?? 'Help & Support';
    $fPolicyNote = $content['footer_policy_note'] ?? '🌿 Strictly No Swimming Pool Policy';

    $footerBranches = \App\Models\Branch::where('status', 'active')->orderBy('sort_order')->take(4)->get();
    $cleanPhone = preg_replace('/[^\d+]/', '', $fPhone);
@endphp

<!-- LUXURY EDITORIAL SHARED FOOTER (DYNAMIC PPT VISUAL EDITOR POWERED) -->
<footer {{ ($attributes ?? new \Illuminate\View\ComponentAttributeBag)->merge(['class' => 'bg-forest text-paper border-t border-forest/10 mt-12 md:mt-20']) }}>
    <div class="mx-auto max-w-[1480px] px-4 py-14 md:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
            <!-- 1. Brand Identity & Bio -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-paper text-sm font-bold text-forest">K</span>
                    <div>
                        <span class="serif text-xl font-bold block text-paper tracking-tight">{{ $fBrandTitle }}</span>
                        <span class="text-[9px] uppercase tracking-[.25em] text-brass font-bold block">{{ $fBrandTagline }}</span>
                    </div>
                </div>
                <p class="text-xs text-paper/70 leading-relaxed max-w-sm">
                    {{ $fBrandDesc }}
                </p>
                <div class="flex items-center gap-2 text-xs text-brass font-semibold">
                    <i data-lucide="shield-check" class="w-4 h-4 shrink-0"></i>
                    <span>{{ $fBadge }}</span>
                </div>
            </div>

            <!-- 2. Destinations -->
            <div>
                <h4 class="eyebrow text-brass mb-4">{{ $fDestTitle }}</h4>
                <ul class="space-y-2 text-xs text-paper/75">
                    @forelse($footerBranches as $b)
                        <li>
                            <a href="{{ route('rooms.index', ['branch_id' => $b->id]) }}" class="hover:text-brass transition flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-3 h-3 text-brass/70"></i>
                                <span>{{ $b->display_name ?: ($b->name . ' (' . $b->city . ')') }}</span>
                            </a>
                        </li>
                    @empty
                        <li><a href="{{ route('rooms.index') }}" class="hover:text-brass transition">Munnar High Forest</a></li>
                        <li><a href="{{ route('rooms.index') }}" class="hover:text-brass transition">Wayanad Valley Reserve</a></li>
                        <li><a href="{{ route('rooms.index') }}" class="hover:text-brass transition">Kumarakom Waterside</a></li>
                    @endforelse
                    <li><a href="{{ route('facilities.index') }}" class="hover:text-brass transition">Ayurvedic Wellness Pavilion</a></li>
                </ul>
            </div>

            <!-- 3. Curated Experiences -->
            <div>
                <h4 class="eyebrow text-brass mb-4">{{ $fExpTitle }}</h4>
                <ul class="space-y-2 text-xs text-paper/75">
                    <li><a href="{{ route('dining.index') }}" class="hover:text-brass transition">{{ $fExpLink1 }}</a></li>
                    <li><a href="{{ route('spices.index') }}" class="hover:text-brass transition">{{ $fExpLink2 }}</a></li>
                    <li><a href="{{ route('gallery.index') }}" class="hover:text-brass transition">{{ $fExpLink3 }}</a></li>
                    <li><a href="{{ route('nearby.index') }}" class="hover:text-brass transition">{{ $fExpLink4 }}</a></li>
                </ul>
            </div>

            <!-- 4. Direct Concierge -->
            <div>
                <h4 class="eyebrow text-brass mb-4">{{ $fConciergeTitle }}</h4>
                <p class="text-xs text-paper/70 mb-3">{{ $fConciergeDesc }}</p>
                <div class="space-y-2 text-xs">
                    <a href="tel:{{ $cleanPhone }}" class="flex items-center gap-2 text-brass hover:underline">
                        <i data-lucide="phone" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>{{ $fPhone }}</span>
                    </a>
                    <a href="mailto:{{ $fEmail }}" class="flex items-center gap-2 text-paper/75 hover:underline">
                        <i data-lucide="mail" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>{{ $fEmail }}</span>
                    </a>
                </div>
                <button type="button" onclick="openChat()" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brass px-3.5 py-2 text-[10px] font-bold text-forest hover:brightness-105 transition cursor-pointer">
                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                    <span>{{ $fChatBtn }}</span>
                </button>
            </div>
        </div>

        <!-- Bottom Copyright & Legal Links -->
        <div class="border-t border-paper/10 mt-12 pt-6 flex flex-col md:flex-row items-center justify-between text-[11px] text-paper/50 gap-4">
            <p>&copy; {{ date('Y') }} {{ $fCopyright }}</p>
            <div class="flex items-center gap-6">
                <a href="{{ route('contact.index') }}" class="hover:text-paper transition">{{ $fSupportLink }}</a>
                <span class="text-brass/70">{{ $fPolicyNote }}</span>
            </div>
        </div>
    </div>
</footer>
