<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Requests\CancelBookingRequest;
use App\Http\Requests\StoreBookingNoteRequest;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\County;
use App\Models\Payment;
use App\Models\ProviderProfile;
use App\Models\User;
use App\Services\AuditService;
use App\Services\BookingService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with(['customer', 'providerProfile', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($pq) use ($request) {
                $pq->where('status', $request->query('payment_status'));
            });
        }

        if ($request->filled('county_id')) {
            $county = County::find($request->query('county_id'));
            if ($county) {
                $query->where('county_name', $county->name);
            }
        }

        if ($request->filled('provider_id')) {
            $query->where('provider_profile_id', $request->query('provider_id'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->query('customer_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('preferred_start_at', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('preferred_start_at', '<=', $request->query('date_to'));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->query('q') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', $term)
                  ->orWhere('service_title', 'like', $term)
                  ->orWhere('provider_name', 'like', $term)
                  ->orWhereHas('customer', function ($cq) use ($term) {
                      $cq->where('name', 'like', $term)
                         ->orWhere('email', 'like', $term);
                  });
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();
        $counties = County::orderBy('name')->get();
        $providers = ProviderProfile::orderBy('business_name')->get();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'counties' => $counties,
            'providers' => $providers,
            'filters' => $request->only(['status', 'payment_status', 'county_id', 'provider_id', 'customer_id', 'date_from', 'date_to', 'q']),
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'customer',
            'providerProfile.user',
            'service',
            'payment',
            'payout',
        ]);

        $auditLogs = AuditLog::with('actor')
            ->where(function ($q) use ($booking) {
                $q->where('auditable_type', Booking::class)
                  ->where('auditable_id', $booking->id);
            })
            ->orWhere(function ($q) use ($booking) {
                if ($booking->payment) {
                    $q->where('auditable_type', Payment::class)
                      ->where('auditable_id', $booking->payment->id);
                }
            })
            ->latest('id')
            ->get();

        $internalNotes = $auditLogs->where('action', 'booking.note');

        return view('admin.bookings.show', [
            'booking' => $booking,
            'auditLogs' => $auditLogs,
            'internalNotes' => $internalNotes,
        ]);
    }

    public function cancel(CancelBookingRequest $request, Booking $booking, BookingService $bookingService): RedirectResponse
    {
        try {
            $refund = $request->boolean('refund');
            $bookingService->cancel($booking, $request->input('reason'), $request->user(), $refund);
            return back()->with('status', 'Booking cancelled successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['cancel' => $e->getMessage()]);
        }
    }

    public function storeNote(StoreBookingNoteRequest $request, Booking $booking, AuditService $audit): RedirectResponse
    {
        $audit->log('booking.note', $booking, [
            'note' => $request->input('note'),
        ], $request->user());

        return back()->with('status', 'Internal note recorded.');
    }
}
