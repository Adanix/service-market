@props([
    'county',
])

@php
    $name = is_object($county) ? $county->name : (string) $county;
@endphp

<span class="inline-flex items-center gap-1 text-xs font-medium text-[var(--color-muted)] bg-[var(--color-page)] border border-[var(--color-line)] px-2.5 py-0.5 rounded-full">
    <x-ui.icon name="map-pin" class="h-3.5 w-3.5 text-[var(--color-muted)]" />
    {{ $name }}
</span>
