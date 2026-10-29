<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Support\Money;
use App\Support\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected SimulatedPaymentGateway $gateway,
        protected SettingsService $settings,
        protected AuditService $audit,
        protected NotificationService $notifications
    ) {}

    /**
     * Authorize a payment and create a booking.
     *
     * @return array{
     *     success: bool,
     *     booking: ?Booking,
     *     failure_message: ?string,
     *     is_idempotent: bool
     * }
     */
    public function authorizeAndCreateBooking(
        User $customer,
        Service $service,
        Carbon $preferredStartAt,
        ?string $customerNote,
        string $cardNumber,
        string $cardExpiry,
        string $cardCvc,
        string $idempotencyKey
    ): array {
        // Idempotency check: if key already exists, return existing booking
        $existingPayment = Payment::where('idempotency_key', $idempotencyKey)->first();
        if ($existingPayment) {
            return [
                'success' => true,
                'booking' => $existingPayment->booking,
                'failure_message' => null,
                'is_idempotent' => true,
            ];
        }

        // Simulate gateway authorization
        $authResult = $this->gateway->authorize($cardNumber, $cardExpiry, $cardCvc);

        if (!$authResult['success']) {
            $this->audit->log('payment.authorization_failed', $service, [
                'failure_code' => $authResult['failure_code'],
                'service_id' => $service->id,
            ], $customer);

            return [
                'success' => false,
                'booking' => null,
                'failure_message' => $authResult['failure_message'],
                'is_idempotent' => false,
            ];
        }

        return DB::transaction(function () use (
            $customer,
            $service,
            $preferredStartAt,
            $customerNote,
            $authResult,
            $idempotencyKey
        ) {
            // Check again inside transaction to prevent race conditions
            $existing = Payment::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return [
                    'success' => true,
                    'booking' => $existing->booking,
                    'failure_message' => null,
                    'is_idempotent' => true,
                ];
            }

            $feeBps = $this->settings->platformFeeBps();
            $priceCents = $service->price_cents;
            $split = Money::calculateFeeAndNet($priceCents, $feeBps);
            $authDays = $this->settings->authorizationDays();
            $captureBefore = now()->addDays($authDays);

            // Create Booking with full snapshots
            $booking = Booking::create([
                'reference' => Reference::booking(),
                'customer_id' => $customer->id,
                'provider_profile_id' => $service->provider_profile_id,
                'service_id' => $service->id,
                'status' => BookingStatus::PendingProvider,
                'service_title' => $service->title,
                'category_name' => $service->category?->name,
                'provider_name' => $service->providerProfile->business_name,
                'county_name' => $service->county->name,
                'price_cents' => $priceCents,
                'currency' => 'EUR',
                'platform_fee_bps' => $feeBps,
                'platform_fee_cents' => $split['fee_cents'],
                'provider_net_cents' => $split['net_cents'],
                'preferred_start_at' => $preferredStartAt,
                'customer_note' => $customerNote,
                'authorization_expires_at' => $captureBefore,
            ]);

            // Create Payment
            Payment::create([
                'booking_id' => $booking->id,
                'reference' => Reference::payment(),
                'driver' => 'simulated',
                'status' => PaymentStatus::Authorized,
                'amount_cents' => $priceCents,
                'currency' => 'EUR',
                'card_brand' => $authResult['brand'],
                'card_last4' => $authResult['last4'],
                'gateway_reference' => $authResult['gateway_reference'],
                'idempotency_key' => $idempotencyKey,
                'attempt_count' => 1,
                'authorized_at' => now(),
                'capture_before' => $captureBefore,
            ]);

            $this->notifications->bookingCreated($booking);

            $this->audit->log('booking.created', $booking, [
                'reference' => $booking->reference,
                'amount_cents' => $priceCents,
            ], $customer);

            return [
                'success' => true,
                'booking' => $booking,
                'failure_message' => null,
                'is_idempotent' => false,
            ];
        });
    }

    public function capture(Payment $payment, ?User $actor = null): void
    {
        if ($payment->status !== PaymentStatus::Authorized) {
            throw new RuntimeException('Only authorized payments can be captured.');
        }

        $booking = $payment->booking;

        // Check if capture_before has passed
        if ($payment->capture_before && $payment->capture_before->isPast()) {
            $this->void($payment, 'Authorization expired prior to capture', $actor);
            $booking->update([
                'status' => BookingStatus::Expired,
                'expired_at' => now(),
            ]);
            $this->audit->log('booking.expired', $booking, ['reason' => 'capture_expired'], $actor);
            throw new RuntimeException('Payment authorization has expired and has been voided.');
        }

        if (!in_array($booking->status, [BookingStatus::AwaitingConfirmation, BookingStatus::Completed])) {
            throw new RuntimeException('Booking is not ready for payment capture.');
        }

        DB::transaction(function () use ($payment, $booking, $actor) {
            $payment->update([
                'status' => PaymentStatus::Captured,
                'captured_at' => now(),
            ]);

            if ($booking->status !== BookingStatus::Completed) {
                $booking->update([
                    'status' => BookingStatus::Completed,
                    'completed_at' => now(),
                ]);
            }

            $this->notifications->bookingCompleted($booking);

            $this->audit->log('payment.captured', $payment, [
                'amount_cents' => $payment->amount_cents,
                'booking_reference' => $booking->reference,
            ], $actor);
        });
    }

    public function void(Payment $payment, ?string $reason = null, ?User $actor = null): void
    {
        if ($payment->status !== PaymentStatus::Authorized) {
            throw new RuntimeException('Only authorized payments can be voided.');
        }

        if ($payment->booking->status === BookingStatus::Completed) {
            throw new RuntimeException('Completed bookings cannot have their payment voided.');
        }

        $payment->update([
            'status' => PaymentStatus::Voided,
            'voided_at' => now(),
        ]);

        $this->audit->log('payment.voided', $payment, [
            'booking_reference' => $payment->booking->reference,
            'reason' => $reason,
        ], $actor);
    }

    public function refund(Payment $payment, string $reason, ?User $actor = null): void
    {
        if ($payment->status !== PaymentStatus::Captured) {
            throw new RuntimeException('Only captured payments can be refunded.');
        }

        DB::transaction(function () use ($payment, $reason, $actor) {
            $payment->update([
                'status' => PaymentStatus::Refunded,
                'refunded_at' => now(),
                'refund_amount_cents' => $payment->amount_cents,
            ]);

            $booking = $payment->booking;
            if ($booking->status !== BookingStatus::Cancelled) {
                $booking->update([
                    'status' => BookingStatus::Cancelled,
                    'cancelled_at' => now(),
                    'cancelled_by' => 'admin',
                    'cancellation_reason' => $reason,
                ]);
            }

            $this->notifications->bookingCancelled($booking, 'Administrator', 'Your payment was refunded.');

            $this->audit->log('payment.refunded', $payment, [
                'amount_cents' => $payment->amount_cents,
                'booking_reference' => $booking->reference,
                'reason' => $reason,
            ], $actor);
        });
    }

    public function expireAuthorizations(): int
    {
        $expiredPayments = Payment::where('status', PaymentStatus::Authorized)
            ->where('capture_before', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredPayments as $payment) {
            DB::transaction(function () use ($payment) {
                $payment->update([
                    'status' => PaymentStatus::Voided,
                    'voided_at' => now(),
                ]);

                $booking = $payment->booking;
                if ($booking->status !== BookingStatus::Completed) {
                    $booking->update([
                        'status' => BookingStatus::Expired,
                        'expired_at' => now(),
                    ]);

                    $this->notifications->bookingExpired($booking);

                    $this->audit->log('booking.expired', $booking, [
                        'reason' => 'authorization_expired',
                    ]);
                }

                $this->audit->log('payment.expired', $payment, [
                    'booking_reference' => $booking->reference,
                ]);
            });
            $count++;
        }

        return $count;
    }
}
