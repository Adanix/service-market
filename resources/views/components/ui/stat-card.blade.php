@props([
    'label' => null,
    'title' => null,
    'value',
    'icon' => null,
    'subtext' => null,
    'color' => 'emerald',
])

@php
    $displayLabel = $label ?? $title ?? '';

    $colorClasses = match ($color) {
        'blue', 'sky' => 'bg-sky-50 text-sky-600 border border-sky-100',
        'amber' => 'bg-amber-50 text-amber-600 border border-amber-100',
        'purple' => 'bg-purple-50 text-purple-600 border border-purple-100',
        'rose' => 'bg-rose-50 text-rose-600 border border-rose-100',
        'indigo' => 'bg-indigo-50 text-indigo-600 border border-indigo-100',
        'slate', 'neutral' => 'bg-stone-50 text-stone-600 border border-stone-200',
        default => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
    };
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white border border-stone-200/80 p-5 shadow-xs hover:shadow-md transition duration-150 flex items-center justify-between gap-4']) }}>
    <div class="space-y-1">
        <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">{{ $displayLabel }}</p>
        <p class="text-2xl font-extrabold tracking-tight text-stone-900">{{ $value }}</p>
        @if ($subtext)
            <p class="text-xs text-stone-500">{{ $subtext }}</p>
        @endif
    </div>
    @if ($icon)
        <div class="h-12 w-12 rounded-xl {{ $colorClasses }} flex items-center justify-center shrink-0 shadow-xs">
            <x-ui.icon :name="$icon" class="h-6 w-6" />
        </div>
    @endif
</div>
