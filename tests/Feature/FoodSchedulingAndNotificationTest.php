<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Facility;
use App\Models\Guest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\NearbyLocation;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SpiceCategory;
use App\Models\SpiceProduct;
use App\Models\User;
use App\Services\EmailNotificationService;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class FoodSchedulingAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected RoomType $roomType;
    protected Room $room;
    protected User $customer;
    protected Guest $guest;
    protected MenuItem $menuItem;
    protected SpiceProduct $spiceProduct;
    protected Facility $facility;
    protected $mockEmail;
    protected $mockTelegram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockEmail = Mockery::mock(EmailNotificationService::class)->makePartial();
        $this->mockTelegram = Mockery::mock(TelegramNotificationService::class)->makePartial();

        $this->app->instance(EmailNotificationService::class, $this->mockEmail);
        $this->app->instance(TelegramNotificationService::class, $this->mockTelegram);

        $this->branch = Branch::create([
            'name' => 'Wayanad Evergreen Estate',
            'code' => 'WYN',
            'slug' => 'wayanad-estate',
            'city' => 'Wayanad',
            'state' => 'Kerala',
            'phone' => '+91 94471 22334',
            'email' => 'wayanad@krishnacottages.com',
            'status' => 'active',
            'is_published' => true,
        ]);

        $this->roomType = RoomType::create([
            'branch_id' => $this->branch->id,
            'name' => 'Plantation Villa',
            'slug' => 'plantation-villa',
            'code' => 'PV1',
            'base_price' => 5000.00,
            'max_guests' => 3,
            'is_active' => true,
            'is_bookable' => true,
        ]);

        $this->room = Room::create([
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '102',
            'operational_status' => 'available',
            'housekeeping_status' => 'clean',
        ]);

        $this->customer = User::create([
            'name' => 'Rahul Verma',
            'email' => 'rahul.verma@example.com',
            'password' => bcrypt('Secret123!'),
            'role' => 'customer',
            'phone' => '+91 98765 00001',
            'is_active' => true,
        ]);

        $this->guest = Guest::create([
            'user_id' => $this->customer->id,
            'first_name' => 'Rahul',
            'last_name' => 'Verma',
            'email' => 'rahul.verma@example.com',
            'phone' => '+91 98765 00001',
            'city' => 'Bangalore',
            'country' => 'India',
        ]);

        $category = MenuCategory::create([
            'name' => 'Kerala Breakfast',
            'slug' => 'kerala-breakfast',
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $this->menuItem = MenuItem::create([
            'branch_id' => $this->branch->id,
            'menu_category_id' => $category->id,
            'name' => 'Appam with Vegetable Stew',
            'slug' => 'appam-veg-stew',
            'price' => 280.00,
            'is_vegetarian' => true,
            'is_active' => true,
            'availability_state' => 'in_stock',
        ]);

        $spiceCat = SpiceCategory::create([
            'name' => 'Estate Cardamom',
            'slug' => 'estate-cardamom',
            'is_active' => true,
        ]);

        $this->spiceProduct = SpiceProduct::create([
            'spice_category_id' => $spiceCat->id,
            'name' => 'Green Cardamom 8mm Bold',
            'slug' => 'green-cardamom-8mm',
            'sku' => 'SP-CRD-01',
            'price' => 650.00,
            'is_active' => true,
            'stock_quantity' => 50,
        ]);

        $this->facility = Facility::create([
            'branch_id' => $this->branch->id,
            'name' => 'Ayurvedic Abhyanga Spa',
            'slug' => 'ayurvedic-abhyanga-spa',
            'category' => 'wellness',
            'rate' => 2500.00,
            'is_bookable' => true,
            'is_published' => true,
        ]);
    }

    public function test_food_order_rejected_for_unbooked_customer()
    {
        $response = $this->actingAs($this->customer)->postJson(route('dining.order'), [
            'customer_name' => 'Rahul Verma',
            'customer_phone' => '+91 98765 00001',
            'branch_id' => $this->branch->id,
            'order_type' => 'room_service',
            'table_number' => 'Villa 102',
            'items' => [
                ['id' => $this->menuItem->id, 'quantity' => 2]
            ]
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'is_unbooked' => true,
        ]);
    }

    public function test_food_order_scheduled_with_delivery_date_and_time_for_booked_customer()
    {
        $reservation = Reservation::create([
            'booking_code' => 'KR-TEST-101',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'created_by' => $this->customer->id,
            'check_in_date' => Carbon::today()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'rooms_count' => 1,
            'nightly_rate' => 5000.00,
            'subtotal' => 5000.00,
            'total_amount' => 5600.00,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $this->mockEmail->shouldReceive('sendFoodOrderAlert')->once()->andReturn(null);
        $this->mockTelegram->shouldReceive('sendFoodOrderAlert')->once()->andReturn(null);

        $deliveryDate = Carbon::today()->format('Y-m-d');
        $deliveryTime = '08:30';

        $response = $this->actingAs($this->customer)->postJson(route('dining.order'), [
            'customer_name' => 'Rahul Verma',
            'customer_phone' => '+91 98765 00001',
            'branch_id' => $this->branch->id,
            'order_type' => 'room_service',
            'table_number' => 'Villa 102',
            'delivery_date' => $deliveryDate,
            'delivery_time' => $deliveryTime,
            'special_instructions' => 'Extra coconut chutney',
            'items' => [
                ['id' => $this->menuItem->id, 'quantity' => 2]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('food_orders', [
            'guest_id' => $this->guest->id,
            'order_type' => 'room_service',
            'table_number' => 'Villa 102',
        ]);

        $order = \App\Models\FoodOrder::latest('id')->first();
        $this->assertNotNull($order->scheduled_at);
        $this->assertEquals("{$deliveryDate} 08:30:00", $order->scheduled_at->format('Y-m-d H:i:s'));
        $this->assertStringContainsString('Scheduled for', $order->special_instructions);
        $this->assertStringContainsString('Extra coconut chutney', $order->special_instructions);
    }

    public function test_quick_order_dining_dispatches_notifications_and_saves_schedule()
    {
        $reservation = Reservation::create([
            'booking_code' => 'KR-TEST-102',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'created_by' => $this->customer->id,
            'check_in_date' => Carbon::today()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'rooms_count' => 1,
            'nightly_rate' => 5000.00,
            'subtotal' => 5000.00,
            'total_amount' => 5600.00,
            'status' => 'checked_in',
            'payment_status' => 'paid',
        ]);

        $this->mockEmail->shouldReceive('sendFoodOrderAlert')->once()->andReturn(null);
        $this->mockTelegram->shouldReceive('sendFoodOrderAlert')->once()->andReturn(null);

        $response = $this->actingAs($this->customer)->postJson(route('customer.quick-order-dining'), [
            'reservation_id' => $reservation->id,
            'delivery_date' => Carbon::today()->format('Y-m-d'),
            'delivery_time' => '19:45',
            'payment_choice' => 'charge_to_room',
            'special_instructions' => 'Warm water alongside',
            'items' => [
                ['id' => $this->menuItem->id, 'quantity' => 1]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $order = \App\Models\FoodOrder::latest('id')->first();
        $this->assertNotNull($order->scheduled_at);
        $this->assertEquals(Carbon::today()->format('Y-m-d') . ' 19:45:00', $order->scheduled_at->format('Y-m-d H:i:s'));
        $this->assertStringContainsString('Scheduled for', $order->special_instructions);
    }

    public function test_quick_order_spices_dispatches_notifications()
    {
        $this->mockEmail->shouldReceive('sendSpiceOrderAlert')->once()->andReturn(null);
        $this->mockTelegram->shouldReceive('sendSpiceOrderAlert')->once()->andReturn(null);

        $response = $this->actingAs($this->customer)->postJson(route('customer.quick-order-spices'), [
            'delivery_mode' => 'courier',
            'items' => [
                ['id' => $this->spiceProduct->id, 'quantity' => 2, 'type' => 'packet']
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('spice_orders', [
            'user_id' => $this->customer->id,
            'delivery_mode' => 'courier',
        ]);
    }

    public function test_extend_stay_dispatches_notifications_and_creates_record()
    {
        $reservation = Reservation::create([
            'booking_code' => 'KR-TEST-103',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'created_by' => $this->customer->id,
            'check_in_date' => Carbon::today()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'rooms_count' => 1,
            'nightly_rate' => 5000.00,
            'subtotal' => 5000.00,
            'total_amount' => 5600.00,
            'status' => 'checked_in',
            'payment_status' => 'paid',
        ]);

        $this->mockEmail->shouldReceive('sendStayExtensionAlert')->once()->andReturn(null);
        $this->mockTelegram->shouldReceive('sendStayExtensionAlert')->once()->andReturn(null);

        $newCheckout = Carbon::today()->addDays(3)->format('Y-m-d');

        $response = $this->actingAs($this->customer)->postJson(route('customer.extend-stay'), [
            'reservation_id' => $reservation->id,
            'new_checkout_date' => $newCheckout,
            'notes' => 'Would love to extend our holiday in this peaceful estate.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('stay_extension_requests', [
            'reservation_id' => $reservation->id,
            'extra_nights' => 2,
            'status' => 'pending',
        ]);
    }

    public function test_facility_booking_dispatches_notifications()
    {
        $reservation = Reservation::create([
            'booking_code' => 'KR-TEST-104',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'created_by' => $this->customer->id,
            'check_in_date' => Carbon::today()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'rooms_count' => 1,
            'nightly_rate' => 5000.00,
            'subtotal' => 5000.00,
            'total_amount' => 5600.00,
            'status' => 'checked_in',
            'payment_status' => 'paid',
        ]);

        $this->mockEmail->shouldReceive('sendFacilityBookingAlert')->once()->andReturn(null);
        $this->mockTelegram->shouldReceive('sendFacilityBookingAlert')->once()->andReturn(null);

        $response = $this->actingAs($this->customer)->postJson(route('customer.facilities.book'), [
            'reservation_id' => $reservation->id,
            'facility_id' => $this->facility->id,
            'booking_date' => Carbon::today()->format('Y-m-d'),
            'guests_count' => 2,
            'notes' => 'Herbal oil preference',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('facility_bookings', [
            'reservation_id' => $reservation->id,
            'facility_id' => $this->facility->id,
            'guests_count' => 2,
        ]);
    }

    public function test_facility_adding_dispatches_notifications()
    {
        $admin = User::create([
            'name' => 'General Manager',
            'email' => 'gm@krishnacottages.com',
            'password' => bcrypt('AdminPass123!'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->mockEmail->shouldReceive('sendFacilityAddedAlert')->once()->andReturn(null);
        $this->mockTelegram->shouldReceive('sendFacilityAddedAlert')->once()->andReturn(null);

        $response = $this->actingAs($admin)->postJson(route('admin.facilities.store'), [
            'branch_id' => $this->branch->id,
            'name' => 'Sunset Bamboo Infinity Pool',
            'category' => 'recreation',
            'is_bookable' => false,
            'short_description' => 'Heated estate infinity pool overlooking Chembra Peak.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('facilities', [
            'name' => 'Sunset Bamboo Infinity Pool',
            'category' => 'recreation',
        ]);
    }

    public function test_taxi_request_dispatches_notifications()
    {
        $reservation = Reservation::create([
            'booking_code' => 'KR-TEST-105',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'created_by' => $this->customer->id,
            'check_in_date' => Carbon::today()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'rooms_count' => 1,
            'nightly_rate' => 5000.00,
            'subtotal' => 5000.00,
            'total_amount' => 5600.00,
            'status' => 'checked_in',
            'payment_status' => 'paid',
        ]);

        $location = NearbyLocation::create([
            'branch_id' => $this->branch->id,
            'name' => 'Banasura Sagar Dam',
            'category' => 'attraction',
            'distance_km' => 14.5,
            'is_taxi_available' => true,
        ]);

        $this->mockEmail->shouldReceive('sendTaxiRequestAlert')->once()->andReturn(null);
        $this->mockTelegram->shouldReceive('sendTaxiRequestAlert')->once()->andReturn(null);

        $response = $this->actingAs($this->customer)->postJson(route('customer.taxi.book'), [
            'reservation_id' => $reservation->id,
            'pickup_date' => Carbon::today()->format('Y-m-d'),
            'pickup_time' => '10:00 AM',
            'passengers_count' => 2,
            'selected_location_ids' => [$location->id],
            'extra_locations_notes' => 'Scenic photo stops please',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('taxi_requests', [
            'reservation_id' => $reservation->id,
            'passengers_count' => 2,
        ]);
    }
}
