@props([
    'amount' => 0,
    'cents' => null,
])

@php
    $value = $cents !== null ? (int) $cents : (int) $amount;
@endphp

<span>{{ \App\Support\Money::format($value) }}</span>
