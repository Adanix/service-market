<?php

use App\Support\Money;

test('it formats integer cents to standard euro string', function () {
    expect(Money::format(123450))->toBe('€1,234.50');
    expect(Money::format(0))->toBe('€0.00');
    expect(Money::format(50))->toBe('€0.50');
    expect(Money::format(100))->toBe('€1.00');
});

test('it parses various euro input formats to integer cents', function () {
    expect(Money::parse('12.50'))->toBe(1250);
    expect(Money::parse('12,50'))->toBe(1250);
    expect(Money::parse('€1,234.50'))->toBe(123450);
    expect(Money::parse('100'))->toBe(10000);
    expect(Money::parse('0.99'))->toBe(99);
});

test('it calculates platform fee and provider net without loss', function () {
    $result = Money::calculateFeeAndNet(10000, 1500); // 15% of €100.00
    expect($result['fee_cents'])->toBe(1500);
    expect($result['net_cents'])->toBe(8500);
    expect($result['fee_cents'] + $result['net_cents'])->toBe(10000);

    // Test odd rounding
    $resultOdd = Money::calculateFeeAndNet(4500, 1500); // 15% of €45.00 = €6.75
    expect($resultOdd['fee_cents'])->toBe(675);
    expect($resultOdd['net_cents'])->toBe(3825);
    expect($resultOdd['fee_cents'] + $resultOdd['net_cents'])->toBe(4500);
});
