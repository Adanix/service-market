@props([
    'title',
    'subtitle' => null,
])

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-3xl md:text-4xl font-semibold tracking-tight text-[var(--color-navy-900)]">
            {{ $title }}
        </h1>
        @if ($subtitle)
            <p class="mt-2 text-base text-[var(--color-muted)] max-w-3xl leading-relaxed">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex items-center gap-3 shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>
