<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CountySeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\UserAndProviderSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\BookingAndPaymentSeeder;
use Database\Seeders\PayoutSeeder;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed([
        CountySeeder::class,
        CategorySeeder::class,
        SettingSeeder::class,
        UserAndProviderSeeder::class,
        ServiceSeeder::class,
        BookingAndPaymentSeeder::class,
        PayoutSeeder::class,
    ]);
});

test('customer can view checkout for an active service', function () {
    $customer = User::where('email', 'emma@demo.test')->firstOrFail();
    $service = Service::where('slug', 'standard-home-clean')->firstOrFail();

    $this->actingAs($customer)
        ->get(route('checkout.show', $service->slug))
        ->assertStatus(200)
        ->assertSee($service->title);
});

test('provider cannot book their own service', function () {
    $provider = User::where('email', 'liam@demo.test')->firstOrFail();
    $service = Service::where('provider_profile_id', $provider->providerProfile->id)->firstOrFail();

    $this->actingAs($provider)
        ->get(route('checkout.show', $service->slug))
        ->assertStatus(403);
});

test('successful card authorizes payment and creates booking in pending_provider status', function () {
    $customer = User::where('email', 'emma@demo.test')->firstOrFail();
    $service = Service::where('slug', 'lawn-mowing')->firstOrFail();
    $idempotencyKey = (string) Str::uuid();

    $response = $this->actingAs($customer)->post(route('checkout.process', $service->slug), [
        'preferred_start_at' => now()->addDays(2)->format('Y-m-d H:i'),
        'customer_note' => 'Please ring front doorbell on arrival.',
        'cardholder_name' => 'Emma Walsh',
        'card_number' => '4242424242424242',
        'card_expiry' => '12/28',
        'card_cvc' => '123',
        'accept_terms' => 1,
        'idempotency_key' => $idempotencyKey,
    ]);

    $payment = Payment::where('idempotency_key', $idempotencyKey)->first();
    expect($payment)->not->toBeNull();
    expect($payment->status)->toBe(PaymentStatus::Authorized);
    expect($payment->card_brand)->toBe('visa');
    expect($payment->card_last4)->toBe('4242');

    $booking = $payment->booking;
    expect($booking)->not->toBeNull();
    expect($booking->status)->toBe(BookingStatus::PendingProvider);
    expect($booking->price_cents)->toBe($service->price_cents);

    $response->assertRedirect(route('customer.bookings.show', $booking->reference));
});

test('declined card returns error and creates no booking or payment', function () {
    $customer = User::where('email', 'emma@demo.test')->firstOrFail();
    $service = Service::where('slug', 'lawn-mowing')->firstOrFail();
    $idempotencyKey = (string) Str::uuid();

    $response = $this->actingAs($customer)->post(route('checkout.process', $service->slug), [
        'preferred_start_at' => now()->addDays(2)->format('Y-m-d H:i'),
        'cardholder_name' => 'Emma Walsh',
        'card_number' => '4000000000000002', // Simulated declined card
        'card_expiry' => '12/28',
        'card_cvc' => '123',
        'accept_terms' => 1,
        'idempotency_key' => $idempotencyKey,
    ]);

    $response->assertSessionHasErrors(['card_number']);
    expect(Payment::where('idempotency_key', $idempotencyKey)->exists())->toBeFalse();
});

test('idempotency key prevents double charge on duplicate submission', function () {
    $customer = User::where('email', 'emma@demo.test')->firstOrFail();
    $service = Service::where('slug', 'lawn-mowing')->firstOrFail();
    $idempotencyKey = (string) Str::uuid();

    $payload = [
        'preferred_start_at' => now()->addDays(2)->format('Y-m-d H:i'),
        'cardholder_name' => 'Emma Walsh',
        'card_number' => '4242424242424242',
        'card_expiry' => '12/28',
        'card_cvc' => '123',
        'accept_terms' => 1,
        'idempotency_key' => $idempotencyKey,
    ];

    // First request
    $this->actingAs($customer)->post(route('checkout.process', $service->slug), $payload);

    $countPayments = Payment::where('idempotency_key', $idempotencyKey)->count();
    expect($countPayments)->toBe(1);

    // Second request with same key
    $secondResponse = $this->actingAs($customer)->post(route('checkout.process', $service->slug), $payload);
    $secondResponse->assertRedirect();

    // Verify still exactly 1 payment
    expect(Payment::where('idempotency_key', $idempotencyKey)->count())->toBe(1);
});
