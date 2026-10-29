<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CountySeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\UserAndProviderSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\BookingAndPaymentSeeder;
use Database\Seeders\PayoutSeeder;
use Illuminate\Support\Facades\Artisan;

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

test('provider accepts pending booking request', function () {
    $booking = Booking::where('status', BookingStatus::PendingProvider)->firstOrFail();
    $providerUser = $booking->providerProfile->user;

    $this->actingAs($providerUser)
        ->post(route('provider.bookings.accept', $booking->reference))
        ->assertRedirect(route('provider.bookings.show', $booking->reference));

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Accepted);
    expect($booking->accepted_at)->not->toBeNull();
    expect($booking->payment->status)->toBe(PaymentStatus::Authorized);
});

test('provider declines pending booking with reason and voids authorization', function () {
    $booking = Booking::where('status', BookingStatus::PendingProvider)->firstOrFail();
    $providerUser = $booking->providerProfile->user;

    $this->actingAs($providerUser)
        ->post(route('provider.bookings.decline', $booking->reference), [
            'reason' => 'Schedule fully booked at this time slot.',
        ])
        ->assertRedirect(route('provider.bookings.show', $booking->reference));

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Declined);
    expect($booking->decline_reason)->toBe('Schedule fully booked at this time slot.');
    expect($booking->payment->status)->toBe(PaymentStatus::Voided);
});

test('provider marks accepted booking as in progress', function () {
    $booking = Booking::where('status', BookingStatus::Accepted)->firstOrFail();
    $providerUser = $booking->providerProfile->user;

    $this->actingAs($providerUser)
        ->post(route('provider.bookings.start', $booking->reference))
        ->assertRedirect(route('provider.bookings.show', $booking->reference));

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::InProgress);
    expect($booking->started_at)->not->toBeNull();
});

test('provider marks in progress booking as complete and sets auto complete deadline', function () {
    $booking = Booking::where('status', BookingStatus::InProgress)->firstOrFail();
    $providerUser = $booking->providerProfile->user;

    $this->actingAs($providerUser)
        ->post(route('provider.bookings.complete', $booking->reference))
        ->assertRedirect(route('provider.bookings.show', $booking->reference));

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::AwaitingConfirmation);
    expect($booking->provider_completed_at)->not->toBeNull();
    expect($booking->auto_complete_at)->not->toBeNull();
});

test('customer confirms completion and captures payment', function () {
    $booking = Booking::where('status', BookingStatus::AwaitingConfirmation)->firstOrFail();
    $customer = $booking->customer;

    $this->actingAs($customer)
        ->post(route('customer.bookings.confirm', $booking->reference))
        ->assertRedirect(route('customer.bookings.show', $booking->reference));

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Completed);
    expect($booking->customer_confirmed_at)->not->toBeNull();
    expect($booking->payment->status)->toBe(PaymentStatus::Captured);
});

test('customer cancels accepted booking and releases authorization hold', function () {
    $booking = Booking::where('status', BookingStatus::Accepted)->firstOrFail();
    $customer = $booking->customer;

    $this->actingAs($customer)
        ->post(route('customer.bookings.cancel', $booking->reference), [
            'reason' => 'Change of personal schedule.',
        ])
        ->assertRedirect(route('customer.bookings.show', $booking->reference));

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Cancelled);
    expect($booking->cancelled_by)->toBe('customer');
    expect($booking->payment->status)->toBe(PaymentStatus::Voided);
});

test('auto complete command captures payments for overdue awaiting confirmation bookings', function () {
    $booking = Booking::where('status', BookingStatus::AwaitingConfirmation)->firstOrFail();
    // Simulate auto_complete_at in the past
    $booking->update(['auto_complete_at' => now()->subHour()]);

    Artisan::call('bookings:auto-complete');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Completed);
    expect($booking->payment->status)->toBe(PaymentStatus::Captured);
});

test('expire authorizations command voids expired payment holds', function () {
    $booking = Booking::where('status', BookingStatus::PendingProvider)->firstOrFail();
    // Simulate authorization expired in the past
    $booking->payment->update(['capture_before' => now()->subHour()]);

    Artisan::call('bookings:expire-authorizations');

    $booking->refresh();
    expect($booking->status)->toBe(BookingStatus::Expired);
    expect($booking->payment->status)->toBe(PaymentStatus::Voided);
});
