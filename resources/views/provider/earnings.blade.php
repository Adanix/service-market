<x-layouts.app title="Earnings & Payouts - ServiceLocal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('provider.dashboard')],
            ['label' => 'Earnings & Payouts'],
        ]" class="mb-6" />

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Earnings & Payout History</h1>
            <p class="text-sm text-stone-500 mt-1">
                Track captured revenue, platform commission deductions, and bank payouts.
            </p>
        </div>

        {{-- Financial Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Available for Payout</span>
                <x-ui.money :cents="$availableCents" class="text-2xl font-extrabold text-emerald-600 mt-1 block" />
                <span class="text-[11px] text-stone-400 mt-1 block">Completed & pending transfer</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Total Net Captured</span>
                <x-ui.money :cents="$capturedRevenueCents" class="text-2xl font-extrabold text-stone-900 mt-1 block" />
                <span class="text-[11px] text-stone-400 mt-1 block">Lifetime earned net</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Paid Out to Date</span>
                <x-ui.money :cents="$paidOutCents" class="text-2xl font-extrabold text-stone-900 mt-1 block" />
                <span class="text-[11px] text-stone-400 mt-1 block">Transferred to your IBAN</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Platform Commission</span>
                <x-ui.money :cents="$platformFeesCents" class="text-2xl font-extrabold text-stone-600 mt-1 block" />
                <span class="text-[11px] text-stone-400 mt-1 block">Total platform service fee</span>
            </div>
        </div>

        <div class="space-y-8">
            {{-- Payouts Table --}}
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-stone-900">Payout Records</h2>
                </div>

                @if($payouts->isEmpty())
                    <div class="p-6 text-center text-xs text-stone-500">
                        No payouts have been processed yet.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200 text-sm">
                            <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-5 py-3.5 text-left">Payout Ref</th>
                                    <th class="px-5 py-3.5 text-left">Period Covered</th>
                                    <th class="px-5 py-3.5 text-left">Status</th>
                                    <th class="px-5 py-3.5 text-left">Processed Date</th>
                                    <th class="px-5 py-3.5 text-right">Net Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 bg-white">
                                @foreach($payouts as $payout)
                                    <tr>
                                        <td class="px-5 py-4 font-mono font-bold text-xs text-stone-700">
                                            {{ $payout->reference }}
                                        </td>
                                        <td class="px-5 py-4 text-xs text-stone-600">
                                            {{ $payout->period_start->format('d/m/Y') }} &ndash; {{ $payout->period_end->format('d/m/Y') }}
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $payout->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ ucfirst($payout->status->value) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-stone-600">
                                            {{ $payout->paid_at ? $payout->paid_at->format('d/m/Y H:i') : 'Pending' }}
                                        </td>
                                        <td class="px-5 py-4 text-right font-extrabold text-stone-900">
                                            <x-ui.money :cents="$payout->amount_cents" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Completed Bookings Detail --}}
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-stone-900">Completed Service Bookings</h2>
                </div>

                @if($completedBookings->isEmpty())
                    <div class="p-6 text-center text-xs text-stone-500">
                        No completed bookings recorded yet.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-stone-200 text-sm">
                            <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-5 py-3.5 text-left">Booking</th>
                                    <th class="px-5 py-3.5 text-left">Service</th>
                                    <th class="px-5 py-3.5 text-right">Gross Price</th>
                                    <th class="px-5 py-3.5 text-right">Fee (15%)</th>
                                    <th class="px-5 py-3.5 text-right">Your Net</th>
                                    <th class="px-5 py-3.5 text-center">Payout Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 bg-white">
                                @foreach($completedBookings as $bk)
                                    <tr>
                                        <td class="px-5 py-4 font-mono font-bold text-xs text-stone-700">
                                            <a href="{{ route('provider.bookings.show', $bk->reference) }}" class="hover:underline text-emerald-700">
                                                {{ $bk->reference }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-4 font-medium text-stone-900">
                                            {{ $bk->service_title }}
                                        </td>
                                        <td class="px-5 py-4 text-right text-stone-600">
                                            <x-ui.money :cents="$bk->price_cents" />
                                        </td>
                                        <td class="px-5 py-4 text-right text-stone-500 text-xs">
                                            <x-ui.money :cents="$bk->platform_fee_cents" />
                                        </td>
                                        <td class="px-5 py-4 text-right font-extrabold text-emerald-700">
                                            <x-ui.money :cents="$bk->provider_net_cents" />
                                        </td>
                                        <td class="px-5 py-4 text-center text-xs">
                                            @if($bk->payout)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                    Paid ({{ $bk->payout->reference }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                    Unpaid (In Next Cycle)
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
