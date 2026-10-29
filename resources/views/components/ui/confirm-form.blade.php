@props([
    'action',
    'method' => 'POST',
    'buttonText' => 'Confirm',
    'buttonVariant' => 'danger',
    'title' => 'Are you sure?',
    'message' => 'Please confirm this action.',
    'triggerText' => 'Perform action',
    'triggerVariant' => 'danger',
    'triggerSize' => 'md',
    'name' => null,
])

@php
    $modalName = $name ?? 'confirm-modal-' . md5($action . $triggerText);
@endphp

<div x-data="{}" class="inline-block">
    <x-ui.button :variant="$triggerVariant" :size="$triggerSize" @click="$dispatch('open-modal', '{{ $modalName }}')">
        {{ $triggerText }}
    </x-ui.button>

    <x-ui.modal :name="$modalName" :title="$title">
        <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-6">
            {{ $message }}
        </p>

        <form action="{{ $action }}" method="POST" class="space-y-4">
            @csrf
            @if (!in_array(strtoupper($method), ['GET', 'POST']))
                @method($method)
            @endif

            {{ $slot }}

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-ui.button variant="secondary" type="button" @click="$dispatch('close-modal', '{{ $modalName }}')">
                    Cancel
                </x-ui.button>
                <x-ui.button :variant="$buttonVariant" type="submit">
                    {{ $buttonText }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
