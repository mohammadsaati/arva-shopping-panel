<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeNewsletterRequest extends FormRequest
{
    protected $errorBag = 'newsletter';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['email' => ['required', 'email', 'max:255']];
    }

    public function attributes(): array
    {
        return [
            'email' => 'ایمیل',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'برای عضویت در خبرنامه ایمیل خود را وارد کنید.',
            'email.email' => 'فرمت ایمیل واردشده معتبر نیست.',
        ];
    }
}
