<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(): View
    {
        $customer = auth()->user();

        // Nearest accepted or in_progress booking
        $nextBooking = Booking::where('customer_id', $customer->id)
            ->whereIn('status', [BookingStatus::Accepted, BookingStatus::InProgress])
            ->where('preferred_start_at', '>=', now()->subHours(4))
            ->orderBy('preferred_start_at', 'asc')
            ->first();

        // Stat cards
        $activeBookingsCount = Booking::where('customer_id', $customer->id)
            ->whereIn('status', [
                BookingStatus::PendingProvider,
                BookingStatus::Accepted,
                BookingStatus::InProgress,
                BookingStatus::AwaitingConfirmation,
            ])
            ->count();

        $completedBookingsCount = Booking::where('customer_id', $customer->id)
            ->where('status', BookingStatus::Completed)
            ->count();

        // Total spent: sum of captured payments
        $totalSpentCents = (int) Payment::whereHas('booking', function ($q) use ($customer) {
            $q->where('customer_id', $customer->id);
        })->where('status', PaymentStatus::Captured)->sum('amount_cents');

        // Recent bookings (5 most recent)
        $recentBookings = Booking::where('customer_id', $customer->id)
            ->with(['payment'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // Book again: up to 3 active services from customer's county
        $bookAgainServices = collect();
        if ($customer->county_id) {
            $bookAgainServices = Service::publicListing()
                ->where('county_id', $customer->county_id)
                ->with(['category', 'county', 'providerProfile'])
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        return view('customer.dashboard', compact(
            'customer',
            'nextBooking',
            'activeBookingsCount',
            'completedBookingsCount',
            'totalSpentCents',
            'recentBookings',
            'bookAgainServices'
        ));
    }
}
