@props([
    'service',
])

<div class="group rounded-2xl bg-white border border-[var(--color-line)] p-5 md:p-6 shadow-xs hover:shadow-md transition duration-200 flex flex-col justify-between h-full">
    <div>
        {{-- Card Header --}}
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-xl bg-[var(--color-page)] flex items-center justify-center text-[var(--color-teal-600)]">
                    <x-ui.icon :name="$service->category->icon ?? 'briefcase'" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-medium text-[var(--color-muted)]">{{ $service->category->name ?? 'General' }}</p>
                    <x-marketplace.county-badge :county="$service->county" />
                </div>
            </div>
            @if ($service->duration_minutes)
                <span class="inline-flex items-center gap-1 text-xs text-[var(--color-muted)]">
                    <x-ui.icon name="clock" class="h-3.5 w-3.5" />
                    {{ $service->duration_minutes }} min
                </span>
            @endif
        </div>

        {{-- Title and Provider --}}
        <h3 class="text-lg font-semibold text-[var(--color-navy-900)] group-hover:text-[var(--color-teal-600)] transition line-clamp-2 mb-1">
            <a href="{{ route('services.show', $service->slug) }}">
                {{ $service->title }}
            </a>
        </h3>

        <p class="text-xs text-[var(--color-muted)] mb-3">
            by <a href="{{ route('providers.show', $service->providerProfile->slug) }}" class="hover:underline font-medium text-[var(--color-ink)]">{{ $service->providerProfile->business_name }}</a>
        </p>

        {{-- Description excerpt --}}
        <p class="text-sm text-[var(--color-muted)] line-clamp-3 leading-relaxed mb-6">
            {{ $service->description }}
        </p>
    </div>

    {{-- Footer Price and Action --}}
    <div class="pt-4 border-t border-[var(--color-line)] flex items-center justify-between gap-4 mt-auto">
        <div>
            <span class="text-xs uppercase tracking-wider text-[var(--color-muted)] block">Fixed price</span>
            <span class="text-xl font-bold text-[var(--color-ink)]">
                <x-ui.money :cents="$service->price_cents" />
            </span>
        </div>

        <x-ui.button variant="secondary" size="sm" :href="route('services.show', $service->slug)">
            View details
        </x-ui.button>
    </div>
</div>
