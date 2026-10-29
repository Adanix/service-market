<?php

namespace App\Support;

use InvalidArgumentException;

class Money
{
    public static function format(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';
        return $sign . '€' . number_format(abs($cents) / 100, 2, '.', ',');
    }

    public static function parse(string $value): int
    {
        $clean = trim(str_replace(['€', ' '], '', $value));

        if (str_contains($clean, ',') && str_contains($clean, '.')) {
            $clean = str_replace(',', '', $clean);
        } else {
            $clean = str_replace(',', '.', $clean);
        }

        if (!preg_match('/^\d{1,6}(\.\d{1,2})?$/', $clean)) {
            throw new InvalidArgumentException("Invalid price format: {$value}");
        }

        [$euros, $cents] = array_pad(explode('.', $clean), 2, '0');
        return ((int) $euros * 100) + (int) str_pad(substr($cents, 0, 2), 2, '0');
    }

    public static function toInputFormat(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    /**
     * @return array{fee_cents: int, net_cents: int}
     */
    public static function calculateFeeAndNet(int $priceCents, int $feeBps): array
    {
        $fee = intdiv($priceCents * $feeBps + 5000, 10000);
        $net = $priceCents - $fee;

        return [
            'fee_cents' => $fee,
            'net_cents' => $net,
        ];
    }
}
