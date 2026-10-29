<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'marketplace_name' => ['required', 'string', 'max:60'],
            'support_email' => ['required', 'email', 'max:120'],
            'platform_fee_bps' => ['required', 'integer', 'min:0', 'max:5000'],
            'authorization_days' => ['required', 'integer', 'min:1', 'max:14'],
            'auto_complete_hours' => ['required', 'integer', 'min:24', 'max:168'],
            'min_notice_hours' => ['required', 'integer', 'min:0', 'max:168'],
            'max_advance_days' => ['required', 'integer', 'min:7', 'max:365'],
            'reminder_hours' => ['required', 'integer', 'min:1', 'max:72'],
        ];
    }
}
