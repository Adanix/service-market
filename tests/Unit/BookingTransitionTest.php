<?php

use App\Enums\BookingStatus;
use App\Services\BookingService;

test('it validates allowed booking state transitions', function () {
    // Valid transitions
    expect(BookingService::canTransition(BookingStatus::PendingProvider, BookingStatus::Accepted))->toBeTrue();
    expect(BookingService::canTransition(BookingStatus::PendingProvider, BookingStatus::Declined))->toBeTrue();
    expect(BookingService::canTransition(BookingStatus::PendingProvider, BookingStatus::Cancelled))->toBeTrue();
    expect(BookingService::canTransition(BookingStatus::PendingProvider, BookingStatus::Expired))->toBeTrue();

    expect(BookingService::canTransition(BookingStatus::Accepted, BookingStatus::InProgress))->toBeTrue();
    expect(BookingService::canTransition(BookingStatus::Accepted, BookingStatus::Cancelled))->toBeTrue();
    expect(BookingService::canTransition(BookingStatus::Accepted, BookingStatus::Expired))->toBeTrue();

    expect(BookingService::canTransition(BookingStatus::InProgress, BookingStatus::AwaitingConfirmation))->toBeTrue();
    expect(BookingService::canTransition(BookingStatus::InProgress, BookingStatus::Cancelled))->toBeTrue();

    expect(BookingService::canTransition(BookingStatus::AwaitingConfirmation, BookingStatus::Completed))->toBeTrue();
    expect(BookingService::canTransition(BookingStatus::AwaitingConfirmation, BookingStatus::Cancelled))->toBeTrue();

    expect(BookingService::canTransition(BookingStatus::Completed, BookingStatus::Cancelled))->toBeTrue();
});

test('it rejects illegal booking state transitions', function () {
    // From pending_provider cannot jump to completed directly
    expect(BookingService::canTransition(BookingStatus::PendingProvider, BookingStatus::Completed))->toBeFalse();
    expect(BookingService::canTransition(BookingStatus::PendingProvider, BookingStatus::InProgress))->toBeFalse();

    // From completed cannot go back to accepted or in_progress
    expect(BookingService::canTransition(BookingStatus::Completed, BookingStatus::Accepted))->toBeFalse();
    expect(BookingService::canTransition(BookingStatus::Completed, BookingStatus::InProgress))->toBeFalse();

    // Terminal states cannot transition
    expect(BookingService::canTransition(BookingStatus::Declined, BookingStatus::Accepted))->toBeFalse();
    expect(BookingService::canTransition(BookingStatus::Cancelled, BookingStatus::Accepted))->toBeFalse();
    expect(BookingService::canTransition(BookingStatus::Expired, BookingStatus::Completed))->toBeFalse();
});
