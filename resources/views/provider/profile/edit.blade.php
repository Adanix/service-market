<x-layouts.app title="Edit Business Profile - ServiceLocal">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('provider.dashboard')],
            ['label' => 'Edit Business Profile'],
        ]" class="mb-6" />

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Business Profile</h1>
            <p class="text-sm text-stone-500 mt-1">
                Manage your public provider presence, operating county, and contact information.
            </p>
        </div>

        <form action="{{ route('provider.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="business_name" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Business or Trading Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="business_name"
                           id="business_name"
                           required
                           value="{{ old('business_name', $profile->business_name) }}"
                           class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('business_name') border-red-500 @enderror">
                    @error('business_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="county_id" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Primary Operating County <span class="text-red-500">*</span>
                        </label>
                        <select name="county_id"
                                id="county_id"
                                required
                                class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('county_id') border-red-500 @enderror">
                            @foreach($counties as $co)
                                <option value="{{ $co->id }}" @selected(old('county_id', $profile->county_id) == $co->id)>
                                    {{ $co->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('county_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Contact Phone
                        </label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="+353 1 234 5678"
                               class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label for="bio" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Business Bio & Experience (Min 40 chars)
                    </label>
                    <textarea name="bio"
                              id="bio"
                              rows="4"
                              placeholder="Tell customers about your qualifications, trade certifications, insurance, and work ethic..."
                              class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('bio') border-red-500 @enderror">{{ old('bio', $profile->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="avatar" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Business Logo or Avatar
                    </label>
                    <input type="file"
                           name="avatar"
                           id="avatar"
                           accept="image/*"
                           class="block w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('provider.dashboard') }}" class="px-4 py-2.5 text-xs font-semibold text-stone-600 hover:text-stone-900">
                    Cancel
                </a>
                <x-ui.button type="submit" variant="primary">
                    Save Changes
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.app>
