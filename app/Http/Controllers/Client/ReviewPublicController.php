<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReviewPublicController extends Controller
{
    /**
     * Display public review submission form (share-only link).
     * No login required. Prefills data if user or reservation is present.
     */
    public function create(Request $request)
    {
        $branches = Branch::where('status', 'active')
            ->with(['roomTypes' => fn($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $user = Auth::user();
        $prefill = [
            'name' => $user?->name ?? '',
            'email' => $user?->email ?? '',
            'phone' => $user?->phone ?? '',
            'branch_id' => null,
            'room_type_id' => null,
            'booking_code' => '',
            'reservation_id' => null,
        ];

        // If coming with reservation_id
        if ($request->filled('reservation_id')) {
            $res = Reservation::with(['branch', 'roomType', 'guest'])->find($request->query('reservation_id'));
            if ($res) {
                $prefill['reservation_id'] = $res->id;
                $prefill['booking_code'] = $res->booking_code;
                $prefill['branch_id'] = $res->branch_id;
                $prefill['room_type_id'] = $res->room_type_id;
                if ($res->guest && empty($prefill['name'])) {
                    $prefill['name'] = $res->guest->full_name;
                    $prefill['email'] = $res->guest->email;
                    $prefill['phone'] = $res->guest->phone;
                }
            }
        } elseif ($request->filled('branch_id')) {
            $prefill['branch_id'] = (int) $request->query('branch_id');
        }

        return view('client.reviews.share', compact('branches', 'prefill'));
    }

    /**
     * Store submitted guest review with photo & video uploads.
     * Starts in status = 'pending' for Admin approval.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:120',
            'guest_email' => 'nullable|email|max:120',
            'guest_phone' => 'nullable|string|max:30',
            'branch_id' => 'required|exists:branches,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'booking_code' => 'nullable|string|max:50',
            'reservation_id' => 'nullable|exists:reservations,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'comment' => 'required|string|max:3000',
            'photos.*' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:15360',
            'video' => 'nullable|file|mimes:mp4,mov,webm,ogg|max:61440',
            'video_url' => 'nullable|url|max:500',
        ]);

        $uploadedPhotos = [];
        $uploadedVideos = [];

        // 1. Process Photos
        if ($request->hasFile('photos')) {
            $photoDir = public_path('uploads/reviews/photos');
            if (!file_exists($photoDir)) {
                mkdir($photoDir, 0755, true);
            }
            foreach ($request->file('photos') as $file) {
                if ($file && $file->isValid()) {
                    $filename = 'rev_photo_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                    $file->move($photoDir, $filename);
                    $uploadedPhotos[] = '/uploads/reviews/photos/' . $filename;
                }
            }
        }

        // 2. Process Video File
        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            if ($videoFile && $videoFile->isValid()) {
                $videoDir = public_path('uploads/reviews/videos');
                if (!file_exists($videoDir)) {
                    mkdir($videoDir, 0755, true);
                }
                $filename = 'rev_video_' . time() . '_' . Str::random(8) . '.' . $videoFile->getClientOriginalExtension();
                $videoFile->move($videoDir, $filename);
                $uploadedVideos[] = '/uploads/reviews/videos/' . $filename;
            }
        }

        // Direct Video URL if provided
        if (!empty($validated['video_url'])) {
            $uploadedVideos[] = trim($validated['video_url']);
        }

        // 3. Resolve Reservation & Verification
        $reservationId = null;
        $guestId = null;
        $roomTypeId = $validated['room_type_id'] ?? null;
        $verifiedStay = false;
        $staySummary = null;

        if (!empty($validated['reservation_id'])) {
            $res = Reservation::find($validated['reservation_id']);
            if ($res) {
                $reservationId = $res->id;
                $guestId = $res->guest_id;
                $roomTypeId = $res->room_type_id ?: $roomTypeId;
                $verifiedStay = true;
                $staySummary = ($res->nights ?? 2) . ' nights · ' . ($res->roomType ? $res->roomType->name : 'Resort Villa');
            }
        } elseif (!empty($validated['booking_code'])) {
            $res = Reservation::where('booking_code', trim($validated['booking_code']))->first();
            if ($res) {
                $reservationId = $res->id;
                $guestId = $res->guest_id;
                $roomTypeId = $res->room_type_id ?: $roomTypeId;
                $verifiedStay = true;
                $staySummary = ($res->nights ?? 2) . ' nights · ' . ($res->roomType ? $res->roomType->name : 'Resort Villa');
            }
        }

        if (!$staySummary && $roomTypeId) {
            $rt = RoomType::find($roomTypeId);
            if ($rt) {
                $staySummary = $rt->name;
            }
        }

        // If authenticated user
        if (Auth::check()) {
            $user = Auth::user();
            if (!$guestId && $user->guest) {
                $guestId = $user->guest->id;
            }
        }

        // Prevent duplicate review if linked to a specific reservation
        if ($reservationId && Review::where('reservation_id', $reservationId)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A review for this reservation booking has already been submitted.');
        }

        // Create Review in status = 'pending'
        $review = Review::create([
            'reservation_id' => $reservationId,
            'branch_id' => $validated['branch_id'],
            'room_type_id' => $roomTypeId,
            'guest_id' => $guestId,
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'] ?? null,
            'guest_phone' => $validated['guest_phone'] ?? null,
            'rating' => (int) $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => trim($validated['comment']),
            'stay_summary' => $staySummary,
            'photos' => !empty($uploadedPhotos) ? $uploadedPhotos : null,
            'videos' => !empty($uploadedVideos) ? $uploadedVideos : null,
            'status' => 'pending',
            'verified_stay' => $verifiedStay,
            'is_featured' => false,
            'is_pinned' => false,
        ]);

        return redirect()->route('reviews.share-experience')
            ->with('review_submitted', true)
            ->with('guest_name', $validated['guest_name']);
    }

    /**
     * Public Google-style reviews showcase page.
     * Displays rating breakdown, dynamic branch filters, media previews, and verified reviews.
     * Strictly share/view only (does NOT display public add button).
     */
    public function index(Request $request)
    {
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $selectedBranchId = $request->query('branch_id');
        $selectedRating = $request->query('rating');
        $hasMediaOnly = $request->boolean('has_media');

        // Query base approved reviews
        $baseQuery = Review::with(['branch', 'roomType', 'guest'])
            ->where('status', 'approved');

        // Statistics query
        $allApproved = (clone $baseQuery)->get();
        $totalReviews = $allApproved->count();
        $averageRating = $totalReviews > 0 ? round($allApproved->avg('rating'), 1) : 4.9;

        // Rating breakdown (5, 4, 3, 2, 1 stars)
        $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($allApproved as $rev) {
            $r = (int) $rev->rating;
            if (isset($ratingCounts[$r])) {
                $ratingCounts[$r]++;
            }
        }

        // Filtered listing query
        $query = (clone $baseQuery);

        if ($selectedBranchId) {
            $query->where('branch_id', (int) $selectedBranchId);
        }

        if ($selectedRating) {
            $query->where('rating', (int) $selectedRating);
        }

        if ($hasMediaOnly) {
            $query->where(function ($q) {
                $q->whereNotNull('photos')
                  ->orWhereNotNull('videos');
            });
        }

        $reviews = $query->latest('created_at')->paginate(12)->withQueryString();

        return view('client.reviews.index', compact(
            'branches',
            'reviews',
            'totalReviews',
            'averageRating',
            'ratingCounts',
            'selectedBranchId',
            'selectedRating',
            'hasMediaOnly'
        ));
    }
}
