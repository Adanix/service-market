<?php

namespace App\Models;

use App\Enums\ProviderStatus;
use App\Enums\ServiceStatus;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_profile_id',
        'category_id',
        'title',
        'slug',
        'description',
        'inclusions',
        'exclusions',
        'price_cents',
        'currency',
        'duration_minutes',
        'county_id',
        'image_path',
        'status',
        'moderation_note',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ServiceStatus::class,
            'price_cents' => 'integer',
            'duration_minutes' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ServiceStatus::Active);
    }

    public function scopePublicListing(Builder $query): Builder
    {
        return $query->where('services.status', ServiceStatus::Active)
            ->whereHas('providerProfile', function (Builder $q) {
                $q->where('status', ProviderStatus::Approved)
                    ->whereHas('user', function (Builder $qu) {
                        $qu->where('status', UserStatus::Active);
                    });
            });
    }

    public function getInclusionsListAttribute(): array
    {
        if (empty($this->inclusions)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $this->inclusions)))));
    }

    public function getExclusionsListAttribute(): array
    {
        if (empty($this->exclusions)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $this->exclusions)))));
    }
}
