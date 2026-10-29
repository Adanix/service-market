@props([
    'title',
    'description' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-[var(--color-line)] bg-white/60 p-8 md:p-12 text-center']) }}>
    <div class="mx-auto h-12 w-12 rounded-full bg-[var(--color-page)] flex items-center justify-center text-[var(--color-muted)] mb-4">
        <x-ui.icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="text-base font-semibold text-[var(--color-ink)]">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-[var(--color-muted)] max-w-md mx-auto">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-6 flex justify-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
