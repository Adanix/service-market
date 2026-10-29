<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isProvider();
    }

    public function rules(): array
    {
        $providerProfileId = $this->user()->providerProfile?->id;

        return [
            'title' => [
                'required',
                'string',
                'min:5',
                'max:90',
                Rule::unique('services', 'title')->where('provider_profile_id', $providerProfileId),
            ],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('is_active', true),
            ],
            'description' => ['required', 'string', 'min:40', 'max:3000'],
            'inclusions' => ['nullable', 'string', 'max:1000'],
            'exclusions' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'regex:/^\d{1,6}(\.\d{1,2})?$/', 'numeric', 'min:5.00', 'max:5000.00'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:720'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
