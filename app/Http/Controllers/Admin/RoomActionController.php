<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Room;
use App\Models\RoomBlock;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomActionController extends Controller
{
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        
        $operationalStatus = $request->input('operational_status');
        $housekeepingStatus = $request->input('housekeeping_status');

        $updates = [];
        if ($operationalStatus) {
            $updates['operational_status'] = $operationalStatus;
        }
        if ($housekeepingStatus) {
            $updates['housekeeping_status'] = $housekeepingStatus;
        }

        $room->update($updates);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $room->branch_id,
            'action' => 'update_room_status',
            'entity_type' => 'Room',
            'entity_id' => $room->id,
            'new_values' => $updates,
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Room {$room->room_number} status updated.",
            'room' => $room->fresh(['branch', 'roomType']),
        ]);
    }

    public function blockRoom(Request $request, $id): JsonResponse
    {
        $room = Room::findOrFail($id);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'block_type' => 'required|string',
        ]);

        $block = RoomBlock::create([
            'room_id' => $room->id,
            'branch_id' => $room->branch_id,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'reason' => $request->input('reason'),
            'block_type' => $request->input('block_type', 'maintenance'),
            'created_by' => auth()->id() ?? 1,
        ]);

        $room->update(['operational_status' => 'maintenance']);

        return response()->json([
            'success' => true,
            'message' => "Room {$room->room_number} blocked for {$block->block_type}.",
            'room' => $room->fresh(['branch', 'roomType']),
        ]);
    }
}
