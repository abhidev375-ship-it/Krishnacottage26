<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CancellationRule;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CancellationRuleController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'hours_before_checkin' => 'required|integer|min:0',
            'refund_percentage' => 'required|numeric|min:0|max:100',
            'description' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $rule = CancellationRule::create([
            'branch_id' => $validated['branch_id'] ?? null,
            'hours_before_checkin' => $validated['hours_before_checkin'],
            'refund_percentage' => $validated['refund_percentage'],
            'description' => $validated['description'],
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $rule->branch_id,
            'action' => 'create_cancellation_rule',
            'entity_type' => 'CancellationRule',
            'entity_id' => $rule->id,
            'previous_values' => [],
            'new_values' => $rule->toArray(),
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Cancellation rule created successfully ({$rule->hours_before_checkin}h -> {$rule->refund_percentage}% cashback).",
            'rule' => $rule,
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $rule = CancellationRule::findOrFail($id);
        $oldValues = $rule->toArray();

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'hours_before_checkin' => 'sometimes|required|integer|min:0',
            'refund_percentage' => 'sometimes|required|numeric|min:0|max:100',
            'description' => 'sometimes|required|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $rule->update([
            'branch_id' => array_key_exists('branch_id', $validated) ? $validated['branch_id'] : $rule->branch_id,
            'hours_before_checkin' => $validated['hours_before_checkin'] ?? $rule->hours_before_checkin,
            'refund_percentage' => $validated['refund_percentage'] ?? $rule->refund_percentage,
            'description' => $validated['description'] ?? $rule->description,
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : $rule->is_active,
            'sort_order' => $validated['sort_order'] ?? $rule->sort_order,
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $rule->branch_id,
            'action' => 'update_cancellation_rule',
            'entity_type' => 'CancellationRule',
            'entity_id' => $rule->id,
            'previous_values' => $oldValues,
            'new_values' => $rule->toArray(),
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Cancellation rule #{$rule->id} updated successfully.",
            'rule' => $rule,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $rule = CancellationRule::findOrFail($id);
        $old = $rule->toArray();
        $rule->delete();

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $rule->branch_id,
            'action' => 'delete_cancellation_rule',
            'entity_type' => 'CancellationRule',
            'entity_id' => $id,
            'previous_values' => $old,
            'new_values' => [],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Cancellation rule tier deleted.",
        ]);
    }

    public function updatePolicyDescription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'required|string|max:3000',
        ]);

        Setting::set(
            'cancellation_policy_description',
            $validated['description'],
            'cancellation',
            'Public resort cancellation and automated cashback policy terms'
        );

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'action' => 'update_cancellation_policy',
            'entity_type' => 'Setting',
            'entity_id' => 1,
            'previous_values' => [],
            'new_values' => ['policy' => $validated['description']],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resort cancellation policy description updated successfully.',
            'description' => $validated['description'],
        ]);
    }
}
