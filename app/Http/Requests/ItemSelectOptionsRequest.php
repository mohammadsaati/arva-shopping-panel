<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemSelectOptionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'selected_ids' => ['nullable', 'array', 'max:25'],
            'selected_ids.*' => ['integer', 'distinct', 'min:1'],
        ];
    }
}
