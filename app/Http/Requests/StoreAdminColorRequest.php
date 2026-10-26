<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('colors', 'name')->where(fn ($query) => $query->where('value', $this->input('value'))),
            ],
            'value' => ['required', 'string', 'max:32'],
        ];
    }
}
