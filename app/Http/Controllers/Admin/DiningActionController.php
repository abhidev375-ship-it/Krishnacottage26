<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FoodOrder;
use App\Models\MenuItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiningActionController extends Controller
{
    public function toggleStock(Request $request, $id): JsonResponse
    {
        $item = MenuItem::findOrFail($id);
        $state = $request->input('availability_state'); // in_stock, limited_quantity, out_of_stock, hidden
        $qty = $request->input('stock_quantity');

        $previous = $item->availability_state;
        $item->update([
            'availability_state' => $state,
            'stock_quantity' => $qty !== null ? (int)$qty : $item->stock_quantity,
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $item->branch_id,
            'action' => 'update_menu_stock',
            'entity_type' => 'MenuItem',
            'entity_id' => $item->id,
            'previous_values' => ['availability_state' => $previous],
            'new_values' => ['availability_state' => $state, 'stock_quantity' => $item->stock_quantity],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$item->name} availability set to {$state}.",
            'item' => $item->fresh(['category']),
        ]);
    }

    public function updateOrderStatus(Request $request, $id): JsonResponse
    {
        $order = FoodOrder::findOrFail($id);
        $status = $request->input('status'); // new, accepted, preparing, ready, completed, cancelled

        $updates = ['status' => $status];
        if ($status === 'completed') {
            $updates['completed_at'] = Carbon::now();
        }

        $order->update($updates);

        return response()->json([
            'success' => true,
            'message' => "Food order {$order->order_number} status updated to {$status}.",
            'order' => $order->fresh(['items.menuItem', 'branch', 'foodReview']),
        ]);
    }

    public function toggleDailyAvailability(Request $request, $id): JsonResponse
    {
        $item = MenuItem::findOrFail($id);

        if ($request->has('today')) {
            $val = $request->boolean('today');
            $item->update(['is_available_today' => $val]);
            $msg = $val ? "{$item->name} is now active on Today's Menu." : "{$item->name} paused from Today's Menu.";
        } elseif ($request->has('day')) {
            $day = strtolower($request->input('day'));
            $days = $item->available_days ?? ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
            if (in_array($day, $days)) {
                $days = array_values(array_diff($days, [$day]));
                $msg = "{$item->name} disabled for " . ucfirst($day) . ".";
            } else {
                $days[] = $day;
                $msg = "{$item->name} enabled for " . ucfirst($day) . ".";
            }
            $item->update(['available_days' => $days]);
        } else {
            $val = !$item->is_available_today;
            $item->update(['is_available_today' => $val]);
            $msg = $val ? "{$item->name} is active on Today's Menu." : "{$item->name} removed from Today's Menu.";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'item' => $item->fresh(['category', 'branch']),
        ]);
    }

    public function approveFoodReview(Request $request, $id): JsonResponse
    {
        $review = \App\Models\FoodReview::findOrFail($id);
        $review->update([
            'status' => 'approved',
            'approved_by' => auth()->id() ?? 1,
            'approved_at' => Carbon::now(),
        ]);

        if ($review->menuItem) {
            $review->menuItem->recalculateRating();
        }

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $review->branch_id,
            'action' => 'approve_food_review',
            'entity_type' => 'FoodReview',
            'entity_id' => $review->id,
            'previous_values' => ['status' => 'pending'],
            'new_values' => ['status' => 'approved'],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Guest food review #{$review->id} approved and published to public dining menu.",
            'review' => $review->fresh(['order', 'menuItem']),
        ]);
    }

    public function rejectFoodReview(Request $request, $id): JsonResponse
    {
        $review = \App\Models\FoodReview::findOrFail($id);
        $review->update([
            'status' => 'rejected',
            'approved_by' => auth()->id() ?? 1,
            'approved_at' => Carbon::now(),
        ]);

        if ($review->menuItem) {
            $review->menuItem->recalculateRating();
        }

        return response()->json([
            'success' => true,
            'message' => "Guest food review #{$review->id} has been rejected.",
            'review' => $review->fresh(),
        ]);
    }
}
