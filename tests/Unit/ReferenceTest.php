<?php

use App\Support\Reference;

test('it generates properly prefixed references', function () {
    expect(Reference::booking())->toStartWith('BK-');
    expect(Reference::payment())->toStartWith('PAY-');
    expect(Reference::gateway())->toStartWith('SIM-');
    expect(Reference::payout())->toStartWith('PO-');
});

test('it generates unique references', function () {
    $bookings = [];
    $payments = [];
    $payouts = [];

    for ($i = 0; $i < 100; $i++) {
        $bookings[] = Reference::booking();
        $payments[] = Reference::payment();
        $payouts[] = Reference::payout();
    }

    expect(count(array_unique($bookings)))->toBe(100);
    expect(count(array_unique($payments)))->toBe(100);
    expect(count(array_unique($payouts)))->toBe(100);
});
