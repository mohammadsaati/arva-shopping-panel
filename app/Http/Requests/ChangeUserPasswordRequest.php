<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeUserPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'رمز عبور فعلی',
            'password' => 'رمز عبور جدید',
            'password_confirmation' => 'تکرار رمز عبور جدید',
        ];
    }

    public function messages(): array
    {
        return [
            'password.min' => 'رمز جدید باید حداقل ۸ نویسه باشد.',
            'password.confirmed' => 'تکرار رمز جدید با مقدار واردشده یکسان نیست.',
            'password.different' => 'رمز جدید نباید با رمز فعلی یکسان باشد.',
        ];
    }
}
