@props([
    'status',
])

@php
    $statusObj = is_string($status) ? \App\Enums\PaymentStatus::tryFrom($status) : $status;

    if (!$statusObj) {
        $label = ucfirst((string) $status);
        $variant = 'neutral';
    } else {
        $label = $statusObj->label();
        $variant = $statusObj->badgeVariant();
    }
@endphp

<x-ui.badge :variant="$variant">
    <x-ui.icon name="credit-card" class="h-3 w-3" />
    {{ $label }}
</x-ui.badge>
