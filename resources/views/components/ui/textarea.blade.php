@props([
    'disabled' => false,
    'error' => false,
    'rows' => 4,
])

@php
    $baseClasses = 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-[var(--color-ink)] transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:bg-gray-100 disabled:cursor-not-allowed';
    $borderClasses = $error
        ? 'border-[var(--color-danger-600)] focus:border-[var(--color-danger-600)] focus:ring-[var(--color-danger-600)]'
        : 'border-[var(--color-line)] focus:border-[var(--color-teal-600)] focus:ring-[var(--color-teal-600)]';
    $classes = "{$baseClasses} {$borderClasses}";
@endphp

<textarea rows="{{ $rows }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</textarea>
