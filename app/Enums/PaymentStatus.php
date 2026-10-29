<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Authorized = 'authorized';
    case Captured = 'captured';
    case Voided = 'voided';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Authorized => 'Authorized',
            self::Captured => 'Captured',
            self::Voided => 'Voided',
            self::Refunded => 'Refunded',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Authorized => 'warning',
            self::Captured => 'success',
            self::Voided => 'neutral',
            self::Refunded => 'danger',
        };
    }
}
