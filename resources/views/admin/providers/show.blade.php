<x-layouts.app :title="'Provider ' . $provider->business_name . ' - Admin'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ suspendModalOpen: false }">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Providers', 'url' => route('admin.providers.index')],
            ['label' => $provider->business_name],
        ]" class="mb-6" />

        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">{{ $provider->business_name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $provider->status->value === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($provider->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($provider->status->value) }}
                    </span>
                </div>
                <p class="text-xs text-stone-500 mt-1">Operating in {{ $provider->county->name }}, Ireland &bull; Joined {{ $provider->created_at->format('d/m/Y') }}</p>
            </div>

            <div class="flex items-center gap-3">
                @if($provider->status->value === 'pending')
                    <form action="{{ route('admin.providers.approve', $provider->id) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="primary">
                            Approve Provider
                        </x-ui.button>
                    </form>
                @endif

                @if($provider->status->value === 'approved')
                    <button type="button" @click="suspendModalOpen = true" class="px-4 py-2 text-xs font-semibold rounded-xl bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition-colors">
                        Suspend Provider
                    </button>
                @endif

                @if($provider->status->value === 'suspended')
                    <form action="{{ route('admin.providers.approve', $provider->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                            Re-activate Provider
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Provider Info Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-xs text-stone-700">
            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-2">
                <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Business Details</h2>
                <div><strong>Business:</strong> {{ $provider->business_name }}</div>
                <div><strong>County:</strong> {{ $provider->county->name }}</div>
                <div><strong>Phone:</strong> {{ $provider->phone ?? 'None' }}</div>
                <div><strong>IBAN:</strong> {{ $provider->iban ?? 'Not recorded' }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-2">
                <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">User Account</h2>
                <div><strong>Name:</strong> {{ $provider->user->name }}</div>
                <div><strong>Email:</strong> {{ $provider->user->email }}</div>
                <div><strong>Account Status:</strong> {{ ucfirst($provider->user->status->value) }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-2">
                <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Business Bio</h2>
                <p class="text-xs text-stone-600 line-clamp-4 leading-relaxed">
                    {{ $provider->bio ?? 'No biography provided.' }}
                </p>
            </div>
        </div>

        {{-- Listed Services --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden mb-8">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-stone-900">Services Offered ({{ $provider->services->count() }})</h2>
            </div>

            @if($provider->services->isEmpty())
                <div class="p-6 text-center text-xs text-stone-500">
                    No services created by this provider.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200 text-sm">
                        <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3 text-left">Title</th>
                                <th class="px-5 py-3 text-left">Category</th>
                                <th class="px-5 py-3 text-left">Status</th>
                                <th class="px-5 py-3 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @foreach($provider->services as $svc)
                                <tr>
                                    <td class="px-5 py-3 font-semibold text-stone-900 text-xs">{{ $svc->title }}</td>
                                    <td class="px-5 py-3 text-stone-600 text-xs">{{ $svc->category->name }}</td>
                                    <td class="px-5 py-3"><x-marketplace.status-badge :status="$svc->status->value" /></td>
                                    <td class="px-5 py-3 text-right font-bold text-xs"><x-ui.money :cents="$svc->price_cents" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Suspend Modal --}}
        <div x-show="suspendModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="suspendModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200">
                <h3 class="text-lg font-bold text-stone-900 mb-2">Suspend Provider</h3>
                <p class="text-xs text-stone-600 mb-4">
                    Suspending <strong>{{ $provider->business_name }}</strong> will remove their listings from public search and notify the provider.
                </p>

                <form action="{{ route('admin.providers.suspend', $provider->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="reason" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Reason for Suspension <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" id="reason" rows="3" required placeholder="State compliance, quality, or documentation reason..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="suspendModalOpen = false" class="px-4 py-2 text-xs font-medium text-stone-600 hover:text-stone-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-lg shadow">
                            Confirm Suspension
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
