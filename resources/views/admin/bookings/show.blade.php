<x-layouts.app :title="'Booking ' . $booking->reference . ' - Admin'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ cancelModalOpen: false }">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Bookings', 'url' => route('admin.bookings.index')],
            ['label' => $booking->reference],
        ]" class="mb-6" />

        {{-- Header & Global Actions --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight font-mono">
                        {{ $booking->reference }}
                    </h1>
                    <x-marketplace.status-badge :status="$booking->status->value" />
                    @if($booking->payment)
                        <x-marketplace.payment-badge :status="$booking->payment->status->value" />
                    @endif
                </div>
                <p class="text-xs text-stone-500 mt-1">
                    Booked on {{ $booking->created_at->format('d/m/Y H:i') }} &bull; Scheduled for {{ $booking->preferred_start_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if(!in_array($booking->status->value, ['cancelled', 'declined', 'expired']))
                    <button type="button" @click="cancelModalOpen = true" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition-colors">
                        Cancel Booking
                    </button>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        <div class="bg-white p-6 rounded-2xl border border-stone-200 mb-8 shadow-sm">
            <h2 class="text-xs font-bold text-stone-900 uppercase tracking-wider mb-6">Booking Lifecycle Timeline</h2>
            <x-marketplace.booking-timeline :booking="$booking" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-8">
            {{-- Left: Details, Parties, Notes --}}
            <div class="lg:col-span-7 space-y-6">
                {{-- Snapshot Details --}}
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-3 text-xs text-stone-700">
                    <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Service & Location Snapshot</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div><strong>Service:</strong> {{ $booking->service_title }}</div>
                        <div><strong>Category:</strong> {{ $booking->category_name }}</div>
                        <div><strong>County:</strong> {{ $booking->county_name }}</div>
                        <div><strong>Scheduled Time:</strong> {{ $booking->preferred_start_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($booking->customer_note)
                        <div class="pt-2 border-t border-stone-100">
                            <strong>Customer Note:</strong>
                            <p class="mt-1 p-2.5 bg-stone-50 rounded border border-stone-200">{{ $booking->customer_note }}</p>
                        </div>
                    @endif
                    @if($booking->cancellation_reason)
                        <div class="pt-2 border-t border-red-100 text-red-700">
                            <strong>Cancelled By:</strong> {{ ucfirst($booking->cancelled_by) }} &bull; <strong>Reason:</strong> {{ $booking->cancellation_reason }}
                        </div>
                    @endif
                </div>

                {{-- Parties Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm text-xs text-stone-700 space-y-1.5">
                        <span class="font-bold text-stone-900 block text-sm mb-1">Customer</span>
                        <div class="font-bold text-stone-800">{{ $booking->customer->name }}</div>
                        <div>{{ $booking->customer->email }}</div>
                        <div>ID: {{ $booking->customer->id }}</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm text-xs text-stone-700 space-y-1.5">
                        <span class="font-bold text-stone-900 block text-sm mb-1">Provider</span>
                        <div class="font-bold text-stone-800">{{ $booking->provider_name }}</div>
                        <div>Profile ID: {{ $booking->provider_profile_id }}</div>
                        <a href="{{ route('admin.providers.show', $booking->provider_profile_id) }}" class="text-emerald-700 font-semibold hover:underline block mt-1">View Provider Profile</a>
                    </div>
                </div>

                {{-- Internal Notes (Admin Only) --}}
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Internal Admin Notes</h2>

                    @if($internalNotes->isEmpty())
                        <p class="text-xs text-stone-400 italic">No internal notes added to this booking yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($internalNotes as $noteLog)
                                <div class="bg-stone-50 p-3 rounded-xl border border-stone-200 text-xs text-stone-700 space-y-1">
                                    <div class="flex items-center justify-between text-[11px] text-stone-500">
                                        <span><strong>{{ $noteLog->actor?->name ?? 'Admin' }}</strong></span>
                                        <span>{{ $noteLog->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="whitespace-pre-line text-stone-800">{{ $noteLog->meta['note'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.bookings.notes', $booking->reference) }}" method="POST" class="pt-3 border-t border-stone-100 space-y-2">
                        @csrf
                        <label for="admin_note" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                            Add Internal Note
                        </label>
                        <textarea name="note" id="admin_note" rows="2" required placeholder="Confidential admin note..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                        <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-stone-900 text-white hover:bg-stone-800 transition-colors">
                            Save Note
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right: Payment Card & Financial Management --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Financial Accounting</h2>

                    <div class="space-y-2 text-xs text-stone-600">
                        <div class="flex justify-between">
                            <span>Total Price</span>
                            <x-ui.money :cents="$booking->price_cents" class="font-bold text-stone-900" />
                        </div>
                        <div class="flex justify-between">
                            <span>Marketplace Commission ({{ $booking->platform_fee_bps / 100 }}%)</span>
                            <x-ui.money :cents="$booking->platform_fee_cents" class="font-medium text-stone-700" />
                        </div>
                        <div class="flex justify-between">
                            <span>Provider Net Due</span>
                            <x-ui.money :cents="$booking->provider_net_cents" class="font-bold text-emerald-700" />
                        </div>
                    </div>

                    @if($booking->payment)
                        <div class="pt-3 border-t border-stone-100 space-y-2 text-xs text-stone-700">
                            <div class="flex justify-between">
                                <span>Payment Reference</span>
                                <span class="font-mono">{{ $booking->payment->reference }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Card Details</span>
                                <span class="font-mono">{{ strtoupper($booking->payment->card_brand) }} &bull;&bull;&bull;&bull; {{ $booking->payment->card_last4 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Simulated Gateway Ref</span>
                                <span class="font-mono text-[11px] text-stone-500">{{ $booking->payment->gateway_reference }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Payment State</span>
                                <x-marketplace.payment-badge :status="$booking->payment->status->value" />
                            </div>

                            {{-- Payment Actions (Capture / Void / Refund) --}}
                            <div class="pt-3 border-t border-stone-100 space-y-2">
                                @if($booking->payment->status->value === 'authorized')
                                    <div class="flex items-center gap-2">
                                        @if(in_array($booking->status->value, ['awaiting_confirmation', 'completed']))
                                            <form action="{{ route('admin.payments.capture', $booking->payment->id) }}" method="POST" onsubmit="return confirm('Capture authorized payment now?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-sm">
                                                    Capture Payment
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.payments.void', $booking->payment->id) }}" method="POST" onsubmit="return confirm('Void this payment authorization?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 border border-stone-200 bg-stone-50 hover:bg-stone-100 text-stone-700 font-semibold text-xs rounded-lg">
                                                Void Authorization
                                            </button>
                                        </form>
                                    </div>
                                @elseif($booking->payment->status->value === 'captured')
                                    <form action="{{ route('admin.payments.refund', $booking->payment->id) }}" method="POST" onsubmit="return confirm('Refund this payment back to customer?');" class="space-y-2">
                                        @csrf
                                        <input type="text" name="reason" required placeholder="Mandatory refund reason..." class="w-full px-2.5 py-1.5 bg-stone-50 border border-stone-200 rounded text-xs">
                                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-lg">
                                            Issue Full Refund
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Audit Trail for this Booking --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-stone-100">
                <h2 class="text-sm font-bold text-stone-900 uppercase tracking-wider">Audit Trail ({{ $auditLogs->count() }} Events)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-xs">
                    <thead class="bg-stone-50 text-stone-600 font-semibold uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Timestamp</th>
                            <th class="px-4 py-3 text-left">Actor</th>
                            <th class="px-4 py-3 text-left">Action</th>
                            <th class="px-4 py-3 text-left">Metadata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 bg-white">
                        @foreach($auditLogs as $log)
                            <tr>
                                <td class="px-4 py-3 text-stone-500 font-mono">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3 font-semibold text-stone-800">{{ $log->actor?->name ?? 'System' }}</td>
                                <td class="px-4 py-3"><span class="font-mono bg-stone-100 px-1.5 py-0.5 rounded text-stone-800">{{ $log->action }}</span></td>
                                <td class="px-4 py-3 text-stone-600 font-mono text-[11px] truncate max-w-xs">
                                    {{ json_encode($log->meta) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Cancel Modal --}}
        <div x-show="cancelModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="cancelModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200">
                <h3 class="text-lg font-bold text-stone-900 mb-2">Administrator Cancel Booking</h3>
                <p class="text-xs text-stone-600 mb-4">
                    Cancelling booking <strong>{{ $booking->reference }}</strong>.
                </p>

                <form action="{{ route('admin.bookings.cancel', $booking->reference) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cancel_reason" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Cancellation Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" id="cancel_reason" rows="3" required placeholder="Administrative reason for cancellation..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    @if($booking->payment && $booking->payment->status->value === 'captured')
                        <div class="flex items-center gap-2 p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900">
                            <input type="checkbox" name="refund" id="refund_checkbox" value="1" class="rounded text-amber-600">
                            <label for="refund_checkbox" class="font-semibold cursor-pointer">
                                Refund the captured payment (<x-ui.money :cents="$booking->price_cents" />)
                            </label>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="cancelModalOpen = false" class="px-4 py-2 text-xs font-medium text-stone-600 hover:text-stone-800">
                            Cancel
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
