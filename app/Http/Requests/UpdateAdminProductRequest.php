<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'price' => ['required', 'integer', 'min:0'],
            'color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'shipping_price' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['required', 'image', 'max:5120'],
            'replacements' => ['nullable', 'array', 'max:10'],
            'replacements.*' => ['required', 'image', 'max:5120'],
            'delete_image_ids' => ['nullable', 'array', 'max:10'],
            'delete_image_ids.*' => ['required', 'integer', 'distinct'],
            'city_rates_present' => ['required', 'accepted'],
            'city_rates' => ['nullable', 'array', 'max:100'],
            'city_rates.*.city_id' => ['required', 'integer', 'distinct', 'exists:cities,id'],
            'city_rates.*.shipping_price' => ['required', 'integer', 'min:0'],
        ];
    }
}
