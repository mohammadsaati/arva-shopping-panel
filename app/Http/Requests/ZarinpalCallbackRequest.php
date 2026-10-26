<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZarinpalCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Authority' => ['required', 'string', 'max:64'],
            'Status' => ['required', 'string', 'max:16'],
        ];
    }
}
