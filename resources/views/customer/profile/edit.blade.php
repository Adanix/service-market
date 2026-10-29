<x-layouts.app title="Customer Profile - ServiceLocal">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('customer.dashboard')],
            ['label' => 'Edit Profile'],
        ]" class="mb-6" />

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Profile & Location</h1>
            <p class="text-sm text-stone-500 mt-1">
                Your home county ensures you find services and local providers available in your area.
            </p>
        </div>

        <form action="{{ route('customer.profile.update') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           required
                           value="{{ old('name', $customer->name) }}"
                           class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Email Address
                    </label>
                    <input type="email"
                           id="email"
                           disabled
                           value="{{ $customer->email }}"
                           class="block w-full px-3 py-2.5 bg-stone-100 border border-stone-200 rounded-lg text-sm text-stone-500 cursor-not-allowed">
                    <p class="mt-1 text-[11px] text-stone-400">Demo account emails cannot be modified.</p>
                </div>

                <div>
                    <label for="county_id" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Home County <span class="text-red-500">*</span>
                    </label>
                    <select name="county_id"
                            id="county_id"
                            required
                            class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('county_id') border-red-500 @enderror">
                        <option value="">Select your County</option>
                        @foreach($counties as $co)
                            <option value="{{ $co->id }}" @selected(old('county_id', $customer->county_id) == $co->id)>
                                {{ $co->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('county_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('customer.dashboard') }}" class="px-4 py-2.5 text-xs font-semibold text-stone-600 hover:text-stone-900">
                    Cancel
                </a>
                <x-ui.button type="submit" variant="primary">
                    Save Changes
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.app>
