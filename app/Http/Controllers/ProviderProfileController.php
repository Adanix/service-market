<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\Service;
use Illuminate\View\View;

class ProviderProfileController extends Controller
{
    public function show(ProviderProfile $provider): View
    {
        $this->authorize('view', $provider);

        $provider->load(['county', 'user']);

        $services = Service::publicListing()
            ->where('provider_profile_id', $provider->id)
            ->with(['category', 'county'])
            ->latest('published_at')
            ->get();

        return view('providers.show', compact('provider', 'services'));
    }
}
