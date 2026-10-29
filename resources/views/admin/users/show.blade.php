<x-layouts.app :title="'User ' . $user->name . ' - Admin'">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => $user->name],
        ]" class="mb-6" />

        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">{{ $user->name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->status->value === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($user->status->value) }}
                    </span>
                    <span class="capitalize text-xs font-semibold px-2 py-0.5 rounded-full bg-stone-100 text-stone-800">
                        {{ $user->role->value }}
                    </span>
                </div>
                <p class="text-xs font-mono text-stone-500 mt-1">{{ $user->email }} &bull; Registered {{ $user->created_at->format('d/m/Y') }}</p>
            </div>

            @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST" onsubmit="return confirm('Toggle status for this user?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $user->status->value === 'active' ? 'suspended' : 'active' }}">
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl {{ $user->status->value === 'active' ? 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                        {{ $user->status->value === 'active' ? 'Suspend Account' : 'Activate Account' }}
                    </button>
                </form>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- User Info Card --}}
            <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-3 text-xs text-stone-700">
                <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Profile Details</h2>
                <div><strong>User ID:</strong> {{ $user->id }}</div>
                <div><strong>County:</strong> {{ $user->county?->name ?? 'None' }}</div>
                <div><strong>Phone:</strong> {{ $user->phone ?? 'None' }}</div>
                @if($user->providerProfile)
                    <div class="pt-2 border-t border-stone-100">
                        <strong>Associated Provider:</strong>
                        <a href="{{ route('admin.providers.show', $user->providerProfile->id) }}" class="text-emerald-700 font-bold hover:underline">
                            {{ $user->providerProfile->business_name }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Recent Activity / Bookings Count --}}
            <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-3 text-xs text-stone-700">
                <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Booking Activity</h2>
                <div><strong>Total Bookings Placed:</strong> {{ $user->bookings->count() }}</div>
                @if($user->providerProfile)
                    <div><strong>Listed Services:</strong> {{ $user->providerProfile->services->count() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
