<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isCustomer();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'phone' => ['nullable', 'regex:/^\+?[0-9 ()-]{7,20}$/'],
            'county_id' => [
                'required',
                Rule::exists('counties', 'id')->where('is_active', true),
            ],
        ];
    }
}
