@props([
    'variant' => 'info',
    'title' => null,
])

@php
    $variantClasses = match ($variant) {
        'success' => 'bg-emerald-50 text-emerald-900 border-emerald-200',
        'warning' => 'bg-amber-50 text-amber-900 border-amber-200',
        'error', 'danger' => 'bg-red-50 text-red-900 border-red-200',
        default => 'bg-blue-50 text-blue-900 border-blue-200',
    };

    $iconName = match ($variant) {
        'success' => 'circle-check',
        'warning', 'error', 'danger' => 'triangle-alert',
        default => 'info',
    };

    $role = in_array($variant, ['error', 'danger', 'warning']) ? 'alert' : 'status';
@endphp

<div role="{{ $role }}" {{ $attributes->merge(['class' => "rounded-2xl border p-4 {$variantClasses} flex items-start gap-3 shadow-xs"]) }}>
    <div class="shrink-0 mt-0.5">
        <x-ui.icon :name="$iconName" class="h-5 w-5" />
    </div>
    <div class="flex-1 text-sm leading-6">
        @if ($title)
            <h3 class="font-semibold mb-1">{{ $title }}</h3>
        @endif
        {{ $slot }}
    </div>
</div>
