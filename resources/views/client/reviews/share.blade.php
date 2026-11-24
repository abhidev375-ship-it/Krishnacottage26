@extends('layouts.customer')

@section('title', 'Share Your Experience — Country Side Cottages')
@section('meta_description', 'Share your memories, reviews, photos, and videos from your stay at Country Side Cottages across Munnar, Rajakkad, and Adimali.')

@section('content')
<div class="min-h-screen py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8 sm:mb-12">
            <span class="eyebrow text-brass font-bold uppercase tracking-wider block mb-2">Guest Memories &amp; Stories</span>
            <h1 class="serif text-3xl sm:text-4xl md:text-5xl font-bold text-forest tracking-tight">
                Share Your Experience
            </h1>
            <p class="text-xs sm:text-sm text-forest/70 max-w-xl mx-auto mt-2 leading-relaxed">
                Thank you for staying with us at Country Side Cottages. Tell us about your journey, rate your holiday, and upload your favorite photos and videos.
            </p>
        </div>

        @if(session('review_submitted'))
        <!-- Success Confirmation Card -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 soft-border shadow-card text-center space-y-4 mb-8">
            <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto shadow-inner border border-emerald-200">
                <i data-lucide="check-circle" class="w-8 h-8 text-emerald"></i>
            </div>
            <h2 class="serif text-2xl font-bold text-forest">Thank You, {{ session('guest_name') }}!</h2>
            <p class="text-xs sm:text-sm text-forest/70 max-w-lg mx-auto leading-relaxed">
                Your review and memories have been successfully received. Our resort team will verify your stay details and post your review to our official guest stories wall shortly.
            </p>
            <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-full bg-forest text-paper text-xs font-bold hover:bg-emerald transition shadow-xs">
                    Return to Home
                </a>
                <a href="{{ route('reviews.index') }}" class="px-5 py-2.5 rounded-full bg-mint text-forest text-xs font-bold hover:bg-emerald/20 transition soft-border">
                    Read Guest Reviews
                </a>
            </div>
        </div>
        @else

        <!-- Review Submission Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 soft-border shadow-card">
            @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                    <span>Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-[11px] pl-2 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('reviews.share-experience.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if(!empty($prefill['reservation_id']))
                    <input type="hidden" name="reservation_id" value="{{ $prefill['reservation_id'] }}">
                @endif

                <!-- Section 1: Guest Information -->
                <div class="border-b border-forest/10 pb-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-forest/70 mb-4 flex items-center gap-1.5">
                        <i data-lucide="user-check" class="w-4 h-4 text-emerald"></i>
                        <span>1. Your Information</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-forest mb-1">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="guest_name" required value="{{ old('guest_name', $prefill['name']) }}" placeholder="e.g. Ananya Sharma" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 border border-forest/15 text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-forest mb-1">Email Address (Optional)</label>
                            <input type="email" name="guest_email" value="{{ old('guest_email', $prefill['email']) }}" placeholder="ananya@example.com" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 border border-forest/15 text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-forest mb-1">Phone Number (Optional)</label>
                            <input type="tel" name="guest_phone" value="{{ old('guest_phone', $prefill['phone']) }}" placeholder="+91 98765 43210" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 border border-forest/15 text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-forest mb-1">Booking Reference Code (If known)</label>
                            <input type="text" name="booking_code" value="{{ old('booking_code', $prefill['booking_code']) }}" placeholder="e.g. KR-2026-XXXX" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 border border-forest/15 text-xs text-forest uppercase tracking-wider focus:ring-1 focus:ring-forest focus:outline-hidden">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Destination & Stay Details -->
                <div class="border-b border-forest/10 pb-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-forest/70 mb-4 flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-4 h-4 text-emerald"></i>
                        <span>2. Where Did You Stay?</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-forest mb-1">Resort Branch <span class="text-rose-500">*</span></label>
                            <select name="branch_id" id="review_branch_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 border border-forest/15 text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                                <option value="">Select Branch</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id', $prefill['branch_id']) == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }} ({{ $b->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-forest mb-1">Cottage / Villa Type (Optional)</label>
                            <select name="room_type_id" id="review_room_type_id" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 border border-forest/15 text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                                <option value="">Select Cottage / Suite</option>
                                @foreach($branches as $b)
                                    <optgroup label="{{ $b->name }}">
                                        @foreach($b->roomTypes as $rt)
                                            <option value="{{ $rt->id }}" {{ old('room_type_id', $prefill['room_type_id']) == $rt->id ? 'selected' : '' }}>
                                                {{ $rt->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Rating & Story -->
                <div class="border-b border-forest/10 pb-6 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-forest/70 mb-2 flex items-center gap-1.5">
                        <i data-lucide="star" class="w-4 h-4 text-amber-500"></i>
                        <span>3. Your Rating &amp; Story</span>
                    </h3>

                    <!-- Interactive Star Rating -->
                    <div>
                        <label class="block text-[11px] font-bold text-forest mb-1.5">Overall Experience Rating <span class="text-rose-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1" id="star-rating-container">
                                @for($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating({{ $i }})" onmouseover="highlightStars({{ $i }})" onmouseleave="resetStars()" class="text-2xl sm:text-3xl transition cursor-pointer text-gray-300 hover:scale-110" id="star-btn-{{ $i }}" title="{{ $i }} Star">
                                    ★
                                </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="review-rating-input" value="{{ old('rating', 5) }}">
                            <span id="rating-label" class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/60 ml-2">5 - Exceptional</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-forest mb-1">Headline / Review Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Unforgettable weekend amidst misty tea hills" class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 border border-forest/15 text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-forest mb-1">Your Detailed Experience <span class="text-rose-500">*</span></label>
                        <textarea name="comment" required rows="4" placeholder="Tell other travelers about your stay, hospitality, plantation walks, cottage warmth, food, or sunset views..." class="w-full px-3.5 py-2.5 rounded-xl bg-paper/50 border border-forest/15 text-xs text-forest focus:ring-1 focus:ring-forest focus:outline-hidden">{{ old('comment') }}</textarea>
                    </div>
                </div>

                <!-- Section 4: Photo & Video Upload -->
                <div class="pb-4 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-forest/70 mb-2 flex items-center gap-1.5">
                        <i data-lucide="camera" class="w-4 h-4 text-emerald"></i>
                        <span>4. Upload Photos &amp; Video</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Photos Upload -->
                        <div class="p-4 rounded-2xl bg-paper/40 border border-forest/15 space-y-2">
                            <label class="block text-[11px] font-bold text-forest flex items-center justify-between">
                                <span>Upload Photos (Up to 5)</span>
                                <span class="text-[10px] text-forest/50 font-normal">JPG, PNG, WebP</span>
                            </label>
                            <input type="file" name="photos[]" id="review-photos-input" multiple accept="image/*" class="w-full text-xs text-forest file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-forest file:text-paper hover:file:bg-emerald cursor-pointer">
                            <p class="text-[10px] text-forest/60">Share your cottage views, plantation trails, or dining moments.</p>
                        </div>

                        <!-- Video Upload -->
                        <div class="p-4 rounded-2xl bg-paper/40 border border-forest/15 space-y-2">
                            <label class="block text-[11px] font-bold text-forest flex items-center justify-between">
                                <span>Upload Video or Clip</span>
                                <span class="text-[10px] text-forest/50 font-normal">MP4, MOV, WebM (Max 50MB)</span>
                            </label>
                            <input type="file" name="video" id="review-video-input" accept="video/*" class="w-full text-xs text-forest file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-forest file:text-paper hover:file:bg-emerald cursor-pointer">
                            <input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="Or paste YouTube / video link" class="w-full px-3 py-1.5 rounded-lg bg-white border border-forest/15 text-[11px] text-forest focus:ring-1 focus:ring-forest focus:outline-hidden mt-1">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-forest/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-[11px] text-forest/60 flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald shrink-0"></i>
                        <span>Submitted reviews are reviewed by property staff before appearing on our verified wall.</span>
                    </p>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-forest hover:bg-emerald text-paper font-bold text-xs shadow-card transition flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="send" class="w-3.5 h-3.5 text-brass"></i>
                        <span>Submit Experience</span>
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

<script>
    let selectedRating = parseInt(document.getElementById('review-rating-input')?.value || '5', 10);
    const ratingLabels = {
        5: '5 - Exceptional',
        4: '4 - Very Good',
        3: '3 - Average',
        2: '2 - Needs Improvement',
        1: '1 - Poor Experience'
    };

    function highlightStars(val) {
        for (let i = 1; i <= 5; i++) {
            const btn = document.getElementById(`star-btn-${i}`);
            if (btn) {
                if (i <= val) {
                    btn.classList.add('text-amber-400');
                    btn.classList.remove('text-gray-300');
                } else {
                    btn.classList.add('text-gray-300');
                    btn.classList.remove('text-amber-400');
                }
            }
        }
        const lbl = document.getElementById('rating-label');
        if (lbl) lbl.textContent = ratingLabels[val] || `${val} Stars`;
    }

    function resetStars() {
        highlightStars(selectedRating);
    }

    function setRating(val) {
        selectedRating = val;
        const input = document.getElementById('review-rating-input');
        if (input) input.value = val;
        highlightStars(val);
    }

    document.addEventListener('DOMContentLoaded', function() {
        highlightStars(selectedRating);
    });
</script>
@endsection
