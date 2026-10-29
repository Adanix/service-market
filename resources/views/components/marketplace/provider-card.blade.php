@props([
    'provider',
])

<div class="rounded-2xl bg-white border border-[var(--color-line)] p-5 shadow-xs flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <x-ui.avatar :name="$provider->business_name" size="md" />
        <div>
            <h4 class="text-sm font-semibold text-[var(--color-ink)]">{{ $provider->business_name }}</h4>
            <div class="flex items-center gap-2 mt-0.5">
                <x-marketplace.county-badge :county="$provider->county" />
                @if ($provider->services_count ?? null)
                    <span class="text-xs text-[var(--color-muted)]">{{ $provider->services_count }} services</span>
                @endif
            </div>
        </div>
    </div>

    <x-ui.button variant="secondary" size="sm" :href="route('providers.show', $provider->slug)">
        View provider
    </x-ui.button>
</div>
