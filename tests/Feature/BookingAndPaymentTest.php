<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\SpiceCategory;
use App\Models\SpiceProduct;
use App\Models\User;
use App\Services\EmailNotificationService;
use App\Services\RazorpayService;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class BookingAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected RoomType $roomType;
    protected Room $room;
    protected User $customer;
    protected Guest $guest;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure mock or test credentials for Razorpay
        Setting::set('razorpay_key_id', 'rzp_test_validKeyId123', 'payment');
        Setting::set('razorpay_key_secret', 'testSecretKey456', 'payment');

        // Disable actual external network calls for Email and Telegram in tests
        $mockEmail = Mockery::mock(EmailNotificationService::class)->makePartial();
        $mockEmail->shouldReceive('sendBookingAlert')->andReturn(null);
        $mockEmail->shouldReceive('sendFoodOrderAlert')->andReturn(null);
        $mockEmail->shouldReceive('sendSpiceOrderAlert')->andReturn(null);
        $this->app->instance(EmailNotificationService::class, $mockEmail);

        $mockTelegram = Mockery::mock(TelegramNotificationService::class)->makePartial();
        $mockTelegram->shouldReceive('sendBookingAlert')->andReturn(null);
        $mockTelegram->shouldReceive('sendFoodOrderAlert')->andReturn(null);
        $mockTelegram->shouldReceive('sendSpiceOrderAlert')->andReturn(null);
        $this->app->instance(TelegramNotificationService::class, $mockTelegram);

        // Seed basic operational hierarchy
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
            'name' => 'Heritage Teak Villa',
            'slug' => 'heritage-teak-villa',
            'code' => 'HTV',
            'base_price' => 4500.00,
            'weekend_price' => 5200.00,
            'max_guests' => 3,
            'max_adults' => 2,
            'max_children' => 1,
            'is_active' => true,
            'is_bookable' => true,
        ]);

        $this->room = Room::create([
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '101',
            'operational_status' => 'available',
            'housekeeping_status' => 'clean',
        ]);

        $this->customer = User::create([
            'name' => 'Aditi Sharma',
            'email' => 'aditi.sharma@example.com',
            'password' => bcrypt('Password123!'),
            'role' => 'customer',
            'phone' => '+91 98765 43210',
            'is_active' => true,
        ]);

        $this->guest = Guest::create([
            'user_id' => $this->customer->id,
            'first_name' => 'Aditi',
            'last_name' => 'Sharma',
            'email' => 'aditi.sharma@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Bengaluru',
            'state' => 'Karnataka',
            'country' => 'India',
        ]);
    }

    /**
     * Test 1: Availability Check API returns available when room is free.
     */
    public function test_availability_check_api_returns_available_for_free_dates(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $response = $this->postJson(route('api.availability.check'), [
            'room_type_id' => $this->roomType->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'adults' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 1,
            'nights' => 2,
        ]);
        $this->assertEquals(1, $response->json('results.0.available_rooms_count'));
    }

    /**
     * Test 2: Availability Check API detects conflicting reservation and marks unavailable.
     */
    public function test_availability_check_api_returns_unavailable_when_dates_overlap(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        // Create an existing conflicting reservation
        Reservation::create([
            'booking_code' => 'KR-EXISTING-001',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'rooms_count' => 1,
            'nightly_rate' => 4500.00,
            'subtotal' => 9000.00,
            'tax_amount' => 1080.00,
            'total_amount' => 10080.00,
            'paid_amount' => 10080.00,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'card',
        ]);

        $response = $this->postJson(route('api.availability.check'), [
            'room_type_id' => $this->roomType->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'adults' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 0,
            'results' => [],
        ]);
    }

    /**
     * Test 3: Razorpay Order Creation API creates valid order for available room.
     */
    public function test_razorpay_order_creation_for_available_room(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        // Fake the Razorpay API endpoint
        Http::fake([
            'https://api.razorpay.com/v1/orders' => Http::response([
                'id' => 'order_KRH123456789',
                'entity' => 'order',
                'amount' => 1008000, // in paise: ₹10,080.00
                'amount_paid' => 0,
                'amount_due' => 1008000,
                'currency' => 'INR',
                'receipt' => 'KR-TEST-001',
                'status' => 'created',
                'attempts' => 0,
            ], 200),
        ]);

        $response = $this->actingAs($this->customer)->postJson(route('booking.razorpay.create-order'), [
            'room_type_id' => $this->roomType->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'first_name' => 'Aditi',
            'last_name' => 'Sharma',
            'email' => 'aditi.sharma@example.com',
            'phone' => '+91 98765 43210',
            'payment_choice' => 'full',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'order_id' => 'order_KRH123456789',
            'currency' => 'INR',
            'key_id' => 'rzp_test_validKeyId123',
        ]);
    }

    /**
     * Test 4: Razorpay Order Creation is rejected with 422 if room is fully booked.
     */
    public function test_razorpay_order_rejected_when_room_is_fully_booked(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        // Book the single room
        Reservation::create([
            'booking_code' => 'KR-EXISTING-002',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'status' => 'confirmed',
            'nightly_rate' => 4500,
            'subtotal' => 9000,
            'total_amount' => 10080,
        ]);

        $response = $this->actingAs($this->customer)->postJson(route('booking.razorpay.create-order'), [
            'room_type_id' => $this->roomType->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'first_name' => 'Pooja',
            'last_name' => 'Hegde',
            'email' => 'pooja@example.com',
            'phone' => '+91 99999 88888',
            'payment_choice' => 'full',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test 5: Full booking checkout flow with Pay At Resort.
     */
    public function test_booking_store_with_pay_at_resort(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($this->customer)->post(route('booking.store'), [
            'room_type_id' => $this->roomType->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'first_name' => 'Aditi',
            'last_name' => 'Sharma',
            'email' => 'aditi.sharma@example.com',
            'phone' => '+91 98765 43210',
            'city' => 'Bengaluru',
            'country' => 'India',
            'payment_method' => 'pay_at_resort',
            'payment_choice' => 'full',
            'special_requests' => 'Quiet villa near garden requested.',
        ]);

        // Should redirect to confirmation page
        $response->assertStatus(302);
        $this->assertDatabaseHas('reservations', [
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'status' => 'confirmed',
            'payment_status' => 'pending',
            'payment_method' => 'pay_at_resort',
        ]);

        $reservation = Reservation::where('guest_id', $this->guest->id)->latest()->first();
        $this->assertNotNull($reservation);
        $this->assertStringStartsWith('KR-', $reservation->booking_code);
        $this->assertEquals(10080.00, (float) $reservation->total_amount);
    }

    /**
     * Test 6: Booking with Online Razorpay payment and cryptographic signature verification.
     */
    public function test_booking_store_with_valid_razorpay_signature(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $orderId = 'order_validOrder12345';
        $paymentId = 'pay_validPayment67890';
        $secret = Setting::get('razorpay_key_secret');

        // Generate authentic HMAC-SHA256 signature
        $validSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);

        $response = $this->actingAs($this->customer)->post(route('booking.store'), [
            'room_type_id' => $this->roomType->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'first_name' => 'Aditi',
            'last_name' => 'Sharma',
            'email' => 'aditi.sharma@example.com',
            'phone' => '+91 98765 43210',
            'payment_method' => 'upi',
            'payment_choice' => 'full',
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $validSignature,
        ]);

        $response->assertStatus(302);

        // Assert reservation created with paid status
        $this->assertDatabaseHas('reservations', [
            'room_type_id' => $this->roomType->id,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'upi',
        ]);

        // Assert payment recorded in financial ledger
        $this->assertDatabaseHas('payments', [
            'payable_type' => Reservation::class,
            'transaction_id' => $paymentId,
            'payment_method' => 'upi',
            'gateway' => 'razorpay',
            'status' => 'successful',
        ]);
    }

    /**
     * Test 7: Booking fails and rejects transaction if Razorpay signature is invalid/tampered.
     */
    public function test_booking_fails_on_tampered_razorpay_signature(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $response = $this->actingAs($this->customer)->post(route('booking.store'), [
            'room_type_id' => $this->roomType->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'first_name' => 'Aditi',
            'last_name' => 'Sharma',
            'email' => 'aditi.sharma@example.com',
            'phone' => '+91 98765 43210',
            'payment_method' => 'upi',
            'payment_choice' => 'full',
            'razorpay_order_id' => 'order_fake123',
            'razorpay_payment_id' => 'pay_fake123',
            'razorpay_signature' => 'BOGUS_INVALID_SIGNATURE_HASH',
        ]);

        $response->assertSessionHas('error');

        // No reservation should be created
        $this->assertDatabaseMissing('reservations', [
            'room_type_id' => $this->roomType->id,
            'payment_method' => 'upi',
        ]);
    }

    /**
     * Test 8: Simultaneous race condition protection and 100% automated refund handling.
     */
    public function test_simultaneous_booking_race_condition_triggers_automated_refund(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        // Guest A books the only room in this room type
        Reservation::create([
            'booking_code' => 'KR-RACE-WINNER',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'status' => 'confirmed',
            'nightly_rate' => 4500,
            'subtotal' => 9000,
            'total_amount' => 10080,
        ]);

        // Guest B attempts to submit booking for the exact same room & dates with online payment
        $orderId = 'order_race2';
        $paymentId = 'pay_race2';
        $secret = Setting::get('razorpay_key_secret');
        $validSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);

        // Mock refund API on Razorpay
        Http::fake([
            'https://api.razorpay.com/v1/payments/*/refund' => Http::response([
                'id' => 'rfnd_raceRefund123',
                'entity' => 'refund',
                'amount' => 1008000,
                'status' => 'processed',
            ], 200),
        ]);

        $guestBUser = User::create([
            'name' => 'Rahul Verma',
            'email' => 'rahul.verma@example.com',
            'password' => bcrypt('Password123!'),
            'role' => 'customer',
            'phone' => '+91 91111 22222',
        ]);

        $response = $this->actingAs($guestBUser)->post(route('booking.store'), [
            'room_type_id' => $this->roomType->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'first_name' => 'Rahul',
            'last_name' => 'Verma',
            'email' => 'rahul.verma@example.com',
            'phone' => '+91 91111 22222',
            'payment_method' => 'card',
            'payment_choice' => 'full',
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $validSignature,
        ]);

        // Double-booking must be blocked: only 1 reservation should exist in DB
        $this->assertEquals(1, Reservation::where('room_type_id', $this->roomType->id)->count());

        // An automated refund payment record must be created for Guest B
        $this->assertDatabaseHas('payments', [
            'transaction_id' => $paymentId,
            'status' => 'refunded',
            'gateway' => 'razorpay',
        ]);
    }

    /**
     * Test 9: Booking confirmation page loads cleanly with reservation details.
     */
    public function test_booking_confirmation_page_loads_with_details(): void
    {
        $checkIn = Carbon::tomorrow()->format('Y-m-d');
        $checkOut = Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        $reservation = Reservation::create([
            'booking_code' => 'KR-CONFIRM-TEST',
            'branch_id' => $this->branch->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'guest_id' => $this->guest->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'adults' => 2,
            'children' => 0,
            'rooms_count' => 1,
            'nightly_rate' => 4500.00,
            'subtotal' => 9000.00,
            'tax_amount' => 1080.00,
            'total_amount' => 10080.00,
            'paid_amount' => 10080.00,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'card',
        ]);

        $response = $this->actingAs($this->customer)->get(route('booking.confirmation', ['code' => 'KR-CONFIRM-TEST']));

        $response->assertStatus(200);
        $response->assertSee('KR-CONFIRM-TEST');
        $response->assertSee('Heritage Teak Villa');
        $response->assertSee('Wayanad Evergreen Estate');
    }

    /**
     * Test 10: Spice store order placement and checkout flow.
     */
    public function test_spice_store_order_placement(): void
    {
        $category = SpiceCategory::create([
            'name' => 'Fresh Pod Spices',
            'slug' => 'fresh-pod-spices',
            'is_active' => true,
        ]);

        $spice = SpiceProduct::create([
            'spice_category_id' => $category->id,
            'name' => 'Wayanad Cardamom Reserve',
            'slug' => 'wayanad-cardamom-reserve',
            'sku' => 'WCR-200G',
            'price' => 480.00,
            'weight_grams' => 200,
            'stock_quantity' => 50,
            'is_active' => true,
            'is_available' => true,
            'is_published' => true,
            'status' => 'in_stock',
        ]);

        $response = $this->actingAs($this->customer)->post(route('spices.order'), [
            'customer_name' => 'Aditi Sharma',
            'customer_email' => 'aditi.sharma@example.com',
            'customer_phone' => '+91 98765 43210',
            'shipping_address_line1' => 'Villa 101, Krishna Cottages Wayanad',
            'shipping_city' => 'Wayanad',
            'shipping_state' => 'Kerala',
            'shipping_pincode' => '673121',
            'delivery_mode' => 'villa',
            'room_number' => '101',
            'payment_method' => 'charge_to_room',
            'items_json' => json_encode([
                [
                    'id' => $spice->id,
                    'quantity' => 2,
                    'type' => 'packet',
                ]
            ]),
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('spice_orders', [
            'customer_name' => 'Aditi Sharma',
            'delivery_mode' => 'villa',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);
    }
}
