<?php

namespace Database\Seeders;

use App\Enums\ServiceStatus;
use App\Models\Category;
use App\Models\County;
use App\Models\ProviderProfile;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $dublin = County::where('slug', 'dublin')->firstOrFail();
        $cork = County::where('slug', 'cork')->firstOrFail();
        $galway = County::where('slug', 'galway')->firstOrFail();
        $limerick = County::where('slug', 'limerick')->firstOrFail();

        $cleaning = Category::where('slug', 'home-cleaning')->firstOrFail();
        $gardening = Category::where('slug', 'gardening')->firstOrFail();
        $plumbing = Category::where('slug', 'plumbing')->firstOrFail();
        $handyman = Category::where('slug', 'handyman')->firstOrFail();

        $oconnor = ProviderProfile::where('slug', 'oconnor-home-cleaning')->firstOrFail();
        $silva = ProviderProfile::where('slug', 'silva-garden-care')->firstOrFail();
        $novak = ProviderProfile::where('slug', 'novak-plumbing')->firstOrFail();
        $byrne = ProviderProfile::where('slug', 'byrne-repair')->firstOrFail();

        // Liam's Services
        Service::updateOrCreate(
            ['slug' => 'standard-home-clean'],
            [
                'provider_profile_id' => $oconnor->id,
                'category_id' => $cleaning->id,
                'title' => 'Standard home clean',
                'description' => 'A comprehensive general cleaning service designed for regular domestic upkeep. We dust all surfaces, vacuum carpets, mop hard floors, and sanitize kitchen and bathroom facilities thoroughly.',
                'inclusions' => "Dusting and wiping surfaces\nVacuuming and floor mopping\nKitchen and bathroom sanitization",
                'exclusions' => "Inside oven cleaning\nCarpet shampooing",
                'price_cents' => 8500,
                'currency' => 'EUR',
                'duration_minutes' => 120,
                'county_id' => $dublin->id,
                'status' => ServiceStatus::Active,
                'published_at' => now()->subDays(60),
            ]
        );

        Service::updateOrCreate(
            ['slug' => 'deep-clean'],
            [
                'provider_profile_id' => $oconnor->id,
                'category_id' => $cleaning->id,
                'title' => 'Deep clean',
                'description' => 'An intensive top-to-bottom cleaning package suitable for seasonal refreshes or neglected properties. Our team reaches behind appliances, scrubs tile grout, washes skirting boards, and leaves your living space immaculate.',
                'inclusions' => "Skirting boards and door frames washed\nTile grout scrubbing and lime scale removal\nDeep kitchen degreasing",
                'exclusions' => "Exterior window washing\nRubbish removal",
                'price_cents' => 14000,
                'currency' => 'EUR',
                'duration_minutes' => 240,
                'county_id' => $dublin->id,
                'status' => ServiceStatus::Active,
                'published_at' => now()->subDays(50),
            ]
        );

        Service::updateOrCreate(
            ['slug' => 'end-of-tenancy-clean'],
            [
                'provider_profile_id' => $oconnor->id,
                'category_id' => $cleaning->id,
                'title' => 'End of tenancy clean',
                'description' => 'Complete handover cleaning designed to satisfy landlord and letting agency checkout checklists. We inspect every room in detail, clean inside cupboards, and sanitize all sanitary ware.',
                'inclusions' => "Full interior cupboard cleaning\nSanitary ware and shower screen descaling\nInternal window glass cleaning",
                'exclusions' => "Wall repainting\nFurniture disposal",
                'price_cents' => 18000,
                'currency' => 'EUR',
                'duration_minutes' => 300,
                'county_id' => $dublin->id,
                'status' => ServiceStatus::Active,
                'published_at' => now()->subDays(40),
            ]
        );

        Service::updateOrCreate(
            ['slug' => 'office-clean'],
            [
                'provider_profile_id' => $oconnor->id,
                'category_id' => $cleaning->id,
                'title' => 'Office clean',
                'description' => 'Tailored workplace sanitization for small commercial offices and communal work hubs. We ensure all workstations, meeting rooms, and kitchenettes are orderly and hygienic.',
                'inclusions' => "Desk sanitization\nBin emptying and relining\nRestroom refreshment",
                'exclusions' => "Server room cleaning\nExterior facade washing",
                'price_cents' => 15000,
                'currency' => 'EUR',
                'duration_minutes' => 180,
                'county_id' => $dublin->id,
                'status' => ServiceStatus::Draft,
                'published_at' => null,
            ]
        );

        // Maria's Services
        Service::updateOrCreate(
            ['slug' => 'lawn-mowing'],
            [
                'provider_profile_id' => $silva->id,
                'category_id' => $gardening->id,
                'title' => 'Lawn mowing',
                'description' => 'Professional lawn cutting and edge trimming for residential gardens in Cork. We collect grass clippings, tidy bordering pathways, and leave your garden looking neat and healthy.',
                'inclusions' => "Grass mowing and perimeter edging\nBlowing debris off walkways\nCollection and bagging of cuttings",
                'exclusions' => "Weed killer spraying\nLawn aeration",
                'price_cents' => 4500,
                'currency' => 'EUR',
                'duration_minutes' => 60,
                'county_id' => $cork->id,
                'status' => ServiceStatus::Active,
                'published_at' => now()->subDays(35),
            ]
        );

        Service::updateOrCreate(
            ['slug' => 'garden-tidy-up'],
            [
                'provider_profile_id' => $silva->id,
                'category_id' => $gardening->id,
                'title' => 'Garden tidy-up',
                'description' => 'A restorative maintenance session for overgrown residential gardens. We remove accumulated weeds from flowerbeds, prune dead branches, and rake leaves across all lawn areas.',
                'inclusions' => "Flowerbed weeding\nShrub pruning and deadheading\nLeaf collection",
                'exclusions' => "Tree felling\nHard landscaping",
                'price_cents' => 9500,
                'currency' => 'EUR',
                'duration_minutes' => 180,
                'county_id' => $cork->id,
                'status' => ServiceStatus::Active,
                'published_at' => now()->subDays(30),
            ]
        );

        Service::updateOrCreate(
            ['slug' => 'hedge-trimming'],
            [
                'provider_profile_id' => $silva->id,
                'category_id' => $gardening->id,
                'title' => 'Hedge trimming',
                'description' => 'Precision cutting and shaping for perimeter garden hedges and decorative shrubs. Our equipment allows clean, straight cuts that promote healthy future growth.',
                'inclusions' => "Hedge cutting and leveling\nShrub shaping\nClippings collection and cleanup",
                'exclusions' => "Hedge removal\nRoot excavation",
                'price_cents' => 12000,
                'currency' => 'EUR',
                'duration_minutes' => 150,
                'county_id' => $cork->id,
                'status' => ServiceStatus::Active,
                'published_at' => now()->subDays(25),
            ]
        );

        Service::updateOrCreate(
            ['slug' => 'seasonal-planting'],
            [
                'provider_profile_id' => $silva->id,
                'category_id' => $gardening->id,
                'title' => 'Seasonal planting',
                'description' => 'Planting of seasonal flowering bulbs and bedding plants across garden borders and decorative planters.',
                'inclusions' => "Bed preparation\nPlant positioning and planting\nInitial watering",
                'exclusions' => "Supply of premium plants\nAutomatic irrigation setup",
                'price_cents' => 16000,
                'currency' => 'EUR',
                'duration_minutes' => 180,
                'county_id' => $cork->id,
                'status' => ServiceStatus::Rejected,
                'moderation_note' => 'Please add a clearer scope.',
                'published_at' => null,
            ]
        );

        // Tomas's Services
        Service::updateOrCreate(
            ['slug' => 'emergency-callout'],
            [
                'provider_profile_id' => $novak->id,
                'category_id' => $plumbing->id,
                'title' => 'Emergency callout',
                'description' => 'Rapid response plumbing diagnostic and emergency fault repair in County Galway. We isolate sudden pipe leaks, clear blocked domestic drains, and restore water safety.',
                'inclusions' => "On-site diagnostic inspection\nTemporary or permanent leak isolation\nDrain flow clearing",
                'exclusions' => "Replacement boiler installation\nMajor bathroom tiling repairs",
                'price_cents' => 11000,
                'currency' => 'EUR',
                'duration_minutes' => 90,
                'county_id' => $galway->id,
                'status' => ServiceStatus::PendingReview,
                'published_at' => null,
            ]
        );

        // Sinead's Services
        Service::updateOrCreate(
            ['slug' => 'furniture-repair'],
            [
                'provider_profile_id' => $byrne->id,
                'category_id' => $handyman->id,
                'title' => 'Furniture repair',
                'description' => 'Structural repair and refinishing for wooden household furniture. We reinforce loose joints, re-align cabinet doors, and replace damaged hardware.',
                'inclusions' => "Structural joint re-gluing\nHardware replacement\nSurface touch-up",
                'exclusions' => "Full upholstery replacement\nAntique conservation",
                'price_cents' => 7000,
                'currency' => 'EUR',
                'duration_minutes' => 90,
                'county_id' => $limerick->id,
                'status' => ServiceStatus::Active,
                'published_at' => now()->subDays(45),
            ]
        );
    }
}
