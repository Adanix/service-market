@props([
    'disabled' => false,
])

<input type="checkbox"
       {{ $disabled ? 'disabled' : '' }}
       {{ $attributes->merge(['class' => 'h-5 w-5 rounded-md border-[var(--color-line)] text-[var(--color-teal-600)] focus:ring-[var(--color-teal-600)] transition cursor-pointer']) }}>
