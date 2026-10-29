@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <div class="flex items-center justify-between">
            <label @if($name) for="{{ $name }}" @endif class="block text-sm font-medium text-[var(--color-ink)]">
                {{ $label }}
                @if ($required)
                    <span class="text-xs font-normal text-[var(--color-danger-600)] ml-1" title="Required">*</span>
                @endif
            </label>
        </div>
    @endif

    {{ $slot }}

    @if ($hint && !$error)
        <p class="text-xs text-[var(--color-muted)]">{{ $hint }}</p>
    @endif

    @if ($error)
        <p class="text-xs text-[var(--color-danger-600)] font-medium">{{ $error }}</p>
    @endif
</div>
