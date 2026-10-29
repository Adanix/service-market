<x-layouts.app :title="$provider->business_name . ' - Provider Profile'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Services', 'url' => route('services.index')],
            ['label' => $provider->business_name],
        ]" class="mb-6" />

        {{-- Provider Banner & Header --}}
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200 mb-8 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <x-ui.avatar :name="$provider->business_name" size="lg" />
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900">{{ $provider->business_name }}</h1>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                <x-ui.icon name="check" class="w-3.5 h-3.5" />
                                Verified Provider
                            </span>
                        </div>
                        <p class="text-sm text-stone-500 mt-1 flex items-center gap-3">
                            <span class="flex items-center gap-1">
                                <x-ui.icon name="map-pin" class="w-4 h-4 text-stone-400" />
                                Base: {{ $provider->county->name }}
                            </span>
                            @if($provider->phone)
                                <span class="flex items-center gap-1">
                                    <x-ui.icon name="phone" class="w-4 h-4 text-stone-400" />
                                    {{ $provider->phone }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="text-sm text-stone-600 bg-stone-50 px-4 py-2.5 rounded-xl border border-stone-200">
                    <span class="font-bold text-stone-900">{{ $services->count() }}</span> Active Services Listed
                </div>
            </div>

            @if($provider->description)
                <div class="mt-6 pt-6 border-t border-stone-100 text-sm text-stone-700 leading-relaxed max-w-3xl">
                    {{ $provider->description }}
                </div>
            @endif
        </div>

        {{-- Services Catalog --}}
        <div>
            <h2 class="text-xl font-bold text-stone-900 mb-6">Services by {{ $provider->business_name }}</h2>
            @if($services->isEmpty())
                <x-ui.empty-state
                    title="No active services"
                    description="This provider does not have any active service listings currently."
                    icon="package"
                />
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($services as $service)
                        <x-marketplace.service-card :service="$service" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
