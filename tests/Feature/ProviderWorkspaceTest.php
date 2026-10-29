<?php

use App\Enums\ServiceStatus;
use App\Models\Category;
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

test('provider can view services list', function () {
    $provider = User::where('email', 'liam@demo.test')->firstOrFail();

    $this->actingAs($provider)
        ->get(route('provider.services.index'))
        ->assertStatus(200)
        ->assertSee('Standard home clean');
});

test('provider can create draft service', function () {
    $provider = User::where('email', 'liam@demo.test')->firstOrFail();
    $category = Category::firstOrFail();

    $this->actingAs($provider)->post(route('provider.services.store'), [
        'category_id' => $category->id,
        'title' => 'Carpet Steam Cleaning Special',
        'price' => '65.00',
        'duration_minutes' => 90,
        'description' => 'Comprehensive carpet steam cleaning using professional hot water extraction units.',
        'inclusions' => "Hallway\nLiving room\nStain pre-treatment",
        'exclusions' => 'Furniture moving',
        'action' => 'draft',
    ])->assertRedirect(route('provider.services.index'));

    $service = Service::where('title', 'Carpet Steam Cleaning Special')->first();
    expect($service)->not->toBeNull();
    expect($service->status)->toBe(ServiceStatus::Draft);
    expect($service->price_cents)->toBe(6500);
});

test('submitting service sets status to pending review', function () {
    $provider = User::where('email', 'liam@demo.test')->firstOrFail();
    $service = Service::where('provider_profile_id', $provider->providerProfile->id)
        ->where('status', ServiceStatus::Draft)
        ->first();

    if (!$service) {
        $service = Service::create([
            'provider_profile_id' => $provider->providerProfile->id,
            'category_id' => Category::first()->id,
            'title' => 'Window Cleaning Package',
            'slug' => 'window-cleaning-package',
            'description' => 'Detailed window cleaning inside and out.',
            'price_cents' => 5000,
            'currency' => 'EUR',
            'duration_minutes' => 60,
            'county_id' => $provider->providerProfile->county_id,
            'status' => ServiceStatus::Draft,
        ]);
    }

    $this->actingAs($provider)
        ->post(route('provider.services.submit', $service->id))
        ->assertRedirect(route('provider.services.index'));

    $service->refresh();
    expect($service->status)->toBe(ServiceStatus::PendingReview);
});

test('editing an active service resets status to pending review', function () {
    $provider = User::where('email', 'liam@demo.test')->firstOrFail();
    $service = Service::where('provider_profile_id', $provider->providerProfile->id)
        ->where('status', ServiceStatus::Active)
        ->firstOrFail();

    $this->actingAs($provider)->put(route('provider.services.update', $service->id), [
        'category_id' => $service->category_id,
        'title' => $service->title . ' Updated',
        'price' => '95.00',
        'duration_minutes' => 120,
        'description' => 'Comprehensive updated service description with more than forty characters for validation.',
        'action' => 'save',
    ])->assertRedirect(route('provider.services.index'));

    $service->refresh();
    expect($service->status)->toBe(ServiceStatus::PendingReview);
    expect($service->title)->toContain('Updated');
});

test('provider can view earnings and payout records', function () {
    $provider = User::where('email', 'maria@demo.test')->firstOrFail();

    $this->actingAs($provider)
        ->get(route('provider.earnings'))
        ->assertStatus(200)
        ->assertSee('PO-4F2K1');
});
