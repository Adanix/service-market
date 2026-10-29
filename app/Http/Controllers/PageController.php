<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\ProviderStatus;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\Booking;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\County;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\User;
use App\Services\SettingsService;
use App\Services\SimulatedPaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        protected SettingsService $settings
    ) {}

    public function home(): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $counties = County::where('is_active', true)->orderBy('name')->get();

        $user = auth()->user();
        $userCounty = ($user && $user->isCustomer() && $user->county_id) ? $user->county : null;

        $popularServicesQuery = Service::publicListing()->with(['category', 'county', 'providerProfile']);

        if ($userCounty) {
            $popularServices = (clone $popularServicesQuery)
                ->where('county_id', $userCounty->id)
                ->latest('published_at')
                ->take(6)
                ->get();

            // Fallback if user's county has fewer than 6 services
            if ($popularServices->count() < 3) {
                $popularServices = $popularServicesQuery->latest('published_at')->take(6)->get();
            }
        } else {
            $popularServices = $popularServicesQuery->latest('published_at')->take(6)->get();
        }

        $featuredProviders = ProviderProfile::where('status', ProviderStatus::Approved)
            ->with(['county', 'user'])
            ->withCount(['services' => fn($q) => $q->active()])
            ->take(3)
            ->get();

        $stats = [
            'services_count' => Service::active()->count(),
            'providers_count' => ProviderProfile::where('status', ProviderStatus::Approved)->count(),
            'counties_count' => County::where('is_active', true)->count(),
            'completed_bookings' => Booking::where('status', BookingStatus::Completed)->count(),
        ];

        return view('pages.home', compact('categories', 'counties', 'popularServices', 'userCounty', 'featuredProviders', 'stats'));
    }

    public function docs(): View
    {
        $users = User::with(['county', 'providerProfile.county'])->get();
        $settings = $this->settings->all();
        $demoCards = SimulatedPaymentGateway::DEMO_CARDS;

        return view('pages.docs', compact('users', 'settings', 'demoCards'));
    }

    public function help(): View
    {
        $supportEmail = $this->settings->supportEmail();
        return view('pages.help', compact('supportEmail'));
    }

    public function contact(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        return redirect()->route('help')
            ->with('status', 'Your message has been sent to the demo administrator.');
    }

    public function terms(): View
    {
        return view('pages.legal.terms');
    }

    public function privacy(): View
    {
        return view('pages.legal.privacy');
    }

    public function refunds(): View
    {
        return view('pages.legal.refunds');
    }
}
