<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }
}
