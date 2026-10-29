<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'reference',
        'driver',
        'status',
        'amount_cents',
        'currency',
        'card_brand',
        'card_last4',
        'gateway_reference',
        'idempotency_key',
        'attempt_count',
        'failure_code',
        'failure_message',
        'authorized_at',
        'capture_before',
        'captured_at',
        'voided_at',
        'refunded_at',
        'refund_amount_cents',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount_cents' => 'integer',
            'attempt_count' => 'integer',
            'refund_amount_cents' => 'integer',
            'authorized_at' => 'datetime',
            'capture_before' => 'datetime',
            'captured_at' => 'datetime',
            'voided_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
