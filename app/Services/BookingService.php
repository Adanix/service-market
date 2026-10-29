<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InvalidBookingTransitionException;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected SettingsService $settings,
        protected AuditService $audit,
        protected NotificationService $notifications
    ) {}

    /**
     * Check if a transition between two statuses is allowed.
     */
    public static function canTransition(BookingStatus $from, BookingStatus $to): bool
    {
        $allowed = [
            BookingStatus::PendingProvider->value => [
                BookingStatus::Accepted->value,
                BookingStatus::Declined->value,
                BookingStatus::Cancelled->value,
                BookingStatus::Expired->value,
            ],
            BookingStatus::Accepted->value => [
                BookingStatus::InProgress->value,
                BookingStatus::Cancelled->value,
                BookingStatus::Expired->value,
            ],
            BookingStatus::InProgress->value => [
                BookingStatus::AwaitingConfirmation->value,
                BookingStatus::Cancelled->value,
            ],
            BookingStatus::AwaitingConfirmation->value => [
                BookingStatus::Completed->value,
                BookingStatus::Cancelled->value,
            ],
            BookingStatus::Completed->value => [
                BookingStatus::Cancelled->value,
            ],
            BookingStatus::Declined->value => [],
            BookingStatus::Cancelled->value => [],
            BookingStatus::Expired->value => [],
        ];

        return in_array($to->value, $allowed[$from->value] ?? [], true);
    }

    public function accept(Booking $booking, User $providerUser): void
    {
        if (!self::canTransition($booking->status, BookingStatus::Accepted)) {
            throw new InvalidBookingTransitionException("Cannot accept booking from status {$booking->status->value}");
        }

        // Re-check payment authorization validity
        $payment = $booking->payment;
        if (!$payment || $payment->status !== PaymentStatus::Authorized || ($payment->capture_before && $payment->capture_before->isPast())) {
            throw new InvalidBookingTransitionException("Payment authorization has expired. Cannot accept booking.");
        }

        $booking->update([
            'status' => BookingStatus::Accepted,
            'accepted_at' => now(),
        ]);

        $this->notifications->bookingAccepted($booking);

        $this->audit->log('booking.accepted', $booking, [
            'reference' => $booking->reference,
        ], $providerUser);
    }

    public function decline(Booking $booking, string $reason, User $providerUser): void
    {
        if (!self::canTransition($booking->status, BookingStatus::Declined)) {
            throw new InvalidBookingTransitionException("Cannot decline booking from status {$booking->status->value}");
        }

        DB::transaction(function () use ($booking, $reason, $providerUser) {
            $booking->update([
                'status' => BookingStatus::Declined,
                'decline_reason' => $reason,
            ]);

            if ($booking->payment && $booking->payment->status === PaymentStatus::Authorized) {
                $this->paymentService->void($booking->payment, 'Provider declined booking', $providerUser);
            }

            $this->notifications->bookingDeclined($booking, $reason);

            $this->audit->log('booking.declined', $booking, [
                'reference' => $booking->reference,
                'reason' => $reason,
            ], $providerUser);
        });
    }

    public function start(Booking $booking, User $providerUser): void
    {
        if (!self::canTransition($booking->status, BookingStatus::InProgress)) {
            throw new InvalidBookingTransitionException("Cannot start booking from status {$booking->status->value}");
        }

        $booking->update([
            'status' => BookingStatus::InProgress,
            'started_at' => now(),
        ]);

        $this->notifications->serviceStarted($booking);

        $this->audit->log('booking.started', $booking, [
            'reference' => $booking->reference,
        ], $providerUser);
    }

    public function completeByProvider(Booking $booking, User $providerUser): void
    {
        if (!self::canTransition($booking->status, BookingStatus::AwaitingConfirmation)) {
            throw new InvalidBookingTransitionException("Cannot mark booking complete from status {$booking->status->value}");
        }

        $hours = $this->settings->autoCompleteHours();
        $autoCompleteAt = now()->addHours($hours);

        $booking->update([
            'status' => BookingStatus::AwaitingConfirmation,
            'provider_completed_at' => now(),
            'auto_complete_at' => $autoCompleteAt,
        ]);

        $this->notifications->awaitingConfirmation($booking);

        $this->audit->log('booking.completed_by_provider', $booking, [
            'reference' => $booking->reference,
            'auto_complete_at' => $autoCompleteAt->toIso8601String(),
        ], $providerUser);
    }

    public function confirmByCustomer(Booking $booking, User $customer): void
    {
        if (!self::canTransition($booking->status, BookingStatus::Completed)) {
            throw new InvalidBookingTransitionException("Cannot confirm completion for booking in status {$booking->status->value}");
        }

        DB::transaction(function () use ($booking, $customer) {
            $booking->update([
                'status' => BookingStatus::Completed,
                'customer_confirmed_at' => now(),
                'completed_at' => now(),
            ]);

            if ($booking->payment && $booking->payment->status === PaymentStatus::Authorized) {
                $this->paymentService->capture($booking->payment, $customer);
            }

            $this->audit->log('booking.confirmed', $booking, [
                'reference' => $booking->reference,
            ], $customer);
        });
    }

    public function cancel(Booking $booking, string $reason, User $actor, bool $refundPayment = false): void
    {
        if (!self::canTransition($booking->status, BookingStatus::Cancelled)) {
            throw new InvalidBookingTransitionException("Cannot cancel booking in status {$booking->status->value}");
        }

        DB::transaction(function () use ($booking, $reason, $actor, $refundPayment) {
            $actorType = $actor->isAdmin() ? 'admin' : ($actor->isProvider() ? 'provider' : 'customer');

            $booking->update([
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now(),
                'cancelled_by' => $actorType,
                'cancellation_reason' => $reason,
            ]);

            $paymentNote = 'Your authorization was released.';
            if ($booking->payment) {
                if ($booking->payment->status === PaymentStatus::Authorized) {
                    $this->paymentService->void($booking->payment, "Booking cancelled by {$actorType}", $actor);
                } elseif ($booking->payment->status === PaymentStatus::Captured && $refundPayment) {
                    $this->paymentService->refund($booking->payment, "Booking cancelled by admin: {$reason}", $actor);
                    $paymentNote = 'Your payment was refunded.';
                }
            }

            $actorName = match ($actorType) {
                'admin' => 'Administrator',
                'provider' => $booking->provider_name,
                default => $booking->customer->name,
            };

            $this->notifications->bookingCancelled($booking, $actorName, $paymentNote);

            $this->audit->log('booking.cancelled', $booking, [
                'reference' => $booking->reference,
                'cancelled_by' => $actorType,
                'reason' => $reason,
            ], $actor);
        });
    }

    public function autoCompleteDueBookings(): int
    {
        $dueBookings = Booking::where('status', BookingStatus::AwaitingConfirmation)
            ->where('auto_complete_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($dueBookings as $booking) {
            DB::transaction(function () use ($booking) {
                $booking->update([
                    'status' => BookingStatus::Completed,
                    'completed_at' => now(),
                ]);

                if ($booking->payment && $booking->payment->status === PaymentStatus::Authorized) {
                    $this->paymentService->capture($booking->payment, null);
                }

                $this->audit->log('booking.auto_completed', $booking, [
                    'reference' => $booking->reference,
                ]);
            });
            $count++;
        }

        return $count;
    }

    public function sendReminders(): int
    {
        $reminderHours = $this->settings->reminderHours();
        $cutoff = now()->addHours($reminderHours);

        $bookings = Booking::whereIn('status', [BookingStatus::Accepted, BookingStatus::InProgress])
            ->whereNull('reminder_sent_at')
            ->where('preferred_start_at', '<=', $cutoff)
            ->where('preferred_start_at', '>', now())
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            $this->notifications->authorizationExpiring($booking);
            $booking->update(['reminder_sent_at' => now()]);
            $count++;
        }

        return $count;
    }
}
