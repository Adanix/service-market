@props([
    'align' => 'right',
    'width' => 'w-56',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        default => 'origin-top-right right-0',
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width }} {{ $alignmentClasses }} rounded-2xl bg-white border border-[var(--color-line)] shadow-lg py-1.5 focus:outline-none"
         style="display: none;">
        {{ $content }}
    </div>
</div>
