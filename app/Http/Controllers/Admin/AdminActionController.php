<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\FolioCharge;
use App\Models\GalleryAlbum;
use App\Models\GalleryImage;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\NavigationItem;
use App\Models\NearbyLocation;
use App\Models\Page;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\SpiceProduct;
use App\Models\TaxiRequest;
use App\Models\User;
use App\Services\RazorpayService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminActionController extends Controller
{
    private function logAudit(string $action, string $entityType, int $entityId, ?int $branchId = null, array $prev = [], array $new = []): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'user_name' => auth()->user()->name ?? 'Administrator',
                'role' => auth()->user()->role ?? 'super_admin',
                'branch_id' => $branchId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'previous_values' => $prev,
                'new_values' => $new,
                'ip_address' => request()->ip(),
                'created_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            // Non-blocking audit failure
        }
    }

    // ==========================================
    // 1. BRANCHES (ADM-09)
    // ==========================================
    public function storeBranch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'code' => 'required|string|max:20|unique:branches,code',
            'address' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'tagline' => 'nullable|string|max:255',
            'hero_image_url' => 'nullable|string',
            'cover_image_url' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_published'] = $request->boolean('is_published', true);
        $branch = Branch::create($validated);

        $this->logAudit('create', 'Branch', $branch->id, $branch->id, [], $branch->toArray());

        return response()->json([
            'success' => true,
            'message' => "Branch '{$branch->name}' successfully created.",
            'branch' => $branch,
        ]);
    }

    public function updateBranch(Request $request, int $id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'tagline' => 'nullable|string|max:255',
            'hero_image_url' => 'nullable|string',
            'cover_image_url' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $prev = $branch->toArray();
        $branch->update($validated);
        $this->logAudit('update', 'Branch', $branch->id, $branch->id, $prev, $branch->toArray());

        return response()->json([
            'success' => true,
            'message' => "Branch '{$branch->name}' updated successfully.",
            'branch' => $branch,
        ]);
    }

    public function toggleBranchStatus(int $id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        $newStatus = $branch->status === 'active' ? 'inactive' : 'active';
        $prev = ['status' => $branch->status];
        $branch->update(['status' => $newStatus]);
        $this->logAudit('toggle_status', 'Branch', $branch->id, $branch->id, $prev, ['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Branch {$branch->name} status set to {$newStatus}.",
            'status' => $newStatus,
        ]);
    }

    // ==========================================
    // 2. ROOM TYPES (ADM-05)
    // ==========================================
    public function storeRoomType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_category_id' => 'nullable|exists:room_categories,id',
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'max_guests' => 'nullable|integer|min:1',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'size_sqft' => 'nullable|integer|min:50',
            'bed_type' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'amenity_ids' => 'nullable|array',
            'short_description' => 'nullable|string|max:300',
            'description' => 'nullable|string',
            'cover_image_url' => 'nullable|string',
            'gallery_images' => 'nullable',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['code'] = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 4)) . rand(10, 99);
        $validated['max_guests'] = !empty($validated['max_guests']) ? (int)$validated['max_guests'] : (($validated['max_adults'] ?? 2) + ($validated['max_children'] ?? 0));
        $validated['weekend_price'] = $validated['weekend_price'] ?? ($validated['base_price'] * 1.15);
        $validated['is_active'] = true;
        $validated['is_bookable'] = true;

        if (!empty($validated['gallery_images']) && is_string($validated['gallery_images'])) {
            $urls = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $validated['gallery_images'])));
            $validated['gallery_images'] = array_values($urls);
        }

        $amenityIds = $validated['amenity_ids'] ?? [];
        if (!empty($amenityIds)) {
            $amenityNames = Amenity::whereIn('id', $amenityIds)->pluck('name')->toArray();
            $validated['amenities'] = $amenityNames;
        }

        $roomType = RoomType::create($validated);
        if (!empty($amenityIds)) {
            $roomType->amenitiesList()->sync($amenityIds);
        }

        $this->logAudit('create', 'RoomType', $roomType->id, $roomType->branch_id, [], $roomType->toArray());

        return response()->json([
            'success' => true,
            'message' => "Room Type '{$roomType->name}' created successfully.",
            'roomType' => $roomType->load(['branch', 'category', 'amenitiesList']),
        ]);
    }

    public function updateRoomType(Request $request, int $id): JsonResponse
    {
        $roomType = RoomType::findOrFail($id);
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_category_id' => 'nullable|exists:room_categories,id',
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'max_guests' => 'nullable|integer|min:1',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'size_sqft' => 'nullable|integer|min:50',
            'bed_type' => 'nullable|string|max:100',
            'amenities' => 'nullable|array',
            'amenity_ids' => 'nullable|array',
            'short_description' => 'nullable|string|max:300',
            'description' => 'nullable|string',
            'cover_image_url' => 'nullable|string',
            'gallery_images' => 'nullable',
        ]);

        $validated['max_guests'] = !empty($validated['max_guests']) ? (int)$validated['max_guests'] : (($validated['max_adults'] ?? 2) + ($validated['max_children'] ?? 0));

        if (isset($validated['gallery_images']) && is_string($validated['gallery_images'])) {
            $urls = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $validated['gallery_images'])));
            $validated['gallery_images'] = array_values($urls);
        }

        $amenityIds = $validated['amenity_ids'] ?? null;
        if (is_array($amenityIds)) {
            $amenityNames = Amenity::whereIn('id', $amenityIds)->pluck('name')->toArray();
            $validated['amenities'] = $amenityNames;
            $roomType->amenitiesList()->sync($amenityIds);
        }

        $prev = $roomType->toArray();
        $roomType->update($validated);
        $this->logAudit('update', 'RoomType', $roomType->id, $roomType->branch_id, $prev, $roomType->toArray());

        return response()->json([
            'success' => true,
            'message' => "Room Type '{$roomType->name}' updated successfully.",
            'roomType' => $roomType->load(['branch', 'category', 'amenitiesList']),
        ]);
    }

    // ==========================================
    // 2B. ROOM CATEGORIES (ADM-05)
    // ==========================================
    public function storeRoomCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        if (RoomCategory::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-' . Str::random(4);
        }
        $validated['icon'] = $validated['icon'] ?: 'palmtree';
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? (RoomCategory::max('sort_order') + 1);

        $category = RoomCategory::create($validated);
        $this->logAudit('create', 'RoomCategory', $category->id, null, [], $category->toArray());

        return response()->json([
            'success' => true,
            'message' => "Room Category '{$category->name}' created successfully.",
            'category' => $category,
        ]);
    }

    public function updateRoomCategory(Request $request, int $id): JsonResponse
    {
        $category = RoomCategory::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if (!empty($validated['name']) && $validated['name'] !== $category->name) {
            $newSlug = Str::slug($validated['name']);
            if (RoomCategory::where('slug', $newSlug)->where('id', '!=', $category->id)->exists()) {
                $newSlug .= '-' . Str::random(4);
            }
            $validated['slug'] = $newSlug;
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $prev = $category->toArray();
        $category->update($validated);
        $this->logAudit('update', 'RoomCategory', $category->id, null, $prev, $category->toArray());

        return response()->json([
            'success' => true,
            'message' => "Room Category '{$category->name}' updated successfully.",
            'category' => $category,
        ]);
    }

    public function deleteRoomCategory(int $id): JsonResponse
    {
        $category = RoomCategory::findOrFail($id);
        $name = $category->name;
        $prev = $category->toArray();
        $category->delete();
        $this->logAudit('delete', 'RoomCategory', $id, null, $prev, []);

        return response()->json([
            'success' => true,
            'message' => "Room Category '{$name}' removed successfully.",
        ]);
    }

    // ==========================================
    // 2C. AMENITIES LIBRARY (ADM-05)
    // ==========================================
    public function storeAmenity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'category' => 'required|string|max:60',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        if (Amenity::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-' . Str::random(4);
        }
        $validated['icon'] = $validated['icon'] ?: 'sparkles';
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_active'] = true;
        $validated['sort_order'] = $validated['sort_order'] ?? (Amenity::max('sort_order') + 1);

        $amenity = Amenity::create($validated);
        $this->logAudit('create', 'Amenity', $amenity->id, null, [], $amenity->toArray());

        return response()->json([
            'success' => true,
            'message' => "Amenity '{$amenity->name}' added to library.",
            'amenity' => $amenity,
        ]);
    }

    public function updateAmenity(Request $request, int $id): JsonResponse
    {
        $amenity = Amenity::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'category' => 'required|string|max:60',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured', false);
        $prev = $amenity->toArray();
        $amenity->update($validated);
        $this->logAudit('update', 'Amenity', $amenity->id, null, $prev, $amenity->toArray());

        return response()->json([
            'success' => true,
            'message' => "Amenity '{$amenity->name}' updated successfully.",
            'amenity' => $amenity,
        ]);
    }

    public function deleteAmenity(int $id): JsonResponse
    {
        $amenity = Amenity::findOrFail($id);
        $name = $amenity->name;
        $prev = $amenity->toArray();
        $amenity->delete();
        $this->logAudit('delete', 'Amenity', $id, null, $prev, []);

        return response()->json([
            'success' => true,
            'message' => "Amenity '{$name}' removed from library.",
        ]);
    }

    // ==========================================
    // 3. PHYSICAL ROOMS (ADM-05, ADM-06)
    // ==========================================
    public function storeRoom(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:50',
            'max_guests' => 'nullable|integer|min:1|max:20',
            'floor' => 'nullable|string|max:50',
            'operational_status' => 'required|in:available,occupied,reserved,maintenance,blocked,out_of_order',
            'housekeeping_status' => 'required|in:clean,dirty,inspecting',
            'notes' => 'nullable|string|max:255',
        ]);

        if (empty($validated['max_guests'])) {
            $validated['max_guests'] = null;
        }

        $room = Room::create($validated);
        $this->logAudit('create', 'Room', $room->id, $room->branch_id, [], $room->toArray());

        return response()->json([
            'success' => true,
            'message' => "Room unit '{$room->room_number}' created successfully.",
            'room' => $room->load(['roomType', 'branch']),
        ]);
    }

    // ==========================================
    // 4. DINING ITEMS & CATEGORIES (ADM-13)
    // ==========================================
    public function storeMenuItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_allocation' => 'nullable|in:all,specific',
            'branch_id' => 'nullable|exists:branches,id',
            'allocated_branch_ids' => 'nullable|array',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'dietary_tags' => 'nullable|array',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'is_vegetarian' => 'nullable|boolean',
            'prep_time_minutes' => 'nullable|integer|min:1',
            'available_days' => 'nullable|array',
            'is_available_today' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $allocation = $request->input('branch_allocation', 'all');
        if ($allocation === 'all') {
            $validated['is_all_branches'] = true;
            $validated['branch_id'] = null;
            $validated['allocated_branch_ids'] = null;
        } else {
            $validated['is_all_branches'] = false;
            $allocated = $request->input('allocated_branch_ids', []);
            $validated['allocated_branch_ids'] = $allocated;
            $validated['branch_id'] = !empty($allocated) ? $allocated[0] : ($request->input('branch_id') ?: null);
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['availability_state'] = 'in_stock';
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_vegetarian'] = $request->boolean('is_vegetarian', true);
        $validated['is_available_today'] = $request->boolean('is_available_today', true);
        $validated['tax_rate'] = $validated['tax_rate'] ?? 5.00;

        $item = MenuItem::create($validated);
        $this->logAudit('create', 'MenuItem', $item->id, $item->branch_id, [], $item->toArray());

        return response()->json([
            'success' => true,
            'message' => "Menu item '{$item->name}' added successfully.",
            'item' => $item->load(['category', 'branch']),
        ]);
    }

    public function updateMenuItem(Request $request, int $id): JsonResponse
    {
        $item = MenuItem::findOrFail($id);
        $validated = $request->validate([
            'branch_allocation' => 'nullable|in:all,specific',
            'branch_id' => 'nullable|exists:branches,id',
            'allocated_branch_ids' => 'nullable|array',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'dietary_tags' => 'nullable|array',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'is_vegetarian' => 'nullable|boolean',
            'prep_time_minutes' => 'nullable|integer|min:1',
            'available_days' => 'nullable|array',
            'is_available_today' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $allocation = $request->input('branch_allocation', $item->is_all_branches ? 'all' : 'specific');
        if ($allocation === 'all') {
            $validated['is_all_branches'] = true;
            $validated['branch_id'] = null;
            $validated['allocated_branch_ids'] = null;
        } else {
            $validated['is_all_branches'] = false;
            $allocated = $request->input('allocated_branch_ids', []);
            $validated['allocated_branch_ids'] = $allocated;
            $validated['branch_id'] = !empty($allocated) ? $allocated[0] : ($request->input('branch_id') ?: $item->branch_id);
        }

        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['is_vegetarian'] = $request->boolean('is_vegetarian', true);
        if ($request->has('is_available_today')) {
            $validated['is_available_today'] = $request->boolean('is_available_today');
        }

        $prev = $item->toArray();
        $item->update($validated);
        $this->logAudit('update', 'MenuItem', $item->id, $item->branch_id, $prev, $item->toArray());

        return response()->json([
            'success' => true,
            'message' => "Menu item '{$item->name}' updated successfully.",
            'item' => $item->load(['category', 'branch']),
        ]);
    }

    public function deleteMenuItem(int $id): JsonResponse
    {
        $item = MenuItem::findOrFail($id);
        $name = $item->name;
        $prev = $item->toArray();
        $item->delete();
        $this->logAudit('delete', 'MenuItem', $id, $item->branch_id, $prev, []);

        return response()->json([
            'success' => true,
            'message' => "Menu item '{$name}' removed from kitchen menu.",
        ]);
    }

    public function storeMenuCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        if (MenuCategory::where('slug', $validated['slug'])->when(!empty($validated['branch_id']), fn($q) => $q->where('branch_id', $validated['branch_id']))->exists()) {
            $validated['slug'] .= '-' . Str::random(4);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? (MenuCategory::max('sort_order') + 1);

        $category = MenuCategory::create($validated);
        $this->logAudit('create', 'MenuCategory', $category->id, $category->branch_id, [], $category->toArray());

        return response()->json([
            'success' => true,
            'message' => "Menu category '{$category->name}' added successfully.",
            'category' => $category->load('branch'),
        ]);
    }

    public function updateMenuCategory(Request $request, int $id): JsonResponse
    {
        $category = MenuCategory::findOrFail($id);
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $prev = $category->toArray();
        $category->update($validated);
        $this->logAudit('update', 'MenuCategory', $category->id, $category->branch_id, $prev, $category->toArray());

        return response()->json([
            'success' => true,
            'message' => "Menu category '{$category->name}' updated successfully.",
            'category' => $category->load('branch'),
        ]);
    }

    public function deleteMenuCategory(int $id): JsonResponse
    {
        $category = MenuCategory::findOrFail($id);
        $name = $category->name;
        $itemsCount = $category->menuItems()->count();
        if ($itemsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete category '{$name}' because it has {$itemsCount} linked dishes. Please reassign or delete the dishes first.",
            ], 422);
        }

        $prev = $category->toArray();
        $category->delete();
        $this->logAudit('delete', 'MenuCategory', $id, $category->branch_id, $prev, []);

        return response()->json([
            'success' => true,
            'message' => "Menu category '{$name}' deleted successfully.",
        ]);
    }

    // ==========================================
    // 5. SPICE PRODUCTS (ADM-16)
    // ==========================================
    public function storeSpiceProduct(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'spice_category_id' => 'required|exists:spice_categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:spice_products,sku',
            'selling_mode' => 'nullable|in:packet,loose,both',
            'price' => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'weight_grams' => 'nullable|integer|min:1',
            'package_size' => 'nullable|string|max:50',
            'min_loose_weight_kg' => 'nullable|numeric|min:0.01',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'short_description' => 'nullable|string|max:300',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'gallery' => 'nullable',
            'is_featured' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
            'is_returnable' => 'nullable|boolean',
        ]);

        if (!empty($validated['gallery'])) {
            if (is_string($validated['gallery'])) {
                $urls = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $validated['gallery'])));
                $validated['gallery'] = array_values($urls);
            } elseif (is_array($validated['gallery'])) {
                $validated['gallery'] = array_values(array_filter($validated['gallery']));
            }
        }

        $mode = $validated['selling_mode'] ?? 'both';
        $weightGrams = (int)($validated['weight_grams'] ?? 100);
        $packetPrice = isset($validated['price']) ? (float)$validated['price'] : null;
        $pricePerKg = isset($validated['price_per_kg']) ? (float)$validated['price_per_kg'] : null;

        if ($packetPrice === null && $pricePerKg !== null) {
            $packetPrice = round($pricePerKg * ($weightGrams / 1000), 2);
        } elseif ($pricePerKg === null && $packetPrice !== null) {
            $pricePerKg = round($packetPrice * (1000 / $weightGrams), 2);
        }

        $validated['selling_mode'] = $mode;
        $validated['price'] = $packetPrice ?? 100.00;
        $validated['price_per_kg'] = $pricePerKg ?? 1000.00;
        $validated['weight_grams'] = $weightGrams;
        $validated['package_size'] = $validated['package_size'] ?? ($weightGrams . 'g Pack');
        $validated['min_loose_weight_kg'] = (float)($validated['min_loose_weight_kg'] ?? 0.100);
        $validated['tax_rate'] = (float)($validated['tax_rate'] ?? 5.00);
        $validated['stock_quantity'] = (int)($validated['stock_quantity'] ?? 999);
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $isActive = $request->boolean('is_available', true);
        $validated['is_active'] = $isActive;
        $validated['is_available'] = $isActive;
        $validated['is_published'] = $isActive;
        $validated['is_returnable'] = $request->boolean('is_returnable', true);
        $validated['status'] = $isActive ? 'in_stock' : 'discontinued';
        $validated['low_stock_threshold'] = 0;

        $product = SpiceProduct::create($validated);
        $this->logAudit('create', 'SpiceProduct', $product->id, null, [], $product->toArray());

        return response()->json([
            'success' => true,
            'message' => "Spice product '{$product->name}' added successfully.",
            'product' => $product->load('category'),
        ]);
    }

    public function updateSpiceProduct(Request $request, int $id): JsonResponse
    {
        $product = SpiceProduct::findOrFail($id);

        $validated = $request->validate([
            'spice_category_id' => 'required|exists:spice_categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:spice_products,sku,' . $product->id,
            'selling_mode' => 'nullable|in:packet,loose,both',
            'price' => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'weight_grams' => 'nullable|integer|min:1',
            'package_size' => 'nullable|string|max:50',
            'min_loose_weight_kg' => 'nullable|numeric|min:0.01',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'short_description' => 'nullable|string|max:300',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'gallery' => 'nullable',
            'is_featured' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
            'is_returnable' => 'nullable|boolean',
        ]);

        if (isset($validated['gallery'])) {
            if (is_string($validated['gallery'])) {
                $urls = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $validated['gallery'])));
                $validated['gallery'] = array_values($urls);
            } elseif (is_array($validated['gallery'])) {
                $validated['gallery'] = array_values(array_filter($validated['gallery']));
            }
        }

        $mode = $validated['selling_mode'] ?? $product->selling_mode;
        $weightGrams = (int)($validated['weight_grams'] ?? $product->weight_grams ?? 100);
        $packetPrice = isset($validated['price']) ? (float)$validated['price'] : (float)$product->price;
        $pricePerKg = isset($validated['price_per_kg']) ? (float)$validated['price_per_kg'] : (float)$product->price_per_kg;

        if ($packetPrice <= 0 && $pricePerKg > 0) {
            $packetPrice = round($pricePerKg * ($weightGrams / 1000), 2);
        } elseif ($pricePerKg <= 0 && $packetPrice > 0) {
            $pricePerKg = round($packetPrice * (1000 / $weightGrams), 2);
        }

        $validated['selling_mode'] = $mode;
        $validated['price'] = $packetPrice;
        $validated['price_per_kg'] = $pricePerKg;
        $validated['weight_grams'] = $weightGrams;
        $validated['package_size'] = $validated['package_size'] ?? ($weightGrams . 'g Pack');
        $validated['min_loose_weight_kg'] = (float)($validated['min_loose_weight_kg'] ?? 0.100);
        if ($request->has('is_available')) {
            $isActive = $request->boolean('is_available');
            $validated['is_available'] = $isActive;
            $validated['is_active'] = $isActive;
            $validated['is_published'] = $isActive;
            $validated['status'] = $isActive ? 'in_stock' : 'discontinued';
        }

        if ($request->has('is_returnable')) {
            $validated['is_returnable'] = $request->boolean('is_returnable');
        }

        $old = $product->toArray();
        $product->update($validated);
        $this->logAudit('update', 'SpiceProduct', $product->id, null, $old, $product->toArray());

        return response()->json([
            'success' => true,
            'message' => "Spice product '{$product->name}' updated successfully.",
            'product' => $product->load('category'),
        ]);
    }

    public function deleteSpiceProduct(Request $request, int $id): JsonResponse
    {
        $product = SpiceProduct::findOrFail($id);
        $old = $product->toArray();
        $name = $product->name;
        $product->delete();
        $this->logAudit('delete', 'SpiceProduct', $id, null, $old, []);

        return response()->json([
            'success' => true,
            'message' => "Spice product '{$name}' deleted successfully.",
        ]);
    }

    // ==========================================
    // 6. FACILITIES (ADM-10)
    // ==========================================
    public function storeFacility(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'nullable',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'is_bookable' => 'nullable|boolean',
            'rate' => 'nullable|numeric|min:0',
            'has_scheduling' => 'nullable|boolean',
            'operating_hours' => 'nullable|string|max:100',
            'short_description' => 'nullable|string|max:300',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        if (empty($validated['branch_id']) || $validated['branch_id'] === '0' || $validated['branch_id'] === 'all') {
            $validated['branch_id'] = null;
        }

        $validated['is_bookable'] = $request->boolean('is_bookable');
        $validated['rate'] = $validated['is_bookable'] ? (float)($validated['rate'] ?? 0) : 0.00;
        $validated['has_scheduling'] = $request->boolean('has_scheduling');
        $validated['category'] = !empty($validated['category']) ? $validated['category'] : 'experience';

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_published'] = true;

        $facility = Facility::create($validated);
        $this->logAudit('create', 'Facility', $facility->id, $facility->branch_id, [], $facility->toArray());

        return response()->json([
            'success' => true,
            'message' => "Facility '{$facility->name}' created successfully.",
            'facility' => $facility->load('branch'),
        ]);
    }

    public function allocateFacilityTimeSlot(Request $request, int $bookingId): JsonResponse
    {
        $booking = FacilityBooking::with(['facility', 'reservation.room', 'guest'])->findOrFail($bookingId);

        $validated = $request->validate([
            'allocated_time_slot' => 'required|string|max:120',
            'status' => 'nullable|string|in:pending,confirmed,completed,cancelled',
            'charge_room_folio' => 'nullable|boolean',
        ]);

        $booking->allocated_time_slot = $validated['allocated_time_slot'];
        $booking->status = $validated['status'] ?? 'confirmed';

        // Check if manager requested posting the charge to room folio
        if ($request->boolean('charge_room_folio') && $booking->total_amount > 0 && !$booking->folio_charge_id) {
            $folioCharge = FolioCharge::create([
                'reservation_id' => $booking->reservation_id,
                'branch_id' => $booking->branch_id ?? ($booking->reservation ? $booking->reservation->branch_id : null),
                'category' => 'activity',
                'title' => "Facility: " . ($booking->facility ? $booking->facility->name : 'Resort Experience') . " ({$booking->guests_count} Guests)",
                'description' => "Allocated Slot: {$booking->allocated_time_slot} on " . Carbon::parse($booking->booking_date)->format('d M Y'),
                'amount' => $booking->total_amount,
                'is_paid' => false,
                'added_by' => auth()->id(),
            ]);

            $booking->folio_charge_id = $folioCharge->id;
        }

        $booking->save();

        $this->logAudit('update', 'FacilityBooking', $booking->id, $booking->branch_id, [], $booking->toArray());

        return response()->json([
            'success' => true,
            'message' => "Time slot '{$booking->allocated_time_slot}' allocated successfully for " . ($booking->guest ? $booking->guest->full_name : 'Guest') . ".",
            'booking' => $booking->load(['facility', 'reservation', 'guest', 'folioCharge']),
        ]);
    }

    public function updateFacilityBookingStatus(Request $request, int $bookingId): JsonResponse
    {
        $booking = FacilityBooking::findOrFail($bookingId);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->status = $validated['status'];
        $booking->save();

        $this->logAudit('status_change', 'FacilityBooking', $booking->id, $booking->branch_id, [], ['status' => $booking->status]);

        return response()->json([
            'success' => true,
            'message' => "Facility booking #{$booking->id} status updated to '{$booking->status}'.",
            'booking' => $booking->load(['facility', 'reservation', 'guest']),
        ]);
    }

    public function storeFacilityBookingAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'facility_id' => 'required|exists:facilities,id',
            'booking_date' => 'required|date',
            'guests_count' => 'nullable|integer|min:1',
            'allocated_time_slot' => 'nullable|string|max:120',
            'notes' => 'nullable|string|max:500',
            'charge_room_folio' => 'nullable|boolean',
        ]);

        $reservation = Reservation::with(['guest', 'room'])->findOrFail($validated['reservation_id']);
        $facility = Facility::findOrFail($validated['facility_id']);

        $guestsCount = (int)($validated['guests_count'] ?? 1);
        $rate = (float)($facility->rate ?? 0.00);
        $totalAmount = $facility->is_bookable ? round($rate * $guestsCount, 2) : 0.00;

        $booking = FacilityBooking::create([
            'facility_id' => $facility->id,
            'reservation_id' => $reservation->id,
            'guest_id' => $reservation->guest_id,
            'branch_id' => $facility->branch_id ?? $reservation->branch_id,
            'booking_date' => $validated['booking_date'],
            'guests_count' => $guestsCount,
            'rate' => $rate,
            'total_amount' => $totalAmount,
            'allocated_time_slot' => $validated['allocated_time_slot'] ?? null,
            'status' => !empty($validated['allocated_time_slot']) ? 'confirmed' : 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->boolean('charge_room_folio') && $totalAmount > 0) {
            $charge = FolioCharge::create([
                'reservation_id' => $reservation->id,
                'branch_id' => $reservation->branch_id,
                'category' => 'activity',
                'title' => "Facility: {$facility->name} ({$guestsCount} Guests)",
                'description' => "Scheduled for " . Carbon::parse($booking->booking_date)->format('d M Y') . ($booking->allocated_time_slot ? " ({$booking->allocated_time_slot})" : ''),
                'amount' => $totalAmount,
                'is_paid' => false,
                'added_by' => auth()->id(),
            ]);
            $booking->update(['folio_charge_id' => $charge->id]);
        }

        $this->logAudit('create', 'FacilityBooking', $booking->id, $booking->branch_id, [], $booking->toArray());

        return response()->json([
            'success' => true,
            'message' => "Facility '{$facility->name}' booked successfully for {$reservation->guest->full_name}.",
            'booking' => $booking->load(['facility', 'reservation', 'guest']),
        ]);
    }

    // ==========================================
    // 7. NEARBY LOCATIONS (ADM-12) & TAXI REQUESTS
    // ==========================================
    public function storeNearbyLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'distance_km' => 'required|numeric|min:0',
            'travel_time' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'is_available' => 'nullable|boolean',
            'is_taxi_available' => 'nullable|boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_taxi_available'] = $request->boolean('is_taxi_available', true);

        $nearby = NearbyLocation::create($validated);
        $this->logAudit('create', 'NearbyLocation', $nearby->id, $nearby->branch_id, [], $nearby->toArray());

        return response()->json([
            'success' => true,
            'message' => "Nearby discovery '{$nearby->name}' added successfully.",
            'nearby' => $nearby->load('branch'),
        ]);
    }

    public function updateNearbyLocation(Request $request, int $id): JsonResponse
    {
        $nearby = NearbyLocation::findOrFail($id);

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'distance_km' => 'required|numeric|min:0',
            'travel_time' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'is_available' => 'nullable|boolean',
            'is_taxi_available' => 'nullable|boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($request->has('is_available')) {
            $validated['is_available'] = $request->boolean('is_available');
        }
        if ($request->has('is_taxi_available')) {
            $validated['is_taxi_available'] = $request->boolean('is_taxi_available');
        }

        $prev = $nearby->toArray();
        $nearby->update($validated);
        $this->logAudit('update', 'NearbyLocation', $nearby->id, $nearby->branch_id, $prev, $nearby->toArray());

        return response()->json([
            'success' => true,
            'message' => "Nearby location '{$nearby->name}' updated successfully.",
            'nearby' => $nearby->load('branch'),
        ]);
    }

    public function toggleNearbyAvailability(int $id): JsonResponse
    {
        $nearby = NearbyLocation::findOrFail($id);
        $nearby->is_available = !$nearby->is_available;
        $nearby->save();

        $statusText = $nearby->is_available ? 'open and visible to guests' : 'temporarily unavailable and hidden';
        $this->logAudit('toggle_availability', 'NearbyLocation', $nearby->id, $nearby->branch_id, [], ['is_available' => $nearby->is_available]);

        return response()->json([
            'success' => true,
            'is_available' => $nearby->is_available,
            'message' => "Location '{$nearby->name}' is now {$statusText}.",
        ]);
    }

    public function toggleNearbyTaxi(int $id): JsonResponse
    {
        $nearby = NearbyLocation::findOrFail($id);
        $nearby->is_taxi_available = !$nearby->is_taxi_available;
        $nearby->save();

        $statusText = $nearby->is_taxi_available ? 'available for cab bookings' : 'cab service disabled';
        $this->logAudit('toggle_taxi', 'NearbyLocation', $nearby->id, $nearby->branch_id, [], ['is_taxi_available' => $nearby->is_taxi_available]);

        return response()->json([
            'success' => true,
            'is_taxi_available' => $nearby->is_taxi_available,
            'message' => "Cab service for '{$nearby->name}' is now {$statusText}.",
        ]);
    }

    public function deleteNearbyLocation(int $id): JsonResponse
    {
        $nearby = NearbyLocation::findOrFail($id);
        $name = $nearby->name;
        $branchId = $nearby->branch_id;
        $nearby->delete();

        $this->logAudit('delete', 'NearbyLocation', $id, $branchId, ['name' => $name], []);

        return response()->json([
            'success' => true,
            'message' => "Location '{$name}' has been archived.",
        ]);
    }

    public function updateTaxiRequestStatus(Request $request, int $id): JsonResponse
    {
        $taxi = TaxiRequest::with(['reservation', 'guest'])->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,confirmed,completed,cancelled',
            'driver_details' => 'nullable|string|max:255',
            'manager_notes' => 'nullable|string|max:1000',
            'pickup_time' => 'nullable|string|max:50',
        ]);

        $prev = $taxi->toArray();
        $taxi->update($validated);

        $this->logAudit('update_status', 'TaxiRequest', $taxi->id, $taxi->branch_id, $prev, $taxi->toArray());

        return response()->json([
            'success' => true,
            'message' => "Taxi request #{$taxi->booking_reference} status updated to '{$taxi->status}'.",
            'taxi_request' => $taxi,
        ]);
    }

    public function quoteTaxiRequestFare(Request $request, int $id): JsonResponse
    {
        $taxi = TaxiRequest::with(['reservation', 'guest'])->findOrFail($id);

        $validated = $request->validate([
            'estimated_fare' => 'required|numeric|min:0',
            'pickup_time' => 'nullable|string|max:50',
            'driver_details' => 'nullable|string|max:255',
            'manager_notes' => 'nullable|string|max:1000',
            'post_to_folio' => 'nullable|boolean',
            'status' => 'nullable|in:contacted,confirmed,completed',
        ]);

        $taxi->estimated_fare = $validated['estimated_fare'];
        if (!empty($validated['pickup_time'])) {
            $taxi->pickup_time = $validated['pickup_time'];
        }
        if (isset($validated['driver_details'])) {
            $taxi->driver_details = $validated['driver_details'];
        }
        if (isset($validated['manager_notes'])) {
            $taxi->manager_notes = $validated['manager_notes'];
        }
        $taxi->status = $validated['status'] ?? 'contacted';

        // Optional folio charge posting
        if ($request->boolean('post_to_folio') && $taxi->estimated_fare > 0 && !$taxi->folio_charge_id) {
            $charge = FolioCharge::create([
                'reservation_id' => $taxi->reservation_id,
                'branch_id' => $taxi->branch_id,
                'category' => 'transport',
                'title' => "Resort Cab / Excursion ({$taxi->booking_reference})",
                'description' => "Destinations: " . ($taxi->selected_locations->pluck('name')->implode(', ') ?: 'Local Excursion') . ($taxi->extra_locations_notes ? " | Notes: {$taxi->extra_locations_notes}" : ''),
                'amount' => $taxi->estimated_fare,
                'is_paid' => false,
                'added_by' => auth()->id() ?? 1,
            ]);
            $taxi->folio_charge_id = $charge->id;
            $taxi->status = 'confirmed';
        }

        $taxi->save();

        $this->logAudit('quote_fare', 'TaxiRequest', $taxi->id, $taxi->branch_id, [], $taxi->toArray());

        return response()->json([
            'success' => true,
            'message' => "Quoted ₹" . number_format($taxi->estimated_fare, 2) . " for Excursion #{$taxi->booking_reference}." . ($taxi->folio_charge_id ? " Posted to Room Folio." : ""),
            'taxi_request' => $taxi->load('folioCharge'),
        ]);
    }

    // ==========================================
    // ==========================================
    // 8. GALLERY ALBUMS (ADM-11)
    // ==========================================
    public function storeGalleryAlbum(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'cover_image_url' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|string',
        ]);

        $name = !empty($validated['name']) ? $validated['name'] : (!empty($validated['title']) ? $validated['title'] : 'Resort Album');
        $title = !empty($validated['title']) ? $validated['title'] : $name;
        $category = !empty($validated['category']) ? $validated['category'] : 'resort';

        $branchId = !empty($validated['branch_id']) ? $validated['branch_id'] : null;

        $album = GalleryAlbum::create([
            'branch_id' => $branchId,
            'name' => $name,
            'title' => $title,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'category' => $category,
            'description' => !empty($validated['description']) ? $validated['description'] : null,
            'cover_image_url' => $validated['cover_image_url'] ?? null,
            'is_featured' => $request->boolean('is_featured', false),
            'is_published' => true,
            'images_count' => 0,
            'sort_order' => 0,
        ]);

        $photoUrls = [];
        if (!empty($validated['images'])) {
            $photoUrls = array_merge($photoUrls, $validated['images']);
        }
        // Also support direct device files in store if passed
        if ($request->hasFile('files')) {
            $dest = public_path('uploads/media');
            if (!file_exists($dest)) {
                mkdir($dest, 0755, true);
            }
            foreach ($request->file('files') as $file) {
                if ($file && $file->isValid()) {
                    $fn = 'krishna_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                    $file->move($dest, $fn);
                    $photoUrls[] = '/uploads/media/' . $fn;
                }
            }
        }

        $photoCount = 0;
        foreach ($photoUrls as $idx => $imgUrl) {
            $imgUrl = trim($imgUrl);
            if (!empty($imgUrl)) {
                GalleryImage::create([
                    'gallery_album_id' => $album->id,
                    'image_url' => $imgUrl,
                    'title' => $album->name . ' Photo ' . ($idx + 1),
                    'sort_order' => $idx + 1,
                ]);
                $photoCount++;
            }
        }

        $album->update(['images_count' => $photoCount]);

        $this->logAudit('create', 'GalleryAlbum', $album->id, $album->branch_id, [], $album->toArray());

        return response()->json([
            'success' => true,
            'message' => "Gallery album '{$album->name}' created successfully.",
            'album' => $album->load(['images', 'branch']),
        ]);
    }

    public function updateGalleryAlbum(Request $request, $id): JsonResponse
    {
        $album = GalleryAlbum::findOrFail($id);

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'cover_image_url' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $old = $album->toArray();

        if (isset($validated['name']) || isset($validated['title'])) {
            $name = $validated['name'] ?? ($validated['title'] ?? $album->name);
            $album->name = $name;
            $album->title = $validated['title'] ?? $name;
        }

        if (array_key_exists('branch_id', $validated)) {
            $album->branch_id = !empty($validated['branch_id']) ? $validated['branch_id'] : null;
        }
        if (!empty($validated['category'])) {
            $album->category = $validated['category'];
        }
        if (array_key_exists('description', $validated)) {
            $album->description = !empty($validated['description']) ? $validated['description'] : null;
        }
        if (array_key_exists('cover_image_url', $validated)) {
            $album->cover_image_url = $validated['cover_image_url'];
        }
        if ($request->has('is_published')) {
            $album->is_published = $request->boolean('is_published');
        }

        $album->save();

        $this->logAudit('update', 'GalleryAlbum', $album->id, $album->branch_id, $old, $album->toArray());

        return response()->json([
            'success' => true,
            'message' => "Gallery album '{$album->name}' updated successfully.",
            'album' => $album->load(['images', 'branch']),
        ]);
    }

    public function deleteGalleryAlbum($id): JsonResponse
    {
        $album = GalleryAlbum::findOrFail($id);
        $name = $album->name;
        $branchId = $album->branch_id;
        $old = $album->toArray();

        // Delete associated gallery images
        $album->images()->delete();
        $album->delete();

        $this->logAudit('delete', 'GalleryAlbum', $id, $branchId, $old, []);

        return response()->json([
            'success' => true,
            'message' => "Gallery album '{$name}' deleted successfully.",
        ]);
    }

    public function getAlbumPhotos($id): JsonResponse
    {
        $album = GalleryAlbum::with(['branch', 'images'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'album' => $album,
            'photos' => $album->images,
            'count' => $album->images->count(),
        ]);
    }

    public function uploadAlbumPhotos(Request $request, $id): JsonResponse
    {
        $album = GalleryAlbum::findOrFail($id);

        $request->validate([
            'images' => 'nullable|array',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,webp,gif,avif,svg|max:12288',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'nullable|string',
            'single_url' => 'nullable|string',
        ]);

        $addedPhotos = [];
        $currentMaxOrder = $album->images()->max('sort_order') ?: 0;

        // 1. Files uploaded from device
        if ($request->hasFile('images')) {
            $dest = public_path('uploads/media');
            if (!file_exists($dest)) {
                mkdir($dest, 0755, true);
            }

            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $filename = 'album_' . $album->id . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $file->move($dest, $filename);
                    $url = '/uploads/media/' . $filename;
                    $currentMaxOrder++;

                    $img = GalleryImage::create([
                        'gallery_album_id' => $album->id,
                        'image_url' => $url,
                        'title' => $album->name . ' Photo ' . $currentMaxOrder,
                        'sort_order' => $currentMaxOrder,
                    ]);
                    $addedPhotos[] = $img;
                }
            }
        }

        // 2. Direct image URLs passed as array or single
        $urls = (array) ($request->input('image_urls') ?: []);
        if ($request->filled('single_url')) {
            $urls[] = $request->input('single_url');
        }

        foreach ($urls as $url) {
            $url = trim($url);
            if (!empty($url)) {
                $currentMaxOrder++;
                $img = GalleryImage::create([
                    'gallery_album_id' => $album->id,
                    'image_url' => $url,
                    'title' => $album->name . ' Photo ' . $currentMaxOrder,
                    'sort_order' => $currentMaxOrder,
                ]);
                $addedPhotos[] = $img;
            }
        }

        // Update album images count
        $totalCount = $album->images()->count();
        $album->update(['images_count' => $totalCount]);

        // If album had no cover image, use first photo
        if (empty($album->cover_image_url) && !empty($addedPhotos[0])) {
            $album->update(['cover_image_url' => $addedPhotos[0]->image_url]);
        }

        return response()->json([
            'success' => true,
            'message' => count($addedPhotos) . " photo(s) added to album '{$album->name}'.",
            'added' => $addedPhotos,
            'total_count' => $totalCount,
            'photos' => $album->images()->get(),
        ]);
    }

    public function deleteAlbumPhoto($albumId, $photoId): JsonResponse
    {
        $album = GalleryAlbum::findOrFail($albumId);
        $photo = GalleryImage::where('gallery_album_id', $album->id)->where('id', $photoId)->firstOrFail();

        $photo->delete();
        $totalCount = $album->images()->count();
        $album->update(['images_count' => $totalCount]);

        return response()->json([
            'success' => true,
            'message' => 'Photo removed from album.',
            'total_count' => $totalCount,
        ]);
    }

    // ==========================================
    // 9. SYSTEM SETTINGS (ADM-28)
    // ==========================================
    public function updateSettings(Request $request): JsonResponse
    {
        // 1. Handle Login & Register Page Image
        if ($request->hasFile('login_page_image_file')) {
            $file = $request->file('login_page_image_file');
            if ($file && $file->isValid()) {
                $dest = public_path('uploads/settings');
                if (!file_exists($dest)) {
                    mkdir($dest, 0755, true);
                }
                $fn = 'login_bg_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move($dest, $fn);
                $imageUrl = '/uploads/settings/' . $fn;
                Setting::set('login_page_image', $imageUrl, 'auth_config', 'Login & Register Background Image');
            }
        } elseif ($request->filled('login_page_image')) {
            Setting::set('login_page_image', trim($request->input('login_page_image')), 'auth_config', 'Login & Register Background Image');
        }

        // 2. Update general resort settings
        $data = $request->except(['_token', 'login_page_image_file', 'login_page_image']);
        foreach ($data as $key => $val) {
            Setting::set($key, $val, 'resort_config');
        }

        $this->logAudit('update_settings', 'Setting', 1, null, [], $request->except(['_token', 'login_page_image_file']));

        return response()->json([
            'success' => true,
            'message' => 'Resort configuration and login background image updated successfully.',
            'login_page_image' => Setting::get('login_page_image'),
        ]);
    }

    /**
     * Update Razorpay Payment Gateway Settings from Admin Panel
     */
    public function updateRazorpaySettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'razorpay_key_id' => 'required|string|max:100',
            'razorpay_key_secret' => 'required|string|max:100',
            'razorpay_webhook_secret' => 'nullable|string|max:100',
        ]);

        Setting::set('razorpay_key_id', trim($validated['razorpay_key_id']), 'payment_gateway', 'Razorpay API Key ID');
        Setting::set('razorpay_key_secret', trim($validated['razorpay_key_secret']), 'payment_gateway', 'Razorpay API Key Secret');
        if ($request->has('razorpay_webhook_secret')) {
            Setting::set('razorpay_webhook_secret', trim($validated['razorpay_webhook_secret'] ?? ''), 'payment_gateway', 'Razorpay Webhook Secret');
        }

        $this->logAudit('update_razorpay_settings', 'Setting', 1, null, [], [
            'key_id' => $validated['razorpay_key_id'],
            'is_test' => str_starts_with($validated['razorpay_key_id'], 'rzp_test_'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Razorpay payment gateway credentials saved successfully.',
            'is_test' => str_starts_with($validated['razorpay_key_id'], 'rzp_test_'),
        ]);
    }

    /**
     * Test Razorpay API Connection
     */
    public function testRazorpayConnection(Request $request, RazorpayService $razorpayService): JsonResponse
    {
        $keyId = $request->input('key_id');
        $keySecret = $request->input('key_secret');

        $result = $razorpayService->testConnection($keyId, $keySecret);
        return response()->json($result);
    }

    // ==========================================
    // 10. STAFF MANAGEMENT (ADM-26)
    // ==========================================
    public function storeStaff(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,central_manager,branch_manager,reservation_staff,support_staff',
            'phone' => 'nullable|string|max:50',
            'branch_access_type' => 'required|in:all,specific,assigned',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $isCustom = in_array($validated['branch_access_type'], ['specific', 'assigned']);
        if ($isCustom && empty($validated['branch_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one branch for customized branch access.',
            ], 422);
        }

        $validated['branch_access_type'] = $isCustom ? 'specific' : 'all';
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $staff = User::create($validated);

        if ($isCustom && !empty($validated['branch_ids'])) {
            $staff->branches()->sync($validated['branch_ids']);
        } else {
            $staff->branches()->detach();
        }

        $this->logAudit('create', 'User', $staff->id, null, [], [
            'name' => $staff->name,
            'email' => $staff->email,
            'role' => $staff->role,
            'branch_access_type' => $staff->branch_access_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Staff member '{$staff->name}' created successfully.",
            'staff' => $staff->load('branches'),
        ]);
    }

    public function updateStaff(Request $request, $id): JsonResponse
    {
        $staff = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $staff->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:super_admin,central_manager,branch_manager,reservation_staff,support_staff',
            'phone' => 'nullable|string|max:50',
            'branch_access_type' => 'required|in:all,specific,assigned',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'is_active' => 'nullable|boolean',
        ]);

        $isCustom = in_array($validated['branch_access_type'], ['specific', 'assigned']);
        if ($isCustom && empty($validated['branch_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one branch for customized branch access.',
            ], 422);
        }

        $oldValues = [
            'name' => $staff->name,
            'email' => $staff->email,
            'role' => $staff->role,
            'branch_access_type' => $staff->branch_access_type,
            'is_active' => $staff->is_active,
        ];

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->role = $validated['role'];
        $staff->phone = $validated['phone'] ?? null;
        $staff->branch_access_type = $isCustom ? 'specific' : 'all';
        if ($request->has('is_active')) {
            $staff->is_active = $request->boolean('is_active');
        }

        if (!empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        if ($isCustom && !empty($validated['branch_ids'])) {
            $staff->branches()->sync($validated['branch_ids']);
        } else {
            $staff->branches()->detach();
        }

        $this->logAudit('update', 'User', $staff->id, null, $oldValues, [
            'name' => $staff->name,
            'email' => $staff->email,
            'role' => $staff->role,
            'branch_access_type' => $staff->branch_access_type,
            'is_active' => $staff->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Staff member '{$staff->name}' updated successfully.",
            'staff' => $staff->load('branches'),
        ]);
    }

    public function deleteStaff($id): JsonResponse
    {
        $staff = User::findOrFail($id);

        if (auth()->check() && auth()->id() === $staff->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        $name = $staff->name;
        $staff->branches()->detach();
        $staff->delete();

        $this->logAudit('delete', 'User', $id, null, ['name' => $name], []);

        return response()->json([
            'success' => true,
            'message' => "Staff member '{$name}' deleted successfully.",
        ]);
    }

    // ==========================================
    // 10. CMS PAGES & NAVIGATION (ADM-22 & ADM-23)
    // ==========================================
    public function storePage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'branch_id' => 'nullable|exists:branches,id',
            'short_description' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:500',
            'content_blocks' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        if (!empty($validated['content_blocks'])) {
            $validated['content_blocks'] = ['body' => $validated['content_blocks']];
        }

        $page = Page::create($validated);
        $this->logAudit('create', 'Page', $page->id, $page->branch_id, [], $page->toArray());

        return response()->json([
            'success' => true,
            'message' => "Page '{$page->title}' published successfully.",
            'page' => $page->load('branch'),
        ]);
    }

    public function updatePage(Request $request, int $id): JsonResponse
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $id,
            'branch_id' => 'nullable|exists:branches,id',
            'short_description' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:500',
            'content_blocks' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->has('is_published')) {
            $validated['is_published'] = $request->boolean('is_published');
        }
        if (!empty($validated['content_blocks'])) {
            $validated['content_blocks'] = ['body' => $validated['content_blocks']];
        }

        $prev = $page->toArray();
        $page->update($validated);
        $this->logAudit('update', 'Page', $page->id, $page->branch_id, $prev, $page->toArray());

        return response()->json([
            'success' => true,
            'message' => "Page '{$page->title}' updated successfully.",
            'page' => $page->load('branch'),
        ]);
    }

    public function deletePage(int $id): JsonResponse
    {
        $page = Page::findOrFail($id);
        $title = $page->title;
        $page->delete();
        $this->logAudit('delete', 'Page', $id, $page->branch_id, ['title' => $title]);

        return response()->json([
            'success' => true,
            'message' => "Page '{$title}' removed.",
        ]);
    }

    public function togglePageStatus(int $id): JsonResponse
    {
        $page = Page::findOrFail($id);
        $page->is_published = !$page->is_published;
        $page->save();

        $this->logAudit('toggle_publish', 'Page', $page->id, $page->branch_id, [], ['is_published' => $page->is_published]);

        return response()->json([
            'success' => true,
            'message' => "Page '{$page->title}' is now " . ($page->is_published ? 'published' : 'draft') . ".",
            'is_published' => $page->is_published,
        ]);
    }

    public function storeNavigationItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'parent_id' => 'nullable|exists:navigation_items,id',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $nav = NavigationItem::create($validated);
        $this->logAudit('create', 'NavigationItem', $nav->id, $nav->branch_id, [], $nav->toArray());

        return response()->json([
            'success' => true,
            'message' => "Navigation item '{$nav->label}' added.",
            'nav_item' => $nav,
        ]);
    }

    public function updateNavigationItem(Request $request, int $id): JsonResponse
    {
        $nav = NavigationItem::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'parent_id' => 'nullable|exists:navigation_items,id',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $prev = $nav->toArray();
        $nav->update($validated);
        $this->logAudit('update', 'NavigationItem', $nav->id, $nav->branch_id, $prev, $nav->toArray());

        return response()->json([
            'success' => true,
            'message' => "Navigation item '{$nav->label}' updated.",
            'nav_item' => $nav,
        ]);
    }

    public function deleteNavigationItem(int $id): JsonResponse
    {
        $nav = NavigationItem::findOrFail($id);
        $label = $nav->label;
        $nav->delete();
        $this->logAudit('delete', 'NavigationItem', $id, $nav->branch_id, ['label' => $label]);

        return response()->json([
            'success' => true,
            'message' => "Navigation link '{$label}' removed.",
        ]);
    }

    // ==========================================
    // 11. HOMEPAGE MODULES & FAST ROOM TOGGLE
    // ==========================================
    public function toggleHomepageModule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module' => 'required|string|in:branches,categories,facilities,dining,spices,reviews',
            'is_enabled' => 'required|boolean',
        ]);

        $key = 'homepage_module_' . $validated['module'];
        Setting::set($key, $validated['is_enabled'] ? '1' : '0', 'homepage', "Homepage {$validated['module']} module visibility");

        $this->logAudit('toggle_module', 'Setting', 0, null, [], [$key => $validated['is_enabled']]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($validated['module']) . " module " . ($validated['is_enabled'] ? 'enabled' : 'hidden') . " on public homepage.",
            'module' => $validated['module'],
            'is_enabled' => $validated['is_enabled'],
        ]);
    }

    public function quickToggleHousekeeping(int $roomId): JsonResponse
    {
        $room = Room::findOrFail($roomId);
        $newStatus = $room->housekeeping_status === 'clean' ? 'dirty' : 'clean';
        $prev = $room->housekeeping_status;
        $room->housekeeping_status = $newStatus;
        $room->save();

        $this->logAudit('toggle_housekeeping', 'Room', $room->id, $room->branch_id, ['housekeeping_status' => $prev], ['housekeeping_status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Room {$room->room_number} marked as {$newStatus}.",
            'housekeeping_status' => $newStatus,
            'room' => $room,
        ]);
    }
}
