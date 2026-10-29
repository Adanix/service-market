@props([
    'variant' => 'neutral',
])

@php
    $variantClasses = match ($variant) {
        'info' => 'bg-blue-50 text-blue-700 border-blue-200',
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
        'danger' => 'bg-red-50 text-red-700 border-red-200',
        default => 'bg-gray-100 text-gray-700 border-gray-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {$variantClasses}"]) }}>
    {{ $slot }}
</span>
