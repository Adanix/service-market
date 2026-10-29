@props([
    'items' => [],
])

<nav class="flex items-center text-sm text-[var(--color-muted)] mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 flex-wrap">
        <li>
            <a href="{{ route('home') }}" class="hover:text-[var(--color-teal-600)] transition">Home</a>
        </li>
        @foreach ($items as $item)
            <li class="flex items-center gap-2">
                <x-ui.icon name="chevron-right" class="h-4 w-4 shrink-0 text-[var(--color-line)]" />
                @if (isset($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="hover:text-[var(--color-teal-600)] transition">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-[var(--color-ink)]" aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
