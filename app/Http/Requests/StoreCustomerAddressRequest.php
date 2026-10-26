<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'province_id' => [
                'required',
                'integer',
                Rule::exists('provinces', 'id')->where('status', GlobalStatusEnum::ACTIVE->value),
            ],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('province_id', $this->input('province_id'))
                    ->where('status', GlobalStatusEnum::ACTIVE->value),
            ],
            'full_address' => ['required', 'string', 'max:2000'],
            'phone_number' => ['required', 'string', 'max:255'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_national_code' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'province_id' => 'استان',
            'city_id' => 'شهر',
            'full_address' => 'نشانی کامل',
            'phone_number' => 'شماره تماس گیرنده',
            'recipient_name' => 'نام گیرنده',
            'recipient_national_code' => 'کد ملی گیرنده',
        ];
    }
}
