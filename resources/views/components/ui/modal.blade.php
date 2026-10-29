@props([
    'name',
    'title' => null,
    'maxWidth' => 'max-w-lg',
])

<div x-data="{ open: false }"
     x-cloak
     x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
     x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     class="relative z-50"
     role="dialog"
     aria-modal="true"
     @if($title) aria-labelledby="modal-title-{{ $name }}" @endif>

    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         @click="open = false"></div>

    {{-- Modal container --}}
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 md:p-20 flex min-h-full items-center justify-center">
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full {{ $maxWidth }} transform overflow-hidden rounded-2xl bg-white border border-[var(--color-line)] shadow-xl transition-all">
            
            @if ($title)
                <div class="flex items-center justify-between border-b border-[var(--color-line)] px-6 py-4">
                    <h3 class="text-base font-semibold text-[var(--color-ink)]" id="modal-title-{{ $name }}">
                        {{ $title }}
                    </h3>
                    <button type="button"
                            @click="open = false"
                            class="rounded-lg p-1 text-[var(--color-muted)] hover:bg-gray-100 hover:text-[var(--color-ink)] focus:outline-none focus:ring-2 focus:ring-[var(--color-teal-600)] cursor-pointer"
                            aria-label="Close modal">
                        <x-ui.icon name="x" class="h-5 w-5" />
                    </button>
                </div>
            @endif

            <div class="p-6 max-h-[calc(100vh-12rem)] overflow-y-auto">
                {{ $slot }}
            </div>

            @if (isset($footer))
                <div class="bg-[var(--color-page)] px-6 py-4 border-t border-[var(--color-line)] flex items-center justify-end gap-3">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
