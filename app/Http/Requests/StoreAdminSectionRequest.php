<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:sections,slug'],
            'bg_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'bg_image' => ['nullable', 'image', 'max:5120'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:5120'],
            'item_ids' => ['nullable', 'array', 'max:100'],
            'item_ids.*' => ['integer', 'distinct', 'exists:items,id'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
        ];
    }
}
