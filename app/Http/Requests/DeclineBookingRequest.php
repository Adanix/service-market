<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeclineBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isProvider();
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }
}
