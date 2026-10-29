<?php

namespace Database\Seeders;

use App\Enums\ProviderStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\County;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAndProviderSeeder extends Seeder
{
    public function run(): void
    {
        $dublin = County::where('slug', 'dublin')->firstOrFail();
        $cork = County::where('slug', 'cork')->firstOrFail();
        $galway = County::where('slug', 'galway')->firstOrFail();
        $limerick = County::where('slug', 'limerick')->firstOrFail();

        // 1. Aoife Brennan (Admin)
        User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Aoife Brennan',
                'password' => Hash::make(Str::random(32)),
                'role' => UserRole::Admin,
                'status' => UserStatus::Active,
                'county_id' => $dublin->id,
                'phone' => '+353 1 234 5678',
            ]
        );

        // 2. Liam O'Connor (Provider - Approved - Dublin)
        $liam = User::updateOrCreate(
            ['email' => 'liam@demo.test'],
            [
                'name' => "Liam O'Connor",
                'password' => Hash::make(Str::random(32)),
                'role' => UserRole::Provider,
                'status' => UserStatus::Active,
                'county_id' => $dublin->id,
                'phone' => '+353 1 987 6543',
            ]
        );
        ProviderProfile::updateOrCreate(
            ['user_id' => $liam->id],
            [
                'business_name' => "O'Connor Home Cleaning",
                'slug' => 'oconnor-home-cleaning',
                'bio' => 'Professional home and office cleaning service based in Dublin with over ten years of experience in domestic and commercial sanitization.',
                'county_id' => $dublin->id,
                'status' => ProviderStatus::Approved,
                'approved_at' => now()->subMonths(6),
            ]
        );

        // 3. Maria Silva (Provider - Approved - Cork)
        $maria = User::updateOrCreate(
            ['email' => 'maria@demo.test'],
            [
                'name' => 'Maria Silva',
                'password' => Hash::make(Str::random(32)),
                'role' => UserRole::Provider,
                'status' => UserStatus::Active,
                'county_id' => $cork->id,
                'phone' => '+353 21 456 7890',
            ]
        );
        ProviderProfile::updateOrCreate(
            ['user_id' => $maria->id],
            [
                'business_name' => 'Silva Garden Care',
                'slug' => 'silva-garden-care',
                'bio' => 'Dedicated landscaping and seasonal garden care across County Cork. We provide reliable maintenance, hedge cutting, and lawn treatments.',
                'county_id' => $cork->id,
                'status' => ProviderStatus::Approved,
                'approved_at' => now()->subMonths(4),
            ]
        );

        // 4. Tomas Novak (Provider - Pending - Galway)
        $tomas = User::updateOrCreate(
            ['email' => 'tomas@demo.test'],
            [
                'name' => 'Tomas Novak',
                'password' => Hash::make(Str::random(32)),
                'role' => UserRole::Provider,
                'status' => UserStatus::Active,
                'county_id' => $galway->id,
                'phone' => '+353 91 234 8901',
            ]
        );
        ProviderProfile::updateOrCreate(
            ['user_id' => $tomas->id],
            [
                'business_name' => 'Novak Plumbing',
                'slug' => 'novak-plumbing',
                'bio' => 'Certified plumbing specialist in Galway providing rapid emergency callouts, radiator installations, and pipe repair work.',
                'county_id' => $galway->id,
                'status' => ProviderStatus::Pending,
                'approved_at' => null,
            ]
        );

        // 5. Sinead Byrne (Provider - Suspended - Limerick)
        $sinead = User::updateOrCreate(
            ['email' => 'sinead@demo.test'],
            [
                'name' => 'Sinead Byrne',
                'password' => Hash::make(Str::random(32)),
                'role' => UserRole::Provider,
                'status' => UserStatus::Suspended,
                'county_id' => $limerick->id,
                'phone' => '+353 61 789 0123',
            ]
        );
        ProviderProfile::updateOrCreate(
            ['user_id' => $sinead->id],
            [
                'business_name' => 'Byrne Repair',
                'slug' => 'byrne-repair',
                'bio' => 'General household and furniture repair service based in Limerick.',
                'county_id' => $limerick->id,
                'status' => ProviderStatus::Suspended,
                'suspension_reason' => 'Account under administrative review.',
                'approved_at' => now()->subMonths(8),
            ]
        );

        // 6. Emma Walsh (Customer - Active - Dublin)
        User::updateOrCreate(
            ['email' => 'emma@demo.test'],
            [
                'name' => 'Emma Walsh',
                'password' => Hash::make(Str::random(32)),
                'role' => UserRole::Customer,
                'status' => UserStatus::Active,
                'county_id' => $dublin->id,
                'phone' => '+353 87 111 2233',
            ]
        );

        // 7. Jack Murphy (Customer - Active - Cork)
        User::updateOrCreate(
            ['email' => 'jack@demo.test'],
            [
                'name' => 'Jack Murphy',
                'password' => Hash::make(Str::random(32)),
                'role' => UserRole::Customer,
                'status' => UserStatus::Active,
                'county_id' => $cork->id,
                'phone' => '+353 86 444 5566',
            ]
        );
    }
}
