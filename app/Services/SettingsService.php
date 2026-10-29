<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    protected ?array $cache = null;

    public const DEFAULTS = [
        'marketplace_name' => 'ServiceLocal',
        'support_email' => 'support@servicelocal.test',
        'platform_fee_bps' => 1500,
        'authorization_days' => 7,
        'auto_complete_hours' => 72,
        'min_notice_hours' => 24,
        'max_advance_days' => 90,
        'reminder_hours' => 24,
    ];

    protected function loadCache(): void
    {
        if ($this->cache === null) {
            $this->cache = self::DEFAULTS;
            try {
                $dbSettings = Setting::all()->pluck('value', 'key')->toArray();
                foreach ($dbSettings as $key => $value) {
                    if (array_key_exists($key, self::DEFAULTS)) {
                        $this->cache[$key] = is_numeric(self::DEFAULTS[$key]) ? (int) $value : (string) $value;
                    } else {
                        $this->cache[$key] = $value;
                    }
                }
            } catch (\Throwable $e) {
                // Return defaults if table is not yet migrated
            }
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->loadCache();

        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        return $default ?? (self::DEFAULTS[$key] ?? null);
    }

    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );

        if ($this->cache !== null) {
            $this->cache[$key] = is_numeric(self::DEFAULTS[$key] ?? null) ? (int) $value : (string) $value;
        }
    }

    public function clearCache(): void
    {
        $this->cache = null;
    }

    public function all(): array
    {
        $this->loadCache();
        return $this->cache;
    }

    public function marketplaceName(): string
    {
        return (string) $this->get('marketplace_name');
    }

    public function supportEmail(): string
    {
        return (string) $this->get('support_email');
    }

    public function platformFeeBps(): int
    {
        return (int) $this->get('platform_fee_bps');
    }

    public function authorizationDays(): int
    {
        return (int) $this->get('authorization_days');
    }

    public function autoCompleteHours(): int
    {
        return (int) $this->get('auto_complete_hours');
    }

    public function minNoticeHours(): int
    {
        return (int) $this->get('min_notice_hours');
    }

    public function maxAdvanceDays(): int
    {
        return (int) $this->get('max_advance_days');
    }

    public function reminderHours(): int
    {
        return (int) $this->get('reminder_hours');
    }
}
