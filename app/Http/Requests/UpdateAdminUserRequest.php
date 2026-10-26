<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = (int) $this->route('userId');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', Rule::unique('users', 'phone')->ignore($userId)],
            'type' => ['required', 'string', Rule::enum(UserTypeEnum::class)],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
            'bank_card' => ['nullable', 'string', 'max:255'],
            'job' => ['nullable', 'string', 'max:255'],
            'city_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
