@props([
    'disabled' => false,
    'error' => false,
])

@php
    $baseClasses = 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-[var(--color-ink)] transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:bg-gray-100 disabled:cursor-not-allowed min-h-[44px] cursor-pointer';
    $borderClasses = $error
        ? 'border-[var(--color-danger-600)] focus:border-[var(--color-danger-600)] focus:ring-[var(--color-danger-600)]'
        : 'border-[var(--color-line)] focus:border-[var(--color-teal-600)] focus:ring-[var(--color-teal-600)]';
    $classes = "{$baseClasses} {$borderClasses}";
@endphp

<select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</select>
