<?php

namespace App\Http\Controllers;

use App\Enums\ProviderStatus;
use App\Http\Requests\SuspendProviderRequest;
use App\Models\County;
use App\Models\ProviderProfile;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProviderController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProviderProfile::with(['user', 'county'])
            ->withCount(['services', 'bookings']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('county_id')) {
            $query->where('county_id', $request->query('county_id'));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->query('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('business_name', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhere('contact_email', 'like', $term)
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('name', 'like', $term)
                         ->orWhere('email', 'like', $term);
                  });
            });
        }

        $providers = $query->latest()->paginate(15)->withQueryString();
        $counties = County::orderBy('name')->get();

        return view('admin.providers.index', [
            'providers' => $providers,
            'counties' => $counties,
            'filters' => $request->only(['status', 'county_id', 'q']),
        ]);
    }

    public function show(ProviderProfile $provider): View
    {
        $provider->load([
            'user',
            'county',
            'services.category',
            'services.county',
            'bookings' => fn($q) => $q->latest()->limit(10),
            'payouts' => fn($q) => $q->latest()->limit(10),
        ]);

        return view('admin.providers.show', [
            'provider' => $provider,
        ]);
    }

    public function approve(ProviderProfile $provider, AuditService $audit, NotificationService $notifications): RedirectResponse
    {
        $provider->update(['status' => ProviderStatus::Approved]);

        $audit->log('provider.approved', $provider, [
            'provider_id' => $provider->id,
            'business_name' => $provider->business_name,
        ]);

        $notifications->providerApproved($provider);

        return back()->with('status', 'Provider approved successfully.');
    }

    public function suspend(SuspendProviderRequest $request, ProviderProfile $provider, AuditService $audit, NotificationService $notifications): RedirectResponse
    {
        $provider->update(['status' => ProviderStatus::Suspended]);

        $audit->log('provider.suspended', $provider, [
            'provider_id' => $provider->id,
            'business_name' => $provider->business_name,
            'reason' => $request->input('reason'),
        ]);

        if ($provider->user) {
            $notifications->notify(
                $provider->user,
                'Provider account suspended',
                'Your provider account has been suspended. Reason: ' . $request->input('reason'),
                route('provider.dashboard'),
                'provider_suspended'
            );
        }

        return back()->with('status', 'Provider account has been suspended.');
    }
}
