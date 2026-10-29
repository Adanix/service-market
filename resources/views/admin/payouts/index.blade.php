<x-layouts.app title="Payouts - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ newPayoutModalOpen: false }">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Provider Payouts</h1>
                <p class="text-sm text-stone-500 mt-1">Aggregate completed bookings and record periodic bank disbursements.</p>
            </div>
            <div>
                <button type="button" @click="newPayoutModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-xs shadow transition-colors">
                    <x-ui.icon name="plus" class="w-4 h-4" />
                    <span>Create Payout Batch</span>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-stone-900">Historical Payouts</h2>
            </div>

            @if($payouts->isEmpty())
                <div class="p-8 text-center text-xs text-stone-500">
                    No payouts have been created yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5 text-left">Payout Ref</th>
                                <th class="px-5 py-3.5 text-left">Provider</th>
                                <th class="px-5 py-3.5 text-left">Period Covered</th>
                                <th class="px-5 py-3.5 text-left">Status</th>
                                <th class="px-5 py-3.5 text-left">Paid At</th>
                                <th class="px-5 py-3.5 text-right">Net Amount</th>
                                <th class="px-5 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @foreach($payouts as $po)
                                <tr class="hover:bg-stone-50/70 transition-colors">
                                    <td class="px-5 py-4 font-mono font-bold text-xs text-stone-700">{{ $po->reference }}</td>
                                    <td class="px-5 py-4 text-xs font-semibold text-stone-900">{{ $po->providerProfile->business_name }}</td>
                                    <td class="px-5 py-4 text-xs text-stone-600">
                                        {{ $po->period_start->format('d/m/Y') }} &ndash; {{ $po->period_end->format('d/m/Y') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $po->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ ucfirst($po->status->value) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-stone-500 font-mono">
                                        {{ $po->paid_at ? $po->paid_at->format('d/m/Y H:i') : 'Pending' }}
                                    </td>
                                    <td class="px-5 py-4 text-right font-extrabold text-stone-900 text-xs">
                                        <x-ui.money :cents="$po->amount_cents" />
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        @if($po->status->value === 'pending')
                                            <form action="{{ route('admin.payouts.mark-paid', $po->id) }}" method="POST" onsubmit="return confirm('Confirm transfer and mark this payout as paid?');">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                                    Mark Paid
                                                </button>
                                            </form>
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
                    {{ $payouts->links() }}
                </div>
            @endif
        </div>

        {{-- Create Payout Modal --}}
        <div x-show="newPayoutModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="newPayoutModalOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-stone-200">
                <h3 class="text-lg font-bold text-stone-900 mb-2">Create Payout Batch</h3>
                <p class="text-xs text-stone-600 mb-4">
                    Batch completed bookings for a provider into an official payout record.
                </p>

                <form action="{{ route('admin.payouts.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="provider_profile_id" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Provider Profile <span class="text-red-500">*</span>
                        </label>
                        <select name="provider_profile_id" id="provider_profile_id" required class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Select Provider</option>
                            @foreach($providers as $p)
                                <option value="{{ $p->id }}">{{ $p->business_name }} ({{ $p->county->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="period_start" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Period Start <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="period_start" id="period_start" required value="{{ now()->subDays(30)->toDateString() }}" class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label for="period_end" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Period End <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="period_end" id="period_end" required value="{{ now()->toDateString() }}" class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label for="note" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Internal Note (Optional)
                        </label>
                        <textarea name="note" id="note" rows="2" placeholder="Bank batch reference or settlement notes..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="newPayoutModalOpen = false" class="px-4 py-2 text-xs font-medium text-stone-600 hover:text-stone-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow">
                            Record Payout
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
