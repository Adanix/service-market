@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs gap-1.5 min-h-[36px]',
        default => 'px-4 py-2.5 text-sm gap-2 min-h-[44px]',
    };

    $variantClasses = match ($variant) {
        'primary' => 'bg-[var(--color-teal-500)] text-white hover:bg-[var(--color-teal-600)] focus:ring-[var(--color-teal-600)] shadow-sm',
        'secondary' => 'bg-white text-[var(--color-ink)] border border-[var(--color-line)] hover:bg-[var(--color-page)] focus:ring-[var(--color-teal-600)] shadow-xs',
        'ghost' => 'bg-transparent text-[var(--color-ink)] hover:bg-[var(--color-line)]/50 focus:ring-[var(--color-teal-600)]',
        'danger' => 'bg-[var(--color-danger-600)] text-white hover:bg-[var(--color-danger-600)]/90 focus:ring-[var(--color-danger-600)] shadow-sm',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
