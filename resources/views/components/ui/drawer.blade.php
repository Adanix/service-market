@props([
    'name',
    'title' => null,
])

<div x-data="{ open: false }"
     x-cloak
     x-on:open-drawer.window="if ($event.detail === '{{ $name }}') open = true"
     x-on:close-drawer.window="if ($event.detail === '{{ $name }}') open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     class="relative z-50 lg:hidden"
     role="dialog"
     aria-modal="true">

    <div x-show="open"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         @click="open = false"></div>

    <div class="fixed inset-0 flex justify-end">
        <div x-show="open"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-full max-w-xs sm:max-w-sm bg-white h-full shadow-2xl flex flex-col">

            <div class="flex items-center justify-between border-b border-[var(--color-line)] px-5 py-4">
                <h3 class="text-base font-semibold text-[var(--color-ink)]">
                    {{ $title ?? 'Filters' }}
                </h3>
                <button type="button"
                        @click="open = false"
                        class="rounded-lg p-1 text-[var(--color-muted)] hover:bg-gray-100 hover:text-[var(--color-ink)] focus:outline-none focus:ring-2 focus:ring-[var(--color-teal-600)] cursor-pointer"
                        aria-label="Close drawer">
                    <x-ui.icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div class="p-5 flex-1 overflow-y-auto">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
