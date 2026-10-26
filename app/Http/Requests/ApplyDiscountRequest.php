<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount_code' => ['required', 'string', 'alpha_dash', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'discount_code' => 'کد تخفیف',
        ];
    }

    public function messages(): array
    {
        return [
            'discount_code.required' => 'کد تخفیف را وارد کنید.',
            'discount_code.alpha_dash' => 'کد تخفیف فقط می‌تواند شامل حروف انگلیسی، عدد، خط تیره و زیرخط باشد.',
        ];
    }

    public function code(): string
    {
        return strtoupper($this->string('discount_code')->value());
    }
}
