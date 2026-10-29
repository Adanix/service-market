@props([
    'note',
])

@if ($note)
    <div class="rounded-xl bg-amber-50 border border-amber-200 p-3.5 text-xs text-amber-900 flex items-start gap-2.5">
        <x-ui.icon name="info" class="h-4 w-4 text-amber-700 shrink-0 mt-0.5" />
        <div>
            <span class="font-semibold block mb-0.5">Moderation note:</span>
            <span>{{ $note }}</span>
        </div>
    </div>
@endif
