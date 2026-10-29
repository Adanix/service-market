<x-layouts.app :title="'Booking ' . $booking->reference . ' - Provider View'">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ declineModalOpen: false, cancelModalOpen: false }">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('provider.dashboard')],
            ['label' => 'Bookings', 'url' => route('provider.bookings.index')],
            ['label' => $booking->reference],
        ]" class="mb-6" />

        {{-- Header & Actions --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight font-mono">
                        {{ $booking->reference }}
                    </h1>
                    <x-marketplace.status-badge :status="$booking->status->value" />
                </div>
                <p class="text-sm text-stone-500 mt-1">
                    Scheduled for {{ $booking->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('l, j F Y \a\t H:i') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Pending Provider Actions --}}
                @if($booking->status->value === 'pending_provider')
                    <form action="{{ route('provider.bookings.accept', $booking->reference) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="primary">
                            Accept Booking
                        </x-ui.button>
                    </form>
                    <button type="button" @click="declineModalOpen = true" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 transition-colors">
                        Decline
                    </button>
                @endif

                {{-- Accepted Actions --}}
                @if($booking->status->value === 'accepted')
                    <form action="{{ route('provider.bookings.start', $booking->reference) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="primary">
                            Start Service
                        </x-ui.button>
                    </form>
                    <button type="button" @click="cancelModalOpen = true" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 transition-colors">
                        Cancel Booking
                    </button>
                @endif

                {{-- In Progress Actions --}}
                @if($booking->status->value === 'in_progress')
                    <form action="{{ route('provider.bookings.complete', $booking->reference) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="primary">
                            Mark Complete
                        </x-ui.button>
                    </form>
                    <button type="button" @click="cancelModalOpen = true" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 transition-colors">
                        Cancel Booking
                    </button>
                @endif
            </div>
        </div>

        {{-- Lifecycle Timeline --}}
        <div class="bg-white p-6 rounded-2xl border border-stone-200 mb-8 shadow-sm">
            <h2 class="text-sm font-bold text-stone-900 uppercase tracking-wider mb-6">Booking Lifecycle</h2>
            <x-marketplace.booking-timeline :booking="$booking" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Left: Details & Customer Information --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-stone-900 border-b border-stone-100 pb-3">Service & Customer Details</h2>

                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-xs text-stone-500 block">Service Requested</span>
                            <span class="font-bold text-stone-900">{{ $booking->service_title }}</span>
                            <span class="text-xs text-stone-500">({{ $booking->category_name }})</span>
                        </div>

                        <div>
                            <span class="text-xs text-stone-500 block">Customer Name</span>
                            <span class="font-bold text-stone-800">{{ $booking->customer->name }}</span>
                            <span class="text-xs text-stone-500 block">{{ $booking->customer->email }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-stone-500 block">Location / County</span>
                            <span class="font-semibold text-stone-800">{{ $booking->county_name }}, Ireland</span>
                        </div>

                        @if($booking->customer_note)
                            <div class="pt-2 border-t border-stone-100">
                                <span class="text-xs text-stone-500 block">Customer Special Instructions</span>
                                <p class="text-xs text-stone-700 bg-stone-50 p-3 rounded-lg border border-stone-200 mt-1 whitespace-pre-line">
                                    {{ $booking->customer_note }}
                                </p>
                            </div>
                        @endif

                        @if($booking->decline_reason)
                            <div class="pt-2 border-t border-red-100">
                                <span class="text-xs font-bold text-red-600 block">Decline Reason Recorded</span>
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
            </div>

            {{-- Right: Earnings Breakdown --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-stone-900 border-b border-stone-100 pb-3">Earnings Breakdown</h2>

                    <div class="space-y-2 text-sm text-stone-600">
                        <div class="flex justify-between">
                            <span>Gross Service Price</span>
                            <x-ui.money :cents="$booking->price_cents" class="font-bold text-stone-800" />
                        </div>
                        <div class="flex justify-between text-xs text-stone-500">
                            <span>Platform Fee ({{ $booking->platform_fee_bps / 100 }}%)</span>
                            <span>- <x-ui.money :cents="$booking->platform_fee_cents" /></span>
                        </div>
                        <div class="flex justify-between items-baseline pt-3 border-t border-stone-100">
                            <div>
                                <span class="font-bold text-stone-900 block">Your Net Earnings</span>
                                <span class="text-[11px] text-stone-400">Available for payout on completion</span>
                            </div>
                            <x-ui.money :cents="$booking->provider_net_cents" class="text-2xl font-extrabold text-emerald-700" />
                        </div>
                    </div>

                    @if($booking->payout)
                        <div class="pt-4 border-t border-stone-100 text-xs text-stone-600">
                            <span class="text-stone-400 block mb-0.5">Payout Status</span>
                            <span class="font-bold text-stone-900">Included in Payout {{ $booking->payout->reference }}</span>
                            <span class="capitalize text-emerald-700 font-semibold">({{ $booking->payout->status->value }})</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Decline Modal --}}
        <div x-show="declineModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="declineModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200">
                <h3 class="text-lg font-bold text-stone-900 mb-2">Decline Booking Request</h3>
                <p class="text-xs text-stone-600 mb-4">
                    Decline booking <strong>{{ $booking->reference }}</strong>. The client's payment hold will be immediately released.
                </p>

                <form action="{{ route('provider.bookings.decline', $booking->reference) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="decline_reason" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Reason for Declining <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" id="decline_reason" rows="3" required placeholder="e.g. Fully booked at this scheduled time..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="declineModalOpen = false" class="px-4 py-2 text-xs font-medium text-stone-600 hover:text-stone-800">
                            Back
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-lg shadow">
                            Decline Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Cancel Modal --}}
        <div x-show="cancelModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="cancelModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200">
                <h3 class="text-lg font-bold text-stone-900 mb-2">Cancel Service Booking</h3>
                <p class="text-xs text-stone-600 mb-4">
                    Cancel booking <strong>{{ $booking->reference }}</strong>. The client's authorization hold will be released.
                </p>

                <form action="{{ route('provider.bookings.cancel', $booking->reference) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cancel_reason" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Cancellation Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" id="cancel_reason" rows="3" required placeholder="Please provide your reason..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
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
