@props([
    'label' => 'Data table',
])

<div class="overflow-x-auto rounded-2xl border border-[var(--color-line)] bg-white shadow-xs" tabindex="0" aria-label="{{ $label }}">
    <table class="w-full text-left text-sm text-[var(--color-ink)] divide-y divide-[var(--color-line)]">
        {{ $slot }}
    </table>
</div>
