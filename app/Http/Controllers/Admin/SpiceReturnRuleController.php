<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\SpiceOrder;
use App\Models\SpiceReturnRule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpiceReturnRuleController extends Controller
{
    /**
     * Store a new spice return/cancellation policy tier.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'applies_to' => 'required|in:before_dispatch,after_delivery,general',
            'time_limit_hours' => 'required|integer|min:0',
            'refund_percentage' => 'required|numeric|min:0|max:100',
            'handling_fee' => 'nullable|numeric|min:0',
            'description' => 'required|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $rule = SpiceReturnRule::create([
            'name' => $validated['name'],
            'applies_to' => $validated['applies_to'],
            'time_limit_hours' => $validated['time_limit_hours'],
            'refund_percentage' => $validated['refund_percentage'],
            'handling_fee' => $validated['handling_fee'] ?? 0.00,
            'description' => $validated['description'],
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'create_spice_return_rule',
            'entity_type' => 'SpiceReturnRule',
            'entity_id' => $rule->id,
            'previous_values' => [],
            'new_values' => $rule->toArray(),
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Spice return rule '{$rule->name}' created successfully.",
            'rule' => $rule,
        ]);
    }

    /**
     * Update an existing spice return rule tier.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rule = SpiceReturnRule::findOrFail($id);
        $oldValues = $rule->toArray();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'applies_to' => 'sometimes|required|in:before_dispatch,after_delivery,general',
            'time_limit_hours' => 'sometimes|required|integer|min:0',
            'refund_percentage' => 'sometimes|required|numeric|min:0|max:100',
            'handling_fee' => 'nullable|numeric|min:0',
            'description' => 'sometimes|required|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $rule->update([
            'name' => $validated['name'] ?? $rule->name,
            'applies_to' => $validated['applies_to'] ?? $rule->applies_to,
            'time_limit_hours' => $validated['time_limit_hours'] ?? $rule->time_limit_hours,
            'refund_percentage' => $validated['refund_percentage'] ?? $rule->refund_percentage,
            'handling_fee' => array_key_exists('handling_fee', $validated) ? ($validated['handling_fee'] ?? 0.00) : $rule->handling_fee,
            'description' => $validated['description'] ?? $rule->description,
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : $rule->is_active,
            'sort_order' => $validated['sort_order'] ?? $rule->sort_order,
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'update_spice_return_rule',
            'entity_type' => 'SpiceReturnRule',
            'entity_id' => $rule->id,
            'previous_values' => $oldValues,
            'new_values' => $rule->toArray(),
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Spice return rule '{$rule->name}' updated successfully.",
            'rule' => $rule,
        ]);
    }

    /**
     * Delete a spice return rule tier.
     */
    public function destroy($id): JsonResponse
    {
        $rule = SpiceReturnRule::findOrFail($id);
        $old = $rule->toArray();
        $rule->delete();

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'delete_spice_return_rule',
            'entity_type' => 'SpiceReturnRule',
            'entity_id' => $id,
            'previous_values' => $old,
            'new_values' => [],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Spice return rule deleted.",
        ]);
    }

    /**
     * 1-Click active/inactive toggle for a spice return rule tier.
     */
    public function toggleActive($id): JsonResponse
    {
        $rule = SpiceReturnRule::findOrFail($id);
        $rule->is_active = !$rule->is_active;
        $rule->save();

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'toggle_spice_return_rule',
            'entity_type' => 'SpiceReturnRule',
            'entity_id' => $rule->id,
            'previous_values' => ['is_active' => !$rule->is_active],
            'new_values' => ['is_active' => $rule->is_active],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Return rule '{$rule->name}' is now " . ($rule->is_active ? 'ACTIVE' : 'PAUSED') . ".",
            'is_active' => $rule->is_active,
            'rule' => $rule,
        ]);
    }

    /**
     * Update public terms in settings table.
     */
    public function updatePolicyDescription(Request $request): JsonResponse
    {
        $desc = $request->input('description') ?? $request->input('spice_return_policy_description');
        if (empty($desc) || !is_string($desc)) {
            return response()->json(['success' => false, 'message' => 'Policy description is required.'], 422);
        }

        Setting::set(
            'spice_return_policy_description',
            $desc,
            'spice_shop',
            'Public estate spices return and cancellation policy displayed in client store and customer dashboard'
        );

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'update_spice_return_policy_description',
            'entity_type' => 'Setting',
            'entity_id' => 1,
            'previous_values' => [],
            'new_values' => ['policy' => $desc],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Spice return policy terms updated successfully.',
            'description' => $desc,
        ]);
    }

    /**
     * Live calculate refund preview for an order.
     */
    public function calculatePreview(Request $request, $orderId): JsonResponse
    {
        $order = SpiceOrder::with(['items', 'returnRule'])->findOrFail($orderId);
        $requestType = $request->input('request_type'); // 'cancellation' or 'return'
        $calc = SpiceReturnRule::calculateRefund($order, $requestType);

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'refund_status' => $order->refund_status,
                'refund_amount' => $order->refund_amount,
            ],
            'calculation' => $calc,
        ]);
    }

    /**
     * Process order return or cancellation (1-click Approve with refund or Reject).
     */
    public function processOrderReturn(Request $request, $orderId): JsonResponse
    {
        $order = SpiceOrder::with(['items'])->findOrFail($orderId);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'refund_amount' => 'nullable|numeric|min:0',
            'spice_return_rule_id' => 'nullable|exists:spice_return_rules,id',
            'manager_notes' => 'nullable|string|max:1000',
        ]);

        $action = $validated['action'];
        $oldValues = [
            'status' => $order->status,
            'refund_status' => $order->refund_status,
            'refund_amount' => $order->refund_amount,
        ];

        if ($action === 'approve') {
            // If refund amount is not provided, dynamically compute from rule
            $refundAmount = $validated['refund_amount'] ?? null;
            $ruleId = $validated['spice_return_rule_id'] ?? $order->spice_return_rule_id;

            if ($refundAmount === null) {
                $calc = SpiceReturnRule::calculateRefund($order);
                $refundAmount = $calc['cashback'];
                if (!$ruleId && $calc['matched_rule']) {
                    $ruleId = $calc['matched_rule']->id;
                }
            }

            $order->update([
                'refund_status' => 'approved',
                'refund_amount' => $refundAmount,
                'spice_return_rule_id' => $ruleId,
                'return_notes' => $validated['manager_notes'] ?? $order->return_notes,
                'returned_at' => Carbon::now(),
                // If cancelled before dispatch, status becomes cancelled
                'status' => in_array($order->status, ['processing', 'paid', 'packed']) ? 'cancelled' : $order->status,
            ]);

            $msg = "Spice order #{$order->order_number} return approved with ₹" . number_format($refundAmount, 2) . " refund.";
        } else {
            $order->update([
                'refund_status' => 'rejected',
                'return_notes' => $validated['manager_notes'] ?? 'Return request declined after inspection.',
            ]);

            $msg = "Spice order #{$order->order_number} return request has been rejected.";
        }

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => null,
            'action' => 'process_spice_return',
            'entity_type' => 'SpiceOrder',
            'entity_id' => $order->id,
            'previous_values' => $oldValues,
            'new_values' => [
                'refund_status' => $order->refund_status,
                'refund_amount' => $order->refund_amount,
                'action' => $action,
                'manager_notes' => $validated['manager_notes'] ?? null,
            ],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $msg,
            'order' => $order->fresh(['items', 'returnRule']),
        ]);
    }
}
