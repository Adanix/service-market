<x-layouts.app title="ServiceLocal - Local Home and Professional Services in Ireland">
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-b from-stone-900 to-stone-800 text-white py-16 sm:py-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl text-center sm:text-left">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-6">
                    <x-ui.icon name="shield-check" class="w-3.5 h-3.5" />
                    Trusted Local Marketplace across all 26 Irish Counties
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white mb-6 leading-tight">
                    Reliable local home services, booked with confidence.
                </h1>
                <p class="text-lg sm:text-xl text-stone-300 mb-8 leading-relaxed">
                    Find vetted local trades, cleaners, tutors and specialists. Book with simulated card authorization—you only pay once the work is completed and confirmed.
                </p>

                {{-- Search Box --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-2xl text-stone-900 border border-stone-200">
                    <form action="{{ route('services.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-4">
                            <label for="q" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                    <x-ui.icon name="search" class="w-5 h-5" />
                                </div>
                                <input type="text" name="q" id="q" placeholder="What service do you need?" class="block w-full pl-10 pr-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="category" class="sr-only">Category</label>
                            <select name="category" id="category" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="county" class="sr-only">County</label>
                            <select name="county" id="county" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Counties</option>
                                @foreach($counties as $co)
                                    <option value="{{ $co->code }}">{{ $co->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <button type="submit" class="w-full h-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-sm shadow transition-colors flex items-center justify-center gap-2">
                                <x-ui.icon name="search" class="w-4 h-4" />
                                <span>Search</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Bar --}}
    <section class="bg-stone-100 border-b border-stone-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-stone-900">{{ $stats['services_count'] }}+</div>
                    <div class="text-xs text-stone-500 font-medium mt-0.5">Active Services</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-stone-900">{{ $stats['providers_count'] }}</div>
                    <div class="text-xs text-stone-500 font-medium mt-0.5">Approved Providers</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-stone-900">{{ $stats['counties_count'] }}</div>
                    <div class="text-xs text-stone-500 font-medium mt-0.5">Counties Covered</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['completed_bookings'] }}+</div>
                    <div class="text-xs text-stone-500 font-medium mt-0.5">Completed Bookings</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Category Strip --}}
    <section class="py-14 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-stone-900">Browse by Category</h2>
                    <p class="text-sm text-stone-500 mt-1">Explore verified professional services across Ireland</p>
                </div>
                <a href="{{ route('services.index') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                    View all <x-ui.icon name="arrow-right" class="w-4 h-4" />
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
                @foreach($categories as $category)
                    <a href="{{ route('services.index', ['category' => $category->slug]) }}" class="group flex flex-col items-center text-center p-4 rounded-xl border border-stone-200 hover:border-emerald-500 hover:shadow-md transition-all bg-stone-50/50 hover:bg-white">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-ui.icon :name="$category->icon" class="w-6 h-6" />
                        </div>
                        <span class="text-xs font-semibold text-stone-800 group-hover:text-emerald-700">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Popular Services --}}
    <section class="py-14 bg-stone-50 border-y border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-stone-900">Popular Services</h2>
                    <p class="text-sm text-stone-500 mt-1">Top-rated services available for booking today</p>
                </div>
                <a href="{{ route('services.index') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                    Browse catalog <x-ui.icon name="arrow-right" class="w-4 h-4" />
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($popularServices as $service)
                    <x-marketplace.service-card :service="$service" />
                @empty
                    <div class="col-span-full py-12 text-center text-stone-500">
                        No services available currently.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold text-emerald-700 tracking-wider uppercase">Simple & Transparent</span>
                <h2 class="text-3xl font-extrabold text-stone-900 mt-2">How ServiceLocal Works</h2>
                <p class="text-stone-500 mt-2 text-sm">We protect both customer and provider with an escrow-like authorization model.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg mb-4">
                        1
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">Find & Book</h3>
                    <p class="text-sm text-stone-600 leading-relaxed">
                        Search verified providers in your county. Choose a service with transparent fixed pricing and pick your preferred time.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg mb-4">
                        2
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">Simulated Authorization</h3>
                    <p class="text-sm text-stone-600 leading-relaxed">
                        Authorize with a demo test card. Your card is held in reserve for up to 7 days, but zero funds are captured upfront.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg mb-4">
                        3
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 mb-2">Confirm & Release</h3>
                    <p class="text-sm text-stone-600 leading-relaxed">
                        The provider performs the service. Once marked complete, review and confirm to release payment to the provider.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Providers --}}
    @if($featuredProviders->isNotEmpty())
        <section class="py-14 bg-stone-50 border-t border-stone-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-stone-900">Featured Local Providers</h2>
                        <p class="text-sm text-stone-500 mt-1">Vetted professionals ready to help in your area</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredProviders as $provider)
                        <x-marketplace.provider-card :provider="$provider" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Provider CTA Banner --}}
    <section class="py-16 bg-stone-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-emerald-950 to-stone-900 border border-emerald-800/50 rounded-3xl p-8 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-xl">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Are you a service professional in Ireland?</h2>
                    <p class="mt-3 text-stone-300 text-sm sm:text-base leading-relaxed">
                        List your services on ServiceLocal to reach homeowners across your county. Enjoy protected payments, automated payout accounting, and a clear booking schedule.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('demo.sign-in') }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-center shadow-lg transition-colors">
                        Try Provider Demo
                    </a>
                    <a href="{{ route('docs') }}" class="px-6 py-3 bg-stone-800 hover:bg-stone-700 text-stone-200 font-medium rounded-xl text-center border border-stone-700 transition-colors">
                        Read Marketplace Specs
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
