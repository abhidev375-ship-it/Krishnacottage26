<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\SpiceProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Search Reservations
        Reservation::with(['guest', 'branch'])
            ->where(function ($query) use ($q) {
                $query->where('booking_code', 'like', "%{$q}%")
                    ->orWhereHas('guest', fn($gq) => $gq->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%"));
            })
            ->limit(5)
            ->get()
            ->each(function ($r) use (&$results) {
                $results[] = [
                    'type' => 'reservation',
                    'icon' => 'calendar-check',
                    'title' => $r->booking_code,
                    'subtitle' => ($r->guest ? $r->guest->full_name : 'Unknown') . ' · ' . ($r->branch ? $r->branch->name : ''),
                    'badge' => $r->status,
                    'section' => 'reservations',
                    'id' => $r->id,
                ];
            });

        // Search Guests
        Guest::where(function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->each(function ($g) use (&$results) {
                $results[] = [
                    'type' => 'guest',
                    'icon' => 'user',
                    'title' => $g->full_name,
                    'subtitle' => $g->email . ($g->phone ? " · {$g->phone}" : ''),
                    'badge' => $g->vip_level,
                    'section' => 'guests',
                    'id' => $g->id,
                ];
            });

        // Search Rooms
        Room::with(['branch', 'roomType'])
            ->where('room_number', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->each(function ($r) use (&$results) {
                $results[] = [
                    'type' => 'room',
                    'icon' => 'bed-double',
                    'title' => "Room {$r->room_number}",
                    'subtitle' => ($r->roomType ? $r->roomType->name : '') . ' · ' . ($r->branch ? $r->branch->name : ''),
                    'badge' => $r->operational_status,
                    'section' => 'rooms',
                    'id' => $r->id,
                ];
            });

        // Search Menu Items
        MenuItem::with('branch')
            ->where('name', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->each(function ($m) use (&$results) {
                $results[] = [
                    'type' => 'menu_item',
                    'icon' => 'utensils',
                    'title' => $m->name,
                    'subtitle' => '₹' . number_format($m->price) . ' · ' . ($m->branch ? $m->branch->name : ''),
                    'badge' => $m->availability_state,
                    'section' => 'dining',
                    'id' => $m->id,
                ];
            });

        // Search Spice Products
        SpiceProduct::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->each(function ($p) use (&$results) {
                $results[] = [
                    'type' => 'spice_product',
                    'icon' => 'leaf',
                    'title' => $p->name,
                    'subtitle' => $p->sku . ' · ₹' . number_format($p->price),
                    'badge' => $p->status,
                    'section' => 'spices',
                    'id' => $p->id,
                ];
            });

        return response()->json(['results' => $results]);
    }
}
