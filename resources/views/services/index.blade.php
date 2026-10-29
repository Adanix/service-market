<x-layouts.app title="Browse Local Services - ServiceLocal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Browse Services</h1>
                <p class="text-sm text-stone-500 mt-1">
                    Showing {{ $services->total() }} available services in the Republic of Ireland
                </p>
            </div>
        </div>

        {{-- Main Layout: Sidebar Filters + Services Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Filter Sidebar --}}
            <aside class="lg:col-span-1">
                <form action="{{ route('services.index') }}" method="GET" class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm space-y-5">
                    <div>
                        <label for="q" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Keyword</label>
                        <input type="text" name="q" id="q" value="{{ $q }}" placeholder="Search services..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label for="category" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category" id="category" class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="county" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">County</label>
                        <select name="county" id="county" class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">All Counties</option>
                            @foreach($counties as $county)
                                <option value="{{ $county->code }}" @selected($selectedCounty === $county->code)>
                                    {{ $county->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Price Range (€)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="price_min" placeholder="Min" value="{{ $priceMin }}" min="0" step="5" class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="number" name="price_max" placeholder="Max" value="{{ $priceMax }}" min="0" step="5" class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label for="sort" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">Sort By</label>
                        <select name="sort" id="sort" class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="newest" @selected($sort === 'newest')>Newest Listings</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>Price: Low to High</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>Price: High to Low</option>
                        </select>
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <x-ui.button type="submit" variant="primary" class="w-full">
                            Apply Filters
                        </x-ui.button>
                        <a href="{{ route('services.index') }}" class="text-xs text-center text-stone-500 hover:text-stone-800 py-1">
                            Reset Filters
                        </a>
                    </div>
                </form>
            </aside>

            {{-- Services Grid --}}
            <main class="lg:col-span-3">
                @if($services->isEmpty())
                    <x-ui.empty-state
                        title="No services match your criteria"
                        description="Try broadening your search keyword, selecting a different county, or resetting filters."
                        icon="search-x"
                    >
                        <div class="mt-4">
                            <a href="{{ route('services.index') }}" class="text-sm font-semibold text-emerald-700 hover:underline">
                                Clear all filters
                            </a>
                        </div>
                    </x-ui.empty-state>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($services as $service)
                            <x-marketplace.service-card :service="$service" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $services->links() }}
                    </div>
                @endif
            </main>
        </div>
    </div>
</x-layouts.app>
