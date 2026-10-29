@props([
    'tabs' => [],
    'active' => null,
])

<div class="border-b border-[var(--color-line)] flex items-center gap-6 overflow-x-auto">
    {{ $slot }}
</div>
