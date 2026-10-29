<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Requests\UpdateProviderProfileRequest;
use App\Models\Booking;
use App\Models\County;
use App\Models\Payout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProviderAccountController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $profile = $user->providerProfile;
        $counties = County::where('is_active', true)->orderBy('name')->get();

        return view('provider.profile.edit', compact('user', 'profile', 'counties'));
    }

    public function update(UpdateProviderProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->providerProfile;

        $avatarPath = $profile->avatar_path;
        if ($request->hasFile('avatar')) {
            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }
            $avatarPath = $request->file('avatar')->store("avatars/{$user->id}", 'public');
        }

        $profile->update([
            'business_name' => $request->input('business_name'),
            'bio' => $request->input('bio'),
            'county_id' => $request->input('county_id'),
            'avatar_path' => $avatarPath,
        ]);

        if ($request->filled('phone')) {
            $user->update(['phone' => $request->input('phone')]);
        }

        return redirect()->route('provider.profile.edit')
            ->with('success', 'Business profile updated successfully.');
    }

    public function earnings(): View
    {
        $profile = auth()->user()->providerProfile;
        if (!$profile) {
            abort(403);
        }

        $profileId = $profile->id;

        // Captured completed bookings
        $completedBookings = Booking::where('provider_profile_id', $profileId)
            ->where('status', BookingStatus::Completed)
            ->whereHas('payment', fn($q) => $q->where('status', PaymentStatus::Captured))
            ->with(['payment', 'payout'])
            ->latest('completed_at')
            ->get();

        $capturedRevenueCents = (int) $completedBookings->sum('provider_net_cents');
        $platformFeesCents = (int) $completedBookings->sum('platform_fee_cents');

        $payouts = Payout::where('provider_profile_id', $profileId)
            ->latest('period_end')
            ->get();

        $paidOutCents = (int) $payouts->where('status', \App\Enums\PayoutStatus::Paid)->sum('amount_cents');
        $availableCents = (int) $completedBookings->whereNull('payout_id')->sum('provider_net_cents');

        return view('provider.earnings', compact(
            'profile',
            'completedBookings',
            'payouts',
            'capturedRevenueCents',
            'platformFeesCents',
            'paidOutCents',
            'availableCents'
        ));
    }
}
