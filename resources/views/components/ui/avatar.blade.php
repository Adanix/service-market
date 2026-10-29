@props([
    'name' => '',
    'size' => 'md',
    'src' => null,
])

@php
    $words = explode(' ', trim($name));
    $initials = '';
    if (count($words) >= 2) {
        $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
    } else {
        $initials = strtoupper(substr($name, 0, 2));
    }

    $sizeClasses = match ($size) {
        'sm' => 'h-8 w-8 text-xs',
        'lg' => 'h-14 w-14 text-lg',
        default => 'h-10 w-10 text-sm',
    };
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}" class="rounded-full object-cover {{ $sizeClasses }}">
@else
    <div class="rounded-full bg-[var(--color-navy-900)] text-white font-semibold flex items-center justify-center shrink-0 {{ $sizeClasses }}" aria-hidden="true">
        {{ $initials }}
    </div>
@endif
