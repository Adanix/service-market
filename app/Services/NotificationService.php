<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payout;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\User;
use App\Notifications\MarketplaceNotification;
use App\Support\Money;

class NotificationService
{
    public function notify(User $user, string $title, string $body, string $url, string $type): void
    {
        $user->notify(new MarketplaceNotification($title, $body, $url, $type));
    }

    public function bookingCreated(Booking $booking): void
    {
        $dt = $booking->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i');

        // Notify provider
        $providerUser = $booking->providerProfile->user;
        if ($providerUser) {
            $this->notify(
                $providerUser,
                'New booking request',
                "{$booking->customer->name} requested {$booking->service_title} on {$dt}.",
                route('provider.bookings.show', $booking->reference),
                'booking_created'
            );
        }

        // Notify customer
        $this->notify(
            $booking->customer,
            'Booking requested',
            "Your request for {$booking->service_title} with {$booking->provider_name} was sent. You will be notified when the provider responds.",
            route('customer.bookings.show', $booking->reference),
            'booking_created'
        );
    }

    public function bookingAccepted(Booking $booking): void
    {
        $dt = $booking->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i');

        $this->notify(
            $booking->customer,
            'Booking accepted',
            "{$booking->provider_name} accepted your booking for {$booking->service_title} on {$dt}.",
            route('customer.bookings.show', $booking->reference),
            'booking_accepted'
        );
    }

    public function bookingDeclined(Booking $booking, string $reason): void
    {
        $this->notify(
            $booking->customer,
            'Booking declined',
            "{$booking->provider_name} declined your booking. Your authorization was released. Reason: {$reason}",
            route('customer.bookings.show', $booking->reference),
            'booking_declined'
        );
    }

    public function serviceStarted(Booking $booking): void
    {
        $this->notify(
            $booking->customer,
            'Service started',
            "{$booking->provider_name} started your {$booking->service_title} booking.",
            route('customer.bookings.show', $booking->reference),
            'service_started'
        );
    }

    public function awaitingConfirmation(Booking $booking): void
    {
        $deadline = $booking->auto_complete_at
            ? $booking->auto_complete_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i')
            : 'the auto-complete deadline';

        $this->notify(
            $booking->customer,
            'Confirm your booking',
            "{$booking->provider_name} marked {$booking->service_title} as complete. Confirm to release payment, or wait until {$deadline}.",
            route('customer.bookings.show', $booking->reference),
            'awaiting_confirmation'
        );
    }

    public function bookingCompleted(Booking $booking): void
    {
        $total = Money::format($booking->price_cents);
        $net = Money::format($booking->provider_net_cents);

        // Notify customer
        $this->notify(
            $booking->customer,
            'Booking completed',
            "Your payment of {$total} for {$booking->service_title} was captured. Thank you.",
            route('customer.bookings.show', $booking->reference),
            'booking_completed'
        );

        // Notify provider
        $providerUser = $booking->providerProfile->user;
        if ($providerUser) {
            $this->notify(
                $providerUser,
                'Payment captured',
                "{$total} was captured for {$booking->service_title}. Your net amount is {$net}.",
                route('provider.bookings.show', $booking->reference),
                'booking_completed'
            );
        }
    }

    public function bookingCancelled(Booking $booking, string $actor, string $paymentNote): void
    {
        $body = "Booking {$booking->reference} was cancelled by {$actor}. {$paymentNote}";

        // Notify customer
        $this->notify(
            $booking->customer,
            'Booking cancelled',
            $body,
            route('customer.bookings.show', $booking->reference),
            'booking_cancelled'
        );

        // Notify provider
        $providerUser = $booking->providerProfile->user;
        if ($providerUser) {
            $this->notify(
                $providerUser,
                'Booking cancelled',
                $body,
                route('provider.bookings.show', $booking->reference),
                'booking_cancelled'
            );
        }
    }

    public function authorizationExpiring(Booking $booking): void
    {
        $date = $booking->authorization_expires_at
            ? $booking->authorization_expires_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i')
            : 'soon';

        $this->notify(
            $booking->customer,
            'Payment authorization expiring',
            "The authorization for {$booking->reference} expires on {$date}. The booking will be cancelled if it is not completed.",
            route('customer.bookings.show', $booking->reference),
            'authorization_expiring'
        );
    }

    public function bookingExpired(Booking $booking): void
    {
        $body = "Booking {$booking->reference} expired because the payment authorization ended. No money was captured.";

        $this->notify(
            $booking->customer,
            'Booking expired',
            $body,
            route('customer.bookings.show', $booking->reference),
            'booking_expired'
        );

        $providerUser = $booking->providerProfile->user;
        if ($providerUser) {
            $this->notify(
                $providerUser,
                'Booking expired',
                $body,
                route('provider.bookings.show', $booking->reference),
                'booking_expired'
            );
        }
    }

    public function payoutRecorded(Payout $payout): void
    {
        $amount = Money::format($payout->amount_cents);
        $period = "{$payout->period_start->format('d/m/Y')} - {$payout->period_end->format('d/m/Y')}";

        $providerUser = $payout->providerProfile->user;
        if ($providerUser) {
            $this->notify(
                $providerUser,
                'Payout recorded',
                "A payout of {$amount} was recorded for {$period}.",
                route('provider.earnings'),
                'payout_recorded'
            );
        }
    }

    public function providerApproved(ProviderProfile $profile): void
    {
        if ($profile->user) {
            $this->notify(
                $profile->user,
                'Provider account approved',
                'Your provider account is approved. Submit services for review to start receiving bookings.',
                route('provider.dashboard'),
                'provider_approved'
            );
        }
    }

    public function serviceApproved(Service $service): void
    {
        $providerUser = $service->providerProfile->user;
        if ($providerUser) {
            $this->notify(
                $providerUser,
                'Service approved',
                "{$service->title} is now live.",
                route('provider.services.index'),
                'service_approved'
            );
        }
    }

    public function serviceRejected(Service $service, string $note): void
    {
        $providerUser = $service->providerProfile->user;
        if ($providerUser) {
            $this->notify(
                $providerUser,
                'Service needs changes',
                "{$service->title} was not approved. Note: {$note}",
                route('provider.services.edit', $service->id),
                'service_rejected'
            );
        }
    }
}
