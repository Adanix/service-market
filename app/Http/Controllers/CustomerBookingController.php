<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Requests\CancelBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $customer = $request->user();
        $tab = $request->query('tab', 'all');

        $query = Booking::where('customer_id', $customer->id)
            ->with(['payment']);

        match ($tab) {
            'upcoming' => $query->whereIn('status', [
                BookingStatus::PendingProvider,
                BookingStatus::Accepted,
                BookingStatus::InProgress,
                BookingStatus::AwaitingConfirmation,
            ])->orderBy('preferred_start_at', 'asc'),
            'completed' => $query->where('status', BookingStatus::Completed)->latest('completed_at'),
            'cancelled' => $query->whereIn('status', [
                BookingStatus::Cancelled,
                BookingStatus::Declined,
                BookingStatus::Expired,
            ])->latest('updated_at'),
            default => $query->latest('created_at'),
        };

        $bookings = $query->paginate(10)->withQueryString();

        return view('customer.bookings.index', compact('bookings', 'tab'));
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['payment', 'customer', 'providerProfile.county', 'service']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function cancel(CancelBookingRequest $request, Booking $booking): RedirectResponse
    {
        $customer = $request->user();

        // Customer cannot cancel if in_progress or awaiting_confirmation
        if (in_array($booking->status, [BookingStatus::InProgress, BookingStatus::AwaitingConfirmation])) {
            abort(403, 'This booking can no longer be cancelled. Contact support.');
        }

        $this->authorize('cancel', $booking);

        $this->bookingService->cancel(
            booking: $booking,
            reason: $request->input('reason'),
            actor: $customer,
            refundPayment: false
        );

        return redirect()->route('customer.bookings.show', $booking->reference)
            ->with('success', 'Booking has been cancelled and payment authorization was released.');
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        $customer = auth()->user();

        $this->authorize('confirm', $booking);

        $this->bookingService->confirmByCustomer($booking, $customer);

        return redirect()->route('customer.bookings.show', $booking->reference)
            ->with('success', 'Thank you! Completion confirmed and payment has been captured.');
    }

    public function receipt(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $payment = $booking->payment;

        // Accessible only when payment status is captured or refunded
        if (!$payment || !in_array($payment->status, [PaymentStatus::Captured, PaymentStatus::Refunded])) {
            abort(403, 'Receipt is only available once payment has been captured or refunded.');
        }

        return view('customer.bookings.receipt', compact('booking', 'payment'));
    }
}
