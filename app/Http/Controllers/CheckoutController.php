<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Service;
use App\Services\PaymentService;
use App\Services\SettingsService;
use App\Services\SimulatedPaymentGateway;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected SettingsService $settings
    ) {}

    public function show(Service $service): View|RedirectResponse
    {
        $this->authorize('view', $service);

        $customer = auth()->user();

        // Check if customer belongs to the provider owning the service
        if ($customer->providerProfile && $customer->providerProfile->id === $service->provider_profile_id) {
            abort(403, 'You cannot book your own service.');
        }

        // Customer must have a county set before checkout
        if (!$customer->county_id) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Add your county before booking a service.');
        }

        $service->load(['category', 'county', 'providerProfile']);
        $idempotencyKey = (string) Str::uuid();

        $minNoticeHours = $this->settings->minNoticeHours();
        $maxAdvanceDays = $this->settings->maxAdvanceDays();
        $earliestTime = now()->addHours($minNoticeHours)->addMinutes(15);
        $latestDate = now()->addDays($maxAdvanceDays);
        $demoCards = SimulatedPaymentGateway::DEMO_CARDS;

        return view('checkout.show', compact('service', 'idempotencyKey', 'earliestTime', 'latestDate', 'demoCards'));
    }

    public function process(StoreBookingRequest $request, Service $service): RedirectResponse
    {
        $this->authorize('view', $service);

        $customer = $request->user();

        if ($customer->providerProfile && $customer->providerProfile->id === $service->provider_profile_id) {
            abort(403, 'You cannot book your own service.');
        }

        if (!$customer->county_id) {
            return redirect()->route('customer.profile.edit')
                ->with('error', 'Add your county before booking a service.');
        }

        $preferredStartAt = Carbon::parse($request->input('preferred_start_at'));

        $result = $this->paymentService->authorizeAndCreateBooking(
            customer: $customer,
            service: $service,
            preferredStartAt: $preferredStartAt,
            customerNote: $request->input('customer_note'),
            cardNumber: $request->input('card_number'),
            cardExpiry: $request->input('card_expiry'),
            cardCvc: $request->input('card_cvc'),
            idempotencyKey: $request->input('idempotency_key')
        );

        if ($result['is_idempotent'] && $result['booking']) {
            return redirect()->route('customer.bookings.show', $result['booking']->reference)
                ->with('status', 'This booking was already created.');
        }

        if (!$result['success']) {
            return back()->withInput($request->except(['card_number', 'card_expiry', 'card_cvc']))
                ->withErrors(['card_number' => $result['failure_message']]);
        }

        return redirect()->route('customer.bookings.show', $result['booking']->reference)
            ->with('status', 'Payment authorized. Your card will be charged after the service is confirmed complete.');
    }
}
