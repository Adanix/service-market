<?php

namespace App\Enums;

enum ContactMessageStatus: string
{
    case New = 'new';
    case Read = 'read';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Read => 'Read',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::New => 'warning',
            self::Read => 'neutral',
        };
    }
}
