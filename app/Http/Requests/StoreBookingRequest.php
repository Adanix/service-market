<?php

namespace App\Http\Requests;

use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isCustomer();
    }

    public function rules(): array
    {
        $settings = app(SettingsService::class);
        $minNoticeHours = $settings->minNoticeHours();
        $maxAdvanceDays = $settings->maxAdvanceDays();

        $minDate = now()->addHours($minNoticeHours)->format('Y-m-d H:i');
        $maxDate = now()->addDays($maxAdvanceDays)->format('Y-m-d H:i');

        return [
            'preferred_start_at' => [
                'required',
                'date',
                "after:{$minDate}",
                "before:{$maxDate}",
            ],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'cardholder_name' => ['required', 'string', 'max:80'],
            'card_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $cleaned = preg_replace('/\s+/', '', $value);
                    if (!preg_match('/^\d{16}$/', $cleaned)) {
                        $fail('The card number must be a 16-digit card number.');
                    }
                },
            ],
            'card_expiry' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', trim($value), $matches)) {
                        $fail('The expiry date must be in MM/YY format.');
                        return;
                    }

                    $month = (int) $matches[1];
                    $year = 2000 + (int) $matches[2];
                    $expiry = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                    if ($expiry->isPast()) {
                        $fail('The card expiry date cannot be in the past.');
                    }
                },
            ],
            'card_cvc' => ['required', 'regex:/^\d{3}$/'],
            'accept_terms' => ['accepted'],
            'idempotency_key' => ['required', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'preferred_start_at.after' => 'The start time must give the provider sufficient advance notice.',
            'preferred_start_at.before' => 'The booking date is too far in advance.',
            'accept_terms.accepted' => 'You must agree to the cancellation policy.',
            'card_cvc.regex' => 'The security code must be 3 digits.',
        ];
    }
}
