<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $booking = $payment->booking;
        if ($user->isCustomer() && $booking->customer_id === $user->id) {
            return true;
        }

        if ($user->isProvider() && $user->providerProfile && $booking->provider_profile_id === $user->providerProfile->id) {
            return true;
        }

        return false;
    }

    public function capture(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    public function void(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }
}
