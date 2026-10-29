<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProviderStatus;
use App\Enums\ServiceStatus;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Payment;
use App\Models\ProviderProfile;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // Totals & KPI
        $newBookingsCount = Booking::where('status', BookingStatus::PendingProvider)->count();
        $pendingProvidersCount = ProviderProfile::where('status', ProviderStatus::Pending)->count();
        $pendingServicesCount = Service::where('status', ServiceStatus::PendingReview)->count();

        // Captured this month & commission this month
        $startOfMonth = now()->startOfMonth();
        $monthlyPayments = Payment::where('status', PaymentStatus::Captured)
            ->where('captured_at', '>=', $startOfMonth)
            ->with('booking')
            ->get();

        $capturedThisMonthCents = (int) $monthlyPayments->sum('amount_cents');
        $commissionThisMonthCents = (int) $monthlyPayments->sum(fn($p) => $p->booking?->platform_fee_cents ?? 0);

        // Available payouts (sum of completed bookings without payout)
        $availablePayoutsCents = (int) Booking::where('status', BookingStatus::Completed)
            ->whereNull('payout_id')
            ->sum('provider_net_cents');

        // Bookings by status
        $statusCounts = [];
        foreach (BookingStatus::cases() as $st) {
            $statusCounts[$st->value] = [
                'enum' => $st,
                'count' => Booking::where('status', $st)->count(),
            ];
        }

        // Bookings last 8 weeks for CSS chart
        $weeks = [];
        $maxCount = 1;
        for ($i = 7; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = (clone $start)->endOfWeek();
            $count = Booking::whereBetween('created_at', [$start, $end])->count();
            if ($count > $maxCount) {
                $maxCount = $count;
            }
            $weeks[] = [
                'label' => $start->format('d M'),
                'start' => $start->format('Y-m-d'),
                'count' => $count,
            ];
        }

        foreach ($weeks as &$w) {
            $w['percent'] = round(($w['count'] / $maxCount) * 100);
        }

        // Recent bookings (8 rows)
        $recentBookings = Booking::with(['customer', 'providerProfile', 'payment'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // Recent payments (8 rows)
        $recentPayments = Payment::with(['booking'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // Needs attention items:
        // - Authorized payments expiring within 48 hours
        $expiringPayments = Payment::where('status', PaymentStatus::Authorized)
            ->where('capture_before', '<=', now()->addHours(48))
            ->where('capture_before', '>', now())
            ->with('booking')
            ->get();

        // - Bookings awaiting confirmation past 48 hours
        $delayedAwaiting = Booking::where('status', BookingStatus::AwaitingConfirmation)
            ->where('provider_completed_at', '<=', now()->subHours(48))
            ->get();

        // - Contact messages unread
        $unreadMessages = ContactMessage::where('status', 'new')->get();

        // - Providers pending approval for > 48h
        $delayedPendingProviders = ProviderProfile::where('status', ProviderStatus::Pending)
            ->where('created_at', '<=', now()->subHours(48))
            ->get();

        return view('admin.dashboard', compact(
            'newBookingsCount',
            'pendingProvidersCount',
            'pendingServicesCount',
            'capturedThisMonthCents',
            'commissionThisMonthCents',
            'availablePayoutsCents',
            'statusCounts',
            'weeks',
            'recentBookings',
            'recentPayments',
            'expiringPayments',
            'delayedAwaiting',
            'unreadMessages',
            'delayedPendingProviders'
        ));
    }
}
