<x-layouts.app title="Admin Dashboard - ServiceLocal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200 mb-2">
                    <x-ui.icon name="shield" class="w-3.5 h-3.5" />
                    Marketplace Administration
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">System Overview</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.settings.edit') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 shadow-sm transition-colors">
                    <x-ui.icon name="settings" class="w-4 h-4" />
                    <span>Settings</span>
                </a>
                <a href="{{ route('admin.audit.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 shadow-sm transition-colors">
                    <x-ui.icon name="activity" class="w-4 h-4" />
                    <span>Audit Trail</span>
                </a>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white p-4 rounded-2xl border border-stone-200 border-t-3 border-t-sky-500 shadow-xs hover:shadow-md transition-shadow">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">New Requests</span>
                <span class="text-2xl font-extrabold text-stone-900 mt-1 block">{{ $newBookingsCount }}</span>
                <a href="{{ route('admin.bookings.index', ['status' => 'pending_provider']) }}" class="text-[11px] text-sky-700 font-semibold hover:underline mt-1 block">View Bookings</a>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200 border-t-3 border-t-amber-500 shadow-xs hover:shadow-md transition-shadow">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Pending Providers</span>
                <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $pendingProvidersCount }}</span>
                <a href="{{ route('admin.providers.index', ['status' => 'pending']) }}" class="text-[11px] text-amber-700 font-semibold hover:underline mt-1 block">Review</a>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200 border-t-3 border-t-orange-500 shadow-xs hover:shadow-md transition-shadow">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Pending Services</span>
                <span class="text-2xl font-extrabold text-orange-600 mt-1 block">{{ $pendingServicesCount }}</span>
                <a href="{{ route('admin.services.index', ['status' => 'pending_review']) }}" class="text-[11px] text-orange-700 font-semibold hover:underline mt-1 block">Moderate</a>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200 border-t-3 border-t-emerald-500 shadow-xs hover:shadow-md transition-shadow">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Captured (MTD)</span>
                <x-ui.money :cents="$capturedThisMonthCents" class="text-xl font-extrabold text-stone-900 mt-1 block" />
                <span class="text-[10px] text-stone-400 mt-1 block font-medium">Gross Volume</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200 border-t-3 border-t-teal-600 shadow-xs hover:shadow-md transition-shadow">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Commission (MTD)</span>
                <x-ui.money :cents="$commissionThisMonthCents" class="text-xl font-extrabold text-emerald-700 mt-1 block" />
                <span class="text-[10px] text-stone-400 mt-1 block font-medium">Platform Revenue</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-stone-200 border-t-3 border-t-purple-500 shadow-xs hover:shadow-md transition-shadow">
                <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Unpaid Payouts</span>
                <x-ui.money :cents="$availablePayoutsCents" class="text-xl font-extrabold text-purple-700 mt-1 block" />
                <a href="{{ route('admin.payouts.index') }}" class="text-[11px] text-purple-700 font-semibold hover:underline mt-1 block">Process</a>
            </div>
        </div>

        {{-- Attention Bar if items require action --}}
        @if($expiringPayments->isNotEmpty() || $delayedPendingProviders->isNotEmpty() || $unreadMessages->isNotEmpty())
            <div class="mb-8 p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 space-y-2">
                <div class="font-bold text-sm flex items-center gap-2">
                    <x-ui.icon name="alert-circle" class="w-4 h-4 text-amber-600" />
                    <span>Items Requiring Administrator Attention</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs pt-1">
                    @if($expiringPayments->isNotEmpty())
                        <div class="bg-white/80 p-2.5 rounded-lg border border-amber-200">
                            <strong>{{ $expiringPayments->count() }} Authorized Payments</strong> expiring within 48 hours.
                            <a href="{{ route('admin.payments.index', ['status' => 'authorized']) }}" class="block text-emerald-800 font-bold hover:underline mt-1">Review Payments</a>
                        </div>
                    @endif
                    @if($delayedPendingProviders->isNotEmpty())
                        <div class="bg-white/80 p-2.5 rounded-lg border border-amber-200">
                            <strong>{{ $delayedPendingProviders->count() }} Providers</strong> awaiting approval > 48h.
                            <a href="{{ route('admin.providers.index', ['status' => 'pending']) }}" class="block text-emerald-800 font-bold hover:underline mt-1">Review Providers</a>
                        </div>
                    @endif
                    @if($unreadMessages->isNotEmpty())
                        <div class="bg-white/80 p-2.5 rounded-lg border border-amber-200">
                            <strong>{{ $unreadMessages->count() }} Contact Inquiries</strong> unread.
                            <a href="{{ route('admin.contact.index', ['status' => 'new']) }}" class="block text-emerald-800 font-bold hover:underline mt-1">Read Messages</a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- 8-Week Booking Volume Chart & Status Counts --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8 items-start">
            {{-- 8-Week CSS Bar Chart --}}
            <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base font-bold text-stone-900">Booking Volume (Last 8 Weeks)</h2>
                        <p class="text-xs text-stone-500">Weekly new booking request creation trends</p>
                    </div>
                </div>

                <div class="flex items-end justify-between gap-3 h-48 pt-6 border-b border-stone-200 pb-2">
                    @foreach($weeks as $week)
                        <div class="flex-1 flex flex-col items-center h-full justify-end group">
                            <span class="text-[10px] font-bold text-stone-700 opacity-0 group-hover:opacity-100 transition-opacity mb-1">
                                {{ $week['count'] }}
                            </span>
                            <div class="w-full max-w-[36px] bg-emerald-600 group-hover:bg-emerald-500 rounded-t-lg transition-all"
                                 style="height: {{ max($week['percent'], 6) }}%">
                            </div>
                            <span class="text-[10px] text-stone-500 mt-2 font-medium">
                                {{ $week['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Status Breakdown --}}
            <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
                <h2 class="text-base font-bold text-stone-900 mb-4">Bookings by Status</h2>
                <div class="space-y-3">
                    @foreach($statusCounts as $key => $st)
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <x-marketplace.status-badge :status="$key" />
                            </div>
                            <span class="font-bold text-stone-800 font-mono">{{ $st['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Recent Bookings Table --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-stone-900">Recent Bookings</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                    View all bookings
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Ref</th>
                            <th class="px-5 py-3.5 text-left">Customer</th>
                            <th class="px-5 py-3.5 text-left">Provider</th>
                            <th class="px-5 py-3.5 text-left">Service</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-right">Price</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @foreach($recentBookings as $bk)
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs font-bold text-stone-700">{{ $bk->reference }}</td>
                                <td class="px-5 py-3.5 text-stone-800 text-xs">{{ $bk->customer->name }}</td>
                                <td class="px-5 py-3.5 text-stone-800 text-xs">{{ $bk->provider_name }}</td>
                                <td class="px-5 py-3.5 text-stone-900 font-medium text-xs">{{ $bk->service_title }}</td>
                                <td class="px-5 py-3.5"><x-marketplace.status-badge :status="$bk->status->value" /></td>
                                <td class="px-5 py-3.5 text-right font-bold text-stone-900 text-xs"><x-ui.money :cents="$bk->price_cents" /></td>
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
        </div>
    </div>
</x-layouts.app>
