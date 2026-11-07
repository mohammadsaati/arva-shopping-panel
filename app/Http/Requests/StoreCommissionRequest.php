<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('commissions', 'title')],
            'percentage' => ['required', 'regex:/^(?:100(?:\.0{1,2})?|(?:0|[1-9][0-9]?)(?:\.[0-9]{1,2})?)$/'],
        ];
    }
}
