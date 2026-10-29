<x-layouts.app title="Customer Dashboard - ServiceLocal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Welcome Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                    Welcome back, {{ $customer->name }}
                </h1>
                <p class="text-sm text-stone-500 mt-1">
                    Manage your service requests, schedule, and transaction receipts.
                </p>
            </div>
            <div>
                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm shadow transition-colors">
                    <x-ui.icon name="search" class="w-4 h-4" />
                    <span>Find a Service</span>
                </a>
            </div>
        </div>

        {{-- Next Upcoming Booking Banner --}}
        @if($nextBooking)
            <div class="mb-8 p-5 sm:p-6 rounded-2xl bg-gradient-to-r from-emerald-900 to-stone-900 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300">
                        <x-ui.icon name="calendar" class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="text-xs uppercase tracking-wider font-bold text-emerald-400">Next Upcoming Appointment</span>
                        <h3 class="text-lg font-bold text-white mt-0.5">{{ $nextBooking->service_title }}</h3>
                        <p class="text-xs text-stone-300 mt-1">
                            With <strong>{{ $nextBooking->provider_name }}</strong> &bull;
                            {{ $nextBooking->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y \a\t H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer.bookings.show', $nextBooking->reference) }}" class="px-4 py-2 bg-white text-stone-900 hover:bg-stone-100 font-semibold text-xs rounded-lg transition-colors">
                        View Details
                    </a>
                </div>
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <x-ui.stat-card
                title="Active Bookings"
                :value="$activeBookingsCount"
                icon="clock"
                color="sky"
            />
            <x-ui.stat-card
                title="Completed Bookings"
                :value="$completedBookingsCount"
                icon="check-circle"
                color="emerald"
            />
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs hover:shadow-md transition duration-150 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Spent</span>
                    <x-ui.money :cents="$totalSpentCents" class="text-2xl font-extrabold text-stone-900 mt-1 block" />
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0 shadow-xs">
                    <x-ui.icon name="credit-card" class="w-6 h-6" />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Recent Bookings --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                    <div class="p-5 sm:p-6 border-b border-stone-100 flex items-center justify-between">
                        <h2 class="text-base font-bold text-stone-900">Recent Bookings</h2>
                        <a href="{{ route('customer.bookings.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                            View all <x-ui.icon name="arrow-right" class="w-3.5 h-3.5" />
                        </a>
                    </div>

                    @if($recentBookings->isEmpty())
                        <div class="p-8 text-center">
                            <x-ui.empty-state
                                title="No bookings yet"
                                description="You have not requested any service bookings yet."
                                icon="calendar"
                            />
                        </div>
                    @else
                        <div class="divide-y divide-stone-100">
                            @foreach($recentBookings as $b)
                                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-stone-50/60 transition-colors">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold text-stone-700">{{ $b->reference }}</span>
                                            <x-marketplace.status-badge :status="$b->status->value" />
                                        </div>
                                        <h3 class="text-sm font-bold text-stone-900">{{ $b->service_title }}</h3>
                                        <p class="text-xs text-stone-500">
                                            {{ $b->provider_name }} &bull; {{ $b->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <x-ui.money :cents="$b->price_cents" class="text-sm font-bold text-stone-900" />
                                        <a href="{{ route('customer.bookings.show', $b->reference) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-stone-200 hover:bg-white bg-stone-50 text-stone-700 transition-colors">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recommendations (Book Again in your County) --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                    <h2 class="text-base font-bold text-stone-900 mb-4">Popular in Your County</h2>
                    @if($bookAgainServices->isEmpty())
                        <p class="text-xs text-stone-500">
                            Set your home county in your profile to view localized recommendations.
                        </p>
                        <a href="{{ route('customer.profile.edit') }}" class="text-xs font-semibold text-emerald-700 hover:underline block mt-2">
                            Update Profile
                        </a>
                    @else
                        <div class="space-y-4">
                            @foreach($bookAgainServices as $svc)
                                <div class="p-3 rounded-xl border border-stone-100 hover:border-emerald-200 bg-stone-50/50 transition-colors">
                                    <span class="text-[10px] font-bold text-emerald-800 uppercase">{{ $svc->category->name }}</span>
                                    <h3 class="text-xs font-bold text-stone-900 mt-0.5 line-clamp-1">{{ $svc->title }}</h3>
                                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-stone-100 text-xs">
                                        <x-ui.money :cents="$svc->price_cents" class="font-bold text-stone-800" />
                                        <a href="{{ route('services.show', $svc->slug) }}" class="text-emerald-700 hover:underline font-semibold">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
