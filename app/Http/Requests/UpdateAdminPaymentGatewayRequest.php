<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentCurrencyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminPaymentGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $requiresMerchant = $this->input('status') === GlobalStatusEnum::ACTIVE->value;

        return [
            'name' => ['required', 'string', 'max:120'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
            'merchant_id' => [
                Rule::requiredIf($requiresMerchant),
                'nullable',
                'string',
                'uuid',
            ],
            'sandbox' => ['nullable', 'boolean'],
            'currency' => ['required', 'string', Rule::enum(PaymentCurrencyEnum::class)],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام نمایشی',
            'status' => 'وضعیت',
            'merchant_id' => 'کد پذیرنده',
            'sandbox' => 'حالت آزمایشی',
            'currency' => 'واحد پولی',
            'image' => 'تصویر درگاه',
            'remove_image' => 'حذف تصویر',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sandbox' => $this->boolean('sandbox'),
            'remove_image' => $this->boolean('remove_image'),
            'merchant_id' => $this->filled('merchant_id') ? $this->string('merchant_id')->value() : null,
        ]);
    }
}
