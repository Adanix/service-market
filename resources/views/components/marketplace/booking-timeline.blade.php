@props([
    'booking',
])

@php
    $status = $booking->status->value;
    $isTerminalFailure = in_array($status, ['declined', 'cancelled', 'expired']);

    $steps = [
        'pending_provider' => ['label' => 'Requested', 'time' => $booking->created_at],
        'accepted' => ['label' => 'Accepted', 'time' => $booking->accepted_at],
        'in_progress' => ['label' => 'In progress', 'time' => $booking->started_at],
        'awaiting_confirmation' => ['label' => 'Awaiting confirmation', 'time' => $booking->provider_completed_at],
        'completed' => ['label' => 'Completed', 'time' => $booking->completed_at],
    ];

    $order = ['pending_provider', 'accepted', 'in_progress', 'awaiting_confirmation', 'completed'];
    $currentIndex = array_search($status, $order);
    if ($currentIndex === false && !$isTerminalFailure) {
        $currentIndex = 0;
    }
@endphp

@if ($isTerminalFailure)
    <div class="rounded-2xl border border-[var(--color-line)] bg-gray-50 p-6 text-center">
        <div class="mx-auto h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 mb-3">
            @if ($status === 'declined')
                <x-ui.icon name="ban" class="h-6 w-6" />
            @elseif ($status === 'cancelled')
                <x-ui.icon name="x" class="h-6 w-6" />
            @else
                <x-ui.icon name="clock" class="h-6 w-6" />
            @endif
        </div>
        <h4 class="text-base font-semibold text-[var(--color-ink)] capitalize">
            Booking {{ $status }}
        </h4>
        @if ($booking->cancellation_reason)
            <p class="mt-1 text-sm text-[var(--color-muted)]">Reason: {{ $booking->cancellation_reason }}</p>
        @elseif ($booking->decline_reason)
            <p class="mt-1 text-sm text-[var(--color-muted)]">Reason: {{ $booking->decline_reason }}</p>
        @elseif ($status === 'expired')
            <p class="mt-1 text-sm text-[var(--color-muted)]">Payment authorization expired without completion.</p>
        @endif
    </div>
@else
    <nav aria-label="Booking progress" class="py-4">
        <ol class="flex items-center justify-between w-full relative">
            @foreach ($order as $index => $stepKey)
                @php
                    $step = $steps[$stepKey];
                    $isPast = $currentIndex !== false && $index < $currentIndex;
                    $isCurrent = $currentIndex !== false && $index === $currentIndex;
                @endphp
                <li class="relative flex-1 flex flex-col items-center text-center {{ !$loop->last ? 'after:content-[\'\'] after:w-full after:h-0.5 after:bg-[var(--color-line)] after:absolute after:top-4 after:left-1/2 after:-z-10' : '' }} {{ $isPast ? 'after:!bg-[var(--color-teal-500)]' : '' }}"
                    @if ($isCurrent) aria-current="step" @endif>
                    
                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-semibold border-2 transition z-10 bg-white
                                {{ $isPast ? 'border-[var(--color-teal-500)] bg-[var(--color-teal-500)] text-white' : '' }}
                                {{ $isCurrent ? 'border-[var(--color-teal-600)] text-[var(--color-teal-600)] ring-4 ring-[var(--color-teal-50)]' : '' }}
                                {{ !$isPast && !$isCurrent ? 'border-[var(--color-line)] text-[var(--color-muted)]' : '' }}">
                        @if ($isPast)
                            <x-ui.icon name="check" class="h-4 w-4" />
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>

                    <span class="mt-2 text-xs font-medium {{ $isCurrent ? 'text-[var(--color-teal-600)] font-semibold' : ($isPast ? 'text-[var(--color-ink)]' : 'text-[var(--color-muted)]') }}">
                        {{ $step['label'] }}
                    </span>

                    @if ($step['time'])
                        <span class="text-[10px] text-[var(--color-muted)]">
                            {{ $step['time']->timezone(config('marketplace.timezone', 'Europe/Dublin'))->format('d M, H:i') }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
