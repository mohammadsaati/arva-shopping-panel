<?php

namespace App\Http\Requests;

use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
            'type' => ['required', 'string', Rule::enum(UserTypeEnum::class)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'vendor_company_name' => ['required_if:type,'.UserTypeEnum::VENDOR->value, 'nullable', 'string', 'max:255'],
            'vendor_city_id' => ['required_if:type,'.UserTypeEnum::VENDOR->value, 'nullable', 'integer'],
            'commission_id' => ['nullable', 'integer', 'exists:commissions,id'],
        ];
    }
}
