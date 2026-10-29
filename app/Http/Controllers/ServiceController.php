<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\County;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $counties = County::where('is_active', true)->orderBy('name')->get();

        $query = Service::publicListing()
            ->with(['category', 'county', 'providerProfile.user']);

        // Default county to customer's home county if not explicitly provided
        $user = auth()->user();
        $selectedCountySlug = $request->query('county');
        if ($selectedCountySlug === null && $user && $user->isCustomer() && $user->county) {
            $selectedCountySlug = $user->county->slug;
        }

        // Apply County filter
        $activeCounty = null;
        if (!empty($selectedCountySlug)) {
            $activeCounty = County::where('slug', $selectedCountySlug)->first();
            if ($activeCounty) {
                $query->where('county_id', $activeCounty->id);
            }
        }

        // Apply Category filter
        $activeCategory = null;
        if ($request->filled('category')) {
            $activeCategory = Category::where('slug', $request->query('category'))->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        // Apply Price filter
        $priceMin = $request->query('price_min');
        if ($request->filled('price_min')) {
            $query->where('services.price_cents', '>=', (int) ($priceMin * 100));
        }

        $priceMax = $request->query('price_max');
        if ($request->filled('price_max')) {
            $query->where('services.price_cents', '<=', (int) ($priceMax * 100));
        }

        // Apply Keyword search
        $q = $request->query('q');
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('services.title', 'like', "%{$q}%")
                    ->orWhere('services.description', 'like', "%{$q}%");
            });
        }

        // Sorting
        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('services.price_cents', 'asc'),
            'price_desc' => $query->orderBy('services.price_cents', 'desc'),
            'newest' => $query->orderBy('services.published_at', 'desc'),
            default => $query->orderBy('services.published_at', 'desc'),
        };

        $services = $query->paginate(12)->withQueryString();
        $selectedCategory = $request->query('category');
        $selectedCounty = $selectedCountySlug;

        return view('services.index', compact(
            'services',
            'categories',
            'counties',
            'selectedCounty',
            'selectedCategory',
            'q',
            'sort',
            'priceMin',
            'priceMax'
        ));
    }

    public function show(Service $service): View
    {
        // Enforce policy check: only active services from approved/active providers are public
        $this->authorize('view', $service);

        $service->load(['category', 'county', 'providerProfile.user', 'providerProfile.county']);

        return view('services.show', compact('service'));
    }
}
