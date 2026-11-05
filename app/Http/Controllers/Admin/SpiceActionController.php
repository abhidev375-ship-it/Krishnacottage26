<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\SpiceInventoryLog;
use App\Models\SpiceOrder;
use App\Models\SpiceProduct;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpiceActionController extends Controller
{
    public function adjustStock(Request $request, $id): JsonResponse
    {
        $product = SpiceProduct::findOrFail($id);

        $request->validate([
            'adjustment_type' => 'required|string',
            'quantity_change' => 'required|integer|not_in:0',
            'reason' => 'required|string',
        ]);

        $change = (int) $request->input('quantity_change');
        $type = $request->input('adjustment_type');
        $reason = $request->input('reason');

        $stockBefore = $product->stock_quantity;
        $stockAfter = max(0, $stockBefore + $change);

        $status = 'in_stock';
        if ($stockAfter <= 0) {
            $status = 'out_of_stock';
        } elseif ($stockAfter <= $product->low_stock_threshold) {
            $status = 'low_stock';
        }

        $product->update([
            'stock_quantity' => $stockAfter,
            'status' => $status,
        ]);

        SpiceInventoryLog::create([
            'spice_product_id' => $product->id,
            'adjustment_type' => $type,
            'quantity_change' => $change,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reason' => $reason,
            'user_id' => auth()->id() ?? 1,
            'created_at' => Carbon::now(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'adjust_spice_stock',
            'entity_type' => 'SpiceProduct',
            'entity_id' => $product->id,
            'previous_values' => ['stock_quantity' => $stockBefore],
            'new_values' => ['stock_quantity' => $stockAfter, 'reason' => $reason],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Stock for {$product->name} adjusted by {$change}. New stock: {$stockAfter}.",
            'product' => $product->fresh(['category']),
        ]);
    }

    public function updateOrderStatus(Request $request, $id): JsonResponse
    {
        $order = SpiceOrder::findOrFail($id);
        $status = $request->input('status');
        $trackingNumber = $request->input('tracking_number');
        $courier = $request->input('shipping_courier');

        $updates = ['status' => $status];
        if ($trackingNumber) {
            $updates['tracking_number'] = $trackingNumber;
        }
        if ($courier) {
            $updates['shipping_courier'] = $courier;
        }
        if ($status === 'shipped' && !$order->shipped_at) {
            $updates['shipped_at'] = Carbon::now();
        }
        if ($status === 'delivered' && !$order->delivered_at) {
            $updates['delivered_at'] = Carbon::now();
        }

        $order->update($updates);

        return response()->json([
            'success' => true,
            'message' => "Spice Order {$order->order_number} updated to {$status}.",
            'order' => $order->fresh(['items']),
        ]);
    }

    public function toggleAvailability(Request $request, $id): JsonResponse
    {
        $product = SpiceProduct::findOrFail($id);
        $newState = !($product->is_active ?? $product->is_available);
        $product->update([
            'is_active' => $newState,
            'is_available' => $newState,
            'is_published' => $newState,
            'status' => $newState ? 'in_stock' : 'discontinued',
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'toggle_spice_availability',
            'entity_type' => 'SpiceProduct',
            'entity_id' => $product->id,
            'previous_values' => ['is_active' => !$newState],
            'new_values' => ['is_active' => $newState],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        $msg = $newState
            ? "Spice product '{$product->name}' is now ACTIVE and visible on client store."
            : "Spice product '{$product->name}' is now INACTIVE and hidden from all client & customer views.";

        return response()->json([
            'success' => true,
            'message' => $msg,
            'is_active' => $newState,
            'is_available' => $newState,
            'product' => $product->fresh(['category']),
        ]);
    }

    public function toggleGlobalReturns(Request $request): JsonResponse
    {
        $currentState = (bool) Setting::get('spice_returns_enabled', true);
        $newState = $request->has('spice_returns_enabled') 
            ? $request->boolean('spice_returns_enabled') 
            : !$currentState;

        Setting::set('spice_returns_enabled', $newState, 'spice_shop', 'Master global toggle to enable or disable return and refund facility across all spices.');

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'toggle_spice_global_returns',
            'entity_type' => 'Setting',
            'entity_id' => 0,
            'previous_values' => ['spice_returns_enabled' => $currentState],
            'new_values' => ['spice_returns_enabled' => $newState],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        $msg = $newState 
            ? "Spice return & refund policy is now globally ENABLED across the estate store." 
            : "Spice return & refund policy is now globally TURNED OFF. Returns facility and guarantee are hidden from client store.";

        return response()->json([
            'success' => true,
            'message' => $msg,
            'spice_returns_enabled' => $newState,
        ]);
    }

    public function toggleProductReturn(Request $request, $id): JsonResponse
    {
        $product = SpiceProduct::findOrFail($id);
        $currentState = (bool) ($product->is_returnable ?? true);
        $newState = $request->has('is_returnable') 
            ? $request->boolean('is_returnable') 
            : !$currentState;

        $product->update([
            'is_returnable' => $newState,
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'toggle_spice_product_return',
            'entity_type' => 'SpiceProduct',
            'entity_id' => $product->id,
            'previous_values' => ['is_returnable' => $currentState],
            'new_values' => ['is_returnable' => $newState],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        $msg = $newState 
            ? "Return policy ENABLED for '{$product->name}'." 
            : "Return policy DISABLED for '{$product->name}'. Card will show 'This product does not have return policies'.";

        return response()->json([
            'success' => true,
            'message' => $msg,
            'is_returnable' => $newState,
            'product' => $product->fresh(['category']),
        ]);
    }
}
