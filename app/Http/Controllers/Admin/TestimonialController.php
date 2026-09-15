<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FoodReview;
use App\Models\Review;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    /**
     * Store a new testimonial.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:150',
            'stay_title' => 'nullable|string|max:150',
            'quote' => 'required|string|max:1500',
            'rating' => 'nullable|integer|min:1|max:5',
            'avatar_url' => 'nullable|string|max:500',
            'branch_id' => 'nullable|exists:branches,id',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable',
        ]);

        $sortOrder = $validated['sort_order'] ?? ((Testimonial::max('sort_order') ?? 0) + 1);
        $isActive = $request->has('is_active') ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : true;

        $testimonial = Testimonial::create([
            'guest_name' => $validated['guest_name'],
            'stay_title' => $validated['stay_title'] ?? 'Completed Stay',
            'quote' => $validated['quote'],
            'rating' => $validated['rating'] ?? 5,
            'avatar_url' => $validated['avatar_url'] ?? null,
            'branch_id' => !empty($validated['branch_id']) ? $validated['branch_id'] : null,
            'sort_order' => $sortOrder,
            'is_active' => $isActive,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'user_name' => Auth::user()->name ?? 'System Admin',
            'role' => Auth::user()->role ?? 'super_admin',
            'branch_id' => $testimonial->branch_id,
            'action' => 'create_testimonial',
            'entity_type' => 'Testimonial',
            'entity_id' => $testimonial->id,
            'new_values' => $testimonial->toArray(),
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial added to carousel successfully!',
            'testimonial' => $testimonial->load('branch'),
        ]);
    }

    /**
     * Update an existing testimonial.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'guest_name' => 'required|string|max:150',
            'stay_title' => 'nullable|string|max:150',
            'quote' => 'required|string|max:1500',
            'rating' => 'nullable|integer|min:1|max:5',
            'avatar_url' => 'nullable|string|max:500',
            'branch_id' => 'nullable',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable',
        ]);

        $isActive = $request->has('is_active') ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : $testimonial->is_active;

        $testimonial->update([
            'guest_name' => $validated['guest_name'],
            'stay_title' => $validated['stay_title'] ?? $testimonial->stay_title,
            'quote' => $validated['quote'],
            'rating' => $validated['rating'] ?? $testimonial->rating,
            'avatar_url' => $validated['avatar_url'] ?? $testimonial->avatar_url,
            'branch_id' => !empty($validated['branch_id']) ? $validated['branch_id'] : null,
            'sort_order' => isset($validated['sort_order']) ? (int) $validated['sort_order'] : $testimonial->sort_order,
            'is_active' => $isActive,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'user_name' => Auth::user()->name ?? 'System Admin',
            'role' => Auth::user()->role ?? 'super_admin',
            'branch_id' => $testimonial->branch_id,
            'action' => 'update_testimonial',
            'entity_type' => 'Testimonial',
            'entity_id' => $testimonial->id,
            'new_values' => $testimonial->toArray(),
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial updated successfully!',
            'testimonial' => $testimonial->fresh(['branch']),
        ]);
    }

    /**
     * Delete a testimonial.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'user_name' => Auth::user()->name ?? 'System Admin',
            'role' => Auth::user()->role ?? 'super_admin',
            'branch_id' => $testimonial->branch_id,
            'action' => 'delete_testimonial',
            'entity_type' => 'Testimonial',
            'entity_id' => $id,
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully!',
        ]);
    }

    /**
     * Toggle active visibility of a testimonial in the welcome carousel.
     */
    public function toggle(Request $request, int $id): JsonResponse
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->is_active = !$testimonial->is_active;
        $testimonial->save();

        $statusLabel = $testimonial->is_active ? 'visible on welcome carousel' : 'hidden from welcome carousel';

        return response()->json([
            'success' => true,
            'message' => "Testimonial is now {$statusLabel}.",
            'is_active' => $testimonial->is_active,
        ]);
    }

    /**
     * Reorder testimonials display order.
     */
    public function reorder(Request $request): JsonResponse
    {
        // Support array of orders: [{ id: 1, sort_order: 1 }, ...]
        if ($request->has('orders') && is_array($request->orders)) {
            foreach ($request->orders as $item) {
                if (isset($item['id']) && isset($item['sort_order'])) {
                    Testimonial::where('id', $item['id'])->update(['sort_order' => (int) $item['sort_order']]);
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Testimonial display orders updated successfully.',
            ]);
        }

        // Support single shift: id + direction ('up' or 'down')
        if ($request->has('id') && $request->has('direction')) {
            $current = Testimonial::findOrFail($request->id);
            $direction = $request->direction;

            if ($direction === 'up') {
                $swapTarget = Testimonial::where('sort_order', '<', $current->sort_order)
                    ->orderByDesc('sort_order')
                    ->first();
            } else {
                $swapTarget = Testimonial::where('sort_order', '>', $current->sort_order)
                    ->orderBy('sort_order')
                    ->first();
            }

            if ($swapTarget) {
                $currentOrder = $current->sort_order;
                $targetOrder = $swapTarget->sort_order;

                if ($currentOrder === $targetOrder) {
                    $targetOrder = $direction === 'up' ? max(1, $currentOrder - 1) : $currentOrder + 1;
                }

                $current->update(['sort_order' => $targetOrder]);
                $swapTarget->update(['sort_order' => $currentOrder]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order moved ' . $direction . ' successfully.',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Already at ' . ($direction === 'up' ? 'top' : 'bottom') . ' of sequence.',
            ]);
        }

        // Support single ID sort_order update: id + sort_order
        if ($request->has('id') && $request->has('sort_order')) {
            $testimonial = Testimonial::findOrFail($request->id);
            $testimonial->update(['sort_order' => (int) $request->sort_order]);

            return response()->json([
                'success' => true,
                'message' => "Order for '{$testimonial->guest_name}' updated to {$testimonial->sort_order}.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid reorder parameters provided.',
        ], 422);
    }

    /**
     * Promote an approved guest review to the welcome page testimonial carousel.
     */
    public function promoteFromReview(Request $request, int $id): JsonResponse
    {
        $type = $request->input('type', 'stay'); // 'stay' or 'food'

        if ($type === 'food') {
            $foodReview = FoodReview::with(['order.guest', 'menuItem', 'branch'])->findOrFail($id);
            if ($foodReview->status !== 'approved') {
                $foodReview->update(['status' => 'approved', 'approved_by' => Auth::id() ?? 1, 'approved_at' => Carbon::now()]);
            }

            $guestName = $foodReview->order->guest->full_name ?? $foodReview->guest_name ?? 'Delighted Food Lover';
            $stayTitle = ($foodReview->menuItem->name ?? 'Dining Experience') . ' · ' . ($foodReview->branch->name ?? 'Krishna Dining');
            $quote = $foodReview->comment ?? 'Exceptional flavors and warm hospitality.';
            $rating = $foodReview->rating ?? 5;
            $branchId = $foodReview->branch_id;
            $reviewId = null;
        } else {
            $review = Review::with(['guest', 'roomType', 'branch'])->findOrFail($id);
            if ($review->status !== 'approved') {
                $review->update(['status' => 'approved']);
            }

            $guestName = $review->guest->full_name ?? $review->guest_name ?? 'Delighted Guest';
            $stayTitle = $review->title ?: (($review->roomType->name ?? 'Resort Stay') . ' · ' . ($review->branch->name ?? 'Krishna Cottage'));
            $quote = $review->comment;
            $rating = $review->rating ?? 5;
            $branchId = $review->branch_id;
            $reviewId = $review->id;
        }

        // Check if already promoted
        $existing = Testimonial::where('review_id', $reviewId)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'This review is already in the Welcome Carousel (# ' . $existing->id . ').',
                'testimonial' => $existing,
            ]);
        }

        $nextOrder = (Testimonial::max('sort_order') ?? 0) + 1;

        $testimonial = Testimonial::create([
            'guest_name' => $guestName,
            'stay_title' => $stayTitle,
            'quote' => $quote,
            'rating' => $rating,
            'branch_id' => $branchId,
            'review_id' => $reviewId,
            'sort_order' => $nextOrder,
            'is_active' => true,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'user_name' => Auth::user()->name ?? 'System Admin',
            'role' => Auth::user()->role ?? 'super_admin',
            'branch_id' => $branchId,
            'action' => 'promote_review_to_testimonial',
            'entity_type' => 'Testimonial',
            'entity_id' => $testimonial->id,
            'new_values' => $testimonial->toArray(),
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Review promoted successfully! It is now live on the welcome page carousel (Order #{$nextOrder}).",
            'testimonial' => $testimonial->load('branch'),
        ]);
    }
}
