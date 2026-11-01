<?php

namespace App\Http\Requests\ERP;

use Illuminate\Foundation\Http\FormRequest;

class PriceRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_price' => ['required', 'numeric', 'gt:0'],
            'new_min_price' => ['nullable', 'numeric', 'gt:0', 'lte:new_price'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}

