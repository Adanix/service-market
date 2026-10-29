<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public const CATEGORIES = [
        ['name' => 'Home Cleaning', 'icon' => 'home', 'description' => 'Professional residential cleaning, deep cleans, and end of tenancy services.'],
        ['name' => 'Plumbing', 'icon' => 'wrench', 'description' => 'Expert plumbing repairs, installations, and emergency callouts.'],
        ['name' => 'Electrical', 'icon' => 'zap', 'description' => 'Certified electricians for domestic repairs, wiring, and lighting.'],
        ['name' => 'Painting', 'icon' => 'paint-roller', 'description' => 'Interior and exterior painting and decorating.'],
        ['name' => 'Gardening', 'icon' => 'leaf', 'description' => 'Lawn maintenance, hedge cutting, and seasonal garden tidy-ups.'],
        ['name' => 'Moving Help', 'icon' => 'truck', 'description' => 'Reliable local moving assistance, loading, and furniture transport.'],
        ['name' => 'Handyman', 'icon' => 'hammer', 'description' => 'General household fixes, furniture assembly, and small installations.'],
        ['name' => 'Tutoring', 'icon' => 'book-open', 'description' => 'Private academic tutoring and skill development sessions.'],
    ];

    public function run(): void
    {
        $order = 1;
        foreach (self::CATEGORIES as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'description' => $cat['description'],
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );
        }
    }
}
