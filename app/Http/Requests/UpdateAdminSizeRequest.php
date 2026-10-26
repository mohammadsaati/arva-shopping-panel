<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminSizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sizeId = (int) $this->route('sizeId');

        return [
            'value' => ['required', 'string', 'max:255', Rule::unique('sizes', 'value')->ignore($sizeId)],
        ];
    }
}
