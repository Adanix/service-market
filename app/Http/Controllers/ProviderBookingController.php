<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Requests\CancelBookingRequest;
use App\Http\Requests\DeclineBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $profile = $request->user()->providerProfile;
        if (!$profile) {
            abort(403);
        }

        $tab = $request->query('tab', 'new');
        $query = Booking::where('provider_profile_id', $profile->id)->with(['customer', 'payment']);

        match ($tab) {
            'new' => $query->where('status', BookingStatus::PendingProvider)->orderBy('preferred_start_at', 'asc'),
            'active' => $query->whereIn('status', [BookingStatus::Accepted, BookingStatus::InProgress])->orderBy('preferred_start_at', 'asc'),
            'awaiting_confirmation' => $query->where('status', BookingStatus::AwaitingConfirmation)->latest('provider_completed_at'),
            'completed' => $query->where('status', BookingStatus::Completed)->latest('completed_at'),
            'closed' => $query->whereIn('status', [BookingStatus::Declined, BookingStatus::Cancelled, BookingStatus::Expired])->latest('updated_at'),
            default => $query->latest('created_at'),
        };

        $bookings = $query->paginate(10)->withQueryString();

        return view('provider.bookings.index', compact('bookings', 'tab', 'profile'));
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['customer', 'payment', 'service', 'payout']);

        return view('provider.bookings.show', compact('booking'));
    }

    public function accept(Booking $booking): RedirectResponse
    {
        $this->authorize('accept', $booking);

        $this->bookingService->accept($booking, auth()->user());

        return redirect()->route('provider.bookings.show', $booking->reference)
            ->with('success', 'Booking accepted successfully.');
    }

    public function decline(DeclineBookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('decline', $booking);

        $this->bookingService->decline($booking, $request->input('reason'), auth()->user());

        return redirect()->route('provider.bookings.show', $booking->reference)
            ->with('success', 'Booking declined and payment authorization was released.');
    }

    public function start(Booking $booking): RedirectResponse
    {
        $this->authorize('start', $booking);

        $this->bookingService->start($booking, auth()->user());

        return redirect()->route('provider.bookings.show', $booking->reference)
            ->with('success', 'Service marked as in progress.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        $this->authorize('complete', $booking);

        $this->bookingService->completeByProvider($booking, auth()->user());

        return redirect()->route('provider.bookings.show', $booking->reference)
            ->with('success', 'Service marked complete. The customer has been asked to confirm.');
    }

    public function cancel(CancelBookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        $this->bookingService->cancel(
            booking: $booking,
            reason: $request->input('reason'),
            actor: auth()->user(),
            refundPayment: false
        );

        return redirect()->route('provider.bookings.show', $booking->reference)
            ->with('success', 'Booking cancelled and authorization was released.');
    }
}
