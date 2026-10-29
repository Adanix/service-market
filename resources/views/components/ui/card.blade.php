@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white border border-[var(--color-line)] shadow-xs ' . ($padding ? 'p-5 md:p-6' : '')]) }}>
    {{ $slot }}
</div>
