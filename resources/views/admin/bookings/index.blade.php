<x-layouts.app title="Bookings - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Marketplace Bookings</h1>
                <p class="text-sm text-stone-500 mt-1">Search, monitor lifecycle states, and administer client reservations.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm mb-6">
            <form action="{{ route('admin.bookings.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search ref, service, customer..." class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <select name="status" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Booking Statuses</option>
                    @foreach(\App\Enums\BookingStatus::cases() as $st)
                        <option value="{{ $st->value }}" @selected(($filters['status'] ?? '') === $st->value)>{{ $st->label() ?? ucfirst($st->value) }}</option>
                    @endforeach
                </select>

                <select name="payment_status" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Payment Statuses</option>
                    @foreach(\App\Enums\PaymentStatus::cases() as $pst)
                        <option value="{{ $pst->value }}" @selected(($filters['payment_status'] ?? '') === $pst->value)>{{ ucfirst($pst->value) }}</option>
                    @endforeach
                </select>

                <select name="provider_id" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Providers</option>
                    @foreach($providers as $p)
                        <option value="{{ $p->id }}" @selected(($filters['provider_id'] ?? '') == $p->id)>{{ $p->business_name }}</option>
                    @endforeach
                </select>

                <div class="sm:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-stone-100">
                    <a href="{{ route('admin.bookings.index') }}" class="px-3 py-1.5 text-xs text-stone-500 hover:text-stone-800">
                        Reset Filters
                    </a>
                    <x-ui.button type="submit" variant="primary" class="text-xs">
                        Apply Filters
                    </x-ui.button>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Reference</th>
                            <th class="px-5 py-3.5 text-left">Customer</th>
                            <th class="px-5 py-3.5 text-left">Provider</th>
                            <th class="px-5 py-3.5 text-left">Service</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-left">Payment</th>
                            <th class="px-5 py-3.5 text-right">Total</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @foreach($bookings as $bk)
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs font-bold text-stone-700">{{ $bk->reference }}</td>
                                <td class="px-5 py-3.5 text-xs text-stone-800">{{ $bk->customer->name }}</td>
                                <td class="px-5 py-3.5 text-xs text-stone-700">{{ $bk->provider_name }}</td>
                                <td class="px-5 py-3.5 text-xs font-medium text-stone-900">{{ $bk->service_title }}</td>
                                <td class="px-5 py-3.5"><x-marketplace.status-badge :status="$bk->status->value" /></td>
                                <td class="px-5 py-3.5">
                                    @if($bk->payment)
                                        <x-marketplace.payment-badge :status="$bk->payment->status->value" />
                                    @else
                                        <span class="text-xs text-stone-400">None</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold text-stone-900 text-xs">
                                    <x-ui.money :cents="$bk->price_cents" />
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('admin.bookings.show', $bk->reference) }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-200">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
