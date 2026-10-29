<?php

use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Enums\ProviderStatus;
use App\Enums\ServiceStatus;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CountySeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\UserAndProviderSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\BookingAndPaymentSeeder;
use Database\Seeders\PayoutSeeder;

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

test('admin can approve and suspend providers', function () {
    $admin = User::where('email', 'admin@demo.test')->firstOrFail();
    $providerProfile = ProviderProfile::where('status', ProviderStatus::Pending)->first();

    if (!$providerProfile) {
        $providerProfile = ProviderProfile::where('status', ProviderStatus::Suspended)->firstOrFail();
    }

    // 1. Approve
    $this->actingAs($admin)
        ->post(route('admin.providers.approve', $providerProfile->id))
        ->assertRedirect();

    $providerProfile->refresh();
    expect($providerProfile->status)->toBe(ProviderStatus::Approved);

    // 2. Suspend with reason
    $this->actingAs($admin)
        ->post(route('admin.providers.suspend', $providerProfile->id), [
            'reason' => 'Failed trade documentation audit.',
        ])
        ->assertRedirect();

    $providerProfile->refresh();
    expect($providerProfile->status)->toBe(ProviderStatus::Suspended);

    // Check audit log
    expect(AuditLog::where('action', 'provider.suspended')->exists())->toBeTrue();
});

test('admin can approve and reject services with moderation notes', function () {
    $admin = User::where('email', 'admin@demo.test')->firstOrFail();
    $service = Service::where('status', ServiceStatus::PendingReview)->first();

    if (!$service) {
        $service = Service::firstOrFail();
        $service->update(['status' => ServiceStatus::PendingReview]);
    }

    // 1. Reject
    $this->actingAs($admin)
        ->post(route('admin.services.reject', $service->id), [
            'note' => 'Please provide clear inclusion details and specify pricing scope.',
        ])
        ->assertRedirect();

    $service->refresh();
    expect($service->status)->toBe(ServiceStatus::Rejected);
    expect($service->moderation_note)->toBe('Please provide clear inclusion details and specify pricing scope.');

    // 2. Approve
    $this->actingAs($admin)
        ->post(route('admin.services.approve', $service->id))
        ->assertRedirect();

    $service->refresh();
    expect($service->status)->toBe(ServiceStatus::Active);
    expect($service->moderation_note)->toBeNull();
});

test('admin can capture and void payment authorizations', function () {
    $admin = User::where('email', 'admin@demo.test')->firstOrFail();

    // 1. Capture payment ready for capture (awaiting confirmation or completed)
    $paymentToCapture = Payment::where('status', PaymentStatus::Authorized)
        ->whereHas('booking', fn($q) => $q->whereIn('status', [\App\Enums\BookingStatus::AwaitingConfirmation, \App\Enums\BookingStatus::Completed]))
        ->first();

    if ($paymentToCapture) {
        $this->actingAs($admin)
            ->post(route('admin.payments.capture', $paymentToCapture->id))
            ->assertRedirect();

        $paymentToCapture->refresh();
        expect($paymentToCapture->status)->toBe(PaymentStatus::Captured);
        expect(AuditLog::where('action', 'payment.captured')->exists())->toBeTrue();
    }

    // 2. Void authorized payment
    $paymentToVoid = Payment::where('status', PaymentStatus::Authorized)
        ->whereHas('booking', fn($q) => $q->where('status', '!=', \App\Enums\BookingStatus::Completed))
        ->firstOrFail();

    $this->actingAs($admin)
        ->post(route('admin.payments.void', $paymentToVoid->id), [
            'reason' => 'Administrative cancellation.',
        ])
        ->assertRedirect();

    $paymentToVoid->refresh();
    expect($paymentToVoid->status)->toBe(PaymentStatus::Voided);
    expect(AuditLog::where('action', 'payment.voided')->exists())->toBeTrue();
});

test('admin can refund captured payment with reason', function () {
    $admin = User::where('email', 'admin@demo.test')->firstOrFail();
    $payment = Payment::where('status', PaymentStatus::Captured)->firstOrFail();

    $this->actingAs($admin)
        ->post(route('admin.payments.refund', $payment->id), [
            'reason' => 'Customer verified incomplete cleaning of kitchen.',
        ])
        ->assertRedirect();

    $payment->refresh();
    expect($payment->status)->toBe(PaymentStatus::Refunded);
    expect($payment->booking->status)->toBe(\App\Enums\BookingStatus::Cancelled);
    expect(AuditLog::where('action', 'payment.refunded')->exists())->toBeTrue();
});

test('admin can batch payout and mark as paid', function () {
    $admin = User::where('email', 'admin@demo.test')->firstOrFail();
    $payout = Payout::where('status', PayoutStatus::Pending)->first();

    if (!$payout) {
        $provider = ProviderProfile::firstOrFail();
        $payout = Payout::create([
            'reference' => 'PO-TEST01',
            'provider_profile_id' => $provider->id,
            'period_start' => now()->subDays(14)->toDateString(),
            'period_end' => now()->toDateString(),
            'amount_cents' => 15000,
            'status' => PayoutStatus::Pending,
        ]);
    }

    $this->actingAs($admin)
        ->post(route('admin.payouts.mark-paid', $payout->id))
        ->assertRedirect();

    $payout->refresh();
    expect($payout->status)->toBe(PayoutStatus::Paid);
    expect($payout->paid_at)->not->toBeNull();
    expect(AuditLog::where('action', 'payout.marked_paid')->exists())->toBeTrue();
});

test('admin can update platform settings', function () {
    $admin = User::where('email', 'admin@demo.test')->firstOrFail();

    $this->actingAs($admin)->put(route('admin.settings.update'), [
        'marketplace_name' => 'ServiceLocal Ireland',
        'support_email' => 'help@servicelocal.test',
        'platform_fee_bps' => 1250,
        'authorization_days' => 5,
        'auto_complete_hours' => 48,
        'min_notice_hours' => 12,
        'max_advance_days' => 60,
        'reminder_hours' => 12,
    ])->assertRedirect();

    $settings = app(\App\Services\SettingsService::class);
    $settings->clearCache();

    expect($settings->marketplaceName())->toBe('ServiceLocal Ireland');
    expect($settings->platformFeeBps())->toBe(1250);
    expect($settings->autoCompleteHours())->toBe(48);
    expect(AuditLog::where('action', 'settings.updated')->exists())->toBeTrue();
});
