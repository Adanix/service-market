<x-layouts.app title="Payments - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ refundModalOpen: false, currentPaymentId: null, currentAmount: '' }">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Payments & Escrow</h1>
                <p class="text-sm text-stone-500 mt-1">Manage payment authorizations, captures, voids, and refund processing.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm mb-6">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search ref, card last4, booking..." class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <select name="status" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Payment States</option>
                    @foreach(\App\Enums\PaymentStatus::cases() as $pst)
                        <option value="{{ $pst->value }}" @selected(($filters['status'] ?? '') === $pst->value)>{{ ucfirst($pst->value) }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" class="w-full text-xs">
                        Filter
                    </x-ui.button>
                    <a href="{{ route('admin.payments.index') }}" class="px-3 py-2 text-xs text-stone-500 hover:text-stone-800">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Payment Ref</th>
                            <th class="px-5 py-3.5 text-left">Booking</th>
                            <th class="px-5 py-3.5 text-left">Card Method</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-left">Authorized Until</th>
                            <th class="px-5 py-3.5 text-right">Amount</th>
                            <th class="px-5 py-3.5 text-right">Financial Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <td class="px-5 py-4 font-mono font-bold text-xs text-stone-700">
                                    {{ $payment->reference }}
                                </td>
                                <td class="px-5 py-4 text-xs font-mono">
                                    <a href="{{ route('admin.bookings.show', $payment->booking->reference) }}" class="text-emerald-700 font-semibold hover:underline">
                                        {{ $payment->booking->reference }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-xs text-stone-700 font-mono">
                                    {{ strtoupper($payment->card_brand) }} &bull;&bull;&bull;&bull; {{ $payment->card_last4 }}
                                </td>
                                <td class="px-5 py-4">
                                    <x-marketplace.payment-badge :status="$payment->status->value" />
                                </td>
                                <td class="px-5 py-4 text-xs text-stone-500">
                                    {{ $payment->capture_before ? $payment->capture_before->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-5 py-4 text-right font-extrabold text-stone-900 text-xs">
                                    <x-ui.money :cents="$payment->amount_cents" />
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    @if($payment->status->value === 'authorized')
                                        @if(in_array($payment->booking->status->value, ['awaiting_confirmation', 'completed']))
                                            <form action="{{ route('admin.payments.capture', $payment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Capture payment now?');">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                                    Capture
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.payments.void', $payment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Void this authorization?');">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-stone-500 hover:text-stone-800">
                                                Void
                                            </button>
                                        </form>
                                    @elseif($payment->status->value === 'captured')
                                        <button type="button"
                                                @click="refundModalOpen = true; currentPaymentId = {{ $payment->id }}; currentAmount = '{{ \App\Support\Money::format($payment->amount_cents) }}'"
                                                class="text-xs font-semibold text-red-600 hover:text-red-800">
                                            Refund
                                        </button>
                                    @else
                                        <span class="text-xs text-stone-400">Settled</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-200">
                {{ $payments->links() }}
            </div>
        </div>

        {{-- Refund Modal --}}
        <div x-show="refundModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="refundModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200">
                <h3 class="text-lg font-bold text-stone-900 mb-2">Process Refund</h3>
                <p class="text-xs text-stone-600 mb-4">
                    Refund <strong x-text="currentAmount"></strong> back to client's card. This will cancel the booking and release captured funds.
                </p>

                <form :action="'/admin/payments/' + currentPaymentId + '/refund'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="refund_reason" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Refund Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" id="refund_reason" rows="3" required placeholder="State refund justification..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="refundModalOpen = false" class="px-4 py-2 text-xs font-medium text-stone-600 hover:text-stone-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-lg shadow">
                            Issue Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
