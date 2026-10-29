<x-layouts.app title="Provider Management - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Service Providers</h1>
                <p class="text-sm text-stone-500 mt-1">Review registrations, approve profiles, and enforce marketplace moderation.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm mb-6">
            <form action="{{ route('admin.providers.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search business or provider..." class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <select name="status" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Statuses</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending Approval</option>
                    <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>Approved</option>
                    <option value="suspended" @selected(($filters['status'] ?? '') === 'suspended')>Suspended</option>
                </select>

                <select name="county_id" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Counties</option>
                    @foreach($counties as $co)
                        <option value="{{ $co->id }}" @selected(($filters['county_id'] ?? '') == $co->id)>{{ $co->name }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" class="w-full text-xs">
                        Filter
                    </x-ui.button>
                    <a href="{{ route('admin.providers.index') }}" class="px-3 py-2 text-xs text-stone-500 hover:text-stone-800">
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
                            <th class="px-5 py-3.5 text-left">Business Name</th>
                            <th class="px-5 py-3.5 text-left">Owner / User</th>
                            <th class="px-5 py-3.5 text-left">County</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-center">Services</th>
                            <th class="px-5 py-3.5 text-center">Bookings</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @foreach($providers as $provider)
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <td class="px-5 py-4 font-bold text-stone-900 text-xs">
                                    {{ $provider->business_name }}
                                </td>
                                <td class="px-5 py-4 text-xs text-stone-600">
                                    {{ $provider->user->name }}
                                    <span class="block text-[11px] text-stone-400 font-mono">{{ $provider->user->email }}</span>
                                </td>
                                <td class="px-5 py-4 text-xs text-stone-600">
                                    {{ $provider->county->name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $provider->status->value === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($provider->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($provider->status->value) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-xs text-stone-700">
                                    {{ $provider->services_count }}
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-xs text-stone-700">
                                    {{ $provider->bookings_count }}
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.providers.show', $provider->id) }}" class="text-xs font-semibold text-stone-700 hover:text-stone-900">
                                        View
                                    </a>
                                    @if($provider->status->value === 'pending')
                                        <form action="{{ route('admin.providers.approve', $provider->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-200">
                {{ $providers->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
