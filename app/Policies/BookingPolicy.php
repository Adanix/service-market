<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer() && $booking->customer_id === $user->id) {
            return true;
        }

        if ($user->isProvider() && $user->providerProfile && $booking->provider_profile_id === $user->providerProfile->id) {
            return true;
        }

        return false;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer() && $booking->customer_id === $user->id) {
            return in_array($booking->status, [
                BookingStatus::PendingProvider,
                BookingStatus::Accepted,
            ]);
        }

        if ($user->isProvider() && $user->providerProfile && $booking->provider_profile_id === $user->providerProfile->id) {
            return in_array($booking->status, [
                BookingStatus::Accepted,
                BookingStatus::InProgress,
            ]);
        }

        return false;
    }

    public function confirm(User $user, Booking $booking): bool
    {
        return $user->isCustomer()
            && $booking->customer_id === $user->id
            && $booking->status === BookingStatus::AwaitingConfirmation;
    }

    public function accept(User $user, Booking $booking): bool
    {
        return $user->isProvider()
            && $user->providerProfile
            && $booking->provider_profile_id === $user->providerProfile->id
            && $booking->status === BookingStatus::PendingProvider;
    }

    public function decline(User $user, Booking $booking): bool
    {
        return $user->isProvider()
            && $user->providerProfile
            && $booking->provider_profile_id === $user->providerProfile->id
            && $booking->status === BookingStatus::PendingProvider;
    }

    public function start(User $user, Booking $booking): bool
    {
        return $user->isProvider()
            && $user->providerProfile
            && $booking->provider_profile_id === $user->providerProfile->id
            && $booking->status === BookingStatus::Accepted;
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $user->isProvider()
            && $user->providerProfile
            && $booking->provider_profile_id === $user->providerProfile->id
            && $booking->status === BookingStatus::InProgress;
    }
}
