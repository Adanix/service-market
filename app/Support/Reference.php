<?php

namespace App\Support;

use Illuminate\Support\Str;

class Reference
{
    public static function booking(): string
    {
        return 'BK-' . strtoupper(Str::random(10));
    }

    public static function payment(): string
    {
        return 'PAY-' . strtoupper(Str::random(8));
    }

    public static function gateway(): string
    {
        return 'SIM-' . strtoupper(Str::random(20));
    }

    public static function payout(): string
    {
        return 'PO-' . strtoupper(Str::random(6));
    }
}
