@props([
    'label' => null,
    'title' => null,
    'value',
    'icon' => null,
    'subtext' => null,
])

@php
    $displayLabel = $label ?? $title ?? '';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white border border-[var(--color-line)] p-5 shadow-xs flex items-center justify-between gap-4']) }}>
    <div class="space-y-1">
        <p class="text-xs font-medium uppercase tracking-wider text-[var(--color-muted)]">{{ $displayLabel }}</p>
        <p class="text-2xl font-bold tracking-tight text-[var(--color-ink)]">{{ $value }}</p>
        @if ($subtext)
            <p class="text-xs text-[var(--color-muted)]">{{ $subtext }}</p>
        @endif
    </div>
    @if ($icon)
        <div class="h-12 w-12 rounded-xl bg-[var(--color-page)] flex items-center justify-center text-[var(--color-teal-600)] shrink-0">
            <x-ui.icon :name="$icon" class="h-6 w-6" />
        </div>
    @endif
</div>
