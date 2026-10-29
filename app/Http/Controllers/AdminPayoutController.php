<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PayoutStatus;
use App\Http\Requests\StorePayoutRequest;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\ProviderProfile;
use App\Services\AuditService;
use App\Services\NotificationService;
use App\Support\Reference;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPayoutController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payout::with(['providerProfile.user', 'bookings']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('provider_id')) {
            $query->where('provider_profile_id', $request->query('provider_id'));
        }

        $payouts = $query->latest()->paginate(15)->withQueryString();
        $providers = ProviderProfile::orderBy('business_name')->get();

        // Calculate pending payouts eligible amount per provider
        $unpaidBookings = Booking::where('status', BookingStatus::Completed)
            ->whereNull('payout_id')
            ->select('provider_profile_id', DB::raw('SUM(provider_net_cents) as total_unpaid_cents'), DB::raw('COUNT(*) as booking_count'))
            ->groupBy('provider_profile_id')
            ->get()
            ->keyBy('provider_profile_id');

        return view('admin.payouts.index', [
            'payouts' => $payouts,
            'providers' => $providers,
            'unpaidBookings' => $unpaidBookings,
            'filters' => $request->only(['status', 'provider_id']),
        ]);
    }

    public function store(StorePayoutRequest $request, AuditService $audit, NotificationService $notifications): RedirectResponse
    {
        $providerId = $request->input('provider_profile_id');
        $start = Carbon::parse($request->input('period_start'))->startOfDay();
        $end = Carbon::parse($request->input('period_end'))->endOfDay();

        $eligibleBookings = Booking::where('provider_profile_id', $providerId)
            ->where('status', BookingStatus::Completed)
            ->whereNull('payout_id')
            ->whereBetween('completed_at', [$start, $end])
            ->get();

        if ($eligibleBookings->isEmpty()) {
            return back()->withErrors([
                'period' => 'No unpaid completed bookings found for this provider within the specified period.',
            ]);
        }

        $totalCents = $eligibleBookings->sum('provider_net_cents');

        $payout = DB::transaction(function () use ($providerId, $start, $end, $totalCents, $request, $eligibleBookings, $audit, $notifications) {
            $payout = Payout::create([
                'reference' => Reference::payout(),
                'provider_profile_id' => $providerId,
                'period_start' => $start->toDateString(),
                'period_end' => $end->toDateString(),
                'amount_cents' => $totalCents,
                'status' => PayoutStatus::Pending,
                'note' => $request->input('note'),
            ]);

            Booking::whereIn('id', $eligibleBookings->pluck('id'))->update([
                'payout_id' => $payout->id,
            ]);

            $audit->log('payout.created', $payout, [
                'reference' => $payout->reference,
                'amount_cents' => $payout->amount_cents,
                'booking_count' => $eligibleBookings->count(),
            ]);

            $notifications->payoutRecorded($payout);

            return $payout;
        });

        return back()->with('status', "Payout {$payout->reference} created successfully.");
    }

    public function markPaid(Payout $payout, AuditService $audit, NotificationService $notifications): RedirectResponse
    {
        if ($payout->status === PayoutStatus::Paid) {
            return back()->with('status', 'Payout is already marked as paid.');
        }

        $payout->update([
            'status' => PayoutStatus::Paid,
            'paid_at' => now(),
        ]);

        $audit->log('payout.marked_paid', $payout, [
            'reference' => $payout->reference,
            'amount_cents' => $payout->amount_cents,
        ]);

        if ($payout->providerProfile && $payout->providerProfile->user) {
            $notifications->notify(
                $payout->providerProfile->user,
                'Payout transferred',
                "Payout {$payout->reference} for the amount of " . \App\Support\Money::format($payout->amount_cents) . " has been sent.",
                route('provider.earnings'),
                'payout_paid'
            );
        }

        return back()->with('status', "Payout {$payout->reference} marked as paid.");
    }
}
