<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer', 'min:1'],
            'payment_gateway_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'address_id' => 'نشانی تحویل',
            'payment_gateway_id' => 'روش پرداخت',
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => 'یک نشانی برای تحویل سفارش انتخاب کنید.',
            'payment_gateway_id.integer' => 'روش پرداخت انتخاب‌شده معتبر نیست.',
        ];
    }
}
