<?php

namespace App\Http\Requests\ERP;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitStatusTransitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(['draft', 'available', 'held', 'booked', 'sold', 'registration_pending', 'registered', 'handed_over', 'cancelled']),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}

