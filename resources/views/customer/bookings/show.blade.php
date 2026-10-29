<x-layouts.app :title="'Booking ' . $booking->reference . ' - ServiceLocal'">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ cancelModalOpen: false }">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('customer.dashboard')],
            ['label' => 'My Bookings', 'url' => route('customer.bookings.index')],
            ['label' => $booking->reference],
        ]" class="mb-6" />

        {{-- Page Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight font-mono">
                        {{ $booking->reference }}
                    </h1>
                    <x-marketplace.status-badge :status="$booking->status->value" />
                </div>
                <p class="text-sm text-stone-500 mt-1">
                    Booked on {{ $booking->created_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y \a\t H:i') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if($booking->payment && in_array($booking->payment->status->value, ['captured', 'refunded']))
                    <a href="{{ route('customer.bookings.receipt', $booking->reference) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 shadow-sm transition-colors">
                        <x-ui.icon name="file-text" class="w-4 h-4" />
                        <span>Print Receipt</span>
                    </a>
                @endif

                @if(in_array($booking->status->value, ['pending_provider', 'accepted']))
                    <button type="button" @click="cancelModalOpen = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 shadow-sm transition-colors">
                        <x-ui.icon name="x" class="w-4 h-4" />
                        <span>Cancel Booking</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Confirmation Alert if Awaiting Confirmation --}}
        @if($booking->status->value === 'awaiting_confirmation')
            <div class="mb-8 p-6 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-base font-bold text-amber-950 flex items-center gap-2">
                            <x-ui.icon name="check-circle" class="w-5 h-5 text-amber-600" />
                            Provider Marked Work as Complete
                        </h2>
                        <p class="text-xs text-amber-800">
                            Please confirm that the service was satisfactorily completed to capture payment (<x-ui.money :cents="$booking->price_cents" />).
                            @if($booking->auto_complete_at)
                                Auto-completion deadline: <strong>{{ $booking->auto_complete_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i') }}</strong>.
                            @endif
                        </p>
                    </div>

                    <form action="{{ route('customer.bookings.confirm', $booking->reference) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="primary" class="whitespace-nowrap font-bold">
                            Confirm Completion
                        </x-ui.button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Lifecycle Timeline --}}
        <div class="bg-white p-6 rounded-2xl border border-stone-200 mb-8 shadow-sm">
            <h2 class="text-sm font-bold text-stone-900 uppercase tracking-wider mb-6">Booking Lifecycle</h2>
            <x-marketplace.booking-timeline :booking="$booking" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Left: Booking & Provider Information --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-stone-900 border-b border-stone-100 pb-3">Service Details</h2>

                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-xs text-stone-500 block">Service Name</span>
                            <span class="font-bold text-stone-900">{{ $booking->service_title }}</span>
                            <span class="text-xs text-stone-500">({{ $booking->category_name }})</span>
                        </div>

                        <div>
                            <span class="text-xs text-stone-500 block">Scheduled Start Time</span>
                            <span class="font-bold text-stone-800">
                                {{ $booking->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('l, j F Y \a\t H:i') }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs text-stone-500 block">Location / County</span>
                            <span class="font-semibold text-stone-800">{{ $booking->county_name }}, Ireland</span>
                        </div>

                        @if($booking->customer_note)
                            <div class="pt-2 border-t border-stone-100">
                                <span class="text-xs text-stone-500 block">Your Instructions / Note</span>
                                <p class="text-xs text-stone-700 bg-stone-50 p-3 rounded-lg border border-stone-200 mt-1 whitespace-pre-line">
                                    {{ $booking->customer_note }}
                                </p>
                            </div>
                        @endif

                        @if($booking->decline_reason)
                            <div class="pt-2 border-t border-red-100">
                                <span class="text-xs font-bold text-red-600 block">Provider Decline Reason</span>
                                <p class="text-xs text-red-800 bg-red-50 p-3 rounded-lg border border-red-200 mt-1">
                                    {{ $booking->decline_reason }}
                                </p>
                            </div>
                        @endif

                        @if($booking->cancellation_reason)
                            <div class="pt-2 border-t border-stone-100">
                                <span class="text-xs font-bold text-stone-600 block">Cancellation Reason (by {{ $booking->cancelled_by }})</span>
                                <p class="text-xs text-stone-700 bg-stone-50 p-3 rounded-lg border border-stone-200 mt-1">
                                    {{ $booking->cancellation_reason }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Provider Card --}}
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
                    <h2 class="text-base font-bold text-stone-900 border-b border-stone-100 pb-3 mb-4">Assigned Provider</h2>
                    <div class="flex items-center gap-4">
                        <x-ui.avatar :name="$booking->provider_name" size="md" />
                        <div>
                            <span class="text-base font-bold text-stone-900 block">{{ $booking->provider_name }}</span>
                            <span class="text-xs text-stone-500">Service Professional</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Payment Breakdown --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-stone-900 border-b border-stone-100 pb-3">Payment Summary</h2>

                    <div class="space-y-2 text-sm text-stone-600">
                        <div class="flex justify-between">
                            <span>Service Price</span>
                            <x-ui.money :cents="$booking->price_cents" class="font-bold text-stone-800" />
                        </div>
                        <div class="flex justify-between">
                            <span>Booking Fee</span>
                            <span class="font-medium text-stone-500">€0.00</span>
                        </div>
                        <div class="flex justify-between items-baseline pt-3 border-t border-stone-100">
                            <span class="font-bold text-stone-900">Total</span>
                            <x-ui.money :cents="$booking->price_cents" class="text-xl font-extrabold text-stone-900" />
                        </div>
                    </div>

                    @if($booking->payment)
                        <div class="pt-4 border-t border-stone-100 space-y-2.5 text-xs text-stone-600">
                            <div class="flex justify-between items-center">
                                <span>Payment Status</span>
                                <x-marketplace.payment-badge :status="$booking->payment->status->value" />
                            </div>
                            <div class="flex justify-between">
                                <span>Card Method</span>
                                <span class="font-mono text-stone-800 font-semibold">{{ strtoupper($booking->payment->card_brand) }} &bull;&bull;&bull;&bull; {{ $booking->payment->card_last4 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Payment Ref</span>
                                <span class="font-mono text-stone-600">{{ $booking->payment->reference }}</span>
                            </div>
                            @if($booking->payment->capture_before)
                                <div class="flex justify-between">
                                    <span>Hold Expires</span>
                                    <span class="text-stone-600">{{ $booking->payment->capture_before->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cancel Modal --}}
        <div x-show="cancelModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="cancelModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200">
                <h3 class="text-lg font-bold text-stone-900 mb-2">Cancel Service Booking</h3>
                <p class="text-xs text-stone-600 mb-4">
                    Are you sure you want to cancel booking <strong>{{ $booking->reference }}</strong>? Your payment authorization will be immediately released.
                </p>

                <form action="{{ route('customer.bookings.cancel', $booking->reference) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="reason" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Reason for Cancellation <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" id="reason" rows="3" required placeholder="Please state your reason for cancelling..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="cancelModalOpen = false" class="px-4 py-2 text-xs font-medium text-stone-600 hover:text-stone-800">
                            Back
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-lg shadow">
                            Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
