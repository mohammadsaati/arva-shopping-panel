<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $colorId = (int) $this->route('colorId');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('colors', 'name')
                    ->where(fn ($query) => $query->where('value', $this->input('value')))
                    ->ignore($colorId),
            ],
            'value' => ['required', 'string', 'max:32'],
        ];
    }
}
