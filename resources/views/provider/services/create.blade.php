<x-layouts.app title="Create Service - ServiceLocal">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('provider.dashboard')],
            ['label' => 'Services', 'url' => route('provider.services.index')],
            ['label' => 'Create New Service'],
        ]" class="mb-6" />

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Create Service Listing</h1>
            <p class="text-sm text-stone-500 mt-1">
                Add a new professional service. Submitted services undergo administrative review before publishing.
            </p>
        </div>

        <form action="{{ route('provider.services.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200 shadow-sm space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Service Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="title"
                           required
                           value="{{ old('title') }}"
                           placeholder="e.g. 3-Bedroom Deep Home Cleaning"
                           class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="category_id" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id"
                                id="category_id"
                                required
                                class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('category_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Price (€ EUR) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400 font-bold text-sm">€</span>
                            <input type="text"
                                   name="price"
                                   id="price"
                                   required
                                   value="{{ old('price') }}"
                                   placeholder="85.00"
                                   class="block w-full pl-8 pr-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_minutes" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Duration (Minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="duration_minutes"
                               id="duration_minutes"
                               required
                               min="15"
                               step="15"
                               value="{{ old('duration_minutes', 60) }}"
                               class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('duration_minutes') border-red-500 @enderror">
                        @error('duration_minutes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Detailed Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description"
                              id="description"
                              rows="4"
                              required
                              placeholder="Describe your service in detail, what equipment you bring, and requirements for the customer..."
                              class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="inclusions" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            What is Included (One per line)
                        </label>
                        <textarea name="inclusions"
                                  id="inclusions"
                                  rows="3"
                                  placeholder="Vacuuming all rooms&#10;Eco-friendly cleaning supplies&#10;Waste removal"
                                  class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('inclusions') }}</textarea>
                    </div>

                    <div>
                        <label for="exclusions" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            What is Excluded (One per line)
                        </label>
                        <textarea name="exclusions"
                                  id="exclusions"
                                  rows="3"
                                  placeholder="Exterior window cleaning&#10;Paint stripping"
                                  class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('exclusions') }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="image" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Cover Image (Optional)
                    </label>
                    <input type="file"
                           name="image"
                           id="image"
                           accept="image/*"
                           class="block w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    @error('image')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('provider.services.index') }}" class="px-4 py-2.5 text-xs font-semibold text-stone-600 hover:text-stone-900">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft" class="px-4 py-2.5 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 transition-colors">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="submit" class="px-4 py-2.5 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                    Submit for Review
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
