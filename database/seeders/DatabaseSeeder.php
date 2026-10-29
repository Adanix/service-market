<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountySeeder::class,
            CategorySeeder::class,
            SettingSeeder::class,
            UserAndProviderSeeder::class,
            ServiceSeeder::class,
            BookingAndPaymentSeeder::class,
            PayoutSeeder::class,
            ContactMessageSeeder::class,
        ]);
    }
}
