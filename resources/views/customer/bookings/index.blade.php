<x-layouts.app title="My Bookings - ServiceLocal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">My Bookings</h1>
                <p class="text-sm text-stone-500 mt-1">Review active, upcoming, and completed local services.</p>
            </div>
            <div>
                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-xs shadow transition-colors">
                    <x-ui.icon name="search" class="w-4 h-4" />
                    <span>Book New Service</span>
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-stone-200 mb-6">
            <nav class="flex space-x-6 text-sm font-medium">
                <a href="{{ route('customer.bookings.index') }}" class="pb-3 border-b-2 {{ $tab === 'all' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    All Bookings
                </a>
                <a href="{{ route('customer.bookings.index', ['tab' => 'upcoming']) }}" class="pb-3 border-b-2 {{ $tab === 'upcoming' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    Upcoming & Active
                </a>
                <a href="{{ route('customer.bookings.index', ['tab' => 'completed']) }}" class="pb-3 border-b-2 {{ $tab === 'completed' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    Completed
                </a>
                <a href="{{ route('customer.bookings.index', ['tab' => 'cancelled']) }}" class="pb-3 border-b-2 {{ $tab === 'cancelled' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    Cancelled / Declined
                </a>
            </nav>
        </div>

        {{-- Bookings Content --}}
        @if($bookings->isEmpty())
            <x-ui.empty-state
                title="No bookings found"
                description="You don't have any bookings in this view."
                icon="calendar"
            >
                <div class="mt-4">
                    <a href="{{ route('services.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                        Explore available services
                    </a>
                </div>
            </x-ui.empty-state>
        @else
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5 text-left">Reference</th>
                                <th class="px-5 py-3.5 text-left">Service</th>
                                <th class="px-5 py-3.5 text-left">Provider</th>
                                <th class="px-5 py-3.5 text-left">Scheduled Time</th>
                                <th class="px-5 py-3.5 text-left">Status</th>
                                <th class="px-5 py-3.5 text-right">Price</th>
                                <th class="px-5 py-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @foreach($bookings as $booking)
                                <tr class="hover:bg-stone-50/70 transition-colors">
                                    <td class="px-5 py-4 font-mono font-bold text-xs text-stone-700">
                                        {{ $booking->reference }}
                                    </td>
                                    <td class="px-5 py-4 font-medium text-stone-900">
                                        {{ $booking->service_title }}
                                    </td>
                                    <td class="px-5 py-4 text-stone-600">
                                        {{ $booking->provider_name }}
                                    </td>
                                    <td class="px-5 py-4 text-stone-600 text-xs">
                                        {{ $booking->preferred_start_at->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('D, j M Y, H:i') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <x-marketplace.status-badge :status="$booking->status->value" />
                                    </td>
                                    <td class="px-5 py-4 text-right font-bold text-stone-900">
                                        <x-ui.money :cents="$booking->price_cents" />
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('customer.bookings.show', $booking->reference) }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-800 transition-colors">
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
        @endif
    </div>
</x-layouts.app>
