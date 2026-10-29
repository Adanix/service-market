<x-layouts.app :title="$service->title . ' - ServiceLocal'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Services', 'url' => route('services.index')],
            ['label' => $service->category->name, 'url' => route('services.index', ['category' => $service->category->slug])],
            ['label' => $service->title],
        ]" class="mb-6" />

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            {{-- Left Column: Details --}}
            <div class="lg:col-span-8 space-y-8">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800">
                            <x-ui.icon :name="$service->category->icon" class="w-3.5 h-3.5" />
                            {{ $service->category->name }}
                        </span>
                        <x-marketplace.county-badge :county="$service->county" />
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                        {{ $service->title }}
                    </h1>

                    <div class="mt-4 flex items-center gap-3">
                        <a href="{{ route('providers.show', $service->providerProfile->slug) }}" class="flex items-center gap-2.5 text-stone-700 hover:text-emerald-700 group">
                            <x-ui.avatar :name="$service->providerProfile->business_name" size="sm" />
                            <div>
                                <span class="text-sm font-bold group-hover:underline block leading-tight">{{ $service->providerProfile->business_name }}</span>
                                <span class="text-xs text-stone-500">Verified Service Provider in {{ $service->county->name }}</span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Description --}}
                <div class="bg-white p-6 rounded-2xl border border-stone-200">
                    <h2 class="text-lg font-bold text-stone-900 mb-3">Service Description</h2>
                    <div class="text-stone-700 text-sm leading-relaxed whitespace-pre-line">
                        {{ $service->description }}
                    </div>
                </div>

                {{-- Inclusions & Exclusions --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-stone-200">
                        <div class="flex items-center gap-2 text-emerald-700 font-bold text-sm mb-3">
                            <x-ui.icon name="check-circle" class="w-5 h-5 text-emerald-600" />
                            <span>What is Included</span>
                        </div>
                        @if(empty($service->inclusions_list))
                            <p class="text-xs text-stone-500 italic">Standard labor and tools for the specified service duration.</p>
                        @else
                            <ul class="space-y-2 text-xs text-stone-700">
                                @foreach($service->inclusions_list as $inc)
                                    <li class="flex items-start gap-2">
                                        <x-ui.icon name="check" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                                        <span>{{ $inc }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-stone-200">
                        <div class="flex items-center gap-2 text-stone-800 font-bold text-sm mb-3">
                            <x-ui.icon name="x-circle" class="w-5 h-5 text-stone-400" />
                            <span>What is Excluded</span>
                        </div>
                        @if(empty($service->exclusions_list))
                            <p class="text-xs text-stone-500 italic">Materials or parts not agreed in advance.</p>
                        @else
                            <ul class="space-y-2 text-xs text-stone-600">
                                @foreach($service->exclusions_list as $exc)
                                    <li class="flex items-start gap-2">
                                        <x-ui.icon name="x" class="w-4 h-4 text-stone-400 shrink-0 mt-0.5" />
                                        <span>{{ $exc }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- Related Services --}}
                @if(isset($relatedServices) && $relatedServices->isNotEmpty())
                    <div class="pt-6 border-t border-stone-200">
                        <h2 class="text-lg font-bold text-stone-900 mb-4">Other Services You Might Like</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($relatedServices as $rel)
                                <x-marketplace.service-card :service="$rel" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column: Sticky Booking Card --}}
            <div class="lg:col-span-4 sticky top-20">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-lg space-y-6">
                    <div class="flex items-baseline justify-between border-b border-stone-100 pb-4">
                        <div>
                            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Fixed Rate</span>
                            <x-ui.money :cents="$service->price_cents" class="text-3xl font-extrabold text-stone-900" />
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-stone-500 block">Est. Duration</span>
                            <span class="text-sm font-bold text-stone-800 flex items-center justify-end gap-1">
                                <x-ui.icon name="clock" class="w-4 h-4 text-stone-400" />
                                {{ $service->duration_minutes }} mins
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs text-stone-600">
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="shield-check" class="w-4 h-4 text-emerald-600 shrink-0" />
                            <span>Simulated Authorization (No upfront charge)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="map-pin" class="w-4 h-4 text-emerald-600 shrink-0" />
                            <span>Available in <strong>{{ $service->county->name }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="calendar" class="w-4 h-4 text-emerald-600 shrink-0" />
                            <span>Pick your preferred start date & time</span>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->isCustomer())
                            <a href="{{ route('checkout.show', $service->slug) }}" class="block w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-center rounded-xl shadow transition-colors">
                                Book This Service
                            </a>
                        @else
                            <div class="bg-stone-50 p-3 rounded-xl border border-stone-200 text-xs text-stone-600 text-center">
                                You are signed in as <strong>{{ auth()->user()->role->value }}</strong>. Switch to a Customer account to test checkout.
                                <a href="{{ route('demo.sign-in') }}" class="block text-emerald-700 font-bold hover:underline mt-1.5">
                                    Switch Demo Account
                                </a>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('demo.sign-in') }}" class="block w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-center rounded-xl shadow transition-colors">
                            Sign in as Demo Customer to Book
                        </a>
                    @endauth

                    <div class="text-[11px] text-stone-400 text-center">
                        Funds are held in temporary authorization and only captured after you confirm satisfactory completion.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
