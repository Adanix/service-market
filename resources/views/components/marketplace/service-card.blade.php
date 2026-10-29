@props([
    'service',
])

@php
    $catSlug = $service->category->slug ?? '';
    $catChipClasses = match(true) {
        str_contains($catSlug, 'clean') => 'bg-sky-50 text-sky-700 border-sky-200/80',
        str_contains($catSlug, 'garden') => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
        str_contains($catSlug, 'plumb') => 'bg-cyan-50 text-cyan-700 border-cyan-200/80',
        str_contains($catSlug, 'electr') => 'bg-amber-50 text-amber-700 border-amber-200/80',
        str_contains($catSlug, 'paint') => 'bg-purple-50 text-purple-700 border-purple-200/80',
        str_contains($catSlug, 'handy') => 'bg-orange-50 text-orange-700 border-orange-200/80',
        str_contains($catSlug, 'mov') => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
        str_contains($catSlug, 'tutor') => 'bg-blue-50 text-blue-700 border-blue-200/80',
        default => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
    };
@endphp

<div class="group rounded-2xl bg-white border border-stone-200 p-5 md:p-6 shadow-xs hover:border-emerald-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between h-full">
    <div>
        {{-- Card Header --}}
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="h-10 w-10 rounded-xl {{ $catChipClasses }} border flex items-center justify-center shrink-0 shadow-2xs">
                    <x-ui.icon :name="$service->category->icon ?? 'briefcase'" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-stone-600">{{ $service->category->name ?? 'General' }}</p>
                    <x-marketplace.county-badge :county="$service->county" />
                </div>
            </div>
            @if ($service->duration_minutes)
                <span class="inline-flex items-center gap-1 text-xs font-medium text-stone-500 bg-stone-50 border border-stone-100 px-2 py-0.5 rounded-lg">
                    <x-ui.icon name="clock" class="h-3.5 w-3.5 text-stone-400" />
                    {{ $service->duration_minutes }} min
                </span>
            @endif
        </div>

        {{-- Title and Provider --}}
        <h3 class="text-lg font-bold text-stone-900 group-hover:text-emerald-700 transition-colors line-clamp-2 mb-1">
            <a href="{{ route('services.show', $service->slug) }}">
                {{ $service->title }}
            </a>
        </h3>

        <p class="text-xs text-stone-500 mb-3">
            by <a href="{{ route('providers.show', $service->providerProfile->slug) }}" class="hover:underline font-semibold text-stone-800 hover:text-emerald-700">{{ $service->providerProfile->business_name }}</a>
        </p>

        {{-- Description excerpt --}}
        <p class="text-sm text-stone-600 line-clamp-3 leading-relaxed mb-6">
            {{ $service->description }}
        </p>
    </div>

    {{-- Footer Price and Action --}}
    <div class="pt-4 border-t border-stone-100 flex items-center justify-between gap-4 mt-auto">
        <div>
            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400 block">Fixed price</span>
            <span class="text-xl font-extrabold text-stone-900 group-hover:text-emerald-700 transition-colors">
                <x-ui.money :cents="$service->price_cents" />
            </span>
        </div>

        <x-ui.button variant="secondary" size="sm" :href="route('services.show', $service->slug)" class="group-hover:border-emerald-500 group-hover:text-emerald-700 font-semibold">
            View details
        </x-ui.button>
    </div>
</div>
