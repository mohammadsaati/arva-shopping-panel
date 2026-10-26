<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['sometimes', 'required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'quantity' => 'تعداد',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.min' => 'تعداد باید حداقل ۱ باشد.',
            'quantity.max' => 'حداکثر تعداد مجاز برای هر محصول ۹۹ عدد است.',
        ];
    }

    public function quantity(): int
    {
        return (int) $this->validated('quantity', 1);
    }
}
