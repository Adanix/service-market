<x-layouts.app :title="'Edit ' . $service->title . ' - ServiceLocal'">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Dashboard', 'url' => route('provider.dashboard')],
            ['label' => 'Services', 'url' => route('provider.services.index')],
            ['label' => 'Edit Service'],
        ]" class="mb-6" />

        <div class="mb-8">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Edit Service Listing</h1>
                <x-marketplace.status-badge :status="$service->status->value" />
            </div>
            <p class="text-sm text-stone-500 mt-1">
                Update service details, pricing, or inclusions.
            </p>
        </div>

        {{-- Moderation Note if Rejected --}}
        @if($service->status->value === 'rejected' && $service->moderation_note)
            <div class="mb-6">
                <x-marketplace.moderation-note :note="$service->moderation_note" />
            </div>
        @endif

        {{-- Warning if Active --}}
        @if($service->status->value === 'active')
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5">
                <x-ui.icon name="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <strong>Notice:</strong> This service is currently active. Saving changes will automatically return its status to <strong>Pending Review</strong> until an administrator re-approves the modifications.
                </div>
            </div>
        @endif

        <form action="{{ route('provider.services.update', $service->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Service Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           id="title"
                           required
                           value="{{ old('title', $service->title) }}"
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
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $service->category_id) == $cat->id)>
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
                                   value="{{ old('price', number_format($service->price_cents / 100, 2, '.', '')) }}"
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
                               value="{{ old('duration_minutes', $service->duration_minutes) }}"
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
                              class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('description') border-red-500 @enderror">{{ old('description', $service->description) }}</textarea>
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
                                  class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('inclusions', $service->inclusions) }}</textarea>
                    </div>

                    <div>
                        <label for="exclusions" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            What is Excluded (One per line)
                        </label>
                        <textarea name="exclusions"
                                  id="exclusions"
                                  rows="3"
                                  class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('exclusions', $service->exclusions) }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="image" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Update Cover Image (Optional)
                    </label>
                    @if($service->image_path)
                        <div class="mb-2 text-xs text-stone-600 flex items-center gap-2">
                            <span>Current image stored.</span>
                            <label class="inline-flex items-center gap-1 text-red-600 cursor-pointer">
                                <input type="checkbox" name="remove_image" value="1" class="rounded text-red-600">
                                Remove image
                            </label>
                        </div>
                    @endif
                    <input type="file"
                           name="image"
                           id="image"
                           accept="image/*"
                           class="block w-full text-xs text-stone-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('provider.services.index') }}" class="px-4 py-2.5 text-xs font-semibold text-stone-600 hover:text-stone-900">
                    Cancel
                </a>
                <button type="submit" name="action" value="save" class="px-4 py-2.5 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
