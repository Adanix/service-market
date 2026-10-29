<?php

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

test('docs page returns 200 and contains all twelve required headings', function () {
    $response = $this->get(route('docs'))->assertStatus(200);

    $requiredHeadings = [
        '1. Executive summary',
        '2. Domain model and entity relationships',
        '3. Geographic scope (Republic of Ireland)',
        '4. User roles and authorization',
        '5. Provider lifecycle and service catalog',
        '6. Booking and payment lifecycle',
        '7. Money handling and payout mechanics',
        '8. Audit trail and moderation',
        '9. Architecture and technology stack',
        '10. Seeded demo data and test personas',
        '11. Demo testing guide',
        '12. Extensibility and custom development roadmap',
    ];

    foreach ($requiredHeadings as $heading) {
        $response->assertSee($heading);
    }

    // Asserts demo personas are listed
    $response->assertSee('Aoife Brennan');
    $response->assertSee('Liam O\'Connor');
    $response->assertSee('Maria Silva');
    $response->assertSee('Emma Walsh');
    $response->assertSee('Jack Murphy');
});
