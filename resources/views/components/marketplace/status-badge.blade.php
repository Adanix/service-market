@props([
    'status',
    'role' => 'customer',
])

@php
    $statusObj = is_string($status) ? \App\Enums\BookingStatus::tryFrom($status) : $status;
    
    if (!$statusObj) {
        $label = ucfirst((string) $status);
        $variant = 'neutral';
    } else {
        $label = match ($role) {
            'provider' => $statusObj->providerLabel(),
            default => $statusObj->customerLabel(),
        };
        $variant = $statusObj->badgeVariant();
    }
@endphp

<x-ui.badge :variant="$variant">
    {{ $label }}
</x-ui.badge>
