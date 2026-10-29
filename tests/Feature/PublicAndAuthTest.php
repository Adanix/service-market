<?php

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

test('public pages are accessible to guests', function () {
    $this->get('/')->assertStatus(200);
    $this->get('/docs')->assertStatus(200);
    $this->get('/services')->assertStatus(200);
    $this->get('/help')->assertStatus(200);
    $this->get('/terms')->assertStatus(200);
    $this->get('/privacy')->assertStatus(200);
    $this->get('/refunds')->assertStatus(200);
    $this->get('/demo/sign-in')->assertStatus(200);
});

test('passwordless demo sign in works for each role', function () {
    // 1. Customer sign in
    $customer = User::where('email', 'emma@demo.test')->firstOrFail();
    $this->post(route('demo.sign-in.as', $customer->id))
        ->assertRedirect(route('customer.dashboard'));
    $this->assertAuthenticatedAs($customer);

    // 2. Provider sign in
    $provider = User::where('email', 'liam@demo.test')->firstOrFail();
    $this->post(route('demo.sign-in.as', $provider->id))
        ->assertRedirect(route('provider.dashboard'));
    $this->assertAuthenticatedAs($provider);

    // 3. Admin sign in
    $admin = User::where('email', 'admin@demo.test')->firstOrFail();
    $this->post(route('demo.sign-in.as', $admin->id))
        ->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);
});

test('suspended demo account cannot sign in', function () {
    $suspended = User::where('email', 'sinead@demo.test')->firstOrFail();
    $this->post(route('demo.sign-in.as', $suspended->id))
        ->assertRedirect(route('demo.sign-in'))
        ->assertSessionHas('error');
    $this->assertGuest();
});

test('role middleware prevents unauthorized workspace cross-access', function () {
    $customer = User::where('email', 'emma@demo.test')->firstOrFail();
    $provider = User::where('email', 'liam@demo.test')->firstOrFail();

    // Customer cannot access provider or admin
    $this->actingAs($customer)->get('/provider/dashboard')->assertStatus(403);
    $this->actingAs($customer)->get('/admin/dashboard')->assertStatus(403);

    // Provider cannot access customer or admin
    $this->actingAs($provider)->get('/customer/dashboard')->assertStatus(403);
    $this->actingAs($provider)->get('/admin/dashboard')->assertStatus(403);
});

test('demo sign out invalidates session and redirects home', function () {
    $customer = User::where('email', 'emma@demo.test')->firstOrFail();

    $this->actingAs($customer)
        ->post(route('demo.sign-out'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});
