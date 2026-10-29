<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\View\View;

class ProviderDashboardController extends Controller
{
    public function index(): View
    {
        $providerUser = auth()->user();
        $profile = $providerUser->providerProfile;

        if (!$profile) {
            abort(403, 'Provider profile not found.');
        }

        $profileId = $profile->id;

        // Stat cards
        $newRequestsCount = Booking::where('provider_profile_id', $profileId)
            ->where('status', BookingStatus::PendingProvider)
            ->count();

        $upcomingBookingsCount = Booking::where('provider_profile_id', $profileId)
            ->whereIn('status', [BookingStatus::Accepted, BookingStatus::InProgress])
            ->count();

        $awaitingCustomerCount = Booking::where('provider_profile_id', $profileId)
            ->where('status', BookingStatus::AwaitingConfirmation)
            ->count();

        // Available earnings: sum of net for completed bookings without a payout
        $availableEarningsCents = (int) Booking::where('provider_profile_id', $profileId)
            ->where('status', BookingStatus::Completed)
            ->whereNull('payout_id')
            ->sum('provider_net_cents');

        // Action queue: bookings in pending_provider, most urgent (earliest start) first
        $actionQueue = Booking::where('provider_profile_id', $profileId)
            ->where('status', BookingStatus::PendingProvider)
            ->with(['customer'])
            ->orderBy('preferred_start_at', 'asc')
            ->get();

        // Next 7 days: accepted and in_progress bookings
        $nextWeekBookings = Booking::where('provider_profile_id', $profileId)
            ->whereIn('status', [BookingStatus::Accepted, BookingStatus::InProgress])
            ->where('preferred_start_at', '>=', now())
            ->where('preferred_start_at', '<=', now()->addDays(7))
            ->with(['customer', 'service'])
            ->orderBy('preferred_start_at', 'asc')
            ->get();

        // Profile completeness checklist
        $hasCounty = (bool) $profile->county_id;
        $hasBio = !empty($profile->bio) && strlen($profile->bio) >= 40;
        $hasActiveService = $profile->services()->where('status', 'active')->exists();
        $hasServiceImage = $profile->services()->whereNotNull('image_path')->exists();

        $checklist = [
            'county' => ['label' => 'County selected', 'done' => $hasCounty, 'url' => route('provider.profile.edit')],
            'bio' => ['label' => 'Business bio (min 40 characters)', 'done' => $hasBio, 'url' => route('provider.profile.edit')],
            'service' => ['label' => 'At least one active service', 'done' => $hasActiveService, 'url' => route('provider.services.index')],
            'image' => ['label' => 'At least one service photo', 'done' => $hasServiceImage, 'url' => route('provider.services.index')],
        ];

        return view('provider.dashboard', compact(
            'profile',
            'newRequestsCount',
            'upcomingBookingsCount',
            'awaitingCustomerCount',
            'availableEarningsCents',
            'actionQueue',
            'nextWeekBookings',
            'checklist'
        ));
    }
}
