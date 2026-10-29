<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'customer_id',
        'provider_profile_id',
        'service_id',
        'status',
        'service_title',
        'category_name',
        'provider_name',
        'county_name',
        'price_cents',
        'currency',
        'platform_fee_bps',
        'platform_fee_cents',
        'provider_net_cents',
        'preferred_start_at',
        'customer_note',
        'decline_reason',
        'cancellation_reason',
        'cancelled_by',
        'accepted_at',
        'started_at',
        'provider_completed_at',
        'customer_confirmed_at',
        'completed_at',
        'cancelled_at',
        'expired_at',
        'auto_complete_at',
        'authorization_expires_at',
        'reminder_sent_at',
        'payout_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'price_cents' => 'integer',
            'platform_fee_bps' => 'integer',
            'platform_fee_cents' => 'integer',
            'provider_net_cents' => 'integer',
            'preferred_start_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'provider_completed_at' => 'datetime',
            'customer_confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'expired_at' => 'datetime',
            'auto_complete_at' => 'datetime',
            'authorization_expires_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }
}
