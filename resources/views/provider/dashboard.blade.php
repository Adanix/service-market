<x-layouts.app title="Provider Dashboard - ServiceLocal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header & Status Banner --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
                        {{ $profile->business_name }}
                    </h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $profile->status->value === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($profile->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                        <x-ui.icon name="{{ $profile->status->value === 'approved' ? 'check' : 'clock' }}" class="w-3.5 h-3.5" />
                        Status: {{ ucfirst($profile->status->value) }}
                    </span>
                </div>
                <p class="text-sm text-stone-500 mt-1">
                    Manage service requests, weekly work schedule, and earnings payouts.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('provider.services.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-xs shadow transition-colors">
                    <x-ui.icon name="plus" class="w-4 h-4" />
                    <span>New Service Listing</span>
                </a>
            </div>
        </div>

        {{-- If Provider is Pending Moderation --}}
        @if($profile->status->value === 'pending')
            <div class="mb-8 p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start gap-3">
                <x-ui.icon name="clock" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <span class="font-bold block text-base text-amber-950 mb-1">Account Awaiting Administrative Approval</span>
                    Your business profile is under review by marketplace administrators. Once approved, you can publish services and receive booking requests from local customers.
                </div>
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <x-ui.stat-card
                title="Action Required"
                :value="$newRequestsCount"
                icon="bell"
                color="amber"
            />
            <x-ui.stat-card
                title="Upcoming Bookings"
                :value="$upcomingBookingsCount"
                icon="calendar"
                color="sky"
            />
            <x-ui.stat-card
                title="Awaiting Confirmation"
                :value="$awaitingCustomerCount"
                icon="clock"
                color="purple"
            />
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-xs hover:shadow-md transition duration-150 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Available Net Earnings</span>
                    <x-ui.money :cents="$availableEarningsCents" class="text-2xl font-extrabold text-stone-900 mt-1 block" />
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0 shadow-xs">
                    <x-ui.icon name="coins" class="w-6 h-6" />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Left: Action Queue & Next 7 Days --}}
            <div class="lg:col-span-8 space-y-8">
                {{-- Action Queue (Pending Requests) --}}
                <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-stone-900">Incoming Requests</h2>
                            @if($actionQueue->isNotEmpty())
                                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-100 text-amber-800">
                                    {{ $actionQueue->count() }} urgent
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($actionQueue->isEmpty())
                        <div class="p-6 text-center text-xs text-stone-500">
                            No pending requests requiring your response right now.
                        </div>
                    @else
                        <div class="divide-y divide-stone-100">
                            @foreach($actionQueue as $req)
                                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-stone-50/60 transition-colors">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold text-stone-700">{{ $req->reference }}</span>
                                            <x-marketplace.status-badge :status="$req->status->value" />
                                        </div>
                                        <h3 class="text-sm font-bold text-stone-900">{{ $req->service_title }}</h3>
                                        <p class="text-xs text-stone-500">
                                            Customer: <strong>{{ $req->customer->name }}</strong> &bull;
                                            Requested for: {{ $req->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('provider.bookings.show', $req->reference) }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                                            Respond (Accept / Decline)
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Next 7 Days Schedule --}}
                <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                        <h2 class="text-base font-bold text-stone-900">Upcoming Schedule (Next 7 Days)</h2>
                        <a href="{{ route('provider.bookings.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                            View all bookings
                        </a>
                    </div>

                    @if($nextWeekBookings->isEmpty())
                        <div class="p-6 text-center text-xs text-stone-500">
                            No bookings scheduled for the next 7 days.
                        </div>
                    @else
                        <div class="divide-y divide-stone-100">
                            @foreach($nextWeekBookings as $bk)
                                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-semibold text-stone-600">{{ $bk->reference }}</span>
                                            <x-marketplace.status-badge :status="$bk->status->value" />
                                        </div>
                                        <h3 class="text-sm font-bold text-stone-900">{{ $bk->service_title }}</h3>
                                        <p class="text-xs text-stone-500">
                                            {{ $bk->customer->name }} &bull; {{ $bk->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('provider.bookings.show', $bk->reference) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-stone-200 hover:bg-stone-50 text-stone-800 transition-colors">
                                        Manage
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right: Completeness Checklist & Shortcuts --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
                    <h2 class="text-base font-bold text-stone-900 mb-3">Profile Completeness</h2>
                    <div class="space-y-3">
                        @foreach($checklist as $item)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <x-ui.icon name="{{ $item['done'] ? 'check-circle' : 'circle' }}" class="w-4 h-4 {{ $item['done'] ? 'text-emerald-600' : 'text-stone-300' }}" />
                                    <span class="{{ $item['done'] ? 'text-stone-800' : 'text-stone-500' }}">{{ $item['label'] }}</span>
                                </div>
                                @if(!$item['done'])
                                    <a href="{{ $item['url'] }}" class="text-emerald-700 font-bold hover:underline">Complete</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Shortcuts --}}
                <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-2">
                    <h2 class="text-base font-bold text-stone-900 mb-3">Quick Navigation</h2>
                    <a href="{{ route('provider.services.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-50 text-xs font-medium text-stone-700 transition-colors">
                        <span class="flex items-center gap-2">
                            <x-ui.icon name="package" class="w-4 h-4 text-stone-400" />
                            Manage Service Catalog
                        </span>
                        <x-ui.icon name="chevron-right" class="w-4 h-4 text-stone-400" />
                    </a>
                    <a href="{{ route('provider.earnings') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-50 text-xs font-medium text-stone-700 transition-colors">
                        <span class="flex items-center gap-2">
                            <x-ui.icon name="coins" class="w-4 h-4 text-stone-400" />
                            Earnings & Payout History
                        </span>
                        <x-ui.icon name="chevron-right" class="w-4 h-4 text-stone-400" />
                    </a>
                    <a href="{{ route('provider.profile.edit') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-50 text-xs font-medium text-stone-700 transition-colors">
                        <span class="flex items-center gap-2">
                            <x-ui.icon name="user" class="w-4 h-4 text-stone-400" />
                            Edit Business Profile
                        </span>
                        <x-ui.icon name="chevron-right" class="w-4 h-4 text-stone-400" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
