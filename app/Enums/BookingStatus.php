<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PendingProvider = 'pending_provider';
    case Accepted = 'accepted';
    case InProgress = 'in_progress';
    case AwaitingConfirmation = 'awaiting_confirmation';
    case Completed = 'completed';
    case Declined = 'declined';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return $this->customerLabel();
    }

    public function customerLabel(): string
    {
        return match ($this) {
            self::PendingProvider => 'Pending provider',
            self::Accepted => 'Accepted',
            self::InProgress => 'In progress',
            self::AwaitingConfirmation => 'Awaiting your confirmation',
            self::Completed => 'Completed',
            self::Declined => 'Declined',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
        };
    }

    public function providerLabel(): string
    {
        return match ($this) {
            self::PendingProvider => 'New request',
            self::Accepted => 'Accepted',
            self::InProgress => 'In progress',
            self::AwaitingConfirmation => 'Awaiting customer',
            self::Completed => 'Completed',
            self::Declined => 'Declined',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::PendingProvider => 'warning',
            self::Accepted, self::InProgress => 'info',
            self::AwaitingConfirmation => 'warning',
            self::Completed => 'success',
            self::Declined, self::Cancelled, self::Expired => 'neutral',
        };
    }
}
